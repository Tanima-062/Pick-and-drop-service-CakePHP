<?php
/**
 * Dispatcher takes the URL information, parses it for parameters and
 * tells the involved controllers what to do.
 *
 * This is the heart of CakePHP's operation.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Routing
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Router', 'Routing');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('Scaffold', 'Controller');
App::uses('View', 'View');
App::uses('Debugger', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('CakeEventListener', 'Event');

/**
 * Dispatcher converts Requests into controller actions. It uses the dispatched Request
 * to locate and load the correct controller. If found, the requested action is called on
 * the controller.
 *
 * @package       Cake.Routing
 */
class Dispatcher implements CakeEventListener {

/**
 * Event manager, used to handle dispatcher filters
 *
 * @var CakeEventManager
 */
	protected $_eventManager;

/**
 * Constructor.
 *
 * @param string $base The base directory for the application. Writes `App.base` to Configure.
 */
	public function __construct($base = false) {
		if ($base !== false) {
			Configure::write('App.base', $base);
		}
	}

/**
 * Returns the CakeEventManager instance or creates one if none was
 * created. Attaches the default listeners and filters
 *
 * @return CakeEventManager
 */
	public function getEventManager() {
		if (!$this->_eventManager) {
			$this->_eventManager = new CakeEventManager();
			$this->_eventManager->attach($this);
			$this->_attachFilters($this->_eventManager);
		}
		return $this->_eventManager;
	}

/**
 * Returns the list of events this object listens to.
 *
 * @return array
 */
	public function implementedEvents() {
		return array('Dispatcher.beforeDispatch' => 'parseParams');
	}

/**
 * Attaches all event listeners for this dispatcher instance. Loads the
 * dispatcher filters from the configured locations.
 *
 * @param CakeEventManager $manager Event manager instance.
 * @return void
 * @throws MissingDispatcherFilterException
 */
	protected function _attachFilters($manager) {
		$filters = Configure::read('Dispatcher.filters');
		if (empty($filters)) {
			return;
		}

		foreach ($filters as $index => $filter) {
			$settings = array();
			if (is_array($filter) && !is_int($index) && class_exists($index)) {
				$settings = $filter;
				$filter = $index;
			}
			if (is_string($filter)) {
				$filter = array('callable' => $filter);
			}
			if (is_string($filter['callable'])) {
				list($plugin, $callable) = pluginSplit($filter['callable'], true);
				App::uses($callable, $plugin . 'Routing/Filter');
				if (!class_exists($callable)) {
					throw new MissingDispatcherFilterException($callable);
				}
				$manager->attach(new $callable($settings));
			} else {
				$on = strtolower($filter['on']);
				$options = array();
				if (isset($filter['priority'])) {
					$options = array('priority' => $filter['priority']);
				}
				$manager->attach($filter['callable'], 'Dispatcher.' . $on . 'Dispatch', $options);
			}
		}
	}

/**
 * Dispatches and invokes given Request, handing over control to the involved controller. If the controller is set
 * to autoRender, via Controller::$autoRender, then Dispatcher will render the view.
 *
 * Actions in CakePHP can be any public method on a controller, that is not declared in Controller. If you
 * want controller methods to be public and in-accessible by URL, then prefix them with a `_`.
 * For example `public function _loadPosts() { }` would not be accessible via URL. Private and protected methods
 * are also not accessible via URL.
 *
 * If no controller of given name can be found, invoke() will throw an exception.
 * If the controller is found, and the action is not found an exception will be thrown.
 *
 * @param CakeRequest $request Request object to dispatch.
 * @param CakeResponse $response Response object to put the results of the dispatch into.
 * @param array $additionalParams Settings array ("bare", "return") which is melded with the GET and POST params
 * @return string|null if `$request['return']` is set then it returns response body, null otherwise
 * @triggers Dispatcher.beforeDispatch $this, compact('request', 'response', 'additionalParams')
 * @triggers Dispatcher.afterDispatch $this, compact('request', 'response')
 * @throws MissingControllerException When the controller is missing.
 */
	public function dispatch(CakeRequest $request, CakeResponse $response, $additionalParams = array()) {
		$beforeEvent = new CakeEvent('Dispatcher.beforeDispatch', $this, compact('request', 'response', 'additionalParams'));
		$this->getEventManager()->dispatch($beforeEvent);

		$request = $beforeEvent->data['request'];
		if ($beforeEvent->result instanceof CakeResponse) {
			if (isset($request->params['return'])) {
				return $beforeEvent->result->body();
			}
			$beforeEvent->result->send();
			return null;
		}

		$controller = $this->_getController($request, $response);

		if (!($controller instanceof Controller)) {
			// routes.phpにルールが無く、controllerも見つからない場合に
			// レンタカー独自のルーティング処理をする
			// 新マッチングルール
			// /rentacar/地方/
			// /rentacar/地方/都道府県/
			// /rentacar/地方/都道府県/空港/
			// /rentacar/地方/都道府県/都市/
			// /rentacar/地方/都道府県/市区町村
			// /rentacar/地方/都道府県/駅/
			// /rentacar/company/企業/
			// /rentacar/company/企業/店舗/
			// 旧ルール
			// /rentacar/会社
			// /rentacar/空港
			// /rentacar/地方
			// /rentacar/都市
			// /rentacar/駅
			// /rentacar/会社/店舗
			// 新マッチングルール(検索へ遷移する)
			// /rentacar/地方/searches
			// /rentacar/地方/都道府県/searches
			// /rentacar/地方/都道府県/空港/searches
			// /rentacar/地方/都道府県/都市/searches
			// /rentacar/地方/都道府県/市区町村/searches
			// /rentacar/地方/都道府県/駅/searches
			
			$client_id		 = 0;
			$office_id		 = 0;
			$prefecture_id	 = 0;
			$airport_arr	 = array();
			$region_arr		 = array();
			$area_arr		 = array();
			$params = $request->params;

			$matching = false;

			// /rentacar/〇〇〇/searchesかどうかの判定フラグ
			$is_move_search = (!empty($request->query['place']))  ? true : false;

			if ($params['controller'] != '') {
				
				if($is_move_search) {
					// /rentacar/〇〇〇/searches を全部searchesコントローラーに遷移する
					$params['is_move_search']	 = true;
					$params['controller']		 = 'searches';
					$params['action']			 = 'index';
					$matching					 = true;
				}

				if ($params['controller'] !== 'company') {
					// 地方
					App::uses('Prefecture','Model');
					$prefecture = new Prefecture;

					$region_link_cd = (preg_match('/^area_[A-za-z]+$/', $params['controller'])) ? $params['controller'] : 'area_' . $params['controller'];
					$region_arr = $prefecture->getPrefectureListByRegionLinkCd($region_link_cd);

					if (!empty($region_arr)) {
						$pref_link_cd;
						$link_cd;
						$ng_pass = false;

						// 北海道・沖縄のみ都道府県 = 地方とする
						if ($region_link_cd === 'area_hokkaido' || $region_link_cd === 'area_okinawa') {
							$pref_link_cd = str_replace('area_', '', $region_link_cd);
							$link_cd = (!empty($params['action']) && $params['action'] !== 'index') ? $params['action'] : '';
							$ng_pass = !empty($params['pass'][0]) && !$is_move_search ? true : false;
						} else {
							$pref_link_cd = (!empty($params['action']) && $params['action'] !== 'index') ? $params['action'] : '';
							$link_cd = !empty($params['pass'][0]) ? $params['pass'][0] : '';
							$ng_pass = !empty($params['pass'][1]) ? true : false;

							if ($pref_link_cd === '') {
								$params['region_link_cd']	 = $region_link_cd;
								$params['region_arr']		 = $region_arr;
								$params['controller']		 = 'region';
								$params['action']			 = 'index';
								$matching					 = true;
							}
						}

						if ($ng_pass) {
							// 階層が多い場合は例外処理へ
							throw new MissingControllerException(array(
								'class' => Inflector::camelize($request->params['controller']) . 'Controller',
								'plugin' => empty($request->params['plugin']) ? null : Inflector::camelize($request->params['plugin'])
							));
						}

						if (!$matching) {
							foreach ((array)$region_arr as $k => $v) {
								if ($pref_link_cd === key($v)) {
									$prefecture_id = $k;
									break;
								}
							}

							if ($prefecture_id > 0 && $link_cd === '') {
								$params['pref_link_cd']		 = $pref_link_cd;
								$params['region_link_cd']	 = $region_link_cd;
								$params['prefecture_id']	 = $prefecture_id;
								$params['controller']		 = 'prefectures';
								$params['action']			 = 'index';
								$matching					 = true;
							}
						}

						if (!$matching) {
							// 空港
							App::uses('Landmark', 'Model');
							$landmark = new Landmark;
							$airport_arr = $landmark->getAirportByLinkCd($link_cd);

							// 都道府県IDが空港の都道府県IDと一致する時
							if (!empty($airport_arr) && $airport_arr['Landmark']['prefecture_id'] == $prefecture_id) {
								$params['pref_link_cd']		 = $pref_link_cd;
								$params['region_link_cd']	 = $region_link_cd;
								$params['link_cd']			 = $link_cd;
								$params['airport_arr']		 = $airport_arr['Landmark'];
								$params['controller']		 = 'fromairport';
								$params['action']			 = 'index';
								$matching					 = true;
							}
						}

						if (!$matching) {
							// 港
							App::uses('Landmark', 'Model');
							$landmark = new Landmark;
							$airport_arr = $landmark->getFerryTerminalByLinkCd($link_cd);

							// 都道府県IDが港の都道府県IDと一致する時
							if (!empty($airport_arr) && $airport_arr['Landmark']['prefecture_id'] == $prefecture_id) {
								$params['pref_link_cd']		 = $pref_link_cd;
								$params['region_link_cd']	 = $region_link_cd;
								$params['link_cd']			 = $link_cd;
								$params['airport_arr']		 = $airport_arr['Landmark'];
								$params['controller']		 = 'ferryterminal';
								$params['action']			 = 'index';
								$matching					 = true;
							}
						}

						if (!$matching) {
							// エリア
							App::uses('Area','Model');
							$area = new Area;
							$area_arr = $area->getAreaByAreaLinkCd($link_cd);

							// 都道府県IDがエリアの都道府県IDと一致する時
							if (!empty($area_arr) && $area_arr[0]['Area']['prefecture_id'] == $prefecture_id) {
								$params['pref_link_cd']		 = $pref_link_cd;
								$params['region_link_cd']	 = $region_link_cd;
								$params['area_link_cd']		 = $link_cd;
								$params['area_arr']			 = $area_arr;
								$params['controller']		 = 'city';
								$params['action']			 = 'index';
								$matching					 = true;
							}
						}

						if (!$matching) {
							// 市区町村
							App::uses('City','Model');
							$city = new City;
							$city_arr = $city->getCityByCityLinkCd($link_cd);

							// 都道府県IDが市区町村の都道府県IDと一致する時
							if (!empty($city_arr) && $city_arr[0]['City']['prefecture_id'] == $prefecture_id) {
								$params['pref_link_cd']		 = $pref_link_cd;
								$params['region_link_cd']	 = $region_link_cd;
								$params['link_cd']			 = $link_cd;
								$params['city_arr']			 = $city_arr;
								$params['controller']		 = 'municipality';
								$params['action']			 = 'index';
								$matching					 = true;
							} else {
								// 駅
								App::uses('Station','Model');
								$station = new Station;
								$station_arr = $station->getStationByStationLinkCd($link_cd);

							// 都道府県IDが駅の都道府県IDと一致する時
								if (!empty($station_arr) && $station_arr[0]['Station']['prefecture_id'] == $prefecture_id) {
									$params['pref_link_cd']		 = $pref_link_cd;
									$params['region_link_cd']	 = $region_link_cd;
									$params['url']				 = $link_cd;
									$params['station_arr']		 = $station_arr;
									$params['controller']		 = 'station';
									$params['action']			 = 'index';
									$matching					 = true;
								}
							}

							if (!$matching) {
								// マッチしない時は例外処理へ
								throw new MissingControllerException(array(
									'class' => Inflector::camelize($request->params['controller']) . 'Controller',
									'plugin' => empty($request->params['plugin']) ? null : Inflector::camelize($request->params['plugin'])
								));
							}
						}
					}
				}
			}

			if (!$matching) {
				$client_id		 = 0;
				$office_id		 = 0;
				$prefecture_id	 = 0;
				$airport_arr	 = array();
				$region_arr		 = array();
				$area_arr		 = array();
				$params = $request->params;

				if ($params['controller'] != '') {
					// 会社
					App::uses('Client','Model');
					$client = new Client;
					$client->recursive = -1;

					//URLがマッチングしたクライアントIDを取得する
					$client_arr = $client->getClientByLinkCd($params['controller']);

					if (!empty($client_arr)) {
						$client_id = key($client_arr);
					} else {

						$matching_link_cd = false;

						// 空港
						App::uses('Landmark', 'Model');
						$landmark = new Landmark;
						if($airport_arr = $landmark->getAirportByLinkCd($params['controller'])){
							$matching_link_cd = true;
						}

						if (!$matching_link_cd) {
							// 地方
							App::uses('Prefecture','Model');
							$prefecture = new Prefecture;

							$_region_link_cd = (preg_match('/^area_[A-za-z]+$/', $params['controller'])) ? $params['controller'] : 'area_' . $params['controller'];
							if($region_arr = $prefecture->getPrefectureListByRegionLinkCd($_region_link_cd)){
								$matching_link_cd = true;
							}
						}

						if (!$matching_link_cd) {
							// エリア
							App::uses('Area','Model');
							$area = new Area;
							if($area_arr = $area->getAreaByAreaLinkCd($params['controller'])){
								$matching_link_cd = true;
							}
						}

						if (!$matching_link_cd) {
							// 駅
							App::uses('Station','Model');
							$station = new Station;
							if($station_arr = $station->getStationByStationLinkCd($params['controller'])){
								$matching_link_cd = true;
							}
						}
					}
				}

				if ($client_id > 0 && $params['action'] != '' && $params['action'] != 'index') {
					// 店舗
					App::uses('Office','Model');
					$office = new Office;
					$office->recursive = -1;

					// URLがマッチングしたオフィスIDを取得する
					$office_arr = $office->getOfficeIdByLinkCd($client_id, $params['action']);

					if (isset($office_arr['Office']['id'])) {
						$office_id = $office_arr['Office']['id'];
					}
				}

				if ($office_id > 0) {
					// リンクコードをlocalstoredetailのmoved_urlに渡す
					$params['client_link_cd']	 = $params['controller'];
					$params['office_link_cd']	 = $params['action'];
					$params['controller']		 = 'localstoredetail';
					$params['action']			 = 'moved_url';
					$matching					 = true;

				} else if ($client_id > 0 && ($params['action'] == '' || $params['action'] == 'index')) {
					// リンクコードをcompanyのmoved_urlに渡す
					$params['link_cd']			 = $params['controller'];
					$params['controller']		 = 'company';
					$params['action']			 = 'moved_url';
					$matching					 = true;

				} else if (!empty($airport_arr)) {
					// リンクコードをfromairportのmoved_urlに渡す
					$params['link_cd']			 = $params['controller'];
					$params['airport_arr']		 = $airport_arr['Landmark'];
					$params['controller']		 = 'fromairport';
					$params['action']			 = 'moved_url';
					$matching					 = true;

				} else if (!empty($region_arr)) {

					$params['region_link_cd']	 = $params['controller'];
					$params['region_arr']		 = $region_arr;
					$params['controller']		 = 'region';
					$params['action']			 = 'index';
					$matching					 = true;

				} else if (!empty($area_arr)) {
					// リンクコードをcityのmoved_urlに渡す
					$params['area_link_cd']		 = $params['controller'];
					$params['area_arr']			 = $area_arr;
					$params['controller']		 = 'city';
					$params['action']			 = 'moved_url';
					$matching					 = true;

				} else if (!empty($station_arr)) {

					$params['url']				 = $params['controller'];
					$params['station_arr']		 = $station_arr;
					$params['controller']		 = 'station';
					$params['action']			 = 'moved_url';
					$matching					 = true;

				}
			}

			if ($matching) {
				$request->params = $params;
				$controller = $this->_getController($request, $response);
			}

			// 見つからない場合は本来の例外処理へ
			if (!($controller instanceof Controller)) {
				throw new MissingControllerException(array(
					'class' => Inflector::camelize($request->params['controller']) . 'Controller',
					'plugin' => empty($request->params['plugin']) ? null : Inflector::camelize($request->params['plugin'])
				));
			}
		}

		$response = $this->_invoke($controller, $request);
		if (isset($request->params['return'])) {
			return $response->body();
		}

		$afterEvent = new CakeEvent('Dispatcher.afterDispatch', $this, compact('request', 'response'));
		$this->getEventManager()->dispatch($afterEvent);
		$afterEvent->data['response']->send();
	}

/**
 * Initializes the components and models a controller will be using.
 * Triggers the controller action, and invokes the rendering if Controller::$autoRender
 * is true and echo's the output. Otherwise the return value of the controller
 * action are returned.
 *
 * @param Controller $controller Controller to invoke
 * @param CakeRequest $request The request object to invoke the controller for.
 * @return CakeResponse the resulting response object
 */
	protected function _invoke(Controller $controller, CakeRequest $request) {
		$controller->constructClasses();
		$controller->startupProcess();

		$response = $controller->response;
		$render = true;
		$result = $controller->invokeAction($request);
		if ($result instanceof CakeResponse) {
			$render = false;
			$response = $result;
		}

		if ($render && $controller->autoRender) {
			$response = $controller->render();
		} elseif (!($result instanceof CakeResponse) && $response->body() === null) {
			$response->body($result);
		}
		$controller->shutdownProcess();

		return $response;
	}

/**
 * Applies Routing and additionalParameters to the request to be dispatched.
 * If Routes have not been loaded they will be loaded, and app/Config/routes.php will be run.
 *
 * @param CakeEvent $event containing the request, response and additional params
 * @return void
 */
	public function parseParams($event) {
		$request = $event->data['request'];
		Router::setRequestInfo($request);
		$params = Router::parse($request->url);
		$request->addParams($params);

		if (!empty($event->data['additionalParams'])) {
			$request->addParams($event->data['additionalParams']);
		}
	}

/**
 * Get controller to use, either plugin controller or application controller
 *
 * @param CakeRequest $request Request object
 * @param CakeResponse $response Response for the controller.
 * @return mixed name of controller if not loaded, or object if loaded
 */
	protected function _getController($request, $response) {
		$ctrlClass = $this->_loadController($request);
		if (!$ctrlClass) {
			return false;
		}
		$reflection = new ReflectionClass($ctrlClass);
		if ($reflection->isAbstract() || $reflection->isInterface()) {
			return false;
		}
		return $reflection->newInstance($request, $response);
	}

/**
 * Load controller and return controller class name
 *
 * @param CakeRequest $request Request instance.
 * @return string|bool Name of controller class name
 */
	protected function _loadController($request) {
		$pluginName = $pluginPath = $controller = null;
		if (!empty($request->params['plugin'])) {
			$pluginName = $controller = Inflector::camelize($request->params['plugin']);
			$pluginPath = $pluginName . '.';
		}
		if (!empty($request->params['controller'])) {
			$controller = Inflector::camelize($request->params['controller']);
		}
		if ($pluginPath . $controller) {
			$class = $controller . 'Controller';
			App::uses('AppController', 'Controller');
			App::uses($pluginName . 'AppController', $pluginPath . 'Controller');
			App::uses($class, $pluginPath . 'Controller');
			if (class_exists($class)) {
				return $class;
			}
		}
		return false;
	}

}

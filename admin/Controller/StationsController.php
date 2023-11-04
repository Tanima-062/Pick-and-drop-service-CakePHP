<?php
class StationsController extends AppController
{

    public $uses = array('Station','Prefecture', 'City');

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $prefectureList = $this->Prefecture->getPrefectureList();
        $this->set('prefectureList', $prefectureList);
        $this->set('stationTypes', Constant::stationTypes());
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $this->Station->recursive = 0;

        $conditions = array('Station.name <>' => '');

        // 都道府県
        if (!empty($this->request->query['Prefecture'])) {
            $conditions['prefecture_id'] = $this->request->query['Prefecture'];
        }
        // 主要駅
        if (!empty($this->request->query['major_flg'])) {
            $conditions['major_flg'] = $this->request->query['major_flg'];
        }
        // 駅タイプ
        if (isset($this->request->query['type']) && is_numeric($this->request->query['type'])) {
            $conditions['type'] = $this->request->query['type'];
        }

        $this->request->data['Station'] = $this->request->query;
        $this->paginate = array('conditions'=> $conditions,'order' => 'Station.id asc');

        $this->set('stations', $this->paginate());

        $cityList = $this->City->getCityList();
        $this->set('cityList', $cityList);
    }

    /**
     * 追加画面処理
     *
     * @return void
     */
    public function add()
    {

        if ($this->request->is('post')) {
            $saveData = $this->request->data['Station'];

            $saveData['staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $saveData['deleted'] = date("Y-m-d H:i:s", time());
            }

            if ($this->Station->save($saveData)) {
                $this->Session->setFlash('駅を追加しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('駅の登録に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        }
    }

    /**
     * 編集画面処理
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        if (!$this->Station->exists($id)) {
            throw new NotFoundException(__('Invalid station'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $saveData = $this->request->data['Station'];

            $saveData['staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $this->Station->id = $id;
                if ($this->Station->field('delete_flg') == 0) {
                    $saveData['deleted'] = date("Y-m-d H:i:s", time());
                }
            } else {
                $saveData['deleted'] = null;
            }

            if ($this->Station->save($saveData)) {
                $this->Session->setFlash('駅を編集しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('駅の編集に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->Station->recursive = -1;
            $airports = $this->Station->find('first', array('conditions' => array('Station.id'=> $id)));
            $this->request->data['Station'] = $airports['Station'];
            $cityList = $this->City->find(
                'list',
                array(
                    'conditions' => array(
                        'City.prefecture_id' => $airports['Station']['prefecture_id'],
                        'City.delete_flg' => 0
                    )
                )
            );
            $this->set('cityList', $cityList);
        }
    }

    /**
     * 削除メソッド
     * 論理削除
     *
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $saveData['id'] = $id;
        $saveData['staff_id'] = $this->cdata['id'];
        $saveData['delete_flg'] = 1;

        if ($this->Station->save($saveData)) {
            $this->Session->setFlash('削除しました', 'default', array('class' => 'alert alert-success'));
        } else {
            $this->Session->setFlash('削除に失敗しました', 'default', array('class' => 'alert alert-error'));
        }

        $this->redirect($this->referer());
    }

    /**
     * ajax用 都道府県に応じた市区町村をgetする
     * @param string $prefectureId
     */
    public function get_city_list($prefectureId = '')
    {
        $this->autoRender = false;
        if (!empty($prefectureId) && $this->request->is('ajax')) {
            $cityList = $this->City->find('list', array('conditions' => array('City.prefecture_id' => $prefectureId, 'City.delete_flg' => 0)));
            return json_encode($cityList);
        }
    }

    /**
     * 元画面へ遷移
     * ※元画面情報がなければ初期画面へ
     *
     * @return void
     */
    private function redirectReferer()
    {
        // refererがあれば戻す
        if (!empty($this->request->data['Custom']['referer'])) {
            $this->redirect($this->request->data['Custom']['referer']);
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }
}

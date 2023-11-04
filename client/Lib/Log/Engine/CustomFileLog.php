<?php
/**
 * File Storage stream for Logging
 *
 * CakePHP(tm) :  Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       Cake.Log.Engine
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AuthComponent', 'Controller/Component');
App::uses('FileLog', 'Log/Engine');
App::uses('Router', 'Routing');

/**
 * File Storage stream for Logging. Writes logs to different files
 * based on the type of log it is.
 *
 * @package       Cake.Log.Engine
 */
class CustomFileLog extends FileLog {

	/**
	 * Implements writing to log files.
	 *
	 * @param string $type The type of log you are making.
	 * @param string $message The message you want to log.
	 * @return bool success of write.
	 */
	public function write($type, $message) {
		$jsonLog = array(
			'datetime' => date('Y-m-d H:i:s'),
			'level' => ucfirst($type),
			'app' => 'rentacar_client'
		);
		$request = Router::getRequest(true);
		if ($request) {
			$jsonLog['uri'] = $request->here();
			$jsonLog['ip'] = $request->clientIp();
		}
		if (session_status() === PHP_SESSION_ACTIVE) {
			$jsonLog['session_id'] = session_id();
		}
		$user = AuthComponent::user();
		if ($user) {
			$jsonLog['staff_id'] = $user['id'];
		}
		$jsonLog['message'] = $message;
		$output = json_encode($jsonLog) . "\n";
		$filename = $this->_getFilename($type);
		if (!empty($this->_size)) {
			$this->_rotateFile($filename);
		}

		$pathname = $this->_path . $filename;
		if (empty($this->_config['mask'])) {
			return file_put_contents($pathname, $output, FILE_APPEND);
		}

		$exists = file_exists($pathname);
		$result = file_put_contents($pathname, $output, FILE_APPEND);
		static $selfError = false;
		if (!$selfError && !$exists && !chmod($pathname, (int)$this->_config['mask'])) {
			$selfError = true;
			trigger_error(__d(
				'cake_dev', 'Could not apply permission mask "%s" on log file "%s"',
				array($this->_config['mask'], $pathname)), E_USER_WARNING);
			$selfError = false;
		}
		return $result;
	}

}

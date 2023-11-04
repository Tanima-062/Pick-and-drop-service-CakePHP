<?php
App::uses('AppHelper', 'View/Helper');

class CreateUrlHelper extends AppHelper {

	public function view($url, $query) {

		$parts = parse_url($url);
		$default = array();
		$query_arr = array();
		parse_str($parts['query'], $default);
		parse_str($query, $query_arr);
		$result = array_filter(array_merge($default,$query_arr), function($v) {
			return !is_string($v) || strlen($v);
		});
		echo $parts['path'] . Router::queryString($result);

	}
}

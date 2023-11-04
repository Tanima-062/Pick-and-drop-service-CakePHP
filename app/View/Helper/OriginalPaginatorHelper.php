<?php
App::uses('Helper', 'View');

class OriginalPaginatorHelper extends Helper {

	public $helpers = array('Html','Form');

	public $paging = array();

	public $options = array(
		'convertKeys' => array('page', 'limit', 'sort', 'direction')
	);

	public function set($paging) {

		$this->options();
		$this->paging = $paging;

    }

    public function options() {

    	if(!empty($this->request->paginateQuery)) {
    		$this->options = http_build_query($this->request->paginateQuery);
    	} else {
    		$this->options = http_build_query($this->request->query);
    	}
    }

    public function paginator() {

		return pr($this->params);

    }

	public function numbers($options = array()) {

		$defaultsOptions = array('tag' => 'span', 'class' => '', 'before' => '  ', 'separator' => '  |  ', 'after' => '  ', 'omission' => false, 'max' => 5);
		$options = array_merge($defaultsOptions, $options);

		$here = $this->_createHere();

		$first = 0;
		if ($options['omission']) {

			if ($options['max'] > 1) {

				/**
				 * 5ページ表示 5ページ表示以外は無理なので、5ページ表示以外にしたい場合は書き直してください
				 */
				$first = $this->paging['page'] - 2;
				$pageCount = $this->paging['page'] + 2;

				/**
				 * 最初の1,2ページ
				 */
				if($this->paging['page'] == 1) {
					$pageCount = $options['max'];
				} else if ($this->paging['page'] == 2) {
					$pageCount++;
				}

				/**
				 * 最後の1,2ページ
				 */
				if(($this->paging['page'] == $this->paging['pageCount'])) {
					$first = $this->paging['page'] - ($options['max'] - 1);
				} else if($this->paging['page'] == ($this->paging['pageCount'] - 1)) {
					$first--;
				}

			} else {
				$first = $this->paging['page'];
				$pageCount = $first;
			}

		} else {
			$pageCount = $this->paging['pageCount'];
			$first = 1;
		}


    	if ($first < 1) {
    		$first = 1;
    	}

    	if ($pageCount > $this->paging['pageCount']) {
    		$pageCount = $this->paging['pageCount'];
    	}

		$result = '';
		for ($i = $first;$i <= $pageCount;$i++) {

			if ($i == $first && $i != $this->paging['page']) {
				if ($i != 1) {
					$result .= '...';
				}
				$result .= $this->_pagingLink(
						$options['before'].$i,
						$here.$i.'/'.$this->paging['order'].'/?'.$this->options,
						false
				);

			} else if ($i == $first && $i == $this->paging['page']) {

				if ($i != 1) {
					$result .= '...';
				}
				if (!empty($options['class'])) {
					$result .= $this->Html->tag($options['tag'],$options['before'].$i,array('class' => $options['class']));
				} else {
					$result .= $options['before'].$i;
				}


			} else if ($i == 1 && $i != $this->paging['page']) {

				$result .= $this->_pagingLink(
						$options['before'].$i,
						$here.$i.'/'.$this->paging['order'].'/?'.$this->options,
						false
				);

			} else if ($i == 1 && $i == $this->paging['page']) {

				if (!empty($options['class'])) {
					$result .= $this->Html->tag($options['tag'],$options['before'].$i,array('class' => $options['class']));
				} else {
					$result .= $options['before'].$i;
				}

			} else if ($i != 1 && $i != $this->paging['pageCount'] && $i != $this->paging['page']) {

				$result .= $options['separator'];
				$result .= $this->_pagingLink(
						$i,
						$here.$i.'/'.$this->paging['order'].'/?'.$this->options,
						false
				);

			} else if ($i != 1 && $i != $this->paging['pageCount'] && $i == $this->paging['page']) {

				$result .= $options['separator'];
				if (!empty($options['class'])) {
					$result .= $this->Html->tag($options['tag'],$i,array('class' => $options['class']));
				} else {
					$result .= $i;
				}

			} else if ($i == $this->paging['pageCount'] && $i != $this->paging['page']) {

				$result .= $options['separator'];
				$result .= $this->_pagingLink(
						$i.$options['after'],
						$here.$i.'/'.$this->paging['order'].'/?'.$this->options,
						false
				);

			} else if ($i == $this->paging['pageCount'] && $i == $this->paging['page']) {

				$result .= $options['separator'];

				if (!empty($options['class'])) {
					$result .= $this->Html->tag($options['tag'],$i.$options['after'],array('class' => $options['class']));
				} else {
					$result .= $i.$options['after'];
				}

			}
		}

		if ($i-1 < $this->paging['pageCount']) {
			$result .= '...';
		}

		return $result;
    }

    public function prev($title = '< Prev', $options = array()) {

		$here = $this->_createHere();

    	return $this->_pagingLink(
						$title,
						$here.$this->paging['prevPage'].'/'.$this->paging['order'].'/?'.$this->options,
						$options
    			);
    }

	 public function next($title = 'Next >', $options = array('escape' => false)) {

	 	$here = $this->_createHere();

		return $this->_pagingLink(
						$title,
						$here.$this->paging['nextPage'].'/'.$this->paging['order'].'/?'.$this->options,
						$options
				);
    }

    public function hasPrev() {

    	if ($this->paging['page'] != 1) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function hasNext() {

    	if ($this->paging['pageCount'] != $this->paging['page'] && $this->paging['pageCount'] != 0) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function hasNumber() {

    	if ($this->paging['pageCount'] > 1) {
    		return true;
    	} else {
    		return false;
    	}
    }

    protected function _pagingLink($title, $url, $options) {

    	return $this->Html->link(
		    			$title,
		    			$url,
		    			$options
    			);
    }

    protected function _createHere() {

    	if (isset($this->request->pass[1]) && (
    			strcmp($this->request->pass[1],'terms') == 0 ||
    			strcmp($this->request->pass[1],'service') == 0 ||
    			strcmp($this->request->pass[1],'equipment') == 0)) {

			$here = "/".$this->params->pass[1]."/".$this->params->pass[2]."/";

    	} else if (isset($this->request->pass[3])) {
    		$here = '/'.$this->request->pass[1].'/';
    	} else if (!empty($this->request->pass[0])) {
    		$here = str_replace($this->request->pass[0].'/'.$this->request->pass[1].'/', '', $this->params->here);
    	} else {
    		$here = $this->params->here;
    	}

    	return $here;
    }

    public function sort($key, $title = null, $options = array()) {

    	$here = $this->_createHere();

		return $this->_pagingLink(
						$title,
						$here.'1/'.$key.'/?'.$this->options,
						$options
				);
    }

    public function select($array) {
    	$here = $this->_createHere();

  		$order = $this->paging['order'];

  		echo '<select id="sort" name="sort">';
    	$options = array();
    	foreach($array as $key => $val) {
    		$link = $here . '1/' . $key . '/?' . $this->options;
    		$options[$link] = $val;

    		$selected = '';
    		if($key == $order) {
    			$selected = 'selected=selected';
    		}

    		echo "<option value='{$link}' {$selected}>{$val}</option>";
    	}
    	echo '</select>';

    }
}

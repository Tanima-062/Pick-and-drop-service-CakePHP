<?php

App::uses('Model', 'Client');

class CreateYotpoJsonShell extends AppShell {

    private $config = null;
    public $uses = array('Client','KeyValue');

	public function startup() {
        Configure::load('YotpoConfig', 'default');
        $this->config = Configure::read('Yotpo');
		parent::startup();
	}

	/**
	 * SSRのためYOTPOのwidgetを取得してjsonをDBに保存
	 */
	public function main() {
		for ($page = 1; $page < 10; $page++) {
			try {
                
                $json = $this->sendYotpoHttpRequest($page);

                if ($json) {
                    $keyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'yotpo_json_top_'.$page)));

                    if(empty($keyValue)){
                        $record = array();
                        $record['id'] = null;
                        $record['key'] = 'yotpo_json_top_'.$page;
                    } else {
                        $record = $keyValue['KeyValue'];
                        $record['modified'] = date('Y-m-d H:i:s');
                    }
                    $record['value'] = json_encode($json, JSON_UNESCAPED_SLASHES);

                    $this->KeyValue->save($record);
                }
                
				$this->out(date('Y/m/d H:i:s ') . $this->name . ' TOP - PAGE:' . $page . ' : OK ');
			} catch (Exception $e) {
				$this->out(date('Y/m/d H:i:s ') . $this->name .  ' failed (' . $e->getMessage() . ' )');
			}
        }

        // 会社の情報を取得
        $clientList = $this->Client->getClientListAndPostmetaData();
        foreach($clientList as $client){
            try {
                
                $pid = $client['Client']['id'].'cl';
                $json = $this->sendYotpoHttpRequest(1, $pid);
                
                if ($json) {
                    $keyValue = $this->KeyValue->find('first', array('conditions' => array('key'=> 'yotpo_json_company_'.$pid)));

                    if(empty($keyValue)){
                        $record = array();
                        $record['id'] = null;
                        $record['key'] = 'yotpo_json_company_'.$pid;
                    } else {
                        $record = $keyValue['KeyValue'];
                        $record['modified'] = date('Y-m-d H:i:s');
                    }
                    $record['value'] = json_encode($json, JSON_UNESCAPED_SLASHES);

                    $this->KeyValue->save($record);
                }
                
				$this->out(date('Y/m/d H:i:s ') . $this->name . ' COMPANY - ID:' . $client['Client']['id'] . ' : OK ');
			} catch (Exception $e) {
				$this->out(date('Y/m/d H:i:s ') . $this->name .  ' failed (' . $e->getMessage() . ' )');
			}
        }
    }
    
    /**
	 * HTTPリクエストの送信(トップページ)
	 */
    private function sendYotpoHttpRequest($page = 1, $pid = '') {
        
        // トップの場合 
        if (empty($pid)) {
            $url = 'https://staticw2.yotpo.com/batch/app_key/'.$this->config['app_key'].'/domain_key/yotpononproductrelatedwidget/widget/testimonials';
            $data = array(
                'methods' => '[{"method":"testimonials","params":{"page":'.$page.',"per_page":10,"type":"testimonials_custom_tab","index":0,"element_id":"1"}}]',
                'app_key' => $this->config['app_key'],
            );
        }
        // 会社ページの場合
        else{
            $url = 'https://staticw2.yotpo.com/batch/app_key/'.$this->config['app_key'].'/domain_key/'.$pid.'/widget/main_widget';
            $data = array(
                'methods' => '[{"method":"main_widget","params":{"pid":"'.$pid.'","order_metadata_fields":{},"widget_product_id":"'.$pid.'"}}]',
                'app_key' => $this->config['app_key'],
            );
        }
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);
        return $response;
    }
}
<?php

App::uses('Controller','Controller');
App::import('Vendor', 'imageResizeUpLoad');

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

define("FileMaxSize", 1024 * 1024);
define("FileExtChkMsg", '画像ファイルはjpg、gif、pngがご使用できます');
define("FileSizeChkMsg", ' 画像サイズは1MBまでとなります。');
define("FileUploadErrMsg", ' ファイルアップロードに失敗しました。');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClientCampaignsController extends AppController {
        
	public $uses = array('ClientCampaign', 'Client');
        
        public function beforeFilter() {
                parent::beforeFilter();
            	$campaignDeleteFlgOptions = array(
			0=>'公開',
			1=>'非公開'
		);

		$this->set('campaignDeleteFlgOptions',$campaignDeleteFlgOptions);
        }
	
	public function index() {
		//parent::beforeFilter();
		//$clientCampaign = $this->ClientCampaign->find('all');
		//$this->set('clientCampaign', $clientCampaign);          
                $this->paginate = $this->ClientCampaign->getPagenate($this->clientData['client_id']);
		$this->set('clientCampaign', $this->paginate('ClientCampaign'));
	}

	public function add() {

		$this->set('clientList',$this->Client->find('list'));
		$this->ClientCampaign->create();
					
		if ($this->request->is('post')) {
		
			$saveData['ClientCampaign']['title'] = $this->request->data['ClientCampaign']['title'];
			$saveData['ClientCampaign']['client_id'] = $this->clientData['client_id'];
			$saveData['ClientCampaign']['list_explanation'] = $this->request->data['ClientCampaign']['list_explanation'];
			$saveData['ClientCampaign']['overview'] = $this->request->data['ClientCampaign']['overview'];
			$saveData['ClientCampaign']['period_start'] = $this->request->data['ClientCampaign']['period_start'];
			$saveData['ClientCampaign']['period_end'] = $this->request->data['ClientCampaign']['period_end'];
			$saveData['ClientCampaign']['booking_start'] = $this->request->data['ClientCampaign']['booking_start'];
			$saveData['ClientCampaign']['booking_end'] = $this->request->data['ClientCampaign']['booking_end'];
			$saveData['ClientCampaign']['vehicle_fee_example'] = $this->request->data['ClientCampaign']['vehicle_fee_example'];
			$saveData['ClientCampaign']['rank'] = $this->request->data['ClientCampaign']['rank'];
			$saveData['ClientCampaign']['staff_id'] = $this->clientData['staff_id'];
			$saveData['ClientCampaign']['delete_flg'] = $this->request->data['ClientCampaign']['delete_flg'];
			//$saveData['ClientCampaign']['image1'] = null;
			//$saveData['ClientCampaign']['image2'] = null;
			//$saveData['ClientCampaign']['image3'] = null;
			//$saveData['ClientCampaign']['thumbnail_image'] = null;
                        $saveData['ClientCampaign']['image1'] = $this->request->data['ClientCampaign']['image1']['name'];
			$saveData['ClientCampaign']['image2'] = $this->request->data['ClientCampaign']['image2']['name'];
			$saveData['ClientCampaign']['image3'] = $this->request->data['ClientCampaign']['image3']['name'];
			$saveData['ClientCampaign']['thumbnail_image'] = $this->request->data['ClientCampaign']['thumbnail_image']['name'];
			
//			$Current = array();
//			$Current['image1'] = $this->request->data['ClientCampaign']['image1'];
//			$Current['image2'] = $this->request->data['ClientCampaign']['image2'];
//			$Current['image3'] = $this->request->data['ClientCampaign']['image3'];
//			$Current['thumbnail_image'] = $this->request->data['ClientCampaign']['thumbnail_image'];
                        
                        //$imgCheckResult = array();
			//$FName = $this->request->data['ClientCampaign']['image1']['name'];
			//if(!$this->checkExt($FName)){
			//	$imgCheckResult['image1'] []= FileExtChkMsg;
			//}		
			//if($this->request->data['ClientCampaign']['image1']['size'] > FileMaxSize ){
			//	$imgCheckResult['image1'] []= FileSizeChkMsg;
			//}
                        $imgCheckResult = array();
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'image1',$this->request->data['ClientCampaign']['image1']['name'], $this->request->data['ClientCampaign']['image1']['size']);
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'image2',$this->request->data['ClientCampaign']['image2']['name'], $this->request->data['ClientCampaign']['image2']['size']);
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'image3',$this->request->data['ClientCampaign']['image3']['name'], $this->request->data['ClientCampaign']['image3']['size']);
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'thumbnail_image',$this->request->data['ClientCampaign']['thumbnail_image']['name'], $this->request->data['ClientCampaign']['thumbnail_image']['size']);
                        
//			$FileName = $this->request->data['ClientCampaign']['image2']['name'];
//			if(!$this->checkExt($FileName)){
//				$imgCheckResult['image2'] []= FileExtChkMsg;
//			}		
//			if($this->request->data['ClientCampaign']['image2']['size'] > FileMaxSize ){
//				$imgCheckResult['image2'] []= FileSizeChkMsg;
//			}

//			$FileName = $this->request->data['ClientCampaign']['image3']['name'];
//			if(!$this->checkExt($FileName)){
//				$imgCheckResult['image3'] []= FileExtChkMsg;
//			}		
//			if($this->request->data['ClientCampaign']['image3']['size'] > FileMaxSize ){
//				$imgCheckResult['image3'] []= FileSizeChkMsg;
//			}
                        
//                      $FileName = $this->request->data['ClientCampaign']['thumbnail_image']['name'];
//			if(!$this->checkExt($FileName)){
//				$imgCheckResult['thumbnail_image'] []= FileExtChkMsg;
//			}		
//			if($this->request->data['ClientCampaign']['thumbnail_image']['size'] > FileMaxSize ){
//				$imgCheckResult['thumbnail_image'] []= FileSizeChkMsg;
//			}
			
			$dbo = $this->ClientCampaign->getDataSource();
			$dbo->begin($this->ClientCampaign);
			if(!empty($saveData)) {
				$this->ClientCampaign->set($this->request->data);
				if($this->ClientCampaign->validates()){
					if(count($imgCheckResult)>0){
						$this->setImageSizeErrMsg($imgCheckResult);
						$this->Session->setFlash('キャンペーンの追加に失敗しました','default',array('class'=>'alert alert-error'));
					} else {
                                                
                                                try{
                                                  
                                                    $this->ClientCampaign->save($saveData, false);
                                                    $CampaignID =  $this->ClientCampaign->getLastInsertID();
                                                    
                                                    $image1Result = $this->fileUpload($CampaignID, $this->request->data['ClientCampaign'], 'image1');
                                                    $image2Result = $this->fileUpload($CampaignID, $this->request->data['ClientCampaign'], 'image2');
                                                    $image3Result = $this->fileUpload($CampaignID, $this->request->data['ClientCampaign'], 'image3');
                                                    $thumbnailResult = $this->fileUpload($CampaignID, $this->request->data['ClientCampaign'], 'thumbnail_image');

                                                    if(!$image1Result){
                                                        $imgCheckResult['image1'] []= FileUploadErrMsg;
                                                    }
                                                    if(!$image2Result){
                                                        $imgCheckResult['image2'] []= FileUploadErrMsg;
                                                    }
                                                    if(!$image3Result){
                                                        $imgCheckResult['image3'] []= FileUploadErrMsg;
                                                    }
                                                    if(!$thumbnailResult){
                                                        $imgCheckResult['thumbnail'] []= FileUploadErrMsg;
                                                    }
                                                    if(!$image1Result || !$image2Result || !$image3Result || !$thumbnailResult){
                                                        throw new Exception();
                                                    }
/*
                                                    if(!empty($this->request->data['ClientCampaign']['image1']['tmp_name'])){
							$image = $this->request->data['ClientCampaign']['image1'];
							$uploadDir = 'ClientCampaigns' . DS . $CampaignID;			
							//イメージ保存先パス
							$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
							$fld = new Folder($img_save_path);

							// フォルダの有無
							$folder = new Folder($img_save_path);
							if (empty($folder->path)) {
								// フォルダ作成
								$folder = new Folder($img_save_path, true, 0777);
							}
                                                        //ファイルをアップロード、失敗時は例外処理を発生
							if(!move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name'])){
                                                            $imgCheckResult['image1'] []= FileUploadErrMsg;
                                                            throw new Exception();
                                                        }
                                                        //$saveData['ClientCampaign']['image1'] = $image['name'];
                                                    }
//                                                  else{                                               
//							$this->request->data['ClientCampaign']['image1'] = $Current['ClientCampaign']['image1'];
//                                                  }

                                                    if(!empty($this->request->data['ClientCampaign']['image2']['tmp_name'])) {
							$image = $this->request->data['ClientCampaign']['image2'];
							$uploadDir = 'ClientCampaigns' . DS . $CampaignID;			
							//イメージ保存先パス
							$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
							$fld = new Folder($img_save_path);

							// フォルダの有無
							$folder = new Folder($img_save_path);
							if (empty($folder->path)) {
								// フォルダ作成
								$folder = new Folder($img_save_path, true, 0777);
							}

                                                        //ファイルをアップロード、失敗時は例外処理を発生
							if(!move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name'])){
                                                            $imgCheckResult['image2'] []= FileUploadErrMsg;
                                                            throw new Exception();
                                                        }
                                                        //$saveData['ClientCampaign']['image2'] = $image['name'];
                                                    }
//                                                  else{
//      						$this->request->data['ClientCampaign']['image2'] = $Current['ClientCampaign']['image2'];
//                                                  }

                                                    if(!empty($this->request->data['ClientCampaign']['image3']['tmp_name'])) {
							$image = $this->request->data['ClientCampaign']['image3'];					
							$uploadDir = 'ClientCampaigns' . DS . $CampaignID;			
							//イメージ保存先パス
							$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
							$fld = new Folder($img_save_path);

							// フォルダの有無
							$folder = new Folder($img_save_path);
							if (empty($folder->path)) {
								// フォルダ作成
								$folder = new Folder($img_save_path, true, 0777);
							}

                                                        //ファイルをアップロード、失敗時は例外処理を発生
							if(!move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name'])){
                                                            $imgCheckResult['image3'] []= FileUploadErrMsg;
                                                            throw new Exception();
                                                        }
							//$saveData['ClientCampaign']['image3'] = $image['name'];
                                                    }
//                                                  else{
//      						$this->request->data['ClientCampaign']['image3'] = $Current['ClientCampaign']['image3'];
//                                                  }		

                                                    if(!empty($this->request->data['ClientCampaign']['thumbnail_image']['tmp_name'])) {
							$image = $this->request->data['ClientCampaign']['thumbnail_image'];						
							$uploadDir = 'ClientCampaigns' . DS . $CampaignID;			
							//イメージ保存先パス
							$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
							$fld = new Folder($img_save_path);

							// フォルダの有無
							$folder = new Folder($img_save_path);
							if (empty($folder->path)) {
								// フォルダ作成
								$folder = new Folder($img_save_path, true, 0777);
							}

                                                        //ファイルをアップロード、失敗時は例外処理を発生
							if(!move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name'])){
                                                            $imgCheckResult['thumbnail_image'] []= FileUploadErrMsg;
                                                            throw new Exception();
                                                        }
							//$saveData['ClientCampaign']['thumbnail_image'] = $image['name'];

                                                    }
//                                                  else{
//   						        $this->request->data['ClientCampaign']['thumbnail_image'] = $Current['ClientCampaign']['thumbnail_image'];
//                                                  }
*/						
                                                    //if($this->ClientCampaign->save($saveData, false)){
						
							$dbo->commit($this->ClientCampaign);
							$this->redirect(['controller'=>'ClientCampaigns','action'=>'index']);
                                                    //}else{
                                                    //	debug($saveData);
                                                    //	$dbo->rollback($this->ClientCampaign);
							
                                            	    //	$this->Session->setFlash('キャンペーンの追加に失敗しました','default',array('class'=>'alert alert-error'));
                                                    //}
                                                } catch(Exception $e){
                                                    	$dbo->rollback($this->ClientCampaign);
							$this->Session->setFlash('キャンペーンの追加に失敗しました','default',array('class'=>'alert alert-error'));
                                                }
					}
					//$this->Session->setFlash('キャンペーンの追加に失敗しました','default',array('class'=>'alert alert-error'));
				}
			}
			$this->setImageSizeErrMsg($imgCheckResult);

			//$this->request->data['ClientCampaign']['image1'] = $Current['image1'];
			//$this->request->data['ClientCampaign']['image2'] = $Current['image2'];
			//$this->request->data['ClientCampaign']['image3'] = $Current['image3'];
			//$this->request->data['ClientCampaign']['thumbnail_image'] = $Current['thumbnail_image'];
                        
			$this->Session->setFlash('キャンペーンの追加に失敗しました','default',array('class'=>'alert alert-error'));
		}
	}
	
	public function edit($id = null){
		
		//$this->set('clientList',$this->Client->find('list'));
		//$this->set('ClientCampaign', $ClientCampaign);
		$this->ClientCampaign->create();
		$Current = $this->ClientCampaign->find('first', array('conditions' => array('ClientCampaign.' . $this->ClientCampaign->primaryKey => $id)));	
		//$PrevImage = array();
		
//		$maxSize = 1024 * 1024;
//		$imgCheckResult = array();
		
		if (!$this->ClientCampaign->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}
		
/*
		$i = 0;
		$ExtMsg = "画像ファイルはjpg、gif、pngがご使用できます";
		$FName = $this->request->data['ClientCampaign']['image1']['name'];
		if(!$this->checkExt( $FName)){
			debug("in");
			$imgCheckResult['image1'][$i] = $ExtMsg;
			$i = $i + 1;
			$imgCheckResult['image1'][$i] =  'ファイル(' . $FName . ')は不正なファイルです';
			$i = $i + 1;
		}		
		if($this->request->data['ClientCampaign']['image1']['size'] > $maxSize ){
			$imgCheckResult['image1'][$i] = ' 画像サイズは1MBまでとなります。';
		}

		$i = 0;
		$FileName = $this->request->data['ClientCampaign']['image2']['name'];
		if(!$this->checkExt( $FileName)){
			$imgCheckResult['image2'][$i] = $ExtMsg;
			$i = $i + 1;
			$imgCheckResult['image2'][$i] =  'ファイル(' . $FileName . ')はご利用できません';
			$i = $i + 1;
		}		
		if($this->request->data['ClientCampaign']['image2']['size'] > $maxSize ){
			$imgCheckResult['image2'][$i] = '画像サイズは1MBまでとなります。';
		}

		$i = 0;
		$FileName = $this->request->data['ClientCampaign']['image3']['name'];
		if(!$this->checkExt( $FileName)){
			$imgCheckResult['image3'][$i] = $ExtMsg;
			$i = $i + 1;
			$imgCheckResult['image3'][$i] =  'ファイル ' . $FileName . 'はご利用できません';
			$i = $i + 1;
		}		
		if($this->request->data['ClientCampaign']['image3']['size'] > $maxSize ){
			$imgCheckResult['image3'][$i] = '画像サイズは1MBまでとなります。';
		}	

		$i = 0;
		$FileName = $this->request->data['ClientCampaign']['thumbnail_image']['name'];
		if(!$this->checkExt( $FileName)){
			$imgCheckResult['thumbnail_image'][$i] = $ExtMsg;
			$i = $i + 1;
			$imgCheckResult['thumbnail_image'][$i] =  'ファイル ' . $FileName . 'はご利用できません';
			$i = $i + 1;
		}		
		if($this->request->data['ClientCampaign']['thumbnail_image']['size'] > $maxSize ){
			$imgCheckResult['thumbnail_image'][$i] = '画像サイズは1MBまでとなります。';
		}
 * 
 */	
		
		if ($this->request->is('post') || $this->request->is('put')) {
                    
                        if($this->request->data['ClientCampaign']['period_start'] == $Current['ClientCampaign']['period_start']){
                            $this->ClientCampaign->unsetVal('period_start');
                        }

                        if($this->request->data['ClientCampaign']['period_end'] == $Current['ClientCampaign']['period_end']){
                            $this->ClientCampaign->unsetVal('period_end');
                        }

                        if($this->request->data['ClientCampaign']['booking_start'] == $Current['ClientCampaign']['booking_start']){
                            $this->ClientCampaign->unsetVal('booking_start');
                        }
		
                        if($this->request->data['ClientCampaign']['booking_end'] == $Current['ClientCampaign']['booking_end']){
                            $this->ClientCampaign->unsetVal('booking_end');
                        }

                        //画像ファイル拡張子・容量オーバーチェック処理
                        $imgCheckResult = array();
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'image1',$this->request->data['ClientCampaign']['image1']['name'], $this->request->data['ClientCampaign']['image1']['size']);
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'image2',$this->request->data['ClientCampaign']['image2']['name'], $this->request->data['ClientCampaign']['image2']['size']);
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'image3',$this->request->data['ClientCampaign']['image3']['name'], $this->request->data['ClientCampaign']['image3']['size']);
                        $imgCheckResult = $this->fileCheck($imgCheckResult,'thumbnail_image',$this->request->data['ClientCampaign']['thumbnail_image']['name'], $this->request->data['ClientCampaign']['thumbnail_image']['size']);
                
			$saveData = array();	
			$saveData['ClientCampaign']['id'] = $this->request->data['ClientCampaign']['id'];
			$saveData['ClientCampaign']['title'] = $this->request->data['ClientCampaign']['title'];
			$saveData['ClientCampaign']['client_id'] =  $this->clientData['client_id'];
			$saveData['ClientCampaign']['list_explanation'] = $this->request->data['ClientCampaign']['list_explanation'];
			$saveData['ClientCampaign']['overview'] = $this->request->data['ClientCampaign']['overview'];
			$saveData['ClientCampaign']['vehicle_fee_example'] = $this->request->data['ClientCampaign']['vehicle_fee_example'];
                        $saveData['ClientCampaign']['rank'] = $this->request->data['ClientCampaign']['rank'];
			$saveData['ClientCampaign']['staff_id'] = $this->clientData['staff_id'];
			$saveData['ClientCampaign']['delete_flg'] = $this->request->data['ClientCampaign']['delete_flg'];

                        //画像更新時、$saveDataへのセットとアップロード処理を行う
                        $image1Result = true;
                        $image2Result = true;
                        $image3Result = true;
                        $thumbnailResult = true;
                        if(!empty($this->request->data['ClientCampaign']['image1']['tmp_name'])  ) {
                            $saveData['ClientCampaign']['image1'] = $this->request->data['ClientCampaign']['image1']['name'];
                            $image1Result = $this->fileUpload($saveData['ClientCampaign']['id'], $this->request->data['ClientCampaign'], 'image1');
                        } else {
                            $saveData['ClientCampaign']['image1'] = $Current['ClientCampaign']['image1'];
                        }
                        if(!empty($this->request->data['ClientCampaign']['image2']['tmp_name'])  ) {
                            $saveData['ClientCampaign']['image2'] = $this->request->data['ClientCampaign']['image2']['name'];
                            $image2Result = $this->fileUpload($saveData['ClientCampaign']['id'], $this->request->data['ClientCampaign'], 'image2');
                        } else {
                            $saveData['ClientCampaign']['image2'] = $Current['ClientCampaign']['image2'];
                        }
                        if(!empty($this->request->data['ClientCampaign']['image3']['tmp_name'])  ) {
                            $saveData['ClientCampaign']['image3'] = $this->request->data['ClientCampaign']['image3']['name'];
                            $image3Result = $this->fileUpload($saveData['ClientCampaign']['id'], $this->request->data['ClientCampaign'], 'image3');
                        } else {
                            $saveData['ClientCampaign']['image3'] = $Current['ClientCampaign']['image3'];
                        }
                        if(!empty($this->request->data['ClientCampaign']['thumbnail_image']['tmp_name'])  ) {
                            $saveData['ClientCampaign']['thumbnail_image'] = $this->request->data['ClientCampaign']['thumbnail_image']['name'];
                            $thumbnailResult = $this->fileUpload($saveData['ClientCampaign']['id'], $this->request->data['ClientCampaign'], 'thumbnail_image');
                        } else {
                            $saveData['ClientCampaign']['thumbnail_image'] = $Current['ClientCampaign']['thumbnail_image'];
                        }     
                        
                        if(!$image1Result){
                            $imgCheckResult['image1'] []= FileUploadErrMsg;
                        }
                        if(!$image2Result){
                            $imgCheckResult['image2'] []= FileUploadErrMsg;
                        }
                        if(!$image3Result){
                            $imgCheckResult['image3'] []= FileUploadErrMsg;
                        }
                        if(!$thumbnailResult){
                            $imgCheckResult['thumbnail'] []= FileUploadErrMsg;
                        }              
                        
/*
			if(!empty($this->request->data['ClientCampaign']['image1']['tmp_name'])  ) {
				
				$image = $this->request->data['ClientCampaign']['image1'];
				$uploadDir = 'ClientCampaigns' . DS .$saveData['ClientCampaign']['id'];			
				//イメージ保存先パス
				$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
				$fld = new Folder($img_save_path);
				
				// フォルダの有無
				$folder = new Folder($img_save_path);
				if (empty($folder->path)) {
					// フォルダ作成
					$folder = new Folder($img_save_path, true, 0777);
				}

				//イメージの保存処理
				move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name']);

//				$saveData['ClientCampaign']['image1'] = $image['name'];
			}
//                      else{
//				$this->request->data['ClientCampaign']['image1'] = $Current['ClientCampaign']['image1'];
//			}
			
			if(!empty($this->request->data['ClientCampaign']['image2']['tmp_name']) ) {
				$image = $this->request->data['ClientCampaign']['image2'];
				$uploadDir = 'ClientCampaigns' . DS .$saveData['ClientCampaign']['id'];			
				//イメージ保存先パス
				$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
				$fld = new Folder($img_save_path);
				
				// フォルダの有無
				$folder = new Folder($img_save_path);
				if (empty($folder->path)) {
					// フォルダ作成
					$folder = new Folder($img_save_path, true, 0777);
				}
			
				//イメージの保存処理
				move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name']);

//				$saveData['ClientCampaign']['image2'] = $image['name'];
			}
//			else{
//				$this->request->data['ClientCampaign']['image2'] = $Current['ClientCampaign']['image2'];
//			}			
			
			if(!empty($this->request->data['ClientCampaign']['image3']['tmp_name']) && $ValidImage ) {
				$image = $this->request->data['ClientCampaign']['image3'];
				$uploadDir = 'ClientCampaigns' . DS .$saveData['ClientCampaign']['id'];			
				//イメージ保存先パス
				$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
				$fld = new Folder($img_save_path);
				
				// フォルダの有無
				$folder = new Folder($img_save_path);
				if (empty($folder->path)) {
					// フォルダ作成
					$folder = new Folder($img_save_path, true, 0777);
				}
				move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name']);

				$saveData['ClientCampaign']['image3'] = $image['name'];
			
				
			}
//                      else{
//      		        $this->request->data['ClientCampaign']['image3'] = $Current['ClientCampaign']['image3'];
//			}			

			if(!empty($this->request->data['ClientCampaign']['thumbnail_image']['tmp_name']) && $ValidImage ) {

				//$PrevImage = $PrevImage + $this->request->data['ClientCampaign']['thumbnail_image'];
				$image = $this->request->data['ClientCampaign']['thumbnail_image'];
				//var_dump(array('CamapignID', $CampaignID));						
				$uploadDir = 'ClientCampaigns' . DS .$saveData['ClientCampaign']['id'];			
				//イメージ保存先パス
				$img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
				$fld = new Folder($img_save_path);
				//debug(array('imgpath', $img_save_path, 'fld', $fld->Read()));
				
				// フォルダの有無
				$folder = new Folder($img_save_path);
				if (empty($folder->path)) {
					// フォルダ作成
					$folder = new Folder($img_save_path, true, 0777);
				}
				//イメージの保存処理
				move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name']);

				$saveData['ClientCampaign']['thumbnail_image'] = $image['name'];
			}
//                      else {
//				$this->request->data['ClientCampaign']['thumbnail_image'] = $Current['ClientCampaign']['thumbnail_image'];
//			}
 * 
 */
			if(!empty($saveData)){
				try{
                                    
					$this->ClientCampaign->set($this->request->data);	
					if($this->ClientCampaign->validates()){
						if(count($imgCheckResult)>0){
							$this->setImageSizeErrMsg($imgCheckResult);
							$this->Session->setFlash('クライアントキャンペーンの登録に失敗しました','default',array('class'=>'alert alert-error'));		
						}
						else{
							$this->ClientCampaign->save($saveData, false);
							$this->redirect(['controller'=>'ClientCampaigns','action'=>'index']);
						}
					}else{
						$this->setImageSizeErrMsg($imgCheckResult);
						$this->Session->setFlash('クライアントキャンペーンの登録に失敗しました','default',array('class'=>'alert alert-error'));			
					}
				}
				catch(Exception $e)
				{
					$this->Session->setFlash('クライアントキャンペーンの登録に失敗しました','default',array('class'=>'alert alert-error'));		
				}
			}
		}
		else{
			$this->request->data = $this->ClientCampaign->find('first', array('conditions' => array('ClientCampaign.' . $this->ClientCampaign->primaryKey => $id)));	
		}
	}
	
	public function checkExt($FileName){
		
		if( !$FileName ) return true;
                
                $Ext = strtoupper(pathinfo($FileName, PATHINFO_EXTENSION));              
                $r = in_array($Ext ,array('JPG','JPEG','GIF','PNG'));	
		
//		$Ext = explode('.', strtoupper($FileName ));
		
//		$r = in_array($Ext[count($Ext)-1],array('JPG','JPEG','GIF','PNG'))	;
//		debug(array($FileName, $Ext, $i, $r));
		
		return $r;
	}
	
	public function setImageSizeErrMsg($imgCheckResult){
                
                foreach ($imgCheckResult as $key1 => $val1){
                   foreach ($val1 as $key2 => $val2){
                       $this->ClientCampaign->invalidate($key1, $val2);
                   }
                }

/*
		$Errors = $imgCheckResult['image1'];
		if( count($Errors) > 0 )
		{
			debug($Errors);
			for($i = 0; $i < count($Errors); $i ++ ){
				$this->ClientCampaign->invalidate('image1',$Errors[$i]);
			}
		}
		$Errors = $imgCheckResult['image2'];
		if( count($Errors) > 0 )
		{
			debug($Errors);
			for($i = 0; $i < count($Errors); $i ++ ){
				$this->ClientCampaign->invalidate('image2',$Errors[$i]);
			}
		}
		$Errors = $imgCheckResult['image3'];
		if( count($Errors) > 0 )
		{
			debug($Errors);
			for($i = 0; $i < count($Errors); $i ++ ){
				$this->ClientCampaign->invalidate('image3',$Errors[$i]);
			}
		}
		$Errors = $imgCheckResult['thumbnail_image'];
		if( count($Errors) > 0 )
		{
			debug($Errors);
			for($i = 0; $i < count($Errors); $i ++ ){
				$this->ClientCampaign->invalidate('thumbnail_image',$Errors[$i]);
			}
		}        
*/
	}
        
        public function fileCheck($targetArray, $targetName, $fileName, $size){
            if(!$this->checkExt($fileName)){
		$targetArray[$targetName] []= FileExtChkMsg;
            }		
            if($size > FileMaxSize ){
		$targetArray[$targetName] []= FileSizeChkMsg;
            }
            return $targetArray;
        }
        
        public function fileUpload($id, $campaignData, $targetName){
            if(!empty($campaignData[$targetName]['tmp_name'])){
                $image = $campaignData[$targetName];
                $uploadDir = 'ClientCampaigns' . DS . $id;			
                //イメージ保存先パス
                $img_save_path = ROOT.DS.WEBROOT_DIR.DS."img".DS.$uploadDir;
                // フォルダの有無
                $folder = new Folder($img_save_path);
                if (empty($folder->path)) {
                    // フォルダ作成
                    $folder = new Folder($img_save_path, true, 0777);
                }
                //ファイルをアップロード、実行結果を返却
		if(!move_uploaded_file($image['tmp_name'], $img_save_path.DS.$image['name'])){
                    return false;
                }
                return true;
            } else {
                return true;
            }
        }

}
?>
<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'imageResizeUpLoad');

/**
 * CarModels Controller
 *
 * @property CarModel $CarModel
 */
class CarModelsController extends AppController
{

    public $components = array('FileUp');

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $conditions = array();
        $this->CarModel->recursive = 0;
        
        if (!empty($this->request->query['automaker_id'])) {
            $conditions['CarModel.automaker_id'] = $this->request->query['automaker_id'];
            $this->request->data['CarModel']['automaker_id'] = $this->request->query['automaker_id'];
        }
        if (!empty($this->request->query['name'])) {
            $name = trim($this->request->query['name']);
            $name = Sanitize::clean($name);
            $conditions['CarModel.name LIKE'] = '%'.$name.'%';
            $this->request->data['CarModel']['name'] = $this->request->query['name'];
        }
        $this->paginate = array('conditions'=> $conditions);
        $this->set('carModels', $this->paginate());
        $this->set('automakerList', $this->CarModel->Automaker->find('list', array('conditions' => array('delete_flg' => 0))));
    }

    /**
     * 詳細画面処理
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null)
    {

        $this->CarModel->id = $id;
        if (!$this->CarModel->exists()) {
            throw new NotFoundException(__('Invalid carModel'));
        }
        $this->set('carModel', $this->CarModel->read(null, $id));
        $this->set('automakerList', $this->CarModel->Automaker->find('list', array('conditions' => array('delete_flg' => 0))));
    }

    /**
     * 追加画面処理
     *
     * @return void
     */
    public function add()
    {

        if ($this->request->is('post')) {
            if ($this->request->data['CarModel']['image_relative_url']['error'] != 4) {
                $imgpath  = $this->request->data['CarModel']['image_relative_url'];

                if (isset($imgpath)) {
                    $this->ImageResize = new ImageResizeUpLoad();
                    $upLoadDir = 'car_model_img'.DS;
                    $width = "300";
                    $height = "300";

                    $imgName = $this->ImageResize->resizeUpLoad($imgpath, $upLoadDir, null, array($width,$height));

                    if (empty($imgName)) {
                        $this->Session->setFlash('アップロードに失敗しました、再度イメージを選択してください');
                        $this->redirect(array('action' => 'index'));
                    }
                    $this->request->data['CarModel']['image_relative_url'] = $imgName;
                } else {
                    unset($this->request->data['CarModel']['image_relative_url']);
                }
            } else {
                unset($this->request->data['CarModel']['image_relative_url']);
            }

            $this->request->data['CarModel']['staff_id'] = $this->cdata['id'];
            if ($this->request->data['CarModel']['delete_flg'] == 1) {
                $this->request->data['CarModel']['deleted'] = date("Y-m-d H:i:s", time());
            }

            if ($this->CarModel->save($this->request->data)) {
                $this->Session->setFlash('登録が正しく完了しました。', 'default', array('class' => 'alert alert-info'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('入力に失敗しました、各項目を見なおして下さい。', 'default', array('class' => 'alert alert-error'));
            }
        }
        $staffs = $this->CarModel->Staff->find('list');
        $this->set(compact('staffs'));
        $this->set('automakerList', $this->CarModel->Automaker->find('list', array('conditions' => array('delete_flg' => 0))));
    }

    /**
     * 編集画面処理
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $this->CarModel->id = $id;
        if (!$this->CarModel->exists()) {
            throw new NotFoundException(__('Invalid CarModel description'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->request->data['CarModel']['image_relative_url']['error'] != 4) {
                $imgpath  = $this->request->data['CarModel']['image_relative_url'];

                if (isset($imgpath)) {
                    $this->ImageResize = new ImageResizeUpLoad();
                    $upLoadDir = 'car_model_img'.DS;
                    $width = "300";
                    $height = "300";

                    $imgName = $this->ImageResize->resizeUpLoad($imgpath, $upLoadDir, null, array($width,$height));

                    if (empty($imgName)) {
                        $this->Session->setFlash('アップロードに失敗しました、再度イメージを選択してください');
                        $this->redirect(array('action' => 'index'));
                    }
                    $this->request->data['CarModel']['image_relative_url'] = $imgName;
                } else {
                    unset($this->request->data['CarModel']['image_relative_url']);
                }
            } else {
                unset($this->request->data['CarModel']['image_relative_url']);
            }

            $this->request->data['CarModel']['staff_id'] = $this->cdata['id'];
            if ($this->request->data['CarModel']['delete_flg'] == 1) {
                if ($this->CarModel->field('delete_flg') == 0) {
                    $this->request->data['CarModel']['deleted'] = date("Y-m-d H:i:s", time());
                }
            } else {
                $this->request->data['CarModel']['deleted'] = null;
            }

            if ($this->CarModel->save($this->request->data)) {
                $this->Session->setFlash('登録が正しく完了しました。', 'default', array('class' => 'alert alert-info'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('入力に失敗しました、各項目を見なおして下さい。', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->request->data = $this->CarModel->read(null, $id);
        }
        $staffs = $this->CarModel->Staff->find('list');
        $this->set(compact('staffs'));

        $this->set('automakerList', $this->CarModel->Automaker->find('list', array('conditions' => array('delete_flg' => 0))));
    }

    /**
     * 削除処理
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->CarModel->id = $id;
        if (!$this->CarModel->exists()) {
            throw new NotFoundException(__('Invalid carModel'));
        }
        if ($this->CarModel->delete()) {
            $this->Session->setFlash(__('CarModel deleted'));
            $this->redirectReferer();
        }
        $this->Session->setFlash(__('CarModel was not deleted'));
        $this->redirectReferer();
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

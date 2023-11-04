<?php
class CitiesController extends AppController
{

    public $uses = array('City', 'Prefecture', 'Area');

    /**
     * 前処理
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $prefectureList = $this->Prefecture->getPrefectureList();
        $areaList = $this->Area->getAreaList();
        $this->set('prefectureList', $prefectureList);
        $this->set('areaList', $areaList);
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $this->City->recursive = -1;

        $conditions = array();

        // 都道府県
        if (!empty($this->request->query['Prefecture'])) {
            $conditions['prefecture_id'] = $this->request->query['Prefecture'];
        } else {
            $conditions['prefecture_id'] = '1';
            $this->request->data['Prefecture'] = '1';
        }

        $this->request->data['City'] = $this->request->query;
        $this->paginate = array('conditions'=> $conditions, 'order' => 'City.id asc');

        $this->set('cities', $this->paginate());
    }

    /**
     * 追加画面処理
     *
     * @return void
     */
    public function add()
    {

        if ($this->request->is('post')) {
            $saveData = $this->request->data['City'];

            $saveData['staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $saveData['deleted'] = date("Y-m-d H:i:s", time());
            }

            if ($this->City->save($saveData)) {
                $this->Session->setFlash('市区町村を追加しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('市区町村の登録に失敗しました', 'default', array('class' => 'alert alert-error'));
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
        if (!$this->City->exists($id)) {
            throw new NotFoundException(__('Invalid city'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $saveData = $this->request->data['City'];

            $saveData['staff_id'] = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $this->City->id = $id;
                if ($this->City->field('delete_flg') == 0) {
                    $saveData['deleted'] = date("Y-m-d H:i:s", time());
                }
            } else {
                $saveData['deleted'] = null;
            }

            if ($this->City->save($saveData)) {
                $this->Session->setFlash('市区町村を編集しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('市区町村の編集に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->City->recursive = -1;
            $city = $this->City->find('first', array('conditions' => array('City.id'=> $id)));
            $this->request->data['City'] = $city['City'];
            $areaList = $this->Area->find('list', array('conditions' => array('Area.prefecture_id' => $city['City']['prefecture_id'])));
            $this->set('areaList', $areaList);
        }
    }

    /**
     * ajax用 都道府県に応じたエリアをgetする
     * @param string $prefectureId
     */
    public function get_area_list($prefectureId = '')
    {
        $this->autoRender = false;
        if (!empty($prefectureId) && $this ->request->is('ajax')) {
            $areaList = $this->Area->find('list', array('conditions' => array('Area.prefecture_id' => $prefectureId, 'Area.delete_flg' => 0)));
            return json_encode($areaList);
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

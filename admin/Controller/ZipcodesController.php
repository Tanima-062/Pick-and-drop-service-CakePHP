<?php
class ZipcodesController extends AppController
{

    public $uses = array('Zipcode','Prefecture', 'City');

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
    }

    /**
     * 一覧画面処理
     *
     * @return void
     */
    public function index()
    {
        $this->Zipcode->recursive = -1;

        $conditions = array();

        // 都道府県
        if (!empty($this->request->query['Prefecture'])) {
            $conditions['prefecture_id'] = $this->request->query['Prefecture'];
        } else {
            $conditions['prefecture_id'] = '1';
            $this->request->query['Prefecture'] = '1';
        }
        // 郵便番号
        if (!empty($this->request->query['Code'])) {
            if (strlen($this->request->query['Code']) == 7) {
                $conditions['zipcode'] = $this->request->query['Code'];
            } else {
                $conditions['zipcode LIKE'] = $this->request->query['Code'] . '%';
            }
        }

        $this->request->data['Zipcode'] = $this->request->query;
        $this->paginate = array('conditions'=> $conditions,'order' => 'Zipcode.id asc');

        $this->set('zipcodes', $this->paginate());

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
            $saveData = $this->request->data['Zipcode'];

            $saveData['staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $saveData['deleted'] = date("Y-m-d H:i:s", time());
            }

            if ($this->Zipcode->save($saveData)) {
                $this->Session->setFlash('郵便番号を追加しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('郵便番号の登録に失敗しました', 'default', array('class' => 'alert alert-error'));
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
        if (!$this->Zipcode->exists($id)) {
            throw new NotFoundException(__('Invalid zipcode'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $saveData = $this->request->data['Zipcode'];

            $saveData['staff_id']  = $this->cdata['id'];
            if ($saveData['delete_flg'] == 1) {
                $this->Zipcode->id = $id;
                if ($this->Zipcode->field('delete_flg') == 0) {
                    $saveData['deleted'] = date("Y-m-d H:i:s", time());
                }
            } else {
                $saveData['deleted'] = null;
            }

            if ($this->Zipcode->save($saveData)) {
                $this->Session->setFlash('郵便番号を編集しました', 'default', array('class' => 'alert alert-success'));
                $this->redirectReferer();
            } else {
                $this->Session->setFlash('郵便番号の編集に失敗しました', 'default', array('class' => 'alert alert-error'));
            }
        } else {
            $this->Zipcode->recursive = -1;
            $zipcode = $this->Zipcode->find('first', array('conditions' => array('Zipcode.id'=> $id)));
            $this->request->data['Zipcode'] = $zipcode['Zipcode'];
            $cityList = $this->City->find(
                'list',
                array(
                    'conditions' => array(
                        'City.prefecture_id' => $zipcode['Zipcode']['prefecture_id'],
                        'City.delete_flg' => 0
                    )
                )
            );
            $this->set('cityList', $cityList);
        }
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

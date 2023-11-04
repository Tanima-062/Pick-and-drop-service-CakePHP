<?php
App::uses('AppModel', 'Model');
/**
 * CreditCard Model
 *
 * @property Staff $Staff
 * @property ClientCard $ClientCard
 */
class CreditCard extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_relative_url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'staff_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delete_flg' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ClientCard' => array(
			'className' => 'ClientCard',
			'foreignKey' => 'credit_card_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function getCreditCard() {

		$options = array(
			'conditions' => array(
				'delete_flg' => 0,
			),
		);

		return $this->find('list', $options);
	}

	public function getCreditCardList($clientId) {
		$options = array (
				'conditions' => array (
						'ClientCard.delete_flg' => 0,
						'ClientCard.client_id' => $clientId
				),
				'joins'=> array(
							array(
								'type'=>'Left',
								'table'=>'client_cards',
								'alias'=>'ClientCard',
								'conditions'=>'ClientCard.credit_card_id = CreditCard.id'
							)
				),
				'fields'=> array(
						'CreditCard.name',
						'CreditCard.image_relative_url',
				)
		);

		return $this->find('list', $options);

	}

}

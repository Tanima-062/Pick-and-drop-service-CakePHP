<?php

App::uses('AppModel', 'Model');
App::import('Vendor', 'imageResizeUpLoad');

/**
 * AgentOrganizedPrice Model
 *
 */
class AgentOrganizedPrice extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [];

    /**
     * hasMany associations
     *
     * @var array
     *
     */
    public $hasMany = [];

}

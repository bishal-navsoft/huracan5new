<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class HsseRemidialTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('hsse_remidials');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        // Define associations
        // 'remidial_createby' => AdminMasters.id
        $this->belongsTo('CreatedByAdmin', [
            'className' => 'AdminMasters',
            'foreignKey' => 'remidial_createby',
            'joinType' => 'LEFT',
        ]);

        // 'remidial_responsibility' => AdminMasters.id
        $this->belongsTo('ResponsibleAdmin', [
            'className' => 'AdminMasters',
            'foreignKey' => 'remidial_responsibility',
            'joinType' => 'LEFT',
        ]);

        // 'remidial_priority' => Priorities.id
        $this->belongsTo('Priority', [
            'className' => 'Priorities',
            'foreignKey' => 'remidial_priority',
            'joinType' => 'LEFT',
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ],
            ],
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('report_no')
            ->requirePresence('report_no', 'create')
            ->notEmptyString('report_no');

        $validator
            ->date('remidial_create')
            ->allowEmptyDate('remidial_create');

        $validator
            ->integer('remidial_createby')
            ->allowEmptyString('remidial_createby');

        $validator
            ->integer('remidial_responsibility')
            ->allowEmptyString('remidial_responsibility');

        $validator
            ->integer('remidial_priority')
            ->allowEmptyString('remidial_priority');

        $validator
            ->scalar('remidial_summery')
            ->allowEmptyString('remidial_summery');

        $validator
            ->date('remidial_closure_date')
            ->allowEmptyDate('remidial_closure_date');

        $validator
            ->scalar('isblocked')
            ->maxLength('isblocked', 1)
            ->inList('isblocked', ['Y', 'N'])
            ->allowEmptyString('isblocked');

        $validator
            ->scalar('isdeleted')
            ->maxLength('isdeleted', 1)
            ->inList('isdeleted', ['Y', 'N'])
            ->allowEmptyString('isdeleted');

        return $validator;
    }
}

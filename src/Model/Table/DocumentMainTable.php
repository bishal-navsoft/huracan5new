<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DocumentMainTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('document_mains');
        $this->setDisplayField('report_no');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AdminMasters', [
            'foreignKey' => 'created_by',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AdminMasters', [
            'className' => 'AdminMasters',
            'foreignKey' => 'validate_by',
            'propertyName' => 'validator',
        ]);
        $this->belongsTo('DocumentationTypes', [
            'foreignKey' => 'd_type',
        ]);
        $this->belongsTo('BusinessTypes', [
            'foreignKey' => 'business_unit',
        ]);
        $this->belongsTo('Fieldlocations', [
            'foreignKey' => 'field_location',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country',
        ]);

        $this->hasMany('DocumentAttachments', [
            'foreignKey' => 'report_id',
            'dependent' => true,
        ]);
        $this->hasMany('DocumentLinks', [
            'foreignKey' => 'report_id',
            'dependent' => true,
        ]);
        $this->hasMany('LdjsEmails', [
            'foreignKey' => 'leid',
            'conditions' => ['LdjsEmails.type' => 'document'],
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('report_no')
            ->maxLength('report_no', 255)
            ->requirePresence('report_no', 'create')
            ->notEmptyString('report_no');

        $validator
            ->date('create_date')
            ->allowEmptyDate('create_date');

        $validator
            ->date('validation_date')
            ->allowEmptyDate('validation_date');

        $validator
            ->date('revalidate_date')
            ->allowEmptyDate('revalidate_date');

        $validator
            ->integer('d_type')
            ->allowEmptyString('d_type');

        $validator
            ->integer('business_unit')
            ->allowEmptyString('business_unit');

        $validator
            ->integer('field_location')
            ->allowEmptyString('field_location');

        $validator
            ->integer('country')
            ->allowEmptyString('country');

        $validator
            ->integer('validate_by')
            ->allowEmptyString('validate_by');

        $validator
            ->scalar('summary')
            ->allowEmptyString('summary');

        $validator
            ->scalar('details')
            ->allowEmptyString('details');

        $validator
            ->scalar('isblocked')
            ->maxLength('isblocked', 1)
            ->notEmptyString('isblocked');

        $validator
            ->scalar('isdeleted')
            ->maxLength('isdeleted', 1)
            ->notEmptyString('isdeleted');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('created_by', 'AdminMasters'), ['errorField' => 'created_by']);
        $rules->add($rules->existsIn('validate_by', 'AdminMasters'), ['errorField' => 'validate_by']);
        $rules->add($rules->existsIn('d_type', 'DocumentationTypes'), ['errorField' => 'd_type']);
        $rules->add($rules->existsIn('business_unit', 'BusinessTypes'), ['errorField' => 'business_unit']);
        $rules->add($rules->existsIn('field_location', 'Fieldlocations'), ['errorField' => 'field_location']);
        $rules->add($rules->existsIn('country', 'Countries'), ['errorField' => 'country']);

        return $rules;
    }
}
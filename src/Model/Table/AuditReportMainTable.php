<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AuditReportMainTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('audit_report_mains');
        $this->setDisplayField('report_no');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AdminMasters', [
            'foreignKey' => 'created_by',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AdminMasters', [
            'className' => 'AdminMasters',
            'foreignKey' => 'reporter',
            'propertyName' => 'reporter_admin',
        ]);
        $this->belongsTo('AuditTypes', [
            'foreignKey' => 'audit_type',
        ]);
        $this->belongsTo('BusinessTypes', [
            'foreignKey' => 'business_unit',
        ]);
        $this->belongsTo('Clients', [
            'foreignKey' => 'client',
        ]);
        $this->belongsTo('Fieldlocations', [
            'foreignKey' => 'field_location',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country',
        ]);

        $this->hasMany('AuditRemidials', [
            'foreignKey' => 'report_no',
            'dependent' => true,
        ]);
        $this->hasMany('AuditAttachments', [
            'foreignKey' => 'report_id',
            'dependent' => true,
        ]);
        $this->hasMany('AuditLinks', [
            'foreignKey' => 'report_id',
            'dependent' => true,
        ]);
        $this->hasOne('AuditClients', [
            'foreignKey' => 'report_id',
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
            ->date('event_date')
            ->allowEmptyDate('event_date');

        $validator
            ->date('closer_date')
            ->allowEmptyDate('closer_date');

        $validator
            ->integer('audit_type')
            ->allowEmptyString('audit_type');

        $validator
            ->integer('business_unit')
            ->allowEmptyString('business_unit');

        $validator
            ->integer('client')
            ->allowEmptyString('client');

        $validator
            ->integer('field_location')
            ->allowEmptyString('field_location');

        $validator
            ->integer('country')
            ->allowEmptyString('country');

        $validator
            ->integer('reporter')
            ->allowEmptyString('reporter');

        $validator
            ->integer('since_event')
            ->allowEmptyString('since_event');

        $validator
            ->integer('official')
            ->allowEmptyString('official');

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
        $rules->add($rules->existsIn('audit_type', 'AuditTypes'), ['errorField' => 'audit_type']);
        $rules->add($rules->existsIn('business_unit', 'BusinessTypes'), ['errorField' => 'business_unit']);
        $rules->add($rules->existsIn('client', 'Clients'), ['errorField' => 'client']);
        $rules->add($rules->existsIn('field_location', 'Fieldlocations'), ['errorField' => 'field_location']);
        $rules->add($rules->existsIn('country', 'Countries'), ['errorField' => 'country']);

        return $rules;
    }
}
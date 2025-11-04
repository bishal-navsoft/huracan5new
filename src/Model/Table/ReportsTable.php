<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class ReportsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('reports');
        $this->setPrimaryKey('id');
        $this->setDisplayField('report_no');

        $this->addBehavior('Timestamp');

        // Associations
        $this->belongsTo('IncidentSeverities', [
            'className' => 'App\Model\Table\IncidentSeverityTable',
            'foreignKey' => 'incident_severity',
            'joinType' => 'LEFT',
            'propertyName' => 'incident_severity_data',
        ]);

        $this->belongsTo('Clients', [
            'className' => 'App\Model\Table\ClientTable',
            'foreignKey' => 'client', 
            'joinType' => 'LEFT',
            'propertyName' => 'client_data',
        ]);

        $this->belongsTo('AdminMasters', [
            'className' => 'App\Model\Table\AdminMastersTable',
            'foreignKey' => 'created_by',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Incidents', [
            'foreignKey' => 'incident_type',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('BusinessTypes', [
            'foreignKey' => 'business_unit',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Fieldlocations', [
            'foreignKey' => 'field_location',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Residuals', [
            'foreignKey' => 'residual',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Potentials', [
            'foreignKey' => 'potential',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Countries', [
            'foreignKey' => 'country',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('HsseRemidials', [
            'className' => 'App\Model\Table\HsseRemidialTable',
            'foreignKey' => 'report_no',
            'bindingKey' => 'id',
        ]);

        $this->hasMany('HsseClients', [
            'className' => 'App\Model\Table\HsseClientTable',
            'foreignKey' => 'report_id',
        ]);

        $this->hasMany('HsseIncidents', [
            'className' => 'App\Model\Table\HsseIncidentTable',
            'foreignKey' => 'report_id',
        ]);

        $this->hasMany('HssePersonnels', [
            'className' => 'App\Model\Table\HssePersonnelsTable',
            'foreignKey' => 'report_id',
        ]);

        $this->hasMany('HsseAttachments', [
            'className' => 'App\Model\Table\HsseAttachmentTable',
            'foreignKey' => 'report_id',
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
            ->integer('incident_type')
            ->allowEmptyString('incident_type');

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
            ->integer('incident_severity')
            ->allowEmptyString('incident_severity');

        $validator
            ->integer('recorable')
            ->allowEmptyString('recorable');

        $validator
            ->integer('potential')
            ->allowEmptyString('potential');

        $validator
            ->integer('residual')
            ->allowEmptyString('residual');

        $validator
            ->scalar('summary')
            ->allowEmptyString('summary');

        $validator
            ->scalar('details')
            ->allowEmptyString('details');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

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
        $rules->add($rules->existsIn(['created_by'], 'AdminMasters'), ['errorField' => 'created_by']);
        $rules->add($rules->existsIn(['incident_severity'], 'IncidentSeverities'), ['errorField' => 'incident_severity']);
        $rules->add($rules->existsIn(['client'], 'Clients'), ['errorField' => 'client']);

        return $rules;
    }
}

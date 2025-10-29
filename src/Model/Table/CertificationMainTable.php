<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CertificationMainTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('certification_mains');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AdminMasters', [
            'foreignKey' => 'certificate_user',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CertificationLists', [
            'foreignKey' => 'cretficate_id',
        ]);

        $this->hasMany('CertificationEmails', [
            'foreignKey' => 'cid',
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('cretficate_id')
            ->requirePresence('cretficate_id', 'create')
            ->notEmptyString('cretficate_id');

        $validator
            ->integer('certificate_user')
            ->requirePresence('certificate_user', 'create')
            ->notEmptyString('certificate_user');

        $validator
            ->date('cert_date')
            ->requirePresence('cert_date', 'create')
            ->notEmptyDate('cert_date');

        $validator
            ->date('expire_date')
            ->requirePresence('expire_date', 'create')
            ->notEmptyDate('expire_date');

        $validator
            ->integer('valid_date')
            ->allowEmptyString('valid_date');

        $validator
            ->scalar('triner')
            ->maxLength('triner', 255)
            ->allowEmptyString('triner');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('certificate_user', 'AdminMasters'), ['errorField' => 'certificate_user']);
        $rules->add($rules->existsIn('cretficate_id', 'CertificationLists'), ['errorField' => 'cretficate_id']);

        return $rules;
    }
}
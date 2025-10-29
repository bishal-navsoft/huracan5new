<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class HsseClientsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('hsse_clients');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Reports', [
            'foreignKey' => 'report_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('report_id')
            ->requirePresence('report_id', 'create')
            ->notEmptyString('report_id');

        $validator
            ->scalar('well')
            ->allowEmptyString('well');

        $validator
            ->scalar('rig')
            ->allowEmptyString('rig');

        $validator
            ->scalar('clientncr')
            ->allowEmptyString('clientncr');

        $validator
            ->integer('clientreviewed')
            ->allowEmptyString('clientreviewed');

        $validator
            ->scalar('clientreviewer')
            ->allowEmptyString('clientreviewer');

        $validator
            ->scalar('wellsiterep')
            ->allowEmptyString('wellsiterep');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['report_id'], 'Reports'), ['errorField' => 'report_id']);

        return $rules;
    }
}
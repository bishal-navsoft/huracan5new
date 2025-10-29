<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class TeamsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('teams');
        $this->setPrimaryKey('id');
        $this->setDisplayField('team_name');

        $this->addBehavior('Timestamp');

        // Associations
        $this->belongsTo('Divisions', [
            'foreignKey' => 'division_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('team_name')
            ->maxLength('team_name', 255)
            ->requirePresence('team_name', 'create')
            ->notEmptyString('team_name');

        $validator
            ->integer('division_id')
            ->requirePresence('division_id', 'create')
            ->notEmptyString('division_id');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['division_id'], 'Divisions'), ['errorField' => 'division_id']);

        return $rules;
    }
}
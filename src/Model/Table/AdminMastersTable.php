<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class AdminMastersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('admin_masters');
        $this->setPrimaryKey('id');
        $this->setDisplayField('admin_user');

        $this->addBehavior('Timestamp');

        // Associations
        $this->belongsTo('RoleMasters', [
            'foreignKey' => 'role_master_id',
            'joinType' => 'INNER',
        ]);


    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('admin_user')
            ->maxLength('admin_user', 255)
            ->requirePresence('admin_user', 'create')
            ->notEmptyString('admin_user', 'Username is required')
            ->add('admin_user', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('admin_pass')
            ->maxLength('admin_pass', 255)
            ->requirePresence('admin_pass', 'create')
            ->notEmptyString('admin_pass', 'Password is required');

        $validator
            ->email('admin_email')
            ->requirePresence('admin_email', 'create')
            ->notEmptyString('admin_email', 'Email is required');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 255)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name', 'First name is required');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name', 'Last name is required');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 20)
            ->allowEmptyString('phone');



        $validator
            ->integer('role_master_id')
            ->requirePresence('role_master_id', 'create')
            ->notEmptyString('role_master_id');

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
        $rules->add($rules->isUnique(['admin_user']), ['errorField' => 'admin_user']);
        $rules->add($rules->isUnique(['admin_email']), ['errorField' => 'admin_email']);
        $rules->add($rules->existsIn(['role_master_id'], 'RoleMasters'), ['errorField' => 'role_master_id']);


        return $rules;
    }
}


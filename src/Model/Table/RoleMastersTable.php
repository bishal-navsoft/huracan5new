<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class RoleMastersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('role_masters');
        $this->setPrimaryKey('id');
        $this->setDisplayField('role_name');

        $this->addBehavior('Timestamp');

        // Define associations
        $this->hasMany('AdminMasters', [
            'foreignKey' => 'role_master_id',
        ]);

        $this->hasMany('RolePermissions', [
            'foreignKey' => 'role_master_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('role_name')
            ->maxLength('role_name', 255)
            ->requirePresence('role_name', 'create')
            ->notEmptyString('role_name', 'Role name is required');

        $validator
            ->scalar('isdeleted')
            ->maxLength('isdeleted', 1)
            ->notEmptyString('isdeleted');

        return $validator;
    }
}

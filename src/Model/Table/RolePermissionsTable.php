<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class RolePermissionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('role_permissions');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Associations
        $this->belongsTo('RoleMasters', [
            'foreignKey' => 'role_master_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('AdminMenus', [
            'foreignKey' => 'admin_menu_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('role_master_id')
            ->requirePresence('role_master_id', 'create')
            ->notEmptyString('role_master_id');

        $validator
            ->integer('admin_menu_id')
            ->requirePresence('admin_menu_id', 'create')
            ->notEmptyString('admin_menu_id');

        $validator
            ->boolean('view')
            ->notEmptyString('view');

        $validator
            ->boolean('add')
            ->notEmptyString('add');

        $validator
            ->boolean('edit')
            ->notEmptyString('edit');

        $validator
            ->boolean('delete')
            ->notEmptyString('delete');

        $validator
            ->boolean('block')
            ->notEmptyString('block');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['role_master_id'], 'RoleMasters'), ['errorField' => 'role_master_id']);
        $rules->add($rules->existsIn(['admin_menu_id'], 'AdminMenus'), ['errorField' => 'admin_menu_id']);

        return $rules;
    }
}
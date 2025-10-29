<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AdminMenusTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('admin_menus');
        $this->setPrimaryKey('id');
        $this->setDisplayField('menu_name');

        $this->addBehavior('Timestamp');



        $this->hasMany('RolePermissions', [
            'foreignKey' => 'admin_menu_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('menu_name')
            ->maxLength('menu_name', 255)
            ->requirePresence('menu_name', 'create')
            ->notEmptyString('menu_name');

        $validator
            ->scalar('url')
            ->maxLength('url', 255)
            ->allowEmptyString('url');



        $validator
            ->integer('sort_order')
            ->allowEmptyString('sort_order');

        return $validator;
    }
}
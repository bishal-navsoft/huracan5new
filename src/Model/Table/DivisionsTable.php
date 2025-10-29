<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class DivisionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('divisions');
        $this->setPrimaryKey('id');
        $this->setDisplayField('division_name');

        $this->addBehavior('Timestamp');

        // Associations
        $this->hasMany('AdminMasters', [
            'foreignKey' => 'division_id',
        ]);

        $this->hasMany('Teams', [
            'foreignKey' => 'division_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('division_name')
            ->maxLength('division_name', 255)
            ->requirePresence('division_name', 'create')
            ->notEmptyString('division_name');

        return $validator;
    }
}
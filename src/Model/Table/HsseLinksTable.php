<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class HsseLinksTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Set the correct table name
        $this->setTable('hsse_links');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        // Define relationships
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
            ->notEmptyString('report_id', 'Report ID is required');

        $validator
            ->scalar('type')
            ->maxLength('type', 255)
            ->allowEmptyString('type');

        return $validator;
    }
}

<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class IncidentLocationTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Define the actual DB table
        $this->setTable('incident_locations');

        // Primary key
        $this->setPrimaryKey('id');

        // Display field (used in dropdowns, etc.)
        $this->setDisplayField('type');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('type')
            ->maxLength('type', 255)
            ->notEmptyString('type', 'Type is required');

        return $validator;
    }
}

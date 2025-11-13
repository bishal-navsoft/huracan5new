<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class HsseClientfeedbacksTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('hsse_clientfeedbacks');
        $this->setPrimaryKey('id');
    }
}

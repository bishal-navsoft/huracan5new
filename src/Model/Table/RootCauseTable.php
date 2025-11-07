<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class RootCauseTable extends Table
{
    public function initialize(array $config): void
    {
	parent::initialize($config);

	$this->setTable('root_causes');
	$this->setPrimaryKey('id');
	$this->setDisplayField('type');
    }
}
?>

<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ImmediateSubCauseTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('immediate_sub_causes');
        $this->setPrimaryKey('id');
        $this->setDisplayField('type');

        // ✅ Define the relationship properly
        $this->belongsTo('ImmediateCauses', [
            'foreignKey' => 'imm_cau_id',
            'joinType' => 'LEFT',
        ]);
    }
}

?>

<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class HssePersonnel extends Entity
{
    protected array $_accessible = [
        'report_id' => true,
        'personal_data' => true,
        'last_sleep' => true,
        'since_sleep' => true,
        'isblocked' => true,
        'isdeleted' => true,
        'created' => true,
        'modified' => true,
        'report' => true,
        'admin_master' => true,
    ];
}
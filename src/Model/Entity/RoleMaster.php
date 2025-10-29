<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class RoleMaster extends Entity
{
    protected array $_accessible = [
        'role_name' => true,
        'description' => true,
        'isdeleted' => true,
        'created' => true,
        'modified' => true,
        'admin_masters' => true,
        'role_permissions' => true,
    ];
}


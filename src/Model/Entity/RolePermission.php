<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class RolePermission extends Entity
{
    protected array $_accessible = [
        'role_master_id' => true,
        'admin_menu_id' => true,
        'view' => true,
        'add' => true,
        'edit' => true,
        'delete' => true,
        'block' => true,
        'created' => true,
        'modified' => true,
        'role_master' => true,
        'admin_menu' => true,
    ];

    protected array $_casts = [
        'view' => 'boolean',
        'add' => 'boolean',
        'edit' => 'boolean',
        'delete' => 'boolean',
        'block' => 'boolean',
    ];
}
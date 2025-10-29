<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AdminMenu extends Entity
{
    protected array $_accessible = [
        'menu_name' => true,
        'url' => true,
        'parent_id' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'parent_menu' => true,
        'child_menus' => true,
        'role_permissions' => true,
    ];
}
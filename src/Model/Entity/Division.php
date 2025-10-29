<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Division extends Entity
{
    protected array $_accessible = [
        'division_name' => true,
        'created' => true,
        'modified' => true,
        'admin_masters' => true,
        'teams' => true,
    ];
}
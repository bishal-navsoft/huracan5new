<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Team extends Entity
{
    protected array $_accessible = [
        'team_name' => true,
        'division_id' => true,
        'created' => true,
        'modified' => true,
        'division' => true,
    ];
}
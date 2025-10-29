<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class HsseClient extends Entity
{
    protected array $_accessible = [
        'report_id' => true,
        'well' => true,
        'rig' => true,
        'clientncr' => true,
        'clientreviewed' => true,
        'clientreviewer' => true,
        'wellsiterep' => true,
        'created' => true,
        'modified' => true,
        'report' => true,
    ];
}
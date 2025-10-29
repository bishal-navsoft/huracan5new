<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CertificationMain extends Entity
{
    protected $_accessible = [
        'cretficate_id' => true,
        'certificate_user' => true,
        'cert_date' => true,
        'expire_date' => true,
        'valid_date' => true,
        'triner' => true,
        'created' => true,
        'modified' => true,
        'admin_master' => true,
        'certification_list' => true,
        'certification_emails' => true,
    ];

    protected $_hidden = [
        'certificate_user',
    ];
}
<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AdminMaster extends Entity
{
    // Allow mass assignment for these fields
    protected array $_accessible = [
        'admin_user' => true,
        'admin_pass' => true,
        'admin_email' => true,
        'first_name' => true,
        'last_name' => true,
        'phone' => true,
        'division_id' => true,
        'team_id' => true,
        'role_master_id' => true,
        'isblocked' => true,
        'isdeleted' => true,
        'created' => true,
        'modified' => true,
        'role_master' => true, // for the association
        'division' => true,
        'teams' => true,
    ];

    // Hide sensitive fields from JSON/array output
    protected array $_hidden = [
        'admin_pass',
    ];

    // Virtual fields
    protected array $_virtual = [
        'full_name',
    ];

    /**
     * Get full name virtual field
     *
     * @return string
     */
    protected function _getFullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}


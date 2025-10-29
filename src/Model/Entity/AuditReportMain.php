<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AuditReportMain extends Entity
{
    protected $_accessible = [
        'report_no' => true,
        'event_date' => true,
        'closer_date' => true,
        'audit_type' => true,
        'business_unit' => true,
        'client' => true,
        'field_location' => true,
        'country' => true,
        'reporter' => true,
        'since_event' => true,
        'official' => true,
        'summary' => true,
        'details' => true,
        'created_by' => true,
        'isblocked' => true,
        'isdeleted' => true,
        'created' => true,
        'modified' => true,
        'admin_master' => true,
        'reporter_admin' => true,
        'audit_type_detail' => true,
        'business_type' => true,
        'client_detail' => true,
        'fieldlocation' => true,
        'country_detail' => true,
        'audit_remidials' => true,
        'audit_attachments' => true,
        'audit_links' => true,
        'audit_client' => true,
    ];

    protected $_hidden = [
        'created_by',
    ];
}
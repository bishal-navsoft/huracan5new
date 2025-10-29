<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class DocumentMain extends Entity
{
    protected $_accessible = [
        'report_no' => true,
        'create_date' => true,
        'validation_date' => true,
        'revalidate_date' => true,
        'd_type' => true,
        'business_unit' => true,
        'field_location' => true,
        'country' => true,
        'validate_by' => true,
        'summary' => true,
        'details' => true,
        'created_by' => true,
        'isblocked' => true,
        'isdeleted' => true,
        'created' => true,
        'modified' => true,
        'admin_master' => true,
        'validator' => true,
        'documentation_type' => true,
        'business_type' => true,
        'fieldlocation' => true,
        'country_detail' => true,
        'document_attachments' => true,
        'document_links' => true,
        'ldjs_emails' => true,
    ];

    protected $_hidden = [
        'created_by',
        'validate_by',
    ];
}
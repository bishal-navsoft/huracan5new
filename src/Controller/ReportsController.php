<?php
declare(strict_types=1);
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Collection\Collection;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\Http\Response;
use Cake\I18n\FrozenTime;
use Cake\Utility\Hash;
use Cake\I18n\FrozenDate;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Log\Log;

use App\Model\Table\AdminMastersTable;
use App\Model\Table\RoleMastersTable;
use App\Model\Table\ReportsTable;
use App\Model\Table\IncidentTable;
use App\Model\Table\BusinessTypeTable;
use App\Model\Table\FieldlocationTable;
use App\Model\Table\ClientTable;
use App\Model\Table\IncidentSeverityTable;
use App\Model\Table\ResidualTable;
use App\Model\Table\PotentialTable;
use App\Model\Table\CountryTable;
use App\Model\Table\HsseClientTable;
use App\Model\Table\HsseIncidentTable;
use App\Model\Table\ImmediateCausesTable;
use App\Model\Table\ImmediateSubCauseTable;
use App\Model\Table\LossesTable;
use App\Model\Table\HsseInvestigationTable;
use App\Model\Table\HsseInvestigationDataTable;
use App\Model\Table\HssePersonnelTable;
use App\Model\Table\IncidentCategoryTable;
use App\Model\Table\IncidentSubCategoryTable;
use App\Model\Table\PriorityTable;
use App\Model\Table\HsseRemidialTable;
use App\Model\Table\RemidialEmailListTable;
use App\Model\Table\RootCauseTable;

class ReportsController extends AppController
{
    public AdminMastersTable $AdminMasters;
    public RoleMastersTable $RoleMasters;
    public ReportsTable $Reports;
    public IncidentTable $Incident;
    public BusinessTypeTable $BusinessType;
    public FieldlocationTable $Fieldlocation;
    public ClientTable $Client;
    public IncidentSeverityTable $IncidentSeverity;
    public ResidualTable $Residual;
    public PotentialTable $Potential;
    public CountryTable $Country;
    public HsseClientTable $HsseClient;
    public HsseIncidentTable $HsseIncident;
    public ImmediateCausesTable $ImmediateCauses;
    public ImmediateSubCauseTable $ImmediateSubCause;
    public LossesTable $Losses;
    public HsseInvestigationTable $HsseInvestigation;
    public HsseInvestigationDataTable $HsseInvestigationData;
    public HssePersonnelTable $HssePersonnel;
    public IncidentCategoryTable $IncidentCategory;
    public IncidentSubCategoryTable $IncidentSubCategory;
    public PriorityTable $Priority;
    public HsseRemidialTable $HsseRemidial;
    public RemidialEmailListTable $RemidialEmailList;
    public RootCauseTable $RootCause;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent("Flash");
        // load models explicitly instead of $uses
        $this->Reports = $this->fetchTable('Reports');
        $this->AdminMasters = $this->fetchTable('AdminMasters');
        //$this->RoleMasters = $this->fetchTable('RoleMasters');
        $this->Incident = $this->fetchTable('Incident');
        $this->BusinessType = $this->fetchTable('BusinessType');
        $this->Fieldlocation = $this->fetchTable('Fieldlocation');
        $this->Client = $this->fetchTable('Client');
        $this->IncidentSeverity = $this->fetchTable('IncidentSeverity');
        $this->Residual = $this->fetchTable('Residual');
        $this->Potential = $this->fetchTable('Potential');
        $this->Country = $this->fetchTable('Country');
        $this->HsseClient = $this->fetchTable('HsseClient');
        $this->HsseIncident = $this->fetchTable('HsseIncident');
        $this->ImmediateCauses = $this->fetchTable('ImmediateCauses');
        $this->ImmediateSubCause = $this->fetchTable('ImmediateSubCause');
        $this->Losses = $this->fetchTable('Losses');
        $this->HsseInvestigation = $this->fetchTable('HsseInvestigation');
        $this->HsseInvestigationData = $this->fetchTable('HsseInvestigationData');
        $this->HssePersonnel = $this->fetchTable('HssePersonnel');
        $this->IncidentCategory = $this->fetchTable('IncidentCategory');
        $this->IncidentSubCategory = $this->fetchTable('IncidentSubCategory');
        $this->Priority = $this->fetchTable('Priority');
        $this->HsseRemidial = $this->fetchTable('HsseRemidial'); 
        $this->RemidialEmailList = $this->fetchTable('RemidialEmailList');
        $this->viewBuilder()->setLayout("after_adminlogin_template");
        $this->RootCause = $this->fetchTable('RootCause');
    }

    // public $name = 'Reports';
    // public $uses = array('Report','RemidialEmailList','DocumentMain','JobLink','SuggestionMain','JhaMain','LessonMain','JnReportMain','AuditReportMain','SqReportMain','JobReportMain','Incident','BusinessType','Fieldlocation','Client','HsseLink','IncidentLocation','IncidentSeverity','Country','AdminMaster','HsseClient','HsseIncident','HssePersonnel','RolePermission','RoleMaster','AdminMenu','IncidentCategory','Loss','IncidentSubCategory','Residual','HsseAttachment','HsseRemidial','Priority','ImmediateCauses','ImmediateSubCause','HsseInvestigation','RootCause','HsseInvestigationData','Potential','HsseClientfeedback');
    // public $helpers = array('Html','Form','Session','Js');
    // public $components = array('RequestHandler', 'Cookie', 'Resizer');

    /*public function reportHsseList()
    {
        $this->_checkAdminSession(); // assuming this exists in AppController
        $this->_getRoleMenuPermission(); // legacy helper method
        $this->grid_access();
        $this->report_hsse_link();
        $this->viewBuilder()->setLayout("after_adminlogin_template");
        dd($this->viewBuilder()->getTemplate());

        // To print the layout being used:
        // debug($this->viewBuilder()->getLayout());
        $data = $this->request->getData();
        // dd("wertyuki");
        // dd($this->_checkAdminSession());
        // dd($data);
        $limit = $data["Report"]["limit"] ?? 50;
        $action = $data["Report"]["action"] ?? "all";

        $this->set(compact("limit", "action"));

        $session = $this->request->getSession();
        // dd($session);
        $session->write("limit", $limit);
        $session->delete("filter");
        $session->delete("value");
        $session->delete("idBollen");

        $reports = $this->Reports
            ->find()
            ->where(["Reports.isdeleted" => "N"])
            ->order(["Reports.id" => "DESC"])
            ->limit($limit)
            ->all()
            ->toArray();
        // dd($reports);

        // handle session admin data (must be array-accessible)
        $adminData = $session->read("adminData");
        // dd($adminData);
        // debug( $session->read('adminData'),$session->read());
        $idBoolen = [];

        foreach ($reports as $report) {
            // dd("as");
            // if (
            //     ($adminData['AdminMaster']['id'] ?? null) == $report['created_by']
            //     || ($adminData['RoleMaster']['id'] ?? null) == 1
            // ) {
            //     $idBoolen[] = 1;
            // } else {
            //     $idBoolen[] = 0;
            // }

            if (
                ($adminData->id ?? null) == $report["created_by"] ||
                ($adminData->role_master_id ?? null) == 1
            ) {
                $idBoolen[] = 1;
            } else {
                $idBoolen[] = 0;
            }
        }

        $session->write("idBollen", $idBoolen);

        $this->set("reports", $reports);
    }*/
    public function reportHsseList(): void
    {
        // Ensure session and permissions
        $this->_checkAdminSession(); 
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->report_hsse_link();

        // Use correct layout
        $this->viewBuilder()->setLayout('after_adminlogin_template');
        //dd($this->viewBuilder()->getTemplate());
        // Get session
        $session = $this->request->getSession();

        // Read any POST/GET data safely
        $data = $this->request->getData() ?: [];
        //dd($data);
        // Default values
        $limit = $data['Report']['limit'] ?? 50;
        $action = $data['Report']['action'] ?? 'all';

        // Store some parameters in session
        $session->write('limit', $limit);
        $session->delete('filter');
        $session->delete('value');
        $session->delete('idBollen');

        // Query reports
        $reports = $this->Reports->find()
            ->where(['Reports.isdeleted' => 'N'])
            ->order(['Reports.id' => 'DESC'])
            ->limit($limit)
            ->all();
        //dd($reports);
        // Handle admin session data (object or array)
        $adminData = $session->read('adminData');

        $idBoolean = [];
        foreach ($reports as $report) {
            $createdBy = $report->created_by ?? null;
            $isSuperAdmin = false;
            $adminId = null;
            $roleId = null;

            // Handle both entity or array structures safely
            if (is_array($adminData)) {
                $adminId = $adminData['AdminMaster']['id'] ?? null;
                $roleId = $adminData['RoleMaster']['id'] ?? null;
            } elseif (is_object($adminData)) {
                $adminId = $adminData->id ?? null;
                $roleId = $adminData->role_master_id ?? null;
            }

            // Check permissions
            if ($adminId == $createdBy || $roleId == 1) {
                $idBoolean[] = 1;
            } else {
                $idBoolean[] = 0;
            }
        }

        // Store result
        $session->write('idBollen', $idBoolean);

        // Pass variables to view
        $this->set(compact('limit', 'action', 'reports'));
    }


    public function getAllReport($action = "all")
    {
        $this->request->allowMethod(["get", "post"]);
        $this->viewBuilder()->setLayout("ajax");
        $this->autoRender = false;

        // Get session
        $session = $this->request->getSession();

        if (!$session->check("adminData")) {
            $data = [
                "admins" => [],
                "total" => 0,
                "status" => "error",
                "message" => "Session expired",
                "_serialize" => ["admins", "total", "status", "message"],
            ];
            return $this->response->withType('application/json')->withStringBody(json_encode($data));
        }

        $adminData = $session->read('adminData') ?? [];
        $loggedInAdminId = $adminData['id'] ?? null;
        $loggedInRoleId = $adminData['RoleMaster']['id'] ?? null;

        $condition = ["Reports.isdeleted" => "N"];
        //dd($condition);
        // Filtering
        $filter = $this->request->getQuery("filter");
        $value = $this->request->getQuery("value");

        if ($filter && $value) {
            switch ($filter) {
                case "report_no":
                    $condition["UPPER(Reports.report_no) LIKE"] =
                        strtoupper($value) . "%";
                    break;

                case "client_name":
                    $client = $this->Reports->Clients
                        ->find()
                        ->where([
                            "UPPER(Clients.name) LIKE" =>
                                strtoupper($value) . "%",
                        ])
                        ->first();
                    if ($client) {
                        $condition["Reports.client"] = $client->id;
                    }
                    break;

                case "creater_name":
                    $names = explode(" ", $value);
                    $firstName = $names[0];
                    $lastName = end($names);
                    $admin = $this->Reports->AdminMasters
                        ->find()
                        ->where([
                            "AdminMasters.first_name LIKE" => "%{$firstName}%",
                            "AdminMasters.last_name LIKE" => "%{$lastName}%",
                        ])
                        ->first();
                    if ($admin) {
                        $condition["Reports.created_by"] = $admin->id;
                    }
                    break;

                case "event_date_val":
                    $parts = explode("/", $value);
                    if (count($parts) === 3) {
                        $day = str_pad($parts[0], 2, "0", STR_PAD_LEFT);
                        $month = str_pad($parts[1], 2, "0", STR_PAD_LEFT);
                        $year = "20" . $parts[2];
                        $condition[
                            "Reports.event_date"
                        ] = "{$year}-{$month}-{$day}";
                    }
                    break;
            }
        }

        // Get pagination parameters
        $start = (int) ($this->request->getQuery("start")
            ? $this->request->getQuery("start")
            : 0);
        $limit = $this->request->getQuery("limit");

        // Convert limit properly
        if ($limit === "all" || $limit === null || $limit === "") {
            $limit = 60;
        } else {
            $limit = (int) $limit;
        }

        // Calculate total count without pagination
        $total = $this->Reports
            ->find()
            ->where($condition)
            ->count();
        //dd($total);
        // dd([
        //     'limit' => $limit,
        //     'start' => $start
        // ]);
        // Query for paginated results
        $reports = $this->Reports
            ->find()
             ->contain([
                 "Clients",
                 "AdminMasters",
                 "IncidentSeverities",
                 "HsseRemidials",
             ])
            ->where($condition)
            ->order(["Reports.id" => "DESC"])
            ->limit($limit)
            ->offset($start)
            ->all();
        //debug($reports->sql());
        //dd($reports->count());
        $adminArray = [];
        $idBoolean = [];

        foreach ($reports as $rec) {
            // Default permissions
            $permissions = [
                "edit_permit" => false,
                "view_permit" => false,
                "delete_permit" => false,
                "block_permit" => false,
                "unblock_permit" => false,
                "checkbox_permit" => false,
                "blockHideIndex" => true,
                "unblockHideIndex" => true,
            ];

            if ($loggedInAdminId == $rec->created_by || $loggedInRoleId == 1) {
                $permissions["blockHideIndex"] = $rec->isblocked === "N";
                $permissions["unblockHideIndex"] = $rec->isblocked !== "N";
            } else {
                $permissions = array_map(fn($v) => true, $permissions);
            }

            // Format event date
            $eventDate = !empty($rec->event_date) ? (new FrozenTime($rec->event_date))->format('d/M/y') : '';

            // Incident severity safe access
            $incidentSeverity = "";
            if (
                !empty($rec->incident_severity) &&
                is_object($rec->incident_severity)
            ) {
                $incidentSeverity =
                    '<font color="' .
                    h($rec->incident_severity->color_code) .
                    '">' .
                    h($rec->incident_severity->type) .
                    "</font>";
            }

            // Client name safe access
            $clientName = "";
            if (!empty($rec->client) && is_object($rec->client)) {
                $clientName = h($rec->client->name);
            } else {
                $clientName = "N/A";
            }

            // Remidials counting
            $remidial_close = $remidial_open = [];
            foreach ((array)$rec->hsse_remidials as $r) {
                if ($r->remidial_closure_date !== "0000-00-00") {
                    $remidial_close[] = $r->id;
                } else {
                    $remidial_open[] = $r->id;
                }
            }
            $remidial_summary =
                count($remidial_close) . "/" . count($remidial_open);

            // Prepare record for frontend
            $adminArray[] = [
                "id" => $rec->id,
                "report_no" => $rec->report_no,
                "event_date" => $rec->event_date,
                "closure_date" => $rec->closure_date,
                "event_date_val" => $eventDate,
                "client" => $rec->client_id ?? null,
                "client_name" => $clientName,
                "incident_severity_name" => $incidentSeverity,
                "creater_name" => $rec->admin_master
                    ? $rec->admin_master->first_name .
                        " " .
                        $rec->admin_master->last_name
                    : "",
                "remidial" => $remidial_summary,
                "summary" => $rec->summary,
                "isblocked" => $rec->isblocked,
                "edit_permit" => $permissions["edit_permit"],
                "view_permit" => $permissions["view_permit"],
                "delete_permit" => $permissions["delete_permit"],
                "block_permit" => $permissions["block_permit"],
                "unblock_permit" => $permissions["unblock_permit"],
                "checkbox_permit" => $permissions["checkbox_permit"],
                "blockHideIndex" => $permissions["blockHideIndex"],
                "unblockHideIndex" => $permissions["unblockHideIndex"],
            ];

            // Used for frontend disabling checkboxes if edit not allowed
            $idBoolean[] = $permissions["edit_permit"] ? 0 : 1;
        }

        $_SESSION["idBollen"] = $idBoolean;

        $responseData = [
            "admins" => $adminArray,
            "total" => $total,
            "status" => $action,
            "idBollen" => $idBoolean,
            "_serialize" => ["admins", "total", "status", "idBollen"],
        ];
        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($responseData));
        
    }

    public function reportBlock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $reportId) {
            $report = $this->Reports->get($reportId);
            $report->isblocked = 'Y';
            $this->Reports->save($report);
        }
        
        $this->autoRender = false;
        echo 'ok';
    }

    public function reportUnblock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $reportId) {
            $report = $this->Reports->get($reportId);
            $report->isblocked = 'N';
            $this->Reports->save($report);
        }
        
        $this->autoRender = false;
        echo 'ok';
    }

    public function mainDelete(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        
        if (!empty($data['id'])) {
            $idArray = explode('^', $data['id']);
            $connection = $this->Reports->getConnection();
            
            foreach ($idArray as $id) {
                // Use transactions for data integrity
                $connection->transactional(function() use ($connection, $id) {
                    $connection->execute('DELETE FROM hsse_incidents WHERE report_id = ?', [$id]);
                    $connection->execute('DELETE FROM hsse_investigation_datas WHERE report_id = ?', [$id]);
                    $connection->execute('DELETE FROM hsse_personnels WHERE report_id = ?', [$id]);
                    $connection->execute('DELETE FROM hsse_remidials WHERE report_no = ?', [$id]);
                    $connection->execute('DELETE FROM hsse_attachments WHERE report_id = ?', [$id]);
                    $connection->execute('DELETE FROM hsse_clients WHERE report_id = ?', [$id]);
                    $connection->execute('DELETE FROM remidial_email_lists WHERE report_id = ? AND report_type = ?', [$id, 'hsse']);
                    $connection->execute('DELETE FROM reports WHERE id = ?', [$id]);
                });
            }
            echo 'ok';
        } else {
            $this->redirect(['action' => 'reportHsseList']);
        }
    }

    public function displayteam(): void
    {
        $this->viewBuilder()->setLayout('ajax');
        
        $data = $this->request->getData();
        if (!empty($data)) {
            $teamlist = $this->Teams->find()
                ->where(['division_id' => $data['id']])
                ->all();

            if (!$teamlist->isEmpty()) {
                $this->set('teamlist', $teamlist);
            }
        }
    }

    public function addReportMain($id = null)
    {
        // Check admin session & permissions
        $this->_checkAdminSession();
        $this->grid_access();
        $this->_getRoleMenuPermission();
        // Load all necessary data
        $incidentDetail = $this->Incident->find()->where(["incident_type" => "hsse"])->all();
        $businessDetail = $this->BusinessType->find()->where(["rtype" => "all"])->all();
        $fieldlocationDetail = $this->Fieldlocation->find()->all();
        $clientDetail = $this->Client->find()->all();
        $incidentSeverityDetail = $this->IncidentSeverity->find()->where(["servrity_type" => "ssh"])->all();
        $residualDetail = $this->Residual->find()->all();
        $potentialDetail = $this->Potential->find()->all();
        $countryDetail = $this->Country->find()->all();
        $userDetail = $this->AdminMasters->find()->all();
        $this->set(
            compact(
                "residualDetail",
                "potentialDetail",
                "incidentDetail",
                "businessDetail",
                "fieldlocationDetail",
                "clientDetail",
                "countryDetail",
                "userDetail",
                "incidentSeverityDetail"
            )
        );

        // Decode ID if present
        $decodedId = $id ? base64_decode($id) : null;

        // Check for client feedback
        $clientFeedback = 0;
        if ($decodedId) {
            $clientDetailHsse = $this->HsseClient
                ->find()
                ->where(["report_id" => $decodedId])
                ->first();
            if ($clientDetailHsse) {
                $clientFeedback = ($clientDetailHsse->clientreviewed == 3) ? 1 : 0;
            }
        }
        $this->set("client_feedback", $clientFeedback);

        $session = $this->request->getSession();
        $adminData = $session->read("adminData");

        // Ensure adminData exists in session
        if (empty($adminData)) {
            // You might fetch logged-in admin data from DB or Auth here
            $defaultAdmin = $this->AdminMasters->find()->first();
            if ($defaultAdmin) {
                $adminData = [
                    "AdminMaster" => ["id" => $defaultAdmin->id],
                    "RoleMaster" => ["id" => 1], // default to admin if unsure
                    "first_name" => $defaultAdmin->first_name,
                    "last_name" => $defaultAdmin->last_name,
                ];
                $session->write("adminData", $adminData);
            }
        }

        // If no ID — Add new report
        if ($decodedId === null) {
            
            $adminName = $adminData["first_name"] . " " . $adminData["last_name"];
            $reportNo = date("YmdHis");

            $this->set([
                "id" => 0,
                "event_date" => "",
                "since_event_hidden" => 0,
                "heading" => "Add HSSE Report (Main)",
                "button" => "Submit",
                "report_no" => "",
                "closer_date" => "00-00-0000",
                "incident_type" => "",
                "created_date" => "",
                "business_unit" => "",
                "client" => "",
                "field_location" => "",
                "incident_severity" => "",
                "recordable" => "",
                "potential" => "",
                "residual" => "",
                "summary" => "",
                "details" => "",
                "reporter" => "",
                "cnt" => 13,
                "created_by" => $adminName,
                "reportno" => $reportNo,
            ]);
            return;
        }

        // Editing existing report
        // $this->hsse_client_tab();

        $reportDetail = $this->Reports->get($decodedId, [
            "contain" => ["AdminMasters"],
        ]); //dd($reportDetail);
        if (!$reportDetail) {
            $this->Flash->error(__("Report not found."));
            return $this->redirect(["action" => "index"]);
        }
        // Write session values safely
        $session->write("report_create", $reportDetail->created_by);
        $clientTab = ($reportDetail->client == 9) ? 0 : 1;
        $session->write("clienttab", $clientTab);
        $this->set("clienttab", $clientTab);
        $adminData = $this->AdminMasters->get($reportDetail->created_by);
        // Format dates from Y-m-d to d-m-Y
        $formatDate = function ($date) {
            if (empty($date)) {
                return "";
            }

            // If it's a Cake\I18n\Date or DateTime object
            if ($date instanceof \Cake\I18n\Date || $date instanceof \DateTimeInterface) {
                return $date->format("d-m-Y");
            }

            // Otherwise, assume it's a string like "YYYY-MM-DD"
            if (is_string($date) && strpos($date, "-") !== false) {
                $parts = explode("-", $date);
                if (count($parts) === 3) {
                    return "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                }
            }

            return "";
        };

        $closedDate = $formatDate($reportDetail->closer_date);
        //$eventDate = $formatDate($reportDetail->event_date);
        $eventDate = "";
        if (!empty($reportDetail->event_date)) {
            $eventDate = $reportDetail->event_date->format("d-m-Y");
        }

        $this->set([
            "id" => $decodedId,
            "event_date" => $eventDate,
            "since_event_hidden" => $reportDetail->since_event,
            "since_event" => $reportDetail->since_event,
            "heading" => "Update HSSE Report (Main)",
            "button" => "Update",
            "reportno" => $reportDetail->report_no,
            "closer_date" => $closedDate,
            "incident_type" => $reportDetail->incident_type,
            "cnt" => $reportDetail->country,
            "created_date" => "",
            "business_unit" => $reportDetail->business_unit,
            "client" => $reportDetail->client,
            "field_location" => $reportDetail->field_location,
            "incident_severity" => $reportDetail->incident_severity,
            "recordable" => $reportDetail->recorable,
            "potential" => $reportDetail->potential,
            "residual" => $reportDetail->residual,
            "summary" => $reportDetail->summary,
            "details" => $reportDetail->details,
            "reporter" => $reportDetail->reporter,
            "created_by" =>
                $adminData->first_name . " " . $adminData->last_name,
        ]);
    }

    public function reportprocess(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->_checkAdminSession();
        $this->autoRender = false;
        
        $data = $this->request->getData();
        
        if ($data['add_report_main_form']['id'] == 0) {
            $res = 'add';
            $report = $this->Reports->newEntity();
        } else {
            $res = 'update';
            $report = $this->Reports->get($data['add_report_main_form']['id']);
        }
        
        // Format event date
        if (!empty($data['event_date'])) {
            $evndate = explode('-', $data['event_date']);
            $eventDate = $evndate[2] . '-' . $evndate[0] . '-' . $evndate[1];
        } else {
            $eventDate = null;
        }
        
        // Format closer date
        if (isset($data['closer_date']) && !empty($data['closer_date'])) {
            $clsdate = explode('-', $data['closer_date']);
            $closerDate = $clsdate[2] . '-' . $clsdate[0] . '-' . $clsdate[1];
        } else {
            $closerDate = '0000-00-00';
        }
        
        $session = $this->request->getSession();
        $adminData = $session->read('adminData');
        
        $reportData = [
            'event_date' => $eventDate,
            'closer_date' => $closerDate,
            'incident_type' => $data['incident_type'],
            'business_unit' => $data['business_unit'],
            'client' => $data['client'],
            'field_location' => $data['field_location'],
            'country' => $data['country'],
            'reporter' => $data['reporter'],
            'incident_severity' => $data['incident_severity'],
            'recorable' => $data['recorable'],
            'report_no' => $data['report_no'],
            'since_event' => $data['since_event'],
            'created_by' => $adminData->id,
            'potential' => $data['potential'],
            'residual' => $data['residual'],
            'summary' => $data['add_report_main_form']['summary'],
            'details' => $data['add_report_main_form']['details']
        ];
        
        $report = $this->Reports->patchEntity($report, $reportData);
        
        if ($this->Reports->save($report)) {
            if ($res == 'add') {
                $lastReport = base64_encode($report->id);
            } else {
                $lastReport = base64_encode($data['add_report_main_form']['id']);
            }
            
            $redirect = ($data['client'] == 0) ? 'Personal' : 'Client';
            echo $res . '~' . $lastReport . '~' . $redirect;
        } else {
            echo 'fail~0~0';
        }
    }
	
    public function addReportClient($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout("after_adminlogin_template");

        $this->set("id", 0);

        $decodedId = base64_decode($id);

        // Fetch report details
        $reportdetail = $this->Reports->get($decodedId, [
            "fields" => ["id", "report_no"],
        ]);

        $this->set("report_number", $reportdetail->report_no);

        // Fetch client details
        $clientdetail = $this->HsseClient
            ->find()
            ->where(["report_id" => $decodedId])
            ->first();

        if ($clientdetail) {
            $this->set("heading", "Update Client Data");
            $this->set("button", "Update");
            $this->set("id", $clientdetail->id);
            $this->set("well", $clientdetail->well);
            $this->set("rig", $clientdetail->rig);
            $this->set("clientncr", $clientdetail->clientncr);
            $this->set("clientreviewed", $clientdetail->clientreviewed);
            $this->set("report_id", $clientdetail->report_id);

            if ($clientdetail->clientreviewed == 3) {
                $this->set("clientreviewed_style", 'style="display:block"');
                $this->set("clientreviewer", $clientdetail->clientreviewer);
                $this->set("client_feedback", 1);
            } else {
                $this->set("clientreviewed_style", 'style="display:none"');
                $this->set("clientreviewer", "");
                $this->set("client_feedback", 0);
            }

            $this->set("wellsiterep", $clientdetail->wellsiterep);
        } else {
            $this->set("heading", "Add Client Data");
            $this->set("button", "Submit");
            $this->set("id", 0);
            $this->set("well", "");
            $this->set("rig", "");
            $this->set("clientncr", "");
            $this->set("clientreviewed", 1);
            $this->set("report_id", $decodedId);
            $this->set("clientreviewer", "");
            $this->set("wellsiterep", "");
            $this->set("clientreviewed_style", 'style="display:none"');
            $this->set("client_feedback", 0);
        }
    }

    public function hsseclientprocess()
    {
        $data = $this->request->getData();
        $this->viewBuilder()->setLayout('ajax'); // CakePHP 3.x way
        $this->_checkAdminSession();

        $hsseClientData = null;
        $clientdetail = $this->HsseClient->find('all', [
            'conditions' => [
                'HsseClient.report_id' => $data['report_id'],
            ]
        ])->first();
        if ($clientdetail) {
            $res = 'update';
            $hsseClientData = $this->HsseClient->patchEntity($clientdetail, $data);
        } else {
            $res = 'add';
            $hsseClientData = $this->HsseClient->newEntity($data);
        }
        if ($this->HsseClient->save($hsseClientData)) {
            echo $res;
        } else {
            echo 'fail';
        }
        exit();
    }

    public function addReportView($id = null): void
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->hsse_client_tab();
        $this->viewBuilder()->setLayout('after_adminlogin_template');
        
        $reportDetail = $this->Reports->find()
            ->where(['Reports.id' => base64_decode($id)])
            ->first();
            
        $session = $this->request->getSession();
        $session->write('report_create', $reportDetail->created_by);
        $this->set('id', base64_decode($id));
        
        if (!empty($reportDetail->event_date)) {
            $eventDate = $reportDetail->event_date->format('d/m/Y');
        } else {
            $eventDate = '';
        }
        $clientDetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => base64_decode($id)])
            ->first();
            
        if ($reportDetail->client == 10) {
            $this->set('clienttabshow', 0);
            $this->set('clienttab', 0);
        } else {
            $this->set('clienttabshow', 1);
            $this->set('clienttab', $clientDetail ? 1 : 0);
        }
        
        if ($clientDetail) {
            $this->set('client_feedback', $clientDetail->clientreviewed == 3 ? 1 : 0);
        } else {
            $this->set('client_feedback', 0);
        }
        $this->set("event_date", $event_date);
        $this->set(
            "since_event_hidden",
            $reportdetail[0]["Report"]["since_event"]
        );
        $this->set("since_event", $reportdetail[0]["Report"]["since_event"]);
        $this->set("reportno", $reportdetail[0]["Report"]["report_no"]);
        $closerDate = $reportdetail[0]["Report"]["closer_date"] ?? null;
        if (!empty($closerDate) && $closerDate !== "0000-00-00") {
            $date = new \Cake\I18n\Date($closerDate);
            $closedt = $date->format('d/m/Y');
        } else {
            $closedt = "00/00/0000";
        }
        $this->set("closer_date", $closedt);
        $this->Incident = $this->fetchTable('Incident');
        $incident_detail = [];
        $incidentId = $reportdetail[0]["Report"]["incident_type"] ?? null;
        if ($incidentId) {
            $incident_detail = $this->Incident->find()
                ->where(['Incident.id' => $incidentId])
                ->all()
                ->toArray();
        }
        if (!empty($incident_detail)) {
            $this->set("incident_type", $incident_detail[0]["Incident"]["type"]);
        } else {
            $this->set("incident_type", null);
        }
        $user_detail = $this->AdminMaster->find("all", [
            "conditions" => [
                "AdminMaster.id" => $reportdetail[0]["Report"]["created_by"],
            ],
        ]);
        $this->set(
            "created_by",
            $user_detail[0]["AdminMaster"]["first_name"] .
                " " .
                $user_detail[0]["AdminMaster"]["last_name"]
        );
        $this->set("created_date", "");
        $business_unit_detail = $this->BusinessType->find("all", [
            "conditions" => [
                "BusinessType.id" =>
                    $reportdetail[0]["Report"]["business_unit"],
            ],
        ]);
        $this->set(
            "business_unit",
            $business_unit_detail[0]["BusinessType"]["type"]
        );
        $client_detail = $this->Client->find("all", [
            "conditions" => [
                "Client.id" => $reportdetail[0]["Report"]["client"],
            ],
        ]);
        $this->set("client", $client_detail[0]["Client"]["name"]);
        $fieldlocation = $this->Fieldlocation->find("all", [
            "conditions" => [
                "Fieldlocation.id" =>
                    $reportdetail[0]["Report"]["field_location"],
            ],
        ]);
        $this->set("fieldlocation", $fieldlocation[0]["Fieldlocation"]["type"]);
        $incidentLocation = $this->IncidentLocation->find("all", [
            "conditions" => [
                "IncidentLocation.id" =>
                    $reportdetail[0]["Report"]["field_location"],
            ],
        ]);
        $this->set(
            "incidentLocation",
            $incidentLocation[0]["IncidentLocation"]["type"]
        );
        $countrty = $this->Country->find("all", [
            "conditions" => [
                "Country.id" => $reportdetail[0]["Report"]["country"],
            ],
        ]);
        $this->set("countrty", $countrty[0]["Country"]["name"]);
        $report_detail = $this->AdminMaster->find("all", [
            "conditions" => [
                "AdminMaster.id" => $reportdetail[0]["Report"]["reporter"],
                "AdminMaster.isdeleted" => "N",
                "AdminMaster.isblocked" => "N",
            ],
        ]);
        $this->set(
            "reporter",
            $report_detail[0]["AdminMaster"]["first_name"] .
                " " .
                $report_detail[0]["AdminMaster"]["last_name"]
        );
        $incidentSeverity_detail = $this->IncidentSeverity->find("all", [
            "conditions" => [
                "IncidentSeverity.id" =>
                    $reportdetail[0]["Report"]["incident_severity"],
            ],
        ]);
        $this->set(
            "incidentseveritydetail",
            $incidentSeverity_detail[0]["IncidentSeverity"]["type"]
        );
        $this->set(
            "incidentseveritydetailcolor",
            'style="background-color:' .
                $incidentSeverity_detail[0]["IncidentSeverity"]["color_code"] .
                '"'
        );
        $residual_detail = $this->Residual->find("all", [
            "conditions" => [
                "Residual.id" => $reportdetail[0]["Report"]["residual"],
            ],
        ]);
        $this->set("residual", $residual_detail[0]["Residual"]["type"]);
        if ($residual_detail[0]["Residual"]["color_code"] != "") {
            $this->set(
                "residualcolor",
                'style="background-color:' .
                    $residual_detail[0]["Residual"]["color_code"] .
                    '"'
            );
        } else {
            $this->set("residualcolor", "");
        }
        if ($reportdetail[0]["Report"]["recorable"] == 1) {
            $this->set("recorable", "Yes");
            $this->set("recorablecolor", 'style="background-color:#FF0000"');
        } elseif ($reportdetail[0]["Report"]["recorable"] == 2) {
            $this->set("recorable", "No");
            $this->set("recorablecolor", 'style="background-color:#40FF00"');
        }
        $potentilal_detail = $this->Potential->find("all", [
            "conditions" => [
                "Potential.id" => $reportdetail[0]["Report"]["potential"],
            ],
        ]);
        $this->set("potential", $potentilal_detail[0]["Potential"]["type"]);
        if ($potentilal_detail[0]["Potential"]["color_code"] != "") {
            $this->set(
                "potentialcolor",
                'style="background-color:' .
                    $potentilal_detail[0]["Potential"]["color_code"] .
                    '"'
            );
        } else {
            $this->set("potentialcolor", "");
        }
        $this->set("summary", $reportdetail[0]["Report"]["summary"]);
        $this->set("details", $reportdetail[0]["Report"]["details"]);
        $this->set(
            "created_by",
            $_SESSION["adminData"]["AdminMaster"]["first_name"] .
                " " .
                $_SESSION["adminData"]["AdminMaster"]["last_name"]
        );

        /************************clientdata***************************/
        $clientdetail = $this->HsseClient->find("all", [
            "conditions" => ["HsseClient.report_id" => base64_decode($id)],
        ]);
        if (count($clientdetail) > 0) {
            $this->set("well", $clientdetail[0]["HsseClient"]["well"]);
            $this->set("rig", $clientdetail[0]["HsseClient"]["rig"]);
            $this->set(
                "clientncr",
                $clientdetail[0]["HsseClient"]["clientncr"]
            );
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 1) {
                $this->set("clientreviewed", "N/A");
            } elseif ($clientdetail[0]["HsseClient"]["clientreviewed"] == 2) {
                $this->set("clientreviewed", "N/A");
            }
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                $this->set("clientreviewed", "Yes");
            }
            $this->set(
                "report_id",
                $clientdetail[0]["HsseClient"]["report_id"]
            );
            $this->set(
                "clientreviewer",
                $clientdetail[0]["HsseClient"]["clientreviewer"]
            );
            $this->set(
                "wellsiterep",
                $clientdetail[0]["HsseClient"]["wellsiterep"]
            );
        } else {
            $this->set("well", "");
            $this->set("rig", "");
            $this->set("clientncr", "");
            $this->set("clientreviewed", "");
            $this->set("clientreviewer", "");
            $this->set("wellsiterep", "");
        }
        /************************indidentdata***************************/
        $incidentdetail = $this->HsseIncident->find("all", [
            "conditions" => [
                "HsseIncident.report_id" => base64_decode($id),
                "HsseIncident.isdeleted" => "N",
                "HsseIncident.isblocked" => "N",
            ],
        ]);
        //echo '<pre>';
        //print_r($incidentdetail);
        $incidentdetailHolder = [];
        if (count($incidentdetail) > 0) {
            for ($i = 0; $i < count($incidentdetail); $i++) {
                if (
                    $incidentdetail[$i]["HsseIncident"]["date_incident"] != ""
                ) {
                    $incidt = explode(
                        "-",
                        $incidentdetail[$i]["HsseIncident"]["date_incident"]
                    );
                    $incidentdetailHolder[$i]["date_incident"] =
                        $incidt[2] . "/" . $incidt[1] . "/" . $incidt[0];
                } else {
                    $incidentdetailHolder[$i]["date_incident"] = "";
                }
                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_time"] != ""
                ) {
                    $incidentdetailHolder[$i]["incident_time"] =
                        $incidentdetail[$i]["HsseIncident"]["incident_time"];
                } else {
                    $incidentdetailHolder[$i]["incident_time"] = "";
                }
                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_severity"] !=
                    0
                ) {
                    $incidentSeverity_type = $this->IncidentSeverity->find(
                        "all",
                        [
                            "conditions" => [
                                "IncidentSeverity.id" =>
                                    $incidentdetail[$i]["HsseIncident"][
                                        "incident_severity"
                                    ],
                            ],
                        ]
                    );
                    $incidentdetailHolder[$i]["incident_severity"] =
                        $incidentSeverity_type[0]["IncidentSeverity"]["type"];
                } else {
                    $incidentdetailHolder[$i]["incident_severity"] = "";
                }
                if ($incidentdetail[$i]["HsseIncident"]["incident_loss"] != 0) {
                    $incidentLoss_type = $this->Losses->find("all", [
                        "conditions" => [
                            "Losses.id" =>
                                $incidentdetail[$i]["HsseIncident"][
                                    "incident_loss"
                                ],
                        ],
                    ]);
                    $incidentdetailHolder[$i]["incident_loss"] =
                        $incidentLoss_type[0]["Loss"]["type"];
                } else {
                    $incidentdetailHolder[$i]["incident_loss"] = "";
                }

                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_category"] !=
                    0
                ) {
                    $incident_category_type = $this->IncidentCategory->find(
                        "all",
                        [
                            "conditions" => [
                                "IncidentCategory.id" =>
                                    $incidentdetail[$i]["HsseIncident"][
                                        "incident_category"
                                    ],
                            ],
                        ]
                    );
                    $incidentdetailHolder[$i]["incident_category"] =
                        $incident_category_type[0]["IncidentCategory"]["type"];
                } else {
                    $incidentdetailHolder[$i]["incident_category"] = "";
                }
                if (
                    $incidentdetail[$i]["HsseIncident"][
                        "incident_sub_category"
                    ] != 0
                ) {
                    $incident_sub_category_type = $this->IncidentSubCategory->find(
                        "all",
                        [
                            "conditions" => [
                                "IncidentSubCategory.id" =>
                                    $incidentdetail[$i]["HsseIncident"][
                                        "incident_sub_category"
                                    ],
                            ],
                        ]
                    );
                    $incidentdetailHolder[$i]["incident_sub_category"] =
                        $incident_sub_category_type[0]["IncidentSubCategory"][
                            "type"
                        ];
                } else {
                    $incidentdetailHolder[$i]["incident_sub_category"] = "";
                }
                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_summary"] !=
                    ""
                ) {
                    $incidentdetailHolder[$i]["incident_summary"] =
                        $incidentdetail[$i]["HsseIncident"]["incident_summary"];
                } else {
                    $incidentdetailHolder[$i]["incident_summary"] = "";
                }
                if ($incidentdetail[$i]["HsseIncident"]["detail"] != "") {
                    $incidentdetailHolder[$i]["detail"] =
                        $incidentdetail[$i]["HsseIncident"]["detail"];
                } else {
                    $incidentdetailHolder[$i]["detail"] = "";
                }
                $incidentdetailHolder[$i]["id"] =
                    $incidentdetail[$i]["HsseIncident"]["id"];
                $incidentdetailHolder[$i]["isblocked"] =
                    $incidentdetail[$i]["HsseIncident"]["isblocked"];
                $incidentdetailHolder[$i]["isdeleted"] =
                    $incidentdetail[$i]["HsseIncident"]["isdeleted"];
                $incidentdetailHolder[$i]["incident_no"] =
                    $incidentdetail[$i]["HsseIncident"]["incident_no"];
                if (count($incidentdetail[$i]["HsseInvestigationData"]) > 0) {
                    for (
                        $v = 0;
                        $v <
                        count($incidentdetail[$i]["HsseInvestigationData"]);
                        $v++
                    ) {
                        $immidiate_cause = explode(
                            ",",
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "immediate_cause"
                            ]
                        );
                        if (
                            $immidiate_cause[0] != 0 ||
                            $immidiate_cause[0] != ""
                        ) {
                            $imdCause = $this->ImmediateCauses->find("all", [
                                "conditions" => [
                                    "ImmediateCauses.id" => $immidiate_cause[0],
                                ],
                            ]);
                            if (count($imdCause) > 0) {
                                $incidentdetailHolder[$i]["imd_cause"][] =
                                    $imdCause[0]["ImmediateCauses"]["type"];
                            }
                            if (
                                $immidiate_cause[1] != 0 ||
                                $immidiate_cause[1] != ""
                            ) {
                                $imdSubCause = $this->ImmediateSubCause->find(
                                    "all",
                                    [
                                        "conditions" => [
                                            "ImmediateSubCause.id" =>
                                                $immidiate_cause[1],
                                        ],
                                    ]
                                );
                                if (count($imdSubCause) > 0) {
                                    $incidentdetailHolder[$i][
                                        "imd_sub_cause"
                                    ][] =
                                        $imdSubCause[0]["ImmediateSubCause"][
                                            "type"
                                        ];
                                }
                            }
                        }
                        if (
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "comments"
                            ] != ""
                        ) {
                            $incidentdetailHolder[$i]["comment"] =
                                $incidentdetail[$i]["HsseInvestigationData"][
                                    $v
                                ]["comments"];
                        }
                        $incidentdetailHolder[$i]["investigation_block"] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "isblocked"
                            ];
                        $incidentdetailHolder[$i]["investigation_delete"] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "isdeleted"
                            ];
                        $incidentdetailHolder[$i]["investigation_no"][] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "investigation_no"
                            ];
                        $incidentdetailHolder[$i]["incident_no_investigation"] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "incident_no"
                            ];
                        if (
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "root_cause_id"
                            ] != 0
                        ) {
                            $explode_rootcause = explode(
                                ",",
                                $incidentdetail[$i]["HsseInvestigationData"][
                                    $v
                                ]["root_cause_id"]
                            );
                            for ($j = 0; $j < count($explode_rootcause); $j++) {
                                if ($explode_rootcause[$j] != 0) {
                                    $rootCauseDetail = $this->RootCause->find(
                                        "all",
                                        [
                                            "conditions" => [
                                                "RootCause.id" =>
                                                    $explode_rootcause[$j],
                                            ],
                                        ]
                                    );
                                    $incidentdetailHolder[$i]["root_cause_val"][
                                        $v
                                    ][$j] =
                                        $rootCauseDetail[0]["RootCause"][
                                            "type"
                                        ];
                                }
                            }
                        }
                        if (
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "remedila_action_id"
                            ] != ""
                        ) {
                            $explode_remedila_action_id = explode(
                                ",",
                                $incidentdetail[$i]["HsseInvestigationData"][
                                    $v
                                ]["remedila_action_id"]
                            );
                            for (
                                $k = 0;
                                $k < count($explode_remedila_action_id);
                                $k++
                            ) {
                                if (isset($explode_remedila_action_id[$k])) {
                                    $remDetail = $this->HsseRemidial->find(
                                        "all",
                                        [
                                            "conditions" => [
                                                "HsseRemidial.id" =>
                                                    $explode_remedila_action_id[
                                                        $k
                                                    ],
                                                "HsseRemidial.isdeleted" => "N",
                                                "HsseRemidial.isblocked" => "N",
                                            ],
                                        ]
                                    );
                                    $incidentdetailHolder[$i]["rem_val"][$v][
                                        $k
                                    ] =
                                        $remDetail[0]["HsseRemidial"][
                                            "remidial_summery"
                                        ];
                                    $incidentdetailHolder[$i]["rem_val_id"][$v][
                                        $k
                                    ] = $remDetail[0]["HsseRemidial"]["id"];
                                }
                            }
                        }
                        $incidentdetailHolder[$i]["view"][] = "yes";
                    }
                } else {
                    $incidentdetailHolder[$i]["view"][] = "no";
                }
            }
        }
        //print_r($incidentdetailHolder);
        $this->set("incidentdetailHolder", $incidentdetailHolder);
        /*************Incident - Personnel****************************/
        $personeldetail = $this->HssePersonnel->find("all", [
            "conditions" => [
                "HssePersonnel.report_id" => base64_decode($id),
                "HssePersonnel.isdeleted" => "N",
                "HssePersonnel.isblocked" => "N",
            ],
        ]);
        if (count($personeldetail) > 0) {
            $this->set("personeldata", 1);
            for ($i = 0; $i < count($personeldetail); $i++) {
                $user_detail = $this->AdminMaster->find("all", [
                    "conditions" => [
                        "AdminMaster.id" =>
                            $personeldetail[$i]["HssePersonnel"][
                                "personal_data"
                            ],
                        "AdminMaster.isblocked" => "N",
                        "AdminMaster.isdeleted" => "N",
                    ],
                ]);
                if (count($user_detail) > 0) {
                    $seniorty = explode(
                        " ",
                        $user_detail[0]["AdminMaster"]["created"]
                    );
                    $snr = explode("-", $seniorty[0]);
                    $snrdt = $snr[2] . "/" . $snr[1] . "/" . $snr[0];
                    $personeldetail[$i]["HssePersonnel"]["seniorty"] = $snrdt;
                    $personeldetail[$i]["HssePersonnel"]["name"] =
                        $user_detail[0]["AdminMaster"]["first_name"] .
                        "  " .
                        $user_detail[0]["AdminMaster"]["last_name"];
                    $personeldetail[$i]["HssePersonnel"]["position"] =
                        $user_detail[0]["RoleMaster"]["role_name"];
                }
            }
            $this->set("personeldetail", $personeldetail);
        } else {
            $this->set("personeldata", 0);
        }
        /*************Attachments****************************/
        $attachmentData = $this->HsseAttachment->find("all", [
            "conditions" => [
                "HsseAttachment.report_id" => base64_decode($id),
                "HsseAttachment.isdeleted" => "N",
                "HsseAttachment.isblocked" => "N",
            ],
        ]);
        if (count($attachmentData) > 0) {
            $this->set("attachmentData", $attachmentData);
            $this->set("attachmentTab", 1);
        } else {
            $this->set("attachmentData", "");
            $this->set("attachmentTab", 0);
        }
        /***********REMIDIAL ACTION****************************/
        $remidialdetail = $this->HsseRemidial->find("all", [
            "conditions" => [
                "HsseRemidial.report_no" => base64_decode($id),
                "HsseRemidial.isblocked" => "N",
                "HsseRemidial.isdeleted" => "N",
            ],
        ]);
        if (count($remidialdetail) > 0) {
            $this->set("remidial", 1);
            for ($i = 0; $i < count($remidialdetail); $i++) {
                $user_detail_createby = $this->AdminMaster->find("all", [
                    "conditions" => [
                        "AdminMaster.id" =>
                            $remidialdetail[$i]["HsseRemidial"][
                                "remidial_createby"
                            ],
                    ],
                ]);
                $remidialdetail[$i]["HsseRemidial"]["remidial_createby"] =
                    $user_detail_createby[0]["AdminMaster"]["first_name"] .
                    "  " .
                    $user_detail_createby[0]["AdminMaster"]["last_name"];
                $user_detail_reponsibilty = $this->AdminMaster->find("all", [
                    "conditions" => [
                        "AdminMaster.id" =>
                            $remidialdetail[$i]["HsseRemidial"][
                                "remidial_responsibility"
                            ],
                    ],
                ]);
                $remidialdetail[$i]["HsseRemidial"]["remidial_responsibility"] =
                    $user_detail_reponsibilty[0]["AdminMaster"]["first_name"] .
                    "  " .
                    $user_detail_reponsibilty[0]["AdminMaster"]["last_name"];
                $priority_detail = $this->Priority->find("all", [
                    "conditions" => [
                        "Priority.id" =>
                            $remidialdetail[$i]["HsseRemidial"][
                                "remidial_priority"
                            ],
                    ],
                ]);
                $remidialdetail[$i]["HsseRemidial"]["priority"] =
                    $priority_detail[0]["Priority"]["type"];
                $remidialdetail[$i]["HsseRemidial"]["priority_color"] =
                    'style="background-color:' .
                    $priority_detail[0]["Priority"]["colorcoder"] .
                    '"';
                $lastupdated = explode(
                    " ",
                    $remidialdetail[$i]["HsseRemidial"]["modified"]
                );
                $lastupdatedate = explode("-", $lastupdated[0]);
                $remidialdetail[$i]["HsseRemidial"]["lastupdate"] =
                    $lastupdatedate[1] .
                    "/" .
                    $lastupdatedate[2] .
                    "/" .
                    $lastupdatedate[0];
                $createdate = explode(
                    "-",
                    $remidialdetail[$i]["HsseRemidial"]["remidial_create"]
                );
                $remidialdetail[$i]["HsseRemidial"]["createRemidial"] = date(
                    "d-M-y",
                    mktime(
                        0,
                        0,
                        0,
                        $createdate[1],
                        $createdate[2],
                        $createdate[0]
                    )
                );
                if (
                    $remidialdetail[$i]["HsseRemidial"][
                        "remidial_closure_date"
                    ] == "0000-00-00"
                ) {
                    $closerDate = explode(
                        "-",
                        $remidialdetail[$i]["HsseRemidial"][
                            "remidial_closure_date"
                        ]
                    );
                    $remidialdetail[$i]["HsseRemidial"]["closeDate"] = "";
                    $remidialdetail[$i]["HsseRemidial"][
                        "remidial_closer_summary"
                    ] = "";
                } elseif (
                    $remidialdetail[$i]["HsseRemidial"][
                        "remidial_closure_date"
                    ] != "0000-00-00"
                ) {
                    $closerDate = explode(
                        "-",
                        $remidialdetail[$i]["HsseRemidial"][
                            "remidial_closure_date"
                        ]
                    );
                    $remidialdetail[$i]["HsseRemidial"]["closeDate"] = date(
                        "d-M-y",
                        mktime(
                            0,
                            0,
                            0,
                            $closerDate[1],
                            $closerDate[2],
                            $closerDate[0]
                        )
                    );
                }
            }
            $this->set("remidialdetail", $remidialdetail);
        } else {
            $this->set("remidialdetail", []);
            $this->set("remidial", 0);
        }
        /****************Investigation Team******************/
        $invetigationDetail = $this->HsseInvestigation->find("all", [
            "conditions" => [
                "HsseInvestigation.report_id" => base64_decode($id),
            ],
        ]);
        if (count($invetigationDetail)) {
            $condition =
                "AdminMaster.isblocked = 'N' AND AdminMaster.isdeleted = 'N' AND AdminMaster.id IN (" .
                $invetigationDetail[0]["HsseInvestigation"]["team_user_id"] .
                ")";
            $investigation_team = $this->AdminMaster->find("all", [
                "conditions" => $condition,
            ]);
            $this->set("invetigationDetail", $invetigationDetail);
            $this->set("investigation_team", $investigation_team);
        } else {
            $this->set("investigation_team", []);
        }
        /****************Incident Investigation******************/
        $incidentInvestigationDetail = $this->HsseInvestigationData->find(
            "all",
            [
                "conditions" => [
                    "HsseInvestigationData.report_id" => base64_decode($id),
                    "HsseInvestigationData.isdeleted" => "N",
                    "HsseInvestigationData.isblocked" => "N",
                ],
                "recursive" => 2,
            ]
        );
        if (count($incidentInvestigationDetail) > 0) {
            for ($i = 0; $i < count($incidentInvestigationDetail); $i++) {
                $incidentDetail = $this->HsseIncident->find("all", [
                    "conditions" => [
                        "HsseIncident.id" =>
                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["incident_id"],
                    ],
                ]);
                $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                    "incident_summary"
                ] = $incidentDetail[0]["HsseIncident"]["incident_summary"];
                $lossDetail = $this->Losses->find("all", [
                    "conditions" => [
                        "id" =>
                            $incidentDetail[0]["HsseIncident"]["incident_loss"],
                    ],
                ]);
                $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                    "loss"
                ] = $lossDetail[0]["Loss"]["type"];
                $immidiate_cause = explode(
                    ",",
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "immediate_cause"
                    ]
                );
                if ($immidiate_cause[0] != "") {
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "imd_cause"
                    ] = $this->ImmediateCauses->find("all", [
                        "conditions" => [
                            "ImmediateCauses.id" => $immidiate_cause[0],
                        ],
                    ]);
                }

                if (isset($immidiate_cause[1])) {
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "imd_sub_cause"
                    ] = $this->ImmediateSubCause->find("all", [
                        "conditions" => [
                            "ImmediateSubCause.id" => $immidiate_cause[1],
                        ],
                    ]);
                }

                if (
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "root_cause_id"
                    ] != 0
                ) {
                    $explode_rootcause = explode(
                        ",",
                        $incidentInvestigationDetail[$i][
                            "HsseInvestigationData"
                        ]["root_cause_id"]
                    );

                    for ($j = 0; $j < count($explode_rootcause); $j++) {
                        if ($explode_rootcause[$j] != 0) {
                            $rootCauseDetail = $this->RootCause->find("all", [
                                "conditions" => [
                                    "RootCause.id" => $explode_rootcause[$j],
                                ],
                            ]);
                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["root_cause_val"][$j] =
                                $rootCauseDetail[0]["RootCause"]["type"];
                        }
                    }
                }

                if (
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "remedila_action_id"
                    ] != ""
                ) {
                    $explode_remedila_action_id = explode(
                        ",",
                        $incidentInvestigationDetail[$i][
                            "HsseInvestigationData"
                        ]["remedila_action_id"]
                    );

                    for (
                        $k = 0;
                        $k < count($explode_remedila_action_id);
                        $k++
                    ) {
                        if (isset($explode_remedila_action_id[$k])) {
                            $remDetail = $this->HsseRemidial->find("all", [
                                "conditions" => [
                                    "HsseRemidial.id" =>
                                        $explode_remedila_action_id[$k],
                                    "HsseRemidial.isblocked" => "N",
                                    "HsseRemidial.isdeleted" => "N",
                                ],
                            ]);

                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["rem_val"][$k] =
                                $remDetail[0]["HsseRemidial"][
                                    "remidial_summery"
                                ];

                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["rem_val_id"][$k] =
                                $remDetail[0]["HsseRemidial"]["id"];
                        }
                    }
                }
            }
            $this->set(
                "incidentInvestigationDetail",
                $incidentInvestigationDetail
            );
        } else {
            $this->set("incidentInvestigationDetail", []);
        }
        /****************Client Feedback******************/

        if (count($clientdetail) > 0) {
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                $clientfeeddetail = $this->HsseClientfeedback->find("all", [
                    "conditions" => [
                        "HsseClientfeedback.report_id" => base64_decode($id),
                    ],
                ]);

                if (count($clientfeeddetail) > 0) {
                    if (
                        $clientfeeddetail[0]["HsseClientfeedback"][
                            "client_summary"
                        ] != ""
                    ) {
                        $this->set(
                            "clientfeedback_summary",
                            $clientfeeddetail[0]["HsseClientfeedback"][
                                "client_summary"
                            ]
                        );
                    } else {
                        $this->set("clientfeedback_summary", "");
                    }

                    if (
                        $clientfeeddetail[0]["HsseClientfeedback"][
                            "close_date"
                        ] != "0000-00-00"
                    ) {
                        $clientfeeddate = explode(
                            "-",
                            $clientfeeddetail[0]["HsseClientfeedback"][
                                "close_date"
                            ]
                        );
                        $clsdt = date(
                            "d-M-y",
                            mktime(
                                0,
                                0,
                                0,
                                $clientfeeddate[1],
                                $clientfeeddate[2],
                                $clientfeeddate[0]
                            )
                        );
                        $this->set("feedback_date", $clsdt);
                    } else {
                        $this->set("feedback_date", "");
                    }
                } else {
                    $this->set("clientfeedback_summary", "");
                    $this->set("feedback_date", "");
                }
            }
        }
    }

    public function addReportPersonal($report_id = null, $personnel_id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $this->set('id', 0);

        // Get active users
        $userDetails = $this->AdminMasters->find()
            ->where(['AdminMasters.isdeleted' => 'N', 'AdminMasters.isblocked' => 'N'])
            ->contain(['RoleMasters'])
            ->all();
        //debug($userDetails);
        foreach ($userDetails as $user) {
            $modifiedDate = $user->modified;
            $user->user_seniority = $modifiedDate->format('d/m/Y');
            $user->position_seniority = $user->role_master->role_name . "~" . $user->user_seniority . "~" . $user->id;
        }
        
        if(count($userDetails->toArray()) > 0){
            $userDetailsarr =$userDetails->toArray();
        }else{
            $userDetailsarr = [];
        }
        //debug($userDetailsarr);
        $this->set('userDetail', $userDetailsarr);
        
        // Get report detail
        $reportDetail = $this->Reports->get(base64_decode($report_id));
        $this->set('report_number', $reportDetail->report_no);

        // Get client feedback
        $clientDetail = $this->HsseClient->find()
            ->where(['report_id' => base64_decode($report_id)])
            ->first();

        if ($clientDetail) {
            $this->set('client_feedback', $clientDetail->clientreviewed == 3 ? 1 : 0);
        } else {
            $this->set('client_feedback', 0);
        }

        // Edit or Add personnel
        if ($personnel_id) {
            $personnelDetail = $this->HssePersonnel->get(base64_decode($personnel_id));
            $personalInfo = $this->AdminMasters->get($personnelDetail->personal_data, ['contain' => ['RoleMasters']]);

            $this->set([
                'heading' => 'Update Personal Data',
                'button' => 'Update',
                'id' => $personnelDetail->id,
                'pid' => $personnelDetail->personal_data,
                'report_id' => $personnelDetail->report_id,
                'time_last_sleep' => $personnelDetail->last_sleep,
                'time_since_sleep' => $personnelDetail->since_sleep,
                'person' => $personnelDetail->personal_data,
                'roll_name' => $personalInfo->role_master->role_name,
                'snr' => $personalInfo->modified->format('d/m/Y'),
                'styledisplay' => 'style="display:block"'
            ]);
        } else {
            $this->set([
                'heading' => 'Add Personal Data',
                'button' => 'Submit',
                'id' => 0,
                'pid' => 0,
                'report_id' => base64_decode($report_id),
                'time_last_sleep' => '',
                'time_since_sleep' => '',
                'person' => '',
                'roll_name' => '',
                'snr' => '',
                'styledisplay' => 'style="display:none"'
            ]);
        }
    }

    public function hssepersonnelprocess()
    {
        $this->autoRender = false;
        $this->viewBuilder()->setLayout('ajax'); // CakePHP 3.x layout
        $data = $this->request->getData();
        if (!isset($data['personal_data'])) {
            echo 'fail';
            return;
        }
        $personalid = explode("~", $data["personal_data"]);
        $res = "";
        // Check if updating existing record
        if (!empty($data["id"]) && $data["id"] != 0) {
            $existing = $this->HssePersonnel->get($data["id"]);
            
            if ($data["pid"] == $personalid[2]) {
                $entity = $this->HssePersonnel->patchEntity($existing, [
                    'personal_data' => $personalid[2],
                    'last_sleep' => $data['last_sleep'] ?? null,
                    'since_sleep' => $data['since_sleep'] ?? null,
                    'report_id' => $data['report_id'] ?? null
                ]);
                $res = "update";
            } else {
                // Check if another record exists with the same personal_data
                $personneldetail = $this->HssePersonnel->find()
                    ->where([
                        'personal_data' => $personalid[2],
                        'report_id' => $data['report_id']
                    ])
                    ->first();

                if ($personneldetail) {
                    echo "avl";
                    return;
                } else {
                    $entity = $this->HssePersonnel->patchEntity($existing, [
                        'personal_data' => $personalid[2],
                        'last_sleep' => $data['last_sleep'] ?? null,
                        'since_sleep' => $data['since_sleep'] ?? null,
                        'report_id' => $data['report_id'] ?? null
                    ]);
                    $res = "update";
                }
            }
        } else {
            // New record
            $personneldetail = $this->HssePersonnel->find()
                ->where([
                    'personal_data' => $personalid[2],
                    'report_id' => $data['report_id'],
                    'isdeleted' => 'N'
                ])
                ->first();
            if ($personneldetail) {
                echo "avl";
                return;
            } else {
                $entity = $this->HssePersonnel->newEntity([
                    'personal_data' => $personalid[2],
                    'last_sleep' => $data['last_sleep'] ?? null,
                    'since_sleep' => $data['since_sleep'] ?? null,
                    'report_id' => $data['report_id'] ?? null
                ]);
                $res = "add";
            }
        }

        // Save entity
        if ($this->HssePersonnel->save($entity)) {
            echo $res;
        } else {
            echo "fail";
        }
    }

    public function reportHssePerssonelList($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');
        $decodedId = base64_decode($id);

        // Fetch the report details safely
        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Fetch client details (only first record)
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $reportdetail->id])
            ->first();

        // Set client feedback flag
        if ($clientdetail && $clientdetail->clientreviewed == 3) {
            $this->set('client_feedback', 1);
        } else {
            $this->set('client_feedback', 0);
        }

        // Set basic variables for view
        $this->set('report_number', $reportdetail->report_no);
        $this->set('report_id', $id);
        $this->set('id', $decodedId);

        // Handle form data or defaults
        $data = $this->request->getData();
        if (!empty($data)) {
            $action = $data['HssePersonnel']['action'] ?? 'all';
            $limit = $data['HssePersonnel']['limit'] ?? 50;
        } else {
            $action = 'all';
            $limit = 50;
        }

        $this->set('action', $action);
        $this->set('limit', $limit);

        // Use CakePHP session object
        $session = $this->request->getSession();
        $session->write('action', $action);
        $session->write('limit', $limit);

        // Unset legacy session values if they exist
        $session->delete('filter');
        $session->delete('value');
    }

    public function getAllPersonnelList($report_id)
    {
        $this->request->allowMethod(['get', 'post']);
        $this->_checkAdminSession();
        $reportId = (int)$report_id;
        $query = $this->HssePersonnel->find()
            ->where([
                'report_id' => $reportId,
                'HssePersonnel.isdeleted' => 'N'
            ])
            ->contain(['AdminMasters.RoleMasters'])
            ->order(['HssePersonnel.id' => 'DESC']);

        // Filter by name if provided
        if (!empty($this->request->getQuery('filter')) && $this->request->getQuery('filter') === 'name') {
            $value = $this->request->getQuery('value');
            if ($value) {
                $names = explode(' ', $value);
                $firstName = $names[0];
                $lastName = end($names);

                $query = $query->matching('AdminMaster', function ($q) use ($firstName, $lastName) {
                    return $q->where([
                        'AdminMaster.first_name LIKE' => "%$firstName%",
                        'AdminMaster.last_name LIKE' => "%$lastName%"
                    ]);
                });
            }
        }

        // Handle pagination / limit
        $start = $this->request->getQuery('start') ?? 0;
        $limit = $this->request->getQuery('limit') ?? null;
        if ($limit !== 'all') {
            $query = $query->offset((int)$start)->limit((int)$limit);
        }

        $personnelList = $query->all();

        $adminArray = [];
        foreach ($personnelList as $person) {
            $isBlocked = $person->isblocked === 'N';
            $seniority = $person->admin_master->modified->format('d/m/Y');

            $adminArray[] = [
                'id' => $person->id,
                'personal_data' => $person->personal_data,
                'report_id' => $person->report_id,
                'last_sleep' => $person->last_sleep,
                'since_sleep' => $person->since_sleep,
                'blockHideIndex' => $isBlocked ? 'true' : 'false',
                'unblockHideIndex' => $isBlocked ? 'false' : 'true',
                'isdeletdHideIndex' => $isBlocked ? 'true' : 'false',
                'name' => $person->admin_master->first_name . ' ' . $person->admin_master->last_name,
                'seniority' => $seniority,
                'position' => $person->admin_master->role_master->role_name ?? ''
            ];
        }

        $response = [
            'total' => $personnelList->count(),
            'admins' => $adminArray
        ];

        // Return JSON for AJAX
        $this->response = $this->response->withType('application/json')
                                        ->withStringBody(json_encode($response));
        return $this->response;
    }

    public function personnel_block($id = null)
    {
        $this->request->allowMethod(['post', 'get']);
        $this->viewBuilder()->setLayout('ajax');

        if (!$id) {
            $this->set(['status' => 'error', 'message' => 'Invalid personnel ID', '_serialize' => ['status', 'message']]);
            return;
        }

        $idArray = explode("^", $id);
        foreach ($idArray as $personnelId) {
            $personnel = $this->HssePersonnel->get($personnelId);
            $personnel->isblocked = 'Y';
            $this->HssePersonnel->save($personnel);
        }

        $this->set(['status' => 'ok', '_serialize' => ['status']]);
    }

    public function personnel_unblock($id = null)
    {
        $this->request->allowMethod(['post', 'get']); // Allow GET/POST if needed
        $this->viewBuilder()->setLayout('ajax'); // For AJAX requests

        if (!$id) {
            $this->set('status', 'error');
            $this->set('message', 'Invalid personnel ID');
            $this->set('_serialize', ['status', 'message']);
            return;
        }

        $idArray = explode("^", $id);

        foreach ($idArray as $personnelId) {
            $personnel = $this->HssePersonnel->get($personnelId);
            $personnel->isblocked = 'N';
            $this->HssePersonnel->save($personnel);
        }

        $this->set('status', 'ok');
        $this->set('_serialize', ['status']);
    }

    public function personnelDelete()
    {
        $this->autoRender = false; // no view
        $this->request->allowMethod(['post']); // allow only POST for CSRF protection

        $idData = $this->request->getData('id'); // get POSTed 'id'
        
        if (!empty($idData)) {
            $idArray = explode("^", $idData);
            foreach ($idArray as $id) {
                $personnel = $this->HssePersonnel->get($id);
                $personnel = $this->HssePersonnel->patchEntity($personnel, [
                    'isdeleted' => 'Y'
                ]);
                $this->HssePersonnel->save($personnel);
            }
            echo "ok";
            return;
        } else {
            return $this->redirect(['action' => 'reportHsseList']);
        }
    }

    public function reportHsseIncidentList($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();

        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $decodedId = base64_decode($id);
        $this->set('id', $decodedId);

        // Fetch report details
        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Fetch related client details (first match)
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $reportdetail->id])
            ->first();

        // Determine client feedback status
        if ($clientdetail && $clientdetail->clientreviewed == 3) {
            $this->set('client_feedback', 1);
        } else {
            $this->set('client_feedback', 0);
        }

        // Set general view variables
        $this->set('report_id', $id);
        $this->set('report_number', $reportdetail->report_no);

        // Handle post data
        $data = $this->request->getData();
        if (!empty($data)) {
            $action = $data['HsseIncident']['action'] ?? 'all';
            $limit = $data['HsseIncident']['limit'] ?? 50;
        } else {
            $action = 'all';
            $limit = 50;
        }

        $this->set('action', $action);
        $this->set('limit', $limit);

        // Use CakePHP 3 session handling
        $session = $this->request->getSession();
        $session->write('action', $action);
        $session->write('limit', $limit);

        // Remove old filter and value keys from session
        $session->delete('filter');
        $session->delete('value');
    }

    public function getAllIncidentList($report_id = null)
    {
        $this->request->allowMethod(['post', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');
        $this->_checkAdminSession();

        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $HsseIncidents = $this->HsseIncident;
        $IncidentSeverity = $this->IncidentSeverity;
        $Losses = $this->Losses;
        $IncidentCategory = $this->IncidentCategory;
        $IncidentSubCategory = $this->IncidentSubCategory;

        // Base conditions
        $conditions = [
            'HsseIncident.report_id' => $report_id,
            'HsseIncident.isdeleted' => 'N',
        ];

        // Filtering
        $filter = $this->request->getData('filter');
        $value = $this->request->getData('value');

        if (!empty($filter) && !empty($value)) {
            switch ($filter) {
                case 'report_no':
                    $conditions["UPPER(HsseIncident.$filter) LIKE"] = strtoupper($value) . '%';
                    break;
            }
        }

        // Pagination
        $start = (int)$this->request->getData('start', 0);
        $limit = (int)$this->request->getData('limit', 25);

        // Count total
        $count = $HsseIncidents->find()
            ->where($conditions)
            ->count();

        // Fetch incidents with limit
        $query = $HsseIncidents->find()
            ->where($conditions)
            ->order(['HsseIncident.id' => 'DESC'])
            ->offset($start)
            ->limit($limit);

        $adminArray = [];
        foreach ($query as $index => $rec) {
            $data = $rec->toArray();

            // Add dynamic UI flags
            if ($data['isblocked'] === 'N') {
                $data['blockHideIndex'] = true;
                $data['unblockHideIndex'] = false;
                $data['isdeletdHideIndex'] = true;
            } else {
                $data['blockHideIndex'] = false;
                $data['unblockHideIndex'] = true;
                $data['isdeletdHideIndex'] = false;
            }

            $data['inc_no'] = $index + 1;

            // Incident Severity
            if (!empty($data['incident_severity'])) {
                $severity = $IncidentSeverity->find()
                    ->select(['type'])
                    ->where(['IncidentSeverity.id' => $data['incident_severity']])
                    ->first();
                $data['incident_severity_type'] = $severity ? $severity->type : '';
            } else {
                $data['incident_severity_type'] = '';
            }

            // Loss Type
            if (!empty($data['incident_loss'])) {
                $loss = $Losses->find()
                    ->select(['type'])
                    ->where(['Losses.id' => $data['incident_loss']])
                    ->first();
                $data['incident_loss_type'] = $loss ? $loss->type : 'N/A';
            } else {
                $data['incident_loss_type'] = 'N/A';
            }

            // Category Type
            if (!empty($data['incident_category'])) {
                $cat = $IncidentCategory->find()
                    ->select(['type'])
                    ->where(['IncidentCategory.id' => $data['incident_category']])
                    ->first();
                $data['incident_category_type'] = $cat ? $cat->type : 'N/A';
            } else {
                $data['incident_category_type'] = 'N/A';
            }

            // Sub-category Type
            if (!empty($data['incident_sub_category'])) {
                $subcat = $IncidentSubCategory->find()
                    ->select(['type'])
                    ->where(['IncidentSubCategory.id' => $data['incident_sub_category']])
                    ->first();
                $data['incident_sub_category_type'] = $subcat ? $subcat->type : 'N/A';
            } else {
                $data['incident_sub_category_type'] = 'N/A';
            }

            $adminArray[] = $data;
        }

        // Output JSON for ExtJS
        $response = [
            'total' => $count,
            'admins' => $adminArray,
        ];

        echo json_encode($response);
        return $this->response;
    }

    public function incidentBlock($id = null)
    {
        $this->request->allowMethod(['post', 'get']);

        if (empty($id)) {
            return $this->redirect(['action' => 'reportHsseList']);
        }

        $ids = explode('^', $id);
        $hsseIncidentTable = $this->getTableLocator()->get('HsseIncident');

        foreach ($ids as $incidentId) {
            $incident = $hsseIncidentTable->get($incidentId);
            $incident->isblocked = 'Y';
            $hsseIncidentTable->save($incident);
        }

        // You can choose to redirect or return a response instead of exit()
        return $this->redirect(['action' => 'reportHsseList']);
    }

    public function incidentUnblock($id = null)
    {
        $this->request->allowMethod(['post', 'get']);

        if (empty($id)) {
            return $this->redirect(['action' => 'reportHsseList']);
        }

        $ids = explode('^', $id);
        $hsseIncidentTable = $this->getTableLocator()->get('HsseIncident');

        foreach ($ids as $incidentId) {
            try {
                $incident = $hsseIncidentTable->get($incidentId);
                $incident->isblocked = 'N';
                $hsseIncidentTable->save($incident);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                // Skip invalid IDs or handle as needed
                continue;
            }
        }

        return $this->redirect(['action' => 'reportHsseList']);
    }

    public function incidentDelete()
    {
        $this->request->allowMethod(['post', 'ajax']);
        $this->autoRender = false; // no view

        $idData = $this->request->getData('id');

        $result = ['status' => 'error']; // default

        if (!empty($idData)) {
            $ids = explode('^', $idData);
            $hsseIncidentTable = $this->getTableLocator()->get('HsseIncident');

            foreach ($ids as $incidentId) {
                try {
                    $incident = $hsseIncidentTable->get($incidentId);
                    $incident->isdeleted = 'Y';
                    $hsseIncidentTable->save($incident);
                } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    continue;
                }
            }

            $result['status'] = 'ok';
        }

        // Force JSON output as string
        $json = json_encode($result);
        $this->response = $this->response
                            ->withType('application/json')
                            ->withStringBody($json);
        return $this->response;
    }

    public function addHsseIncident($report_id = null, $incident_id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $decodedReportId = base64_decode($report_id);
        $decodedIncidentId = $incident_id ? base64_decode($incident_id) : null;
        
        // Fetch severity and loss details
        $incidentSeverityDetail = $this->IncidentSeverity->find()
            ->where(['IncidentSeverity.servrity_type' => 'ssh'])
            ->all();
        $incidentSeverityDetailarr = $incidentSeverityDetail->toArray();
        $incidentLossDetail = $this->Losses->find()->all();
        $incidentLossDetailarr = $incidentLossDetail->toArray();
        $this->set(['incidentSeverityDetail'=>$incidentSeverityDetailarr, 'incidentLossDetail'=>$incidentLossDetailarr]);

        // Fetch report details
        $reportDetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedReportId])
            ->first();

        if (!$reportDetail) {
            $this->Flash->error(__('Invalid report.'));
            return $this->redirect(['controller' => 'Reports', 'action' => 'index']);
        }

        // Client detail
        $clientDetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $reportDetail->id])
            ->first();

        $clientFeedback = ($clientDetail && $clientDetail->clientreviewed == 3) ? 1 : 0;
        $this->set('client_feedback', $clientFeedback);
        $this->set('report_number', $reportDetail->report_no);

        // Incident detail
        $incidentDetail = null;
        if ($decodedIncidentId) {
            $incidentDetail = $this->HsseIncident->find()
                ->where(['HsseIncident.id' => $decodedIncidentId])
                ->first();
        }

        // If editing an incident
        if ($incidentDetail) {
            $incidentLoss = $incidentDetail->incident_loss ?? 0;
            $incidentCategory = $incidentDetail->incident_category ?? '';
            $incidentSubCategory = $incidentDetail->incident_sub_category ?? '';

            $incidentCategoryDetail = [];
            $incidentSubCategoryDetail = [];

            if ($incidentLoss != 0) {
                $incidentCategoryDetail = $this->IncidentCategory->find()
                    ->where(['IncidentCategory.loss_id' => $incidentLoss])
                    ->all();

                if (!$incidentCategoryDetail->isEmpty()) {
                    $firstCategory = $incidentCategoryDetail->first();
                    $incidentSubCategoryDetail = $this->IncidentSubCategory->find()
                        ->where(['IncidentSubCategory.loss_id' => $incidentLoss])
                        ->all();
                }
            }

            $dateIncident = $incidentDetail->date_incident ? $incidentDetail->date_incident->format('m-d-Y') : '';
            $this->set([
                'incident_loss' => $incidentLoss,
                'incident_category' => $incidentCategory,
                'incident_sub_category' => $incidentSubCategory,
                'incidentCategoryDetail' => $incidentCategoryDetail,
                'incidentSubCategoryDetail' => $incidentSubCategoryDetail,
                'date_incident' => $dateIncident,
                'time_incident' => $incidentDetail->incident_time ?? '',
                'incident_summary' => $incidentDetail->incident_summary ?? '',
                'report_id' => $incidentDetail->report_id ?? '',
                'detail' => $incidentDetail->detail ?? '',
                'heading' => 'Edit Incident Data',
                'button' => 'Update',
                'incident_id' => $decodedIncidentId,
                'incident_severity' => $incidentDetail->incident_severity ?? '',
                'incident_no' => $incidentDetail->incident_no ?? ''
            ]);
        } else {
            // Add mode
            // $firstLoss = $incidentLossDetail->first();
            // $incidentCategoryDetail = $firstLoss
            //     ? $this->IncidentCategory->find()->where(['IncidentCategory.loss_id' => $firstLoss->id])->all()
            //     : [];
            // $firstCategory = !$incidentCategoryDetail->isEmpty() ? $incidentCategoryDetail->first() : null;

            // $incidentSubCategoryDetail = $firstCategory
            //     ? $this->IncidentSubCategory->find()->where(['IncidentSubCategory.loss_category_id' => $firstCategory->id])->all()
            //     : [];

            $existingIncidents = $this->HsseIncident->find()
                ->where(['HsseIncident.report_id' => $decodedReportId])
                ->count();

            $incident_no = $existingIncidents + 1;

            $this->set([
                'incidentCategoryDetail' => '',
                'incidentSubCategoryDetail' => '',
                'heading' => 'Add Incident Data',
                'button' => 'Submit',
                'incident_id' => 0,
                'incident_no' => $incident_no,
                'report_id' => $decodedReportId,
                'incident_severity' => '',
                'date_incident' => '',
                'incident_category' => '',
                'incident_sub_category' => '',
                'incident_loss' => '',
                'incident_summary' => '',
                'detail' => '',
                'time_incident' => ''
            ]);
        }
    }

    public function hsseincidentprocess()
    {
        // Use AJAX layout
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false; // prevent auto-rendering a view

        // Get form data
        $data = $this->request->getData() ?? [];

        if (empty($data)) {
            echo 'fail';
            return;
        }
        //dd($this->HsseIncident->newEntity());
        // Check if updating or adding
        if (!empty($data['id']) && $data['id'] != 0) {
            $incident = $this->HsseIncident->get($data['id']);
            $res = 'update';
        } else {
            $incident = $this->HsseIncident->newEmptyEntity();
            $res = 'add';
        }
        
        // Prepare incident data
        $incidentData = [
            'incident_time'       => $data['incident_time'] ?? null,
            'incident_no'         => $data['incident_no'] ?? null,
            'incident_summary'    => $data['incident_summary'] ?? null,
            'detail'              => $data['detail'] ?? null,
            'report_id'           => $data['report_id'] ?? null,
            'incident_loss'       => $data['incident_loss'] ?? null,
            'incident_category'   => $data['incident_category'] ?? 0,
            'incident_sub_category' => $data['incident_sub_category'] ?? 0,
            'incident_severity'   => $data['incident_severity'] ?? 0,
        ];

        // Format date from MM-DD-YYYY to YYYY-MM-DD
        if (!empty($data['date_incident'])) {
            $parts = explode('-', $data['date_incident']);
            if (count($parts) === 3) {
                $incidentData['date_incident'] = sprintf(
                    '%04d-%02d-%02d',
                    $parts[2], // Year
                    $parts[0], // Month
                    $parts[1]  // Day
                );
            }
        } else {
            $incidentData['date_incident'] = null;
        }

        // Patch entity
        $incident = $this->HsseIncident->patchEntity($incident, $incidentData);

        // Save
        if ($this->HsseIncident->save($incident)) {
            echo $res;
        } else {
            echo 'fail';
        }

        return;
    }

    public function displaycontentforloss()
    {
        $this->request->allowMethod(['post']); // ensure only POST
        $this->viewBuilder()->setLayout('ajax');

        $data = $this->request->getData();
        $incidentCategoryDetail = [];
        $incidentSubCategoryDetail = [];
        $type = null;

        if (!empty($data)) {
            $type = $data['type'] ?? null;
            $id = isset($data['id']) ? (int)$data['id'] : null; // cast to integer

            switch ($type) {
                case 'incident_loss':
                    $incidentCategoryDetail = $this->IncidentCategory->find()
                        ->where(['loss_id' => $id])
                        ->toArray();
                    break;

                case 'incident_category':
                    $incidentSubCategoryDetail = $this->IncidentSubCategory->find()
                        ->where(['loss_category_id' => $id])
                        ->toArray();
                    break;
            }
        }

        $this->set(compact('incidentCategoryDetail', 'incidentSubCategoryDetail', 'type'));
        $this->set('_serialize', ['incidentCategoryDetail', 'incidentSubCategoryDetail', 'type']);
    }

    public function addHsseAttachment($report_id = null, $attachment_id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $reportIdDecoded = base64_decode($report_id);

        // Get client details
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $reportIdDecoded])
            ->all();

        // Get all reports
        $reportList = $this->Reports->find()
            ->where(['Reports.isdeleted' => 'N'])
            ->all();
        $this->set('reportList', $reportList);

        // Set client feedback
        if (!$clientdetail->isEmpty()) {
            $clientReviewed = $clientdetail->first()->clientreviewed ?? 0;
            $this->set('client_feedback', ($clientReviewed == 3) ? 1 : 0);
        } else {
            $this->set('client_feedback', 0);
        }

        // Attachment edit mode
        if (!empty($attachment_id)) {
            $attachmentDetail = $this->HsseAttachment->find()
                ->where(['HsseAttachment.id' => base64_decode($attachment_id)])
                ->first();

            if ($attachmentDetail) {
                $imagepath = $this->file_edit_list(
                    $attachmentDetail->file_name,
                    'HsseAttachment',
                    $attachmentDetail
                );

                $this->set(compact(
                    'imagepath'
                ));
                $this->set([
                    'heading' => 'Update File',
                    'attachment_id' => $attachmentDetail->id,
                    'description' => $attachmentDetail->description,
                    'button' => 'Update',
                    'imagename' => $attachmentDetail->file_name,
                    'attachmentstyle' => 'style="display:block;"'
                ]);
            }
        } else {
            // Add mode
            $this->set([
                'ridHolder' => [],
                'heading' => 'Add attachment',
                'attachment_id' => 0,
                'description' => '',
                'button' => 'Add',
                'imagepath' => '',
                'imagename' => '',
                'attachmentstyle' => 'style="display:none;"',
                'edit_id' => 0,
                'id_holder' => 0
            ]);
        }

        // Set report info
        $this->set('report_id', $reportIdDecoded);

        $reportDetail = $this->Reports->find()
            ->where(['Reports.id' => $reportIdDecoded])
            ->first();

        if ($reportDetail) {
            $this->set('report_number', $reportDetail->report_no);
        }
    }

    public function uploadimage($upparname = null, $deleteImageName = null): void
    {
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $allowed_image = $this->image_upload_type();
        $this->upload_image_func($upparname, 'Reports', $allowed_image);
    }

    public function hsseattachmentprocess(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        
        if ($data['Reports']['id'] != 0) {
            $res = 'update';
            $attachment = $this->HsseAttachment->get($data['Reports']['id']);
        } else {
            $res = 'add';
            $attachment = $this->HsseAttachment->newEntity();
        }
        
        $attachmentData = [
            'description' => $data['attachment_description'] ?? '',
            'file_name' => $data['hiddenFile'],
            'report_id' => $data['report_id']
        ];
        
        $attachment = $this->HsseAttachment->patchEntity($attachment, $attachmentData);
        
        if ($this->HsseAttachment->save($attachment)) {
            echo $res;
        } else {
            echo 'fail';
        }
    }

    public function reportHsseAttachmentList($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();

        // Use modern layout assignment
        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $session = $this->request->getSession();
        $decodedId = base64_decode($id);

        // Fetch report details
        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid Report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Fetch client details
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $reportdetail->id])
            ->all();
        
        // Handle client feedback flag
        $clientFeedback = 0;
        if (!$clientdetail->isEmpty()) {
            $client = $clientdetail->first();
            $clientFeedback = ($client->clientreviewed == 3) ? 1 : 0;
        }
        $this->set('client_feedback', $clientFeedback);

        // Set view variables
        $this->set('report_number', $reportdetail->report_no);
        $this->set('id', $decodedId);

        // Handle form data
        $requestData = $this->request->getData();

        if (!empty($requestData)) {
            $action = $requestData['HsseAttachment']['action'] ?? 'all';
            $limit  = $requestData['HsseAttachment']['limit'] ?? 50;
        } else {
            $action = 'all';
            $limit  = 50;
        }

        $this->set(compact('action', 'limit'));

        // Save in session
        $session->write('action', $action);
        $session->write('limit', $limit);

        // Clear any old filters
        $session->delete('filter');
        $session->delete('value');
    }

    public function get_all_attachment_list($report_id)
    {
        Configure::write("debug", "2");
        $this->layout = "ajax";
        $this->_checkAdminSession();
        $condition = "";

        $condition = "HsseAttachment.report_id = $report_id  AND  HsseAttachment.isdeleted = 'N'";

        if (isset($_REQUEST["filter"])) {
            $condition .=
                "AND ucase(HsseAttachment." .
                $_REQUEST["filter"] .
                ") like '" .
                trim($_REQUEST["value"]) .
                "%'";
        }
        $limit = null;
        if ($_REQUEST["limit"] == "all") {
            //$condition .= " order by Category.id DESC";
        } else {
            $limit = $_REQUEST["start"] . ", " . $_REQUEST["limit"];
        }

        $adminArray = [];
        $count = $this->HsseAttachment->find("count", [
            "conditions" => $condition,
        ]);
        $adminA = $this->HsseAttachment->find("all", [
            "conditions" => $condition,
            "order" => "HsseAttachment.id DESC",
            "limit" => $limit,
        ]);

        $i = 0;
        foreach ($adminA as $rec) {
            if ($rec["HsseAttachment"]["isblocked"] == "N") {
                $adminA[$i]["HsseAttachment"]["blockHideIndex"] = "true";
                $adminA[$i]["HsseAttachment"]["unblockHideIndex"] = "false";
                $adminA[$i]["HsseAttachment"]["isdeletdHideIndex"] = "true";
            } else {
                $adminA[$i]["HsseAttachment"]["blockHideIndex"] = "false";
                $adminA[$i]["HsseAttachment"]["unblockHideIndex"] = "true";
                $adminA[$i]["HsseAttachment"]["isdeletdHideIndex"] = "false";
            }

            //$this-->attachment_list(HsseAttachment,$rec);
            $adminA[$i]["HsseAttachment"]["image_src"] = $this->attachment_list(
                "HsseAttachment",
                $rec
            );

            $i++;
        }

        if (count($adminA) > 0) {
            $adminArray = Set::extract($adminA, "{n}.HsseAttachment");
        } else {
            $adminArray = [];
        }
        $this->set("total", $count); //send total to the view
        $this->set("admins", $adminArray); //send products to the view
        //$this->set('status', $action);
    }

    public function attachmentBlock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $attachmentId) {
            $attachment = $this->HsseAttachment->get($attachmentId);
            $attachment->isblocked = 'Y';
            $this->HsseAttachment->save($attachment);
        }
        
        echo 'ok';
    }

    public function attachmentUnblock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $attachmentId) {
            $attachment = $this->HsseAttachment->get($attachmentId);
            $attachment->isblocked = 'N';
            $this->HsseAttachment->save($attachment);
        }
        
        echo 'ok';
    }

    public function attachmentDelete(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        $idArray = explode('^', $data['id']);
        
        foreach ($idArray as $id) {
            $attachment = $this->HsseAttachment->get($id);
            $attachment->isdeleted = 'Y';
            $this->HsseAttachment->save($attachment);
        }
        
        echo 'ok';
    }

    public function addHsseRemidial($report_id = null, $remidial_id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');

        // Load Priority and AdminMaster tables
        $priorityTable = $this->getTableLocator()->get('Priority');
        $adminTable = $this->getTableLocator()->get('AdminMasters');
        $hsseRemidialTable = $this->getTableLocator()->get('HsseRemidial');
        $reportTable = $this->getTableLocator()->get('Reports');
        $clientTable = $this->getTableLocator()->get('HsseClient');

        $priority = $priorityTable->find()->all();
        $userDetail = $adminTable->find()->all();
        $this->set(compact('priority', 'userDetail'));
        $this->set('created_by',$_SESSION['adminData']['first_name'] . ' ' . $_SESSION['adminData']['last_name']);

        // Fetch report details
        $reportDetail = $reportTable->find()
            ->where(['Reports.id' => base64_decode($report_id)])
            ->first();

        $clientDetail = $clientTable->find()
            ->where(['HsseClient.report_id' => base64_decode($report_id)])
            ->first();

        // Client feedback logic
        if ($clientDetail) {
            $this->set('client_feedback', $clientDetail->clientreviewed == 3 ? 1 : 0);
        } else {
            $this->set('client_feedback', 0);
        }

        $this->set('report_number', $reportDetail->report_no);
        $this->set('reportno', base64_decode($report_id));

        // Count existing remedial actions
        $countRem = $hsseRemidialTable->find()
            ->where(['HsseRemidial.report_no' => base64_decode($report_id)])
            ->count();

        if (empty($remidial_id)) {
            $this->set('id', 0);
            $countRem = $countRem ? $countRem + 1 : 1;
            $this->set(compact('countRem'));

            $this->set([
                'heading' => 'Add Remedial Action Item',
                'button' => 'Submit',
                'remidial_create' => '',
                'remidial_summery' => '',
                'remidial_closer_summary' => '',
                'remidial_action' => '',
                'remidial_priority' => '',
                'remidial_closure_target' => '',
                'remidial_responsibility' => '',
                'remidial_reminder_data' => '',
                'remidial_closure_date' => '',
                'remidial_style' => 'style="display:none"',
                'remidial_button_style' => 'style="display:block"',
            ]);
        } else 
        {
            // Fetch existing remedial data
            $remidialData = $hsseRemidialTable->find()
                ->where(['HsseRemidial.id' => base64_decode($remidial_id)])
                ->first();

            $this->set('countRem', $remidialData->remedial_no);
            $this->set('id', base64_decode($remidial_id));

            if (!empty($remidialData->remidial_create)) {
                $parts = explode('-', (string)$remidialData->remidial_create);
                if (count($parts) >= 3) {
                    $remidialCreate = implode('-', [$parts[1], $parts[2], $parts[0]]);
                } else {
                    $remidialCreate = '';
                }
            } else {
                $remidialCreate = '';
            }
            $this->set([
                'heading' => 'Edit Remedial Action Item',
                'button' => 'Update',
                'remidial_create' => $remidialCreate,
                'remidial_summery' => $remidialData->remidial_summery,
                'remidial_action' => $remidialData->remidial_action,
                'remidial_priority' => $remidialData->remidial_priority,
                'remidial_closure_target' => $remidialData->remidial_closure_target,
                'remidial_responsibility' => $remidialData->remidial_responsibility,
                'remidial_reminder_data' => $remidialData->remidial_reminder_data,
            ]);

            if (!empty($remidialData->remidial_closure_date) && $remidialData->remidial_closure_date != '0000-00-00') {
                $formattedClosureDate = date('m/d/Y', strtotime((string)$remidialData->remidial_closure_date));
                $this->set('remidial_closure_date', $formattedClosureDate);
                $this->set('remidial_closer_summary', $remidialData->remidial_closer_summary);
                $this->set('remidial_style', 'style="display:block"');
                $this->set('remidial_button_style', 'style="display:none"');
            } else {
                $this->set('remidial_closure_date', '');
                $this->set('remidial_closer_summary', '');
                $this->set('remidial_style', 'style="display:block"');
                $this->set('remidial_button_style', 'style="display:block"');
            }
        }
    }

    public function datecalculate()
    {
        $this->request->allowMethod(['post', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');

        // Load model
        $this->Priority = $this->fetchTable('Priority');

        $remidial_priority = $this->request->getData('remidial_priority');
        $remidial_create   = $this->request->getData('remidial_create');

        // Validate input
        if (empty($remidial_priority) || empty($remidial_create)) {
            throw new BadRequestException('Missing required parameters.');
        }

        // Fetch priority record
        $rempriority = $this->Priority
            ->find()
            ->where(['id' => $remidial_priority])
            ->first();

        if (!$rempriority) {
            throw new BadRequestException('Invalid priority.');
        }

        // Parse date safely
        try {
            $date = \DateTime::createFromFormat('d-m-Y', $remidial_create);
            if (!$date) {
                throw new \Exception('Invalid date format.');
            }
        } catch (\Exception $e) {
            return $this->response->withStringBody('Invalid date input');
        }

        // Handle Priority Type Logic
        if ((int)$rempriority->id === 5) {
            $formatted = $date->format('d/m/Y H:i:s');
        } else {
            switch (strtolower($rempriority->time_type)) {
                case 'days':
                    $date->modify('+' . (int)$rempriority->time . ' days');
                    break;

                case 'hrs':
                case 'hours':
                    $date->modify('+' . (int)$rempriority->time . ' hours');
                    break;

                default:
                    // No modification
                    break;
            }
            $formatted = $date->format('d/m/Y H:i:s');
        }

        // Return as clean JSON response
        $result = [
            'status' => 'success',
            'calculated_date' => $formatted
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($result));
    }

    public function remidialprocess()
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->_checkAdminSession();

        $data = $this->request->getData(); // CakePHP 3.8 way
        $remidialArray = [];

        // Check if updating or adding
        if (!empty($data['add_report_remidial_form']['id'])) {
            $res = 'update';
            $remidialEntity = $this->HsseRemidial->get($data['add_report_remidial_form']['id']);
        } else {
            $res = 'add';
            $remidialEntity = $this->HsseRemidial->newEmptyEntity();
        }

        // Prepare dates
        $remidialCreateParts = explode('-', $data['remidial_create']);
        $remidialCreateFormatted = $remidialCreateParts[2] . '-' . $remidialCreateParts[0] . '-' . $remidialCreateParts[1];

        $session = $this->getRequest()->getSession();
        $adminData = $session->read('adminData');
        $adminId = $adminData['AdminMasters']['id'] ?? null;

        $remidialEntity = $this->HsseRemidial->patchEntity($remidialEntity, [
            'remidial_create' => $remidialCreateFormatted,
            'remidial_createby' => $adminId,
            'report_no' => $data['report_no'],
            'remedial_no' => $data['countRem'],
            'remidial_priority' => explode('~', $data['remidial_priority'])[0],
            'remidial_responsibility' => $data['responsibility'],
            'remidial_summery' => $data['add_report_remidial_form']['remidial_summery'] ?? '',
            'remidial_closer_summary' => $data['add_report_remidial_form']['remidial_closer_summary'] ?? '',
            'remidial_action' => $data['add_report_remidial_form']['remidial_action'] ?? '',
            'remidial_reminder_data' => $data['add_report_remidial_form']['remidial_reminder_data'] ?? '',
            'remidial_closure_target' => $data['add_report_remidial_form']['remidial_closure_target'] ?? '',
            'remidial_closure_date' => !empty($data['remidial_closure_date'])
                ? $data['remidial_closure_date']
                : null
        ]);
        
        // Calculate reminder & email dates
        $createON = ($remidialEntity->remidial_create ?? date('Y-m-d')) . ' 00:00:00';
        $strCreateOn = strtotime($createON);

        // Initialize defaults
        $reminderON = date('Y-m-d H:i:s');
        $explodeCTD = [];
        $remdate = date('Y-m-d');

        // Safely extract closure target
        $target = trim((string)($remidialEntity->remidial_closure_target ?? ''));

        if (!empty($target)) {
            $explodeCTR = explode(' ', $target);
            $datePart = $explodeCTR[0] ?? '';
            $timePart = $explodeCTR[1] ?? '00:00:00';

            $explodeCTD = explode('/', $datePart);

            if (count($explodeCTD) === 3) {
                // Convert dd/mm/yyyy to yyyy-mm-dd format
                $reminderON = sprintf('%s-%s-%s %s', $explodeCTD[1], $explodeCTD[0], $explodeCTD[2], $timePart);
                $remdate = sprintf('%s-%s-%s', $explodeCTD[2], $explodeCTD[1], $explodeCTD[0]);
            }
        }

        $strReminderOn = strtotime($reminderON);

        // Initialize holder arrays
        $dateHolder = [$remdate];
        $dateIndex = [3, 7, 30];

        // Only run loop if valid date parts exist
        if (count($explodeCTD) === 3) {
            $day = (int)$explodeCTD[0];
            $month = (int)$explodeCTD[1];
            $year = (int)$explodeCTD[2];

            foreach ($dateIndex as $idx) {
                $emaildateBefore = date('Y-m-d', mktime(0, 0, 0, $month, $day - $idx, $year));
                if ($strCreateOn < strtotime($emaildateBefore)) {
                    $dateHolder[] = $emaildateBefore;
                }

                $emaildateAfter = date('Y-m-d', mktime(0, 0, 0, $month, $day + $idx, $year));
                if ($strCreateOn < strtotime($emaildateAfter)) {
                    $dateHolder[] = $emaildateAfter;
                }
            }
        }

        // ✅ Delete existing email reminders safely
        $connection = $this->RemidialEmailList->getConnection();
        $connection->execute(
            'DELETE FROM remidial_email_lists 
            WHERE remedial_no = :remedial_no 
            AND report_id = :report_id 
            AND report_type = :report_type',
            [
                'remedial_no' => $remidialEntity->remedial_no ?? '',
                'report_id' => $remidialEntity->report_no ?? '',
                'report_type' => 'hsse'
            ]
        );

        // Insert new email reminders
        if (empty($data['remidial_closure_date'])) {
            foreach ($dateHolder as $d) {
                $userDetail = $this->AdminMaster->find()->where(['id' => $data['responsibility']])->first();
                if ($userDetail) {
                    $remidialEmailEntity = $this->RemidialEmailList->newEntity();
                    $remidialEmailEntity = $this->RemidialEmailList->patchEntity($remidialEmailEntity, [
                        'report_id' => $remidialEntity->report_no,
                        'remedial_no' => $remidialEntity->remedial_no,
                        'report_type' => 'hsse',
                        'email' => $userDetail->admin_email,
                        'status' => 'N',
                        'email_date' => $d,
                        'send_to' => $userDetail->id
                    ]);
                    $this->RemidialEmailList->save($remidialEmailEntity);
                }
            }
        }

        // Save remedial entity
        if ($this->HsseRemidial->save($remidialEntity)) {
            echo $res . "~hsse";
        } else {
            echo 'fail';
        }

        exit();
    }

    public function remidialEmailView($id, $remedial_no, $report_id): void
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('ajax');
        
        $remidialData = $this->HsseRemidial->find()
            ->where([
                'HsseRemidial.report_no' => $report_id,
                'HsseRemidial.remedial_no' => $remedial_no
            ])
            ->first(); 
        $reportData = $this->Reports->get($remidialData->report_no);
        $userData = $this->AdminMasters->get($remidialData->remidial_responsibility);
        $fullname=$userData->first_name . ' ' . $userData->last_name;
        $repotId= $reportData->report_no;
        $this->set('fullname', $fullname);
        $this->set('report_no', $repotId);
        $this->set('remidialData', $remidialData);
    }

    public function reportHsseRemidialList($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();

        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $decodedId = base64_decode($id);

        $this->set('report_no', $decodedId);
        $this->set('id', $decodedId);

        // Load models explicitly (if not loaded in initialize)
        $this->Reports = $this->fetchTable('Reports');
        $this->HsseClient = $this->fetchTable('HsseClient');

        // Fetch report detail
        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Fetch client detail
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $decodedId])
            ->first();

        // Set client feedback flag
        $clientFeedback = ($clientdetail && $clientdetail->clientreviewed == 3) ? 1 : 0;
        $this->set('client_feedback', $clientFeedback);

        // Set report variables for the view
        $this->set('report_val', $id);
        $this->set('report_number', $reportdetail->report_no);

        // Handle form action and limit
        $data = $this->request->getData();
        if (!empty($data)) {
            $action = $data['HsseRemidial']['action'] ?? 'all';
            $limit = $data['HsseRemidial']['limit'] ?? 50;
        } else {
            $action = 'all';
            $limit = 50;
        }

        $this->set('action', $action);
        $this->set('limit', $limit);

        // Use CakePHP 3 session handling
        $session = $this->request->getSession();
        $session->write('action', $action);
        $session->write('limit', $limit);

        // Delete old filter session keys
        $session->delete('filter');
        $session->delete('value');
    }

    public function get_all_remidial_list($report_id)
    {
        return $this->getAllRemidialList($report_id);
    }

    public function getAllRemidialList($reportId)
    {
        // Ajax layout
        $this->viewBuilder()->setLayout('ajax');
        $this->request->allowMethod(['get', 'post']);

        $this->_checkAdminSession(); // keep your session check

        // Input (support POST or GET)
        $filter = $this->request->getData('filter') ?? $this->request->getQuery('filter');
        $value  = $this->request->getData('value')  ?? $this->request->getQuery('value');
        $start  = $this->request->getData('start')  ?? $this->request->getQuery('start');
        $limit  = $this->request->getData('limit')  ?? $this->request->getQuery('limit');

        // Base conditions
        $conditions = [
            'HsseRemidial.report_no' => $reportId,
            'HsseRemidial.isdeleted'  => 'N'
        ];

        // Apply filters
        if (!empty($filter) && $value !== null) {
            switch ($filter) {
                case 'create_on':
                    // Expecting d/M/yy or d/M/yyyy like "01/Jan/23" per original code
                    $parts = explode('/', $value);
                    if (count($parts) === 3) {
                        $day   = $parts[0];
                        $month = date('m', strtotime($parts[1]));
                        $year  = strlen($parts[2]) === 2 ? '20' . $parts[2] : $parts[2];
                        $createOn = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);
                        $conditions['HsseRemidial.remidial_create'] = $createOn;
                    }
                    break;

                case 'remidial_create_name':
                    // Find admin by (first last) or single name fallback
                    $names = preg_split('/\s+/', trim($value));
                    $first = $names[0] ?? '';
                    $last  = count($names) > 1 ? end($names) : '';

                    $adminQuery = $this->fetchTable('AdminMasters')->find();

                    if ($last !== '') {
                        $adminQuery->where([
                            'first_name LIKE' => "%{$first}%",
                            'last_name LIKE'  => "%{$last}%"
                        ]);
                    } else {
                        // single name: search either first OR last
                        $adminQuery->where([
                            'OR' => [
                                'first_name LIKE' => "%{$first}%",
                                'last_name LIKE'  => "%{$first}%"
                            ]
                        ]);
                    }

                    $admin = $adminQuery->first();
                    if ($admin) {
                        $conditions['HsseRemidial.remidial_createby'] = $admin->id;
                    } else {
                        // If no admin found, add impossible condition so query returns none
                        $conditions['HsseRemidial.remidial_createby'] = -1;
                    }
                    break;

                case 'responsibility_person':
                    // same approach but map to remidial_responsibility
                    $names = preg_split('/\s+/', trim($value));
                    $first = $names[0] ?? '';
                    $last  = count($names) > 1 ? end($names) : '';

                    $adminQuery = $this->fetchTable('AdminMasters')->find();
                    if ($last !== '') {
                        $adminQuery->where([
                            'first_name LIKE' => "%{$first}%",
                            'last_name LIKE'  => "%{$last}%"
                        ]);
                    } else {
                        $adminQuery->where([
                            'OR' => [
                                'first_name LIKE' => "%{$first}%",
                                'last_name LIKE'  => "%{$first}%"
                            ]
                        ]);
                    }
                    $admin = $adminQuery->first();
                    if ($admin) {
                        $conditions['HsseRemidial.remidial_responsibility'] = $admin->id;
                    } else {
                        $conditions['HsseRemidial.remidial_responsibility'] = -1;
                    }
                    break;

                case 'remidial_priority_name':
                    $priority = $this->fetchTable('Priority')
                        ->find()
                        ->where(['type' => trim($value)])
                        ->first();
                    if ($priority) {
                        $conditions['HsseRemidial.remidial_priority'] = $priority->id;
                    } else {
                        $conditions['HsseRemidial.remidial_priority'] = -1;
                    }
                    break;
            }
        }

        // Count query (without limit/offset)
        $table = $this->fetchTable('HsseRemidial');
        $count = $table->find()
            ->where($conditions)
            ->count();

        // Prepare main query with contain and ordering
        $query = $table->find()
            ->contain(['CreatedByAdmin', 'ResponsibleAdmin', 'Priority'])
            ->where($conditions)
            ->order(['HsseRemidial.id' => 'DESC']);

        // Pagination if requested
        if ($limit !== 'all' && is_numeric($start) && is_numeric($limit)) {
            $query->limit((int)$limit)->offset((int)$start);
        }

        $rows = $query->all()->toArray();

        // Helper to format date strings or Date objects as d/M/y
        $fmt = function ($val) {
            if (empty($val) || $val === '0000-00-00') {
                return '';
            }
            if (is_object($val) && method_exists($val, 'format')) {
                return $val->format('d/M/y');
            }
            // assume Y-m-d or Y-m-d H:i:s
            $dt = \DateTime::createFromFormat('Y-m-d', substr($val,0,10));
            if ($dt) {
                return $dt->format('d/M/y');
            }
            // fallback
            return $val;
        };

        $admins = [];
        foreach ($rows as $rec) {
            // convert entity -> array for output (you can adjust to keep entities)
            $r = $rec->toArray();

            // block/unblock flags (original used strings, keep booleans or strings as you prefer)
            if (($rec->isblocked ?? 'N') === 'N') {
                $r['blockHideIndex'] = 'true';
                $r['unblockHideIndex'] = 'false';
                $r['isdeletdHideIndex'] = 'true';
            } else {
                $r['blockHideIndex'] = 'false';
                $r['unblockHideIndex'] = 'true';
                $r['isdeletdHideIndex'] = 'false';
            }

            // dates
            $r['create_on']      = $fmt($rec->remidial_create ?? null);
            $r['lastupdate']     = $fmt($rec->modified ?? null);
            $r['createRemidial'] = $fmt($rec->remidial_create ?? null);

            // priority html (be mindful of HTML-in-data; original used <font>)
            $priorityType = $rec->priority->type ?? '';
            $priorityColor = $rec->priority->colorcoder ?? '';
            $r['remidial_priority_name'] = '<font color="' . h($priorityColor) . '">' . h($priorityType) . '</font>';

            // creator name
            if (!empty($rec->admin_master)) {
                $r['remidial_create_name'] = trim(($rec->admin_master->first_name ?? '') . ' ' . ($rec->admin_master->last_name ?? ''));
            } else {
                $r['remidial_create_name'] = '';
            }

            // responsibility person (attempt to lookup if only id present)
            $respName = '';
            if (!empty($rec->remidial_responsibility)) {
                // if contain didn't bring the admin_master for responsibility, fetch
                $resp = $this->fetchTable('AdminMasters')->find()
                    ->select(['first_name', 'last_name'])
                    ->where(['id' => $rec->remidial_responsibility])
                    ->first();
                if ($resp) {
                    $respName = trim($resp->first_name . ' ' . $resp->last_name);
                }
            }
            $r['responsibility_person'] = $respName;

            // closure date
            $r['closure'] = $fmt($rec->remidial_closure_date ?? null);

            $admins[] = $r;
        }

        // If you prefer original behavior where adminArray is empty when count == 0:
        if ($count === 0) {
            $admins = [];
        }

        // Set variables used by your view (keeps same names as original)
        $this->set('total', $count);
        $this->set('admins', $admins);
    }


    public function remidialBlock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $remidialId) {
            $remidial = $this->HsseRemidial->get($remidialId);
            $remidial->isblocked = 'Y';
            $this->HsseRemidial->save($remidial);
        }
        
        echo 'ok';
    }

    public function remidialUnblock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $remidialId) {
            $remidial = $this->HsseRemidial->get($remidialId);
            $remidial->isblocked = 'N';
            $this->HsseRemidial->save($remidial);
        }
        
        echo 'ok';
    }

    public function remidialDelete(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        $idArray = explode('^', $data['id']);
        $connection = $this->HsseRemidial->getConnection();
        
        foreach ($idArray as $id) {
            $remidialData = $this->HsseRemidial->get($id);
            
            $connection->transactional(function() use ($connection, $remidialData, $id) {
                $connection->execute(
                    'DELETE FROM remidial_email_lists WHERE report_id = ? AND remedial_no = ? AND report_type = ?',
                    [$remidialData->report_no, $remidialData->remedial_no, 'hsse']
                );
                $connection->execute('DELETE FROM hsse_remidials WHERE id = ?', [$id]);
            });
        }
        
        echo 'ok';
    }

    public function hsseRemedilaEmailList($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();

        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $decodedId = base64_decode($id);
        $this->set('report_no', $decodedId);
        $this->set('id', $decodedId);

        // Fetch report detail
        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Fetch client detail
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $decodedId])
            ->first();

        // Set client feedback
        $clientFeedback = ($clientdetail && $clientdetail->clientreviewed == 3) ? 1 : 0;
        $this->set('client_feedback', $clientFeedback);

        $this->set('report_val', $id);
        $this->set('report_number', $reportdetail->report_no);

        // Handle form action and limit
        $data = $this->request->getData();
        if (!empty($data)) {
            $action = $data['RemidialEmailList']['action'] ?? 'all';
            $limit = $data['RemidialEmailList']['limit'] ?? 50;
        } else {
            $action = 'all';
            $limit = 50;
        }

        $this->set('action', $action);
        $this->set('limit', $limit);

        // Use CakePHP 3 session handling
        $session = $this->request->getSession();
        $session->write('action', $action);
        $session->write('limit', $limit);

        // Remove old filter session keys
        $session->delete('filter');
        $session->delete('value');
    }

    /*public function get_all_remidial_email_list($report_id)
    {
        Configure::write("debug", "2"); //set debug to 0 for this function because debugging info breaks the XMLHttpRequest
        $this->layout = "ajax"; //this tells the controller to use the Ajax layout instead of the default layout (since we're using ajax . . .)
        //pr($_REQUEST);
        $this->_checkAdminSession();
        $condition = "";

        $condition = "RemidialEmailList.report_id = $report_id AND report_type='hsse'";

        if (isset($_REQUEST["filter"])) {
            //echo '<pre>';
            //print_r($_REQUEST);

            switch ($this->data["filter"]) {
                case "email_date":
                    $explodemonth = explode("/", $this->data["value"]);
                    $day = $explodemonth[0];
                    $month = date("m", strtotime($explodemonth[1]));
                    $year = "20$explodemonth[2]";
                    $createon = $year . "-" . $month . "-" . $day;
                    $condition .=
                        " AND RemidialEmailList.email_date ='" .
                        $createon .
                        "'";
                    break;
                case "email":
                    $condition .=
                        " AND RemidialEmailList.email like '%" .
                        $_REQUEST["value"] .
                        "%'";

                    break;
                case "responsibility_person":
                    $spliNAME = explode(" ", $_REQUEST["value"]);
                    $spliLname = $spliNAME[count($spliNAME) - 1];
                    $spliFname = $spliNAME[0];
                    $adminCondition =
                        "AdminMaster.first_name like '%" .
                        $spliFname .
                        "%' AND AdminMaster.last_name like '%" .
                        $spliLname .
                        "%'";
                    $userDetail = $this->AdminMaster->find("all", [
                        "conditions" => $adminCondition,
                    ]);
                    $addimid = $userDetail[0]["AdminMaster"]["id"];
                    $condition .=
                        " AND RemidialEmailList.send_to ='" . $addimid . "'";
                    break;
            }
        }

        $limit = null;
        if ($_REQUEST["limit"] == "all") {
            //$condition .= " order by Category.id DESC";
        } else {
            $limit = $_REQUEST["start"] . ", " . $_REQUEST["limit"];
        }

        //$count = $this->HsseIncident->find('count' ,array('conditions' => $condition));
        $adminArray = [];
        $count = $this->RemidialEmailList->find("count", [
            "conditions" => $condition,
        ]);
        $adminA = $this->RemidialEmailList->find("all", [
            "conditions" => $condition,
            "order" => "RemidialEmailList.id DESC",
            "limit" => $limit,
        ]);

        $i = 0;
        foreach ($adminA as $rec) {
            if ($rec["RemidialEmailList"]["status"] == "N") {
                $adminA[$i]["RemidialEmailList"]["status_value"] = "Not Send";
            } else {
                $adminA[$i]["RemidialEmailList"]["status_value"] = "Sent";
            }

            $remC = $this->HsseRemidial->find("all", [
                "conditions" => [
                    "HsseRemidial.report_no" =>
                        $rec["RemidialEmailList"]["report_id"],
                    "HsseRemidial.remedial_no" =>
                        $rec["RemidialEmailList"]["remedial_no"],
                ],
            ]);

            $rrd = explode(
                " ",
                $remC[0]["HsseRemidial"]["remidial_reminder_data"]
            );
            $rrdE = explode("/", $rrd[0]);
            $adminA[$i]["RemidialEmailList"]["reminder_data"] = date(
                "d/M/y",
                mktime(0, 0, 0, $rrdE[1], $rrdE[0], $rrdE[2])
            );

            $create_on = explode(
                "-",
                $remC[0]["HsseRemidial"]["remidial_create"]
            );
            $adminA[$i]["RemidialEmailList"]["create_on"] = date(
                "d/M/y",
                mktime(0, 0, 0, $create_on[1], $create_on[2], $create_on[0])
            );
            $user_detail_reponsibilty = $this->AdminMaster->find("all", [
                "conditions" => [
                    "AdminMaster.id" => $rec["RemidialEmailList"]["send_to"],
                ],
            ]);
            $adminA[$i]["RemidialEmailList"]["responsibility_person"] =
                $user_detail_reponsibilty[0]["AdminMaster"]["first_name"] .
                "  " .
                $user_detail_reponsibilty[0]["AdminMaster"]["last_name"];
            $explodemail = explode(
                "-",
                $rec["RemidialEmailList"]["email_date"]
            );
            $adminA[$i]["RemidialEmailList"]["email_date"] = date(
                "d/M/y",
                mktime(
                    0,
                    0,
                    0,
                    $explodemail[1],
                    $explodemail[2],
                    $explodemail[0]
                )
            );

            $i++;
        }

        if ($count == 0) {
            $adminArray = [];
        } else {
            $adminArray = Set::extract($adminA, "{n}.RemidialEmailList");
        }

        $this->set("total", $count); //send total to the view
        $this->set("admins", $adminArray); //send products to the view
        //$this->set('status', $action);
    }*/
    public function getAllRemidialEmailList($reportId = null)
    {
        $this->request->allowMethod(['get', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');

        $this->_checkAdminSession();

        $this->HsseRemidial = $this->fetchTable('HsseRemidial'); 
        $this->RemidialEmailList = $this->fetchTable('RemidialEmailList');
        $this->AdminMasters = $this->fetchTable('AdminMasters');

        $condition = [
            'RemidialEmailList.report_id' => $reportId,
            'RemidialEmailList.report_type' => 'hsse'
        ];

        $filter = $this->request->getQuery('filter');
        $value = $this->request->getQuery('value');

        if ($filter && $value) {
            switch ($filter) {
                case 'email_date':
                    $exploded = explode('/', $value);
                    if (count($exploded) === 3) {
                        $day = $exploded[0];
                        $month = date('m', strtotime($exploded[1]));
                        $year = '20' . $exploded[2];
                        $createOn = "$year-$month-$day";
                        $condition['RemidialEmailList.email_date'] = $createOn;
                    }
                    break;

                case 'email':
                    $condition['RemidialEmailList.email LIKE'] = "%$value%";
                    break;

                case 'responsibility_person':
                    $nameParts = explode(' ', trim($value));
                    $firstName = $nameParts[0] ?? '';
                    $lastName = end($nameParts);

                    $user = $this->AdminMasters->find()
                        ->select(['id'])
                        ->where([
                            'first_name LIKE' => "%$firstName%",
                            'last_name LIKE' => "%$lastName%"
                        ])
                        ->first();

                    if ($user) {
                        $condition['RemidialEmailList.send_to'] = $user->id;
                    }
                    break;
            }
        }

        // Pagination
        $limit = $this->request->getQuery('limit');
        $start = $this->request->getQuery('start');
        $query = $this->RemidialEmailList->find()
            ->where($condition)
            ->order(['RemidialEmailList.id' => 'DESC']);

        if ($limit && $limit !== 'all') {
            $query->limit((int)$limit)->offset((int)$start);
        }

        $records = $query->all();
        $count = $query->count();

        $results = [];
        foreach ($records as $rec) {
            $recArray = $rec->toArray();
            $recArray['status_value'] = $rec->status === 'N' ? 'Not Sent' : 'Sent';

            $remidial = $this->HsseRemidial->find()
                ->where([
                    'report_no' => $rec->report_id,
                    'remedial_no' => $rec->remedial_no
                ])
                ->first();

            if ($remidial) {
                if (!empty($remidial->remidial_reminder_data)) {
                    $recArray['reminder_data'] = $remidial->remidial_reminder_data instanceof \DateTimeInterface
                        ? $remidial->remidial_reminder_data->format('d/M/y')
                        : date('d/M/y', strtotime((string)$remidial->remidial_reminder_data));
                }

                if (!empty($remidial->remidial_create)) {
                    $recArray['create_on'] = $remidial->remidial_create instanceof \DateTimeInterface
                        ? $remidial->remidial_create->format('d/M/y')
                        : date('d/M/y', strtotime((string)$remidial->remidial_create));
                }
            }

            $responsible = $this->AdminMasters->find()
                ->select(['first_name', 'last_name'])
                ->where(['id' => $rec->send_to])
                ->first();

            $recArray['responsibility_person'] = $responsible
                ? $responsible->first_name . ' ' . $responsible->last_name
                : '';

            if (!empty($rec->email_date)) {
                $recArray['email_date'] = $rec->email_date instanceof \DateTimeInterface
                    ? $rec->email_date->format('d/M/y')
                    : date('d/M/y', strtotime((string)$rec->email_date));
            }

            $results[] = $recArray;
        }

        $this->set([
            'total' => $count,
            'admins' => $results,
            '_serialize' => ['total', 'admins']
        ]);
    }


    public function remidialEmailDelete(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        $idArray = explode('^', $data['id']);
        $connection = $this->RemidialEmailList->getConnection();
        
        foreach ($idArray as $id) {
            $connection->execute('DELETE FROM remidial_email_lists WHERE id = ?', [$id]);
        }
        
        echo 'ok';
    }

    public function addHsseInvestigation($report_id, $investigation_id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();

        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $decodedReportId = base64_decode($report_id);
        
        /** -----------------------------
         *  Fetch Data from Related Tables
         *  ----------------------------- */

        // Immediate Causes
        $immediateCauseDetail = $this->ImmediateCauses->find()->all();
        //dd($immediateCauseDetail);
        $this->set('immediateCauseDetail', $immediateCauseDetail);

        // Admin Users
        $user_detail = $this->AdminMasters->find()
            ->contain(['RoleMasters'])
            ->where(['AdminMasters.isdeleted' => 'N', 'AdminMasters.isblocked' => 'N'])
            ->all();
        // dd($user_detail);
        // Build derived seniority and position data
        $userData = [];
        foreach ($user_detail as $u) {
            $modified = $u->modified ? $u->modified->format('Y-m-d H:i:s') : '';
            $seniorityParts = explode(' ', $modified);
            $dateParts = explode('-', $seniorityParts[0] ?? '');
            $seniorityDate = isset($dateParts[2])
                ? $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0]
                : '';

            $u->user_seniority = $seniorityDate;
            $u->position_seniorty = implode('~', [
                trim($u->first_name . ' ' . $u->last_name),
                $u->role_master->role_name ?? '',
                $u->user_seniority,
                $u->id,
                $u->position
            ]);

            $userData[] = $u;
        }
        $this->set('userDetail', $userData);
        //dd($userData);
        /** -----------------------------
         *  Client and Report Details
         *  ----------------------------- */

        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $decodedReportId])
            ->first();

        $this->set('client_feedback', ($clientdetail && $clientdetail->clientreviewed == 3) ? 1 : 0);

        $this->set('report_id', $decodedReportId);

        $lossDetail = $this->Losses->find()->all();
        $this->set('lossDetail', $lossDetail);

        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedReportId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('report_number', $reportdetail->report_no);

        /** -----------------------------
         *  Investigation Details
         *  ----------------------------- */

        $investigationdetail = $this->HsseInvestigation->find()
            ->where(['report_id' => $decodedReportId])
            ->first();

        if ($investigationdetail) {
            $teamIds = explode(',', (string)$investigationdetail->team_user_id);
            $this->set('investigation_team', $teamIds);

            // Fetch each team member’s info
            $investnameHolader = [];
            foreach ($teamIds as $i => $memberId) {
                $teamMember = $this->AdminMasters->find()
                    ->contain(['RoleMasters'])
                    ->where(['AdminMasters.id' => $memberId])
                    ->first();

                if ($teamMember) {
                    $modified = $teamMember->modified ? $teamMember->modified->format('Y-m-d H:i:s') : '';
                    $seniorityParts = explode(' ', $modified);
                    $dateParts = explode('-', $seniorityParts[0] ?? '');
                    $userSeniority = isset($dateParts[2])
                        ? $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0]
                        : '';

                    $investnameHolader[] = [
                        'id' => $teamMember->id,
                        'first_name' => $teamMember->first_name,
                        'last_name' => $teamMember->last_name,
                        'user_seniority' => $userSeniority,
                        'position' => $teamMember->position,
                        'role_name' => $teamMember->role_master->role_name ?? '',
                        'position_seniorty' => implode('~', [
                            trim($teamMember->first_name . ' ' . $teamMember->last_name),
                            $teamMember->role_master->role_name ?? '',
                            $userSeniority,
                            $teamMember->id
                        ])
                    ];
                }
            }

            $this->set('investnameHolader', $investnameHolader);

            // Helper to safely get non-empty string or fallback
            $getOrDefault = function ($value, $default) {
                return !empty($value) ? $value : $default;
            };

            $this->set('epeoplet', $getOrDefault($investigationdetail->people_title, 'Enter People Title'));
            $this->set('epeopled', $getOrDefault($investigationdetail->people_description, 'Enter People Description'));
            $this->set('eposplet', $getOrDefault($investigationdetail->position_title, 'Enter Position Title'));
            $this->set('epospled', $getOrDefault($investigationdetail->position_description, 'Enter Position Description'));
            $this->set('epartsplet', $getOrDefault($investigationdetail->parts_title, 'Enter Parts Title'));
            $this->set('epartspled', $getOrDefault($investigationdetail->parts_description, 'Enter Parts Description'));
            $this->set('epapert', $getOrDefault($investigationdetail->paper_title, 'Enter Paper Title'));
            $this->set('epaperd', $getOrDefault($investigationdetail->paper_descrption, 'Enter Paper Description'));

            $this->set('button', 'Update');
            $this->set('id_holder', $investigationdetail->team_user_id);
        } else {
            // Default values for new investigation
            $this->set('button', 'Add');
            $this->set('id_holder', '');
            $this->set('investigation_team', []);
            $this->set('investnameHolader', []);
            $this->set('epeoplet', 'Enter People Title');
            $this->set('epeopled', 'Enter People Description');
            $this->set('eposplet', 'Enter Position Title');
            $this->set('epospled', 'Enter Position Description');
            $this->set('epartsplet', 'Enter Parts Title');
            $this->set('epartspled', 'Enter Parts Description');
            $this->set('epapert', 'Enter Paper Title');
            $this->set('epaperd', 'Enter Paper Description');
        }
    }

    public function retrivecause(): void
    {
        $this->viewBuilder()->setLayout('ajax');
        
        $data = $this->request->getData();
        $causeList = $this->ImmediateSubCause->find()
            ->where(['ImmediateSubCause.imm_cau_id' => $data['immediate_cause']])
            ->all();
            
        $this->set('causeList', $causeList);
    }
        
    public function saveHsseInvestigation()
    {
        $this->request->allowMethod(['post', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;

        $data = $this->request->getData();
        $hsseInvestigationTable = $this->getTableLocator()->get('HsseInvestigation');

        // Check if an investigation already exists for this report
        $investigationEntity = $hsseInvestigationTable->find()
            ->where(['report_id' => $data['report_id']])
            ->first();

        $res = $investigationEntity ? 'Update' : 'add';

        if (!$investigationEntity) {
            $investigationEntity = $hsseInvestigationTable->newEmptyEntity();
        }

        // Set fields
        $investigationEntity->report_id = $data['report_id'] ?? null;
        $investigationEntity->team_user_id = $data['id_holder'] ?? null;

        $investigationEntity->people_title = ($data['people_title'] ?? '') === 'Enter People Title' ? '' : $data['people_title'];
        $investigationEntity->people_description = ($data['people_descrption'] ?? '') === 'Enter People Description' ? '' : $data['people_descrption'];

        $investigationEntity->position_title = ($data['position_title'] ?? '') === 'Enter Position Title' ? '' : $data['position_title'];
        $investigationEntity->position_description = ($data['position_descrption'] ?? '') === 'Enter Position Description' ? '' : $data['position_descrption'];

        $investigationEntity->parts_title = ($data['part_title'] ?? '') === 'Enter Parts Title' ? '' : $data['part_title'];
        $investigationEntity->parts_description = ($data['part_descrption'] ?? '') === 'Enter Parts Description' ? '' : $data['part_descrption'];

        $investigationEntity->paper_title = ($data['paper_title'] ?? '') === 'Enter Paper Title' ? '' : $data['paper_title'];
        $investigationEntity->paper_descrption = ($data['paper_descrption'] ?? '') === 'Enter Paper Description' ? '' : $data['paper_descrption'];

        if ($hsseInvestigationTable->save($investigationEntity)) {
            echo $res;
        } else {
            echo 'fail';
        }
    }

    public function mainClose(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        $report = $this->Reports->get($data['report_id']);
        
        if ($data['type'] == 'close') {
            $report->closer_date = date('Y-m-d');
        } elseif ($data['type'] == 'reopen') {
            $report->closer_date = '0000-00-00';
        }
        
        if ($this->Reports->save($report)) {
            echo $data['type'] . '~' . date('d/m/Y');
        }
    }

    public function reportHsseInvestigationDataList($report_id)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();

        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $decodedId = base64_decode($report_id);
        $this->set('report_id', $decodedId);

        // Fetch report detail safely
        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $decodedId])
            ->first();

        if (!$reportdetail) {
            $this->Flash->error(__('Invalid report ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Set client tab flag
        $clientTab = ($reportdetail->client == 9) ? 0 : 1;
        $this->set('clienttab', $clientTab);

        $this->set('report_number', $reportdetail->report_no);

        // Fetch client detail
        $clientdetail = $this->HsseClient->find()
            ->where(['HsseClient.report_id' => $decodedId])
            ->first();

        // Determine client feedback
        $clientFeedback = ($clientdetail && $clientdetail->clientreviewed == 3) ? 1 : 0;
        $this->set('client_feedback', $clientFeedback);

        // Handle form data
        $data = $this->request->getData();
        if (!empty($data)) {
            $action = $data['HsseRemidial']['action'] ?? 'all';
            $limit = $data['HsseRemidial']['limit'] ?? 50;
        } else {
            $action = 'all';
            $limit = 50;
        }

        $this->set('action', $action);
        $this->set('limit', $limit);

        // Use modern CakePHP session handling
        $session = $this->request->getSession();
        $session->write('action', $action);
        $session->write('limit', $limit);

        // Clear old session keys
        $session->delete('filter');
        $session->delete('value');

        $this->set('report_val', $report_id);
    }

    /*public function get_all_hsse_investigation_data_list($report_id)
    {
        Configure::write("debug", "2");
        $this->layout = "ajax";
        $this->_checkAdminSession();
        $condition = "";

        $condition = "HsseInvestigationData.report_id = $report_id  AND  HsseInvestigationData.isdeleted = 'N'";

        if (isset($_REQUEST["filter"])) {
            switch ($_REQUEST["filter"]) {
                case "incident_loss":
                    $lossDetail = $this->Losses->find("all", [
                        "conditions" => ["type" => trim($_REQUEST["value"])],
                    ]);
                    if (count($lossDetail) > 0) {
                        $incidentLoss = $this->HsseIncident->find("all", [
                            "conditions" => [
                                "incident_loss" => $lossDetail[0]["Loss"]["id"],
                                "report_id" => $report_id,
                            ],
                        ]);
                        $incident_invest_id = [];
                        for ($i = 0; $i < count($incidentLoss); $i++) {
                            $incident_invest_id[] =
                                $incidentLoss[$i]["HsseInvestigationData"][0][
                                    "id"
                                ];
                        }
                        $incident_id = "";
                        if (count($incident_invest_id) > 0) {
                            $incident_id = implode(",", $incident_invest_id);
                        }
                        $condition =
                            "HsseInvestigationData.id IN (" .
                            $incident_id .
                            ")";
                    }
                    break;
                case "imd_cause_name":
                    $imd_cause_holder = [];
                    $immdiateCause = $this->ImmediateCauses->find("all", [
                        "conditions" => ["type" => trim($_REQUEST["value"])],
                    ]);

                    if (count($immdiateCause) > 0) {
                        $investData = $this->HsseInvestigationData->find(
                            "all",
                            ["conditions" => ["report_id" => $report_id]]
                        );

                        if (count($investData) > 0) {
                            for ($i = 0; $i < count($investData); $i++) {
                                $explode_imd_cause = explode(
                                    ",",
                                    $investData[$i]["HsseInvestigationData"][
                                        "immediate_cause"
                                    ]
                                );
                                if (
                                    $explode_imd_cause[0] ==
                                    $immdiateCause[0]["ImmediateCauses"]["id"]
                                ) {
                                    $imd_cause_holder[] =
                                        $investData[$i][
                                            "HsseInvestigationData"
                                        ]["id"];
                                }
                            }
                            if (count($imd_cause_holder) > 0) {
                                $incident_id = implode(",", $imd_cause_holder);
                                $condition =
                                    "HsseInvestigationData.id IN (" .
                                    $incident_id .
                                    ")";
                            }
                        }
                    }
                    $immdiateSubCause = $this->ImmediateSubCause->find("all", [
                        "conditions" => ["type" => trim($_REQUEST["value"])],
                    ]);

                    if (count($immdiateSubCause) > 0) {
                        $investData = $this->HsseInvestigationData->find(
                            "all",
                            ["conditions" => ["report_id" => $report_id]]
                        );

                        if (count($investData) > 0) {
                            for ($i = 0; $i < count($investData); $i++) {
                                $explode_imd_cause = explode(
                                    ",",
                                    $investData[$i]["HsseInvestigationData"][
                                        "immediate_cause"
                                    ]
                                );
                                if (
                                    $explode_imd_cause[1] ==
                                    $immdiateSubCause[0]["ImmediateSubCause"][
                                        "id"
                                    ]
                                ) {
                                    $imd_cause_holder[] =
                                        $investData[$i][
                                            "HsseInvestigationData"
                                        ]["id"];
                                }
                            }
                            if (count($imd_cause_holder) > 0) {
                                $incident_id = implode(",", $imd_cause_holder);
                                $condition =
                                    "HsseInvestigationData.id IN (" .
                                    $incident_id .
                                    ")";
                            }
                        }
                    }
                    break;
                case "root_cause_list":
                    $rootCause_array = [];
                    $rootCause = $this->RootCause->find("all", [
                        "conditions" => ["type" => trim($_REQUEST["value"])],
                    ]);
                    if (count($rootCause) > 0) {
                        $investData = $this->HsseInvestigationData->find(
                            "all",
                            ["conditions" => ["report_id" => $report_id]]
                        );

                        if (count($investData) > 0) {
                            for ($i = 0; $i < count($investData); $i++) {
                                $explode_root_cause = explode(
                                    ",",
                                    $investData[$i]["HsseInvestigationData"][
                                        "root_cause_id"
                                    ]
                                );

                                if (
                                    in_array(
                                        $rootCause[0]["RootCause"]["id"],
                                        $explode_root_cause
                                    )
                                ) {
                                    $rootCause_array[] =
                                        $investData[$i][
                                            "HsseInvestigationData"
                                        ]["id"];
                                }
                            }
                            if (count($rootCause_array) > 0) {
                                $incident_id = implode(",", $rootCause_array);
                                $condition =
                                    "HsseInvestigationData.id IN (" .
                                    $incident_id .
                                    ")";
                            }
                        }
                    }

                    break;
            }
        }

        /*if(isset($_REQUEST['filter'])){
			$condition .= "AND ucase(HsseInvestigationData.".$_REQUEST['filter'].") like '".$_REQUEST['value']."%'";	
		}
		*/
        /*$limit = null;
        if ($_REQUEST["limit"] == "all") {
        } else {
            $limit = $_REQUEST["start"] . ", " . $_REQUEST["limit"];
        }

        $adminArray = [];
        $count = $this->HsseInvestigationData->find("count", [
            "conditions" => $condition,
        ]);
        $adminA = $this->HsseInvestigationData->find("all", [
            "conditions" => $condition,
            "order" => "HsseInvestigationData.id DESC",
            "limit" => $limit,
        ]);

        $i = 0;
        foreach ($adminA as $rec) {
            if ($rec["HsseInvestigationData"]["isblocked"] == "N") {
                $adminA[$i]["HsseInvestigationData"]["blockHideIndex"] = "true";
                $adminA[$i]["HsseInvestigationData"]["unblockHideIndex"] =
                    "false";
                $adminA[$i]["HsseInvestigationData"]["isdeletdHideIndex"] =
                    "true";
            } else {
                $adminA[$i]["HsseInvestigationData"]["blockHideIndex"] =
                    "false";
                $adminA[$i]["HsseInvestigationData"]["unblockHideIndex"] =
                    "true";
                $adminA[$i]["HsseInvestigationData"]["isdeletdHideIndex"] =
                    "false";
            }

            $i++;
        }

        $adminArray = Set::extract($adminA, "{n}.HsseInvestigationData");
        $rootCauseval = [];
        $rval = "";
        if (count($adminArray) > 0) {
            for ($i = 0; $i < count($adminArray); $i++) {
                $incdent_loss_ID = $this->HsseIncident->find("all", [
                    "conditions" => [
                        "HsseIncident.id" => $adminArray[$i]["incident_id"],
                    ],
                ]);
                $incdent_loss = $this->Losses->find("all", [
                    "conditions" => [
                        "Losses.id" =>
                            $incdent_loss_ID[0]["HsseIncident"][
                                "incident_loss"
                            ],
                    ],
                ]);
                $adminArray[$i]["incident_loss"] =
                    $incdent_loss[0]["Loss"]["type"];
                $explode_imd_cause = explode(
                    ",",
                    $adminArray[$i]["immediate_cause"]
                );
                $immidiate_cause = $this->ImmediateCauses->find("all", [
                    "conditions" => [
                        "ImmediateCauses.id" => $explode_imd_cause[0],
                    ],
                ]);
                if (isset($explode_imd_cause[1])) {
                    $immidiate_sub_cause = $this->ImmediateSubCause->find(
                        "all",
                        [
                            "conditions" => [
                                "ImmediateSubCause.id" => $explode_imd_cause[1],
                            ],
                        ]
                    );
                    if (count($immidiate_sub_cause) > 0) {
                        $adminArray[$i]["imd_cause_name"] =
                            $immidiate_cause[0]["ImmediateCauses"]["type"] .
                            "<br/>" .
                            $immidiate_sub_cause[0]["ImmediateSubCause"][
                                "type"
                            ];
                    } else {
                        $adminArray[$i]["imd_cause_name"] =
                            $immidiate_cause[0]["ImmediateCauses"]["type"];
                    }
                } else {
                    $adminArray[$i]["imd_cause_name"] = "";
                }
                $explode_root_cause = explode(
                    ",",
                    $adminArray[$i]["root_cause_id"]
                );

                for ($r = 0; $r < count($explode_root_cause); $r++) {
                    if ($explode_root_cause[$r] != 0) {
                        $root_cause = $this->RootCause->find("all", [
                            "conditions" => [
                                "RootCause.id" => $explode_root_cause[$r],
                            ],
                        ]);
                        $rootCauseval[$i][] =
                            $root_cause[0]["RootCause"]["type"];
                    }
                }
                $adminArray[$i]["root_cause_list"] = implode(
                    "<br/>",
                    $rootCauseval[$i]
                );
            }
        }

        $this->set("total", $count); //send total to the view

        $this->set("admins", $adminArray); //send products to the view
        //$this->set('status', $action);
    }*/
    public function getAllHsseInvestigationDataList($reportId): void
    {
        $this->request->allowMethod(['get', 'post', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');
        $this->_checkAdminSession();

        $query = $this->HsseInvestigationData->find()
            ->where([
                'HsseInvestigationData.report_id' => $reportId,
                'HsseInvestigationData.isdeleted' => 'N'
            ]);

        $filter = $this->request->getQuery('filter') ?? $this->request->getData('filter');
        $valueRaw = $this->request->getQuery('value') ?? $this->request->getData('value');
        $value = is_string($valueRaw) ? trim($valueRaw) : '';

        if (!empty($filter)) {
            switch ($filter) {
                case 'incident_loss':
                    $loss = $this->Losses->find()
                        ->select(['id'])
                        ->where(['type' => $value])
                        ->first();

                    if ($loss) {
                        $incidentIds = $this->HsseIncident->find()
                            ->select(['id'])
                            ->where([
                                'incident_loss' => $loss->id,
                                'report_id' => $reportId
                            ])
                            ->all()
                            ->extract('id')
                            ->toList();

                        if ($incidentIds) {
                            $query->where(['HsseInvestigationData.incident_id IN' => $incidentIds]);
                        }
                    }
                    break;

                case 'imd_cause_name':
                    $immediateCause = $this->ImmediateCauses->find()
                        ->select(['id'])
                        ->where(['type' => $value])
                        ->first();

                    $immediateSubCause = $this->ImmediateSubCause->find()
                        ->select(['id'])
                        ->where(['type' => $value])
                        ->first();

                    if ($immediateCause || $immediateSubCause) {
                        $investigationIds = [];

                        $investigations = $this->HsseInvestigationData->find()
                            ->where(['report_id' => $reportId])
                            ->all();

                        foreach ($investigations as $inv) {
                            $immediateIds = array_filter(explode(',', (string)$inv->immediate_cause));

                            if (
                                ($immediateCause && in_array($immediateCause->id, $immediateIds)) ||
                                ($immediateSubCause && in_array($immediateSubCause->id, $immediateIds))
                            ) {
                                $investigationIds[] = $inv->id;
                            }
                        }

                        if ($investigationIds) {
                            $query->where(['HsseInvestigationData.id IN' => $investigationIds]);
                        }
                    }
                    break;

                case 'root_cause_list':
                    $rootCause = $this->RootCause->find()
                        ->select(['id'])
                        ->where(['type' => $value])
                        ->first();

                    if ($rootCause) {
                        $investigationIds = [];

                        $investigations = $this->HsseInvestigationData->find()
                            ->where(['report_id' => $reportId])
                            ->all();

                        foreach ($investigations as $inv) {
                            $rootIds = array_filter(explode(',', (string)$inv->root_cause_id));
                            if (in_array($rootCause->id, $rootIds)) {
                                $investigationIds[] = $inv->id;
                            }
                        }

                        if ($investigationIds) {
                            $query->where(['HsseInvestigationData.id IN' => $investigationIds]);
                        }
                    }
                    break;
            }
        }

        // Pagination (limit/start)
        $start = (int)($this->request->getQuery('start') ?? 0);
        $limit = $this->request->getQuery('limit');

        if ($limit && $limit !== 'all') {
            $query->limit((int)$limit)->offset($start);
        }

        $query->orderDesc('HsseInvestigationData.id');

        $count = $query->count();
        $records = $query->all();

        $adminArray = [];
        foreach ($records as $record) {
            $data = $record->toArray();

            // Status flags
            if ($record->isblocked === 'N') {
                $data['blockHideIndex'] = true;
                $data['unblockHideIndex'] = false;
                $data['isdeletdHideIndex'] = true;
            } else {
                $data['blockHideIndex'] = false;
                $data['unblockHideIndex'] = true;
                $data['isdeletdHideIndex'] = false;
            }

            // Incident & Loss type
            $incident = $this->HsseIncident->find()
                ->where(['HsseIncident.id' => $record->incident_id])
                ->first();

            $lossType = '';
            if ($incident) {
                $loss = $this->Losses->find()
                    ->where(['id' => $incident->incident_loss])
                    ->first();
                $lossType = $loss ? $loss->type : '';
            }
            $data['incident_loss'] = $lossType;

            // Immediate cause
            $immediateIds = array_filter(explode(',', (string)$record->immediate_cause));
            $imdCauseName = '';

            if (!empty($immediateIds)) {
                $mainCause = $this->ImmediateCauses->find()
                    ->where(['id' => $immediateIds[0]])
                    ->first();
                $subCause = null;

                if (isset($immediateIds[1])) {
                    $subCause = $this->ImmediateSubCause->find()
                        ->where(['id' => $immediateIds[1]])
                        ->first();
                }

                $imdCauseName = $mainCause ? $mainCause->type : '';
                if ($subCause) {
                    $imdCauseName .= "<br/>" . $subCause->type;
                }
            }
            $data['imd_cause_name'] = $imdCauseName;

            // Root causes
            $rootIds = array_filter(explode(',', (string)$record->root_cause_id));
            $rootCauseList = [];

            if (!empty($rootIds)) {
                $rootCauses = $this->RootCause->find()
                    ->where(['id IN' => $rootIds])
                    ->all();

                foreach ($rootCauses as $root) {
                    $rootCauseList[] = $root->type;
                }
            }

            $data['root_cause_list'] = implode('<br/>', $rootCauseList);

            $adminArray[] = $data;
        }

        $this->set('total', $count);
        $this->set('admins', $adminArray);
        $this->viewBuilder()->setOption('serialize', ['total', 'admins']);

    }


    public function investigationDataBlock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $investigationId) {
            $investigation = $this->HsseInvestigationData->get($investigationId);
            $investigation->isblocked = 'Y';
            $this->HsseInvestigationData->save($investigation);
        }
        
        echo 'ok';
    }

    public function investigationDataUnblock($id = null): void
    {
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $investigationId) {
            $investigation = $this->HsseInvestigationData->get($investigationId);
            $investigation->isblocked = 'N';
            $this->HsseInvestigationData->save($investigation);
        }
        
        echo 'ok';
    }

    public function investigationDataDelete(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();
        $idArray = explode('^', $data['id']);
        
        foreach ($idArray as $id) {
            $investigation = $this->HsseInvestigationData->get($id);
            $investigation->isdeleted = 'Y';
            $this->HsseInvestigationData->save($investigation);
        }
        
        echo 'ok';
    }

    public function addInvestigationDataAnalysis(
        string $report_id,
        ?string $incident_id = null,
        ?string $investigation_id = null
    ): void {
        
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');

        $reportIdDecoded = base64_decode($report_id);

        $immediateCauseDetail = $this->ImmediateCauses->find('all')->toArray();
        $this->set(compact('immediateCauseDetail'));

        $rootParrentCauseData = $this->RootCause->find('all', [
            'conditions' => ['parrent_id' => 0],
        ])->toArray();//dd($rootParrentCauseData);
        $this->set(compact('rootParrentCauseData'));

        $remidialData = $this->HsseRemidial->find('all', [
            'conditions' => [
                'HsseRemidial.report_no' => $reportIdDecoded,
                'HsseRemidial.isblocked' => 'N',
                'HsseRemidial.isdeleted' => 'N',
            ],
        ])->toArray();//dd($remidialData);
        $this->set(compact('remidialData'));

        $reportdetail = $this->Reports->find()
            ->where(['Reports.id' => $reportIdDecoded])
            ->first();

        $report_number = $reportdetail->report_no ?? '';
        $this->set(compact('report_number'));
        $this->set('report_id', $reportIdDecoded);

        $edit_investigation_id = $this->request->getQuery('edit_investigation_id')
            ?? $this->request->getData('edit_investigation_id')
            ?? null;
        $this->set('edit_investigation_id', $edit_investigation_id);
        //dd($reportIdDecoded);
        
        $incidentdetail = $this->HsseIncident->find('all', [
            'conditions' => [
                'HsseIncident.report_id' => $reportIdDecoded,
                'HsseIncident.isblocked' => 'N',
                'HsseIncident.isdeleted' => 'N',
            ],
        ])->toArray();//dd($incidentdetail);
        $this->set(compact('incidentdetail'));

        $clientdetail = $this->HsseClient->find('all', [
            'conditions' => ['HsseClient.report_id' => $reportIdDecoded],
        ])->toArray();

        $client_feedback = (!empty($clientdetail) && $clientdetail[0]->clientreviewed == 3) ? 1 : 0;
        $this->set(compact('client_feedback'));

        if (!empty($investigation_id)) {
            $investigationIdDecoded = base64_decode($investigation_id);

            $investigationDetail = $this->HsseInvestigationData->find('all', [
                'conditions' => ['HsseInvestigationData.id' => $investigationIdDecoded],
            ])->contain(['HsseIncident.Loss'])->toArray();

            if (empty($investigationDetail)) {
                $this->Flash->error(__('Invalid investigation record.'));
                $this->redirect(['action' => 'index']);
                return;
            }

            $data = $investigationDetail[0]['HsseInvestigationData'];

            // Set main investigation info
            $this->set([
                'investigation_no' => $data['investigation_no'] ?? '',
                'ltype' => $investigationDetail[0]['HsseIncident']['Loss']['type'] ?? '',
                'incident_summary' => $investigationDetail[0]['HsseIncident']['incident_summary'] ?? '',
                'comment' => $data['comments'] ?? '',
            ]);

            // Handle Immediate Cause & Sub-Cause
            $explode_immd_cause = explode(',', $data['immediate_cause'] ?? '0');
            $immediate_cause = (int)($explode_immd_cause[0] ?? 0);
            $immediate_sub_cause = (int)($explode_immd_cause[1] ?? 0);
            $this->set(compact('immediate_cause', 'immediate_sub_cause'));

            if ($immediate_cause) {
                $immediateSubCauseList = $this->ImmediateSubCause->find('all', [
                    'conditions' => ['imm_cau_id' => $immediate_cause],
                ])->toArray();
                $this->set(compact('immediateSubCauseList'));
            }

            // Handle Remedial Actions
            $remedialIds = $data['remedila_action_id'] ?? '';
            $remidialList = [];
            if (!empty($remedialIds)) {
                $remidialList = $this->HsseRemidial->find('all', [
                    'conditions' => ['HsseRemidial.id IN' => explode(',', $remedialIds)],
                ])->toArray();
            }
            $this->set(compact('remidialList'));

            // Handle Root Causes
            $explode_root_cause = !empty($data['root_cause_id'])
                ? explode(',', $data['root_cause_id'])
                : [];

            $rootParrentCausevalue = $explode_root_cause[0] ?? 0;
            $this->set(compact('rootParrentCausevalue', 'explode_root_cause'));

            $childRoot = [];
            if (!empty($explode_root_cause)) {
                foreach ($explode_root_cause as $rootId) {
                    $childRoot[$rootId] = $this->RootCause->find('all', [
                        'conditions' => ['RootCause.parrent_id' => $rootId],
                    ])->toArray();
                }
            }
            $this->set(compact('childRoot'));
            $comments = '';
            if (!empty($investigationData)) {
                $comments = $investigationData->comments ?? '';
            }
            $this->set(compact('comments'));
            //dd($childRoot);
            // Final View Variables for Edit
            $this->set([
                'heading' => 'Edit Investigation Data Analysis',
                'button' => 'Update',
                'edit_investigation_id' => $investigationIdDecoded,
                'disabled' => 'disabled="disabled"',
                'edit_incident_id' => base64_decode($incident_id ?? ''),
                'incident_no' => $data['incident_no'] ?? '',
                'id_holder' => $data['remedila_action_id'] ?? '',
                'idContent' => $data['remedila_action_id'] ?? '',
                'tr_id' => !empty($remedialIds)
                    ? implode(',', array_map(fn($id) => 'tr' . $id, explode(',', $remedialIds)))
                    : '',
            ]);
        }

        else {
            $this->set([
                'investigation_no' => '',
                'ltype' => '',
                'incident_summary' => '',
                'heading' => 'Add Investigation Data Analysis',
                'comments' => '',
                'button' => 'Submit',
                'edit_investigation_id' => 0,
                'disabled' => '',
                'edit_incident_id' => 0,
                'remidialList' => [],
                'immediate_cause' => 0,
                'immediate_sub_cause' => 0,
                'parrentRoot' => [],
                'rootParrentCausevalue' => 0,
                'explode_root_cause' => [],
                'incident_no' => 0,
                'id_holder' => 0,
                'idContent' => '',
                'tr_id' => '',
            ]);
        }
    }


    public function displayincidentdetail(): void
    {
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;

        $immediateCauseDetail = $this->ImmediateCauses->find()->all();
        $this->set('immediateCauseDetail', $immediateCauseDetail);
        
        $data = $this->request->getData();
        $incidentDetail = $this->HsseIncident->find()
            ->where(['HsseIncident.id' => $data['incidentid']])
            ->first();
            
        if ($incidentDetail) {
            $lossData = $this->Losses->find()
                ->where(['Losses.id' => $incidentDetail->incident_loss])
                ->first();

            $this->set('ltype', $lossData ? $lossData->type : '');
            $this->set('incident_summary', $incidentDetail->incident_summary);
            
            $incidentInvestigation = $this->HsseInvestigationData->find()
                ->where(['HsseInvestigationData.incident_id' => $data['incidentid']])
                ->all();

            $zeroParentId = $this->RootCause->find()
                ->where(['parrent_id' => 0])
                ->all();
            $this->set('zeroParrent', $zeroParentId);
            
            $investigation_no = $incidentInvestigation->count() + 1;
            
            echo ($lossData ? $lossData->type : '') . '~' . 
                 $incidentDetail->incident_summary . '~' . 
                 $investigation_no . '~' . 
                 $incidentDetail->incident_no;
        }
    }

    public function retriverootcause(): void
    {
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;
        
        $data = $this->request->getData();//dd($data);
        $rootChildCauseData = $this->RootCause->find()
            ->where(['parrent_id' => $data['id']])
            ->all();
        //dd($rootChildCauseData);    
        if (!$rootChildCauseData->isEmpty()) {
            $this->set('rootChildCauseData', $rootChildCauseData);
            $this->set('parrentid', $data['id']);
            $this->autoRender = true;
        }
    }
    
    public function saveDateAnalysis(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->autoRender = false;

        $data = $this->request->getData();

        // Determine if we’re adding or updating
        if (!empty($data['investigation_id']) && $data['investigation_id'] != 0) {
            $investigation = $this->HsseInvestigationData->get($data['investigation_id']);
            $res = 'update';
        } else {
            $investigation = $this->HsseInvestigationData->newEntity([]);
            $res = 'add';
        }

        // Handle remedial holder string cleanup
        $rH = '';
        if (!empty($data['remidial_holder'])) {
            $rH = implode(',', array_values(array_unique(explode(',', $data['remidial_holder']))));
            $rH = ltrim($rH, ',');
        }

        // Prepare entity data
        $investigationData = [
            'investigation_no'   => $data['investigation_no'] ?? '',
            'incident_no'        => $data['incident_no'] ?? '',
            'report_id'          => $data['report_id'] ?? null,
            'incident_id'        => $data['incident_val'] ?? null,
            'immediate_cause'    => $data['causeCont'] ?? '',
            'root_cause_id'      => $data['rootCauseCont'] ?? '',
            'remedila_action_id' => $rH,
            'comments'           => $data['comments'] ?? '',
            'isdeleted'          => 'N',
            'isblocked'          => 'N',
        ];

        // Patch and save
        $investigation = $this->HsseInvestigationData->patchEntity($investigation, $investigationData);

        if ($this->HsseInvestigationData->save($investigation)) {
            // Prepare encoded IDs for redirect
            $lastInvestigationId = base64_encode((string)(
                $res === 'add' ? $investigation->id : $data['investigation_id']
            ));
            $reportId   = base64_encode((string)$data['report_id']);
            $incidentId = base64_encode((string)$data['incident_val']);

            echo $res . '~' . $reportId . '~' . $incidentId . '~' . $lastInvestigationId;
        } else {
            echo 'fail~0~0~0';
        }
    }

    function add_hsse_feedback($id = null)
    {
        $this->layout = "after_adminlogin_template";
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $clientdetail = $this->HsseClient->find("all", [
            "conditions" => ["HsseClient.report_id" => base64_decode($id)],
        ]);
        $reportdetail = $this->Report->find("all", [
            "conditions" => ["Report.id" => base64_decode($id)],
        ]);

        if (count($clientdetail) > 0) {
            $this->set(
                "clientreviewer",
                $clientdetail[0]["HsseClient"]["clientreviewer"]
            );
            $this->set("clientfeedback_remark", "");
            $clientfeeddetail = $this->HsseClientfeedback->find("all", [
                "conditions" => [
                    "HsseClientfeedback.report_id" => base64_decode($id),
                ],
            ]);
            $clientdetail = $this->HsseClient->find("all", [
                "conditions" => ["HsseClient.report_id" => base64_decode($id)],
            ]);

            if (count($clientdetail) > 0) {
                if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                    $this->set("client_feedback", 1);
                } elseif (
                    $clientdetail[0]["HsseClient"]["clientreviewed"] != 3
                ) {
                    $this->set("client_feedback", 0);
                }
            } else {
                $this->set("client_feedback", 0);
            }

            $this->set("report_id", base64_decode($id));
            if (count($clientfeeddetail) > 0) {
                $cls_date = explode(
                    "-",
                    $clientfeeddetail[0]["HsseClientfeedback"]["close_date"]
                );
                $close_date =
                    $cls_date[1] . "-" . $cls_date[2] . "-" . $cls_date[0];
                $this->set("close_date", $close_date);
                $this->set("heading", "Edit Client Feedback");
                $this->set(
                    "client_summary",
                    $clientfeeddetail[0]["HsseClientfeedback"]["client_summary"]
                );
                $this->set("button", "Update");
                $this->set(
                    "id",
                    $clientfeeddetail[0]["HsseClientfeedback"]["id"]
                );
            } else {
                $this->set("close_date", "");
                $this->set("heading", "Add Client Feedback");
                $this->set("client_summary", "");
                $this->set("button", "Add");
                $this->set("id", 0);
            }
        }
    }

    function clientfeedbackprocess()
    {
        $this->layout = "ajax";
        if ($this->data["add_report_client_data_form"]["id"] != 0) {
            $client_feed_back["HsseClientfeedback"]["id"] =
                $this->data["add_report_client_data_form"]["id"];
            $res = "Update";
        } else {
            $res = "Add";
        }

        $client_feed_back["HsseClientfeedback"]["report_id"] =
            $this->data["report_id"];
        if ($this->data["close_date"] != "") {
            $clsr = explode("-", $this->data["close_date"]);
            $closer_date = $clsr[2] . "-" . $clsr[0] . "-" . $clsr[1];
        } else {
            $closer_date = "";
        }
        $client_feed_back["HsseClientfeedback"]["close_date"] = $closer_date;
        if (
            $this->data["add_report_client_data_form"]["client_summary"] != ""
        ) {
            $client_feed_back["HsseClientfeedback"]["client_summary"] =
                $this->data["add_report_client_data_form"]["client_summary"];
        }
        if ($this->HsseClientfeedback->save($client_feed_back)) {
            echo $res;
        } else {
            echo "fail";
        }
        exit();
    }

    function print_view($id = null)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->hsse_client_tab();
        $this->layout = "ajax";
        $reportdetail = $this->Report->find("all", [
            "conditions" => ["Report.id" => base64_decode($id)],
        ]);

        $this->set("id", base64_decode($id));
        if ($reportdetail[0]["Report"]["event_date"] != "") {
            $evndt = explode("-", $reportdetail[0]["Report"]["event_date"]);
            $event_date = $evndt[2] . "/" . $evndt[1] . "/" . $evndt[0];
        } else {
            $event_date = "";
        }

        $clientdetail = $this->HsseClient->find("all", [
            "conditions" => ["HsseClient.report_id" => base64_decode($id)],
        ]);
        if ($reportdetail[0]["Report"]["client"] == 10) {
            $this->set("clienttabshow", 0);

            $this->set("clienttab", 0);
        } elseif ($reportdetail[0]["Report"]["client"] != 10) {
            $this->set("clienttabshow", 1);
            if (count($clientdetail) > 0) {
                $this->set("clienttab", 1);
            } else {
                $this->set("clienttab", 0);
            }
        }

        if (count($clientdetail) > 0) {
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                $this->set("client_feedback", 1);
            } elseif ($clientdetail[0]["HsseClient"]["clientreviewed"] != 3) {
                $this->set("client_feedback", 0);
            }
        } else {
            $this->set("client_feedback", 0);
        }

        $this->set("event_date", $event_date);
        $this->set(
            "since_event_hidden",
            $reportdetail[0]["Report"]["since_event"]
        );
        $this->set("since_event", $reportdetail[0]["Report"]["since_event"]);
        $this->set("reportno", $reportdetail[0]["Report"]["report_no"]);

        if ($reportdetail[0]["Report"]["closer_date"] != "0000-00-00") {
            $clsdt = explode("-", $reportdetail[0]["Report"]["closer_date"]);
            $closedt = $clsdt[2] . "/" . $clsdt[1] . "/" . $clsdt[0];
        } else {
            $closedt = "00/00/0000";
        }

        $this->set("closer_date", $closedt);

        $incident_detail = $this->Incident->find("all", [
            "conditions" => [
                "Incident.id" => $reportdetail[0]["Report"]["incident_type"],
            ],
        ]);
        $this->set("incident_type", $incident_detail[0]["Incident"]["type"]);

        $user_detail = $this->AdminMaster->find("all", [
            "conditions" => [
                "AdminMaster.id" => $reportdetail[0]["Report"]["created_by"],
            ],
        ]);
        $this->set(
            "created_by",
            $user_detail[0]["AdminMaster"]["first_name"] .
                " " .
                $user_detail[0]["AdminMaster"]["last_name"]
        );

        $this->set("created_date", "");

        $business_unit_detail = $this->BusinessType->find("all", [
            "conditions" => [
                "BusinessType.id" =>
                    $reportdetail[0]["Report"]["business_unit"],
            ],
        ]);
        $this->set(
            "business_unit",
            $business_unit_detail[0]["BusinessType"]["type"]
        );

        $client_detail = $this->Client->find("all", [
            "conditions" => [
                "Client.id" => $reportdetail[0]["Report"]["client"],
            ],
        ]);
        $this->set("client", $client_detail[0]["Client"]["name"]);

        $fieldlocation = $this->Fieldlocation->find("all", [
            "conditions" => [
                "Fieldlocation.id" =>
                    $reportdetail[0]["Report"]["field_location"],
            ],
        ]);
        $this->set("fieldlocation", $fieldlocation[0]["Fieldlocation"]["type"]);

        $incidentLocation = $this->IncidentLocation->find("all", [
            "conditions" => [
                "IncidentLocation.id" =>
                    $reportdetail[0]["Report"]["field_location"],
            ],
        ]);
        $this->set(
            "incidentLocation",
            $incidentLocation[0]["IncidentLocation"]["type"]
        );

        $countrty = $this->Country->find("all", [
            "conditions" => [
                "Country.id" => $reportdetail[0]["Report"]["country"],
            ],
        ]);
        $this->set("countrty", $countrty[0]["Country"]["name"]);

        $report_detail = $this->AdminMaster->find("all", [
            "conditions" => [
                "AdminMaster.id" => $reportdetail[0]["Report"]["reporter"],
                "AdminMaster.isdeleted" => "N",
                "AdminMaster.isblocked" => "N",
            ],
        ]);
        $this->set(
            "reporter",
            $report_detail[0]["AdminMaster"]["first_name"] .
                " " .
                $report_detail[0]["AdminMaster"]["last_name"]
        );

        $incidentSeverity_detail = $this->IncidentSeverity->find("all", [
            "conditions" => [
                "IncidentSeverity.id" =>
                    $reportdetail[0]["Report"]["incident_severity"],
            ],
        ]);
        $this->set(
            "incidentseveritydetail",
            $incidentSeverity_detail[0]["IncidentSeverity"]["type"]
        );
        $this->set(
            "incidentseveritydetailcolor",
            'style="background-color:' .
                $incidentSeverity_detail[0]["IncidentSeverity"]["color_code"] .
                '"'
        );

        $residual_detail = $this->Residual->find("all", [
            "conditions" => [
                "Residual.id" => $reportdetail[0]["Report"]["residual"],
            ],
        ]);
        $this->set("residual", $residual_detail[0]["Residual"]["type"]);
        if ($residual_detail[0]["Residual"]["color_code"] != "") {
            $this->set(
                "residualcolor",
                'style="background-color:' .
                    $residual_detail[0]["Residual"]["color_code"] .
                    '"'
            );
        } else {
            $this->set("residualcolor", "");
        }

        if ($reportdetail[0]["Report"]["recorable"] == 1) {
            $this->set("recorable", "Yes");
            $this->set("recorablecolor", 'style="background-color:#FF0000"');
        } elseif ($reportdetail[0]["Report"]["recorable"] == 2) {
            $this->set("recorable", "No");
            $this->set("recorablecolor", 'style="background-color:#40FF00"');
        }

        $potentilal_detail = $this->Potential->find("all", [
            "conditions" => [
                "Potential.id" => $reportdetail[0]["Report"]["potential"],
            ],
        ]);
        $this->set("potential", $potentilal_detail[0]["Potential"]["type"]);
        if ($potentilal_detail[0]["Potential"]["color_code"] != "") {
            $this->set(
                "potentialcolor",
                'style="background-color:' .
                    $potentilal_detail[0]["Potential"]["color_code"] .
                    '"'
            );
        } else {
            $this->set("potentialcolor", "");
        }

        $this->set("summary", $reportdetail[0]["Report"]["summary"]);
        $this->set("details", $reportdetail[0]["Report"]["details"]);
        $this->set(
            "created_by",
            $_SESSION["adminData"]["AdminMaster"]["first_name"] .
                " " .
                $_SESSION["adminData"]["AdminMaster"]["last_name"]
        );

        /************************clientdata***************************/
        $clientdetail = $this->HsseClient->find("all", [
            "conditions" => ["HsseClient.report_id" => base64_decode($id)],
        ]);

        if (count($clientdetail) > 0) {
            $this->set("well", $clientdetail[0]["HsseClient"]["well"]);
            $this->set("rig", $clientdetail[0]["HsseClient"]["rig"]);
            $this->set(
                "clientncr",
                $clientdetail[0]["HsseClient"]["clientncr"]
            );
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 1) {
                $this->set("clientreviewed", "N/A");
            } elseif ($clientdetail[0]["HsseClient"]["clientreviewed"] == 2) {
                $this->set("clientreviewed", "N/A");
            }
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                $this->set("clientreviewed", "Yes");
            }

            $this->set(
                "report_id",
                $clientdetail[0]["HsseClient"]["report_id"]
            );
            $this->set(
                "clientreviewer",
                $clientdetail[0]["HsseClient"]["clientreviewer"]
            );
            $this->set(
                "wellsiterep",
                $clientdetail[0]["HsseClient"]["wellsiterep"]
            );
        } else {
            $this->set("well", "");
            $this->set("rig", "");
            $this->set("clientncr", "");
            $this->set("clientreviewed", "");
            $this->set("clientreviewer", "");
            $this->set("wellsiterep", "");
        }

        /************************indidentdata***************************/

        $incidentdetail = $this->HsseIncident->find("all", [
            "conditions" => [
                "HsseIncident.report_id" => base64_decode($id),
                "HsseIncident.isdeleted" => "N",
                "HsseIncident.isblocked" => "N",
            ],
        ]);

        //echo '<pre>';
        //print_r($incidentdetail);

        $incidentdetailHolder = [];
        if (count($incidentdetail) > 0) {
            for ($i = 0; $i < count($incidentdetail); $i++) {
                if (
                    $incidentdetail[$i]["HsseIncident"]["date_incident"] != ""
                ) {
                    $incidt = explode(
                        "-",
                        $incidentdetail[$i]["HsseIncident"]["date_incident"]
                    );
                    $incidentdetailHolder[$i]["date_incident"] =
                        $incidt[2] . "/" . $incidt[1] . "/" . $incidt[0];
                } else {
                    $incidentdetailHolder[$i]["date_incident"] = "";
                }

                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_time"] != ""
                ) {
                    $incidentdetailHolder[$i]["incident_time"] =
                        $incidentdetail[$i]["HsseIncident"]["incident_time"];
                } else {
                    $incidentdetailHolder[$i]["incident_time"] = "";
                }

                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_severity"] !=
                    0
                ) {
                    $incidentSeverity_type = $this->IncidentSeverity->find(
                        "all",
                        [
                            "conditions" => [
                                "IncidentSeverity.id" =>
                                    $incidentdetail[$i]["HsseIncident"][
                                        "incident_severity"
                                    ],
                            ],
                        ]
                    );
                    $incidentdetailHolder[$i]["incident_severity"] =
                        $incidentSeverity_type[0]["IncidentSeverity"]["type"];
                } else {
                    $incidentdetailHolder[$i]["incident_severity"] = "";
                }

                if ($incidentdetail[$i]["HsseIncident"]["incident_loss"] != 0) {
                    $incidentLoss_type = $this->Losses->find("all", [
                        "conditions" => [
                            "Losses.id" =>
                                $incidentdetail[$i]["HsseIncident"][
                                    "incident_loss"
                                ],
                        ],
                    ]);
                    $incidentdetailHolder[$i]["incident_loss"] =
                        $incidentLoss_type[0]["Loss"]["type"];
                } else {
                    $incidentdetailHolder[$i]["incident_loss"] = "";
                }

                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_category"] !=
                    0
                ) {
                    $incident_category_type = $this->IncidentCategory->find(
                        "all",
                        [
                            "conditions" => [
                                "IncidentCategory.id" =>
                                    $incidentdetail[$i]["HsseIncident"][
                                        "incident_category"
                                    ],
                            ],
                        ]
                    );
                    $incidentdetailHolder[$i]["incident_category"] =
                        $incident_category_type[0]["IncidentCategory"]["type"];
                } else {
                    $incidentdetailHolder[$i]["incident_category"] = "";
                }

                if (
                    $incidentdetail[$i]["HsseIncident"][
                        "incident_sub_category"
                    ] != 0
                ) {
                    $incident_sub_category_type = $this->IncidentSubCategory->find(
                        "all",
                        [
                            "conditions" => [
                                "IncidentSubCategory.id" =>
                                    $incidentdetail[$i]["HsseIncident"][
                                        "incident_sub_category"
                                    ],
                            ],
                        ]
                    );
                    $incidentdetailHolder[$i]["incident_sub_category"] =
                        $incident_sub_category_type[0]["IncidentSubCategory"][
                            "type"
                        ];
                } else {
                    $incidentdetailHolder[$i]["incident_sub_category"] = "";
                }

                if (
                    $incidentdetail[$i]["HsseIncident"]["incident_summary"] !=
                    ""
                ) {
                    $incidentdetailHolder[$i]["incident_summary"] =
                        $incidentdetail[$i]["HsseIncident"]["incident_summary"];
                } else {
                    $incidentdetailHolder[$i]["incident_summary"] = "";
                }
                if ($incidentdetail[$i]["HsseIncident"]["detail"] != "") {
                    $incidentdetailHolder[$i]["detail"] =
                        $incidentdetail[$i]["HsseIncident"]["detail"];
                } else {
                    $incidentdetailHolder[$i]["detail"] = "";
                }

                $incidentdetailHolder[$i]["id"] =
                    $incidentdetail[$i]["HsseIncident"]["id"];

                $incidentdetailHolder[$i]["isblocked"] =
                    $incidentdetail[$i]["HsseIncident"]["isblocked"];
                $incidentdetailHolder[$i]["isdeleted"] =
                    $incidentdetail[$i]["HsseIncident"]["isdeleted"];
                $incidentdetailHolder[$i]["incident_no"] =
                    $incidentdetail[$i]["HsseIncident"]["incident_no"];
                if (count($incidentdetail[$i]["HsseInvestigationData"]) > 0) {
                    for (
                        $v = 0;
                        $v <
                        count($incidentdetail[$i]["HsseInvestigationData"]);
                        $v++
                    ) {
                        $immidiate_cause = explode(
                            ",",
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "immediate_cause"
                            ]
                        );
                        if ($immidiate_cause[0] != "") {
                            $imdCause = $this->ImmediateCauses->find("all", [
                                "conditions" => [
                                    "ImmediateCauses.id" => $immidiate_cause[0],
                                ],
                            ]);
                            if (count($imdCause) > 0) {
                                $incidentdetailHolder[$i]["imd_cause"][] =
                                    $imdCause[0]["ImmediateCauses"]["type"];
                            }

                            if (isset($immidiate_cause[1])) {
                                $imdSubCause = $this->ImmediateSubCause->find(
                                    "all",
                                    [
                                        "conditions" => [
                                            "ImmediateSubCause.id" =>
                                                $immidiate_cause[1],
                                        ],
                                    ]
                                );
                                if (count($imdSubCause) > 0) {
                                    $incidentdetailHolder[$i][
                                        "imd_sub_cause"
                                    ][] =
                                        $imdSubCause[0]["ImmediateSubCause"][
                                            "type"
                                        ];
                                }
                            }
                        }
                        if (
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "comments"
                            ] != ""
                        ) {
                            $incidentdetailHolder[$i]["comment"] =
                                $incidentdetail[$i]["HsseInvestigationData"][
                                    $v
                                ]["comments"];
                        }
                        $incidentdetailHolder[$i]["investigation_block"] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "isblocked"
                            ];
                        $incidentdetailHolder[$i]["investigation_delete"] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "isdeleted"
                            ];
                        $incidentdetailHolder[$i]["investigation_no"][] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "investigation_no"
                            ];
                        $incidentdetailHolder[$i]["incident_no_investigation"] =
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "incident_no"
                            ];
                        if (
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "root_cause_id"
                            ] != 0
                        ) {
                            $explode_rootcause = explode(
                                ",",
                                $incidentdetail[$i]["HsseInvestigationData"][
                                    $v
                                ]["root_cause_id"]
                            );

                            for ($j = 0; $j < count($explode_rootcause); $j++) {
                                if ($explode_rootcause[$j] != 0) {
                                    $rootCauseDetail = $this->RootCause->find(
                                        "all",
                                        [
                                            "conditions" => [
                                                "RootCause.id" =>
                                                    $explode_rootcause[$j],
                                            ],
                                        ]
                                    );
                                    $incidentdetailHolder[$i]["root_cause_val"][
                                        $v
                                    ][$j] =
                                        $rootCauseDetail[0]["RootCause"][
                                            "type"
                                        ];
                                }
                            }
                        }
                        if (
                            $incidentdetail[$i]["HsseInvestigationData"][$v][
                                "remedila_action_id"
                            ] != ""
                        ) {
                            $explode_remedila_action_id = explode(
                                ",",
                                $incidentdetail[$i]["HsseInvestigationData"][
                                    $v
                                ]["remedila_action_id"]
                            );
                            for (
                                $k = 0;
                                $k < count($explode_remedila_action_id);
                                $k++
                            ) {
                                if (isset($explode_remedila_action_id[$k])) {
                                    $remDetail = $this->HsseRemidial->find(
                                        "all",
                                        [
                                            "conditions" => [
                                                "HsseRemidial.id" =>
                                                    $explode_remedila_action_id[
                                                        $k
                                                    ],
                                                "HsseRemidial.isdeleted" => "N",
                                                "HsseRemidial.isblocked" => "N",
                                            ],
                                        ]
                                    );
                                    $incidentdetailHolder[$i]["rem_val"][$v][
                                        $k
                                    ] =
                                        $remDetail[0]["HsseRemidial"][
                                            "remidial_summery"
                                        ];
                                    $incidentdetailHolder[$i]["rem_val_id"][$v][
                                        $k
                                    ] = $remDetail[0]["HsseRemidial"]["id"];
                                }
                            }
                        }

                        $incidentdetailHolder[$i]["view"][] = "yes";
                    }
                } else {
                    $incidentdetailHolder[$i]["view"][] = "no";
                }
            }
        }

        //print_r($incidentdetailHolder);

        $this->set("incidentdetailHolder", $incidentdetailHolder);

        /*************Incident - Personnel****************************/
        $personeldetail = $this->HssePersonnel->find("all", [
            "conditions" => [
                "HssePersonnel.report_id" => base64_decode($id),
                "HssePersonnel.isdeleted" => "N",
                "HssePersonnel.isblocked" => "N",
            ],
        ]);
        if (count($personeldetail) > 0) {
            $this->set("personeldata", 1);
            for ($i = 0; $i < count($personeldetail); $i++) {
                $user_detail = $this->AdminMaster->find("all", [
                    "conditions" => [
                        "AdminMaster.id" =>
                            $personeldetail[$i]["HssePersonnel"][
                                "personal_data"
                            ],
                        "AdminMaster.isblocked" => "N",
                        "AdminMaster.isdeleted" => "N",
                    ],
                ]);
                if (count($user_detail) > 0) {
                    $seniorty = explode(
                        " ",
                        $user_detail[0]["AdminMaster"]["created"]
                    );
                    $snr = explode("-", $seniorty[0]);
                    $snrdt = $snr[2] . "/" . $snr[1] . "/" . $snr[0];
                    $personeldetail[$i]["HssePersonnel"]["seniorty"] = $snrdt;
                    $personeldetail[$i]["HssePersonnel"]["name"] =
                        $user_detail[0]["AdminMaster"]["first_name"] .
                        "  " .
                        $user_detail[0]["AdminMaster"]["last_name"];
                    $personeldetail[$i]["HssePersonnel"]["position"] =
                        $user_detail[0]["RoleMaster"]["role_name"];
                }
            }
            $this->set("personeldetail", $personeldetail);
        } else {
            $this->set("personeldata", 0);
        }
        /*************Attachments****************************/
        $attachmentData = $this->HsseAttachment->find("all", [
            "conditions" => [
                "HsseAttachment.report_id" => base64_decode($id),
                "HsseAttachment.isdeleted" => "N",
                "HsseAttachment.isblocked" => "N",
            ],
        ]);
        if (count($attachmentData) > 0) {
            $this->set("attachmentData", $attachmentData);
            $this->set("attachmentTab", 1);
        } else {
            $this->set("attachmentData", "");
            $this->set("attachmentTab", 0);
        }
        /***********REMIDIAL ACTION****************************/
        $remidialdetail = $this->HsseRemidial->find("all", [
            "conditions" => [
                "HsseRemidial.report_no" => base64_decode($id),
                "HsseRemidial.isblocked" => "N",
                "HsseRemidial.isdeleted" => "N",
            ],
        ]);
        if (count($remidialdetail) > 0) {
            $this->set("remidial", 1);
            for ($i = 0; $i < count($remidialdetail); $i++) {
                $user_detail_createby = $this->AdminMaster->find("all", [
                    "conditions" => [
                        "AdminMaster.id" =>
                            $remidialdetail[$i]["HsseRemidial"][
                                "remidial_createby"
                            ],
                    ],
                ]);
                $remidialdetail[$i]["HsseRemidial"]["remidial_createby"] =
                    $user_detail_createby[0]["AdminMaster"]["first_name"] .
                    "  " .
                    $user_detail_createby[0]["AdminMaster"]["last_name"];
                $user_detail_reponsibilty = $this->AdminMaster->find("all", [
                    "conditions" => [
                        "AdminMaster.id" =>
                            $remidialdetail[$i]["HsseRemidial"][
                                "remidial_responsibility"
                            ],
                    ],
                ]);
                $remidialdetail[$i]["HsseRemidial"]["remidial_responsibility"] =
                    $user_detail_reponsibilty[0]["AdminMaster"]["first_name"] .
                    "  " .
                    $user_detail_reponsibilty[0]["AdminMaster"]["last_name"];
                $priority_detail = $this->Priority->find("all", [
                    "conditions" => [
                        "Priority.id" =>
                            $remidialdetail[$i]["HsseRemidial"][
                                "remidial_priority"
                            ],
                    ],
                ]);
                $remidialdetail[$i]["HsseRemidial"]["priority"] =
                    $priority_detail[0]["Priority"]["type"];
                $remidialdetail[$i]["HsseRemidial"]["priority_color"] =
                    'style="background-color:' .
                    $priority_detail[0]["Priority"]["colorcoder"] .
                    '"';
                $lastupdated = explode(
                    " ",
                    $remidialdetail[$i]["HsseRemidial"]["modified"]
                );
                $lastupdatedate = explode("-", $lastupdated[0]);
                $remidialdetail[$i]["HsseRemidial"]["lastupdate"] =
                    $lastupdatedate[1] .
                    "/" .
                    $lastupdatedate[2] .
                    "/" .
                    $lastupdatedate[0];
                $createdate = explode(
                    "-",
                    $remidialdetail[$i]["HsseRemidial"]["remidial_create"]
                );
                $remidialdetail[$i]["HsseRemidial"]["createRemidial"] = date(
                    "d-M-y",
                    mktime(
                        0,
                        0,
                        0,
                        $createdate[1],
                        $createdate[2],
                        $createdate[0]
                    )
                );

                if (
                    $remidialdetail[$i]["HsseRemidial"][
                        "remidial_closure_date"
                    ] == "0000-00-00"
                ) {
                    $closerDate = explode(
                        "-",
                        $remidialdetail[$i]["HsseRemidial"][
                            "remidial_closure_date"
                        ]
                    );
                    $remidialdetail[$i]["HsseRemidial"]["closeDate"] = "";
                    $remidialdetail[$i]["HsseRemidial"][
                        "remidial_closer_summary"
                    ] = "";
                } elseif (
                    $remidialdetail[$i]["HsseRemidial"][
                        "remidial_closure_date"
                    ] != "0000-00-00"
                ) {
                    $closerDate = explode(
                        "-",
                        $remidialdetail[$i]["HsseRemidial"][
                            "remidial_closure_date"
                        ]
                    );
                    $remidialdetail[$i]["HsseRemidial"]["closeDate"] = date(
                        "d-M-y",
                        mktime(
                            0,
                            0,
                            0,
                            $closerDate[1],
                            $closerDate[2],
                            $closerDate[0]
                        )
                    );
                }
            }

            $this->set("remidialdetail", $remidialdetail);
        } else {
            $this->set("remidialdetail", []);
            $this->set("remidial", 0);
        }

        /****************Investigation Team******************/
        $invetigationDetail = $this->HsseInvestigation->find("all", [
            "conditions" => [
                "HsseInvestigation.report_id" => base64_decode($id),
            ],
        ]);
        if (count($invetigationDetail)) {
            $condition =
                "AdminMaster.isblocked = 'N' AND AdminMaster.isdeleted = 'N' AND AdminMaster.id IN (" .
                $invetigationDetail[0]["HsseInvestigation"]["team_user_id"] .
                ")";
            $investigation_team = $this->AdminMaster->find("all", [
                "conditions" => $condition,
            ]);
            $this->set("invetigationDetail", $invetigationDetail);
            $this->set("investigation_team", $investigation_team);
        } else {
            $this->set("investigation_team", []);
        }

        /****************Incident Investigation******************/
        $incidentInvestigationDetail = $this->HsseInvestigationData->find(
            "all",
            [
                "conditions" => [
                    "HsseInvestigationData.report_id" => base64_decode($id),
                    "HsseInvestigationData.isdeleted" => "N",
                    "HsseInvestigationData.isblocked" => "N",
                ],
                "recursive" => 2,
            ]
        );

        if (count($incidentInvestigationDetail) > 0) {
            for ($i = 0; $i < count($incidentInvestigationDetail); $i++) {
                $incidentDetail = $this->HsseIncident->find("all", [
                    "conditions" => [
                        "HsseIncident.id" =>
                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["incident_id"],
                    ],
                ]);
                $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                    "incident_summary"
                ] = $incidentDetail[0]["HsseIncident"]["incident_summary"];
                $lossDetail = $this->Losses->find("all", [
                    "conditions" => [
                        "id" =>
                            $incidentDetail[0]["HsseIncident"]["incident_loss"],
                    ],
                ]);
                $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                    "loss"
                ] = $lossDetail[0]["Loss"]["type"];
                $immidiate_cause = explode(
                    ",",
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "immediate_cause"
                    ]
                );

                if ($immidiate_cause[0] != 0 || $immidiate_cause[0] != "") {
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "imd_cause"
                    ] = $this->ImmediateCauses->find("all", [
                        "conditions" => [
                            "ImmediateCauses.id" => $immidiate_cause[0],
                        ],
                    ]);
                }

                if ($immidiate_cause[1] != 0 || $immidiate_cause[1] != "") {
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "imd_sub_cause"
                    ] = $this->ImmediateSubCause->find("all", [
                        "conditions" => [
                            "ImmediateSubCause.id" => $immidiate_cause[1],
                        ],
                    ]);
                }

                if (
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "root_cause_id"
                    ] != 0
                ) {
                    $explode_rootcause = explode(
                        ",",
                        $incidentInvestigationDetail[$i][
                            "HsseInvestigationData"
                        ]["root_cause_id"]
                    );

                    for ($j = 0; $j < count($explode_rootcause); $j++) {
                        if ($explode_rootcause[$j] != 0) {
                            $rootCauseDetail = $this->RootCause->find("all", [
                                "conditions" => [
                                    "RootCause.id" => $explode_rootcause[$j],
                                ],
                            ]);
                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["root_cause_val"][$j] =
                                $rootCauseDetail[0]["RootCause"]["type"];
                        }
                    }
                }

                if (
                    $incidentInvestigationDetail[$i]["HsseInvestigationData"][
                        "remedila_action_id"
                    ] != ""
                ) {
                    $explode_remedila_action_id = explode(
                        ",",
                        $incidentInvestigationDetail[$i][
                            "HsseInvestigationData"
                        ]["remedila_action_id"]
                    );

                    for (
                        $k = 0;
                        $k < count($explode_remedila_action_id);
                        $k++
                    ) {
                        if (isset($explode_remedila_action_id[$k])) {
                            $remDetail = $this->HsseRemidial->find("all", [
                                "conditions" => [
                                    "HsseRemidial.id" =>
                                        $explode_remedila_action_id[$k],
                                    "HsseRemidial.isblocked" => "N",
                                    "HsseRemidial.isdeleted" => "N",
                                ],
                            ]);

                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["rem_val"][$k] =
                                $remDetail[0]["HsseRemidial"][
                                    "remidial_summery"
                                ];

                            $incidentInvestigationDetail[$i][
                                "HsseInvestigationData"
                            ]["rem_val_id"][$k] =
                                $remDetail[0]["HsseRemidial"]["id"];
                        }
                    }
                }
            }
            $this->set(
                "incidentInvestigationDetail",
                $incidentInvestigationDetail
            );
        } else {
            $this->set("incidentInvestigationDetail", []);
        }
        /****************Client Feedback******************/

        if (count($clientdetail) > 0) {
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                $clientfeeddetail = $this->HsseClientfeedback->find("all", [
                    "conditions" => [
                        "HsseClientfeedback.report_id" => base64_decode($id),
                    ],
                ]);

                if (count($clientfeeddetail) > 0) {
                    if (
                        $clientfeeddetail[0]["HsseClientfeedback"][
                            "client_summary"
                        ] != ""
                    ) {
                        $this->set(
                            "clientfeedback_summary",
                            $clientfeeddetail[0]["HsseClientfeedback"][
                                "client_summary"
                            ]
                        );
                    } else {
                        $this->set("clientfeedback_summary", "");
                    }

                    if (
                        $clientfeeddetail[0]["HsseClientfeedback"][
                            "close_date"
                        ] != "0000-00-00"
                    ) {
                        $clientfeeddate = explode(
                            "-",
                            $clientfeeddetail[0]["HsseClientfeedback"][
                                "close_date"
                            ]
                        );
                        $clsdt = date(
                            "d-M-y",
                            mktime(
                                0,
                                0,
                                0,
                                $clientfeeddate[1],
                                $clientfeeddate[2],
                                $clientfeeddate[0]
                            )
                        );
                        $this->set("feedback_date", $clsdt);
                    } else {
                        $this->set("feedback_date", "");
                    }
                } else {
                    $this->set("clientfeedback_summary", "");
                    $this->set("feedback_date", "");
                }
            }
        }
    }

    public function linkrocess(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->autoRender = false;
        
        $data = $this->request->getData();
        $explodeIdType = explode('_', $data['type']);
        
        $existingLink = $this->HsseLink->find()
            ->where([
                'HsseLink.report_id' => base64_decode($data['report_no']),
                'HsseLink.link_report_id' => $explodeIdType[1],
                'HsseLink.type' => $explodeIdType[0]
            ])
            ->first();
            
        if ($existingLink) {
            echo 'avl';
        } else {
            $session = $this->request->getSession();
            $adminData = $session->read('adminData');
            
            $linkData = [
                'type' => $explodeIdType[0],
                'report_id' => base64_decode($data['report_no']),
                'link_report_id' => $explodeIdType[1],
                'user_id' => $adminData->id
            ];
            
            $link = $this->HsseLink->newEntity($linkData);
            
            if ($this->HsseLink->save($link)) {
                echo 'ok';
            } else {
                echo 'fail';
            }
        }
    }

    public function report_hsse_link_list($id = null, $typSearch)
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->hsse_client_tab();
        $this->layout = "after_adminlogin_template";
        $sqlinkDetail = $this->HsseLink->find("all", [
            "conditions" => ["HsseLink.report_id" => base64_decode($id)],
        ]);
        $reportdetail = $this->Report->find("all", [
            "conditions" => ["Report.id" => base64_decode($id)],
        ]);
        $this->set("report_number", $reportdetail[0]["Report"]["report_no"]);
        $this->set("report_id", $id);
        $this->set("id", base64_decode($id));
        $this->set("report_type", "all");
        $reportdetail = $this->Report->find("all", [
            "conditions" => ["Report.id" => base64_decode($id)],
        ]);
        $this->set("report_id_val", $id);
        $this->AdminMaster->recursive = 2;
        $userDeatil = $this->AdminMaster->find("all", [
            "conditions" => [
                "AdminMaster.id" => $_SESSION["adminData"]["AdminMaster"]["id"],
            ],
        ]);
        $clientdetail = $this->HsseClient->find("all", [
            "conditions" => ["HsseClient.report_id" => base64_decode($id)],
        ]);

        if (count($clientdetail) > 0) {
            if ($clientdetail[0]["HsseClient"]["clientreviewed"] == 3) {
                $this->set("client_feedback", 1);
            } elseif ($clientdetail[0]["HsseClient"]["clientreviewed"] != 3) {
                $this->set("client_feedback", 0);
            }
        } else {
            $this->set("client_feedback", 0);
        }

        $reportDeatil = $this->derive_link_data($userDeatil);
        $this->set("reportDeatil", $reportDeatil);

        if (!empty($this->request->data)) {
            //$action = $this->request->data['HssePersonnel']['action'];
            //$this->set('action',$action);
            //$limit = $this->request->data['HssePersonnel']['limit'];
            //$this->set('limit', $limit);
        } else {
            $action = "all";
            $this->set("action", "all");
            $limit = 50;
            $this->set("limit", $limit);
        }
        $this->set("typSearch", base64_decode($typSearch));
        $this->Session->write("action", $action);
        $this->Session->write("limit", $limit);

        unset($_SESSION["filter"]);
        unset($_SESSION["value"]);
    }

    public function get_all_link_list($report_id, $filterTYPE)
    {
        Configure::write("debug", "2");
        $this->layout = "ajax";
        $this->_checkAdminSession();
        $condition = "";

        $condition = "HsseLink.report_id =" . base64_decode($report_id);

        if (isset($_REQUEST["filter"])) {
            $link_type = explode("~", $this->link_search($_REQUEST["value"]));

            $condition .=
                " AND HsseLink.link_report_id ='" .
                $link_type[0] .
                "' AND HsseLink.type ='" .
                $link_type[1] .
                "'";
        }

        $limit = null;
        if ($_REQUEST["limit"] == "all") {
        } else {
            $limit = $_REQUEST["start"] . ", " . $_REQUEST["limit"];
        }

        $adminArray = [];

        if ($filterTYPE != "all") {
            $condition .= " AND HsseLink.type ='" . $filterTYPE . "'";
        }

        $count = $this->HsseLink->find("count", ["conditions" => $condition]);
        $adminA = $this->HsseLink->find("all", [
            "conditions" => $condition,
            "order" => "HsseLink.id DESC",
            "limit" => $limit,
        ]);

        $i = 0;
        foreach ($adminA as $rec) {
            if ($rec["HsseLink"]["isblocked"] == "N") {
                $adminA[$i]["HsseLink"]["blockHideIndex"] = "true";
                $adminA[$i]["HsseLink"]["unblockHideIndex"] = "false";
                $adminA[$i]["HsseLink"]["isdeletdHideIndex"] = "true";
            } else {
                $adminA[$i]["HsseLink"]["blockHideIndex"] = "false";
                $adminA[$i]["HsseLink"]["unblockHideIndex"] = "true";
                $adminA[$i]["HsseLink"]["isdeletdHideIndex"] = "false";
            }

            //$adminA[$i]['HsseLink']['link_report_no']=$this->link_grid($adminA[$i],$rec['HsseLink']['type'],'HsseLink',$rec);

            $link_type = $this->link_grid(
                $adminA[$i],
                $rec["HsseLink"]["type"],
                "HsseLink",
                $rec
            );
            $explode_link_type = explode("~", $link_type);
            $adminA[$i]["HsseLink"]["link_report_no"] = $explode_link_type[0];
            $adminA[$i]["HsseLink"]["type_name"] = $explode_link_type[1];
            $i++;
        }

        if ($count == 0) {
            $adminArray = [];
        } else {
            $adminArray = Set::extract($adminA, "{n}.HsseLink");
        }

        $this->set("total", $count); //send total to the view
        $this->set("admins", $adminArray); //send products to the view
    }

    public function linkBlock($id = null): void
    {
        $this->_checkAdminSession();
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $linkId) {
            $link = $this->HsseLink->get($linkId);
            $link->isblocked = 'Y';
            $this->HsseLink->save($link);
        }
        
        echo 'ok';
    }

    public function linkUnblock($id = null): void
    {
        $this->_checkAdminSession();
        $this->request->allowMethod(['post', 'get']);
        $this->autoRender = false;
        
        if (!$id) {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
        
        $idArray = explode('^', $id);
        foreach ($idArray as $linkId) {
            $link = $this->HsseLink->get($linkId);
            $link->isblocked = 'N';
            $this->HsseLink->save($link);
        }
        
        echo 'ok';
    }

    public function linkDelete(): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');
        $this->_checkAdminSession();
        $this->autoRender = false;
        
        $data = $this->request->getData();
        
        if (!empty($data['id'])) {
            $idArray = explode('^', $data['id']);
            $connection = $this->HsseLink->getConnection();
            
            foreach ($idArray as $id) {
                $connection->execute('DELETE FROM hsse_links WHERE id = ?', [$id]);
            }
            
            echo 'ok';
        } else {
            $this->redirect(['action' => 'reportHsseList']);
            return;
        }
    }


}
?>

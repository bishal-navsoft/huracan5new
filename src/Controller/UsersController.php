<?php
declare(strict_types=1);

/**
 * (Created by DEBI PRASAD SAHOO)
 * Converted to CakePHP 5.2
 */

namespace App\Controller;

use Cake\Http\Response;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{



    // User Listing method for admin section start by Piyanwita Dey
    public function userList(): ?Response
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->grid_access();
        $this->viewBuilder()->setLayout('after_adminlogin_template');
        
        $data = $this->request->getData();
        if (!empty($data)) {
            $action = $data['User']['action'] ?? 'all';
            $this->set('action', $action);
            $limit = $data['User']['limit'] ?? 10;
            $this->set('limit', $limit);
        } else {
            $action = 'all';
            $this->set('action', 'all');
            $limit = 10;
            $this->set('limit', $limit);
        }
        
        $session = $this->request->getSession();
        $session->write('action', $action);
        $session->write('limit', $limit);
        $session->delete('filter');
        $session->delete('value');
        
        return null;
    }
	
    public function getAllUser(string $action = 'all'): ?Response
    {
        $this->viewBuilder()->setLayout('ajax');
        
        $conditions = ['AdminMasters.isdeleted' => 'N'];
        
        switch ($action) {
            case 'all':
                break;
            case 'unblocked':
                $conditions['AdminMasters.isblocked'] = 'N';
                break;
            case 'blocked':
                $conditions['AdminMasters.isblocked'] = 'Y';
                break;
        }
        
        $conditions['AdminMasters.role_master_id'] = 50;
        
        $session = $this->request->getSession();
        $request = $this->request;
        
        $filter = $request->getQuery('filter') ?? $request->getData('filter');
        $value = $request->getQuery('value') ?? $request->getData('value');
        
        if ($filter && $value) {
            $session->write('filter', $filter);
            $session->write('value', $value);
            $conditions['UPPER(AdminMasters.' . $filter . ') LIKE'] = strtoupper(trim($value)) . '%';
        } elseif ($session->read('filter') && $session->read('value')) {
            $filter = $session->read('filter');
            $value = $session->read('value');
            $conditions['UPPER(AdminMasters.' . $filter . ') LIKE'] = strtoupper(trim($value)) . '%';
        }
        
        $this->loadModel('AdminMasters');
        $this->loadModel('Teams');
        $this->loadModel('Divisions');
        
        $query = $this->AdminMasters->find()
            ->where($conditions)
            ->order(['AdminMasters.id' => 'DESC']);
            
        $count = $query->count();
        
        $limitParam = $request->getQuery('limit') ?? $request->getData('limit');
        if ($limitParam !== 'all') {
            $start = (int)($request->getQuery('start') ?? $request->getData('start') ?? 0);
            $limit = (int)($limitParam ?? 10);
            $query->limit($limit)->offset($start);
        }
        
        $adminA = $query->toArray();
        
        // Process admin data
        foreach ($adminA as $i => $rec) {
            $adminA[$i]['blockHideIndex'] = $rec['isblocked'] === 'N' ? 'true' : 'false';
            $adminA[$i]['unblockHideIndex'] = $rec['isblocked'] === 'N' ? 'false' : 'true';
            $adminA[$i]['name'] = $rec['first_name'] . ' ' . $rec['last_name'];
            
            // Get team names
            $teamVal = '';
            if (!empty($rec['team_id'])) {
                $teamIds = explode(',', $rec['team_id']);
                $teams = $this->Teams->find()
                    ->where(['id IN' => $teamIds])
                    ->select(['team_name'])
                    ->toArray();
                    
                $teamNames = [];
                foreach ($teams as $team) {
                    $teamNames[] = $team['team_name'];
                }
                $adminA[$i]['team'] = implode(', ', $teamNames);
            } else {
                $adminA[$i]['team'] = '';
            }
            
            // Get division name
            if (!empty($rec['division_id'])) {
                $division = $this->Divisions->find()
                    ->where(['id' => $rec['division_id']])
                    ->first();
                if ($division) {
                    $adminA[$i]['division_name'] = $division['division_name'];
                }
            }
        }
        
        $this->set('total', $count);
        $this->set('admins', $adminA);
        $this->set('status', $action);
        
        return null;
    }




	
    public function addUser(?int $id = null): ?Response
    {
        $this->_checkAdminSession();
        $this->_getRoleMenuPermission();
        $this->viewBuilder()->setLayout('after_adminlogin_template');
        
        $this->loadModel('AdminMasters');
        $this->loadModel('Divisions');
        $this->loadModel('Teams');
        
        if ($id === null) {
            // Add new user
            $divisionlist = $this->Divisions->find('all')->toArray();
            $this->set('divisionlist', $divisionlist);
            
            $teamlist = $this->Teams->find('all')
                ->where(['division_id' => 1])
                ->toArray();
            $this->set('teamlist', $teamlist);
            
            $this->set([
                'id' => '0',
                'division_name' => '',
                'heading' => 'Add User',
                'team_name' => '',
                'user_name' => '',
                'password' => '',
                'fname' => '',
                'lname' => '',
                'email' => '',
                'phone' => '',
                'division_id' => '',
                'team_id' => '',
                'hteamid' => '',
                'teamID' => [],
                'userButton' => 'Add',
                'hid' => count($teamlist) > 0 ? $teamlist[0]['id'] : 0
            ]);
        } else {
            // Edit existing user
            $userDetail = $this->AdminMasters->get($id);
            
            $divisionlist = $this->Divisions->find('all')
                ->where(['id' => $userDetail['division_id']])
                ->toArray();
            $this->set('divisionlist', $divisionlist);
            
            $divisionDetail = $this->Divisions->get($userDetail['division_id']);
            
            $teamlist = $this->Teams->find('all')
                ->where(['division_id' => $userDetail['division_id']])
                ->toArray();
            
            $teamID = !empty($userDetail['team_id']) ? explode(',', $userDetail['team_id']) : [];
            $tid = implode(',', $teamID);
            
            $this->set([
                'teamID' => $teamID,
                'teamlist' => $teamlist,
                'id' => $id,
                'division_id' => $userDetail['division_id'],
                'division_name' => $divisionDetail['division_name'],
                'heading' => 'Edit User',
                'team_name' => '',
                'user_name' => $userDetail['admin_user'],
                'password' => $userDetail['admin_pass'],
                'fname' => $userDetail['first_name'],
                'lname' => $userDetail['last_name'],
                'email' => $userDetail['admin_email'],
                'phone' => $userDetail['phone'],
                'hteamid' => $tid,
                'userButton' => 'Update'
            ]);
        }
        
        return null;
    }
    public function displayteam(): ?Response
    {
        $this->viewBuilder()->setLayout('ajax');
        
        $data = $this->request->getData();
        if (!empty($data)) {
            $this->loadModel('Teams');
            $teamlist = $this->Teams->find('all')
                ->where(['division_id' => $data['id']])
                ->toArray();
                
            if (count($teamlist) > 0) {
                $this->set('teamlist', $teamlist);
            }
        }
        
        return null;
    }
    public function userprocess(): Response
    {
        $this->autoRender = false;
        $data = $this->request->getData();
        
        $this->loadModel('AdminMasters');
        
        if ($data['add_user_form']['id'] == 0) {
            // Add new user
            $existingUser = $this->AdminMasters->find()
                ->where(['admin_user' => $data['add_user_form']['user_name']])
                ->first();
                
            if (!$existingUser) {
                $userData = [
                    'division_id' => $data['division_name'],
                    'role_master_id' => 50,
                    'admin_user' => $data['add_user_form']['user_name'],
                    'first_name' => $data['add_user_form']['fname'],
                    'last_name' => $data['add_user_form']['lname'],
                    'admin_pass' => password_hash($data['add_user_form']['password'], PASSWORD_DEFAULT),
                    'admin_email' => $data['add_user_form']['email'],
                    'phone' => $data['add_user_form']['phone']
                ];
                
                $user = $this->AdminMasters->newEntity($userData);
                if ($this->AdminMasters->save($user)) {
                    return $this->response->withStringBody('add');
                } else {
                    return $this->response->withStringBody('fail');
                }
            } else {
                return $this->response->withStringBody('avl');
            }
        } else {
            // Update existing user
            $existingUser = $this->AdminMasters->find()
                ->where([
                    'admin_user' => $data['add_user_form']['user_name'],
                    'id !=' => $data['add_user_form']['id']
                ])
                ->first();
                
            if (!$existingUser) {
                $user = $this->AdminMasters->get($data['add_user_form']['id']);
                
                $userData = [
                    'division_id' => $data['division_name'],
                    'role_master_id' => 50,
                    'admin_user' => $data['add_user_form']['user_name'],
                    'first_name' => $data['add_user_form']['fname'],
                    'last_name' => $data['add_user_form']['lname'],
                    'admin_email' => $data['add_user_form']['email'],
                    'phone' => $data['add_user_form']['phone']
                ];
                
                if (!empty($data['add_user_form']['password'])) {
                    $userData['admin_pass'] = password_hash($data['add_user_form']['password'], PASSWORD_DEFAULT);
                }
                
                $user = $this->AdminMasters->patchEntity($user, $userData);
                if ($this->AdminMasters->save($user)) {
                    return $this->response->withStringBody('update');
                } else {
                    return $this->response->withStringBody('fail');
                }
            } else {
                return $this->response->withStringBody('avl');
            }
        }
    }
	
	
	
	
	
    public function block(?string $id = null): Response
    {
        $this->autoRender = false;
        
        if (!$id) {
            $this->Flash->error('Invalid id for admin');
            return $this->redirect(['action' => 'userList']);
        }
        
        $this->loadModel('AdminMasters');
        $idArray = explode('^', $id);
        
        foreach ($idArray as $userId) {
            if (!empty($userId)) {
                $user = $this->AdminMasters->get($userId);
                $user->isblocked = 'Y';
                $this->AdminMasters->save($user);
            }
        }
        
        return $this->response->withStringBody('success');
    }
    
    public function unblock(?string $id = null): Response
    {
        $this->autoRender = false;
        
        if (!$id) {
            $this->Flash->error('Invalid id for admin');
            return $this->redirect(['action' => 'userList']);
        }
        
        $this->loadModel('AdminMasters');
        $idArray = explode('^', $id);
        
        foreach ($idArray as $userId) {
            if (!empty($userId)) {
                $user = $this->AdminMasters->get($userId);
                $user->isblocked = 'N';
                $this->AdminMasters->save($user);
            }
        }
        
        return $this->response->withStringBody('success');
    }
    
    public function delete(?string $id = null): Response
    {
        $this->autoRender = false;
        
        if (!$id) {
            $this->Flash->error('Invalid id for admin');
            return $this->redirect(['action' => 'userList']);
        }
        
        $this->loadModel('AdminMasters');
        $idArray = explode('^', $id);
        
        foreach ($idArray as $userId) {
            if (!empty($userId)) {
                $user = $this->AdminMasters->get($userId);
                $user->isdeleted = 'Y';
                $this->AdminMasters->save($user);
            }
        }
        
        return $this->response->withStringBody('success');
    }
}

<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class ManagementController extends AppController
{

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Global');
    }
    
   public function isAuthorized($user)
    {
        parent::isAuthorized($user);
        $this->Auth->allow();
            return true;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        
        $this->viewBuilder()->setLayout('admin');
    }

    public function index()
    {
        $this->loadModel('Users');

        $conn = ConnectionManager::get("default"); // name of your database connection
        
        $role_id = $this->Auth->user( 'usertype' );

        $this->set('count_students', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1,
            'soft_delete' => 0
        ]))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Teacher%'), 
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Staff%'),  
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );
        
        $this->loadModel('Activity');
        
        $this->set('countActivity', $this->Activity->find('all')->count());

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%'" ;
        } else {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0";
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc');
        
        //$users = $this->Users->find('all',array('conditions' => ['soft_delete' => 0] ));
        
        $this->set(compact('users', 'search', 'count_students', 'count_teacher', 'count_staff'));

        $this->set('page_title', 'Users Types');

        $this->set('page_icon', '<i class="fal fa-home mr-1"></i>');

    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null) {
        
        $this->loadModel('Users');

        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $conn = ConnectionManager::get("default"); // name of your database connection              
            
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
                
        $before = array(                     
            'usertype' => $user->usertype
        );
       
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $user = $this->Users->patchEntity($user, $this->request->getData());    
            
            $usertype = $this->request->data('usertype');
            
            $user->usertype = implode(",",$usertype);

            $after = array(
                'usertype' => $usertype,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $id
            );
            
            $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'update', $before, $after);
            
            if ($this->Users->save($user)) {
                
                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                
                $this->Flash->success(__('User roles updated successfully.'));
                
                return $this->redirect(['action' => 'edit', $idEncode]);
            }
            $this->Flash->error(__('Changes could not be saved. Please, try again.'));
        }
        
        $userID = $this->Global->userIdEncode($id);

        $roles_data = $conn->execute("SELECT * FROM roles WHERE soft_delete = '0'");

        $rolesDetails = $roles_data->fetchAll('assoc');

//        $this->set('count_students', $this->Users->find('all',array('conditions' => [
//            array('usertype LIKE'=>'%Student%'),
//            'status' => 1,
//            'soft_delete' => 0
//        ]))->count() );
//
//        $this->set('count_teacher', $this->Users->find('all',array('conditions' => [
//            array('usertype LIKE'=>'%Teacher%'),
//            'status' => 1,
//            'soft_delete' => 0
//        ] ))->count() );
//
//        $this->set('count_staff', $this->Users->find('all',array('conditions' => [
//            array('usertype LIKE'=>'%Staff%'),
//            'status' => 1,
//            'soft_delete' => 0
//        ] ))->count() );
                
        $this->set('page_title', 'Edit User Types');

        $this->set('page_icon', '<i class="fal fa-users-crown mr-1"></i>');

        $this->set(compact('user', 'userID', 'rolesDetails', 'count_students', 'count_teacher', 'count_staff'));
    }
}
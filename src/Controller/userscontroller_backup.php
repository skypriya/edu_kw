<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\FrozenDate;
use Cake\Core\Configure;
use Cake\Validation\Validator;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController {

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Global');
    }

    public function isAuthorized($user) {
        parent::isAuthorized($user);
        if (isset($user['usertype']) && $user['usertype'] == 'Admin') {
            $this->Auth->allow();
            return true;
        } elseif (isset($user['usertype'])) {
            $this->Auth->allow('index');
            return true;
        }
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        
        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);
        
        $user_id = $this->Auth->user( 'id' );

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        $users_teacher = array();
        
        if(in_array('Admin',$session_user_role)) {        

            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Student',usertype) > 0 AND `status` = 1" ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Student%' AND `status` = 1";
            }
        } else if(in_array('Teacher',$session_user_role)) {        

            $query = 'SELECT ca.userId as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND s.fk_user_id = "'.$user_id.'"';

            $querySql = $conn->execute($query);

            $query_response = $querySql->fetchAll('assoc');   

            foreach($query_response as $key => $value) {  

                $fk_user_id = $value['id'];

                if(isset($search) && $search != "") {
                    $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND  `id` = '" . $fk_user_id . "' AND `usertype` like '%".$search."%' AND FIND_IN_SET('Student',usertype) > 0 AND `status` = 1" ;
                } else {
                    $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND  `id` = '" . $fk_user_id . "' AND `usertype` like '%Student%' AND `status` = 1";
                }
                $response_data = $conn->execute($query_response);

                $responseDetails = $response_data->fetch('assoc');
                
                $users_teacher[] = $fk_user_id;
            }

            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Student',usertype) > 0 AND `status` = 1" ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Student%' AND `status` = 1";
            }
        } else {
            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Student',usertype) > 0 AND `status` = 1 AND `id` = ".$user_id ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Student%' AND `status` = 1 AND `id` = ".$user_id;
            }
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc');       

        $query_response_students = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Student%' AND `status` = 1";

        $results_students = $conn->execute($query_response_students);

        $usersStudents = $results_students->fetchAll('assoc'); 

        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);
                
        $this->set('page_title', 'Students');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');

        $this->loadModel('Eform');
        $eform_list = $this->Eform->find('all' , array('conditions' => ['soft_delete' => 0], 'order'=>array('id' => 'DESC')))->toArray();

        $this->set(compact('users', 'role_id', 'countries', 'search', 'usersStudents', 'users_teacher','eform_list'));
        
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function recycle() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%".$search."%' ORDER BY created DESC";
        } else {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%Student%' ORDER BY created DESC";
        }

        $results = $conn->execute($query_response);

        $userArray = $results->fetchAll('assoc'); 
        
        $users = array();
        foreach($userArray as $users_values){
            $users[$users_values['id']] = array(
                "id" => $users_values['id'],
                "usertype" => $users_values['usertype'],
                "name" => $users_values['name'],
                "email" => $users_values['email'],
                "mobileNumber" => $users_values['mobileNumber'],
                "akcessId" => $users_values['akcessId'],
                "created" => $users_values['created']
            );            
        }

        $query_delete_response = "SELECT * FROM `users_delete` WHERE `usertype` like '%Student%'";

        $results_delete = $conn->execute($query_delete_response);

        $users_delete = $results_delete->fetchAll('assoc'); 

        $userDeleteArray = array();
        foreach($users_delete as $users_delete_values){
            $userDeleteArray[$users_delete_values['fk_user_id']] = array(
                "id" => $users_delete_values['fk_user_id'],
                "usertype" => $users_delete_values['usertype'],
                "name" => $users_delete_values['name'],
                "email" => $users_delete_values['email'],
                "mobileNumber" => $users_delete_values['mobileNumber'],
                "akcessId" => $users_delete_values['akcessId'],
                "created" => $users_delete_values['created']
            );            
        }

        $users_unique_arr = array_merge($userDeleteArray,$users);

        $users = $this->unique_key($users_unique_arr,'id');

        $users = $this->sortAssociativeArrayByKey($users,'created', "DESC");
                        
        $this->set('page_title', 'Bins');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');
        
        $this->set(compact('users', 'search'));
        
    }    

    function sortAssociativeArrayByKey($array, $key, $direction){

        switch ($direction){
            case "ASC":
                usort($array, function ($first, $second) use ($key) {
                    return $first[$key] <=> $second[$key];
                });
                break;
            case "DESC":
                usort($array, function ($first, $second) use ($key) {
                    return $second[$key] <=> $first[$key];
                });
                break;
            default:
                break;
        }
    
        return $array;
    }
    
    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function restore($idEncode = null) {

        $conn = ConnectionManager::get("default"); // name of your database connection  
        
        $id = $this->Global->userIdDecode($idEncode);

        $checkword = $_SERVER['HTTP_REFERER'];

        $this->request->allowMethod(['post', 'delete']);
        
        $user = $this->Users->get($id);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_user_id' => $id,     
            'soft_delete' => 0
        );
        
        $before = array(
            'soft_delete' => 1
        );

        $usertype_query_string = "";
        $usertype_query_string_message = "";
        if (strpos($checkword, 'staff-recycle') !== false) {
            $usertype_query_string = "Staff";
            $usertype_query_string_message = "Staff";
        } else if (strpos($checkword, 'teacher-recycle') !== false) {
            $usertype_query_string = "Teacher";
            $usertype_query_string_message = "Academic Personnel";
        } else if (strpos($checkword, 'admin-recycle') !== false) {
            $usertype_query_string = "Admin";
            $usertype_query_string_message = "Admin";
        } else if (strpos($checkword, 'recycle') !== false) {
            $usertype_query_string = "Student";
            $usertype_query_string_message = "Student";
        }

        $user->usertype = implode(' ',array_unique(explode(' ', $user->usertype)));        

        if($user->soft_delete == 0){
            $utype = $user->usertype;
        } else {
            $utype = "";
            $arrayType = explode(',', $user->usertype); 
            foreach($arrayType as $arrayTypes) {   
                if(isset($arrayTypes) && $arrayTypes != "") {   
                                   
                    $usertype_data = $conn->execute("SELECT count(*) as count_usertype FROM users_delete WHERE `fk_user_id` = $id AND `usertype` = '".$arrayTypes."'");
                    $usertypeDetails = $usertype_data->fetch('assoc');  
                    
                    if((isset($usertypeDetails['count_usertype']) && $usertypeDetails['count_usertype'] == 0)) { 
                        $usertype = $user->usertype;
                        $id = $user->id;
                        $akcessId = $user->akcessId;
                        $name = $user->name;
                        $email = $user->email;
                        $mobileNumber = $user->mobileNumber;

                        $sql_insert = "INSERT INTO `users_delete` (`fk_user_id`, `akcessId`, `usertype`, `name`, `email`, `mobileNumber`) VALUES ('".$id."','".$akcessId."', '".$arrayTypes."', '".$name."', '".$email."', '".$mobileNumber."')";

                        $conn->execute($sql_insert);
                    }
                }
            }
        }
        
       $utype = $utype . "," .$usertype_query_string;
       $string = implode(',', array_unique(explode(',', $utype)));
        $string = trim($string,",");

        $updateUser_string = $this->Users->updateAll(
            [
                'usertype' => $string
            ], 
            [
                'id' => $id
            ]
        );

        $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'delete', $before, $after);
        
        $updateUser = $this->Users->updateAll(
            [
                'soft_delete' => 0
            ], 
            [
                'id' => $id
            ]
        );        
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);

        $conn->execute("DELETE FROM `users_delete` WHERE `fk_user_id` = $id AND REPLACE (`usertype`, ' ', '' ) = '".$usertype_query_string."'");


        if ($updateUser_string > 0 || $updateUser > 0) {
            $this->Flash->success(__('The ' . $usertype_query_string_message . ' has been restored.'));
        } else {
            $this->Flash->error(__('The ' . $usertype_query_string_message . ' could not be restored.'));
        }

        if (strpos($checkword, 'staff-recycle') !== false)
            return $this->redirect(['action' => 'staffList']);
        else if (strpos($checkword, 'teacher-recycle') !== false)
            return $this->redirect(['action' => 'teacherList']);
        else if (strpos($checkword, 'admin-recycle') !== false)
            return $this->redirect(['action' => 'adminList']);
        else if (strpos($checkword, 'recycle') !== false)
            return $this->redirect(['action' => 'index']);
    }

     /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function adminRecycle() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%".$search."%' ORDER BY created DESC";
        } else {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%Admin%' ORDER BY created DESC";
        }

        $results = $conn->execute($query_response);

        $userArray = $results->fetchAll('assoc'); 
        
        $users = array();
        foreach($userArray as $users_values){
            $users[$users_values['id']] = array(
                "id" => $users_values['id'],
                "usertype" => $users_values['usertype'],
                "name" => $users_values['name'],
                "email" => $users_values['email'],
                "mobileNumber" => $users_values['mobileNumber'],
                "akcessId" => $users_values['akcessId'],
                "created" => $users_values['created']
            );            
        }

        $query_delete_response = "SELECT * FROM `users_delete` WHERE `usertype` like '%Admin%'";

        $results_delete = $conn->execute($query_delete_response);

        $users_delete = $results_delete->fetchAll('assoc'); 

        $userDeleteArray = array();
        foreach($users_delete as $users_delete_values){
            $userDeleteArray[$users_delete_values['fk_user_id']] = array(
                "id" => $users_delete_values['fk_user_id'],
                "usertype" => $users_delete_values['usertype'],
                "name" => $users_delete_values['name'],
                "email" => $users_delete_values['email'],
                "mobileNumber" => $users_delete_values['mobileNumber'],
                "akcessId" => $users_delete_values['akcessId'],
                "created" => $users_delete_values['created']
            );            
        }

        $users_unique_arr = array_merge($userDeleteArray,$users);

        $users = $this->unique_key($users_unique_arr,'id');

        $users = $this->sortAssociativeArrayByKey($users,'created', "DESC");

        $this->set('page_title', 'Bins');

        $this->set('page_icon', '<i class="fal fa-users-crown mr-1"></i>');

        $this->set(compact('users', 'search'));
    }

    function unique_key($array,$keyname){

        $new_array = array();
        foreach($array as $key=>$value){
       
          if(!isset($new_array[$value[$keyname]])){
            $new_array[$value[$keyname]] = $value;
          }
       
        }
        $new_array = array_values($new_array);
        return $new_array;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function staffRecycle() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%".$search."%' ORDER BY created DESC";
        } else {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%Staff%' ORDER BY created DESC";
        }

        $results = $conn->execute($query_response);

        $userArray = $results->fetchAll('assoc'); 
        
        $users = array();
        foreach($userArray as $users_values){
            $users[$users_values['id']] = array(
                "id" => $users_values['id'],
                "usertype" => $users_values['usertype'],
                "name" => $users_values['name'],
                "email" => $users_values['email'],
                "mobileNumber" => $users_values['mobileNumber'],
                "akcessId" => $users_values['akcessId'],
                "created" => $users_values['created']
            );            
        }

        $query_delete_response = "SELECT * FROM `users_delete` WHERE `usertype` like '%Staff%'";

        $results_delete = $conn->execute($query_delete_response);

        $users_delete = $results_delete->fetchAll('assoc'); 

        $userDeleteArray = array();
        foreach($users_delete as $users_delete_values){
            $userDeleteArray[$users_delete_values['fk_user_id']] = array(
                "id" => $users_delete_values['fk_user_id'],
                "usertype" => $users_delete_values['usertype'],
                "name" => $users_delete_values['name'],
                "email" => $users_delete_values['email'],
                "mobileNumber" => $users_delete_values['mobileNumber'],
                "akcessId" => $users_delete_values['akcessId'],
                "created" => $users_delete_values['created']
            );            
        }

        $users_unique_arr = array_merge($userDeleteArray,$users);

        $users = $this->unique_key($users_unique_arr,'id');

        $users = $this->sortAssociativeArrayByKey($users,'created', "DESC");

        $this->set('page_title', 'Bins');
        $this->set('page_icon', '<i class="fal fa-users mr-1"></i>');
        $this->set(compact('users', 'search'));
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function teacherRecycle() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%".$search."%' ORDER BY created DESC";
        } else {
            $query_response = "SELECT `users`.`usertype`,`users`.`id`,`users`.`name`,`users`.`email`,`users`.`mobileNumber`,`users`.`akcessId`,`users`.`created` FROM `users` WHERE `soft_delete` = 1 AND `usertype` like '%Teacher%' ORDER BY created DESC";
        }

        $results = $conn->execute($query_response);

        $userArray = $results->fetchAll('assoc'); 
        
        $users = array();
        foreach($userArray as $users_values){
            $users[$users_values['id']] = array(
                "id" => $users_values['id'],
                "usertype" => $users_values['usertype'],
                "name" => $users_values['name'],
                "email" => $users_values['email'],
                "mobileNumber" => $users_values['mobileNumber'],
                "akcessId" => $users_values['akcessId'],
                "created" => $users_values['created']
            );            
        }

        $query_delete_response = "SELECT * FROM `users_delete` WHERE `usertype` like '%Teacher%'";

        $results_delete = $conn->execute($query_delete_response);

        $users_delete = $results_delete->fetchAll('assoc'); 

        $userDeleteArray = array();
        foreach($users_delete as $users_delete_values){
            $userDeleteArray[$users_delete_values['fk_user_id']] = array(
                "id" => $users_delete_values['fk_user_id'],
                "usertype" => $users_delete_values['usertype'],
                "name" => $users_delete_values['name'],
                "email" => $users_delete_values['email'],
                "mobileNumber" => $users_delete_values['mobileNumber'],
                "akcessId" => $users_delete_values['akcessId'],
                "created" => $users_delete_values['created']
            );            
        }

        $users_unique_arr = array_merge($userDeleteArray,$users);

        $users = $this->unique_key($users_unique_arr,'id');

        $users = $this->sortAssociativeArrayByKey($users,'created', "DESC");

        $this->set('page_title', 'Bins');

        $this->set('page_icon', '<i class="fal fa-chalkboard-teacher mr-1"></i>');

        $this->set(compact('users', 'search'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function adminList() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Admin',usertype) > 0 AND status = 1" ;
        } else {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Admin%' AND `status` = '1'";
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc');

        $this->set('page_title', 'Admin');

        $this->set('page_icon', '<i class="fal fa-users-crown mr-1"></i>');

        $this->loadModel('Countries');
       
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);      

        $this->set(compact('users', 'countries', 'search'));
    }
    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewAdmin($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => []
        ]);
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
            }
        }
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
         
        $userID = $this->Global->userIdEncode($id);
        
        $documentList = $this->Global->getDocumentList();
                
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $created = isset($user['created']) ? $user['created'] : '';
        $modified = isset($user['modified']) ? $user['modified'] : '';
        
        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = isset($companyName) ? $companyName : '';
        $user->address = isset($address) ? $address : '';
        $user->city = isset($city) ? $city : '';
        $user->nationality = isset($country) ? $country : '';
        $user->email = isset($email) ? $email : '';
        $user->mobileNumber = isset($mobileNumber) ? $mobileNumber : '';
        $user->usertype = isset($usertype) ? $usertype : '';
        $user->gender = isset($gender) ? $gender : '';
        $user->dob = isset($dob) && $dob != "" ? $dob : '';
        $user->photo = isset($photo) ? $photo : '';
        $user->otherdetails = isset($otherdetails) ? $otherdetails : '';
        $user->active = isset($active) ? $active : '';
        $user->created = isset($created) && $created != "" ? $created->format("d/m/Y") : '';
        $user->modified = isset($modified) && $modified != "" ? $modified->format("d/m/Y") : '';
        
        $this->loadModel('Countries');
        
        $users = $this->Users->find('all', array('conditions' => [
            array('usertype LIKE'=>'%Admin%'),
            'status' => 1, 
            'soft_delete' => 0
        ]));
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);

        $this->set('page_title', 'View Admin');

        $this->set('page_icon', '<i class="fal fa-users-crown mr-1"></i>');
      
        $this->set(compact('user','userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList', 'users', 'countries'));
    }
    
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addAdmin() {
        
        $user = $this->Users->newEntity();
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $email = $this->request->data('email');      
            $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $emailDetails = $email_data->fetch('assoc');

            if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                //$this->Flash->error(__('This email id is already exists.'));                
                $check_email = 1;
            }

            $mobileNumber = $this->request->data('mobileNumber'); 

            $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $phoneDetails = $phone_data->fetch('assoc');

            if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                //$this->Flash->error(__('This mobile no is already exists. '));                
                $check_mobile = 1;
            }

            $akcessId = $this->request->data('akcessId');   
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');
            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                //$this->Flash->error(__('This AKcess ID is already exists. '));                
                $check_akcess = 1;
            }

        }

        $force_add_user = $this->request->data('force_add_user');
        if ($this->request->is(['patch', 'post', 'put']))
        {
            // Check User role
            $old_user_type_array = array();
            if($force_add_user == 1)
            {
                $akcessId_data = $conn->execute("SELECT id,usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if(!empty($akcessIdDetails) && isset($akcessIdDetails['usertype']) && $akcessIdDetails['usertype'])
                {
                    $ut = $this->request->data('ut');

                    $old_user_type_array = explode(',',$akcessIdDetails['usertype']);
                    if(!in_array($ut,$old_user_type_array))
                    {
                        $check_email  = 0;
                        $check_mobile = 0;
                        $check_akcess = 0;
                        $u_id = $akcessIdDetails['id'];
                        $user = $this->Users->get($u_id, [
                            'conditions' => ['soft_delete' => 0]
                        ]);
                    }
                }
            }


            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                //$this->Flash->error(__('This ' . $append_string . ' is already exists. '));   
                $this->Flash->error(__('This admin already exists. '));
            }
        }
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
                
             
        
        if ($this->request->is('post') && ($check_mobile == 0 && $check_email == 0 && $check_akcess == 0)) {
            
            $user = $this->Users->patchEntity($user, $this->request->getData());       
            $usertype = $this->request->data('ut');
            if($force_add_user == 1 && !empty($old_user_type_array))
            {
                $old_user_type_array[] = $usertype;
                $usertype = implode(',',$old_user_type_array);
            }
            $akcessId = $this->request->data('akcessId');
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->usertype = $usertype;
            $user->loginOpt = 'pin';
            $user->siteStatus = 'Development';
            $user->status = 1;
            
            $result = $this->Users->save($user);
            
            if ($result) {  
                
                $insertedId = $result->id;
                
                $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                
                $updateUsers = $this->Users->updateAll(
                    [
                        'idcardno' => $idcard_randon
                    ], 
                    [
                        'id' => $insertedId
                    ]
                );
               
                $after = array(                    
                    'akcessId' => $akcessId,
                    'idcardno' => $idcardno,
                    'user_id' => $user_id,
                    'role_id' => $role_id,            
                    'name' => $firstname . " " . $lastname,
                    'companyName' => $companyName,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'email' => $email,
                    'mobileNumber' => $mobileNumber,
                    'gender' => $gender,
                    'dob' => $dob,
                    'otherdetails' => $otherdetails,                
                    'active' => $active,
                    'usertype' => $usertype,
                    'loginOpt' => 'pin',
                    'siteStatus' => 'Development',
                    'status' => 1
                );
                
                $this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);
                                
                $this->Flash->success(__('The user has been saved.'));
                
                $idEncode = $this->Global->userIdEncode($insertedId);
                
                return $this->redirect(['action' => 'view-admin', $idEncode]);
            }
           
            $this->Flash->error(__('The user could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $userID = $this->Global->userIdEncode($id);
        
        $this->set('count_students', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Teacher%'),
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );
        
        $this->set('count_admin', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Admin%'),
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );

        $this->set('count_staff', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Staff%'),
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );
        
        $this->set('page_title', 'Add Admin');

        $this->set('page_icon', '<i class="fal fa-users-crown mr-1"></i>');

        $this->set(compact('user', 'countries', 'userID', 'count_students', 'count_teacher', 'count_admin', 'count_staff'));
    }
   
    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editAdmin($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
                
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
                
                return $this->redirect(['action' => 'edit-staff', $idEncode]);
            }
        }
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            if (empty($this->request->getData('photo')) && empty($this->request->getData('photo')['name'])) {
            
                $email = $this->request->data('email');      
                $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $emailDetails = $email_data->fetch('assoc');

                if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                    //$this->Flash->error(__('This email id is already exists. '));                
                    $check_email = 1;
                }

                $mobileNumber = $this->request->data('mobileNumber'); 

                $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $phoneDetails = $phone_data->fetch('assoc');

                if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                    //$this->Flash->error(__('This mobile no is already exists. '));                
                    $check_mobile = 1;
                }

                $akcessId = $this->request->data('akcessId');   
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                    //$this->Flash->error(__('This AKcess ID is already exists. '));                
                    $check_akcess = 1;
                }
            }
            
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                //$this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This admin already exists. '));
                return $this->redirect(['action' => 'edit-staff', $idEncode]);
            }
        }
            
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        
        $before = array(                     
            'akcessId' => $user->akcessId,
            'name' => $user->name,
            'companyName' => $user->companyName,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country,
            'email' => $user->email,
            'mobileNumber' => $user->mobileNumber,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'otherdetails' => $user->otherdetails,
            'active' => $user->active,
        );
       
        if ($this->request->is(['patch', 'post', 'put']) && ($check_email == 0 && $check_mobile == 0 && $check_akcess == 0)) {
            
            
            $user = $this->Users->patchEntity($user, $this->request->getData());    
            
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $akcessId = $this->request->data('akcessId');
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;

            $after = array(
                'akcessId' => $akcessId,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $id,                
                'name' => $firstname . " " . $lastname,
                'companyName' => $companyName,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'email' => $email,
                'mobileNumber' => $mobileNumber,
                'gender' => $gender,
                'dob' => $dob,
                'otherdetails' => $otherdetails,                
                'active' => $active,
            );
            
            $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'update', $before, $after);
            
            if ($this->Users->save($user)) {
                
                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                
                $this->Flash->success(__('Changes saved.'));
                
                return $this->redirect(['action' => 'view-admin', $idEncode]);
            }
            $this->Flash->error(__('Changes could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $documentList = $this->Global->getDocumentList();
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';

        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = $companyName;
        $user->address = $address;
        $user->city = $city;
        $user->nationality = $country;
        $user->email = $email;
        $user->mobileNumber = $mobileNumber;
        $user->usertype = $usertype;
        $user->gender = $gender;
        $user->dob = date('d/m/Y', strtotime($dob));
        $user->photo = $photo;
        $user->otherdetails = $otherdetails;
        $user->active = $active;
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
       
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $userID = $this->Global->userIdEncode($id);
       
        $userss = $this->Users->find('all', array('conditions' => ['status' => 1, 'soft_delete' => 0]));
                
        $this->set('page_title', 'Edit Admin');

        $this->set('page_icon', '<i class="fal fa-users-crown mr-1"></i>');

        $this->set(compact('userss', 'user', 'users', 'countries', 'userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function staffList() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {        

            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Staff',usertype) > 0 AND `status` = 1" ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Staff%' AND `status` = 1";
            }
        } else {
            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND AND FIND_IN_SET('Staff',usertype) > 0 `status` = 1 AND `id` = ".$user_id ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Staff%' AND `status` = 1 AND `id` = ".$user_id;
            }
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc');     

        $this->set('page_title', 'Staff');

        $this->set('page_icon', '<i class="fal fa-users mr-1"></i>');

        $this->loadModel('Countries');
       
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);

        $this->loadModel('Eform');
        $eform_list = $this->Eform->find('all' , array('conditions' => ['soft_delete' => 0], 'order'=>array('id' => 'DESC')))->toArray();

        $this->set(compact('users', 'countries', 'search','eform_list'));
    }
    
    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewStaff($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => []
        ]);
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
            }
        }
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
         
        $userID = $this->Global->userIdEncode($id);
        
        $documentList = $this->Global->getDocumentList();
                
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';
        $created = isset($user['created']) ? $user['created'] : '';
        $modified = isset($user['modified']) ? $user['modified'] : '';
        
        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = isset($companyName) ? $companyName : '';
        $user->address = isset($address) ? $address : '';
        $user->city = isset($city) ? $city : '';
        $user->nationality = isset($country) ? $country : '';
        $user->email = isset($email) ? $email : '';
        $user->mobileNumber = isset($mobileNumber) ? $mobileNumber : '';
        $user->usertype = isset($usertype) ? $usertype : '';
        $user->gender = isset($gender) ? $gender : '';
        $user->dob = isset($dob) && $dob != "" ? $dob : '';
        $user->photo = isset($photo) ? $photo : '';
        $user->otherdetails = isset($otherdetails) ? $otherdetails : '';
        $user->active = isset($active) ? $active : '';
        $user->faculty = isset($faculty) ? $faculty : '';
        $user->courses = isset($courses) ? $courses : '';
        $user->academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : '';
        $user->staff_type = isset($staff_type) ? $staff_type : '';
        $user->created = isset($created) && $created != "" ? $created->format("d/m/Y") : '';
        $user->modified = isset($modified) && $modified != "" ? $modified->format("d/m/Y") : '';
        
        $this->loadModel('Countries');
        
        $users = $this->Users->find('all', array('conditions' => [
            array('usertype LIKE'=>'%Staff%'),
            'status' => 1, 
            'soft_delete' => 0
        ]));
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);

        $this->set('page_title', 'View Staff');

        $this->set('page_icon', '<i class="fal fa-users mr-1"></i>');

        $conn = ConnectionManager::get("default"); // name of your database connection
//        $access_data_res = $conn->execute("SELECT a.id, a.attendance_date_time, s.name as class_name, s.label_type, l.name as location_name FROM attendance as a JOIN sclasses as s on s.id = a.class_id JOIN locations as l on l.id = s.location WHERE a.fk_user_id = ".$id." ORDER BY a.attendance_date_time DESC");
        $access_data_res = $conn->execute("SELECT ca.id, ca.created as attendance_date_time, s.name AS class_name, s.label_type, l.name AS location_name FROM class_attends AS ca JOIN sclasses AS s ON s.id = ca.classId JOIN locations AS l ON l.id = s.location WHERE ca.userId = ".$id." AND s.soft_delete = 0 AND ca.soft_delete = 0 ORDER BY ca.created DESC");

        $access_data_list = $access_data_res->fetchAll('assoc');

        $this->set(compact('user','userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList', 'users', 'countries','access_data_list'));
    }
    
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addStaff() {

        $user = $this->Users->newEntity();
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $email = $this->request->data('email');      
            $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $emailDetails = $email_data->fetch('assoc');

            if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                //$this->Flash->error(__('This email id is already exists. '));                
                //$check_email = 1;
            }

            $mobileNumber = $this->request->data('mobileNumber'); 

            $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $phoneDetails = $phone_data->fetch('assoc');

            if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                //$this->Flash->error(__('This mobile no is already exists. '));                
                //$check_mobile = 1;
            }

            $akcessId = $this->request->data('akcessId');   
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');
            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                //$this->Flash->error(__('This AKcess ID is already exists. '));                
                $check_akcess = 1;
            }

        }

        $force_add_user = $this->request->data('force_add_user');
        if ($this->request->is(['patch', 'post', 'put']))
        {
            // Check User role
            $old_user_type_array = array();
            if($force_add_user == 1)
            {
                $akcessId_data = $conn->execute("SELECT id,usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if(!empty($akcessIdDetails) && isset($akcessIdDetails['usertype']) && $akcessIdDetails['usertype'])
                {
                    $ut = $this->request->data('ut');

                    $old_user_type_array = explode(',',$akcessIdDetails['usertype']);
                    if(!in_array($ut,$old_user_type_array))
                    {
                        $check_akcess = 0;
                        $u_id = $akcessIdDetails['id'];
                        $user = $this->Users->get($u_id, [
                            'conditions' => ['soft_delete' => 0]
                        ]);
                    }
                }
            }

            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
 //                $this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This staff already exists. '));
            }
        }
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
                
             
        
        if ($this->request->is('post') && ($check_mobile == 0 && $check_email == 0 && $check_akcess == 0)) {
            
            $user = $this->Users->patchEntity($user, $this->request->getData());
            
            $usertype = $this->request->data('ut');
            if($force_add_user == 1 && !empty($old_user_type_array))
            {
                $old_user_type_array[] = $usertype;
                $usertype = implode(',',$old_user_type_array);
            }
            $akcessId = $this->request->data('akcessId');
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            $faculty = $this->request->data('faculty');
            $courses = $this->request->data('courses');
            $academic_personal_type = $this->request->data('academic_personal_type');
            $staff_type = $this->request->data('staff_type');
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->faculty = $faculty;
            $user->courses = $courses;
            $user->academic_personal_type = $academic_personal_type;
            $user->staff_type = $staff_type;
            $user->usertype = $usertype;
            $user->loginOpt = 'pin';
            $user->siteStatus = 'Development';
            $user->status = 1;
            
            $result = $this->Users->save($user);
            
            if ($result) {  
                
                $insertedId = $result->id;
                
                $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                
                $updateUsers = $this->Users->updateAll(
                    [
                        'idcardno' => $idcard_randon
                    ], 
                    [
                        'id' => $insertedId
                    ]
                );
               
                $after = array(                    
                    'akcessId' => $akcessId,
                    'idcardno' => $idcardno,
                    'user_id' => $user_id,
                    'role_id' => $role_id,            
                    'name' => $firstname . " " . $lastname,
                    'companyName' => $companyName,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'email' => $email,
                    'mobileNumber' => $mobileNumber,
                    'gender' => $gender,
                    'dob' => $dob,
                    'otherdetails' => $otherdetails,                
                    'active' => $active,
                    'faculty' => $faculty,
                    'courses' => $courses,
                    'academic_personal_type' => $academic_personal_type,
                    'staff_type' => $staff_type,
                    'usertype' => $usertype,
                    'loginOpt' => 'pin',
                    'siteStatus' => 'Development',
                    'status' => 1
                );
                
                $this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);
                                
                $this->Flash->success(__('The user has been saved.'));
                
                $idEncode = $this->Global->userIdEncode($insertedId);
                
                return $this->redirect(['action' => 'view-staff', $idEncode]);
            }
           
            $this->Flash->error(__('The user could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $this->loadModel('Stafflist');

        $this->set('page_title', 'Add Staff');
        $this->set('page_icon', '<i class="fal fa-users mr-1"></i>');
        
        $staffList = $this->Stafflist->find('all', array('conditions' => []));
                
        $userID = $this->Global->userIdEncode($id);
        
        $this->set('count_students', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );

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
        
        $this->set(compact('user', 'countries', 'userID', 'staffList', 'count_students', 'count_teacher', 'count_staff'));
    }
   
    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editStaff($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
                
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
                
                return $this->redirect(['action' => 'edit-staff', $idEncode]);
            }
        }
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            if (empty($this->request->getData('photo')) && empty($this->request->getData('photo')['name'])) {
            
                $email = $this->request->data('email');      
                $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $emailDetails = $email_data->fetch('assoc');

                if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                    //$this->Flash->error(__('This email id is already exists. '));                
                    //$check_email = 1;
                }

                $mobileNumber = $this->request->data('mobileNumber'); 

                $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $phoneDetails = $phone_data->fetch('assoc');

                if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                    //$this->Flash->error(__('This mobile no is already exists. '));                
                    //$check_mobile = 1;
                }

                $akcessId = $this->request->data('akcessId');   
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                    //$this->Flash->error(__('This AKcess ID is already exists. '));                
                    $check_akcess = 1;
                }
            }
            
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                //$this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This staff already exists. '));
                return $this->redirect(['action' => 'edit-staff', $idEncode]);
            }
        }
            
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        
        $before = array(                     
            'akcessId' => $user->akcessId,
            'name' => $user->name,
            'companyName' => $user->companyName,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country,
            'email' => $user->email,
            'mobileNumber' => $user->mobileNumber,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'otherdetails' => $user->otherdetails,
            'active' => $user->active,
            'faculty' => $user->faculty,
            'courses' => $user->courses,
            'academic_personal_type' => $user->academic_personal_type,
            'staff_type' => $user->staff_type,
        );
       
        if ($this->request->is(['patch', 'post', 'put']) && ($check_email == 0 && $check_mobile == 0 && $check_akcess == 0)) {
            
            
            $user = $this->Users->patchEntity($user, $this->request->getData());    
            
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $akcessId = $this->request->data('akcessId');
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            $faculty = $this->request->data('faculty');
            $courses = $this->request->data('courses');
            $academic_personal_type = $this->request->data('academic_personal_type');
            $staff_type = $this->request->data('staff_type');
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->faculty = $faculty;
            $user->courses = $courses;
            $user->academic_personal_type = $academic_personal_type;
            $user->staff_type = $staff_type;
            
            $after = array(
                'akcessId' => $akcessId,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $id,                
                'name' => $firstname . " " . $lastname,
                'companyName' => $companyName,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'email' => $email,
                'mobileNumber' => $mobileNumber,
                'gender' => $gender,
                'dob' => $dob,
                'otherdetails' => $otherdetails,                
                'active' => $active,
                'faculty' => $faculty,
                'courses' => $courses,
                'academic_personal_type' => $academic_personal_type,
                'staff_type' => $staff_type
            );
            
            $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'update', $before, $after);
            
            if ($this->Users->save($user)) {
                
                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                
                $this->Flash->success(__('Changes saved.'));
                
                return $this->redirect(['action' => 'view-staff', $idEncode]);
            }
            $this->Flash->error(__('Changes could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $documentList = $this->Global->getDocumentList();
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';

        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = $companyName;
        $user->address = $address;
        $user->city = $city;
        $user->nationality = $country;
        $user->email = $email;
        $user->mobileNumber = $mobileNumber;
        $user->usertype = $usertype;
        $user->gender = $gender;
        $user->dob = date('d/m/Y', strtotime($dob));
        $user->photo = $photo;
        $user->otherdetails = $otherdetails;
        $user->active = $active;
        $user->faculty = $faculty;
        $user->courses = $courses;
        $user->academic_personal_type = $academic_personal_type;
        $user->staff_type = $staff_type;
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
       
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $this->loadModel('Stafflist');

        $this->set('page_title', 'Edit Staff');

        $this->set('page_icon', '<i class="fal fa-users mr-1"></i>');
        
        $staffList = $this->Stafflist->find('all');  
        
        $userID = $this->Global->userIdEncode($id);
       
        $userss = $this->Users->find('all', array('conditions' => ['status' => 1, 'soft_delete' => 0]));
                
        $this->set(compact('userss', 'user', 'users', 'countries', 'userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'staffList', 'documentList'));
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function teacherList() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {        

            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Teacher',usertype) > 0 AND `status` = 1" ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` 
                WHERE `soft_delete` = 0 AND `usertype` like '%Teacher%' AND `status`= 1";
            }
        } else {
            if(isset($search) && $search != "") {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%".$search."%' AND FIND_IN_SET('Teacher',usertype) > 0 AND `status` = 1 AND `id` = ".$user_id ;
            } else {
                $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Teacher%' AND `status` = 1 AND `id` = ".$user_id;
            }
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc');  

        $this->set('page_title', 'Academic Personnel');

        $this->set('page_icon', '<i class="fal fa-chalkboard-teacher mr-1"></i>');

        $this->loadModel('Countries');
       
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);

        $this->loadModel('Eform');
        $eform_list = $this->Eform->find('all' , array('conditions' => ['soft_delete' => 0], 'order'=>array('id' => 'DESC')))->toArray();

        $this->set(compact('users', 'countries', 'search','eform_list'));
    }
    
    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function viewTeacher($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
            }
        }
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }

        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $userID = $this->Global->userIdEncode($id);
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';
        $created = isset($user['created']) ? $user['created'] : '';
        $modified = isset($user['modified']) ? $user['modified'] : '';
        
        $user->name = $firstname . " " . $lastname;
        $user->idcardno = $idcardno;
        $user->akcessId = $akcessId;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = isset($companyName) ? $companyName : '';
        $user->address = isset($address) ? $address : '';
        $user->city = isset($city) ? $city : '';
        $user->nationality = isset($country) ? $country : '';
        $user->email = isset($email) ? $email : '';
        $user->mobileNumber = isset($mobileNumber) ? $mobileNumber : '';
        $user->usertype = isset($usertype) ? $usertype : '';
        $user->gender = isset($gender) ? $gender : '';
        $user->dob = isset($dob) && $dob != "" ? $dob : '';
        $user->photo = isset($photo) ? $photo : '';
        $user->otherdetails = isset($otherdetails) ? $otherdetails : '';
        $user->active = isset($active) ? $active : '';
        $user->faculty = isset($faculty) ? $faculty : '';
        $user->courses = isset($courses) ? $courses : '';
        $user->academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : '';
        $user->staff_type = isset($staff_type) ? $staff_type : '';
        $user->created = isset($created) && $created != "" ? $created->format("d/m/Y") : '';
        $user->modified = isset($modified) && $modified != "" ? $modified->format("d/m/Y") : '';
        
        $documentList = $this->Global->getDocumentList();
        
        $this->loadModel('Countries');

        $this->set('page_title', 'View Academic Personnel');

        $this->set('page_icon', '<i class="fal fa-chalkboard-teacher mr-1"></i>');
        
        $users = $this->Users->find('all', array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1, 
            'soft_delete' => 0
        ]));
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);
        $conn = ConnectionManager::get("default"); // name of your database connection
//        $access_data_res = $conn->execute("SELECT a.id, a.attendance_date_time, s.name as class_name, s.label_type, l.name as location_name FROM attendance as a JOIN sclasses as s on s.id = a.class_id JOIN locations as l on l.id = s.location WHERE a.fk_user_id = ".$id." ORDER BY a.attendance_date_time DESC");
        $access_data_res = $conn->execute("SELECT ca.id, ca.created as attendance_date_time, s.name AS class_name, s.label_type, l.name AS location_name FROM class_attends AS ca JOIN sclasses AS s ON s.id = ca.classId JOIN locations AS l ON l.id = s.location WHERE ca.userId = ".$id." AND s.soft_delete = 0 AND ca.soft_delete = 0 ORDER BY ca.created DESC");

        $access_data_list = $access_data_res->fetchAll('assoc');

        $this->set(compact('user','userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList', 'users', 'countries','access_data_list'));
    }
        
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addTeacher() {
        
        $user = $this->Users->newEntity();
        
        $conn = ConnectionManager::get("default"); // name of your database connection        
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            $email = $this->request->data('email');      
            $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $emailDetails = $email_data->fetch('assoc');

            if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                //$this->Flash->error(__('This email id is already exists. '));                
                //$check_email = 1;
            }

            $mobileNumber = $this->request->data('mobileNumber'); 

            $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $phoneDetails = $phone_data->fetch('assoc');

            if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                //$this->Flash->error(__('This mobile no is already exists. '));                
                //$check_mobile = 1;
            }

            $akcessId = $this->request->data('akcessId');   
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');
            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                //$this->Flash->error(__('This AKcess ID is already exists. '));                
                $check_akcess = 1;
            }
            
        }

        $force_add_user = $this->request->data('force_add_user');
        if ($this->request->is(['patch', 'post', 'put']))
        {
            // Check User role
            $old_user_type_array = array();
            if($force_add_user == 1)
            {
                $akcessId_data = $conn->execute("SELECT id,usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if(!empty($akcessIdDetails) && isset($akcessIdDetails['usertype']) && $akcessIdDetails['usertype'])
                {
                    $ut = $this->request->data('ut');

                    $old_user_type_array = explode(',',$akcessIdDetails['usertype']);
                    if(!in_array($ut,$old_user_type_array))
                    {
                        $check_akcess = 0;
                        $u_id = $akcessIdDetails['id'];
                        $user = $this->Users->get($u_id, [
                            'conditions' => ['soft_delete' => 0]
                        ]);
                    }
                }
            }


            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
//                $this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This teacher already exists. '));
            }
        }
        
        if ($this->request->is('post') && ($check_mobile == 0 && $check_email == 0 && $check_akcess == 0)) {
            
            $user = $this->Users->patchEntity($user, $this->request->getData());                        
            
            $usertype = $this->request->data('ut');
            if($force_add_user == 1 && !empty($old_user_type_array))
            {
                $old_user_type_array[] = $usertype;
                $usertype = implode(',',$old_user_type_array);
            }


            $akcessId = $this->request->data('akcessId');
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            $faculty = $this->request->data('faculty');
            $courses = $this->request->data('courses');
            $academic_personal_type = $this->request->data('academic_personal_type');
            $staff_type = $this->request->data('staff_type');
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->faculty = $faculty;
            $user->courses = $courses;
            $user->academic_personal_type = $academic_personal_type;
            $user->staff_type = $staff_type;
            $user->usertype = $usertype;
            $user->loginOpt = 'pin';
            $user->siteStatus = 'Development';
            $user->status = 1;
            
            $result = $this->Users->save($user);
            
            if ($result) {  
                
                $insertedId = $result->id;
                
                $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                
                $updateUsers = $this->Users->updateAll(
                    [
                        'idcardno' => $idcard_randon
                    ], 
                    [
                        'id' => $insertedId
                    ]
                );
               
                $after = array(
                    'user_id' => $user_id,
                    'akcessId' => $akcessId,
                    'role_id' => $role_id,            
                    'name' => $firstname . " " . $lastname,
                    'companyName' => $companyName,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'email' => $email,
                    'mobileNumber' => $mobileNumber,
                    'gender' => $gender,
                    'dob' => $dob,
                    'otherdetails' => $otherdetails,                
                    'active' => $active,
                    'faculty' => $faculty,
                    'courses' => $courses,
                    'academic_personal_type' => $academic_personal_type,
                    'staff_type' => $staff_type,
                    'usertype' => $usertype,
                    'loginOpt' => 'pin',
                    'siteStatus' => 'Development',
                    'status' => 1
                );
                
                $this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);
                
                $this->Flash->success(__('The user has been saved.'));
                
                $idEncode = $this->Global->userIdEncode($insertedId);
                
                return $this->redirect(['action' => 'view-teacher', $idEncode]);
            }
            $this->Flash->error(__('The user could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $this->loadModel('Teacherlist');

        $this->set('page_title', 'Add Academic Personnel');
        $this->set('page_icon', '<i class="fal fa-chalkboard-teacher mr-1"></i>');
        
        $academicList = $this->Teacherlist->find('all');  
                
        $userID = $this->Global->userIdEncode($id);
        
        $this->set('count_students', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1, 
            'soft_delete' => 0
            ] ))->count() );

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
        
        $this->set(compact('user', 'countries', 'userID', 'academicList', 'count_students', 'count_teacher', 'count_staff'));
    }
    
    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editTeacher($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $conn = ConnectionManager::get("default"); // name of your database connection        
        
        $user = $this->Users->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);        
        
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
                return $this->redirect(['action' => 'edit-teacher', $idEncode]);
            }
        }
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            if (empty($this->request->getData('photo')) && empty($this->request->getData('photo')['name'])) {
                $email = $this->request->data('email');      
                $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $emailDetails = $email_data->fetch('assoc');

                if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                    //$this->Flash->error(__('This email id is already exists. '));                
                    //$check_email = 1;
                }

                $mobileNumber = $this->request->data('mobileNumber'); 

                $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $phoneDetails = $phone_data->fetch('assoc');

                if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                    //$this->Flash->error(__('This mobile no is already exists. '));                
                    //$check_mobile = 1;
                }

                $akcessId = $this->request->data('akcessId');   
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                    //$this->Flash->error(__('This AKcess ID is already exists. '));                
                    $check_akcess = 1;
                }
            }
            
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                $this->Flash->error(__('This ' . $append_string . ' is already exists. '));  
                $this->Flash->error(__('This teacher already exists. '));   
                return $this->redirect(['action' => 'edit-teacher', $idEncode]);
            }
        }
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $before = array(         
            'akcessId' => $user->akcessId,
            'name' => $user->name,
            'companyName' => $user->companyName,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country,
            'email' => $user->email,
            'mobileNumber' => $user->mobileNumber,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'otherdetails' => $user->otherdetails,
            'active' => $user->active,
            'faculty' => $user->faculty,
            'courses' => $user->courses,
            'academic_personal_type' => $user->academic_personal_type,
            'staff_type' => $user->staff_type,
        );
               
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());                        
            
            $akcessId = $this->request->data('akcessId');
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            $faculty = $this->request->data('faculty');
            $courses = $this->request->data('courses');
            $academic_personal_type = $this->request->data('academic_personal_type');
            $staff_type = $this->request->data('staff_type');
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->faculty = $faculty;
            $user->courses = $courses;
            $user->academic_personal_type = $academic_personal_type;
            $user->staff_type = $staff_type;
            
            $after = array(
                'akcessId' => $akcessId,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $id,                
                'name' => $firstname . " " . $lastname,
                'companyName' => $companyName,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'email' => $email,
                'mobileNumber' => $mobileNumber,
                'gender' => $gender,
                'dob' => $dob,
                'otherdetails' => $otherdetails,                
                'active' => $active,
                'faculty' => $faculty,
                'courses' => $courses,
                'academic_personal_type' => $academic_personal_type,
                'staff_type' => $staff_type
            );
            
            $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'update', $before, $after);
            
            if ($this->Users->save($user)) {
                
                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                
                $this->Flash->success(__('Changes saved.'));
                return $this->redirect(['action' => 'view-teacher', $idEncode]);
            }
            $this->Flash->error(__('Changes could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';

        $user->name = $firstname . " " . $lastname;
        $user->idcardno = $idcardno;
        $user->akcessId = $akcessId;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = $companyName;
        $user->address = $address;
        $user->city = $city;
        $user->nationality = $country;
        $user->email = $email;
        $user->mobileNumber = $mobileNumber;
        $user->usertype = $usertype;
        $user->gender = $gender;
        $user->dob = date('d/m/Y', strtotime($dob));
        $user->photo = $photo;
        $user->otherdetails = $otherdetails;
        $user->active = $active;
        $user->faculty = $faculty;
        $user->courses = $courses;
        $user->academic_personal_type = $academic_personal_type;
        $user->staff_type = $staff_type;
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
       
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $this->loadModel('Teacherlist');

        $this->set('page_title', 'Edit Academic Personnel');
        $this->set('page_icon', '<i class="fal fa-chalkboard-teacher mr-1"></i>');
        
        $academicList = $this->Teacherlist->find('all');  
        
        $userID = $this->Global->userIdEncode($id);
        
        $documentList = $this->Global->getDocumentList();
        
        $userss = $this->Users->find('all', array('conditions' => ['status' => 1, 'soft_delete' => 0]));
        
        $this->set(compact('userss', 'user', 'users', 'countries', 'userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'academicList', 'documentList'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => []
        ]);
        
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
            }
        }
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $userID = $this->Global->userIdEncode($id);
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';
        $created = isset($user['created']) ? $user['created'] : '';
        $modified = isset($user['modified']) ? $user['modified'] : '';
        $adminssion_date = isset($user['adminssion_date']) ? $user['adminssion_date'] : '';
        
        
        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = isset($companyName) ? $companyName : '';
        $user->address = isset($address) ? $address : '';
        $user->city = isset($city) ? $city : '';
        $user->nationality = isset($country) ? $country : '';
        $user->email = isset($email) ? $email : '';
        $user->mobileNumber = isset($mobileNumber) ? $mobileNumber : '';
        $user->usertype = isset($usertype) ? $usertype : '';
        $user->gender = isset($gender) ? $gender : '';
        $user->dob = isset($dob) && $dob != "" ? $dob : '';
        $user->photo = isset($photo) ? $photo : '';
        $user->otherdetails = isset($otherdetails) ? $otherdetails : '';
        $user->active = isset($active) ? $active : '';
        $user->faculty = isset($faculty) ? $faculty : '';
        $user->courses = isset($courses) ? $courses : '';
        $user->academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : '';
        $user->staff_type = isset($staff_type) ? $staff_type : '';
        $user->created = isset($created) && $created != "" ? $created->format("d/m/Y") : '';
        $user->modified = isset($modified) && $modified != "" ? $modified->format("d/m/Y") : '';
        $user->adminssion_date = isset($adminssion_date) && $adminssion_date != "" && $adminssion_date != null  ? $adminssion_date : '';
        
        $documentList = $this->Global->getDocumentList();

                
        $users = $this->Users->find('all', array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1
        ]));

        $this->loadModel('Countries');

        $this->set('page_title', 'View Student');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);
        
        $role_id = $this->Auth->user( 'usertype' );

        $conn = ConnectionManager::get("default"); // name of your database connection
//        $access_data_res = $conn->execute("SELECT a.id, a.attendance_date_time, s.name as class_name, s.label_type, l.name as location_name FROM attendance as a JOIN sclasses as s on s.id = a.class_id JOIN locations as l on l.id = s.location WHERE a.fk_user_id = ".$id." ORDER BY a.attendance_date_time DESC");
        $access_data_res = $conn->execute("SELECT ca.id, ca.created as attendance_date_time, s.name AS class_name, s.label_type, l.name AS location_name FROM class_attends AS ca JOIN sclasses AS s ON s.id = ca.classId JOIN locations AS l ON l.id = s.location WHERE ca.userId = ".$id." AND s.soft_delete = 0 AND ca.soft_delete = 0 ORDER BY ca.created DESC");

        $access_data_list = $access_data_res->fetchAll('assoc');

        $this->set(compact('user','userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList', 'countries', 'users', 'role_id','access_data_list'));
    }
 
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        
        $user = $this->Users->newEntity();
        
        $conn = ConnectionManager::get("default"); // name of your database connection

        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            
            $email = $this->request->data('email');      
            $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $emailDetails = $email_data->fetch('assoc');

            if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                //$this->Flash->error(__('This email id is already exists. '));                
                //$check_email = 1;
            }

            $mobileNumber = $this->request->data('mobileNumber'); 

            $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $phoneDetails = $phone_data->fetch('assoc');

            if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                //$this->Flash->error(__('This mobile no is already exists. '));                
                //$check_mobile = 1;
            }

            $akcessId = $this->request->data('akcessId');   
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');
            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                //$this->Flash->error(__('This AKcess ID is already exists. '));                
                $check_akcess = 1;
            }
            
        }

        $force_add_user = $this->request->data('force_add_user');
        if ($this->request->is(['patch', 'post', 'put']))
        {
            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1)
            {
                // Check User role
                $old_user_type_array = array();
                if($force_add_user == 1)
                {
                    $akcessId_data = $conn->execute("SELECT id,usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                    $akcessIdDetails = $akcessId_data->fetch('assoc');
                    if(!empty($akcessIdDetails) && isset($akcessIdDetails['usertype']) && $akcessIdDetails['usertype'])
                    {
                        $ut = $this->request->data('ut');

                        $old_user_type_array = explode(',',$akcessIdDetails['usertype']);
                        if(!in_array($ut,$old_user_type_array))
                        {
                            $check_akcess = 0;
                            $u_id = $akcessIdDetails['id'];
                            $user = $this->Users->get($u_id, [
                                'conditions' => ['soft_delete' => 0]
                            ]);
                        }
                    }
                }

                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                //$this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This student already exists. '));
            }
        }
        
        if ($this->request->is('post') && ($check_mobile == 0 && $check_email == 0 && $check_akcess == 0)) {
                    
            $user = $this->Users->patchEntity($user, $this->request->getData());
            
            $usertype = $this->request->data('ut');
            if($force_add_user == 1 && !empty($old_user_type_array))
            {
                $old_user_type_array[] = $usertype;
                $usertype = implode(',',$old_user_type_array);
            }
            $akcessId = $this->request->data('akcessId');
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            $faculty = $this->request->data('faculty');
            $courses = $this->request->data('courses');
            $academic_personal_type = $this->request->data('academic_personal_type');
            $staff_type = $this->request->data('staff_type');
            $adminssion_date_check = !empty($this->request->data('adminssion_date')) ? str_replace('/', '-', $this->request->data('adminssion_date')) : "";            
            $adminssion_date = date("Y-m-d", strtotime($adminssion_date_check));
            
            $user->name = $firstname . " " . $lastname;
            $user->akcessId = $akcessId;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->faculty = $faculty;
            $user->courses = $courses;
            $user->academic_personal_type = $academic_personal_type;
            $user->staff_type = $staff_type;
            $user->usertype = $usertype;
            $user->adminssion_date = isset($adminssion_date) && $adminssion_date != "" ? $adminssion_date : date("Y-m-d H:i:s");
            $user->loginOpt = 'pin';
            $user->siteStatus = 'Development';
            $user->status = 1;

            
            $result = $this->Users->save($user);
            
            if ($result) {  
                
                $insertedId = $result->id;
                
                $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                
                $updateUsers = $this->Users->updateAll(
                    [
                        'idcardno' => $idcard_randon
                    ], 
                    [
                        'id' => $insertedId
                    ]
                );
               
                $after = array(
                    'user_id' => $user_id,
                    'akcessId' => $akcessId,
                    'role_id' => $role_id,            
                    'name' => $firstname . " " . $lastname,
                    'companyName' => $companyName,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'email' => $email,
                    'mobileNumber' => $mobileNumber,
                    'gender' => $gender,
                    'dob' => $dob,
                    'otherdetails' => $otherdetails,                
                    'active' => $active,
                    'faculty' => $faculty,
                    'courses' => $courses,
                    'academic_personal_type' => $academic_personal_type,
                    'staff_type' => $staff_type,
                    'usertype' => $usertype,
                    'adminssion_date' => $adminssion_date,
                    'loginOpt' => 'pin',
                    'siteStatus' => 'Development',
                    'status' => 1
                );
                
                $this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);
               
                $this->Flash->success(__('The user has been saved.'));
                
                $idEncode = $this->Global->userIdEncode($insertedId);
                
                return $this->redirect(['action' => 'view', $idEncode]);
            }
            $this->Flash->error(__('The user could not be saved. '));
        }
        
        $this->loadModel('Countries');

        $this->set('page_title', 'Add Student');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
                
        $userID = $this->Global->userIdEncode($id);
        
        $this->set('count_students', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1, 
            'soft_delete' => 0
            ] ))->count() );

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

        $this->set(compact('user', 'countries', 'userID', 'count_students', 'count_teacher', 'count_staff'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $conn = ConnectionManager::get("default"); // name of your database connection       
        
        $user = $this->Users->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);      
        
        $flname = $user['id'];

        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
                return $this->redirect(['action' => 'edit', $idEncode]);
            }
        }
        
        $check_mobile = 0;
        $check_email = 0;
        $check_akcess = 0;
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            if (empty($this->request->getData('photo')) && empty($this->request->getData('photo')['name'])) {
            
                $email = $this->request->data('email');      
                $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0' AND id != '" . $id . "'");
                
                $emailDetails = $email_data->fetch('assoc');
                
                if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {      
                    //$this->Flash->error(__('This email id is already exists. '));                
                    //$check_email = 1;
                }

                $mobileNumber = $this->request->data('mobileNumber'); 
                
                $phone_data = $conn->execute("SELECT count(*) as count_phone FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0' AND id != '" . $id . "'");
                
                $phoneDetails = $phone_data->fetch('assoc');
               
                if((isset($phoneDetails['count_phone']) && $phoneDetails['count_phone'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {                
                    //$this->Flash->error(__('This mobile no is already exists. '));                
                    //$check_mobile = 1;
                }
                
                $akcessId = $this->request->data('akcessId');   
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {      
                    //$this->Flash->error(__('This AKcess ID is already exists. '));                
                    $check_akcess = 1;
                }
            }
        }

        
        if ($this->request->is(['patch', 'post', 'put'])) {
            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                //$this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This student already exists. '));
                return $this->redirect(['action' => 'edit', $idEncode]);
            }
        }
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $before = array(          
            'name' => $user->name,
            'akcessId' => $user->akcessId,
            'companyName' => $user->companyName,
            'address' => $user->address,
            'city' => $user->city,
            'country' => $user->country,
            'email' => $user->email,
            'mobileNumber' => $user->mobileNumber,
            'gender' => $user->gender,
            'dob' => $user->dob,
            'otherdetails' => $user->otherdetails,
            'active' => $user->active,
            'faculty' => $user->faculty,
            'courses' => $user->courses,
            'academic_personal_type' => $user->academic_personal_type,
            'staff_type' => $user->staff_type,
            'adminssion_date' => $user->adminssion_date,
        );
        

        if ($this->request->is(['patch', 'post', 'put']) && ($check_email == 0 && $check_mobile == 0 && $check_akcess == 0)) {
            
            $user = $this->Users->patchEntity($user, $this->request->getData());                        
            
            $akcessId = $this->request->data('akcessId');
            $firstname = $this->request->data('firstname');
            $lastname = $this->request->data('lastname');
            $name = $firstname . " " . $lastname;
            $companyName = $this->request->data('companyName');
            $address = $this->request->data('address');
            $city = $this->request->data('city');
            $country = $this->request->data('nationality');
            $email = $this->request->data('email');
            $mobileNumber = $this->request->data('mobileNumber');
            $gender = $this->request->data('gender');
            $dob_date_check = !empty($this->request->data('dob')) ? str_replace('/', '-', $this->request->data('dob')) : "";            
            $dob = date("Y-m-d", strtotime($dob_date_check));
            $otherdetails = $this->request->data('otherdetails');
            $active = $this->request->data('active');
            $faculty = $this->request->data('faculty');
            $courses = $this->request->data('courses');
            $academic_personal_type = $this->request->data('academic_personal_type');
            $staff_type = $this->request->data('staff_type');
            $adminssion_date_check = !empty($this->request->data('adminssion_date')) ? str_replace('/', '-', $this->request->data('adminssion_date')) : "";            
            $adminssion_date = date("Y-m-d", strtotime($adminssion_date_check));
                       
            $user->akcessId = $akcessId;
            $user->name = $name;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->companyName = $companyName;
            $user->address = $address;
            $user->city = $city;
            $user->country = $country;
            $user->email = $email;
            $user->mobileNumber = $mobileNumber;
            $user->gender = $gender;
            $user->dob = $dob;
            $user->otherdetails = $otherdetails;
            $user->active = $active;
            $user->faculty = $faculty;
            $user->courses = $courses;
            $user->academic_personal_type = $academic_personal_type;
            $user->staff_type = $staff_type;
            $user->adminssion_date = isset($adminssion_date) && $adminssion_date != "" ? $adminssion_date : date('Y-m-d H:i:s');
            
           
            $after = array(
                'akcessId' => $akcessId,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $id,                
                'name' => $firstname . " " . $lastname,
                'companyName' => $companyName,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'email' => $email,
                'mobileNumber' => $mobileNumber,
                'gender' => $gender,
                'dob' => $dob,
                'otherdetails' => $otherdetails,                
                'active' => $active,
                'faculty' => $faculty,
                'courses' => $courses,
                'academic_personal_type' => $academic_personal_type,
                'staff_type' => $staff_type,
                'adminssion_date' => $adminssion_date
            );
           
            $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'update', $before, $after);
            
            if ($this->Users->save($user)) {                
                
                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                
                $this->Flash->success(__('Changes saved.'));
                return $this->redirect(['action' => 'view', $idEncode]);
            }
            $this->Flash->error(__('Changes could not be saved. '));
        }
        
        $this->loadModel('Countries');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);  
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';
        $adminssion_date = isset($user['adminssion_date']) ? $user['adminssion_date'] : '';

        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = $companyName;
        $user->address = $address;
        $user->city = $city;
        $user->nationality = $country;
        $user->email = $email;
        $user->mobileNumber = $mobileNumber;
        $user->usertype = $usertype;
        $user->gender = $gender;
        $user->dob = date('d/m/Y', strtotime($dob));
        $user->photo = $photo;
        $user->otherdetails = $otherdetails;
        $user->active = $active;
        $user->faculty = $faculty;
        $user->courses = $courses;
        $user->academic_personal_type = $academic_personal_type;
        $user->staff_type = $staff_type;
        $user->adminssion_date = date('d/m/Y', strtotime($adminssion_date));
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
       
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $userID = $this->Global->userIdEncode($id);

        $this->set('page_title', 'Edit Student');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');
        
        $documentList = $this->Global->getDocumentList();
                
        $userss = $this->Users->find('all', array('conditions' => ['status' => 1, 'soft_delete' => 0]));
                
        $this->set(compact('userss', 'user', 'users', 'countries', 'userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList'));
    }
        
    
    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function approve($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        $user->status = 1;
        $user->adminssion_date = date('Y-m-d H:i:s');
        if ($this->Users->save($user)) {

            $to = $user->email;
            $subject = 'Registration request approved';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            $msg = 'Hello ' . $user->name . '<br><br> Your registration request has been approved. Click on below link to login now: <br>' . Router::Url(['controller' => 'users', 'action' => 'login'], true) . '<br><br> Thank you.';


            $headers .= "From: " . $_SESSION['Auth']['User']['email'] . "\r\n";
            mail($to, $subject, $msg, $headers);

            $this->Flash->success(__($user->name . ' has been approved.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__($user->name . ' could not be approved. '));
        return $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
    }
    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);

        $conn = ConnectionManager::get("default"); // name of your database connection  

        $user_data = $conn->execute("SELECT * FROM users WHERE id = '" . $id . "' AND soft_delete=0");
           
        $userDetails = $user_data->fetch('assoc');

        $checkword = $_SERVER['HTTP_REFERER'];

        if (strpos($checkword, 'staff-list') !== false)
            $utype = "Staff";
        else if (strpos($checkword, 'teacher-list') !== false)
            $utype = "Academic Personnel";
        else if (strpos($checkword, 'admin-list') !== false)
            $utype = "Admin";
        else if (strpos($checkword, 'users') !== false)
            $utype = "Student";
        else if (strpos($checkword, 'dashboard') !== false)
            $utype = "Student";      

        if(isset($utype) && $utype == "Academic Personnel"){
            $utype_delete = "Teacher";
        } else {
            $utype_delete = $utype;
        }

        $usertype = $utype_delete;
        $id = $userDetails['id'];
        $akcessId = $userDetails['akcessId'];
        $name = $userDetails['name'];
        $email = $userDetails['email'];
        $mobileNumber = $userDetails['mobileNumber'];

        $conn->execute("DELETE FROM `users_delete` WHERE `fk_user_id` = $id AND REPLACE (`usertype`, ' ', '' ) = '".$usertype."'");

        $sql_insert = "INSERT INTO `users_delete` (`fk_user_id`, `akcessId`, `usertype`, `name`, `email`, `mobileNumber`) VALUES ('".$id."','".$akcessId."', '".$usertype."', '".$name."', '".$email."', '".$mobileNumber."')";

        $conn->execute($sql_insert);

        $result = 0;
        $check_count = 0;
        if (!empty($userDetails['usertype']) && $userDetails['usertype'] != "") {
            $usertype_array = explode(",", $userDetails['usertype']);
            $result = count($usertype_array);
            if($result > 1) {
                if(isset($utype) && $utype == "Academic Personnel"){
                    $delete_item = "Teacher";
                } else {
                    $delete_item = $utype;
                }
                
                if (($key = array_search($delete_item, $usertype_array)) !== false) {
                    unset($usertype_array[$key]);
                    $implode = implode(",", $usertype_array);
                    $check_count = 1;
                }
            }
        }
       
        //$this->request->allowMethod(['post', 'delete']);

        $user = $this->Users->get($id);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );

        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_user_id' => $id,     
            'soft_delete' => 1
        );

        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'delete', $before, $after);

        if($check_count == 1) {
            $updateUser = $this->Users->updateAll(
                [
                    'usertype' => $implode
                ], 
                [
                    'id' => $id
                ]
            );
        } else if($check_count == 0) {
            $updateUser = $this->Users->updateAll(
                [
                    'soft_delete' => 1
                ], 
                [
                    'id' => $id
                ]
            );
        }

        if (strpos($checkword, 'teacher-list') !== false) {

            $sql_update = "UPDATE `sclasses` SET `fk_user_id`= 0  WHERE `fk_user_id` = ".$userDetails['id'];
                        
            $conn->execute($sql_update); 

        } else if (strpos($checkword, 'users') !== false) {
            
            $sql_update = "UPDATE `class_attends` SET `soft_delete`= 1  WHERE `userId` = ".$userDetails['id'];
                        
            $conn->execute($sql_update); 
        }

        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        
        if ($updateUser) {
            $this->Flash->success(__('The ' . $utype . ' has been deleted.'));
        } else {
            $this->Flash->error(__('The ' . $utype . ' could not be deleted. '));
        }

        if (strpos($checkword, 'dashboard') !== false) {
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        
        if (strpos($checkword, 'staff-list') !== false)
            return $this->redirect(['action' => 'staffList']);
        else if (strpos($checkword, 'teacher-list') !== false)
            return $this->redirect(['action' => 'teacherList']);
        else if (strpos($checkword, 'admin-list') !== false)
            return $this->redirect(['action' => 'adminList']);
        else if (strpos($checkword, 'users') !== false)
            return $this->redirect(['action' => 'index']);
    }

    public function login() {
        
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        
        $conn = ConnectionManager::get("default"); // name of your database connection  
        
                    // $resultJ = json_encode(array('result' => 'success','status' => $url));
                    // $this->response->type('json');
                    // $this->response->body($resultJ);
                    // return $this->response;
         // Search substring 
        $pattern = '/\bapi\b/';
     
        if (preg_match($pattern, $url) == false) {
            $key_api = 0;
        }
        else {
            $key_api = 1;
        }

        if($key_api == 1) {

            $this->qrcode();
           
        } else if(isset($_REQUEST['akcessid']) && strpos($url, '/getDetails') !== false) {
        
            $akcessId = isset($_REQUEST['akcessid']) ? $_REQUEST['akcessid'] : "";
// $resultJ = json_encode(array('result' => 'success','status' => 1));
//                     $this->response->type('json');
//                     $this->response->body($resultJ);
//                     return $this->response;
            if(isset($akcessId)) {

                $user_data = $conn->execute("SELECT * FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete=0");
            
                $userDetails = $user_data->fetch('assoc');
                
                if(!empty($userDetails)){
                    $resultJ = json_encode(array('result' => 'success','status' => 1));
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;
                } else {
                    $resultJ = json_encode(array('result' => 'error','status' => 0));
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;
                }
            } else {
                $resultJ = json_encode(array('result' => 'error','status' => 0));
                $this->response->type('json');
                $this->response->body($resultJ);
                return $this->response;
            }
        } else {
            
            $this->viewBuilder()->setLayout('login');
            
            if ($this->request->is('post')) {
               
                $log = json_decode($this->request->getData('logindata'));                
                $email = $log->email;
                $akcessId = $log->akcessId;
                //$user = $this->Users->findByEmail($akcessId)->first();

                $query = $this->Users->find('all', ['conditions' => ['akcessId' => $akcessId, 'soft_delete' => 0]]);
                $user = $query->first();

                if (is_null($user)) {                    
                    //$this->Flash->error(__('You have not registered yet.'));
                    $this->Flash->reg(__('You are not registered yet. Kindly register.'));
                    $data = array('a' => $this->Global->userIdEncode($_POST['email']),
                        'b' => $this->Global->userIdEncode($_POST['firstName']),
                        'c' => $this->Global->userIdEncode($_POST['lastName']),
                        'd' => $this->Global->userIdEncode($_POST['akcessid']),
                        'e' => $this->Global->userIdEncode($_POST['phone']),
                        'f' => $this->Global->userIdEncode($_POST['atoken']),
                         'h' => $this->Global->userIdEncode($_POST['city']),
                          'g' => $this->Global->userIdEncode($_POST['dob']));
                    return $this->redirect(['action' => 'register', 'data' => $data]);
                } else if ($user->status == 0) {
                    //$this->Flash->error(__('You are not approved yet. Wait for the approval.'));
                    $this->Flash->reg(__('You are not approved yet. Please wait for the approval.'));
                    return $this->redirect(['action' => 'login']);
                }

                //$this->Auth->identify() = $user;
                $user->isLogin = 1;
                $this->Users->save($user);
                $this->request->session()->write('akcessToken', $_POST['atoken']);
                $this->Auth->setUser($user);
                
                $user_id = $this->Auth->user( 'id' );
                $role_id = $this->Auth->user( 'usertype' );
                $id = $user->id;
                
                $after = array(           
                    'isLogin' => 1,
                    'user_id' => $user_id,
                    'role_id' => $role_id,
                    'fk_user_id' => $id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobileNumber' => $user->mobileNumber,
                    'status' => $user->status,
                    'active' => $user->active,
                    'soft_delete' => $user->soft_delete
                );                
                
                $this->Global->auditTrailApi($user->id, 'users', 'login', null, $after);
                
                return $this->redirect($this->Auth->redirectUrl());
            }           
                        
            $this->set(compact('user'));
        }
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function register($from = null) {

        $conn = ConnectionManager::get("default"); // name of your database connection  

        $this->viewBuilder()->setLayout('login');

        $user = $this->Users->newEntity();        
        
        $e = $this->Global->userIdDecode($_REQUEST['data']['a']);
        $fn = $this->Global->userIdDecode($_REQUEST['data']['b']);
        $ln = $this->Global->userIdDecode($_REQUEST['data']['c']);
        $aid = $this->Global->userIdDecode($_REQUEST['data']['d']);
        $p = $this->Global->userIdDecode($_REQUEST['data']['e']);
        $ad = $this->Global->userIdDecode($_REQUEST['data']['f']);
        $g = $this->Global->userIdDecode($_REQUEST['data']['g']);
        $h = $this->Global->userIdDecode($_REQUEST['data']['h']);
        $data_rerponse = array(
            'a' => $e,
            'b' => $fn,
            'c' => $ln,
            'd' => $aid,
            'e' => $p,
            'g' => $g,
            'h' => $h
        );
        
        $data_rerponse_encode = array(
            'a' => $_REQUEST['data']['a'],
            'b' => $_REQUEST['data']['b'],
            'c' => $_REQUEST['data']['c'],
            'd' => $_REQUEST['data']['d'],
            'e' => $_REQUEST['data']['e'],
            'g' => $_REQUEST['data']['g'],
            'h' => $_REQUEST['data']['h']
        );
       
        $check_mobile = 0;
        $check_email = 0;
        if ($this->request->is('post')) {

            $akcessId = $aid;   
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');
            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] == 1) && (isset($akcessId) && $akcessId != "")) {         
                $check_akcess = 1;
            }

            $email = $e;   
            $email_data = $conn->execute("SELECT count(*) as count_email FROM users WHERE email = '" . $email . "' AND soft_delete = '0'");
            $emailDetails = $email_data->fetch('assoc');
            if((isset($emailDetails['count_email']) && $emailDetails['count_email'] == 1) && (isset($email) && $email != "")) {         
                //$check_email = 1;
            }

            $mobileNumber = $p;   
            $mobileNumber_data = $conn->execute("SELECT count(*) as count_mobileNumber FROM users WHERE mobileNumber = '" . $mobileNumber . "' AND soft_delete = '0'");
            $mobileNumberDetails = $mobileNumber_data->fetch('assoc');
            if((isset($mobileNumberDetails['count_email']) && $mobileNumberDetails['count_email'] == 1) && (isset($mobileNumber) && $mobileNumber != "")) {         
                //$check_mobile = 1;
            }

            if($check_email == 1 || $check_mobile == 1 || $check_akcess == 1) {
                $append_string = "";
                $comma = "";
                if($check_email == 1){
                    $append_string .= "Email";
                    $comma = ",";
                } 
                if($check_mobile == 1){
                    $append_string .= $comma . " Phone No";
                    $comma = ",";
                } 
                if($check_akcess == 1){
                    $append_string .= $comma . " AKcess Id";
                }                
                //$this->Flash->error(__('This ' . $append_string . ' is already exists. '));
                $this->Flash->error(__('This student already exists. '));
                
                return $this->redirect(['action' => 'login']);
            }
            
            $user = $this->Users->patchEntity($user, $this->request->getData());
            $user->email = $e;
            $user->name = $fn . " " . $ln;
            $user->akcessId = $aid;
            $user->mobileNumber = $p;
            
            $user->password = 123;
            
            $lastID = $this->Users->save($user);
            
            if ($lastID) {
                
                $idcard_randon = $this->Global->random_string('numeric', 10) . $lastID->id;
                                
                $updateIDCard = $this->Users->updateAll(
                    [
                        'akcessId' => $aid,
                        'idcardno' => $idcard_randon
                    ], [
                        'id' => $lastID->id
                    ]
                );
                
                $id = $lastID->id;
                
                $user = $this->Users->get($id);
                
                $after = array(       
                    'user_id' => $id,
                    'usertype' => isset($user->usertype) ? $user->usertype : '',
                    'role_id' => isset($user->role_id) ? $user->role_id : '',
                    'fk_user_id' => $id,
                    'akcessId' => $aid,      
                    'name' => isset($user->name) ? $user->name : '',
                    'mobileNumber' => isset($user->mobileNumber) ? $user->mobileNumber : '',
                    'status' => isset($user->status) ? $user->status : '',
                    'active' => isset($user->active) ? $user->active : '',
                    'soft_delete' => isset($user->soft_delete) ? $user->soft_delete : ''
                );                   
                
                $this->Global->auditTrailApi($id, 'users', 'register', null, $after);

                $this->request->session()->write('akcessToken', $ad);
                 
                $this->Flash->success(__('The registration request is successfully sent. Wait for the approval now.'));

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error(__('The user could not be saved. '));
        }


        $this->set(compact('user', 'data_rerponse', 'data_rerponse_encode'));
        if ($from == 'login') {
            $this->render('login');
        }
    }

    public function logout() {
        $user = $this->Users->findByEmail($_SESSION['Auth']['User']['email'])->first();
        $user->isLogin = 0;
        $this->Users->save($user);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        $id = $user_id;
        
        $before = array(   
            'isLogin' => 1
        );  
           
        $after = array(   
            'isLogin' => 0,
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_user_id' => $id,
            'akcessId' => $aid,      
            'name' => $user->name,                    
            'mobileNumber' => $user->mobileNumber
        );                   

        $this->Global->auditTrailApi($id, 'users', 'logout', $before, $after);

        $this->Flash->success('You are now logged out.');
        
        $this->Auth->logout();
        
        return $this->redirect('/');
    }

    public function forgotPassword() {
        $this->viewBuilder()->setLayout('login');
        if ($this->request->is('post')) {
            $query = $this->Users->findByEmail($this->request->data['email']);
            $user = $query->first();
            if (is_null($user)) {
                $this->Flash->error('Email address does not exist. Please try again');
            } else {
                $passkey = uniqid();
                $url = Router::Url(['controller' => 'users', 'action' => 'resetPassword'], true) . '/' . $passkey;
                $timeout = time() + DAY;
                if ($this->Users->updateAll(['passkey' => $passkey, 'timeout' => $timeout], ['id' => $user->id])) {
                    //$this->sendResetEmail($url, $user);

                    $to = $user->email;
                    $subject = "Reset your password";
                    $txt = "Hello " . $user->name . "! \r\n\r\n Click on the link below or copy and paste it into your web browser to reset your password:";
                    $txt .= "\r\n" . $url;
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    $headers .= "From: emilia@navonjobs.com" . " \r\n";
                    mail($to, $subject, $txt, $headers);

                    $this->Flash->success(__('Check your email for your reset password link'));
                    $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('Error saving reset passkey/timeout');
                }
            }
        }
    }

    private function sendResetEmail($url, $user) {
        $email = new Email();
        $email->template('resetpw');
        $email->emailFormat('both');
        $email->from('no-reply@naidim.org');
        $email->to($user->email, $user->full_name);
        $email->subject('Reset your password');
        $email->viewVars(['url' => $url, 'username' => $user->username]);
        if ($email->send()) {
            $this->Flash->success(__('Check your email for your reset password link'));
        } else {
            $this->Flash->error(__('Error sending email: ') . $email->smtpError);
        }
    }

    public function resetPassword($passkey = null) {
        $this->viewBuilder()->setLayout('login');
        if ($passkey) {
            $query = $this->Users->find('all', ['conditions' => ['passkey' => $passkey, 'timeout >' => time()]]);
            $user = $query->first();
            if ($user) {
                if (!empty($this->request->data)) {
                    if ($this->request->getData('password') == $this->request->getData('confirm_password')) {
                        // Clear passkey and timeout
                        $user->passkey = null;
                        $user->timeout = null;

                        $user->password = $this->request->getData('password');
                        if ($this->Users->save($user)) {
                            $this->Flash->set(__('Your password has been updated.'));
                            return $this->redirect(array('action' => 'login'));
                        } else {
                            $this->Flash->error(__('The password could not be updated. '));
                        }
                    } else
                        $this->Flash->error(__('Sorry, Password and Confirm Password does not matched'));
                }
            } else {
                $this->Flash->error('Invalid or expired passkey. Please check your email or try again');
                $this->redirect(['action' => 'password']);
            }
            unset($user->password);
            $this->set(compact('user'));
        } else {
            $this->redirect('/');
        }
        }

        public function list() {
        $users = $this->Users->find('all', ['conditions' => ['id !=' => $this->Auth->user()->id]]);

        $this->set('page_title', 'User Management');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');

        $this->set(compact('users'));
    }

    public function roleupdate() {
        $data = $this->request->getData();

        if (!$data['id']) {
            $this->Flash->success(__('User not found'));
        } else {
            if ($this->Users->updateAll(['usertype' => $data['usertype']], ['id' => $data['id']]))
                $this->Flash->success(__('Role successfully updated'));
            else
                $this->Flash->success(__('Unable to update user role.'));
        }

        $this->redirect('/users/list');
    }
    public function viewProfile($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user = $this->Users->get($id, [
            'conditions' => []
        ]);
        
        $flname = $user['id'];
        if ($this->request->is(['patch', 'post', 'put'])) {
            // print_r($this->request->getData()); die;
            if (!empty($this->request->getData('photo')) && !empty($this->request->getData('photo')['name'])) {
                $fileName = time() . $this->request->getData('photo')['name'];
                //$fileSize = $this->request->getData('photo')['size'];
                //$fileType = $this->request->getData('photo')['type'];
                $dir  = WWW_ROOT . "uploads/attachs/" . $flname . "/";
                
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                
                $uploadFile = $dir . $fileName;
                move_uploaded_file($this->request->getData('photo')['tmp_name'], $uploadFile);
                $user->photo = $fileName;
                $this->Users->save($user);
                $this->Flash->success(__('Profile photo uploaded successfully.'));
            }
        }
        
        $this->loadModel('IDCard');
        
        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {     
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }
        
        $this->loadModel('Docs');
        
        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));
        
        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {     
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }
        
        $userID = $this->Global->userIdEncode($id);
        
        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';
        $created = isset($user['created']) ? $user['created'] : '';
        $modified = isset($user['modified']) ? $user['modified'] : '';
        $adminssion_date = isset($user['adminssion_date']) ? $user['adminssion_date'] : '';
        
        
        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = isset($companyName) ? $companyName : '';
        $user->address = isset($address) ? $address : '';
        $user->city = isset($city) ? $city : '';
        $user->nationality = isset($country) ? $country : '';
        $user->email = isset($email) ? $email : '';
        $user->mobileNumber = isset($mobileNumber) ? $mobileNumber : '';
        $user->usertype = isset($usertype) ? $usertype : '';
        $user->gender = isset($gender) ? $gender : '';
        $user->dob = isset($dob) && $dob != "" ? $dob : '';
        $user->photo = isset($photo) ? $photo : '';
        $user->otherdetails = isset($otherdetails) ? $otherdetails : '';
        $user->active = isset($active) ? $active : '';
        $user->faculty = isset($faculty) ? $faculty : '';
        $user->courses = isset($courses) ? $courses : '';
        $user->academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : '';
        $user->staff_type = isset($staff_type) ? $staff_type : '';
        $user->created = isset($created) && $created != "" ? $created->format("d/m/Y") : '';
        $user->modified = isset($modified) && $modified != "" ? $modified->format("d/m/Y") : '';
        $user->adminssion_date = isset($adminssion_date) && $adminssion_date != "" && $adminssion_date != null  ? $adminssion_date : '';
        
        $documentList = $this->Global->getDocumentList();

                
        $users = $this->Users->find('all', array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1
        ]));

        $this->loadModel('Countries');

        $this->set('page_title', 'View Profile');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');
        
        $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);
        
        $role_id = $this->Auth->user( 'usertype' );
      
        $this->set(compact('user','userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList', 'countries', 'users', 'role_id'));
    }

    public function checkAkcessid($akcessId='',$user_type='')
    {
        $conn = ConnectionManager::get("default"); // name of your database connection

        $akcessId_data = $conn->execute("SELECT usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
        $akcessIdDetails = $akcessId_data->fetch('assoc');

        $resultJ = json_encode(array('result' => 'error','status' => 0));
        if(!empty($akcessIdDetails) && isset($akcessIdDetails['usertype']) && $akcessIdDetails['usertype'])
        {
            $user_type = str_replace('create','',$user_type);
            $user_type = str_replace(' ','',$user_type);
            $user_type_array = explode(',',$akcessIdDetails['usertype']);

            if(!in_array(ucfirst($user_type),$user_type_array))
            {
                $resultJ = json_encode(array('result' => 'success','status' => 1));
            }
        }

        echo $resultJ;
        exit;
//
//        $this->response->type('json');
//        $this->response->body($resultJ);
    }

    public function myProfile()
    {
        $id = $this->Auth->user( 'id' );

        $user = $this->Users->get($id, [
            'conditions' => []
        ]);

        $flname = $user['id'];
        $this->loadModel('IDCard');

        $idcard = $this->IDCard->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));

        $idCardExpiyDate = '';
        $cid = '';
        if($idcard) {
            foreach ($idcard as $t) {
                $idCardExpiyDate = date("m/d/Y", strtotime($t->idCardExpiyDate));
                $cid = $this->Global->userIdEncode($t->id);
            }
        }

        $this->loadModel('Docs');

        $doc = $this->Docs->find('all', array('conditions' => ['fk_users_id' => $id, 'soft_delete' => 0]));

        $docs = array();
        if($doc) {
            foreach ($doc as $docst) {
                $docs[] = array(
                    'name' => $docst->name,
                    'idCardExpiyDate' => date("Y-m-d", strtotime($docst->idCardExpiyDate)),
                    'fk_documenttype_id' => $docst->fk_documenttype_id,
                    'fileUrl' => $docst->fileUrl,
                    'fileName' => $docst->fileName,
                    'id' => $this->Global->userIdEncode($docst->id),
                    'ids' => $docst->id
                );
            }
        }

        $userID = $this->Global->userIdEncode($id);

        $name = explode(" ", $user['name']);
        $firstname = isset($name[0]) ? $name[0] : '';
        $lastname = isset($name[1]) ? $name[1] : '';
        $akcessId = isset($user['akcessId']) ? $user['akcessId'] : '';
        $idcardno = isset($user['idcardno']) ? $user['idcardno'] : '';
        $companyName = isset($user['companyName']) ? $user['companyName'] : '';
        $address = isset($user['address']) ? $user['address'] : '';
        $city = isset($user['city']) ? $user['city'] : '';
        $country = isset($user['country']) ? $user['country'] : '';
        $email = isset($user['email']) ? $user['email'] : '';
        $mobileNumber = isset($user['mobileNumber']) ? $user['mobileNumber'] : '';
        $usertype = isset($user['usertype']) ? $user['usertype'] : '';
        $gender = isset($user['gender']) ? $user['gender'] : '';
        $dob = isset($user['dob']) ? $user['dob'] : '';
        $photo = isset($user['photo']) ? $user['photo'] : '';
        $otherdetails = isset($user['otherdetails']) ? $user['otherdetails'] : '';
        $active = isset($user['active']) ? $user['active'] : '';
        $faculty = isset($user['faculty']) ? $user['faculty'] : '';
        $courses = isset($user['courses']) ? $user['courses'] : '';
        $academic_personal_type = isset($user['academic_personal_type']) ? $user['academic_personal_type'] : '';
        $staff_type = isset($user['staff_type']) ? $user['staff_type'] : '';
        $created = isset($user['created']) ? $user['created'] : '';
        $modified = isset($user['modified']) ? $user['modified'] : '';
        $adminssion_date = isset($user['adminssion_date']) ? $user['adminssion_date'] : '';


        $user->name = $firstname . " " . $lastname;
        $user->akcessId = $akcessId;
        $user->idcardno = $idcardno;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->companyName = isset($companyName) ? $companyName : '';
        $user->address = isset($address) ? $address : '';
        $user->city = isset($city) ? $city : '';
        $user->nationality = isset($country) ? $country : '';
        $user->email = isset($email) ? $email : '';
        $user->mobileNumber = isset($mobileNumber) ? $mobileNumber : '';
        $user->usertype = isset($usertype) ? $usertype : '';
        $user->gender = isset($gender) ? $gender : '';
        $user->dob = isset($dob) && $dob != "" ? $dob : '';
        $user->photo = isset($photo) ? $photo : '';
        $user->otherdetails = isset($otherdetails) ? $otherdetails : '';
        $user->active = isset($active) ? $active : '';
        $user->faculty = isset($faculty) ? $faculty : '';
        $user->courses = isset($courses) ? $courses : '';
        $user->academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : '';
        $user->staff_type = isset($staff_type) ? $staff_type : '';
        $user->created = isset($created) && $created != "" ? $created->format("d/m/Y") : '';
        $user->modified = isset($modified) && $modified != "" ? $modified->format("d/m/Y") : '';
        $user->adminssion_date = isset($adminssion_date) && $adminssion_date != "" && $adminssion_date != null  ? $adminssion_date : '';

        $documentList = $this->Global->getDocumentList();


        $users = $this->Users->find('all', array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1
        ]));

        $this->loadModel('Countries');

//        $this->set('page_title', 'Students');

//        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');

        $topcountries = $this->Countries->find('all')
            ->where(['id IN' => ['1','226']])->toArray();

        $othercountries = $this->Countries->find('all')
            ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();

        $countries=array_merge($topcountries,$othercountries);

        $role_id = $this->Auth->user( 'usertype' );

        $this->set(compact('user','userID', 'idcard', 'idCardExpiyDate', 'cid', 'docs', 'documentList', 'countries', 'users', 'role_id'));
    }
}

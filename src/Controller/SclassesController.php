<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Sclasses Controller
 *
 *
 * @method \App\Model\Entity\Sclass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SclassesController extends AppController
{
    public $components = ['Amazon'];
    
    public function initialize() {
                
        parent::initialize();

        $this->loadComponent('Global');
    }
    

    public function isAuthorized($user) {
        parent::isAuthorized($user);
        $this->Auth->allow();
        return true;
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
    public function index()
    {
        $this->loadModel('Sclasses');

        $conn = ConnectionManager::get("default"); // name of your database connection     
        
        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) { 
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id where s.soft_delete = 0 and label_type="classes" ORDER BY s.created DESC';
        } else if(in_array('Teacher',$session_user_role)) { 
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id where s.soft_delete = 0 and label_type="classes" AND s.fk_user_id = "'.$user_id.'" ORDER BY s.created DESC';
        } else {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id
            FROM sclasses as s 
            LEFT JOIN class_attends as ca ON ca.classId = s.id
            LEFT JOIN users as u ON u.id = ca.userId
            LEFT JOIN locations as l ON s.location = l.id 
            where s.soft_delete = 0 and label_type="classes" AND u.id = "'.$user_id.'" ORDER BY s.created DESC';
        }

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetchAll('assoc');
        
        $sclasses = array();

        foreach($query_response as $key => $value) {  

            $fk_user_id = $value['fk_user_id'];

            $response_data = $conn->execute("SELECT * FROM users WHERE id = '" . $fk_user_id . "' AND soft_delete = '0'");

            $responseDetails = $response_data->fetch('assoc');

            
            $sclasses[] = array(
                'teacher' => $responseDetails['name'],
                'id' => $value['id'],
                'sname' => $value['sname'],
                'details' => $value['details'],
                'days' => $value['days'],
                'openFrom' => $value['openFrom'],
                'openTo' => $value['openTo'],
                'userallow' => $value['userallow'],
                'lname' => $value['lname'],
            );
        }

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
       
        $this->set(compact('sclasses'));

        $this->set('page_title', 'Classes');

        $this->set('page_icon', '<i class="fal fa-users-class mr-1"></i>');
    }

    /**
     * View method
     *
     * @param string|null $id Sclass id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($idEncode = null)
    {
        $id = $this->Global->userIdDecode($idEncode);
        
        $sclasses = $this->Sclasses->get($id, [
            'contain' => []
        ]);

        $this->loadModel('ClassAttends');
        $student = $this->ClassAttends->find('all',array('conditions' => ['classId' => $id, 'soft_delete' => 0] ));

        $userID = $this->Global->userIdEncode($id);

        $this->loadModel('Locations');

        $locations = $this->Locations->get($sclasses->location , array('conditions' => ['soft_delete' => 0]));

        $this->loadModel('Users');

        $this->set('page_title', 'View Class');

        $this->set('page_icon', '<i class="fal fa-users-class mr-1"></i>');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
       
        $this->set(compact('sclasses', 'userID', 'locations'));

        $this->set('student', $student);

    }

    public function mergeArrays($arrays)
    {

        $length = count($arrays[0]);
        $result = [];
        for ($i=0;$i<$length;$i++)
        {
            $temp = [];
            foreach ($arrays as $array)
                $temp[] = $array[$i];

            $result[] = $temp;
        }

        return $result;

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $conn = ConnectionManager::get("default"); // name of your database connection     

        $sclasses = $this->Sclasses->newEntity();

        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );

        $check_name = 0;
        $check_location = 0;
        $check_userallow = 0;
        $check_openFrom = 0;
        $check_openTo = 0;
        $check_days = 0;

        if ($this->request->is('post')) {
            
            $sclasses = $this->Sclasses->patchEntity($sclasses, $this->request->getData(), [
                'validate' => true
            ]); 

            $classes = $this->request->data('classes');
            $days = $classes['days'];
            $openFrom = $classes['openFrom'];
            $openTo = $classes['openTo'];

            $result_array = array();
            foreach ($days as $key=>$val)
            {
                $result_array[$key] = array($days[$key],$openFrom[$key],$openTo[$key]);
            }

            $fk_user_id = $this->request->data('fk_user_id');

            if (!empty($fk_user_id) && $fk_user_id != "") {
               
                $response_data = $conn->execute("SELECT * FROM sclasses WHERE fk_user_id = '" . $fk_user_id . "' AND soft_delete = '0' AND id != '" . $id . "'");
                $responseDetails = $response_data->fetchAll('assoc');
            }

            $campus_check  = 0;
            $check_time    = 0;
            $result_arrays = array();

                $location_query          = 'SELECT openFrom, openTo FROM locations where id = '.$this->request->data('location');
                $querySql_location       = $conn->execute($location_query);
                $location_query_response = $querySql_location->fetch('assoc');

                $campus_open_from  = '';
                $campus_open_to    = '';
                if(!empty($location_query_response))
                {
                    $campus_open_from = isset($location_query_response['openFrom']) && $location_query_response['openFrom'] ?  date('H:i',strtotime($location_query_response['openFrom'])) : '';
                    $campus_open_to   = isset($location_query_response['openTo']) && $location_query_response['openTo'] ?  date('H:i',strtotime($location_query_response['openTo'])) : '';
                }

                foreach($result_array as $value)
                {
                    $campus_check   = 1;
                    $c_from_timestamp = date('H:i',strtotime($value[1]));
                    $c_to_timestamp   = date('H:i',strtotime($value[2]));

                    if($c_from_timestamp >= $campus_open_from && $c_from_timestamp <= $campus_open_to && $c_to_timestamp >= $campus_open_from && $c_to_timestamp <= $campus_open_to )
                    {
                        $campus_check = 0;
                    }

                if($campus_check == 1)
                {
                    break;
                }
            }

                    if($campus_check == 1)
                    {
                $error_string = "The selected location is not available at this time. Please check again";
                $this->Flash->error(__($error_string));
                return $this->redirect(['action' => 'add']);
                    }


            if(!empty($responseDetails))
            {
                foreach($result_array as $value)
                {
                        foreach($responseDetails as $valueD) {

                        $dayss = explode(",",$valueD['days']);
                        $openFroms = explode(",",$valueD['openFrom']);
                        $openTos = explode(",",$valueD['openTo']);

                        foreach ($dayss as $keys => $vals)
                        {
                            if ($value[0] == $vals) {
                                $from_timestamp = strtotime($value[1]);
                                $to_timestamp = strtotime($value[2]);
                                $openFroms_timestamp = strtotime($openFroms[$keys]);
                                $openTos_timestamp = strtotime($openTos[$keys]);

                                if ($from_timestamp >= $openFroms_timestamp && $from_timestamp <= $openTos_timestamp) {
                                    $result_arrays[$vals] = array(
                                        "type" => "from",
                                        "days" => $vals,
                                        "openFroms" => $openFroms[$keys],
                                        "openTos" => $openTos[$keys],
                                        "from" => $value[1],
                                        "to" => $value[2]
                                    );
                                    $check_time = 1;
                                }

                                if ($to_timestamp >= $openFroms_timestamp && $to_timestamp <= $openTos_timestamp) {
                                    $result_arrays[$vals] = array(
                                        "type" => "to",
                                        "days" => $vals,
                                        "openFroms" => $openFroms[$keys],
                                        "openTos" => $openTos[$keys],
                                        "from" => $value[1],
                                        "to" => $value[2]
                                    );
                                    $check_time = 1;
                                }
                            }
                        }
                    }
                }
            }

            $append_string = "";
//            $comma = "";
//
//            $fromTaken = "";
//            $toTaken = "";

            if(!empty($result_arrays['monday']) || !empty($result_arrays['tuesday']) || !empty($result_arrays['wednesday']) || !empty($result_arrays['friday']) || !empty($result_arrays['saturday']) || !empty($result_arrays['sunday']))
            {
                $append_string = "The selected teacher is not available at this time. Please check again";
            }

//            if(!empty($result_arrays['monday'])){
//                $fromTaken = $result_arrays['monday']['openFroms'];
//                $toTaken = $result_arrays['monday']['openTos'];
//                $append_string .= "Monday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['tuesday'])){
//                $fromTaken = $result_arrays['tuesday']['openFroms'];
//                $toTaken = $result_arrays['tuesday']['openTos'];
//                $append_string .= $comma . "Tuesday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['wednesday'])){
//                $fromTaken = $result_arrays['wednesday']['openFroms'];
//                $toTaken = $result_arrays['wednesday']['openTos'];
//                $append_string .= $comma . "Wednesday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['thursday'])){
//                $fromTaken = $result_arrays['thursday']['openFroms'];
//                $toTaken = $result_arrays['thursday']['openTos'];
//                $append_string .= $comma . "Thursday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['friday'])){
//                $fromTaken = $result_arrays['friday']['openFroms'];
//                $toTaken = $result_arrays['friday']['openTos'];
//                $append_string .= $comma . "Friday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['saturday'])){
//                $fromTaken = $result_arrays['saturday']['openFroms'];
//                $toTaken = $result_arrays['saturday']['openTos'];
//                $append_string .= $comma . "Saturday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['sunday'])){
//                $fromTaken = $result_arrays['sunday']['openFroms'];
//                $toTaken = $result_arrays['sunday']['openTos'];
//                $append_string .= $comma . "Sunday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//            }

            if($check_time == 1){
                $this->Flash->error(__($append_string));
                return $this->redirect(['action' => 'add']);
            }

            $name = $this->request->data('name');
            $details = $this->request->data('details');
            $location = $this->request->data('location');
            $userallow = $this->request->data('userallow');
            $qrno = rand();
            $openFrom = implode(',', $openFrom);
            $openTo = implode(',', $openTo);
            $days = implode(',', $days);

            $sclasses->name = $name;
            $sclasses->fk_user_id = $fk_user_id;
            $sclasses->details = $details;
            $sclasses->location = $location;
            $sclasses->userallow = $userallow;
            $sclasses->qrno = $qrno;
            $sclasses->openFrom = $openFrom;
            $sclasses->openTo = $openTo;
            $sclasses->days = $days;
            $sclasses->label_type = 'classes';


            if(isset($name) && $name == "") {
                $check_name = 1;
            }

            if(isset($location) && $location == "") {
                $check_location = 1;
            }

            if(isset($userallow) && $userallow == "") {
                $check_userallow = 1;
            }

            if(isset($openFrom) && $openFrom == "") {
                $check_openFrom = 1;
            }

            if(isset($openTo) && $openTo == "") {
                $check_openTo = 1;
            }

            if(isset($days) && $days == "") {
                $check_days = 1;
            }
            $append_string = "";
            if($check_name == 1 || $check_location == 1 || $check_userallow == 1 || $check_openFrom == 1 || $check_openTo == 1 || $check_days == 1) {

                $comma = "";
                if($check_name == 1){
                    $append_string .= "Name";
                    $comma = ",";
                }
                if($check_location == 1){
                    $append_string .= $comma . " Location";
                    $comma = ",";
                }
                if($check_userallow == 1){
                    $append_string .= $comma . " Number of students";
                }
                if($check_openFrom == 1){
                    $append_string .= "OpenFrom";
                    $comma = ",";
                }
                if($check_openTo == 1){
                    $append_string .= $comma . " OpenTo";
                    $comma = ",";
                }
                if($check_days == 1){
                    $append_string .= $comma . " Days";
                }
//		$this->Flash->error(__('This ' . $append_string . ' MISSING MANDATORY FIELDS.'));
	       $this->Flash->error(__('MISSING MANDATORY FIELDS.'));
            } else {


                if ($this->Sclasses->save($sclasses)) {
                    $user_id = $this->Auth->user('id');
                    $role_id = $this->Auth->user('usertype');

                    $insertedId = $result->id;

                    $after = array(
                        'user_id' => $user_id,
                        'role_id' => $role_id,
                        'name' => $name,
                        'details' => $details,
                        'location' => $location,
                        'userallow' => $userallow,
                        'qrno' => $qrno,
                        'openFrom' => $openFrom,
                        'openTo' => $openTo,
                        'days' => $days,
                        'label_type' => 'classes'
                    );

                    //$this->Global->auditTrailApi($insertedId, 'sclasses', 'insert', null, $after);

                    $this->Flash->success(__('The class has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The class could not be saved. Please, try again.'));
            }
        }

        $this->loadModel('Locations');

        $locations = $this->Locations->find('all' , array('conditions' => ['soft_delete' => 0]));

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Teacher%' AND `status` = 1";
        } else {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Teacher%' AND `status` = 1 AND `id` = ".$user_id;
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc');

        $this->set(compact('sclasses', 'locations', 'users'));
    }

    public function ajaxValidation(){
        $classes = $this->request->data('classes');
        $days = $classes['days'];
        $openFrom = $classes['openFrom'];
        $openTo = $classes['openTo'];

        $result_array = array();
        foreach ($days as $key=>$val)
        {
            $result_array[$key] = array($days[$key],$openFrom[$key],$openTo[$key]);
        }

        $fk_user_id = $this->request->data('fk_user_id');

        if (!empty($fk_user_id) && $fk_user_id != "") {

            $response_data = $conn->execute("SELECT * FROM sclasses WHERE fk_user_id = '" . $fk_user_id . "' AND soft_delete = '0' AND id != '" . $id . "'");

            $responseDetails = $response_data->fetchAll('assoc');
        }

        $check_time = 0;

        $result_arrays = array();

        if(!empty($responseDetails)) {

            foreach($result_array as $value){

                foreach($responseDetails as $valueD) {

                    $dayss = explode(",",$valueD['days']);
                    $openFroms = explode(",",$valueD['openFrom']);
                    $openTos = explode(",",$valueD['openTo']);

                    foreach ($dayss as $keys => $vals)
                    {
                        if ($value[0] == $vals) {
                            $from_timestamp = strtotime($value[1]);
                            $to_timestamp = strtotime($value[2]);
                            $openFroms_timestamp = strtotime($openFroms[$keys]);
                            $openTos_timestamp = strtotime($openTos[$keys]);

                            if ($from_timestamp >= $openFroms_timestamp && $from_timestamp <= $openTos_timestamp) {
                                $result_arrays[$vals] = array(
                                    "type" => "from",
                                    "days" => $vals,
                                    "openFroms" => $openFroms[$keys],
                                    "openTos" => $openTos[$keys],
                                    "from" => $value[1],
                                    "to" => $value[2]
                                );
                                $check_time = 1;
                            }

                            if ($to_timestamp >= $openFroms_timestamp && $to_timestamp <= $openTos_timestamp) {
                                $result_arrays[$vals] = array(
                                    "type" => "to",
                                    "days" => $vals,
                                    "openFroms" => $openFroms[$keys],
                                    "openTos" => $openTos[$keys],
                                    "from" => $value[1],
                                    "to" => $value[2]
                                );
                                $check_time = 1;
                            }
                        }
                    }
                }
            }
        }

        $append_string = "";
        $comma = "";

        $fromTaken = "";
        $toTaken = "";

        if(!empty($result_arrays['monday'])){
            $fromTaken = $result_arrays['monday']['openFroms'];
            $toTaken = $result_arrays['monday']['openTos'];
            $append_string .= "Monday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
            $comma = "<br/>";
        }

        if(!empty($result_arrays['tuesday'])){
            $fromTaken = $result_arrays['tuesday']['openFroms'];
            $toTaken = $result_arrays['tuesday']['openTos'];
            $append_string .= $comma . "Tuesday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
            $comma = "<br/>";
        }

        if(!empty($result_arrays['wednesday'])){
            $fromTaken = $result_arrays['wednesday']['openFroms'];
            $toTaken = $result_arrays['wednesday']['openTos'];
            $append_string .= $comma . "Wednesday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
            $comma = "<br/>";
        }

        if(!empty($result_arrays['thursday'])){
            $fromTaken = $result_arrays['thursday']['openFroms'];
            $toTaken = $result_arrays['thursday']['openTos'];
            $append_string .= $comma . "Thursday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
            $comma = "<br/>";
        }

        if(!empty($result_arrays['friday'])){
            $fromTaken = $result_arrays['friday']['openFroms'];
            $toTaken = $result_arrays['friday']['openTos'];
            $append_string .= $comma . "Friday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
            $comma = "<br/>";
        }

        if(!empty($result_arrays['saturday'])){
            $fromTaken = $result_arrays['saturday']['openFroms'];
            $toTaken = $result_arrays['saturday']['openTos'];
            $append_string .= $comma . "Saturday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
            $comma = "<br/>";
        }

        if(!empty($result_arrays['sunday'])){
            $fromTaken = $result_arrays['sunday']['openFroms'];
            $toTaken = $result_arrays['sunday']['openTos'];
            $append_string .= $comma . "Sunday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
        }

        if($check_time == 1){
            $data['message'] = "error";
            $data['data'] = $append_string . '. Please, try again.';
            print_r(json_encode($data));
            exit;
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Locations id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null)
    {
        $conn = ConnectionManager::get("default"); // name of your database connection

        $id = $this->Global->userIdDecode($idEncode);

        $sclasses = $this->Sclasses->get($id, [
            'contain' => []
        ]);

        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );

        $days = explode(",", $sclasses->days);
        $openFrom = explode(",", $sclasses->openFrom);
        $openTo = explode(",", $sclasses->openTo);

        $result_array = array();
        foreach ($days as $key=>$val)
        {
            $result_array[$key] = array($days[$key],$openFrom[$key],$openTo[$key]);
        }
        $merge = $result_array;

        $before = array(
            'name' => $sclasses->name,
            'details' => $sclasses->details,
            'location' => $sclasses->location,
            'userallow' => $sclasses->userallow,
            'qrno' => $sclasses->qrno,
            'days' => $sclasses->days,
            'openFrom' => $sclasses->openFrom,
            'openTo' => $sclasses->openTo
        );

        if ($this->request->is(['patch', 'post', 'put'])) {

            $sclasses = $this->Sclasses->patchEntity($sclasses, $this->request->getData());

            $classes = $this->request->data('classes');
            $days = $classes['days'];
            $openFrom = $classes['openFrom'];
            $openTo = $classes['openTo'];

            $result_array = array();
            foreach ($days as $key=>$val)
            {
                $result_array[$key] = array($days[$key],$openFrom[$key],$openTo[$key]);
            }

            $fk_user_id = $this->request->data('fk_user_id');

            if (!empty($fk_user_id) && $fk_user_id != "") {

                $response_data = $conn->execute("SELECT * FROM sclasses WHERE fk_user_id = '" . $fk_user_id . "' AND soft_delete = '0' AND id != '" . $id . "'");

                $responseDetails = $response_data->fetchAll('assoc');
            }

            $check_time = 0;
            $campus_check = 0;
            $location_query          = 'SELECT openFrom, openTo FROM locations where id = '.$this->request->data('location');
            $querySql_location       = $conn->execute($location_query);
            $location_query_response = $querySql_location->fetch('assoc');

            $campus_open_from  = '';
            $campus_open_to    = '';
            if(!empty($location_query_response))
            {
                $campus_open_from = isset($location_query_response['openFrom']) && $location_query_response['openFrom'] ?  date('H:i',strtotime($location_query_response['openFrom'])) : '';
                $campus_open_to   = isset($location_query_response['openTo']) && $location_query_response['openTo'] ?  date('H:i',strtotime($location_query_response['openTo'])) : '';
            }

            foreach($result_array as $value)
            {
                $campus_check   = 1;
                $c_from_timestamp = date('H:i',strtotime($value[1]));
                $c_to_timestamp   = date('H:i',strtotime($value[2]));

                if($c_from_timestamp >= $campus_open_from && $c_from_timestamp <= $campus_open_to && $c_to_timestamp >= $campus_open_from && $c_to_timestamp <= $campus_open_to )
                {
                    $campus_check = 0;
                }

                if($campus_check == 1)
                {
                    break;
                }
            }

            if($campus_check == 1)
            {
                $error_string = "The selected location is not available at this time. Please check again";
                $this->Flash->error(__($error_string));
                return $this->redirect(['action' => 'edit/'.$idEncode]);
            }

            $result_arrays = array();

            if(!empty($responseDetails)) {

                foreach($result_array as $value){

                    foreach($responseDetails as $valueD) {

                        $dayss = explode(",",$valueD['days']);
                        $openFroms = explode(",",$valueD['openFrom']);
                        $openTos = explode(",",$valueD['openTo']);

                        foreach ($dayss as $keys => $vals)
                        {
                            if ($value[0] == $vals) {
                                $from_timestamp = strtotime($value[1]);
                                $to_timestamp = strtotime($value[2]);
                                $openFroms_timestamp = strtotime($openFroms[$keys]);
                                $openTos_timestamp = strtotime($openTos[$keys]);

                                if ($from_timestamp >= $openFroms_timestamp && $from_timestamp <= $openTos_timestamp) {
                                    $result_arrays[$vals] = array(
                                        "type" => "from",
                                        "days" => $vals,
                                        "openFroms" => $openFroms[$keys],
                                        "openTos" => $openTos[$keys],
                                        "from" => $value[1],
                                        "to" => $value[2]
                                    );
                                    $check_time = 1;
                                }

                                if ($to_timestamp >= $openFroms_timestamp && $to_timestamp <= $openTos_timestamp) {
                                    $result_arrays[$vals] = array(
                                        "type" => "to",
                                        "days" => $vals,
                                        "openFroms" => $openFroms[$keys],
                                        "openTos" => $openTos[$keys],
                                        "from" => $value[1],
                                        "to" => $value[2]
                                    );
                                    $check_time = 1;
                                }
                            }
                        }
                    }
                }
            }


            $append_string = "";
//            $comma = "";
//
//            $fromTaken = "";
//            $toTaken = "";

//            if(!empty($result_arrays['monday'])){
//                $fromTaken = $result_arrays['monday']['openFroms'];
//                $toTaken = $result_arrays['monday']['openTos'];
//                $append_string .= "Monday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['tuesday'])){
//                $fromTaken = $result_arrays['tuesday']['openFroms'];
//                $toTaken = $result_arrays['tuesday']['openTos'];
//                $append_string .= $comma . "Tuesday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['wednesday'])){
//                $fromTaken = $result_arrays['wednesday']['openFroms'];
//                $toTaken = $result_arrays['wednesday']['openTos'];
//                $append_string .= $comma . "Wednesday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['thursday'])){
//                $fromTaken = $result_arrays['thursday']['openFroms'];
//                $toTaken = $result_arrays['thursday']['openTos'];
//                $append_string .= $comma . "Thursday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['friday'])){
//                $fromTaken = $result_arrays['friday']['openFroms'];
//                $toTaken = $result_arrays['friday']['openTos'];
//                $append_string .= $comma . "Friday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['saturday'])){
//                $fromTaken = $result_arrays['saturday']['openFroms'];
//                $toTaken = $result_arrays['saturday']['openTos'];
//                $append_string .= $comma . "Saturday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//                $comma = "<br/>";
//            }
//
//            if(!empty($result_arrays['sunday'])){
//                $fromTaken = $result_arrays['sunday']['openFroms'];
//                $toTaken = $result_arrays['sunday']['openTos'];
//                $append_string .= $comma . "Sunday time slot is not available. Already taken " . $fromTaken . " - " . $toTaken;
//            }

            if(!empty($result_arrays['monday']) || !empty($result_arrays['tuesday']) || !empty($result_arrays['wednesday']) || !empty($result_arrays['friday']) || !empty($result_arrays['saturday']) || !empty($result_arrays['sunday']))
            {
                $append_string .= "The selected teacher is not available at this time. Please check again";
            }

            if($check_time == 1){               
                $this->Flash->error(__($append_string));
                return $this->redirect(['action' => 'edit', $idEncode]);
            }

            $name = $this->request->data('name');
            $details = $this->request->data('details');
            $location = $this->request->data('location');
            $userallow = $this->request->data('userallow');
            $openFrom = implode(',', $openFrom);
            $openTo = implode(',', $openTo);  
            $days = implode(',', $days);    
            
            $sclasses->name = $name;
            $sclasses->fk_user_id = $fk_user_id;
            $sclasses->details = $details;
            $sclasses->location = $location;
            $sclasses->userallow = $userallow;
            $sclasses->openFrom = $openFrom;
            $sclasses->openTo = $openTo;
            $sclasses->days = $days;
            
            $after = array(
                'name' => $name,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $user_id,        
                'id' => $id,                
                'details' => $details,
                'location' => $location,
                'userallow' => $userallow,
                'openFrom' => $openFrom,
                'openTo' => $openTo
            );
            
            $lastInsertedId = $this->Global->auditTrailApi($id, 'sclasses', 'update', $before, $after);

            if ($this->Sclasses->save($sclasses)) {

                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);

                $this->Flash->success(__('The class has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class could not be saved. Please, try again.'));
        }

        $total_days = sizeof($days);
        $this->set('total_days', $total_days);

        $this->set('page_title', 'Edit Class');

        $this->set('page_icon', '<i class="fal fa-users-class mr-1"></i>');

        $this->loadModel('Locations');

        $locations = $this->Locations->find('all' , array('conditions' => ['soft_delete' => 0]));

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $sclasses->merge = $merge;

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) { 
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Teacher%' AND `status` = 1";
        } else {
            $query_response = "SELECT `users`.* FROM `users` WHERE `soft_delete` = 0 AND `usertype` like '%Teacher%' AND `status` = 1 AND `id` = ".$user_id;
        }

        $results = $conn->execute($query_response);

        $users = $results->fetchAll('assoc'); 

        $this->set(compact('sclasses', 'locations' , 'users'));

        
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
        
        $this->request->allowMethod(['post', 'delete']);

        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'id' => $id,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'sclasses', 'delete', $before, $after);
        
        $updateClasses = $this->Sclasses->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        
        if ($updateClasses) {
            $this->Flash->success(__('The class has been deleted.'));
        } else {
            $this->Flash->error(__('The class could not be deleted. Please, try again.'));
        }
       
        return $this->redirect(['action' => 'index']);
        
    }
    

    /**
     * Add/Edit/Delete method
     *
     * @param string|null $id Sclass id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function manageStudents($idEncode = null)
    {        
        $id = $this->Global->userIdDecode($idEncode);
        
        $this->loadModel('ClassAttends');

        $sclass = $this->Sclasses->get($id, [
            'contain' => []
        ]);

        
        /*$classStudents = $this->Users->find()->join(['table' => 'class_attends','alias' => 'c','type' => 'LEFT', 'conditions' => 'c.userId = Users.id',])->where(['c.classId' => $id])->all(); */

        $all = $this->ClassAttends->find('all',array('conditions' => ['classId' => $id, 'soft_delete' => 0] ));

        $allcount = $this->ClassAttends->find()->where(['classId' => $id, 'soft_delete' => 0])->count();
       
        $this->set(compact('sclass'));

        $this->set('all', $all);

        $this->set('allcount', $allcount);

        $this->set('page_title', $sclass->name. '&nbsp; Students');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function attendanceReport($idEncode = null)
    {
        $conn = ConnectionManager::get("default"); // name of your database connection     
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) { 
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance as a LEFT JOIN sclasses as s ON s.id = a.class_id where class_id="'.$id.'" ORDER BY a.id DESC');
        } else if(in_array('Teacher',$session_user_role)) { 
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance as a LEFT JOIN sclasses as s ON s.id = a.class_id WHERE s.fk_user_id = "'.$user_id.'" and class_id="'.$id.'" ORDER BY a.id DESC');
        } else { 
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance  as a LEFT JOIN sclasses as s ON s.id = a.class_id WHERE fk_user_id = "'.$user_id.'" and class_id="'.$id.'" ORDER BY id DESC');
        }

        $response = $query_data->fetchAll('assoc');

        $auditArray = array();
        
        $this->loadModel('Users');        

        $this->loadModel('Sclasses');        
       
        foreach($response as $audits){
            
            $class_id = isset($audits['class_id']) ? $audits['class_id'] : '';
            $fk_user_id = isset($audits['fk_user_id']) ? $audits['fk_user_id'] : 0;
            $id = isset($audits['id']) ? $audits['id'] : '';
            $attendance_date_time = isset($audits['attendance_date_time']) ? $audits['attendance_date_time'] : '';
            $checkin_date_time = isset($audits['checkin_date_time']) ? $audits['checkin_date_time'] : '';
            $checkout_date_time = isset($audits['checkout_date_time']) ? $audits['checkout_date_time'] : '';
            $checkin = (isset($audits['checkin']) && $audits['checkin'] == 1) ? 'Yes' : 'No';
            $checkout = (isset($audits['checkout']) && $audits['checkout'] == 1) ? 'Yes' : 'No';
            $label_type = isset($audits['label_type']) ? $audits['label_type'] : '';
              
            $username = '';
            
            if($fk_user_id != 0) {                
                $userTable = TableRegistry::get('Users');
                $exists = $userTable->exists(['id' => $fk_user_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM users where id="'.$fk_user_id.'"');
                    $results = $stmt->fetch('assoc');
                    $username = $results['name'];    
                }                
            }

            $classname = '';
            
            if($class_id != 0) {                
                $classTable = TableRegistry::get('Sclasses');
                $exists = $classTable->exists(['id' => $class_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM sclasses where id="'.$class_id.'"');
                    $results = $stmt->fetch('assoc');
                    $classname = $results['name'];    
                }                
            }

            if(!empty($label_type) && $label_type == 'classes') {
                $label_type = 'Classes';
            } else if(!empty($label_type) && $label_type == 'accesscontrol') {
                $label_type = 'Access Control';
            }

            
            if($id) {
                $auditArray[] = array(
                    'id' => $id,
                    'classname' => $classname,
                    'username' => $username,
                    'checkout' => $checkout,
                    'checkin' => $checkin,
                    'checkout_date_time' => $checkout_date_time,
                    'checkin_date_time' => $checkin_date_time,
                    'attendance_date_time' => $attendance_date_time,
                    'label_type' => $label_type
                );
            }            
        }

       
        $this->set(compact('auditArray'));

        $this->set('page_title', 'Attendance Report');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');
        
        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }



    /**
     * Add/Edit/Delete method
     *
     * @param string|null $id Sclass id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function addStudents($idEncode = null)
    {
        
        $id = $this->Global->userIdDecode($idEncode);

        $this->loadModel('Users');

        $conn = ConnectionManager::get("default"); // name of your database connection      

        $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.userallow, l.name as lname, ua.name as uaname FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id LEFT JOIN user_allow as ua ON ua.id = l.userAllow where s.soft_delete = 0 and s.id = "'.$id.'" ORDER BY s.created DESC';

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetch('assoc');

        if($query_response['uaname'] == "All active users") {
            //$users = $this->Users->find('all',array('conditions' => ['status' => 1, 'soft_delete' => 0] ));
            $users = $this->Users->find('all')->where(['usertype LIKE' => '%Student%','status' => 1, 'soft_delete' => 0] );
        } else if($query_response['uaname'] == "All students") {
            $users = $this->Users->find('all',array('conditions' => [
                array('usertype LIKE'=>'%Student%'),
                'status' => 1, 
                'soft_delete' => 0
            ]));
            
        } else if($query_response['uaname'] == "All staff") {
            $users = $this->Users->find('all',array('conditions' => [
                array('usertype LIKE'=>'%Staff%'),
                'status' => 1, 
                'soft_delete' => 0
            ]));
           
        } else if($query_response['uaname'] == "All Academic personnel") {
            $users = $this->Users->find('all',array('conditions' => [
                array('usertype LIKE'=>'%Teacher%'),
                'status' => 1, 
                'soft_delete' => 0
            ]));
           
        } else if($query_response['uaname'] == "Other") {
            $users = $this->Users->find('all',array('conditions' => ['soft_delete' => 0] ));
        }  else {
            $users = $this->Users->find('all',array('conditions' => ['soft_delete' => 0] ));
        }  
        
        if ($this->request->is(['patch', 'post', 'put'])) {

            $students = $this->request->getData('users');

            $this->loadModel('ClassAttends');

            $allcount = $this->ClassAttends->find()->where(['classId' => $id, 'soft_delete' => 0])->count();

            $sclass = $this->Sclasses->get($id, [
                'contain' => []
            ]);
            $t = $allcount + count($students);
            
            if($sclass->userallow < $t)
            {
                $this->Flash->error(__('Number of students exceeded the Maximum number of students allowed for the '.$sclass->name));

                return $this->redirect(['action' => 'addStudents', $idEncode]);
               
            }
            
            foreach ($students as $key => $value) {

                $classAttend = $this->ClassAttends->newEntity();

                $classAttend->userId = $value;
                $classAttend->classId = $id;

                $result = $this->ClassAttends->save($classAttend);

                $user_id = $this->Auth->user( 'id' );
                $role_id = $this->Auth->user( 'usertype' );

                $insertedId = $result->id;

                $after = array(       
                    'user_id' => $user_id,
                    'role_id' => $role_id, 
                    'fk_user_id' => $user_id,                    
                    'userId' => $value,
                    'classId' => $id
                );
                
                $this->Global->auditTrailApi($insertedId, 'class_attends', 'insert', null, $after);
            }

            
            $this->Flash->success(__('The students has been added to class.'));

            return $this->redirect(['action' => 'manageStudents', $idEncode]);
             
        }

        $this->loadModel('ClassAttends');

        $all = $this->ClassAttends->find('all',array('conditions' => ['classId' => $id, 'soft_delete' => 0] ));

        $arrayUser = array();

        if($all) {
            foreach ($all as $a){
                $arrayUser[] = $a->userId;
            }
        }

        $this->set('all', $arrayUser);

        $this->set('id', $idEncode);

        $this->set('users', $users);

        $this->set(compact('sclass'));

        $this->loadModel('Users');

        $this->set('page_title', 'Add Student');

        $this->set('page_icon', '<i class="fal fa-user-graduate mr-1"></i>');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteStudent($idEncode = null, $classId = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $this->request->allowMethod(['post', 'delete']);

        $this->loadModel('ClassAttends');

        $sclass = $this->ClassAttends->get($id);

        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'id' => $id,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'class_attends', 'delete', $before, $after);
        
        $updateClassAttends = $this->ClassAttends->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        
        if ($updateClassAttends) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }
       
        return $this->redirect(['action' => 'manageStudents', $classId]);
        
    }

    public function attendance($idEncode = null)
    {
        $this->loadModel('Sclasses');

        $conn = ConnectionManager::get("default"); // name of your database connection

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id where s.soft_delete = 0 and label_type="classes" ORDER BY s.created DESC';
        } else if(in_array('Teacher',$session_user_role)) {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id where s.soft_delete = 0 and label_type="classes" AND s.fk_user_id = "'.$user_id.'" ORDER BY s.created DESC';
        } else {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id
            FROM sclasses as s
            left JOIN attendance as a ON a.class_id = s.id
            LEFT JOIN locations as l ON s.location = l.id
            where s.soft_delete = 0 and s.label_type="classes" AND s.fk_user_id = "'.$user_id.'" ORDER BY a.id DESC';
        }

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetchAll('assoc');

        $sclasses = array();

        foreach($query_response as $key => $value) {

            $fk_user_id = $value['fk_user_id'];

            $response_data = $conn->execute("SELECT * FROM users WHERE id = '" . $fk_user_id . "' AND soft_delete = '0'");

            $responseDetails = $response_data->fetch('assoc');

            $sclasses[] = array(
                'teacher' => $responseDetails['name'],
                'id' => $value['id'],
                'sname' => $value['sname'],
                'details' => $value['details'],
                'days' => $value['days'],
                'openFrom' => $value['openFrom'],
                'openTo' => $value['openTo'],
                'userallow' => $value['userallow'],
                'lname' => $value['lname'],
            );
        }

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );


        $this->set(compact('sclasses'));

        $this->set('page_title', 'Classes');

        $this->set('page_icon', '<i class="fal fa-users-class mr-1"></i>');    }


}

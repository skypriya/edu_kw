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
use Cake\View\ViewBuilder;

/**
 * AccessControl Controller
 *
 *
 * @method \App\Model\Entity\Sclass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccessControlController extends AppController
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
//        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
//        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
//        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
//        $token = $this->Global->getToken();
//
//        $origin_array = array(
//            'authorization: ' . $token,
//            'apikey: ' . $api,
//            'origin: ' . $origin_url
//        );
//
//        $data_array = array(
//            'countryCode'    => 91,
//            'phone'    => 9624257046,
//            'msg'    => 'Hi hitesh, this is testing msg',
//            'recievedType' => 'idcard'
//        );
//
//        $method = "POST";
//
//        $api_url = AK_ORIGIN_URL_GLOBAL;
//
//        // print_r($method);
//        // print_r($type);
//        // print_r($api);
//        // print_r($origin_url);
//        // print_r($api_url);
//        // print_r($data_array);
//        // print_r($origin_array);
//        $type = 'send-sms';
//
//        $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);
//
//        $response_phone_Data = json_decode($response_data);
//
//        echo "<pre>";
//        print_r($response_phone_Data);
//        exit;
//




        $this->loadModel('Sclasses');

        $conn = ConnectionManager::get("default"); // name of your database connection     

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id where s.soft_delete = 0 and label_type = "accesscontrol" ORDER BY s.created DESC';
        } else if(in_array('Teacher',$session_user_role)) {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id where s.soft_delete = 0 and label_type = "accesscontrol" AND s.fk_user_id = "'.$user_id.'" ORDER BY s.created DESC';
        } else {
            $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.days, s.userallow, l.name as lname, s.fk_user_id
            FROM sclasses as s 
            LEFT JOIN class_attends as ca ON ca.classId = s.id
            LEFT JOIN users as u ON u.id = ca.userId
            LEFT JOIN locations as l ON s.location = l.id 
            where s.soft_delete = 0 and label_type = "accesscontrol" AND u.id = "'.$user_id.'" ORDER BY s.created DESC';
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

        $this->loadModel('Locations');

        $locations = $this->Locations->find('all' , array('conditions' => ['soft_delete' => 0]));


        $this->loadModel('GuestPass');

        $guest_pass_list = array();
        if(in_array('Admin',$session_user_role))
        {
            $query1 = 'SELECT g.*,l.name as location_name FROM guest_pass as g LEFT JOIN locations l ON l.id = g.location WHERE g.soft_delete = 0 ORDER BY g.id DESC';
            $querySql1 = $conn->execute($query1);
            $guest_pass_list = $querySql1->fetchAll('assoc');
        }

        $guestPass = $this->GuestPass->newEntity();


        $this->loadModel('Countries');
        $this->set('countries_list', $this->Countries->find('all')->all()->toArray());

        $this->set(compact('sclasses','locations','guestPass','guest_pass_list'));
        $this->set('page_title', 'Access Control');

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
        $this->loadModel('Sclasses');

        $id = $this->Global->userIdDecode($idEncode);

        $sclasses = $this->Sclasses->get($id, [
            'contain' => []
        ]);

        $userID = $this->Global->userIdEncode($id);

        $this->loadModel('Locations');

        $locations = $this->Locations->get($sclasses->location , array('conditions' => ['soft_delete' => 0]));

        $this->loadModel('Users');

        $this->set('page_title', 'View Access Point');

        $this->set('page_icon', '<i class="fal fa-users-class mr-1"></i>');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set(compact('sclasses', 'userID', 'locations'));
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
        $this->loadModel('Sclasses');

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
                $this->Flash->error(__($append_string . '. Please, try again.'));
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
            $sclasses->label_type = 'accesscontrol';

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
                        'label_type' => 'accesscontrol'
                    );

                    //$this->Global->auditTrailApi($insertedId, 'sclasses', 'insert', null, $after);

                    $this->Flash->success(__('The access point has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The access point could not be saved. Please, try again.'));
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

        $this->loadModel('Sclasses');

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
        $this->loadModel('Sclasses');

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
                $this->Flash->error(__($append_string . '. Please, try again.'));
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

                $this->Flash->success(__('The access point has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The access point could not be saved. Please, try again.'));
        }

        $total_days = sizeof($days);
        $this->set('total_days', $total_days);

        $this->set('page_title', 'Edit Access Point');

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

        $this->loadModel('Sclasses');

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
            $this->Flash->success(__('The access point has been deleted.'));
        } else {
            $this->Flash->error(__('The access point could not be deleted. Please, try again.'));
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

        $this->loadModel('Sclasses');

        $sclass = $this->Sclasses->get($id, [
            'contain' => []
        ]);


        /*$classStudents = $this->Users->find()->join(['table' => 'class_attends','alias' => 'c','type' => 'LEFT', 'conditions' => 'c.userId = Users.id',])->where(['c.classId' => $id])->all(); */

        $all = $this->ClassAttends->find('all',array('conditions' => ['classId' => $id, 'soft_delete' => 0] ));

        $allcount = $this->ClassAttends->find()->where(['classId' => $id, 'soft_delete' => 0])->count();

        $this->set(compact('sclass'));

        $this->set('all', $all);

        $this->set('allcount', $allcount);

        $this->set('page_title', $sclass->name. '&nbsp; Users');

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
        $this->loadModel('Sclasses');

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
        $this->loadModel('Sclasses');

        $id = $this->Global->userIdDecode($idEncode);

        $this->loadModel('Users');

        $conn = ConnectionManager::get("default"); // name of your database connection      

        $query = 'SELECT s.id, s.name as sname, s.details, s.openFrom, s.openTo, s.userallow, l.name as lname, ua.name as uaname FROM sclasses as s LEFT JOIN locations as l ON s.location = l.id LEFT JOIN user_allow as ua ON ua.id = l.userAllow where s.soft_delete = 0 and s.id = "'.$id.'" ORDER BY s.created DESC';

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetch('assoc');

        if($query_response['uaname'] == "All active users") {
            $users = $this->Users->find('all',array('conditions' => ['status' => 1, 'soft_delete' => 0] ));
//            $users = $this->Users->find('all')->where(['usertype LIKE' => '%Student%','status' => 1, 'soft_delete' => 0] );
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

            // if($sclass->userallow < $t)
            // {
            //     $this->Flash->error(__('Number of students exceeded the Maximum number of students allowed for the '.$sclass->name));

            //     return $this->redirect(['action' => 'addStudents', $idEncode]);

            // }

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


            $this->Flash->success(__('The users has been added to access control.'));

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

        $this->set('page_title', 'Add User');

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

        $this->loadModel('Sclasses');

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
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageStudents', $classId]);

    }

    public function addGuestPass()
    {
        if($this->request->is('post'))
        {
            $this->loadModel('GuestPass');
            $guestPass     = $this->GuestPass->newEntity();
            $post          = $this->request->data();
            $guestPassDate = '';
            if(isset($post['guestPassDate']) && $post['guestPassDate'])
            {
                $date          = explode('/',$post['guestPassDate']);
                $guestPassDate = $date[2].'-'.$date[1].'-'.$date[0];
            }
            $guestPass->akcessId        = isset($post['akcessid']) ? $post['akcessid'] : '';
            $guestPass->first_name      = isset($post['first_name']) ? $post['first_name'] : '';
            $guestPass->last_name       = isset($post['last_name']) ? $post['last_name'] : '';
            $guestPass->invitee_name    = isset($post['invitee_name']) ? $post['invitee_name'] : '';
            $guestPass->institution_name= isset($post['institution_name']) ? $post['institution_name'] : '';
            $guestPass->mobile          = isset($post['mobile']) ? $post['mobile'] : '';
            $guestPass->email           = isset($post['email']) ? $post['email'] : '';
            $guestPass->country_code    = isset($post['country']) ? $post['country'] : '';
            $guestPass->location        = isset($post['location']) ? $post['location'] : '';
            $guestPass->purpose         = isset($post['purpose']) ? $post['purpose'] : '';
            $guestPass->guest_pass_date = $guestPassDate;
            $guestPass->guest_pass_time = isset($post['guestPassTime']) && $post['guestPassTime'] ?  $post['guestPassTime'] : '';
            $guestPass->note            = isset($post['note']) ? $post['note'] : '';

            $res = $this->GuestPass->save($guestPass);
            if(isset($res->id) && $res->id)
            {
                $this->generatePdf($res->id);
                $this->Flash->success(__('The Guest Pass has been saved.'));
            }
            else
            {
                $this->Flash->error(__('The Guest Pass could not be saved. Please, try again.'));
            }
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function guestPassDelete($idEncode = null) {

        $this->loadModel('GuestPass');

        $id = $this->Global->userIdDecode($idEncode);

        $this->request->allowMethod(['post', 'delete']);

//        $user_id = $this->Auth->user( 'id' );
//        $role_id = $this->Auth->user( 'usertype' );
//
//        $after = array(
//            'user_id' => $user_id,
//            'role_id' => $role_id,
//            'id' => $id,
//            'soft_delete' => 1
//        );
//
//        $before = array(
//            'soft_delete' => 0
//        );
//
//        $lastInsertedId = $this->Global->auditTrailApi($id, 'sclasses', 'delete', $before, $after);

        $updateClasses = $this->GuestPass->updateAll(
            [
                'soft_delete' => 1
            ],
            [
                'id' => $id
            ]
        );

//        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);

        if ($updateClasses) {
            $this->Flash->success(__('The guest pass has been deleted.'));
        } else {
            $this->Flash->error(__('The guest pass could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);

    }

    public function getGuestPassData($idEncode= null)
    {
        $this->loadModel('GuestPass');
        $returnArray = array('code'=>0,'msg'=>'Data not found.');
        $id          = $this->Global->userIdDecode($idEncode);
        $res         = $this->GuestPass->find('all' , array('conditions' => ['soft_delete' => 0,'id'=>$id]))->first()->toArray();
        if(!empty($res))
        {
            if(isset($res['guest_pass_date']) && $res['guest_pass_date'])
            {
                $res['guest_pass_date'] = date('m/d/Y',strtotime($res['guest_pass_date']));
            }
            $returnArray = array('code'=>1,'msg'=>'Data get successfully.','data'=>$res);
        }
        echo json_encode($returnArray);
        exit;
    }

    public function editGuestPass($idEncode= null)
    {
        $id = $this->Global->userIdDecode($idEncode);
        $this->loadModel('GuestPass');
        $model = $this->GuestPass->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $guestPass = $this->GuestPass->patchEntity($model, $this->request->getData());
            $post      = $this->request->data();
            $guestPassDate = '';
            if(isset($post['guestPassDate']) && $post['guestPassDate'])
            {
                $date          = explode('/',$post['guestPassDate']);
                $guestPassDate = $date[2].'-'.$date[1].'-'.$date[0];
            }
            $guestPass->akcessId        = isset($post['akcessid']) ? $post['akcessid'] : '';
            $guestPass->first_name      = isset($post['first_name']) ? $post['first_name'] : '';
            $guestPass->last_name       = isset($post['last_name']) ? $post['last_name'] : '';
            $guestPass->invitee_name    = isset($post['invitee_name']) ? $post['invitee_name'] : '';
            $guestPass->institution_name= isset($post['institution_name']) ? $post['institution_name'] : '';
            $guestPass->mobile          = isset($post['mobile']) ? $post['mobile'] : '';
            $guestPass->email           = isset($post['email']) ? $post['email'] : '';
            $guestPass->country_code    = isset($post['country']) ? $post['country'] : '';
            $guestPass->location        = isset($post['location']) ? $post['location'] : '';
            $guestPass->purpose         = isset($post['purpose']) ? $post['purpose'] : '';
            $guestPass->guest_pass_date = $guestPassDate;
            $guestPass->guest_pass_time = isset($post['guestPassTime']) && $post['guestPassTime'] ?  $post['guestPassTime'] : '';
            $guestPass->note            = isset($post['note']) ? $post['note'] : '';

            if($this->GuestPass->save($guestPass))
            {
                $this->Flash->success(__('The Guest Pass has been saved.'));
            }
            else
            {
                $this->Flash->error(__('The Guest Pass could not be saved. Please, try again.'));
            }
            return $this->redirect(['action' => 'index']);
        }

    }

    public function guestPassView($idEncode = null)
    {
        $this->loadModel('GuestPass');

        $id = $this->Global->userIdDecode($idEncode);

        $res = $this->GuestPass->find()->hydrate(false)->select(['GuestPass.id','GuestPass.invitee_name','GuestPass.first_name','GuestPass.last_name','GuestPass.guest_pass_date','GuestPass.guest_pass_time','GuestPass.mobile','GuestPass.institution_name', 'l.name','GuestPass.country_code'])->join(['table' => 'locations','alias' => 'l','type' => 'LEFT', 'conditions' => 'l.id = GuestPass.location',])->where(array('GuestPass.id'=>$id))->first();

        $this->set('page_title', 'View Guest Pass');

        $this->set('page_icon', '<i class="fal fa-users-class mr-1"></i>');

        $this->set(compact('res'));
    }

    public function generatePdf($id)
    {
        $this->loadModel('GuestPass');
        $res = $this->GuestPass->find()->hydrate(false)->select(['GuestPass.id','GuestPass.invitee_name','GuestPass.first_name','GuestPass.last_name','GuestPass.guest_pass_date','GuestPass.guest_pass_time','GuestPass.mobile','GuestPass.institution_name', 'l.name','GuestPass.country_code'])->join(['table' => 'locations','alias' => 'l','type' => 'LEFT', 'conditions' => 'l.id = GuestPass.location',])->where(array('GuestPass.id'=>$id))->first();

        $name = isset($res['first_name']) ? $res['first_name'].' '.$res['last_name'] : '-';
        $invitee_name =isset($res['invitee_name']) && $res['invitee_name'] ? $res['invitee_name'] : '-';

        $country_code = '';
        if(isset($res['country_code']) && $res['country_code'])
        {
            $country_code = '+'.$res['country_code'];
        }
        $mobile = isset($res['mobile']) ? $country_code.$res['mobile'] : '-';
        $location_name = isset($res['l']['name']) ? $res['l']['name'] : '-';
        $guest_pass_date = isset($res['guest_pass_date']) && $res['guest_pass_date'] ? date('d/m/Y',strtotime($res['guest_pass_date'])) : '-';
        $guest_pass_time = isset($res['guest_pass_date']) && $res['guest_pass_date'] ? $res['guest_pass_time'] : '-';

        $dataArray = array(
            'portal' => ORIGIN_URL,
            'type' => 'guest_pass',
            'id'  => isset($res['id']) ? $res['id'] : '',
            'user_name' => isset($res['first_name']) ? $res['first_name'].' '.$res['last_name'] : '',
            'label_type' => 'guestpass',
        );
        $data = json_encode($dataArray);


        $logo_image  = WWW_ROOT . "/img/generic_logo.png";

        //$logo_image = Router::url("/", true) . 'img/london-university.png';

        $logo_imgData = base64_encode(file_get_contents($logo_image));

        // Format the image SRC:  data:{mime};base64,{data};
        $src_logo = 'data:' . mime_content_type($logo_image) . ';base64,' . $logo_imgData;


        $html = '';

        $html .= '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<style>';
        $html .= 'hr {border: 0; border-top: 1px solid rgb(0 0 0 / 18%);}';
        // $html .= '@page {margin: 0cm 0cm;}';
        $html .= 'body {margin-top: 2cm; margin-left: 2cm; margin-right: 2cm; margin-bottom: 2cm;}';
        $html .= '.card-box{background-color:#fff;padding:30px;border:2px solid #909090;border-radius:5px;}';
        $html .= '.university-img{width:200px;object-fit:contain;}';
        $html .= '.profile-img{height:250px;width:250px;object-fit:contain;border-radius:120px;border:1px solid #eae7e7;margin-left:50px}';
        $html .= '.profile-img-container{padding: 60px 0px;}';
        $html .= '.name{font-size: 28px;text-transform: uppercase;color: #231f20;font-weight: 800;margin: 0;margin-bottom: 20px;}';
        $html .= '.label-name{font-size: 14px;font-weight: 500;color: #231f20;line-height: normal;}';
        $html .= '.label-name .inner-lable{display: inline-block; width:70%; text-align:left;}';
        $html .= '.label-name .inner-lable-value{width:30%; text-align:left;}';
        $html .= '.label-name-light{font-size: 16px;font-weight: 600;color: #231f20b3;}';
        $html .= '.label-name-gray{color:#080508 !important;}';
        $html .= '.label-content{display:inline-block;width:285px;word-break:break-word;color:#818080}';
        $html .= '.mt-5{margin-top:5px;}';
        $html .= '.mt-3{margin-top:3px;}';
        $html .= '.mb-20{margin-bottom: 20px !important;}';
        $html .= '.border-red-div {position: absolute;right: -40px;bottom: 95px;transform: rotate(-42deg);}';
        $html .= '.border-red{height: 150px;width: 305px;transform: skewX(-42deg);background-color: #580A3B;}';
        $html .= '.border-red-div-2 {position: absolute;right: 75.5px;bottom: -7.5px;transform: rotate(-42deg);}';
        $html .= '.border-red-2 {height: 150px;width: 302px;transform: skewX(48deg);background-color: #580A3B;}';
        $html .= '.detail{width: 300px;margin: auto;}';
        $html .= 'table{width: 230px;margin: auto;}';
        $html .= 'td{white-space: nowrap;font-size: 20px;line-height: 30px;text-align: left;}';
        $html .= 'td p{padding: 0 15px;}';
        $html .= '</style></head>';
        $html .= '<body>';
        $html .= '<div class="card-box">';
        $html .= '<div class="row" style="width: 100%;">
                     <div class="col-lg-6" style="width: 50%;text-align: left;">

                         <div class="form-group">
                            <strong>Name</strong>
                            <div>'.$name.'</div>
                         </div>
                         <hr>
                         <div class="form-group">
                            <strong>Invitation from</strong>
                            <div>'.$invitee_name.'</div>
                         </div>
                         <hr>
                         <div class="form-group">
                            <strong>Mobile</strong>
                            <div>'.$mobile.'</div>
                         </div>
                         <hr>
                         <div class="form-group">
                            <strong>Location</strong>
                            <div>'.$location_name.'</div>
                         </div>
                         <hr>
                         <div class="date-time" style="width: 100%;">
                             <div class="col-lg-6" style="width: 50%;">
                                <div class="form-group">
                                    <strong>Date : </strong> '.$guest_pass_date.'
                                </div>
                         </div>
                         <div class="col-lg-6" style="width: 50%;margin-top: -6%;float: right;">
                             <div class="form-group">
                                <strong>Time : </strong> '.$guest_pass_time.'
                             </div>
                         </div>
                     </div>
                  </div>
                  <div class="col-lg-6" style="width: 50%;float: right;text-align: center;margin-top: -48%;">
                      <div>
                        <img class="" style="width:110px;margin-left: 20px;" src="'.$src_logo.'" alt="university-logo" />
                     </div>
                     <div class="card upload-profile-box mb-0" style="margin-left: 26px;">
                         <div class="card-header" style="top: 15%;">
                             <div id="qrdiv">
                                 <input id="qrcodeclassestext" type="hidden" value="{&quot;portal&quot;:&quot;localhost:4200&quot;,&quot;type&quot;:&quot;guest_pass&quot;,&quot;id&quot;:1,&quot;user_name&quot;:&quot;Hitesh Chandavaniya&quot;,&quot;label_type&quot;:&quot;guestpass&quot;}"><br>
                                 <div id="qrcodeclasses" title=""><canvas width="200" height="200" style="display: none;"></canvas><img alt="Scan me!" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAYAAACtWK6eAAAAAXNSR0IArs4c6QAAFOZJREFUeF7tneuW2zgMg7fv/9C7k2S6cX0hPyBU2s5gz9k/jSxLIECCspP58c8///z78f+y//7999fpf/z48f+99p/RRWznuF2znUf5bHu/ap37dVXr3t+/usf2s9W4UGxv46o9UMyU2E5gpuxPGXtjawSyE1lHkgjkQbGp5BeBbCS7OlOmgjzBVrL4VTWrsu23E4gL6B7ESgQ0W6wguhLQCSFXuCi2TbEEV2Mp7l31nLBYSmxptV7B3TsWW4u14iaut1dAdINGhbwClwjkvNJVcVcSsZtU9vePQD6RVKrLFPiVsN17pIK8hlwEAvuhWKyeaG5SUdzBH22xVh/3ueW0sid0zftTGCVoSuNKM/q7j6qpLVWsoGtZlf7o6jBBsfLK3kuLRcm2IpO4DS5dcwRy3gN0Tbqb0al4VghSSWiSxaJki0CuyZYK8kAgAhnw9m6pvSq7SpWoKhbNmsr9JvbaEc/FpbIgfWdyFMQel2oO13Z/GYu1IpO4RKDEp+MikGckFKLT+ClJbML93BPQx///v2qyQoWK/ZrIqkqzTYlPx0UgEchpdXRFoAiSPqyLQM4NjIJLLNZG6L+zguxDSUWgeNgq2LRi0XLd7cedx90D7ReUcdVxNJ3HrcjfzmJ1hKKAVx7WJdcKIkQgj2hEIMV3PmjWdsRxb6o29+4ESMe6xFYsJN3vijnpvbsei87zZQRCN+yWPsVS0Vc/FDIr96dY0Psr954Q8hQp3WpNbbGLi3IdXcs94d4q3s8LVtxEaeDpwpVgV3NO7ZeSprJ7dO9VVaRCqhJaZ4HoXhVsafJTEjFNaB0WEYiL5MV1qSAPYCKQglj0YeAKEClB1SBSHdH7u3tPBXki4FYepVr/Ud9Jd4WV686z9nfChSYwdVwE8omY0it9J+L9LbioxKfjI5AIRPp1kj81OVDCq+N+fGSIpT/7o/g9+nBOeRZQzUm3rrx45xJIwenqNEx5nYTuXVkXnVM5bVvRZygiiUAAWhHIOUgKLu7xcASyQS4VhBExFQRktaEhqSAASCVTxmL1gH5JizVFkhWl1iXlRPmmzz162rARSv/FZjyOmqjkytsOK/rEau90f7c5cAWJQHT74xKUBvcewOLlTPf+lEDKEXCVGCOQi0hNldpUEFcK59dFIE9cfqkgSjaiR3r7ECj3oGGnGejdx6DV3pXsO2EFKywVd0ArAY3d1Lip2O7niUA+I+SSRAnw76x0EcgTAaU/ikAikEMfQ93BO5IKTUCpIAVSsViURuygIQK56EEUmF274HpY2tBPZTX3ftv9KUeyVORKX7Maa4Uv1VomMHNx6XpkfMyrLIA2lQqZXcLSbNgBdRVEuq7b9XSsi4virZWxFYEdkVAcFMwUfir9WARygRYNIh2nBDsC2Vgc4Uc4rqqUkiQPp1gfk1pv81JLoASbluEqA7hgKHaIZk33KHc/P832dJwyv5KZJ5yDwhdq81/abwTygC8CYbKnpFTESpOtK9YIZINAKsg50RUrSD26UiErdxCBGAR2n7hHIBGI0u91dbNs0mnWUchMSy8dp5RP92GS4otpplTsAp2TxqslBXwB0rWlShKb6EuVnrV81aQKWkXECIRlbSp6Om7fO0UgnfSPn3ciTwX5xMytLtSvnx0EXGXHCIQlHF0OwwJxy9tECVUawKnMSQlL76dUVmoDFCFPzOmSULGlFE83Gb2CGX5QqAQ7AnmEUsFsgszK/dyTIyqYCKRAKgKJQCKQCOQUgSoz0+yrHIi490sFYUnM/mVFNzDb4HcnCNVY2h+9e510f0qV/Z2vcLhi3V/n9hnuk3vFbpYx+/hw/F0smikjEIZUBPLAaerghvZ79z4yAulJqgg5FeQczy9RQRSF9rTqRyhlcIVnppm5sgsrxNMjp49wLZ2Ubc0n8Ppujv2Dy93uMKH8C1MugeiGI5AnUgoWFF/awyn9Aq2QirCc/dztD/xjse6D14PFclU4scFujlSQDqH681QQZv0OGtj2IBEIIyHNXEoWTQVh2G9H0TiMVZD9EpWsQxeuw9D7zWpO2hzScV1pd9fiWhdaWV0BuhyobJs7p7IHKoquhyxPsSY24s4xBTAlPh0XgXgpbqKfjUAK7F2AKfHpuAjkGwukKzdX0ChVghJdIeyVvbv9u2tBJkr0FC7UayvUnXjDQLHkNJ7VsetUBaF8uSdD2qQrDSf14ZSErwQiAjmPRgRy7G33CTUC2fz90u6BET2EoBVS6bFSQc7JPJG0u7ingnyi3AEVgfQGbsIR/FUWi1qlHrq+zFcZdZXF2s7rPgOaElbliycypRsjpdK5fKF2b0KACs9ai+VumAbjHRt2wadWKQJh0Xab9CqJ0aTi8iwC2SCcCqIT3T2lqxwBFZJSCV4SyMfF1vdB6EZWeMp3Z20FYHpqNmVdGK2P34+nYVfi585J9+DG4RUnhH+0wVWsAjD12hEIpdRznIJZZV3cBEDtEOVAx8cpsUYgn0jTiqgERiEl7Xl0aTyuUNYSgWwSSyzWOYHc5tDNsBFIL/0/zmJNZdWrrSuvttDTqKoB7ENwbkmUwNB7UAtwlv2peGkleMWjT+9XseTv4MTIT492tiMCOSIQgZyzIgLZ4JIKwvKvW8lTQc7xlZLTtgdRArHaMzPqHEe5b3wqTSx9N8pNAFVFVuye2w9VwqL2TuHShJBdvnTu5xeLpWwqAnlAqzxgVPC9Ik0EMiUFZvEikE+cUkEYYVJBLgSqZMq1Gq9nj8V64hOLpTNxz5+ygtByrnjtasluQBVRXGVAt4LoIXjtiqlE5do9unplfmrXlRi5fVQEskHu3YKk5KJJ5DaOkqtrRql1onuIQOCv2lFA9w2vki1SQc4PDCrsFQIrMfw5VpmfilzhxFgF+ZjIepvXzTgTZFYCr5x5X807FWx37w5Bu2smnkIruHTrcbB351T2bv+6ewRynrXpMxI3uFPXKSS5inUEAqOhZGk3i9J7uGVYqUpVcohAdLvnYg/peRimJIdUEICykikjkC8sEHqsCzj1/xBlTkXZyhquxtK10XG3+7gnYzSLumvZz09FP1WR3cSxghMKhuXP/tCTgCq4ymJWgDGxtqk9UJtYkdldSwTyREDBMAK5UNDE0eM7sm+VxBQi0EZ8hcjdaua6CAUX/C7WRCbuNjRhTypSKp+5++32+PNzSorbeGpPlDcaKNaKyOk6XWzfEb/9PSKQz2gpp2tuFqWWVXmdxCVlBHIu0whkg4vb80QgPbmUanZl77q+aUX8IpAI5I5AKkgv8ru9vWFFffPvHEez9ju8PcVBsW10zhXjlGxPs7ZiE6s9TWDo7i8CgS9cUnF2lmAFuSfmdAk0lYwikIEoUpJOBY0e864O7gB07RQRyBOiAxaVxXLL6QrSKGfXV02fUq6pIKuqscJmuOtSqtufhLWbqNzrDjhFIOdydolIj12V+aeCvd2pUmmd66aSkbt397oIpDUcjwEKga8IlArCwKYVS4lJBLLBnmbDqaxGLWQE8sUEQpW837ZChBUWpLIAU3tybMZUf6IIm1HSr5B0/hV7d3nW8bWK7cjLiu7CletoYNx3h14B8epQYAVJKA7dOMWudHORz93EOHHdK7GNQC6iu7ryKMkhFeS8L6TieUkgH4H6/0m6GwglG9FNKQRdcRxN+wwXfBez6n5V9ZyqrLRaVtXz3Zi5XLqtc/nbvFM2g/YZtGEntuBqjHsP92SF3i8CeUaMYt0ljgjEUAolrJIcJipWBBKB3BFQiOBaggnCRiDnhP1rLZYSULdfmSCsIhDq9ZWq4JZvo1hJl7zitemNKE4KP2gPSdeojKsOSw49SARyDq1y4lT1SkrgnLERiI5aBPI8pCvRo5lxb/EUUurh065Q1kIra5U0K6v0LSpIlQ2rsuiCv8L3u3O6VpDar6lqTe2JGxPXzioCqbCmjwWUvkZJO+VfuY1AHggo5IpAHphFIBvr0p0lK4q9GqtYIHo/GsQI5IkodQ4U2y7zp4JANkcg50DFYvW4QIqdDivfxaKkpOO6BleZ58r+Kdm+spAU1KnG2N37ih7Lzf60b1Pmd+PptgeH3vBmsX/+Y3fkdQWAEtwVJdP1/RHIeY+lEPhKoEriqEQegWzQUYSWCtLXOEp0JTH2dz0XnZuM/miBrNgUFQENbtfk0YAeSmvxk0B0TrrXvfVU9uRW5Ims7R4BU/w6XFZYOsliRSB9NnR7ACVrU7uizBmBMJmOfGHKzXjVdakgT3QikJ7MFKN+pl9HRCAXiK1u/JVsT4OvzJkKwqRSfh+EPhSa8u+0aihEcJs8uhalCrproTbOxUV50Kv0Va5Fd3oLZe9MGo9REQioIAqgblJR7nFFPIUk7jojkA36EyAqWZNmbZcICgnpWlJB9F5JicO77eXBDX38g/WgkJbByn5NAeWWcpoA3ONMxbq4e6BxUPZA40LJe5tPGbvi/lc4dYnQftWEBiYCYRk2AqGyYHhSKxiBFLingjzA6UhyBaFSFZSxVCrunFXcY7GMHkuxJ1R0FQneTdh33+8drmKsgnwEdPyH4+gzBMWj0zkr8JUDA5rFFKLTzKVkRndOiqciHrqWPWZuXNw9KPdb8rtYKxZO54xAziVbnfwpgqwSQgRygc4U+DRQUgYYeOnQrSaKNaOHHlNz0oSTClKwrQKRAqxYkIns5Ip1SgTvJvrE6VdleaiX7/oKmvxc+7VKyOWPNkQgvWxWEGhqzn71vR2bWksEYkRjhepTQYxA7C6hvYRCemUsrcJu9aT7u82fCvIin1Zk2Kk53a1RAimkV8b+NQKpFEo3TMe5wVSuU6rLhL10id75eYdACk60siuHJZRL9N63+dz707W0FUSZ6GpsBNL7/I68U0Lr7vPzc0pSl6DuSZzbwFf77p7FjfyyYpXxIpAIpKuI1NL9FoHcKhXNLFelXckINOsoc1aWY3U2VLBz10KvU9ZCE9cKW6oQ3d17JTrKwbvFikAe4VJAU4hY2dSreSpSuvd2SRmBpIJEIDv1uAcU9DpXrEpySAW5QMvNvqkgT0Ap0d3q8tcKhPrSzpJMvIaiZItq7OpgKyShdmtKrPT0S+n3KEdWJCq6n3vvYL6Dt7+H/bM/LlBTxKfzRCDnSFEL0h2DXok+Ain+tJmrXkp6ZVwEEoEoFbmsIArxlJs6WYZWqM7Prjgm3N7TzZTduq9ioeyHVglqUZU1r7Bt1Tqn+LK/x8gxryIsN6O7/l0h1BXxXe+r4EITjrKfCOQRASV+EQhkLX26mwpyDmgqCPzzygdFFqcLtHdRMoKScVNBjmRXsK6qvFvNaPyWWayPiS9/tIFmUZiU78OolVghLHfOynsrpzwUJyX70n5IOY6esMEK1m4PRPeuxO+w7gjkAYlildxsGIGcYx2BbBBIBellkgryxMi1WNQadvPbp1hVeesp8BjRLY7OQ0utIk66tqnKs2IPbmZ2/TyN1wq7V91b2c9hbTd3QTd2NU4h3nYOSkJlfQr4LoEomV3vO7UHd38KoZTY/Byr7I/2QxEIjIQCvkugCAQG42KYEqMI5DWsD1cr4Ecg5wikgjxxefvLihO2asr3u0SgllJptlccqVPb4cbEjcN+Xe79q0peWXkav9scEcgFi+gpiFKF6PHwBGG6wjwhyAhkg7LyQGzCN9Lsd1f55um80hingjzQcgUZgXRp6PNz10q42Rcu6zBMWSdtxFeLbE/gFT2WsgdqT5SESnng7l1Zy34stlh0E7dx1Eq4c0YgmyZy4JtzEcg5nlIP4pJZUe9UY3W11lQQXVi/O37Urk+JPBVkox7aqE6V9sq20QTkrmU/f0W8iUTlCktZ54o9SAJxF0D9u9JQVwSilk5pRumcVUDpmjvL6GZRGj8qzlfsM92DghndnyLWCKRj4+fnEcgDCMWWUpujnH7RZEvvvRd5l+Dw30lXFkA3lQrC1Eqz75T9ohbLzcx/rUBYuI6jFPHQzEyfbShrngooJZBiXWhSUcg1gbVreaaSX4XLBL4dJ5a/7u6W6AjkGX5aQRRS0gOKCGTx6+4RyDnFlAQQgTwwpA8pz3qnq0qUCrJBpgPjyjq5FnLCAuyDHYvFTDVNQB0n8B/QYct6jJpaXOX1r9azgkD7e9H9KddRfJVG3D0Gpf0QXXNl/bo5lKN5ygklcUUgn2h1mYSKVak2HTnOPo9AdNResWYRSASCK37nDih1XyEsvUdVBX97BVEWQMsiLbVKBqfHoE5Qzq6hlkexZitOoyaqpbIHaulcYblV954QPi5++UcbFAK5RHevq4IdgTzQUZLKVGZ2EqNCVXry1/WsEYhhsZSEcCVQN9j7e6eCnEcjArlgqZINU0FSQdoKcquwbkYk1ymEpeWb2q0q23Zrp0e57unXK76Y4kR7CQUn2ke94wExrRJdrMseKALpS7Ria2hCiECeqFKbOIVZJZjDPSKQCCQV5Fyst38d+U76HmBaht9xnbsWWpYVK+FYo3uQzO+dT1gsajWrWCoCfAcnqtju9xuBUCVcjItAXqvAHfxucpi4LhWkiw74PAKJQABNfh0yoV73OYHSyMkbO7kgAvnGAqHHqVOkXOF3qQgUQdI5KX50vts4BWuaqJSjarevUfY4PfaV2OLvpFeLVoKmNEi0qZ0g4isgXu1pYl1dg0ufBdDj5zMRvnN/0+JQ9nN27wjkE5UI5EkPisWKBPDtBfIVyj59RUXpT2iGp+O6ykOJOGV7qej265oQoYvZbS1vryARyIMCiu93MaMW9R22NwLZoPzVfXEqyCPYSmaOQCKQA2lisfS+5ttbLNf70mylnKhRf6vMucKuuJhVFovaNnpvZZySOFybONU7vb0HoUBOeXRq96p1RSA0amxcBLLg5bpUkJ58StZMBTnHc4/hX/myolJdqOVxm8jKM1NRd9R/9/cl3HXTdXb7vfp8KkbK/SOQT7SmwKeZmfY/+9OiqSqhzLMlFL1O2R8l7FSM6P1u4yKQCOTAl1SQJyQRSAQSgRQlZeTPHygnQLRcK3PSsk/7kb2teXefodyPWhmlKtCxU6d7KzhBbVTXz0Ygn0h2QF0FkZKpCxidpyKlklQqUk6sxe0XqOD3e5263wHfjxv9kT/7owQ7FaST39xrIfS50hRh+509RkzdLwK5QDwV5AlMKsgTi/8A1sIpGyTQz2wAAAAASUVORK5CYII=" style="display: block;"></div>
                             </div>
                         </div>
                     </div>
                  </div>
               </div>';
        $html .= '</body>';
        $html .= '</html> ';


        $flname = $id;

        if ( !is_dir( WWW_ROOT . "/uploads/guestpass" ) ) {
            mkdir( WWW_ROOT . "/uploads/guestpass" );
        }

        $dir  = WWW_ROOT . "/uploads/guestpass/" . $flname;

        if ( !is_dir( $dir ) ) {
            mkdir( $dir );
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        // print_r($fontData);exit;
        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                WWW_ROOT . '/font',
            ]),
            'fontdata' => $fontData + [ // lowercase letters only in font key
                    'basiercircle' => [
                        'R' => 'BasierCircle-Regular.ttf',
                        'I' => 'BasierCircle-RegularItalic.ttf',
                    ]
                ],
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 11,
            'default_font' => 'basiercircle',
            'margin_top' => 26,
            'tempDir'=> WWW_ROOT . '../tmp'
            // 'margin_header' => 50
        ]);
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf->writeHTML($html);

        $output = $mpdf->Output('guestPass.pdf', 'S');


        $name = str_replace(' ', '_', $name);

        $fileName = $name . "_" . rand() . ".pdf";

        file_put_contents($dir . "/" . $fileName, $output);

         $temp_file_location = $dir . "/" . $fileName;


        $fileNameHash = hash('sha256', $dir . "/" . $fileName);

        $conn = ConnectionManager::get("default"); // name of your database connection
        $verifiers_data = $conn->execute("SELECT * FROM verifiers");

        $verifiers = $verifiers_data->fetch('assoc');

        $verifierAkcessId = $verifiers['verifierAkcessId'];
        $verifierName = $verifiers['verifierName'];
        $fileName_ak = $verifiers['fileName'];
        $akData = $verifiers['akData'];

        $file_ak_link = WWW_ROOT . "/uploads/tempfile/" . $fileName_ak;
        $fp = fopen($file_ak_link, "w");
        fwrite($fp, $akData);
        fclose($fp);

        $file_Ak = WWW_ROOT . "/uploads/tempfile/" . $fileName_ak;

        $documentID = $verifierAkcessId . "-" . strtotime("now");

        $akcessIdHash = hash('sha256', $verifierAkcessId);

        $response = $this->Global->getToken();

        $data = array(
            "response"          => $response,
            "verifierAkcessId"  => $verifierAkcessId,
            "verifierName"      => $verifierName,
            "file_Ak"           => $file_Ak,
            "akData"            => $akData,
            "documentID"        => $documentID,
            "akcessIdHash"      => $akcessIdHash,
            "fileNameHash"      => $fileNameHash,
            "temp_file_location"=> $temp_file_location,
            'channelName'       => 'akcessglobal',
            'domainName'        => ORIGIN_URL,
            'company_name'      => COMP_NAME
        );

        $fileUrl = $fileName;
        $transactionID = 'dfsf';

        $transactionIDV = 'dfy';//$response_dverify->txId[0];

        $this->loadModel('VerifyDocs');
        $verifyDocs = $this->VerifyDocs->newEntity();
        $verifyDocs->verifierAkcessId = $verifierAkcessId;
        $verifyDocs->userAkcessId = $verifierAkcessId;
        $verifyDocs->expiryDate = null;
        $verifyDocs->txId = $transactionIDV;
        $verifyDocs->verifierName = $verifierName;
        $verifyDocs->documentId = $documentID;

        $saveVerifyDocs = $this->VerifyDocs->save($verifyDocs);

        $saveVerifyDocsId = $saveVerifyDocs->id;


        unlink(WWW_ROOT . "/uploads/tempfile/" . $fileName_ak);

        $this->loadModel('GuestPass');

        $updateIDCard = $this->GuestPass->updateAll(
            [
                'fileName'        => $fileName,
                'fileUrl'         => $fileUrl,
                'documentId'      => $documentID
            ],
            [
                'id' => $id
            ]
        );

        return true;
    }
}

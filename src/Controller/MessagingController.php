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
class MessagingController extends AppController
{

    
   public function isAuthorized($user)
    {
        parent::isAuthorized($user);
        
        $this->loadComponent('Global');
        
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
       
        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->loadModel('Activity');

        $conn = ConnectionManager::get("default"); // name of your database connection     

        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {        
            $query = 'SELECT * FROM users where soft_delete = 0 AND status = 1 ORDER BY created DESC';
        } else {
            if (in_array('Teacher', $session_user_role)) {
                $query = 'SELECT ca.userId as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND s.fk_user_id = "'.$user_id.'"';
            } else if(in_array('Student',$session_user_role)) {     
                $query = 'SELECT s.fk_user_id as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND ca.userId = "'.$user_id.'" GROUP BY s.fk_user_id';
            }
        }

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetchAll('assoc');
        
        $users = array();

        foreach($query_response as $key => $value) {  

            $fk_user_id = $value['id'];

            $response_data = $conn->execute("SELECT * FROM users WHERE id = '" . $fk_user_id . "' AND soft_delete = '0'");

            $responseDetails = $response_data->fetch('assoc');
            
            $users[] = array(
                'name' => $responseDetails['name'],
                'akcessId' => $responseDetails['akcessId']
            );
        }
        
        $this->set('countActivity', $this->Activity->find('all')->count());
        
        $this->set(compact('users'));

        $this->loadModel('Users');

        $users_notification = $this->Users->find('all',array('conditions' => ['status' => 1, 'soft_delete' => 0] ));

        $this->set('users_notification', $users_notification);
        
        $this->set('page_title', 'Messaging');

        $this->set('page_icon', '<i class="fal fa-comment-alt mr-1"></i>');

        if(in_array('Admin',$session_user_role))
        {
        $send_data_query = 'SELECT s.id, u.name as username, s.ackessID, s.message, s.createdDate, s.group_type FROM sendData as s LEFT JOIN users as u ON u.akcessId = s.ackessID where s.soft_delete = 0 AND s.send_type = "users/akcessId" AND s.message IS NOT NULL GROUP BY s.group_id ORDER BY s.id DESC LIMIT 5';
        } else {
            $akcessId = $this->Auth->user('akcessId');
            $send_data_query = 'SELECT s.id, u.name as username, s.ackessID, s.message, s.createdDate, s.group_type FROM sendData as s LEFT JOIN users as u ON u.akcessId = s.ackessID where s.soft_delete = 0 AND s.send_type = "users/akcessId" AND s.ackessID = "'.$akcessId.'" AND s.message IS NOT NULL GROUP BY s.group_id ORDER BY s.id DESC LIMIT 5';
        }
        $send_data_querySql = $conn->execute($send_data_query);
        $send_data_res = $send_data_querySql->fetchAll('assoc');
        $this->set('message_res', $send_data_res);

        if(in_array('Admin',$session_user_role))
        {
        $notify_query = 'SELECT n.id, u.name as username, u.akcessId as ackessID, n.msg as message, n.created as createdDate FROM notify as n LEFT JOIN users as u ON u.id = n.sentId where n.soft_delete = 0 ORDER BY n.id DESC LIMIT 5';
        } else {
            $user_id = $this->Auth->user('id');
            $notify_query = 'SELECT n.id, u.name as username, u.akcessId as ackessID, n.msg as message, n.created as createdDate FROM notify as n LEFT JOIN users as u ON u.id = n.sentId where n.soft_delete = 0 AND n.sentId = '.$user_id.' ORDER BY n.id DESC LIMIT 5';
        }
        $notify_querySql = $conn->execute($notify_query);
        $notify_res = $notify_querySql->fetchAll('assoc');
        $this->set('notify_res', $notify_res);
    }

    public function getMessageUsers($user_type=null)
    {
        $options = '';
        $conn = ConnectionManager::get("default"); // name of your database connection

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {
            $query = 'SELECT * FROM users where soft_delete = 0 AND status = 1 ORDER BY created DESC';
        } else {
            if (in_array('Teacher', $session_user_role)) {
                $query = 'SELECT ca.userId as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND s.fk_user_id = "'.$user_id.'"';
            } else if(in_array('Student',$session_user_role)) {
                $query = 'SELECT s.fk_user_id as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND ca.userId = "'.$user_id.'" GROUP BY s.fk_user_id';
            }
        }
        $querySql = $conn->execute($query);
        $query_response = $querySql->fetchAll('assoc');

        $fk_user_ids = '';
        foreach($query_response as $key => $value)
        {
            $fk_user_ids .= $value['id'].',';
        }

        if($fk_user_ids)
        {
            $fk_user_ids = rtrim($fk_user_ids,',');
        }


        if($user_type && $user_type != 5)
        {
            if($user_type == 4)
            {
                $cStart_date1  = date('Y-m-d').'T00:00:00.00';
                $cEnd_date1    = date('Y-m-d').'T23:59:59.999';
                $response_data = $conn->execute("SELECT u.name,u.akcessId  FROM `attendance` as a JOIN users as u ON u.id = a.fk_user_id WHERE a.checkin = 1 AND a.checkout = 0 AND u.soft_delete = '0' AND a.attendance_date_time BETWEEN '".$cStart_date1."' AND '".$cEnd_date1."' ORDER BY u.id DESC");
            }
            else
            {
                $uType = '';
                if($user_type == 1)
                {
                    $uType = 'Student';
                }
                else if($user_type == 2)
                {
                    $uType = 'Staff';
                }
                else if($user_type == 3)
                {
                    $uType = 'Teacher';
                }
                $response_data = $conn->execute("SELECT name, akcessId FROM users WHERE id IN (".$fk_user_ids.") AND soft_delete = '0' AND FIND_IN_SET('$uType',usertype) > 0");
            }
        }
        else
        {
            $response_data = $conn->execute("SELECT  name, akcessId FROM users WHERE id IN (".$fk_user_ids.") AND soft_delete = '0'");
        }

        $responseDetails = $response_data->fetchAll('assoc');

        if(!empty($responseDetails))
        {
            foreach($responseDetails as $r)
            {
                $options .= '<option value="'.$r['akcessId'].'">'.$r['name'].'</option>';
            }
        }
        echo $options;
        exit;
    }

    public function getNotificationUsers($user_type=null)
    {
        if($user_type && $user_type != 5)
        {
            $conn = ConnectionManager::get("default"); // name of your database connection
            if($user_type == 4)
            {
                $cStart_date1  = date('Y-m-d').'T00:00:00.00';
                $cEnd_date1    = date('Y-m-d').'T23:59:59.999';
                $response_data = $conn->execute("SELECT u.name, u.akcessId, u.usertype, u.id, u.email  FROM `attendance` as a JOIN users as u ON u.id = a.fk_user_id WHERE a.checkin = 1 AND a.checkout = 0 AND u.soft_delete = '0' AND a.attendance_date_time BETWEEN '".$cStart_date1."' AND '".$cEnd_date1."' ORDER BY u.id DESC");
            }
            else
            {
                $uType = '';
                if($user_type == 1)
                {
                    $uType = 'Student';
                }
                else if($user_type == 2)
                {
                    $uType = 'Staff';
                }
                else if($user_type == 3)
                {
                    $uType = 'Teacher';
                }
                $response_data = $conn->execute("SELECT * FROM users WHERE status = '1' AND soft_delete = '0' AND FIND_IN_SET('$uType',usertype) > 0");
            }
            $users_notification = $response_data->fetchAll('assoc');
        }
        else
        {
            $this->loadModel('Users');
            $users_notification = $this->Users->find('all',array('conditions' => ['status' => 1, 'soft_delete' => 0] ));
        }

        $options = '';
        foreach ($users_notification as $r)
        {
            $name = $r['name'] . " ( " . $r['akcessId'] . " ) ";
            $user_type = $r['usertype'];
            $val = $r['id'].'-'.$r['email'];
            $options .= '<option value="'.$val.'">'.$name . "( " . $user_type . ")".'</option>';
        }
        echo $options;
        exit;
    }


    public function sendMessage() {        
       
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        $message = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
        $ackessArray = isset($_REQUEST['ackess_id']) ? $_REQUEST['ackess_id'] : '';


        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        $session_user_role = explode(",", $role_id);

        $group_type = null;
        $group_id   = strtotime(date('Y-m-d H:i:s'));

        if(isset($_REQUEST['send_msg_to_all_user']) && $_REQUEST['send_msg_to_all_user'] == 1)
        {
            $user_type  = isset($_REQUEST['to_fiter']) && $_REQUEST['to_fiter'] ? $_REQUEST['to_fiter'] : 0;
            $group_type = $user_type;
            $conn = ConnectionManager::get("default"); // name of your database connection

            if(in_array('Admin',$session_user_role)) {
                $query = 'SELECT * FROM users where soft_delete = 0 AND status = 1 ORDER BY created DESC';
            } else {
                if (in_array('Teacher', $session_user_role)) {
                    $query = 'SELECT ca.userId as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND s.fk_user_id = "'.$user_id.'"';
                } else if(in_array('Student',$session_user_role)) {
                    $query = 'SELECT s.fk_user_id as id FROM sclasses as s LEFT JOIN class_attends as ca ON ca.classId = s.id where s.soft_delete = 0 AND ca.userId = "'.$user_id.'" GROUP BY s.fk_user_id';
                }
            }

            $querySql = $conn->execute($query);

            $query_response = $querySql->fetchAll('assoc');

            $ackessArray = '';
//            foreach($query_response as $key => $value) {
//
//                $fk_user_id = $value['id'];
//
//                $response_data = $conn->execute("SELECT * FROM users WHERE id = '" . $fk_user_id . "' AND soft_delete = '0'");
//
//                $responseDetails = $response_data->fetch('assoc');
//
//
//                if($responseDetails['akcessId'])
//                {
//                    $ackessArray .= $responseDetails['akcessId'].',';
//                }
//            }


            $fk_user_ids = '';
            foreach($query_response as $key => $value)
            {
                $fk_user_ids .= $value['id'].',';
            }

            if($fk_user_ids)
            {
                $fk_user_ids = rtrim($fk_user_ids,',');
            }


            if($user_type && $user_type != 5)
            {
                if($user_type == 4)
                {
                    $cStart_date1  = date('Y-m-d').'T00:00:00.00';
                    $cEnd_date1    = date('Y-m-d').'T23:59:59.999';
                    $response_data = $conn->execute("SELECT u.name,u.akcessId  FROM `attendance` as a JOIN users as u ON u.id = a.fk_user_id WHERE a.checkin = 1 AND a.checkout = 0 AND u.soft_delete = '0' AND a.attendance_date_time BETWEEN '".$cStart_date1."' AND '".$cEnd_date1."' ORDER BY u.id DESC");
                }
                else
                {
                    $uType = '';
                    if($user_type == 1)
                    {
                        $uType = 'Student';
                    }
                    else if($user_type == 2)
                    {
                        $uType = 'Staff';
                    }
                    else if($user_type == 3)
                    {
                        $uType = 'Teacher';
                    }
                    $response_data = $conn->execute("SELECT name, akcessId FROM users WHERE id IN (".$fk_user_ids.") AND soft_delete = '0' AND FIND_IN_SET('$uType',usertype) > 0");
                }
            }
            else
            {
                $response_data = $conn->execute("SELECT  name, akcessId FROM users WHERE id IN (".$fk_user_ids.") AND soft_delete = '0'");
            }

            $responseDetails = $response_data->fetchAll('assoc');

            if(!empty($responseDetails))
            {
                foreach($responseDetails as $r)
                {
                    if($r['akcessId'])
                    {
                        $ackessArray .= $r['akcessId'].',';
                    }
                }
            }
            $ackessArray = rtrim($ackessArray,',');
        }




        
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
        
        $type_method = 'ackess';
       
        if(isset($type_method) && $type_method == 'ackess') {

            if($ackessArray) {

                $ackess = explode(",", $ackessArray);
                foreach($ackess as $key => $value) {
          
                    $response_token = $this->Global->getToken();
                
                    $token = $response_token;

                    $origin_array = array(
                        'authorization: ' . $token,
                        'apikey: ' . $api,
                        'origin: ' . $origin_url,
                        'Content-Type: application/x-www-form-urlencoded'
                    );

                    $data_array = '?akcessId='.$value;

                    $type_method = 'users/akcessId';

                    $method = "GET";

                    $response_data_akcess_verify = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

                    $verify_check = json_decode($response_data_akcess_verify);

                    if(isset($verify_check->data->akcessId) && $verify_check->data->akcessId != "") {

                        $origin_array = array(
                            'authorization: ' . $token,
                            'apikey: ' . $api,
                            'origin: ' . $origin_url
                        );
                        
                        $data_array = array(
                            'title'         => AKCESS_NOTIFICATION,
                            'akcessId'      => $value,
                            'description'   => $message,
                            'sender'        => "portal",
                            "logo"          => $src,
                        );

                        $type = 'notifications';

                        $method = "POST";
                        
                        $response_data_akcess = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);

                        $response_akcess_Data = json_decode($response_data_akcess);
                        
                        $send_status = "error";
                        
                        if($response_akcess_Data->status == 1) {
                            
                            $send_status = "success";

                            $this->loadModel('SendData');

                            $sendData = $this->SendData->newEntity();
                            $sendData->fk_idcard_id = 0;
                            $sendData->ackessID = $value;
                            $sendData->response_id = $response_akcess_Data->data->_id;
                            $sendData->send_type = $type_method;
                            $sendData->send_status = $send_status;
                            $sendData->title = AKCESS_NOTIFICATION;
                            $sendData->message = $message;
                            $sendData->group_id = $group_id;
                            if($group_type)
                            {
                                $sendData->group_type = $group_type;
                            }
                            $savesendData = $this->SendData->save($sendData);
                            $saveSendId = $savesendData->id;

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id, 
                                'ackessID' => $value,
                                'response_id' => $response_akcess_Data->data->_id,
                                'send_type' => $type_method,
                                'send_status' => $send_status,
                                'title' => $message,
                                'description' => AKCESS_NOTIFICATION
                            );

                            $this->Global->auditTrailApi($saveSendId, 'senddata', $type, null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The message has been sent successfully.";
                            $this->request->session()->write('notification_message', 'The message has been sent successfully.');
                        
                        } else {
                            $data['message'] = "error";
                            $data['data'] = "Akcess ID is not found.";
                        }
                    } else {
                        $data['message'] = "error";
                        $data['data'] = "Akcess ID is not found.";
                    }
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Akcess ID is not found.";
            }
        } 
       
        print_r(json_encode($data));
        
        exit;
        
    }

    public function notify()
    {
        
        $this->loadModel('Notify');

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;

        $notify = $this->Notify->newEntity();

        if ($this->request->is(['patch', 'post', 'put'])) {

            $to = array();
            $ids = array();

            $all = $this->request->getData('astudents');

            foreach ($all as $k) {
                $val = explode('-', $k, 2);
                $to[] = $val[1];
                $ids[] = $val[0];
                # code...
            }
            if(array_key_exists(0, $to)){

                $msg = $this->request->getData('message');
                $subject = $this->request->getData('subject'); 

                $notify = $this->Notify->patchEntity($notify, $this->request->getData());
                $notify->students = implode(',',$ids);
                $notify->sentId = $_SESSION['Auth']['User']['id'];
                $notify->subj = $subject;
                $notify->msg = $msg;
                 
                $NotifyData = $this->Notify->save($notify);
                
                $response_token = $this->Global->getToken();

                $token = $response_token;

                $origin_array = array(
                    'authorization: ' . $token,
                    'apikey: ' . $api,
                    'origin: ' . $origin_url
                );
                
                if($to) {
                
                    $emaillist = $to;

                    $user_id = $this->Auth->user( 'id' );
                    $role_id = $this->Auth->user( 'usertype' );

                    $fk_user_ids = implode(',',$ids);
                    $conn = ConnectionManager::get("default"); // name of your database connection
                    $user_res = $conn->execute("SELECT  email, akcessId FROM users WHERE id IN (".$fk_user_ids.") AND soft_delete = '0'");

                    $userData = $user_res->fetchAll('assoc');

                    $akcessIds = array();
                    if(!empty($userData))
                    {
                        foreach($userData as $u)
                        {
                            $akcessIds[$u['akcessId']] = $u['email'];
                        }
                    }

                    foreach($emaillist as $key => $value) {

                        $email = $value;
                        $akcessId = array_search ($email, $akcessIds);

                        $data_array = array(
                            'akcessId' => $akcessId,
                            'title'      => COMP_NAME_TITLE,
                            'description'    => $msg,
                            'sender'    => 'portal'
                        );

                        $type_sent = 'sendAnnouncement';

                        $method = "POST";
                        
                        $response_data_akcess = $this->Global->curlGetPost($method, $type_sent, $api, $origin_url, $api_url, $data_array, $origin_array);

//                        $email = $value;
//
//                        $data_array = array(
//                            'subject' => $subject,
//                            'to'      => $email,
//                            'text'    => $subject,
//                            'html'    => $msg
//                        );
//
//                        $type_sent = 'send-email';
//
//                        $method = "POST";
//
//                        $response_data_akcess = $this->Global->curlGetPost($method, $type_sent, $api, $origin_url, $api_url, $data_array, $origin_array);



                        $response_akcess_Data = json_decode($response_data_akcess);

                        $send_status = "error";
                        
                        if($response_akcess_Data->status == 1) {

                            $send_status = "success";

                            $NotifyDataId = $NotifyData->id;
    
                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id, 
                                'email' => $email,
                                'response_id' => $response_akcess_Data->data->messageId,
                                'send_type' => $type_sent,
                                'send_status' => $send_status,
                                'subject' => $subject,
                                'message' => $msg
                            );

                            $type = 'notifications';
    
                            $this->Global->auditTrailApi($NotifyDataId, 'notifications', $type, null, $after);                                                       
                        
                        } 

                    }

                }
            }

            if($send_status == 'success') {

                $this->Flash->success(__('The Notification has been sent.'));

                return $this->redirect(['action' => 'index']);

            } else if($send_status == 'error') {
                
                $this->Flash->error(__('The Notification has been sent failed.'));


                return $this->redirect(['action' => 'index']);

            }
            
        }
    }
    
    
    public function sendPortalData() {     
        
        $conn = ConnectionManager::get("default"); // name of your database connection     
       
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        $type = isset($_REQUEST['eid']) ? $_REQUEST['eid'] : '';
        $ackessArray = isset($_REQUEST['ackess']) ? $_REQUEST['ackess'] : '';
        $portal = isset($_REQUEST['portal']) ? $_REQUEST['portal'] : '';

        ////print_R($ackessArray);
        //print_r($portal);
        //die();
               
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
     
        if($ackessArray) {

            $ackess = $ackessArray;
            
            foreach($ackess as $key => $value) {
      
                $response_token = $this->Global->getToken();
            
                $token = $response_token;

                $origin_array = array(
                    'authorization: ' . $token,
                    'apikey: ' . $api,
                    'origin: ' . $origin_url,
                    'Content-Type: application/x-www-form-urlencoded'
                );
                
                if(isset($value) && $$value != "") {

                    $origin_array = array(
                        'authorization: ' . $token,
                        'apikey: ' . $api,
                        'origin: ' . $origin_url
                    );
                    
                    // $data_array = array(
                    //     'title'         => AKCESS_NOTIFICATION,
                    //     'akcessId'      => $value,
                    //     'description'   => $message,
                    //     'sender'        => "portal",
                    //     "logo"          => $src,
                    // );

                    // $type = 'notifications';

                    // $method = "POST";
                    
                    // $response_data_akcess = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);

                    // $response_akcess_Data = json_decode($response_data_akcess);
                    
                    $send_status = "error";
                    
                    //if($response_akcess_Data->status == 1) {
                        
                        $send_status = "success";

                        $this->loadModel('SendToPortal');

                        $sql_send_to_portal = "INSERT INTO `send_to_portal` (`portal`, `akcessId`) VALUES ('".$portal."','".$value."')";

                        $conn->execute($sql_send_to_portal);

                        $sql_last_query = "SELECT id FROM `send_to_portal` ORDER BY id DESC LIMIT 0,1";

                        $sql_last_query_id = $conn->execute($sql_last_query);

                        $sql_last_id = $sql_last_query_id->fetch('assoc');

                        $lastInsertedId = $sql_last_id['id'];

                        $saveSendToPortalData = $lastInsertedId;

                        $after = array(
                            'user_id' => $user_id,
                            'role_id' => $role_id, 
                            'portal' => $portal,
                            'ackessID' => $value
                        );

                        $this->Global->auditTrailApi($saveSendToPortalData, 'send_to_portal', 'send_to_portal', null, $after);

                        $data['message'] = "success";
                        $data['data'] = "The student details sent to portal successfully.";
                        $this->request->session()->write('notification_message', 'The student details sent to portal successfully.');
                    
                    //} else {
                    //    $data['message'] = "error";
                    //    $data['data'] = "Akcess ID is not found.";
                    //}
                } else {
                    $data['message'] = "error";
                    $data['data'] = "Akcess ID is not found.";
                }
            }
        } else {
            $data['message'] = "error";
            $data['data'] = "Akcess ID is not found.";
        }
       
        print_r(json_encode($data));
        
        exit;
        
    }

    public function SendInvitationData() {      
        
        $conn = ConnectionManager::get("default"); // name of your database connection      
      
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        $messagee = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
        $messagep = isset($_REQUEST['messagep']) ? $_REQUEST['messagep'] : '';
        $type = isset($_REQUEST['eid']) ? $_REQUEST['eid'] : '';
        $selected_eform_id = isset($_POST['eFormId']) && $_POST['eFormId'] ? $_POST['eFormId'] : '';

        
        $inlineRadioOptions = isset($_REQUEST['inlineRadioOptions']) ? $_REQUEST['inlineRadioOptions'] : '';
        $type_method = $inlineRadioOptions;
        
        if($inlineRadioOptions == 'email') {
            $emailData = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
            $message = $messagee;
        } else if($inlineRadioOptions == 'phone') {
            $field_phone = isset($_REQUEST['field']) ? $_REQUEST['field'] : '';
            $message = $messagep;
        }
       
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );

        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

        $type_doc = 'eform'; 

        $response_token = $this->Global->getToken();
           
        $token = $response_token;
        
        if(isset($type_method) && $type_method == 'email') {          

            if($selected_eform_id)
            {
                $query_response = "SELECT * FROM `eform` where id=".$selected_eform_id." and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);
            }
            else{
            if ($type == 'staff') {                    

                $query_response = "SELECT * FROM `invitation_eform` where send_invitation_eform='Staff' and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);

            } elseif ($type == 'teacher') {

                $query_response = "SELECT * FROM `invitation_eform` where send_invitation_eform='Teacher' and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);

            } elseif ($type == 'student') {

                $query_response = "SELECT * FROM `invitation_eform` where send_invitation_eform='Students' and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);
            }
            }

            $data = $results->fetch('assoc');

            $eformID = isset($data['id']) ? $data['id'] : "";

            $emailArray = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';

            if($emailArray) {
                
                $emaillist = explode(",", $emailArray);
                
                foreach($emaillist as $key => $value) {
                    
                    $email = $value;
            
                    $response_token = $this->Global->getToken();

                    $token = $response_token;

                    $this->loadModel('SendData');

                    $eformID = $data['id'];
                    $formName = $data['formName'];

                    $sendData = $this->SendData->newEntity();
                    $sendData->fk_idcard_id = $data['id'];
                    $sendData->email = $email;
                    $sendData->send_type = $inlineRadioOptions;
                    $sendData->recievedType = $type_doc;
                    $sendData->soft_delete = 1;     

                    $savesendData = $this->SendData->save($sendData);

                    $saveSendId = $savesendData->id;

                    $passsaveSendId = $savesendData->id;

                    $response_token = $this->Global->getToken();

                    $token = $response_token;
                
                    $origin_array = array(
                        'authorization: ' . $token,
                        'apiKey: ' . $api,
                        'origin: ' . $origin_url,
                        'Content-Type: application/json'
                    );

                    $eformName = $formName;

                    $saveID = $this->Global->userIdEncode($eformID . "|||||" . $saveSendId . "|||||invitation");

                    $api_name = 'qrcode/eform';
                    
                    $fullurl = BASE_ORIGIN_URL.$api_name.'/'.$this->Global->userIdEncode($eformName).'/'.$saveID;
                    
                    $data_array ='{"eformurl":  "'.$fullurl.'"}';
                    
                    $method = "POST";

                    $type_method_link = 'firebase/generateeformdeeplinking';

                    $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method_link, $api_name, $origin_url, $api_url, $data_array, $origin_array);

                    $response_deeplinking_Data = json_decode($response_data_deeplinking);

                    if($response_deeplinking_Data->status == 1) {

                        $deeplink = $response_deeplinking_Data->data->shortLink;

                        $origin_array = array(
                            'authorization: ' . $token,
                            'apikey: ' . $api,
                            'origin: ' . $origin_url
                        );

                        $data_array = array(
                            'to'        => $email,
                            'template'  => "client-invitation",
                            'link'    => $deeplink,
                            'body' => $message
                        );

                        $type_sent = 'send-email-via-template';
    
                        $method = "POST";
                        
                        $api_url = AK_ORIGIN_URL_GLOBAL;

                        $response_data_akcess = $this->Global->curlGetPost($method, $type_sent, $api, $origin_url, $api_url, $data_array, $origin_array);
                        

                        $response_akcess_Data = json_decode($response_data_akcess);

                        $send_status = "error";
                        
                        if($response_akcess_Data->status == 1) {
                            
                            $send_status = "success";
    
                            $this->loadModel('SendData');
    
                            $sendData = $this->SendData->newEntity();
                            $sendData->fk_idcard_id = 0;
                            $sendData->email = $email;
                            $sendData->response_id = $response_akcess_Data->data->messageId;
                            $sendData->send_type = $type_method;
                            $sendData->send_status = $send_status;
                            $sendData->title = EMAIL_NOTIFICATION;
                            $sendData->message = $message;

                            $savesendData = $this->SendData->save($sendData);
    
                            $saveSendId = $savesendData->id;

                            $date = date('Y-m-d H:i:s'); 

                            $insert_query_users_by_akcess = "INSERT INTO `users_by_akcess` (
                                `fk_eform_id`, 
                                `fk_senddata_id`, 
                                `type`, 
                                `status`, 
                                `email`,
                                `created`,
                                `send_request_type`
                            ) VALUES (
                                ".$eformID.",
                                ".$passsaveSendId.",
                                '".$type."',
                                1,
                                '".$email."',
                                '".$date."',
                                'EMAIL'
                            )";
                
                            $conn->execute($insert_query_users_by_akcess);
    
                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id, 
                                'email' => $email,
                                'response_id' => $response_akcess_Data->data->messageId,
                                'send_type' => $type_method,
                                'send_status' => $send_status,
                                'title' => $message,
                                'description' => EMAIL_NOTIFICATION
                            );

                            $type = 'Invitation';
    
                            $this->Global->auditTrailApi($saveSendId, 'senddata', $type, null, $after);
    
                            $responseData['message'] = "success";
                            $responseData['data'] = "The Invitation has been sent successfully.";
                        
                        } 

                    }
                }
            } else {
                $responseData['message'] = "error";
                $responseData['data'] = "Link generation issue!";
            }
            
        }  else if(isset($type_method) && $type_method == 'phone') {
            
            if($selected_eform_id)
            {
                $query_response = "SELECT * FROM `eform` where id=".$selected_eform_id." and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);
            }
            else{
            if ($type == 'staff') {                    

                $query_response = "SELECT * FROM `invitation_eform` where send_invitation_eform='Staff' and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);

            } elseif ($type == 'teacher') {

                $query_response = "SELECT * FROM `invitation_eform` where send_invitation_eform='Teacher' and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);

            } elseif ($type == 'student') {

                $query_response = "SELECT * FROM `invitation_eform` where send_invitation_eform='Students' and 'soft_delete'=0 ORDER BY id DESC";

                $results = $conn->execute($query_response);
            }
            }

            $data = $results->fetch('assoc');

            $eformID = isset($data['id']) ? $data['id'] : "";

            if($field_phone) {
                
                foreach($field_phone as $key => $value) {
                    
                    $phone = $value['phone'];
                    $country_code = $value['country_code'];
                    
                    $this->loadModel('SendData');

                    $eformID = $data['id'];
                    $formName = $data['formName'];

                    $sendData = $this->SendData->newEntity();
                    $sendData->fk_idcard_id = $eformID;
                    $sendData->phone_no = $phone;
                    $sendData->country_code = $country_code;
                    $sendData->send_type = $inlineRadioOptions;
                    $sendData->recievedType = $type_doc;
                    $sendData->soft_delete = 1;     
    
                    $savesendData = $this->SendData->save($sendData);
    
                    $saveSendId = $savesendData->id;

                    $response_token = $this->Global->getToken();

                    $token = $response_token;
                
                    $origin_array = array(
                        'authorization: ' . $token,
                        'apiKey: ' . $api,
                        'origin: ' . $origin_url,
                        'Content-Type: application/json'
                    );

                    $eformName = $formName;

                    $saveID = $this->Global->userIdEncode($eformID . "|||||" . $saveSendId);

                    $api_name = 'qrcode/eform';
                    
                    $fullurl = BASE_ORIGIN_URL.$api_name.'/'.$this->Global->userIdEncode($eformName).'/'.$saveID;
                    
                    $data_array ='{"eformurl":  "'.$fullurl.'"}';
                    
                    $method = "POST";

                    $type_method = 'firebase/generateeformdeeplinking';
                                        
                    $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method, $api_name, $origin_url, $api_url, $data_array, $origin_array);
                
                    $response_deeplinking_Data = json_decode($response_data_deeplinking);

                    if($response_deeplinking_Data->status == 1) {
    
                        $deeplink = $response_deeplinking_Data->data->shortLink;

                        $origin_array = array(
                            'authorization:' . $token,
                            'apikey:' . $api,
                            'origin:' . $origin_url
                        );

                        $data_array_phone = array(
                            'countryCode'    => $country_code,
                            'phone'    => $phone,
                            'msg'    => $message . ". Please check your Eform Invitation. " . $deeplink,
                            'recievedType' => $type_doc
                        );
    
                        $method = "POST";

                        $type_url = 'send-sms';

                        $api_url = AK_ORIGIN_URL_GLOBAL;

                        $response_data = $this->Global->curlGetPost($method, $type_url, $api, $origin_url, $api_url, $data_array_phone, $origin_array);
    
                        $response_phone_Data = json_decode($response_data);

                        $send_status = "error";

                        if($response_phone_Data->status == 1) {
    
                            $send_status = "success";
    
                            $this->loadModel('SendData');
    
                            $this->SendData->updateAll(
                                [
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

                            $date = date('Y-m-d H:i:s'); 

                            $insert_query_users_by_akcess = "INSERT INTO `users_by_akcess` (
                                `fk_eform_id`, 
                                `fk_senddata_id`, 
                                `type`, 
                                `status`, 
                                `phoneno`,
                                `created`,
                                `send_request_type`
                            ) VALUES (
                                ".$eformID.",
                                ".$saveSendId.",
                                '".$type."',
                                1,
                                '".$country_code.' '.$phone."',
                                '".$date."',
                                'PHONENO'
                            )";
                
                            $conn->execute($insert_query_users_by_akcess);
    
                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_idcard_id' => $idcard->id,
                                'phone_no' => $phone,
                                'country_code' => $country_code,
                                'send_type' => $inlineRadioOptions,
                                'send_status' => $send_status,
                                'recievedType' => $type_doc,
                                'soft_delete' => 0
                            );

                            $type = 'Invitation';
    
                            $this->Global->auditTrailApi($saveSendId, 'senddata', $type, null, $after);

                            $responseData['message'] = "success";
                            $responseData['data'] = "The Invitation has been sent successfully.";
                        } 
                    } 
                }
            }  else {
                $responseData['message'] = "error";
                $responseData['data'] = "Link generation issue!";
            }
        } else {
            if(isset($type_method) && $type_method == 'email') {
                $responseData['message'] = "error";
                $responseData['data'] = "Email is not found!";
            } else if(isset($type_method) && $type_method == 'phone') {
                $responseData['message'] = "error";
                $responseData['data'] = "Phone No is not found!";
            }
            
        }
       
        print_r(json_encode($responseData));
        
        exit;
        
    }

    public function notification()
    {
        $role_id = $this->Auth->user( 'usertype' );
        $session_user_role = explode(",", $role_id);
        if(in_array('Admin',$session_user_role))
        {
            $notify_query = 'SELECT n.id, u.name as username, u.akcessId as ackessID, n.msg as message, n.subj, n.created as createdDate FROM notify as n LEFT JOIN users as u ON u.id = n.sentId where n.soft_delete = 0 ORDER BY n.id DESC';
        } else {
            $user_id = $this->Auth->user('id');
            $notify_query = 'SELECT n.id, u.name as username, u.akcessId as ackessID, n.msg as message, n.subj, n.created as createdDate FROM notify as n LEFT JOIN users as u ON u.id = n.sentId where n.soft_delete = 0 AND n.sentId = '.$user_id.' ORDER BY n.id DESC';
        }
        $conn = ConnectionManager::get("default"); // name of your database connection
        $notify_querySql = $conn->execute($notify_query);

        $notify_res = $notify_querySql->fetchAll('assoc');

        $this->set('notify', $notify_res);

//        $this->loadModel('Notify');
//
//        $notify = $this->Notify->find('all',array('conditions' => [ 'soft_delete' => 0] ));
//
//        $this->set(compact('notify'));

        $this->set('page_title', 'All notifications');

        $this->set('page_icon', '<i class="fal fa-bell mr-1"></i>');
    }

    public function messages()
    {
        $role_id = $this->Auth->user( 'usertype' );
        $session_user_role = explode(",", $role_id);
        if(in_array('Admin',$session_user_role))
        {
            $send_data_query = 'SELECT s.id, u.name as username, s.ackessID, s.message, s.createdDate, s.group_type FROM sendData as s LEFT JOIN users as u ON u.akcessId = s.ackessID where s.soft_delete = 0 AND s.send_type = "users/akcessId" AND s.message IS NOT NULL GROUP BY s.group_id ORDER BY s.id DESC';
        } else {
            $akcessId = $this->Auth->user('akcessId');
            $send_data_query = 'SELECT s.id, u.name as username, s.ackessID, s.message, s.createdDate, s.group_type FROM sendData as s LEFT JOIN users as u ON u.akcessId = s.ackessID where s.soft_delete = 0 AND s.send_type = "users/akcessId" AND s.ackessID =  "'.$akcessId.'" AND s.message IS NOT NULL GROUP BY s.group_id ORDER BY s.id DESC';
        }

        $conn = ConnectionManager::get("default"); // name of your database connection       
        $send_data_querySql = $conn->execute($send_data_query);

        $send_data_res = $send_data_querySql->fetchAll('assoc');

        $this->set('message', $send_data_res);

        $this->set('page_title', 'All messaging');

        $this->set('page_icon', '<i class="fal fa-comment-alt mr-1"></i>');
    }
}

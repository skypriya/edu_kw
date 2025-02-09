<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotifyTable $notify
 *
 * @method \App\Model\Entity\Notify[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class NotificationsController extends AppController
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

        $this->loadModel('Notify');

        $this->viewBuilder()->setLayout('admin');

    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $conn = ConnectionManager::get("default"); // name of your database connection
//        $this->loadModel('Audit');
//        $notify = $this->Audit->find('all',array('conditions' => ['action'=>'register'] ));

        $query_response = "SELECT a.id, a.action, a.created_at, a.status, u.name, u.akcessId FROM `audit_trail` AS a LEFT JOIN users AS u ON u.id = a.user_id WHERE a.action = 'register' OR a.action = 'eFormSubmit' ORDER BY a.status DESC";

        $results = $conn->execute($query_response);
        $notify = $results->fetchAll('assoc');

//        echo "<pre>";
//        print_r($notify);
//        exit;


//        $notify = $this->Notify->find('all',array('conditions' => [ 'soft_delete' => 0] ));

        $this->set(compact('notify'));

//        $this->loadModel('Users');
//
//        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );
//
//        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
//
//        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('page_title', 'Notifications');

        $this->set('page_icon', '<i class="fal fa-bell mr-1"></i>');
    }


    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function notify()
    {
     
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
                    
                    foreach($emaillist as $key => $value) {

                        $email = $value;

                        $data_array = array(
                            'subject' => $subject,
                            'to'      => $email,
                            'text'    => $subject,
                            'html'    => $msg
                        );

                        $type_sent = 'send-email';

                        $method = "POST";
                        
                        $response_data_akcess = $this->Global->curlGetPost($method, $type_sent, $api, $origin_url, $api_url, $data_array, $origin_array);

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

                $data['message'] = "success";

                $data['data'] = "The message has been sent successfully.";

                $this->request->session()->write('notification_message', 'The Notification has been sent.');

                print_r(json_encode($data));
        
                exit;

                return $this->redirect(['action' => 'index']);

            } else if($send_status == 'error') {

                $data['message'] = "error";
                $data['data'] = "The Notification has been sent failed.";
                
                $this->Flash->error(__('The Notification has been sent failed.'));

                print_r(json_encode($data));
        
                exit;

                return $this->redirect(['action' => 'index']);

            }
            
        }

        $this->set('page_title', 'Sent Notifications');

        $this->set('page_icon', '<i class="fal fa-bell mr-1"></i>');

        $this->loadModel('Users');

        $users = $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ));

        $this->set('users', $users);

        $this->set(compact('notify'));

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
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'notification', 'delete', $before, $after);
        
        $updateNotify = $this->Notify->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        
        if ($updateNotify) {
            $this->Flash->success(__('The notification has been deleted.'));
        } else {
            $this->Flash->error(__('The notification could not be deleted. Please, try again.'));
        }
       
        return $this->redirect(['action' => 'index']);
        
    }

    public function changeNotification($idEncode = null)
    {
        $returnArray = array('code'=>0,'msg'=>'Status can not changed, please try gain !');
        $id = $this->Global->userIdDecode($idEncode);
        $this->loadModel('Audit');
        $updateNotify = $this->Audit->updateAll(
            [
                'status' => 1
            ],
            [
                'id' => $id
            ]
        );

        if($updateNotify)
        {
            $returnArray = array('code'=>1,'msg'=>'Status changed successfully');
        }
        echo json_encode($returnArray);
        exit;
    }

    
}

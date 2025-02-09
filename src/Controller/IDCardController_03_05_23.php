<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Dompdf\Dompdf;
use Dompdf\Options;
use Helper;
use HtmlHelper;
use Cake\Datasource\ConnectionManager;
use DateTime;
use Cake\Routing\Router;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotifyTable $notify
 *
 * @method \App\Model\Entity\Notify[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class IDCardController extends AppController {

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
    public function recycle() {
        
        $this->loadModel('Countries');
        
        $this->loadModel('Users');
        
        $idcard = $this->IDCard->find('all' , array('conditions' => ['soft_delete' => 1]));    
       
        $users = $this->Users->find('all', array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0]));
        
        $countries = $this->Countries->find('all');
        
        $this->loadModel('Users');
       
        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
       
        $this->set(compact('idcard', 'countries', 'users'));
        
        $this->set('page_title', 'Recycle IDs');

        $this->set('page_icon', '<i class="fal fa-id-card mr-1"></i>');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {

        $conn = ConnectionManager::get("default"); // name of your database connection
        
        $this->loadModel('Countries');
        
        $this->loadModel('Users');

        $search = isset($this->request->query['s']) ? $this->request->query['s'] : "";

        if(isset($search) && $search != "") {
            $query_response = "SELECT `idcard`.*, `users`.`usertype`, `users`.`name`, `users`.`email` FROM `idcard` LEFT JOIN `users` ON `idcard`.`fk_users_id` = `users`.`id` WHERE `idcard`.`soft_delete` = 0 AND `users`.`usertype` like '%".$search."%'" ;
        } else {
            $query_response = "SELECT `idcard`.*, `users`.`usertype`, `users`.`name`, `users`.`email` FROM `idcard` LEFT JOIN `users` ON `idcard`.`fk_users_id` = `users`.`id` WHERE `idcard`.`soft_delete` = 0";
        }

        $results = $conn->execute($query_response);

        $idcard = $results->fetchAll('assoc');
        
        //$idcard = $this->IDCard->find('all' , array('conditions' => ['soft_delete' => 0]));    
       
        $users = $this->Users->find('all', array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0]));
        
        $countries = $this->Countries->find('all');
        
        $this->loadModel('Users');
       
        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
       
        $this->set(compact('idcard', 'countries', 'users', 'search'));
        
        $this->set('page_title', 'IDs');

        $this->set('page_icon', '<i class="fal fa-id-card mr-1"></i>');
    }

    /**
     * View method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);

        $idcard = $this->IDCard->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);

        $this->set('idcard', $idcard);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $idcard = $this->IDCard->newEntity();
        if ($this->request->is('post')) {
            $idcard = $this->IDCard->patchEntity($idcard, $this->request->getData());

            if (!empty($this->request->data['photo']['name'])) {

                $file = $this->request->data['photo']; // Creating a variable to handle upload

                $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
                $arr_ext = array('jpg', 'jpeg', 'gif', 'png'); //processing file extension
                //if extension is valid
                if (in_array($ext, $arr_ext)) {
                    //do the actual uploading of the file. First arg is the tmp name, second arg is
                    //where we are putting it

                    move_uploaded_file($file['tmp_name'], WWW_ROOT . '/uploads/users/' . $file['name']);

                    //saving file on database
                    $this->request->data['photo']['name'] = $file['name'];
                }
                $idcard->photo = $this->request->data['photo']['name'];
            }

            if ($this->IDCard->save($idcard)) {
                $this->Flash->success(__('The idcard has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The idcard could not be saved. Please, try again.'));
        }
        $this->set('page_title', 'Add IDCard');

        $this->set('page_icon', '<i class="fal fa-id-card mr-1"></i>');

        $this->set(compact('idcard'));
    }

    /**
     * Edit method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);

        $idcard = $this->IDCard->get($id, [
            'conditions' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            $idcard = $this->IDCard->patchEntity($idcard, $this->request->getData());

            //Check if image was sent
            if (!empty($this->request->data['photo']['name'])) {

                $file = $this->request->data['photo']; // Creating a variable to handle upload

                $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
                $arr_ext = array('jpg', 'jpeg', 'gif', 'png'); //processing file extension
                //if extension is valid
                if (in_array($ext, $arr_ext)) {
                    //do the actual uploading of the file. First arg is the tmp name, second arg is
                    //where we are putting it

                    move_uploaded_file($file['tmp_name'], WWW_ROOT . '/uploads/users/' . $file['name']);

                    //saving file on database
                    $this->request->data['photo']['name'] = $file['name'];
                }
                $idcard->photo = $this->request->data['photo']['name'];
            } else {
                $idcards = $this->IDCard->get($id);
                $idcard->photo = $idcards->photo;
            }

            if ($this->IDCard->save($idcard)) {
                $this->Flash->success(__('The idcard has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The idcard could not be saved. Please, try again.'));
        }

        $this->set('page_title', 'View IDCard');

        $this->set('page_icon', '<i class="fal fa-id-card mr-1"></i>');

        $this->set(compact('idcard'));
    }
    
    /**
     * Delete method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function restore($idEncode = null, $userIdEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $userId = $this->Global->userIdDecode($userIdEncode);
        
        $url = $_SERVER['HTTP_REFERER'];
        
        $this->request->allowMethod(['post', 'delete']);
        
        $doc = $this->IDCard->get($id);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_user_id' => $userId,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'idcard', 'delete', $before, $after);

        $updateIDCard = $this->IDCard->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id,
                'fk_users_id' => $userId
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
         
        if ($updateIDCard) {            
            $this->Flash->success(__('The id card has been deleted.'));
        } else {
            $this->Flash->error(__('The id card could not be deleted. Please, try again.'));
        }
        
        if(strpos($url, 'edit') !== false) {
            return $this->redirect(['controller' => 'Users', 'action' => 'edit', $userIdEncode]);
        } else if(strpos($url, 'view') !== false) {
            return $this->redirect(['controller' => 'Users', 'action' => 'view', $userIdEncode]);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($idEncode = null, $userIdEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $userId = $this->Global->userIdDecode($userIdEncode);
        
        $url = $_SERVER['HTTP_REFERER'];
        
        $this->request->allowMethod(['post', 'delete']);
        
        $doc = $this->IDCard->get($id);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_user_id' => $userId,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'idcard', 'delete', $before, $after);

        $updateIDCard = $this->IDCard->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id,
                'fk_users_id' => $userId
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
         
        if ($updateIDCard) {            
            $this->Flash->success(__('The ID card has been deleted.'));
        } else {
            $this->Flash->error(__('The id card could not be deleted. Please, try again.'));
        }
        
//        if(strpos($url, 'edit') !== false) {
//            return $this->redirect(['controller' => 'Users', 'action' => 'edit', $userIdEncode]);
//        } else if(strpos($url, 'view') !== false) {
//            return $this->redirect(['controller' => 'Users', 'action' => 'view', $userIdEncode]);
//        }

        return $this->redirect($this->referer());
    }

    public function pdfGenrate() {
        
        $conn = ConnectionManager::get("default"); // name of your database connection       
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
                        
        $this->loadModel('Users');
       
        $id = $this->Global->userIdDecode($this->request->data('a'));
        $idcard_type = $this->Global->userIdDecode($this->request->data('c'));
        
        $userDetails = $this->Users->get($id, array('conditions' => ['status' => 1, 'id' => $id]));
        
        $flname = $userDetails->id;
        
        $dir  = WWW_ROOT . "/uploads/attachs/" . $flname;
        
        if (!empty($this->request->data['attachs']['name'])) {

            $file = $this->request->data['attachs']; // Creating a variable to handle upload

            $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
            $arr_ext = array('jpg', 'jpeg', 'gif', 'png'); //processing file extension
            //if extension is valid
            if (in_array($ext, $arr_ext)) {
                //do the actual uploading of the file. First arg is the tmp name, second arg is
                //where we are putting it
                        
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }

                move_uploaded_file($file['tmp_name'], $dir . "/" . $file['name']);

                //saving file on database
                $this->request->data['attachs']['name'] = $file['name'];
            }
            $photo = $this->request->data['attachs']['name'];
            
            $id = $this->Global->userIdDecode($this->request->data('a'));
            
            $usersUpdate = $this->Users->get($id, [
                'conditions' => ['soft_delete' => 0]
            ]);
            
            $before = array(           
                'photo'             => $usersUpdate->photo
            );

            $after = array(
                'user_id'           => $user_id,
                'role_id'           => $role_id,
                'fk_user_id'        => $id,                
                'photo'             => $photo
            );

            //$lastInsertedId = $this->Global->auditTrailApi($id, 'users', 'update', $before, $after);
            
            $updateIDUser = $this->Users->updateAll(
                [
                    'photo'      => $photo
                ], 
                [
                    'id' => $id
                ]
            );
            
            $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        }
        
        $status_sucess = 0;
        
        if($this->request->data('a') != "") {
           
            $idCardExpiyDate = $this->request->data('idCardExpiyDateDoc');
            $id = $this->Global->userIdDecode($this->request->data('a'));
            $cardid = $this->Global->userIdDecode($this->request->data('b'));
            
            if($this->request->data('b') != "") {
                $query_data = $conn->execute('SELECT * FROM idcard where id='.$cardid);
                $query_idCard = $query_data->fetch('assoc');
            }
            
            $this->loadModel('VerifyDocs');
            
            //$userDetails = $conn->execute('SELECT * FROM users where usertype="Student" AND status=1 AND id='.$id);
            $userDetails = $this->Users->get($id, array('conditions' => ['status' => 1, 'id' => $id]));

            $name = isset($userDetails->name) ? $userDetails->name : '';
            $faculty = isset($userDetails->faculty) ? $userDetails->faculty : '';
            $expiyDate = isset($idCardExpiyDate) ? $idCardExpiyDate : '';
            $idNo = isset($userDetails->idcardno) ? $userDetails->idcardno : 0;
            $photo = isset($userDetails->photo) ? $userDetails->photo : 'user.png';
            $dob = isset($userDetails->dob) ? $userDetails->dob : '';
            $fk_users_id =  isset($id) ? $id : $userDetails->id;
                       
            // A few settings
            $img_file = $dir . "/" . $photo;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = $img_file;//'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
            
            $logo_image  = WWW_ROOT . "/img/generic_logo.png";
            
            //$logo_image = Router::url("/", true) . 'img/london-university.png';
            
            $logo_imgData = base64_encode(file_get_contents($logo_image));

            // Format the image SRC:  data:{mime};base64,{data};
            $src_logo = 'data:' . mime_content_type($logo_image) . ';base64,' . $logo_imgData;           

            $red_bg_image  = WWW_ROOT . "/img/red-bg.jpg";
            
            $red_bg_imgData = base64_encode(file_get_contents($red_bg_image));

            $src_red_bg = 'data:' . mime_content_type($red_bg_image) . ';base64,' . $red_bg_imgData;            

            $html = '';
            
            $html .= '<!DOCTYPE html>';
            $html .= '<html>';
            $html .= '<head>';
            $html .= '<style>';
            // $html .= '@page {margin: 0cm 0cm;}';
            $html .= 'body {margin-top: 2cm; margin-left: 2cm; margin-right: 2cm; margin-bottom: 2cm;}';
            $html .= '.card-box{background-color:#fff;padding:60px 40px;border:2px solid #909090;border-radius:5px;box-sizing:border-box;width:450px;margin:auto;position:relative;text-align: center;}';
            $html .= '.university-img{width:300px;object-fit:contain;}';
            $html .= '.profile-img{height:250px;width:250px;object-fit:contain;border-radius:120px;border:1px solid #eae7e7;margin-left:50px}';
            $html .= '.profile-img-container{padding: 60px 0px;}';
            $html .= '.name{font-size: 34px;text-transform: uppercase;color: #231f20;font-weight: 800;margin: 0;margin-bottom: 20px;}';
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
            $html .= '.detail{width: 100%;margin: auto;}';
            $html .= 'table{width: 230px;margin: auto;}';
            $html .= 'td{white-space: nowrap;font-size: 26px;line-height: 30px;text-align: left;}';
            $html .= 'td p{padding: 0 15px;}';
            $html .= '</style></head>';
            $html .= '<body>';
            $html .= '<div class="card-box">';
            $html .= '<div>';
            $html .= '<img class="university-img" src="'.$src_logo.'" alt="university-logo" />';
            $html .= '</div>';
            $html .= '<div class="profile-img-container" style="text-align:center;"> ';
            $html .= '<div style="width:250px;height:260px;background-image: url('.$src.');background-size: contain; background-repeat: no-repeat; background-position: center;border-radius: 50%;display:inline-block;border:5px solid #ededed;background-color: #ededed;margin:auto;"></div>';
            $html .= '</div>';
            $html .= '<div class="user-details" style="text-align:center;">';
            $html .= '<p class="name"><b>' . $name . '</b></p>';
            $html .= '<div class="detail mb-20">';
            $html .= '<div class="label-name-light" style="color:gray;font-size:28px;"><b>ID Number : ' . $idNo . '</b></div>';
            $html .= '</div>';
            $html .= '<table>';
            $html .= '<tbody>';
            $html .= '<tr>';
            $html .= '<td>DOB:</td>';
            $html .= '<td width="15%"><p></p></td>';
            $html .= '<td>' . h(date("M d, Y", strtotime($dob))) . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>VALID THRU:</td>';
            $html .= '<td><span></span></td>';
            $html .= '<td>' . h(date("M d, Y", strtotime($expiyDate))) . '</td>';
            $html .= '</tr>';
            if($idcard_type == 'Student' && $faculty != '') {
                $html .= '<tr>';
                $html .= '<td style="font-family:Basier-Circle">FACULTY:</td>';
                $html .= '<td><span></span></td>';
                $html .= '<td>' . h($faculty) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</body>';
            $html .= '</html> ';
            
            // instantiate and use the dompdf class
            // $dompdf = new Dompdf();

            // $options = $dompdf->getOptions();

            // $options->set(array('isRemoteEnabled' => false, 'isPhpEnabled' => false, 'isHtml5ParserEnabled' => false));

            // $dompdf->setOptions($options);

            // $dompdf->loadHtml($html);
            
            // $dompdf->setPaper('A4', 'portrait');

            // // Render the HTML as PDF
            // $dompdf->render();

            // // Output the generated PDF to Browser

            // $output = $dompdf->output();
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

            $output = $mpdf->Output('paymentReceipt.pdf', 'S');
            
            
            $name = str_replace(' ', '_', $name);

            $fileName = $name . "_" . rand() . ".pdf";

            file_put_contents($dir . "/" . $fileName, $output);

            $temp_file_location = $dir . "/" . $fileName;
                        
            $fileNameHash = hash('sha256', $dir . "/" . $fileName);

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

            //$response_document_add_data = $this->Global->getDocumentAdd($data);

            //$response_ddate = json_decode($response_document_add_data);
			//$response_ddate->statusCode = 200;
           
            //if($response_ddate->statusCode == 200) {                
                
                //                if($this->request->data('b') != "") {
                //                    $responseS3Delete = $this->Global->S3BucketDelete($query_idCard['fileName']);
                //                }
                //                                
                //                $responseS3 = $this->Global->S3Bucket($fileName, $temp_file_location);
                //
                $fileUrl = $fileName;
                
                $transactionID = $response_ddate->txId[0];
                $transactionID = 'dfsf';
               
                $data['expiryDate'] = date("Y-m-d\TH:i:s.000\Z", strtotime($expiyDate));

                //$response_document_verify = $this->Global->getVerifyDoc($data);   

                //$response_dverify = json_decode($response_document_verify);

                //if($response_dverify->statusCode == 200) {

                    //$transactionIDV = $response_dverify->txId[0];
                     $transactionIDV = 'dfy';//$response_dverify->txId[0];

                    $verifyDocs = $this->VerifyDocs->newEntity();
                    $verifyDocs->verifierAkcessId = $verifierAkcessId;
                    $verifyDocs->userAkcessId = $verifierAkcessId;
                    $verifyDocs->expiryDate = $expiyDate;
                    $verifyDocs->txId = $transactionIDV;
                    $verifyDocs->verifierName = $verifierName;
                    $verifyDocs->documentId = $documentID;

                    $saveVerifyDocs = $this->VerifyDocs->save($verifyDocs);
                       
                    $saveVerifyDocsId = $saveVerifyDocs->id;

                    $after = array(
                        'user_id' => $user_id,
                        'role_id' => $role_id, 
                        'fk_user_id' => $fk_users_id,
                        'verifierAkcessId' => $verifierAkcessId,
                        'userAkcessId' => $verifierAkcessId,
                        'expiryDate' => $expiyDate,
                        'txId' => $transactionIDV,
                        'verifierName' => $verifierName,
                        'documentId' => $documentID
                    );
                        
                    //$this->Global->auditTrailApi($saveVerifyDocsId, 'verifeddocs', 'insert', null, $after);
                    
                    $status_sucess = 1;
                    
                    //unlink(WWW_ROOT . '/uploads/pdf/' . $fileName);
                    
                    unlink(WWW_ROOT . "/uploads/tempfile/" . $fileName_ak);
                    
                    if($this->request->data('b') != "") {
                        
                        $idcardUpdate = $this->IDCard->get($cardid, [
                            'conditions' => ['soft_delete' => 0]
                        ]);

                        $before = array( 
                            'image_fileName'    => $idcardUpdate->image_fileName,
                            'fileName'          => $idcardUpdate->fileName,
                            'fileUrl'           => $idcardUpdate->fileUrl,
                            'documentHash'      => $idcardUpdate->documentHash,
                            'transactionID'     => $idcardUpdate->transactionID,
                            'timeStamp'         => $idcardUpdate->timeStamp,
                            'channelName'       => $idcardUpdate->channelName,
                            'documentId'        => $idcardUpdate->documentId,
                            'AkcessID'          => $idcardUpdate->AkcessID,
                            'idCardExpiyDate'   => $idcardUpdate->idCardExpiyDate
                        );

                        $after = array(
                            'user_id'           => $user_id,
                            'role_id'           => $role_id,
                            'fk_user_id'        => $fk_users_id,                
                            'fileName'          => $fileName,
                            'fileUrl'           => $fileUrl,
                            'documentHash'      => $fileNameHash,
                            'transactionID'     => $transactionID,
                            'timeStamp'         => date('Y-m-d H:i:s'),
                            'channelName'       => 'akcessglobal',
                            'documentId'        => $documentID,
                            'AkcessID'          => $verifierAkcessId,
                            'idCardExpiyDate'   => $expiyDate,
                            'image_fileName'    => $photo
                        );

                        //$lastInsertedId = $this->Global->auditTrailApi($cardid, 'idcard', 'update', $before, $after);
                
                        $updateIDCard = $this->IDCard->updateAll(
                            [
                                'fileName'        => $fileName,
                                'fileUrl'         => $fileUrl,
                                'documentHash'    => $fileNameHash,
                                'transactionID'   => $transactionID,
                                'timeStamp'       => date('Y-m-d H:i:s'),
                                'channelName'     => 'akcessglobal',
                                'documentId'      => $documentID,
                                'AkcessID'        => $verifierAkcessId,
                                'idCardExpiyDate' => $expiyDate,
                                'image_fileName'  => $photo
                            ], 
                            [
                                'id' => $cardid
                            ]
                        );
                        
                        //$this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                        
                    } else {

                        $idcard = $this->IDCard->newEntity();

                        $idcard->fk_users_id = $fk_users_id;
                        $idcard->fileName = $fileName;
                        $idcard->fileUrl = $fileUrl;
                        $idcard->documentHash = $fileNameHash;
                        $idcard->transactionID = $transactionID;
                        $idcard->timeStamp = date('Y-m-d H:i:s');
                        $idcard->channelName = 'akcessglobal';
                        $idcard->documentId = $documentID;
                        $idcard->AkcessID = $verifierAkcessId;
                        $idcard->idCardExpiyDate = $expiyDate;
                        $idcard->idNo = $idNo;
                        $idcard->image_fileName = $photo;

                        $saveIDCard = $this->IDCard->save($idcard);
                                                    
                        $saveIDCardId = $saveIDCard->id;

                        $after = array(
                            'user_id' => $user_id,
                            'role_id' => $role_id,       
                            'fk_user_id' => $fk_users_id,
                            'fileName' => $fileName,
                            'fileUrl' => $fileUrl,
                            'documentHash' => $fileNameHash,
                            'transactionID' => $transactionID,
                            'timeStamp' => date('Y-m-d H:i:s'),
                            'channelName' => 'akcessglobal',
                            'documentId' => $documentID,
                            'AkcessID' => $verifierAkcessId,                            
                            'idCardExpiyDate' => $expiyDate,
                            'idNo' => $idNo,
                            'image_fileName' => $photo,
                        );

                        //$this->Global->auditTrailApi($saveIDCardId, 'idcard', 'insert', null, $after);
                    }
                    
                //}
           //}
        }
        
        if ($status_sucess == 1) {
                
            $resultJ = json_encode(array('result' => 'success','msg' => 'The id card has been saved.'));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        } else {
            $resultJ = json_encode(array('result' => 'error','msg' => 'The id card could not be saved. Please, try again.'));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        }
    }
    
    public function sendData() {    
        
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
        $emailArray = isset($_POST['email']) ? $_POST['email'] : '';
        $ackess = isset($_POST['ackess']) ? $_POST['ackess'] : '';
        $field_phone = isset($_POST['field']) ? $_POST['field'] : '';      
        $idcardid = isset($_POST['idcardid']) ? $this->Global->userIdDecode($_POST['idcardid']) : '';      
        $inlineRadioOptions = isset($_POST['inlineRadioOptions']) ? $_POST['inlineRadioOptions'] : '';
        $type_doc = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
        $fileUrl = '';
        $description_title = "";
        if($type_doc == 'idcard') {
            
            $description_title = "ID Card";
            
            $this->loadModel('IDCard');
        
            $idcard = $this->IDCard->get($idcardid);

            $fk_users_id = $idcard->fk_users_id;

            $fileUrl = $idcard->fileUrl;

            $explode_filename = explode("_", $fileUrl);

            $doc_name = $explode_filename[0] . " " . $explode_filename[1];
            
            $email_message_type = "Hello, your student ID is ready, click save to add it to your IDs folder.";
            $phone_message_type = "Hello, your student ID is ready, click the link below to save it in your IDs Folder.";
            $akcess_message_type = "Hello! Your Student ID is ready. Open it to save it in your IDs folder";

            $api_qrcode = "idcard";
            
        } 
        elseif($type_doc == 'guestpass') {

            $description_title = "Guest Pass";

            $this->loadModel('GuestPass');

            $idcard = $this->GuestPass->get($idcardid);
            $fk_users_id = $idcardid;
//
//            $fileUrl = $idcard->fileUrl;
//
//            $explode_filename = explode("_", $fileUrl);
//
//            $doc_name = $idcard->invitee_name;


            $fileUrl = $idcard->fileUrl;

            $explode_filename = explode("_", $fileUrl);

            $doc_name = $explode_filename[0] . " " . $explode_filename[1];

//            $email_message_type = "Hello, your Guest Pass is ready, click save to add it to your ID folder.";
//            $phone_message_type = "Hello, your Guest Pass is ready, click the link below to save it in your calender.";
//            $akcess_message_type = "Hello, your Guest Pass is ready. Open the app to save it in your calender";

            $email_message_type = 'Hello, your Guest Pass sent by "'.COMP_NAME.'" is ready. Click on the link below to save it and add it to your calendar';
            $phone_message_type = 'Hello, your Guest Pass sent by "'.COMP_NAME.'" is ready. Click on the link below to save it and add it to your calendar';
            $akcess_message_type = 'Hello, your Guest Pass sent by "'.COMP_NAME.'" is ready. Open the app to save it in your calendar';

            $api_qrcode = "guestpass";

        }
        else if($type_doc == 'document') {
            
            $description_title = "Document";
            
            $this->loadModel('Docs');
        
            $idcard = $this->Docs->get($idcardid);

            $fk_users_id = $idcard->fk_users_id;

            $fileUrl = $idcard->fileUrl;
            
            $doc_name = $idcard->name;
            
            $email_message_type = "<p>Hello, Your received a new document, click save to add it to your Document folder.</p>";
            $phone_message_type = "Hello, Your received a new document, click the link below to save it in your Document folder.";
            $akcess_message_type = "Hello, Your received a new document, click save to add it to your Document folder.";

            $api_qrcode = "document";
        }
                
        $dir = '';
        if($fileUrl)
        {
            if($type_doc == 'guestpass')
            {
                $dir  = Router::url("/", true) . "uploads/guestpass/" . $fk_users_id . "/" . $fileUrl;
            }
            else
            {
                $dir  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $fileUrl;
            }
        }
                
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );

        $conn = ConnectionManager::get("default"); // name of your database connection   

        if($type_doc == 'guestpass')
        {
            $query_response = "SELECT guest_pass.email, guest_pass.akcessId, guest_pass.mobile as mobileNumber, guest_pass.country_code as calling_code FROM `guest_pass` WHERE guest_pass.id= '" . $fk_users_id . "'";
        }
        else
        {
            $query_response = "SELECT email, mobileNumber, akcessId, calling_code FROM `users` LEFT JOIN countries ON countries.id = users.country WHERE users.id='" . $fk_users_id . "'";
        }

        $results = $conn->execute($query_response);
        $data = $results->fetch('assoc');

        $type = '';
        $field_phone = array();
        if($inlineRadioOptions == 'phone') {

            $type = 'send-sms';

            $mobileNumber = $data['mobileNumber'];
            $calling_code = isset($data['calling_code']) ? $data['calling_code'] : '';

            $field_phone[] = array(
                'phone' => $mobileNumber,
                'country_code' => $calling_code,
            );

        } else if($inlineRadioOptions == 'email') {
            $type = 'send-email';

            $emailArray = $data['email'];
            

        } else if($inlineRadioOptions == 'ackess') {
            $type = 'ackess';
            $ackess = [$data['akcessId']];
        }

        if(isset($type) && $inlineRadioOptions == 'phone') {
        
            $response_token = $this->Global->getToken();
           
            $token = $response_token;

            if($field_phone) {
                
                foreach($field_phone as $key => $value) {
                    
                    $phone = $value['phone'];
                    $country_code = $value['country_code'];
                    
                    $fullphone = "+" . $country_code . $phone;
                    
                    $this->loadModel('SendData');

                    $sendData = $this->SendData->newEntity();
                    $sendData->fk_idcard_id = isset($idcard->id) && $idcard->id ? $idcard->id : $fk_users_id ;
                    $sendData->phone_no = $phone;
                    $sendData->country_code = $country_code;
                    if($ackess)
                    {
                    $sendData->ackessID = $ackess;
                    }
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

                    $eformName = $doc_name;

                    $saveID = $this->Global->userIdEncode($idcard->id . "|||||" . $saveSendId);

                    $api = 'qrcode/'.$api_qrcode;
                    
                    $fullurl = BASE_ORIGIN_URL.$api.'/'.$this->Global->userIdEncode($eformName).'/'.$saveID;
                    
                    $data_array ='{"eformurl":  "'.$fullurl.'"}';
                    
                    $method = "POST";

                    $type_method = 'firebase/generateeformdeeplinking';  

                    $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);
                   
                    $response_deeplinking_Data = json_decode($response_data_deeplinking);

                    if($response_deeplinking_Data->status == 1) {

                        $deeplink = $response_deeplinking_Data->data->shortLink;
                        $doc_id = isset($response_deeplinking_Data->data->doc_id) ? $response_deeplinking_Data->data->doc_id : "";

                        $origin_array = array(
                            'authorization: ' . $token,
                            'apikey: ' . $api,
                            'origin: ' . $origin_url
                        );

                        $data_array = array(
                            'countryCode'    => $country_code,
                            'phone'    => $phone,
                            'msg'    => $phone_message_type . " " . $deeplink,
                            'recievedType' => $type_doc
                        );

                        $method = "POST";

                        $api_url = AK_ORIGIN_URL_GLOBAL;

                        // print_r($method);
                        // print_r($type);
                        // print_r($api);
                        // print_r($origin_url);
                        // print_r($api_url);
                        // print_r($data_array);
                        // print_r($origin_array);

                        $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);

                        $response_phone_Data = json_decode($response_data);

                        //print_R($response_phone_Data );

                        $send_status = "error";
                        if($response_phone_Data->status == 1) {

                            $send_status = "success";

                            $this->loadModel('SendData');

                            $docid = $this->Global->userIdEncode($doc_id);

                            $this->SendData->updateAll(
                                [
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0,
                                    'document_id'        => $docid 
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

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

                            $this->Global->auditTrailApi($saveSendId, 'senddata', 'phone', null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The " . $description_title . " has been sent successfully.";
                        } else {
                            $data['data'] = "type is not set!";
                            $data['message'] = "error";
                        }
                    } else {
                        $data['message'] = "error";
                        $data['data'] = "Link generation issue!";
                    }
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Link generation issue!";
            }
        } 
        else if(isset($type) && $inlineRadioOptions == 'email') {
           
            if($emailArray) {
                
                $emaillist = explode(",", $emailArray);
             
                foreach($emaillist as $key => $value) {
                    
                    $email = $value;
            
                    $response_token = $this->Global->getToken();

                    $token = $response_token;

                    $this->loadModel('SendData');

                    $sendData = $this->SendData->newEntity();
                    $sendData->fk_idcard_id = $idcard->id;
                    $sendData->email = $email;
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

                    $eformName = $doc_name;

                    $saveID = $this->Global->userIdEncode($idcard->id . "|||||" . $saveSendId);

                    $api = 'qrcode/'.$api_qrcode;
                    
                    $fullurl = BASE_ORIGIN_URL.$api.'/'.$this->Global->userIdEncode($eformName).'/'.$saveID;
                    
                    $data_array ='{"eformurl":  "'.$fullurl.'"}';

                    $method = "POST";

                    $type_method = 'firebase/generateeformdeeplinking';                    
                    
                    $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);
                   
                    $response_deeplinking_Data = json_decode($response_data_deeplinking);

                    //print_R($response_deeplinking_Data);

                    if($response_deeplinking_Data->status == 1) {

                        $deeplink = $response_deeplinking_Data->data->shortLink;
                        $doc_id = isset($response_deeplinking_Data->data->doc_id) ? $response_deeplinking_Data->data->doc_id : "";
                        
                        $origin_array = array(
                            'authorization: ' . $token,
                            'apikey: ' . $api,
                            'origin: ' . $origin_url
                        );

                        $data_array = array(
                            'to'        => $email,
                            'template'  => "client-invitation",
                            'link'    => $deeplink
                        );
    
                        $type = 'send-email-via-template';
                        
                        $method = "POST";
                        
                        $api_url = AK_ORIGIN_URL_GLOBAL;
                        
                        $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);

                        $response_phone_Data = json_decode($response_data);

                        $send_status = "error";

                        if($response_phone_Data->status == 1) {

                            $send_status = "success";

                            $this->loadModel('SendData');

                            $docid = $this->Global->userIdEncode($doc_id);                            

                            $this->SendData->updateAll(
                                [
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0,
                                    'document_id'        => $docid 
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_idcard_id' => $idcard->id,
                                'email' => $email,
                                'send_type' => $inlineRadioOptions,
                                'send_status' => $send_status,
                                'recievedType' => $type_doc,
                                'soft_delete' => 0
                            );

                            $this->Global->auditTrailApi($saveSendId, 'senddata', 'email', null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The " . $description_title . " has been sent successfully.";
                        } else {
                            $data['message'] = "type is not set!";
                        }
                    } else {
                        $data['message'] = "error";
                        $data['data'] = "Link generation issue!";
                    }
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Link generation issue!";
            }
        }  
        else if(isset($type) && $inlineRadioOptions == 'ackess') {
            
            if($ackess) {
                
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

                    //$response_data_akcess_verify = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);
                    //$verify_check = json_decode($response_data_akcess_verify);

                    //if(isset($verify_check->data->akcessId) && $verify_check->data->akcessId != "") {
                        $id = $idcard->id;
                        $AkcessID = $idcard->AkcessID;
                        $fileName = '';
                        if($dir)
                        {
                            $fileUrl = $dir;
                            $fileName = $idcard->fileName;
                        }
                        $idCardExpiyDate = isset($idcard->idCardExpiyDate) && $idcard->idCardExpiyDate ? $idcard->idCardExpiyDate : '';
                        $documentId = $idcard->documentId;
                        $id = $idcard->id;

                        $origin_array = array(
                            'authorization: ' . $token,
                            'apikey: ' . $api,
                            'origin: ' . $origin_url
                        );

                        $expiryDate = $idCardExpiyDate ? date("Y-m-d\TH:i:s.000\Z", strtotime($idCardExpiyDate)) : '';

                        $this->loadModel('SendData');

                        $sendData = $this->SendData->newEntity();
                        $sendData->fk_idcard_id = $id;
                        $sendData->ackessID = $value;                
                        $sendData->send_type = $inlineRadioOptions;
                        $sendData->recievedType = $type_doc;
                        $sendData->soft_delete = 1;                        

                        $savesendData = $this->SendData->save($sendData);

                        $saveSendId = $savesendData->id;

                        $data_array = array(
                            'FileURL'    => $fileUrl,
                            'akcessId'    => $value,
                            'Description'    => $akcess_message_type,
                            'DocumentID' => $saveSendId,
                            'recievedType' => $type_doc,
                            'component_id' => $saveSendId,
                            'component' => $type_doc,
                            'domainName' => ORIGIN_URL,
                            'company_name' => COMP_NAME
                        );

                        if($expiryDate)
                        {
                            $data_array['idCardExpiyDate'] = $expiryDate;
                        }
                       
                        if($fileName)
                        {
                            $data_array['Pdf_name'] = $fileName;
                        }
                       
                        $type_method = 'incoming-pdf';

                        $method = "POST";

                        // print_r($method);
                        // print_r($type_method);
                        // print_r($api);
                        // print_r($origin_url);
                        // print_r($api_url);
                        // print_r($data_array);
                        // print_r($origin_array);


                        $response_data_akcess = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

                        $response_akcess_Data = json_decode($response_data_akcess);  

                        //print_R($response_akcess_Data);

                        $send_status = "error";

                        if($response_akcess_Data->status == 1) {

                            
                            $send_status = "success";

                            $this->loadModel('SendData');

                            $this->SendData->updateAll(
                                [
                                    'response_id'        => $response_akcess_Data->data->_id,
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_idcard_id' => $idcard->id,
                                'ackessID' => $value,
                                'response_id' => $response_akcess_Data->data->_id,
                                'send_type' => $inlineRadioOptions,
                                'send_status' => $send_status,
                                'recievedType' => $type_doc,
                                'soft_delete' => 0
                            );

                            $this->Global->auditTrailApi($saveSendId, 'senddata', 'akcess', null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The " . $description_title . " has been sent successfully.";
                            
                        } else {
                            $data['message'] = "error";
                            $data['data'] = "type is not set!";
                        }
                    //} else {
                    //    $data['message'] = "error";
                    //    $data['data'] = "Akcess ID is not found!";
                    //}
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Akcess ID is not found!";
            }
        }  else {
            //$data['message'] = "error";
            //$data['data'] = "Please select any one options!";
        }
        
        print_r(json_encode($data));
        
        exit;
        
    }

    public function getDataFROMData(){

        $conn = ConnectionManager::get("default"); // name of your database connection    

        $api = isset($_REQUEST['api']) ? $_REQUEST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_REQUEST['orurl']) ? $_REQUEST['orurl'] : ORIGIN_URL;
        $api_url = isset($_REQUEST['apiurl']) ? $_REQUEST['apiurl'] : AK_ORIGIN_URL;
        $akcess = isset($_REQUEST['faid']) ? $_REQUEST['faid'] : '';
        $label = isset($_REQUEST['label']) ? $_REQUEST['label'] : '';

        $sql_last_query_fields = "SELECT * FROM `users_by_akcess` WHERE akcessId='".$akcess."'";

        $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

        $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

        if(isset($sql_last_fields_id['id']) && count($sql_last_fields_id['id']) > 0) {
            // $dataArray = array(                
            //     'message' => 'This "' . $akcess . '" Akcess ID is already data requested for the "'.ucfirst($sql_last_fields_id['type']).'" user.',
            //     'status' => 'error'
            // );

            // $resultJ = json_encode($dataArray);
            // $this->response->type('json');
            // $this->response->body($resultJ);
            //return $this->response;
        }
        
        $response_token = $this->Global->getToken();

        $token = $response_token;

        $origin_array = array(
            'authorization: ' . $token,
            'apikey: ' . $api,
            'origin: ' . $origin_url,
            'Content-Type: application/x-www-form-urlencoded'
        );

        $data_array = '?akcessId='.$akcess;

        $type_method = 'users/akcessId';

        $method = "GET";

        //$response_data_akcess_verify = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);
        
        //$verify_check = json_decode($response_data_akcess_verify);

        $dataArray = array();

        //if(isset($verify_check->data->akcessId) && $verify_check->data->akcessId != "") {
            $akcessId = $akcess;  //isset($verify_check->data->akcessId) ? $verify_check->data->akcessId : "";
//            $email =  isset($verify_check->data->email) ? $verify_check->data->email : "";
//            $firstName =  isset($verify_check->data->firstName) ? $verify_check->data->firstName : "";
//            $lastName =  isset($verify_check->data->lastName) ? $verify_check->data->lastName : "";
//            $phone =  isset($verify_check->data->phone) ? $verify_check->data->phone : "";
//            $countryCode =  isset($verify_check->data->countryCode) ? $verify_check->data->countryCode : "";
            
            if($label == 'student') {
                $eid = 1;
                $label_name = 'Student';
                $this->SendEformData($akcessId, $eid, $label_name, $label);
            } else if($label == 'admin') {
                $eid = 2;
                $label_name = 'Admin';
                $this->SendEformData($akcessId, $eid, $label_name, $label);
            } else if($label == 'staff') {
                $eid = 3;
                $label_name = 'Staff';
                $this->SendEformData($akcessId, $eid, $label_name, $label);
            } else if($label == 'teacher') {
                $eid = 4;
                $label_name = 'Academic Personnel';
                $this->SendEformData($akcessId, $eid, $label_name, $label);
            }

            $dataArray = array(                
                'message' => 'The "' . $akcessId . '" AKcess ID data request has been sent successfully.',
                'status' => 'success'
            );
        //} else {
        //    $dataArray = array(                
        //        'message' => 'This "' . $akcess . '" Akcess ID data not found.',
        //        'status' => 'error'
        //    );
        //}

        $resultJ = json_encode($dataArray);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }

    public function SendEformData($akcessId, $eid, $label_name, $label) {        

        $conn = ConnectionManager::get("default"); // name of your database connection    
              
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;        
        $ackess = isset($akcessId) ? $akcessId : '';   
        $eid = isset($eid) ? $eid : '';      
        $type = strtolower($label_name);
       
        $id = $eid;
                
        $user_id = $this->Auth->user( 'id' );
        
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

        $sql_fields = "SELECT count(id) as count FROM `invitation_eform` where id=$id and 'soft_delete'=0 ORDER BY id DESC LIMIT 0,1";

        $sql_fields_id = $conn->execute($sql_fields);

        $sql_last_fields = $sql_fields_id->fetch('assoc');

        if($sql_last_fields['count'] > 0) {
        
            $user_id = $user_id;
            
            $date = date('Y-m-d H:i:s');       

            $this->loadModel('Fields');        
            
            $fieldsInformation_query = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));

            $eformFieldText = array();
            
            foreach($fieldsInformation_query as $key => $fieldsInformations){
            
                $field_id = isset($fieldsInformations->id) ? $fieldsInformations->id : ''; 
                $name = isset($fieldsInformations->labelname) ? $fieldsInformations->labelname : ''; 
                $instructions = isset($fieldsInformations->instructions) ? $fieldsInformations->instructions : ''; 
                $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : '';             
                $verification_grade = isset($fieldsInformations->verification_grade) ? $fieldsInformations->verification_grade : ''; 
                $fieldver = isset($fieldsInformations->file_verified) ? $fieldsInformations->file_verified : ''; 
                $signature_required = isset($fieldsInformations->signature_required) && $fieldsInformations->signature_required != "" ? $fieldsInformations->signature_required : 'no'; 
                $is_mandatory = isset($fieldsInformations->signatureis_mandatory_required) && $fieldsInformations->is_mandatory != "" ? $fieldsInformations->is_mandatory : 'no'; 
                $key = isset($fieldsInformations->keyfields) ? $fieldsInformations->keyfields : ''; 
                $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
                
                $key_value = $key;
                if (strpos($key, '_') !== false) { 
                    $key_value_explode = explode("_", $key);                
                    $key_value = $key_value_explode[0];
                }
            
                $this->loadModel('FieldsOptions');        
            
                $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                    'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                ]);
                
                $array_items = array();

                foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {

                    $optionsid = $fieldsOptionsInformations->uid;
                    $checked = $fieldsOptionsInformations->checked;
                    $lable = $fieldsOptionsInformations->lable;
                    $key = $fieldsOptionsInformations->keyfields;

                    $array_items[] = array(
                        "checked" => $checked,
                        "keyfields" => strtolower($key),
                        "lable" => $lable,
                        "uid" => $optionsid
                    );
                }
                
                $eformFieldText[] = array(
                    "options" => $array_items,
                    "labelname" => $name,
                    "key" => $key_value,
                    "keytype" => $typeIn,
                    "signature_required" => $signature_required,
                    "file_verified" => $fieldver,
                    "verification_grade" => $verification_grade,
                    "instructions" => trim($instructions),
                    "is_mandatory" => $is_mandatory
                );
            }

            $this->loadComponent('Global');        
                
            $response_token = $this->Global->getToken();
            
            $token = $response_token;  
            
            $description = "Get data from AKcess ID for " . $label_name;

            $insert_query = "INSERT INTO `users_by_akcess` (
                `fk_eform_id`, 
                `type`, 
                `status`, 
                `akcessId`,
                `created`
            ) VALUES (
                ".$id.",
                '".$label."',
                1,
                '".$ackess."',
                '".$date."'
            )";

            $conn->execute($insert_query);

            $sql_last_query_fields = "SELECT id FROM `users_by_akcess` ORDER BY id DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];

            $data_array = array(
                "akcessId" => $ackess,
                "description" => $description,
                "status" => "pending",
                "requestId" => $lastfieldsInsertedId,  
                "logo" => $src,        
                "type" => $type,                       
                "date" => $date,
                "fields" => $eformFieldText
            );

            $data_array = json_encode($data_array,JSON_INVALID_UTF8_IGNORE);
            $origin_array = array(
                'Content-Type: application/json',
                'authorization: ' . $token,
                'apiKey: ' . $api,
                'Origin: ' . $origin_url
            );    
                      
            $type_method = 'request-userinfo';

            $method = "POST";

            $response_data_akcess = $this->Global->curlGetPostEform($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);    
            
            $response_akcess_Data = json_decode($response_data_akcess);

            if(!empty($response_akcess_Data->status) && $response_akcess_Data->status == 1) {

                $requested_user_id = $response_akcess_Data->data->_ids[0];

                $update_query = "UPDATE `users_by_akcess` set `requested_user_id`='".$requested_user_id."' WHERE id=".$lastfieldsInsertedId;

                $conn->execute($update_query); 
            }

            $data['message'] = "success";
            $data['data'] = "The ".$label_name." form successfully send it.";
        } else {
            $data['message'] = "error";
            $data['data'] = "The ".$label_name." form could not be found. Please, try again.";
        }

        $resultJ = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
    
    public function getResponseData(){
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
                
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $vid = isset($_GET['vid']) ? $this->Global->userIdDecode($_GET['vid']) : 0;
        $vt = isset($_GET['vt']) ? $_GET['vt'] : '';
        
        $dataArray = array();
        
        if($type == 'viewemail') {
            
            $query_response = "SELECT email, response_id, send_status as status, createdDate as created FROM `sendData` WHERE email != '' AND fk_idcard_id=" . $vid . " AND recievedType='" . $vt . "' ORDER BY id DESC";
            $results = $conn->execute($query_response);

            $data = $results->fetchAll('assoc');
            
            foreach($data as $key => $datas) {
                $dataArray[] = array(
                    $datas["email"],
                    $datas["status"],
                    $datas["created"]
                );
            }
            
        } else if($type == 'viewphone') {
            
            $query_response = "SELECT country_code, phone_no, response_id, send_status as status, createdDate as created FROM `sendData` WHERE phone_no != '' AND fk_idcard_id=" . $vid . " AND recievedType='" . $vt . "' ORDER BY id DESC";
            $results = $conn->execute($query_response);

            $data = $results->fetchAll('assoc');
            
            foreach($data as $key => $datas) {
                $dataArray[] = array(
                    "+" . $datas["country_code"] . " " . $datas["phone_no"],
                    $datas["status"],
                    $datas["created"]
                );
            }
            
        } else if($type == 'viewackess') {
            
            $query_response = "SELECT ackessID as akcessId, response_id, send_status as status, createdDate as created FROM `sendData` WHERE ackessID != '' AND fk_idcard_id=" . $vid . " AND recievedType='" . $vt . "' ORDER BY id DESC";
           
            $results = $conn->execute($query_response);

            $data = $results->fetchAll('assoc');
            
            foreach($data as $key => $datas) {
                $dataArray[] = array(
                    $datas["akcessId"],
                    $datas["status"],
                    $datas["created"]
                );
            }
            
        }  
        
        $resultJ = json_encode($dataArray);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
    
    public function getReceivedResponseData(){
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
      
        $vid = isset($_GET['vid']) ? $this->Global->userIdDecode($_GET['vid']) : 0;
        $vt = isset($_GET['vt']) ? $_GET['vt'] : 0;
        
        $dataArray = array();
        $data = array();
        
        if($vt == 'idcard') {
        
            $query_response = "SELECT ir.akcessId, email, response_id, send_status, status,
                ir.created, country_code, phone_no, firstName, lastName FROM incomingreceived as ir
                JOIN sendData as sd ON ir.incomingreceived_id = sd.id
                WHERE sd.fk_idcard_id=" . $vid . " AND ir.recievedType = 'idcard'";
            $results = $conn->execute($query_response);
            $data = $results->fetchAll('assoc');
        } else if($vt == 'document') {
        
            $query_response = "SELECT ir.akcessId, email, response_id, send_status, status,
                ir.created, country_code, phone_no, firstName, lastName FROM incomingreceived as ir
                JOIN sendData as sd ON ir.incomingreceived_id = sd.id
                WHERE sd.fk_idcard_id=" . $vid . " AND ir.recievedType = 'document'";
            $results = $conn->execute($query_response);
            $data = $results->fetchAll('assoc');
        } else if($vt == 'eform') {
        
            $query_response = "SELECT ir.akcessId, email, response_id, send_status, status,
                ir.created, country_code, phone_no, firstName, lastName FROM incomingreceived as ir
                JOIN sendData as sd ON ir.incomingreceived_id = sd.id
                WHERE sd.fk_idcard_id=" . $vid . " AND ir.recievedType = 'eform'";
            $results = $conn->execute($query_response);
            $data = $results->fetchAll('assoc');
        }
        
        foreach($data as $key => $datas) {
            $ccode = "";
            if(isset($datas["country_code"])) {
                $ccode = "+" . $datas["country_code"];
            }
            
            $phone =  isset($datas["phone_no"]) ? $ccode . " " . $datas["phone_no"] : "-";
            $email =  isset($datas["email"]) ? $datas["email"] : "-";
            $akcessId =  isset($datas["akcessId"]) ? $datas["akcessId"] : "";
            $firstName =  isset($datas["firstName"]) ? $datas["firstName"] : "";
            $lastName =  isset($datas["lastName"]) ? $datas["lastName"] : "";
            $fullName = $firstName . " " . $lastName;
            $status =  (isset($datas["status"]) && $datas["status"] == 1) ? 'Read' : "UnRead";
            $created =  isset($datas["created"]) ? $datas["created"] : "";

            $dataArray[] = array(
                $akcessId,
                $fullName,
                $phone,
                $email,
                $status,
                $created
            );
        }
        
        $resultJ = json_encode($dataArray);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
}

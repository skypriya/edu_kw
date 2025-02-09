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
use Cake\Datasource\ConnectionManager;
use DateTime;
use Cake\Routing\Router;
use Exception;
use \Imagick;


class ApiController extends AppController {

    public function initialize() {

        $this->loadComponent('Flash');

        $this->loadComponent('Global');

        parent::initialize();

        $this->Auth->allow(['qrcode']); // Allows the display page without loggin in. 

    }

    public function qrcode() {
       
        $conn = ConnectionManager::get("default"); // name of your database connection   

        $url  = $_SERVER["REQUEST_URI"];

        $path = explode("/", $url); 

        $last = end($path); 

        $last_value = explode("-", $last); 

        $id = $last_value[0]; 
        $docid = $this->Global->userIdDecode($last_value[0]); 
        $name = end($last_value); 

        $query_response = "SELECT e.formName,sd.recievedType,e.id FROM sendData as sd
                JOIN eform as e ON e.id = sd.fk_idcard_id
                WHERE sd.id='" . $docid . "'";

        $query_data = $conn->execute($query_response);

        $query_data = $query_data->fetch('assoc');

        //print_r($query_data);

        $eformName = $query_data['formName'];
        $recievedType = $query_data['recievedType'];
        $document_id = $this->Global->userIdEncode($query_data['id']);

        $dataArray = array(
            "portal" => BASE_ORIGIN_URL,
            "deeplink_type" => $recievedType,
            "id" => $document_id
        );

        $data = json_encode($dataArray);
        
        $this->set(compact('eformName','document_id','data'));

    }

    
    public function attendance() {

        $conn = ConnectionManager::get("default"); // name of your database connection         
        
        $params = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        if($params && isset($params['akcessid'])) {
            
            $akcessid = isset($params['akcessid']) ? $params['akcessid'] : "";            
            $classid = isset($params['classid']) ? $params['classid'] : "";
            $location = isset($params['location']) ? $params['location'] : "";
            
            $conn = ConnectionManager::get("default"); // name of your database connection    
            
            $queryUser = 'SELECT *  FROM users where soft_delete = 0 and akcessId = "'.$akcessid.'"';

            $queryUserSql = $conn->execute($queryUser);

            $queryUser_response = $queryUserSql->fetch('assoc');

            $userId = isset($queryUser_response['id']) ? $queryUser_response['id'] : 0;
            
            if($userId > 0) {

                try {
                  
                    $query_data = $conn->execute('SELECT count(*) as total FROM class_attends where classId="'.$classid.'" AND userId="'.$userId.'" AND soft_delete=0');

                    $query_data = $query_data->fetch('assoc');

                    if ($query_data['total'] > 0) {

                        date_default_timezone_set('Asia/Calcutta');

                        $date = date('Y-m-d H:i:s');

                        $day = strtolower(date('l', strtotime($date))); 

                        $current_time = strtotime(date('h:i A', strtotime($date)));

                        $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                        $querySclassesSql = $conn->execute($querySclasses);

                        $querySclasses_response = $querySclassesSql->fetch('assoc');

                        $name = isset($querySclasses_response['name']) ? $querySclasses_response['name'] : "";
                        $days = isset($querySclasses_response['days']) ? $querySclasses_response['days'] : "";
                        $openFrom = isset($querySclasses_response['openFrom']) ? $querySclasses_response['openFrom'] : "";
                        $openTo = isset($querySclasses_response['openTo']) ? $querySclasses_response['openTo'] : "";

                        $days = explode(",", $days);
                        $openFrom = explode(",", $openFrom);
                        $openTo = explode(",", $openTo);

                        $result_array = array();
                        foreach ($days as $key=>$val)
                        {
                            if ($day == $val) {
                                $from = strtotime($openFrom[$key]);
                                $to = strtotime($openTo[$key]);
                                $result_array = array(
                                    $val,
                                    $openFrom[$key],
                                    $openTo[$key]
                                );
                            }
                        }
                        if ($from < $current_time && $to > $current_time) {

                            $dateOnly = date('Y-m-d');

                            $dateTime = date('Y-m-d H:i:s');

                            $query_attendance_data = $conn->execute('SELECT * FROM attendance where class_id="'.$classid.'" AND fk_user_id="'.$userId.'" AND (attendance_date_time between "'.$dateOnly.'" and "'.$dateTime.'") AND soft_delete=0');

                            $query_attendance = $query_attendance_data->fetch('assoc');

                            $id = isset($query_attendance['id']) ? $query_attendance['id'] : "";
                            $checkin = isset($query_attendance['checkin']) ? $query_attendance['checkin'] : "";
                            $checkout = isset($query_attendance['checkout']) ? $query_attendance['checkout'] : "";
                            $status = isset($query_attendance['status']) ? $query_attendance['status'] : "";
                            
                            if (isset($id) && $id != "") {
                                if (isset($status) && $status == 0) {
                                    if (!empty($query_attendance) && $query_attendance != "") {
                                        $update_query = "UPDATE `attendance` set `checkout`=1,`checkout_location`='".json_encode($location)."',`status`=1,`checkout_date_time`='".$dateTime."' WHERE id=".$id;

                                        $conn->execute($update_query);

                                        $resultJ = json_encode(array('status' => 'true', 'message' => "Today you are Check-out in the ".$name." class."));
                                        $this->response->type('json');
                                        $this->response->body($resultJ);
                                        return $this->response;
                                    } else {
                                        $insert_query = "INSERT INTO `attendance` (
                                            `class_id`, 
                                            `fk_user_id`, 
                                            `attendance_date_time`, 
                                            `checkin_date_time`, 
                                            `checkin`,
                                            `checkin_location`,
                                            `created`
                                        ) VALUES (
                                            ".$classid.",
                                            '".$userId."',
                                            '".$dateTime."',
                                            '".$dateTime."',
                                            1,
                                            '".json_encode($location)."',
                                            '".$date."'
                                        )";

                                        $conn->execute($insert_query);

                                        $resultJ = json_encode(array('status' => 'true', 'message' => "Today you are Check-in in the ".$name." class."));
                                        $this->response->type('json');
                                        $this->response->body($resultJ);
                                        return $this->response;
                                       
                                    }
                                } else {
                                    $resultJ = json_encode(array('status' => 'false', 'message' => "Today you are already Check-in & Check-Out in the ".$name." class."));
                                    $this->response->type('json');
                                    $this->response->body($resultJ);
                                    return $this->response;
                                }
                            } else {
                                $insert_query = "INSERT INTO `attendance` (
                                    `class_id`, 
                                    `fk_user_id`, 
                                    `attendance_date_time`, 
                                    `checkin_date_time`, 
                                    `checkin`,
                                    `checkin_location`,
                                    `created`
                                ) VALUES (
                                    ".$classid.",
                                    '".$userId."',
                                    '".$dateTime."',
                                    '".$dateTime."',
                                    1,
                                    '".json_encode($location)."',
                                    '".$date."'
                                )";

                                $conn->execute($insert_query);

                                $resultJ = json_encode(array('status' => 'true', 'message' => "Today you are Check-in in the ".$name." class."));
                                $this->response->type('json');
                                $this->response->body($resultJ);
                                return $this->response;
                            }

                        } else {

                            $resultJ = json_encode(array('status' => 'false', 'message' => "Today ".$name." class is closed or not open."));
                            $this->response->type('json');
                            $this->response->body($resultJ);
                            return $this->response;

                        }
                    } else {

                        $resultJ = json_encode(array('status' => 'false', 'message' => "This Akcess id is not found in ".$name." class."));
                        $this->response->type('json');
                        $this->response->body($resultJ);
                        return $this->response;

                    }
                    
                } catch(Exception $e) {

                    $resultJ = json_encode(array('status' => 'false', 'message' => "Error: According EDU Server!"));
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;
                            
                }
            } else {

                $resultJ = json_encode(array('status' => 'false', 'message' => "Akcess id not found"));
                $this->response->type('json');
                $this->response->body($resultJ);
                return $this->response;
            }
        }
    }
    
    public function stringEncryption($action, $string){
        
        $output = false;
        // hash
        $iv = hash('sha256', $secret_key);

        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
      }
    
    
    
      public function getResponseLink() {

        $conn = ConnectionManager::get("default"); // name of your database connection         
        
        $params = (array) json_decode(file_get_contents('php://input'), TRUE);
        
        if($params && isset($params['component_id'])) {
            
            $component = isset($params['component']) ? $params['component'] : "";            
            $domain_name = isset($params['domain_name']) ? $params['domain_name'] : "";
            $component_id = isset($params['component_id']) ? $params['component_id'] : "";
            $company_name = isset($params['company_name']) ? $params['company_name'] : ""; 
            $sender_by = isset($params['sender_by']) ? $params['sender_by'] : "";
            $sender_hash = isset($params['sender_hash']) ? $params['sender_hash'] : "";
            $deeplink_type = isset($params['deeplink_type']) ? $params['deeplink_type'] : "";
            //$deeplink_type = 'eform';
            $conn = ConnectionManager::get("default"); // name of your database connection     
            
            $this->loadModel('SendData');

            $response = array();

            if($component == 'document') {
        
                $message = "Document";
                
            } else if($component == 'idcard') {
        
                $message = "ID Card";
                
            } else if($component == 'eform') {
        
                $message = "Eform";
                
            } 

            try {

                $query_data = $conn->execute('SELECT count(*) as total FROM sendData where id="'.$component_id.'"');

                $query_data = $query_data->fetch('assoc');

                if($query_data['total'] > 0) {

                    $getSendID = $this->SendData->get($component_id);        
                    
                    if($getSendID) {      

                        $title = '';
                        if($getSendID->send_type == 'phone') {
                            $title = 'Send IDCard Transaction by Phone no';
                        } else if($getSendID->send_type == 'email') {
                            $title = 'Send IDCard Transaction by Email';
                        } else if($getSendID->send_type == 'ackess') {
                            $title = 'Send IDCard Transaction by Akcess ID';
                        }
                                    
                        $response['component_id'] = $component_id;
                        $response['send_phone_no'] = $getSendID->phone_no;
                        $response['send_country_code'] = $getSendID->country_code;
                        $response['send_email'] = $getSendID->email;
                        $response['ackessID'] = $getSendID->ackessID;
                        $response['response_id'] = $getSendID->response_id;
                        $response['send_type'] = $getSendID->send_type;
                        $response['component'] = $component;
                        $response['send_status'] = $getSendID->send_status;
                        $response['createdAt'] = date("Y-m-d H:i:s", strtotime($getSendID->createdDate));
                        $response['updatedAt'] = date("Y-m-d H:i:s", strtotime($getSendID->modified));
                        $response['company_name'] = COMP_NAME;        
                        $response['title'] = $title;
                        $response['sender_by'] = $sender_by;
                        $response['sender_hash'] = $sender_hash; 
                        
                        $this->loadModel('IDCard');
                        
                        $this->loadModel('Docs');
                        
                        $this->loadModel('Users');
                        
                        $this->loadModel('Eform');
                        
                        if($deeplink_type == 'document') {
                    
                            $idcard = $this->Docs->get($getSendID->fk_idcard_id);
                            
                            $message = "Document";
                            
                        } else if($deeplink_type == 'idcard') {
                    
                            $idcard = $this->IDCard->get($getSendID->fk_idcard_id);
                            
                            $message = "ID Card";
                            
                        } else if($deeplink_type == 'eform') {
                    
                            $idcard = $this->Eform->get($getSendID->fk_idcard_id);
                            
                            $message = "Eform";
                            
                        } 

                        if($deeplink_type == 'document' || $deeplink_type == 'idcard') {
                        
                            $fk_users_id = $idcard->fk_users_id;
                            
                            $user = $this->Users->get($fk_users_id, [
                                'conditions' => ['soft_delete' => 0]
                            ]);

                            $fileUrl = $idcard->fileUrl;

                            $dir  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $fileUrl;     
                            
                            $response['idNo'] = $idcard->idNo;
                            $response['expiryDate'] = date("Y-m-d H:i:s", strtotime($idcard->idCardExpiyDate));
                            $response['document_hash'] = [$idcard->documentHash];
                            $response['transaction'] = [$idcard->transactionID];
                            $response['channel_name'] = $idcard->channelName;
                            $response['document_ID'] = $idcard->documentId;
                            $response['file_url'] = $dir;
                            $response['file_name'] = $idcard->fileName;
                            $response['akcessId'] = $idcard->AkcessID;
                            $response['signatureHash'] = $idcard->signatureHash;
                            $response['verifier_name'] = $idcard->verifier_name;
                            $response['domain_name'] = ORIGIN_URL; 
                            $response['_id'] = $getSendID->id;  
                            $response['id'] = $getSendID->id;  
                            $response['deeplink_type'] = $deeplink_type;  
                            $response['recievedType'] = $getSendID->recievedType;
                            
                            $name = isset($user->name) ? explode(" ", $user->name) : "";
                            $firstName = isset($name[0]) ? $name[0] : "";
                            $lastName = isset($name[1]) ? $name[1] : "";
                            $response['firstName'] = $firstName;  
                            $response['lastName'] = $lastName;  
                            $response['DOB'] = date("Y-m-d H:i:s", strtotime($user->dob));
                            $response['email'] = $user->email;  
                            $response['phone'] = $user->mobileNumber;  
                            
                            
                            if($getSendID->send_type != 'ackess') {
                                
                                $postData['akcessId'] = $getSendID->AkcessID;
                                $postData['recievedType'] = $getSendID->recievedType;
                                
                                if($getSendID->recievedType == 'eform') {
                                    $postData['eformId'] = $getSendID->id;
                                    $postData['id'] = 0;
                                } else {
                                    $postData['eformId'] = 0;
                                    $postData['id'] = $getSendID->id;
                                }
                                
                                $postData['name'] = COMP_NAME;
                                $postData['firstName'] = $firstName;
                                $postData['lastName'] = $lastName;
                            
                                $this->incomingReceivedByPhoneEmail($postData);
                            }
                            
                            $resultJ = json_encode(array('status' => 'true', 'message' => $message . " successfully found" ,'data' => $response));
                            $this->response->type('json');
                            $this->response->body($resultJ);
                            return $this->response;

                        } else if($deeplink_type == 'eform') {     
                            
                            $eform = $this->Eform->get($getSendID->fk_idcard_id, [
                                'conditions' => ['soft_delete' => 0]
                            ]);               
                            if($eform) {
                                            
                                $formName = $eform->formName;
                                
                                $description = $eform->description;
                                
                                $instruction = $eform->instruction;
                                
                                $user_id = $user_id;
                                
                                $date = date('Y-m-d H:i:s');
                                
                                $signature = $eform->signature;
                                
                                $facematch = $eform->facematch;
                                
                                $pulldata = $eform->pulldata;
                                
                                $publish = $eform->publish;
                                
                                $additional_notification = $eform->additional_notification;
                                
                                $isAdditionalNotification = $eform->isAdditionalNotification;
                                
                                $array_akcesss_id = array();
                                                
                                foreach($ackess as $akcessIds) {
                                    
                                    $akcessId = $akcessIds;
                        
                                    $array_akcesss_id[] = $akcessId;
                        
                                }
                        
                                $storeinprofile = $eform->storeinprofile;
                                
                                $isclientInvitationEform = $eform->isclientInvitationEform;   
                                
                                $this->loadModel('Fields');        
                                
                                $fieldsInformation_query = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $getSendID->fk_idcard_id, 'soft_delete' => 0]));        
                                $eformFields = array();
                                
                                $eformFieldText = [];
                                $eformFieldText1 = [];
                                
                                foreach($fieldsInformation_query as $key => $fieldsInformations){
                                
                                    $array_key = $key;
                                    $field_id = isset($fieldsInformations->id) ? $fieldsInformations->id : ''; 
                                    $name = isset($fieldsInformations->labelname) ? $fieldsInformations->labelname : ''; 
                                    $instructions = isset($fieldsInformations->instructions) ? $fieldsInformations->instructions : ''; 
                                    $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
                                    $isVisible = isset($fieldsInformations->isVisible) ? $fieldsInformations->isVisible : ''; 
                                    $section_id = isset($fieldsInformations->section_id) ? $fieldsInformations->section_id : ''; 
                                    $section_color = isset($fieldsInformations->section_color) ? $fieldsInformations->section_color : ''; 
                                    $sectionfields = isset($fieldsInformations->sectionfields) ? $fieldsInformations->sectionfields : []; 
                                    $verification_grade = isset($fieldsInformations->verification_grade) ? $fieldsInformations->verification_grade : ''; 
                                    $fieldver = isset($fieldsInformations->file_verified) ? $fieldsInformations->file_verified : ''; 
                                    $field_mandate = isset($fieldsInformations->is_mandatory) ? $fieldsInformations->is_mandatory : ''; 
                                    $signature_required = isset($fieldsInformations->signature_required) && $fieldsInformations->signature_required != "" ? $fieldsInformations->signature_required : 'no'; 
                                    $is_mandatory = isset($fieldsInformations->signatureis_mandatory_required) && $fieldsInformations->is_mandatory != "" ? $fieldsInformations->is_mandatory : 'no'; 
                                    $ids = isset($fieldsInformations->ids) ? $fieldsInformations->ids : ''; 
                                    $items = isset($fieldsInformations->options) ? $fieldsInformations->options : ''; 
                                    $key = isset($fieldsInformations->keyfields) ? $fieldsInformations->keyfields : ''; 
                                    $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
                                    
                                    $key_value = $key;
                                    if (strpos($key, '_') !== false) { 
                                        $key_value_explode = explode("_", $key);                
                                        $key_value = $key_value_explode[0];
                                    }
                                
                                    $this->loadModel('FieldsOptions');        
                                
                                    $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                                        'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $getSendID->fk_idcard_id, 'soft_delete' => 0]
                                    ]);
                                    
                                    $array_items = array();
                        
                                    foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                                        
                                        $optionsid = $fieldsOptionsInformations->uid;
                                        $checked = $fieldsOptionsInformations->checked;
                                        $lable = $fieldsOptionsInformations->lable;
                                        $key = $fieldsOptionsInformations->keyfields;
                        
                                        $array_items[] = [
                                            "checked"=> $checked,
                                            "keyfields"=> strtolower($key),
                                            "lable"=> $lable,
                                            "uid"=> $optionsid
                                        ];
                                    }       
                                   
                                    $eformFieldText[$array_key] = [   
                                        "options" => $array_items,
                                        "labelname" => $name,
                                        "key" => $key_value,
                                        "keytype" => $typeIn,
                                        "signature_required" => $signature_required,
                                        "file_verified" => $fieldver,
                                        "verification_grade" => $verification_grade,
                                        "instructions" => $instructions,
                                        "is_mandatory" => $is_mandatory
                                    ];         
                                    
                                    if($sectionfields){
                                        $eformFieldText1[$array_key] = [   
                                            "section_id" => $section_id,
                                            "section_color" => $section_color,
                                            "sectionfields" => $sectionfields,
                                        ];
                                        $eformFieldText = array_merge_recursive($eformFieldText,$eformFieldText1);
                                    }

                                } 
                                
                                $description_title = "Eform";

                                $dir  = WWW_ROOT . "/img/logo.png";
                                // A few settings
                                $img_file = $dir;
                                // Read image path, convert to base64 encoding
                                $imgData = base64_encode(file_get_contents($img_file));

                                // Format the image SRC:  data:{mime};base64,{data};
                                $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

                                $date = date("Y-m-d\TH:i:s.000\Z");

                                $response = [
                                    "eform" => [
                                        "status"=> "alive",
                                        "eformId"=> $eform->eformid,
                                        "signature"=> $signature,
                                        "fields"=> $eformFieldText,
                                        "date" => $date,
                                        "formName" => $formName,
                                        "description" => $description,
                                        "name"=> ORIGIN_URL,
                                        "logo"=> $src,
                                        "pulldata"=> $pulldata,
                                        "facematch"=>$facematch,
                                        "recievedType"=> $type_doc,
                                        "component_id"=> $component_id,
                                        "component"=>$type_doc,
                                        "company_name"=> null,
                                        "isAdditionalNotification"=>"no",
                                        "additionalNotificationTo"=> [],
                                        "companyName"=> null,
                                        "storeinprofile"=> $storeinprofile
                                    ]
                                ];
                            
                                $response['expiryDate'] = date("Y-m-d H:i:s", strtotime($idcard->idCardExpiyDate));
                                $response['document_hash'] = [$idcard->documentHash];
                                $response['transaction'] = [$idcard->transactionID];
                                $response['channel_name'] = $idcard->channelName;
                                $response['document_ID'] = $idcard->documentId;
                                $response['file_url'] = $dir;
                                $response['file_name'] = $idcard->fileName;
                                $response['akcessId'] = $idcard->AkcessID;
                                $response['signatureHash'] = $idcard->signatureHash;
                                $response['verifier_name'] = $idcard->verifier_name;
                                $response['domain_name'] = ORIGIN_URL; 
                                $response['_id'] = $getSendID->id;  
                                $response['id'] = $getSendID->id;  
                                
                                $name = isset($user->name) ? explode(" ", $user->name) : "";
                                $firstName = isset($name[0]) ? $name[0] : "";
                                $lastName = isset($name[1]) ? $name[1] : "";
                                $response['firstName'] = $firstName;  
                                $response['lastName'] = $lastName;  
                                $response['DOB'] = date("Y-m-d H:i:s", strtotime($user->dob));
                                $response['email'] = $user->email;  
                                $response['phone'] = $user->mobileNumber;  
                                $response['deeplink_type'] = $deeplink_type;  

                                if($getSendID->send_type != 'ackess') {
                                    
                                    $postData['akcessId'] = $getSendID->AkcessID;
                                    $postData['recievedType'] = $getSendID->recievedType;
                                    
                                    if($getSendID->recievedType == 'eform') {
                                        $postData['eformId'] = $getSendID->id;
                                        $postData['id'] = 0;
                                    } else {
                                        $postData['eformId'] = 0;
                                        $postData['id'] = $getSendID->id;
                                    }
                                    
                                    $postData['name'] = COMP_NAME;
                                    $postData['firstName'] = $firstName;
                                    $postData['lastName'] = $lastName;

                                
                                    $this->incomingReceivedByPhoneEmail($postData);
                                }
                                
                                $resultJ = json_encode(array('status' => 'true', 'message' => "eform found" ,'data' => $response));
                                $this->response->type('json');
                                $this->response->body($resultJ);
                                return $this->response;
                            } else {
                                $resultJ = json_encode(array('status' => 'false', 'message' => "eform not found" ,'data' => $response));
                                $this->response->type('json');
                                $this->response->body($resultJ);
                                return $this->response;
                            }
                        } else {                
                            $resultJ = json_encode(array('status' => 'false', 'message' => "eform not found" ,'data' => $response));
                            $this->response->type('json');
                            $this->response->body($resultJ);
                            return $this->response;
                        }
                
                    } else {                
                        $resultJ = json_encode(array('status' => 'false', 'message' => "eform not found" ,'data' => $response));
                        $this->response->type('json');
                        $this->response->body($resultJ);
                        return $this->response;
                    }
                } else {                
                    $resultJ = json_encode(array('status' => 'false', 'message' => "eform not found" ,'data' => $response));
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;
                }
            } catch(Exception $e) {

                $resultJ = json_encode(array('status' => 'false', 'message' => "eform not found" ,'data' => $response));
                $this->response->type('json');
                $this->response->body($resultJ);
                return $this->response;
    
            }
        }
    }
    
    public function incomingReceivedByPhoneEmail($response) {

        //$myfile = fopen("/var/www/html/edu/logs/incomingReceivedByPhoneEmail.txt", "a") or die("Unable to open file!");
        //$txt .= json_encode($_POST . " \n");
        
        if(isset($response)) {
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('IncomingReceived');

            $incomingReceived = $this->IncomingReceived->newEntity(); 

            $incomingReceivedData = $this->IncomingReceived->patchEntity($incomingReceived, $response);
            
            $akcessId = isset($response['akcessId']) ? $response['akcessId'] : '';
            $companyName = isset($response['companyName']) ? $response['companyName'] : '';
            $recievedType = isset($response['recievedType']) ? $response['recievedType'] : '';
            $eformId = isset($response['eformId']) ? $response['eformId'] : '';
            $id  = isset($response['id']) ? $response['id'] : '';
            $name = isset($response['name']) ? $response['name'] : '';
            $firstName = isset($response['firstName']) ? $response['firstName'] : '';
            $lastName = isset($response['lastName']) ? $response['lastName'] : '';

            $incomingReceivedData->akcessId = $akcessId;
            $incomingReceivedData->companyName = $companyName;
            $incomingReceivedData->recievedType = $recievedType;
            $incomingReceivedData->eformId = $eformId;
            $incomingReceivedData->incomingreceived_id = $id;
            $incomingReceivedData->name = $name;
            $incomingReceivedData->firstName = $firstName;
            $incomingReceivedData->lastName = $lastName;
            $incomingReceivedData->status = 1;

            //$txt .= json_encode($incomingReceivedData . " \n");
            
            $incomingReceivedResultData = $this->IncomingReceived->save($incomingReceivedData);

            $incomingReceivedId = $incomingReceivedResultData->id;

            //$txt .= json_encode($incomingReceivedId . " \n");

            $after = array(
                'akcessId' => $akcessId,
                'companyName' => $companyName, 
                'recievedType' => $recievedType,
                'eformId' => $eformId,
                'incomingreceived_id' => $id,
                'name' => $name,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'status' => 1
            );

            //$this->Global->auditTrailApi($incomingReceivedId, 'incomingreceived', 'insert', null, $after);

            //$txt = json_encode($after);
            //fwrite($myfile, "\n". $txt);
            //fclose($myfile);
            
            $resultJ = json_encode(array('result' => 'success','status' => 1));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
           
        }
    }
    
    public function incomingReceived() {              

        //$myfile = fopen("/var/www/html/edu/logs/incomingReceived.txt", "a") or die("Unable to open file!");
        //$txt .= json_encode($_POST . " \n");

        if($_POST && isset($_POST['recievedType'])) {
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('IncomingReceived');

            $incomingReceived = $this->IncomingReceived->newEntity();

            $incomingReceivedData = $this->IncomingReceived->patchEntity($incomingReceived, $_POST);

            $akcessId = isset($_POST['akcessId']) ? $_POST['akcessId'] : '';
            $companyName = isset($_POST['companyName']) ? $_POST['companyName'] : '';
            $recievedType = isset($_POST['recievedType']) ? $_POST['recievedType'] : '';
            $eformId = isset($_POST['eformId']) ? $_POST['eformId'] : '';
            $id  = isset($_POST['id']) ? $_POST['id'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : '';
            $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';

            $incomingReceivedData->akcessId = $akcessId;
            $incomingReceivedData->companyName = $companyName;
            $incomingReceivedData->recievedType = $recievedType;
            $incomingReceivedData->eformId = $eformId;
            $incomingReceivedData->incomingreceived_id = $id;
            $incomingReceivedData->name = $name;
            $incomingReceivedData->firstName = $firstName;
            $incomingReceivedData->lastName = $lastName;
            $incomingReceivedData->status = 1;
            
            $incomingReceivedResultData = $this->IncomingReceived->save($incomingReceivedData);

            //$txt .= json_encode($incomingReceivedResultData . " \n");
            
            $incomingReceivedId = $incomingReceivedResultData->id;

            //$txt .= json_encode($incomingReceivedId . " \n");

            $after = array(
                'akcessId' => $akcessId,
                'companyName' => $companyName, 
                'recievedType' => $recievedType,
                'eformId' => $eformId,
                'incomingreceived_id' => $id,
                'name' => $name,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'status' => 1
            );

            //$this->Global->auditTrailApi($incomingReceivedId, 'incomingreceived', 'insert', null, $after);

            //$txt = json_encode($after);
            //fwrite($myfile, "\n". $txt);
            //fclose($myfile);
            
            $resultJ = json_encode(array('result' => 'success','status' => 1));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
           
        }
    }

    public function saveEformresponse() {

        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;

        //$myfile = fopen("/var/www/html/edu/logs/saveEformresponse.txt", "a") or die("Unable to open file!");
        //$txt .= json_encode($_POST . " \n");
        if($_POST && isset($_POST['eformId'])) {            
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('EformResponse');

            $eform = $this->EformResponse->newEntity();

            $eformResponse = $this->EformResponse->patchEntity($eform, $_POST);

            // eformasfile

            if(!empty($_FILES['eformasfile']['name'])) {

                $file_tmpname_eformasfile = $_FILES['eformasfile']['tmp_name'];
            
                $file_name_eformasfile = $_FILES['eformasfile']['name'];
                
                $file_size_eformasfile = $_FILES['eformasfile']['size'];
            
                $file_ext_eformasfile = pathinfo($file_name_eformasfile, PATHINFO_EXTENSION);
                
                $flname = $_POST['eformId'];
        
                $eformasfile_file_name = $file_name_eformasfile;
        
                $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";

                chmod($dir, 0777);

                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
        
                $uploadFile_eformasfile = $dir . $eformasfile_file_name;
        
                move_uploaded_file($file_tmpname_eformasfile, $uploadFile_eformasfile);

            }

            // eformasfile        
            
            //$txt .= json_encode("eformasfile \n");

            // profilepic

            $profilepic_imgData = "";

            $facematch = isset($_POST['facematch']) ? $_POST['facematch'] : '';

            if($facematch == 'yes') {

                if(!empty($_FILES['profilepic']['name'])) {

                    $file_tmpname_profilepic = $_FILES['profilepic']['tmp_name'];
                
                    $file_name_profilepic = $_FILES['profilepic']['name'];
                    
                    $file_size_profilepic = $_FILES['profilepic']['size'];
                
                    $file_ext_profilepic = pathinfo($file_name_profilepic, PATHINFO_EXTENSION);
                    
                    $flname = $_POST['eformId'];
            
                    $profilepic_file_name = $file_name_profilepic;
    
                    $profilepic_image_name = explode('.', $profilepic_file_name);
    
                    unset($profilepic_image_name[count($profilepic_image_name) - 1]);
    
                    $profilepic_image_fullname = $dir . implode('.', $profilepic_image_name) . ".jpeg";
            
                    $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";
    
                    chmod($dir, 0777);
            
                    if( is_dir($dir) === false )
                    {
                        mkdir($dir, 0777, true);
                    }
            
                    $uploadFile_profilepic = $dir . $profilepic_file_name;
            
                    move_uploaded_file($file_tmpname_profilepic, $uploadFile_profilepic);
    
                    chmod($uploadFile_profilepic, 0777);
    
                    $pdf = new \Spatie\PdfToImage\Pdf($uploadFile_profilepic);
    
                    $pdf->saveImage($profilepic_image_fullname);
    
                    chmod($profilepic_image_fullname, 0777);
                    // Read image path, convert to base64 encoding
                    $profilepic_imgData = base64_encode(file_get_contents($profilepic_image_fullname));
                }

            }
            // profilepic

            //$txt .= json_encode("profilepic \n");

            // signaturefile

            if(!empty($_FILES['signaturefile']['name'])) {

                $file_tmpname_signaturefile = $_FILES['signaturefile']['tmp_name'];
            
                $file_name_signaturefile = $_FILES['signaturefile']['name'];
                
                $file_size_signaturefile = $_FILES['signaturefile']['size'];
            
                $file_ext_signaturefile = pathinfo($file_name_signaturefile, PATHINFO_EXTENSION);
                
                $flname = $_POST['eformId'];
        
                $signaturefile_file_name = $file_name_signaturefile;
        
                $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";

                chmod($dir, 0777);
        
                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
        
                $uploadFile_signaturefile = $dir . $signaturefile_file_name;
        
                move_uploaded_file($file_tmpname_signaturefile, $uploadFile_signaturefile);
            }

            // signaturefile
            
            //$txt .= json_encode("signaturefile \n");

            $response_token = $this->Global->getToken();

            //$txt .= json_encode($response_token . " \n");
           
            $token = $response_token;

            $origin_array = array(
                'authorization: ' . $token,
                'apikey: ' . $api,
                'origin: ' . $origin_url
            );

            $formName = isset($_POST['formName']) ? $_POST['formName'] : '';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $eformId = isset($_POST['eformId']) ? $_POST['eformId'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $device_token = isset($_POST['device_token']) ? $_POST['device_token'] : '';
            $akcessId = isset($_POST['akcessId']) ? $_POST['akcessId'] : '';
            $documentHash = isset($_POST['documentHash']) ? $_POST['documentHash'] : '';
            $signatureHash = isset($_POST['signatureHash']) ? $_POST['signatureHash'] : '';
            $filedata = isset($_POST['filedata']) ? $_POST['filedata'] : '';
            $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
            $otp = isset($_POST['otp']) ? $_POST['otp'] : '';
            $bankId = isset($_POST['bankId']) ? $_POST['bankId'] : '';
            $api = isset($_POST['api']) ? $_POST['api'] : '';
            $signaturefile = isset($signaturefile_file_name) ? $signaturefile_file_name : '';
            $faceMatchPic = isset($_POST['faceMatchPic']) ? $_POST['faceMatchPic'] : '';
            $profilepic = isset($profilepic_file_name) ? $profilepic_file_name : '';
            $eformasfile = isset($eformasfile_file_name) ? $eformasfile_file_name : '';
            $facialMatch = isset($_POST['facialMatch']) ? $_POST['facialMatch'] : '';
            $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d h:i:s');
            $approved = isset($_POST['approved']) ? $_POST['approved'] : '';
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            $pulldata = isset($_POST['pulldata']) ? $_POST['pulldata'] : '';
            $facematch = isset($_POST['facematch']) ? $_POST['facematch'] : '';
            $mobile_local_id = isset($_POST['mobile_local_id']) ? $_POST['mobile_local_id'] : '';
            $strict = isset($_POST['strict']) ? $_POST['strict'] : '';

            $eformResponse->formName = $formName;
            $eformResponse->name = $name;
            $eformResponse->eformId = $eformId;
            $eformResponse->description = $description;
            $eformResponse->device_token = $device_token;
            $eformResponse->akcessId = $akcessId;
            $eformResponse->documentHash = serialize($documentHash);
            $eformResponse->signatureHash = $signatureHash;
            $eformResponse->filedata = $filedata;
            $eformResponse->filename = $filename;
            $eformResponse->otp = $otp;
            $eformResponse->bankId = $bankId;
            $eformResponse->api = $api;
            $eformResponse->signaturefile = $signaturefile;
            $eformResponse->faceMatchPic = $faceMatchPic;
            $eformResponse->profilepic = $profilepic;
            $eformResponse->eformasfile = $eformasfile_file_name;
            $eformResponse->facialMatch = $facialMatch;
            $eformResponse->date = $date;
            $eformResponse->approved = $approved;
            $eformResponse->status = $status;
            $eformResponse->pulldata = $pulldata;
            $eformResponse->facematch = $facematch;
            $eformResponse->mobile_local_id = $mobile_local_id;
            $eformResponse->strict = $strict;

            $eformResponseResult = $this->EformResponse->save($eformResponse);

            $eformResponseId = $eformResponseResult->id;

            //$txt .= json_encode($eformResponse . "\n");
            //$txt .= json_encode($eformResponseId . "\n");            

            $dir  = WWW_ROOT . "/img/logo.png";
            // A few settings
            $img_file = $dir;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

            $responseEformData = array(
                'formName' => $formName,
                'name' => $name, 
                'eformId' => $eformId,
                'description' => $description,
                'device_token' => $device_token,
                'akcessId' => $akcessId,
                'documentHash' => serialize($documentHash),
                'signatureHash' => $signatureHash,
                'filedata' => $filedata,
                'filename' => $filename,
                'otp' => $otp, 
                'bankId' => $bankId,
                'api' => $api,
                'signaturefile' => $signaturefile,
                'faceMatchPic' => $faceMatchPic,
                'profilepic' => $profilepic,
                'eformasfile' => $eformasfile_file_name,
                'facialMatch' => $facialMatch,
                'date' => $date,
                'approved' => $approved, 
                'status' => $status,
                'pulldata' => $pulldata,
                'facematch' => $facematch,
                'mobile_local_id' => $mobile_local_id,
                'strict' => $strict,
                'createdAt' => $date,
                '_id'=> $mobile_local_id,
                'logo' => $src
            );

            //$txt .= json_encode($responseEformData . "\n");

            $after = array(
                'formName' => $formName,
                'name' => $name, 
                'eformId' => $eformId,
                'description' => $description,
                'device_token' => $device_token,
                'akcessId' => $akcessId,
                'documentHash' => serialize($documentHash),
                'signatureHash' => $signatureHash,
                'filedata' => $filedata,
                'filename' => $filename,
                'otp' => $otp, 
                'bankId' => $bankId,
                'api' => $api,
                'signaturefile' => $signaturefile,
                'faceMatchPic' => $faceMatchPic,
                'profilepic' => $profilepic,
                'eformasfile' => $eformasfile,
                'facialMatch' => $facialMatch,
                'date' => $date,
                'approved' => $approved, 
                'status' => $status,
                'pulldata' => $pulldata,
                'facematch' => $facematch,
                'mobile_local_id' => $mobile_local_id,
                'strict' => $strict
            );
            
            //$this->Global->auditTrailApi($eformResponseId, 'eformresponse', 'insert', null, $after);

            $fields = isset($_POST['fields']) ? $_POST['fields'] : '';

            if($fields) {

                $this->loadModel('FieldsResponse');

                $fieldsRes = $this->FieldsResponse->newEntity();

                $fieldsResponse = $this->FieldsResponse->patchEntity($fieldsRes, $_POST);

                foreach($fields as $key1 => $fieldsValues) {

                    $fk_eformresponse_id = isset($eformResponseId) ? $eformResponseId : '';
                    $fk_eform_id = isset($eformId) ? $eformId : '';
                    $labelname = isset($fieldsValues['labelname']) ? $fieldsValues['labelname'] : '';
                    $keyfields = isset($fieldsValues['key']) ? $fieldsValues['key'] : '';
                    $keytype = isset($fieldsValues['keytype']) ? $fieldsValues['keytype'] : '';
                    $value = isset($fieldsValues['value']) ? $fieldsValues['value'] : '';
                    $file = isset($fieldsValues['file']) ? $fieldsValues['file'] : '';
                    $verify_status = isset($fieldsValues['verify_status']) ? $fieldsValues['verify_status'] : '';
                    $file_verified = isset($fieldsValues['file_verified']) ? $fieldsValues['file_verified'] : '';
                    $expiryDate = isset($fieldsValues['expiryDate']) ? $fieldsValues['expiryDate'] : date('Y-m-d h:i:s');
                    $isverified = isset($fieldsValues['isverified']) ? $fieldsValues['isverified'] : '';
                    $docuementType = isset($fieldsValues['docuementType']) ? $fieldsValues['docuementType'] : '';
                    $isDocFetched = isset($fieldsValues['isDocFetched']) ? $fieldsValues['isDocFetched'] : '';
                    $signature_required = isset($fieldsValues['signature_required']) ? $fieldsValues['signature_required'] : '';
                    $options = isset($fieldsValues['options']) ? $fieldsValues['options'] : '';
                    $verification_grade = isset($fieldsValues['verification_grade']) ? $fieldsValues['verification_grade'] : '';
                    $section_id = isset($fieldsValues['section_id']) ? $fieldsValues['section_id'] : '';
                    $section_color = isset($fieldsValues['section_color']) ? $fieldsValues['section_color'] : '';
                    $sectionfields = isset($fieldsValues['sectionfields']) ? $fieldsValues['sectionfields'] : '';
                    $docuementType = isset($fieldsValues['docuementType']) ? $fieldsValues['docuementType'] : '';

                    $fieldsResponse->fk_eformresponse_id = $fk_eformresponse_id;
                    $fieldsResponse->fk_eform_id = $fk_eform_id;
                    $fieldsResponse->labelname = $labelname;
                    $fieldsResponse->keyfields = $keyfields;
                    $fieldsResponse->keytype = $keytype;
                    $fieldsResponse->value = $value;
                    $fieldsResponse->file = $file;
                    $fieldsResponse->verify_status = $verify_status;
                    $fieldsResponse->file_verified = $file_verified;
                    $fieldsResponse->expiryDate = $expiryDate;
                    $fieldsResponse->isverified = $isverified;
                    $fieldsResponse->docuementType = $docuementType;
                    $fieldsResponse->isDocFetched = $isDocFetched;
                    $fieldsResponse->signature_required = $signature_required;
                    $fieldsResponse->options = $options;
                    $fieldsResponse->verification_grade = $verification_grade;
                    $fieldsResponse->section_id = isset($section_id) ? serialize($section_id) : "";
                    $fieldsResponse->section_color = isset($section_color) ? serialize($section_color) : "";
                    $fieldsResponse->sectionfields = isset($sectionfields) ? serialize($sectionfields) : "";

                    if(isset($keytype) && $keytype == 'file') {

                        // file
        
                        if(!empty($_FILES['fields']['name'][$key1])) {

                            if(isset($_FILES['fields']['tmp_name'][$key1]['file']) || isset($_FILES['fields']['name'][$key1]['file']) || issset($_FILES['fields']['size'][$key1]['file'])) {
        
                                $file_tmpname_valuefile = $_FILES['fields']['tmp_name'][$key1]['file'];
                            
                                $file_name_valuefile = $_FILES['fields']['name'][$key1]['file'];
                                
                                $file_size_valuefile = $_FILES['fields']['size'][$key1]['file'];
                            
                                $file_ext_valuefile = pathinfo($file_name_valuefile, PATHINFO_EXTENSION);
                                
                                $flname = $_POST['eformId'];
                        
                                $valuefile_file_name = $file_name_valuefile;

                                $valuefile_file_name_explode= explode('.', $valuefile_file_name);

                                unset($valuefile_file_name_explode[count($valuefile_file_name_explode) - 1]);

                                $valuefile_image_fullname = $dir . implode('.', $valuefile_file_name_explode) . ".jpeg";
                        
                                $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";

                                chmod($dir, 0777);
                        
                                if( is_dir($dir) === false )
                                {
                                    mkdir($dir, 0777, true);
                                }
                        
                                $uploadFile_valuefile = $dir . $valuefile_file_name;
                        
                                move_uploaded_file($file_tmpname_valuefile, $uploadFile_valuefile);

                                $value = $valuefile_file_name;

                                chmod($uploadFile_valuefile, 0777);

                                try{

                                    $pdf = new \Spatie\PdfToImage\Pdf($uploadFile_valuefile);

                                    $pdf->saveImage($valuefile_image_fullname);

                                    chmod($valuefile_image_fullname, 0777);
                                    // Read image path, convert to base64 encoding
                                    $valuefile_imgData = base64_encode(file_get_contents($valuefile_image_fullname));

                                    $valuefile_data_array = array(
                                        'document'    => $valuefile_imgData,
                                        'documentType'    => $docuementType,
                                        'facematch' => $facematch,
                                        'faceMatchPic' => isset($profilepic_imgData) ? $profilepic_imgData : ""
                                    );

                                    $valuefile_method = "POST";

                                    $valuefile_type_method = 'getDataFromDocument';

                                    $valuefile_response_data = $this->Global->curlGetPost($valuefile_method, $valuefile_type_method, $api, $origin_url, $api_url, $valuefile_data_array, $origin_array);
                                        
                                    $valuefile_response = json_decode($valuefile_response_data);
                                    
                                    $valuefile_jsonData = "";

                                    if($valuefile_response->status == 200) {
                                        $valuefile_jsonData = json_encode($valuefile_response->data);
                                    }

                                } catch(Exception $e) {
                                    $valuefile_jsonData = json_encode($valuefile_response->data);
                                }
                            } else if(isset($_FILES['fields']['tmp_name'][$key1]['value']) || isset($_FILES['fields']['name'][$key1]['value']) || issset($_FILES['fields']['size'][$key1]['value'])) {
        
                                $file_tmpname_valuefile = $_FILES['fields']['tmp_name'][$key1]['value'];
                            
                                $file_name_valuefile = $_FILES['fields']['name'][$key1]['value'];
                                
                                $file_size_valuefile = $_FILES['fields']['size'][$key1]['value'];
                            
                                $file_ext_valuefile = pathinfo($file_name_valuefile, PATHINFO_EXTENSION);
                                
                                $flname = $_POST['eformId'];
                        
                                $valuefile_file_name = $file_name_valuefile;

                                $valuefile_file_name_explode= explode('.', $valuefile_file_name);

                                unset($valuefile_file_name_explode[count($valuefile_file_name_explode) - 1]);

                                $valuefile_image_fullname = $dir . implode('.', $valuefile_file_name_explode) . ".jpeg";
                        
                                $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";

                                chmod($dir, 0777);
                        
                                if( is_dir($dir) === false )
                                {
                                    mkdir($dir, 0777, true);
                                }
                        
                                $uploadFile_valuefile = $dir . $valuefile_file_name;
                        
                                move_uploaded_file($file_tmpname_valuefile, $uploadFile_valuefile);

                                $value = $valuefile_file_name;

                                chmod($uploadFile_valuefile, 0777);

                                try{

                                    $pdf = new \Spatie\PdfToImage\Pdf($uploadFile_valuefile);

                                    $pdf->saveImage($valuefile_image_fullname);

                                    chmod($valuefile_image_fullname, 0777);
                                    // Read image path, convert to base64 encoding
                                    $valuefile_imgData = base64_encode(file_get_contents($valuefile_image_fullname));

                                    $valuefile_data_array = array(
                                        'document'    => $valuefile_imgData,
                                        'documentType'    => $docuementType,
                                        'facematch' => $facematch,
                                        'faceMatchPic' => isset($profilepic_imgData) ? $profilepic_imgData : ""
                                    );

                                    $valuefile_method = "POST";

                                    $valuefile_type_method = 'getDataFromDocument';

                                    $valuefile_response_data = $this->Global->curlGetPost($valuefile_method, $valuefile_type_method, $api, $origin_url, $api_url, $valuefile_data_array, $origin_array);
                                        
                                    $valuefile_response = json_decode($valuefile_response_data);
                                    
                                    $valuefile_jsonData = "";

                                    if($valuefile_response->status == 200) {
                                        $valuefile_jsonData = json_encode($valuefile_response->data);
                                    }

                                } catch(Exception $e) {
                                    $valuefile_jsonData = json_encode($valuefile_response->data);
                                }
                            }
                            
                        }
        
                        // file
                    }

                    $sql_query = "INSERT INTO `fieldsresponse` (`fk_eformresponse_id`, `fk_eform_id`, `labelname`, `keyfields`,
                     `keytype`, `value`, `file`, `verify_status`, `file_verified`, `expiryDate`, `isverified`, `docuementType`,
                      `documentData`,`isDocFetched`, `signature_required`, `options`, `verification_grade`, `section_id`, `section_color`,
                       `sectionfields`) VALUES (".$fk_eformresponse_id.",'".$fk_eform_id."','".$labelname."','".$keyfields."',
                       '".$keytype."','".$value."','".$file."','".$verify_status."','".$file_verified."','".$expiryDate."',
                       '".$isverified."','".$docuementType."','".$valuefile_jsonData."','".$isDocFetched."','".$signature_required."',
                       '".$options."','".$verification_grade."','".$section_id."','".$section_color."','".$fieldsResponse->sectionfields."')";

                    $sql = $conn->execute($sql_query);

                    $sql_last_query = "SELECT id FROM `fieldsresponse` ORDER BY ID DESC LIMIT 0,1";

                    $sql_last_query_id = $conn->execute($sql_last_query);
                    $sql_last_id = $sql_last_query_id->fetch('assoc');
                    $fieldsResponseId = $sql_last_id['id'];
                
                    $after = array(
                        'fk_eformresponse_id' => $fk_eformresponse_id,
                        'fk_eform_id' => $fk_eform_id, 
                        'labelname' => $labelname,
                        'keyfields' => $keyfields,
                        'keytype' => $keytype,
                        'value' => $value,
                        'file' => $verify_status,
                        'verify_status' => $file_verified,
                        'expiryDate' => $expiryDate,
                        'isverified' => $isverified,
                        'docuementType' => $docuementType, 
                        'isDocFetched' => $isDocFetched,
                        'signature_required' => $signature_required,
                        'options' => $options,
                        'verification_grade' => $verification_grade,
                        'section_id' => $section_id,
                        'section_color' => $section_color,
                        'sectionfields' => $fieldsResponse->sectionfields
                    );

                    //$this->Global->auditTrailApi($fieldsResponseId, 'fieldsresponse', 'insert', null, $after);

                }

            }    

            $fieldsp = isset($_POST['publicFields']) ? $_POST['publicFields'] : '';

            // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
            // $txt = json_encode($fieldsp);
            // fwrite($myfile, "\n". $txt);
            // fclose($myfile); 

            if($fieldsp) {

                $this->loadModel('FieldsResponse');

                $fieldspRes = $this->FieldsResponse->newEntity();

                $fieldspResponse = $this->FieldsResponse->patchEntity($fieldspRes, $_POST);

                foreach($fieldsp as $key1 => $fieldspValues) {

                    $fk_eformresponse_id = isset($eformResponseId) ? $eformResponseId : '';
                    $fk_eform_id = isset($eformId) ? $eformId : '';
                    $labelname = isset($fieldspValues['labelname']) ? $fieldspValues['labelname'] : '';
                    $keyfields = isset($fieldspValues['key']) ? $fieldspValues['key'] : '';
                    $keytype = isset($fieldspValues['keytype']) ? $fieldspValues['keytype'] : '';
                    $value = isset($fieldspValues['value']) ? $fieldspValues['value'] : '';
                    $file = isset($fieldspValues['file']) ? $fieldspValues['file'] : '';
                    $verify_status = isset($fieldspValues['verify_status']) ? $fieldspValues['verify_status'] : '';
                    $file_verified = isset($fieldspValues['file_verified']) ? $fieldspValues['file_verified'] : '';
                    $expiryDate = isset($fieldspValues['expiryDate']) ? $fieldspValues['expiryDate'] : date('Y-m-d h:i:s');
                    $isverified = isset($fieldspValues['isverified']) ? $fieldspValues['isverified'] : '';
                    $docuementType = isset($fieldspValues['docuementType']) ? $fieldspValues['docuementType'] : '';
                    $isDocFetched = isset($fieldspValues['isDocFetched']) ? $fieldspValues['isDocFetched'] : '';
                    $signature_required = isset($fieldspValues['signature_required']) ? $fieldspValues['signature_required'] : '';
                    $options = isset($fieldspValues['options']) ? $fieldspValues['options'] : '';
                    $verification_grade = isset($fieldspValues['verification_grade']) ? $fieldspValues['verification_grade'] : '';
                    $section_id = isset($fieldspValues['section_id']) ? $fieldspValues['section_id'] : '';
                    $section_color = isset($fieldspValues['section_color']) ? $fieldspValues['section_color'] : '';
                    $sectionfields = isset($fieldspValues['sectionfields']) ? $fieldspValues['sectionfields'] : '';
                    $docuementType = isset($fieldspValues['docuementType']) ? $fieldspValues['docuementType'] : '';

                    $fieldspResponse->fk_eformresponse_id = $fk_eformresponse_id;
                    $fieldspResponse->fk_eform_id = $fk_eform_id;
                    $fieldspResponse->labelname = $labelname;
                    $fieldspResponse->keyfields = $keyfields;
                    $fieldspResponse->keytype = $keytype;
                    $fieldspResponse->value = $value;
                    $fieldspResponse->file = $file;
                    $fieldspResponse->verify_status = $verify_status;
                    $fieldspResponse->file_verified = $file_verified;
                    $fieldspResponse->expiryDate = $expiryDate;
                    $fieldspResponse->isverified = $isverified;
                    $fieldspResponse->docuementType = $docuementType;
                    $fieldspResponse->isDocFetched = $isDocFetched;
                    $fieldspResponse->signature_required = $signature_required;
                    $fieldspResponse->options = $options;
                    $fieldspResponse->verification_grade = $verification_grade;
                    $fieldspResponse->section_id = isset($section_id) ? serialize($section_id) : "";
                    $fieldspResponse->section_color = isset($section_color) ? serialize($section_color) : "";
                    $fieldspResponse->sectionfields = isset($sectionfields) ? serialize($sectionfields) : "";

                    $sql_query = "INSERT INTO `fieldsresponse` (`fk_eformresponse_id`, `fk_eform_id`, `labelname`, `keyfields`,
                     `keytype`, `value`, `file`, `verify_status`, `file_verified`, `expiryDate`, `isverified`, `docuementType`,
                      `documentData`,`isDocFetched`, `signature_required`, `options`, `verification_grade`, `section_id`, `section_color`,
                       `sectionfields`) VALUES (".$fk_eformresponse_id.",'".$fk_eform_id."','".$labelname."','".$keyfields."',
                       '".$keytype."','".$value."','".$file."','".$verify_status."','".$file_verified."','".$expiryDate."',
                       '".$isverified."','".$docuementType."','".$valuefile_jsonData."','".$isDocFetched."','".$signature_required."',
                       '".$options."','".$verification_grade."','".$section_id."','".$section_color."','".$fieldspResponse->sectionfields."')";

                    $sql = $conn->execute($sql_query);

                    $sql_last_query = "SELECT id FROM `fieldsresponse` ORDER BY ID DESC LIMIT 0,1";

                    $sql_last_query_id = $conn->execute($sql_last_query);
                    $sql_last_id = $sql_last_query_id->fetch('assoc');
                    $fieldspResponseId = $sql_last_id['id'];
                
                    $after = array(
                        'fk_eformresponse_id' => $fk_eformresponse_id,
                        'fk_eform_id' => $fk_eform_id, 
                        'labelname' => $labelname,
                        'keyfields' => $keyfields,
                        'keytype' => $keytype,
                        'value' => $value,
                        'file' => $verify_status,
                        'verify_status' => $file_verified,
                        'expiryDate' => $expiryDate,
                        'isverified' => $isverified,
                        'docuementType' => $docuementType, 
                        'isDocFetched' => $isDocFetched,
                        'signature_required' => $signature_required,
                        'options' => $options,
                        'verification_grade' => $verification_grade,
                        'section_id' => $section_id,
                        'section_color' => $section_color,
                        'sectionfields' => $fieldspResponse->sectionfields
                    );


                }

            }    

            //fwrite($myfile, "\n". $txt);
            //fclose($myfile);

            return $responseEformData;

            // $resultJ = json_encode(array('result' => 'success','status' => 1, 'data' => $eformResponse));
            // $this->response->type('json');
            // $this->response->body($resultJ);
            // return $this->response;
           
        }
    }
}

?>
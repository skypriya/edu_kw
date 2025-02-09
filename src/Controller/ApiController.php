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

        $this->Auth->allow(['qrcodeApi']); // Allows the display page without loggin in. 

        $this->Auth->allow(['idcardApi']); // Allows the display page without loggin in. 

        $this->Auth->allow(['guestpassApi']); // Allows the display page without loggin in.

        $this->Auth->allow(['documentApi']); // Allows the display page without loggin in. 

        $this->Auth->allow(['getUserDataByAKcessID']); // Allows the display page without loggin in. 

    }

    public function qrcodeApi() {        
        
        $conn = ConnectionManager::get("default"); // name of your database connection  
        
        $this->loadModel('Eform');

        $url  = $_SERVER["REQUEST_URI"];

        $path = explode("/", $url); 

        $_POST['eid'] = $path[4];

        $post_data = isset($_POST['eid']) ? $this->Global->userIdDecode($_POST['eid']) : ''; 
        
        $form_audit_id = explode("|||||", $post_data);

        $id = $form_audit_id[0];

        $typeSendId = $form_audit_id[2];

        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
        
        if(!empty($typeSendId) && $typeSendId == 'invitation') {

            $eforms_sql = $conn->execute("SELECT * FROM eform WHERE `default_invitation_eform_send` = 1 AND id=".$id." AND `soft_delete` = 0");
                    
            $eform = $eforms_sql->fetch('assoc');  

            try {
                if(empty($eform)) {
                    $eform = $this->Eform->get($id, ['conditions' => ['soft_delete' => 0, 'default_invitation_eform_send' => 0]]);
                }
            } catch (Exception $e) {                
                $data_array = array();
                $resultJ = json_encode(array('status' => 1, 'message' => "eform not found", 'data' => $data_array));
                $this->response->type('json');
                $this->response->body($resultJ);
                return $this->response;
            }

            

        } else {

            $eform = $this->Eform->get($id, [
                'conditions' => ['soft_delete' => 0]
            ]);
        }

        if(!empty($eform)) {
                                
            $formName = $eform->formName;
            
            $description = $eform->description;
            
            $instruction = $eform->instruction;
            
            $date = date('Y-m-d H:i:s');
            
            $signature = $eform->signature;
            
            $facematch = $eform->facematch;
            
            $pulldata = $eform->pulldata;
            
            $publish = $eform->publish;
            
            $is_approval = $eform->is_approval;

            $approved_array = array();

            if($is_approval == 1) {

                $sql_query_approval = "SELECT * FROM `eform_approval_process` WHERE fk_eform_id='".$id."'";

                $sql_query_approval_data = $conn->execute($sql_query_approval);

                $result_approval_sql = $sql_query_approval_data->fetchAll('assoc');

                if($result_approval_sql) {

                    foreach($result_approval_sql as $result_sqls) {

                        $random_number = $result_sqls['random_number'];
                        $approved_akcessId = $result_sqls['approval_akcess_id'];
                        $isApprovedNotification = "true";
                        $is_notify_type = $result_sqls['is_notify'];

                        $is_notify_akcess_id = $result_sqls['is_notify_akcess_id'];

                        $get_user_data = $conn->execute("SELECT akcessId FROM users WHERE `id` = ".$is_notify_akcess_id."  AND `soft_delete` = 0");
                        
                        $get_user_data_akcess = $get_user_data->fetch('assoc');  

                        $is_notify_akcess = isset($get_user_data_akcess['akcessId']) ? $get_user_data_akcess['akcessId'] : "";
                        
                        if($approved_akcessId) {
                            $approved_array[$random_number]['approved_akcessId'] = $approved_akcessId;
                            $approved_array[$random_number]['isApprovedNotification'] = $isApprovedNotification;
                            $approved_array[$random_number]['approved'][] = array(
                                'type' => $is_notify_type,
                                'approved_akcessId' => $is_notify_akcess
                            );
                        }

                    }
                    
                }

            }
            
            $additional_notification = $eform->isAdditionalNotification;
            
            $isAdditionalNotificationDetails = array();

            if($additional_notification == 'yes') {

                $sql_query = "SELECT akcessId,email,mobile FROM `additionalnotificationto` WHERE fk_eform_id='".$id."'";

                $sql_query_data = $conn->execute($sql_query);

                $result_sql = $sql_query_data->fetchAll('assoc');
            
                if($result_sql) {

                    foreach($result_sql as $result_sqls) {

                        $akcessIds = $result_sqls['akcessId'];
                        $emails = $result_sqls['email'];
                        $mobiles = $result_sqls['mobile'];

                        $isAdditionalNotificationDetails[] = array(
                            "akcessId" => $akcessIds,
                            "email" => $emails,
                            "mobile" => $mobiles
                        );
                    }

                }

            }

            //$isAdditionalNotificationDetails = rtrim($isAdditionalNotificationDetails, ',');
                    
            $storeinprofile = $eform->storeinprofile;
            
            $this->loadModel('Fields');        
            
            $fieldsInformation_query = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));

            $eformFieldText = array();

            foreach($fieldsInformation_query as $key => $fieldsInformations){
            
                $field_id = isset($fieldsInformations->id) ? $fieldsInformations->id : ''; 
                $name = isset($fieldsInformations->labelname) ? $fieldsInformations->labelname : ''; 
                $instructions = isset($fieldsInformations->instructions) ? $fieldsInformations->instructions : ''; 
                $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
                $isVisible = isset($fieldsInformations->isVisible) ? $fieldsInformations->isVisible : ''; 
                $section_id = isset($fieldsInformations->section_id) ? $fieldsInformations->section_id : ''; 
                $section_color = isset($fieldsInformations->section_color) ? $fieldsInformations->section_color : ''; 
                $sectionfields = isset($fieldsInformations->sectionfields) ? $fieldsInformations->sectionfields : ''; 
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
        
            $approval_status = "false";
            if($is_approval == 1) {
                $approval_status = "true";
            }

            $responseSendID = $form_audit_id[1];
        
            $data_array = array(
                "formName" => $formName,
                "name" => ORIGIN_URL,
                "description" => $description,
                "redirectURL" => "",
                "logo" => $src,
                "eformId" => $eform->eformid,
                "status" => $publish,
                "country" => "",
                "date" => $date,
                "fields" => $eformFieldText,
                "signature" => $signature,
                "facematch" => $facematch,
                "pulldata" => $pulldata,
                "instruction" => $instruction,
                "domainName" => ORIGIN_URL,
                "isAdditionalNotification" => $additional_notification,
                "additionalNotificationTo" => $isAdditionalNotificationDetails,
                "isApproved" => $approval_status,
                "approved" => $approved_array,
                "storeinprofile" => $storeinprofile,
                "typeSendId" => $typeSendId,
                "responseSendID" => $responseSendID,
            );

            $resultJ = json_encode(array('status' => 1, 'message' => "eform found", 'data' => $data_array));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;

        } else {
            $data_array = array();
            $resultJ = json_encode(array('status' => 1, 'message' => "eform not found", 'data' => $data_array));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        }
    }

    public function idcardApi() {        
        
        $this->loadModel('IDCard');

        $url  = $_SERVER["REQUEST_URI"];

        $path = explode("/", $url); 

        $_POST['eid'] = $path[4];

        $post_data = isset($_POST['eid']) ? $this->Global->userIdDecode($_POST['eid']) : '';      

        $form_audit_id = explode("|||||", $post_data);

        $idcardid = $form_audit_id[0];

        $saveSendId = $form_audit_id[1];

        $typeSendId = $form_audit_id[2];
        
        $idcard = $this->IDCard->get($idcardid);

        $fk_users_id = $idcard->fk_users_id;

        $fileUrl = $idcard->fileUrl;

        $dir  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $fileUrl;

        $fileUrl = $dir;

        $fileName = $idcard->fileName;

        $idCardExpiyDate = $idcard->idCardExpiyDate;

        $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($idCardExpiyDate));
             
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
        
        $type_doc = 'idcard';

        $responseSendID = $form_audit_id[1];
                                
        $data_array = array(
            "FileURL"           => $fileUrl,
            "Pdf_name"          => $fileName,
            "idCardExpiyDate"   => $expiryDate,
            "DocumentID"        => $saveSendId,
            "recievedType"      => $type_doc,
            "component_id"      => $saveSendId,
            "component"         => $type_doc,
            "domainName"        => ORIGIN_URL,
            "company_name"      => COMP_NAME,
            "name"              => ORIGIN_URL,
            "logo"              => $src,
            "typeSendId"        => $typeSendId,
            "responseSendID"        => $responseSendID
        );  

        $resultJ = json_encode(array('status' => 1, 'message' => "idcard found", 'data' => $data_array));
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }

    public function guestpassApi() {

        $this->loadModel('GuestPass');

        $url  = $_SERVER["REQUEST_URI"];

        $path = explode("/", $url);

        $_POST['eid'] = $path[4];

        $post_data = isset($_POST['eid']) ? $this->Global->userIdDecode($_POST['eid']) : '';

        $form_audit_id = explode("|||||", $post_data);

        $idcardid = $form_audit_id[0];

        $saveSendId = $form_audit_id[1];

        $typeSendId = $form_audit_id[2];

        $idcard = $this->GuestPass->get($idcardid);

        $fk_users_id = $idcard->id;

        $fileUrl = $idcard->fileUrl;

        $dir  = Router::url("/", true) . "uploads/guestpass/" . $fk_users_id . "/" . $fileUrl;

        $fileUrl = $dir;

        $fileName = $idcard->fileName;
        //
        //        $idCardExpiyDate = $idcard->idCardExpiyDate;

        //        $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($idCardExpiyDate));

        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

        $type_doc = 'guestpass';

        $responseSendID = isset($form_audit_id[1]) ? $form_audit_id[1] : '';

        $data_array = array(
            "FileURL"           => $fileUrl,
            "Pdf_name"          => $fileName,
            //"idCardExpiyDate"   => $expiryDate,
            "DocumentID"        => $saveSendId,
            "recievedType"      => $type_doc,
            "component_id"      => $saveSendId,
            "component"         => $type_doc,
            "domainName"        => ORIGIN_URL,
            "company_name"      => COMP_NAME,
            "name"              => ORIGIN_URL,
            "logo"              => $src,
            "typeSendId"        => $typeSendId,
            "responseSendID"    => $responseSendID
        );

        $resultJ = json_encode(array('status' => 1, 'message' => "Guest pass found", 'data' => $data_array));
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }

    public function documentApi() {        
        
        $this->loadModel('Docs');

        $url  = $_SERVER["REQUEST_URI"];

        $path = explode("/", $url); 

        $_POST['eid'] = $path[4];

        $post_data = isset($_POST['eid']) ? $this->Global->userIdDecode($_POST['eid']) : '';      

        $form_audit_id = explode("|||||", $post_data);

        $idcardid = $form_audit_id[0];

        $saveSendId = $form_audit_id[1];

        $typeSendId = $form_audit_id[2];
        
        $idcard = $this->Docs->get($idcardid);

        $fk_users_id = $idcard->fk_users_id;

        $fileUrl = $idcard->fileUrl;

        $dir  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $fileUrl;

        $fileUrl = $dir;

        $fileName = $idcard->fileName;

        $idCardExpiyDate = $idcard->idCardExpiyDate;

        $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($idCardExpiyDate));
             
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
        
        $type_doc = 'document';

        $responseSendID = $form_audit_id[1];
                                
        $data_array = array(
            "FileURL"           => $fileUrl,
            "Pdf_name"          => $fileName,
            "idCardExpiyDate"   => $expiryDate,
            "DocumentID"        => $saveSendId,
            "recievedType"      => $type_doc,
            "component_id"      => $saveSendId,
            "component"         => $type_doc,
            "domainName"        => ORIGIN_URL,
            "company_name"      => COMP_NAME,
            "name"              => ORIGIN_URL,
            "logo"              => $src,
            "typeSendId"        => $typeSendId,
            "responseSendID"        => $responseSendID
        );  

        $resultJ = json_encode(array('status' => 1, 'message' => "document found", 'data' => $data_array));
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }

    public function qrcode() {
        
        $conn = ConnectionManager::get("default"); // name of your database connection   

        $url  = $_SERVER["REQUEST_URI"];

        $path = explode("/", $url); 

        $page_type =  $path[2];

        $formName = $this->Global->userIdDecode($path[3]);

        $last_eform_id = $this->Global->userIdDecode($path[4]);

        $form_audit_id = explode("|||||", $last_eform_id);

        $second_last_eform_id = $form_audit_id[0];

        $docid = $form_audit_id[1];

        $typeSendId = $form_audit_id[2];
       
        $query_response = "SELECT e.formName,sd.recievedType,e.id FROM sendData as sd
                JOIN eform as e ON e.id = sd.fk_idcard_id
                WHERE sd.id='" . $docid . "'";

        $query_data = $conn->execute($query_response);

        $query_data = $query_data->fetch('assoc');

        $eformName = $formName;
        $recievedType = $query_data['recievedType'];

        $second_last_encode = $second_last_eform_id;

        $document_id = $second_last_encode;

        $dataArray = BASE_ORIGIN_URL . 'api/'.$page_type.'/'.$path[3].'/' . $path[4];

        $data = json_encode($dataArray);

        $this->set(compact('eformName','document_id','data'));

    }

    public function attendance() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $params = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(isset($params['akcessid'])) {
            $params['akcessid'] = $params['akcessid'];
        } else if(isset($params['akcessId'])) {
            $params['akcessid'] = $params['akcessId'];
        }

        if($params && isset($params['akcessid'])) {

            $akcessid = isset($params['akcessid']) ? $params['akcessid'] : "";
            $classid = isset($params['classid']) ? $params['classid'] : "";
            $location = isset($params['location']) ? $params['location'] : "";
            $label_type = isset($params['label_type']) ? $params['label_type'] : "";

            $conn = ConnectionManager::get("default"); // name of your database connection

            $queryUser = 'SELECT *  FROM users where soft_delete = 0 and akcessId = "'.$akcessid.'"';

            $queryUserSql = $conn->execute($queryUser);

            $queryUser_response = $queryUserSql->fetch('assoc');

            $userId = isset($queryUser_response['id']) ? $queryUser_response['id'] : 0;

            if($userId > 0) {

                try {

                    $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                    $querySclassesSql = $conn->execute($querySclasses);

                    $querySclasses_response = $querySclassesSql->fetch('assoc');

                    $label_type = isset($querySclasses_response['label_type']) ? $querySclasses_response['label_type'] : "";

                    if($label_type == 'classes') {

                        $query_data = $conn->execute('SELECT count(*) as total FROM class_attends where classId="'.$classid.'" AND userId="'.$userId.'" AND soft_delete=0');

                        $query_data = $query_data->fetch('assoc');

                        $user_total_count = $query_data['total'];

                    } else if($label_type == 'accesscontrol') {

                        $query_data = $conn->execute('SELECT count(*) as total FROM class_attends where classId="'.$classid.'" AND userId="'.$userId.'" AND soft_delete=0');

                        $query_data = $query_data->fetch('assoc');

                        $user_total_count = $query_data['total'];

                    }

                    if ($user_total_count > 0) {

                        //date_default_timezone_set('Asia/Calcutta');

                        $date = date('Y-m-d H:i:s');

                        $day = strtolower(date('l', strtotime($date)));

                        $current_time = strtotime(date('h:i A', strtotime($date)));

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
                                        if($label_type == 'classes') {
                                            $message = "Check-out the ".$name." class.";
                                        } else {
                                            $message = "Check-out the ".$name;
                                        }

                                        $resultJ = json_encode(
                                            array(
                                                'status' => 'true',
                                                'message' => $message,
                                                'data' => array(
                                                    'label' => "Check-out",
                                                    'label_type' => $label_type
                                                )
                                            )
                                        );
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
                                        if($label_type == 'classes') {
                                            $message = "Check-in the ".$name." class.";
                                        } else {
                                            $message = "Check-in the ".$name;
                                        }

                                        $resultJ = json_encode(
                                            array(
                                                'status' => 'true',
                                                'message' => $message,
                                                'data' => array(
                                                    'label' => "Check-in",
                                                    'label_type' => $label_type
                                                )
                                            )
                                        );
                                        $this->response->type('json');
                                        $this->response->body($resultJ);
                                        return $this->response;
                                    }
                                } else {
                                    if($label_type == 'classes') {
                                        $message = "Already Check-in & Check-Out the ".$name." class.";
                                    } else {
                                        $message = "Already Check-in & Check-Out the ".$name;
                                    }

                                    $resultJ = json_encode(
                                        array(
                                            'status' => 'false',
                                            'message' => $message,
                                            'data' => array(
                                                'label' => "Already Check-in & Check-Out",
                                                'label_type' => $label_type
                                            )
                                        )
                                    );
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

                                if($label_type == 'classes') {
                                    $message = "Check-in the ".$name." class.";
                                } else {
                                    $message = "Check-in the ".$name;
                                }
                                $resultJ = json_encode(
                                    array(
                                        'status' => 'true',
                                        'message' => $message,
                                        'data' => array(
                                            'label' => "Check-in",
                                            'label_type' => $label_type
                                        )
                                    )
                                );
                                $this->response->type('json');
                                $this->response->body($resultJ);
                                return $this->response;
                            }

                        } else {
                            if($label_type == 'classes') {
                                $message = $name." class is closed or not open.";
                            } else {
                                $message = $name." is closed or not open.";
                            }
                            $resultJ = json_encode(
                                array(
                                    'status' => 'false',
                                    'message' => $message,
                                    'data' => array(
                                        'label' => "Closed",
                                        'label_type' => $label_type
                                    )
                                )
                            );
                            $this->response->type('json');
                            $this->response->body($resultJ);
                            return $this->response;

                        }
                    } else {

                        if(!empty($classid)) {
                            $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                            $querySclassesSql = $conn->execute($querySclasses);

                            $querySclasses_response = $querySclassesSql->fetch('assoc');

                            $label_type = isset($querySclasses_response['label_type']) ? $querySclasses_response['label_type'] : "";
                        }

                        if($label_type == 'classes') {
                            $message = "class";
                        } else {
                            $message = "university";
                        }

                        $resultJ = json_encode(
                            array(
                                'status' => 'false',
                                'message' => "You are not registered in this ".$message.".",
                                'data' => array(
                                    'label' => "You are not registered in this ".$message."."
                                )
                            )
                        );
                        $this->response->type('json');
                        $this->response->body($resultJ);
                        return $this->response;

                    }

                } catch(Exception $e) {

                    $resultJ = json_encode(
                        array(
                            'status' => 'false',
                            'message' => "Error: According EDU Server!",
                            'data' => array(
                                'label' => "Error: Server"
                            )
                        )
                    );
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;

                }
            } else {

                if(!empty($classid)) {
                    $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                    $querySclassesSql = $conn->execute($querySclasses);

                    $querySclasses_response = $querySclassesSql->fetch('assoc');

                    $label_type = isset($querySclasses_response['label_type']) ? $querySclasses_response['label_type'] : "";
                }

                if($label_type == 'classes') {
                    $message = "class";
                } else {
                    $message = "university";
                }

                $resultJ = json_encode(
                    array(
                        'status' => 'false',
                        'message' => "You are not registered in this ".$message.".",
                        'data' => array(
                            'label' => "You are not registered in this ".$message."."
                        )
                    )
                );
                $this->response->type('json');
                $this->response->body($resultJ);
                return $this->response;
            }
        }
    }

    public function getattendance() {

        $conn = ConnectionManager::get("default"); // name of your database connection

        $params = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(isset($params['akcessId'])) {
            $params['akcessid'] = $params['akcessId'];
        } else if(isset($params['akcessid'])) {
            $params['akcessid'] = $params['akcessid'];
        }

        // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        // $txt = json_encode($params);
        // fwrite($myfile, "\n". $txt);
        // fclose($myfile);

        if($params && isset($params['akcessid'])) {

            $akcessid = isset($params['akcessid']) ? $params['akcessid'] : "";
            $classid = isset($params['classid']) ? $params['classid'] : "";
            $location = isset($params['location']) ? $params['location'] : "";
            $label_type = isset($params['label_type']) ? $params['label_type'] : "";

            $conn = ConnectionManager::get("default"); // name of your database connection

            $queryUser = 'SELECT *  FROM users where soft_delete = 0 and akcessId = "'.$akcessid.'"';

            $queryUserSql = $conn->execute($queryUser);

            $queryUser_response = $queryUserSql->fetch('assoc');

            $userId = isset($queryUser_response['id']) ? $queryUser_response['id'] : 0;

            // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
            // $txt = json_encode($userId);
            // fwrite($myfile, "\n". $txt);
            // fclose($myfile);

            if($userId > 0) {

                try {

                    $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                    $querySclassesSql = $conn->execute($querySclasses);

                    $querySclasses_response = $querySclassesSql->fetch('assoc');

                    $label_type = isset($querySclasses_response['label_type']) ? $querySclasses_response['label_type'] : "";

                    if($label_type == 'classes') {

                        $query_data = $conn->execute('SELECT count(*) as total FROM class_attends where classId="'.$classid.'" AND userId="'.$userId.'" AND soft_delete=0');

                        $query_data = $query_data->fetch('assoc');

                        $user_total_count = $query_data['total'];

                    } else if($label_type == 'accesscontrol') {

                        $query_data = $conn->execute('SELECT count(*) as total FROM class_attends where classId="'.$classid.'" AND userId="'.$userId.'" AND soft_delete=0');

                        $query_data = $query_data->fetch('assoc');

                        $user_total_count = $query_data['total'];

                    }

                    // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
                    // $txt = json_encode($user_total_count);
                    // fwrite($myfile, "\n". $txt);
                    // fclose($myfile);

                    if ($user_total_count > 0) {

                        //date_default_timezone_set('Asia/Calcutta');

                        $date = date('Y-m-d H:i:s');

                        $day = strtolower(date('l', strtotime($date)));

                        $current_time = strtotime(date('h:i A', strtotime($date)));

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
                            $status = isset($query_attendance['status']) ? $query_attendance['status'] : "";

                            if (isset($id) && $id != "") {
                                if (isset($status) && $status == 0) {
                                    if (!empty($query_attendance) && $query_attendance != "") {

                                        if($label_type == 'classes') {
                                            $message = "Check-out the ".$name." class.";
                                        } else {
                                            $message = "Check-out the ".$name;
                                        }

                                        $resultJ = json_encode(
                                            array(
                                                'status' => 'true',
                                                'message' => $message,
                                                'data' => array(
                                                    'label' => "Check-out",
                                                    'label_type' => $label_type
                                                )
                                            )
                                        );
                                        $this->response->type('json');
                                        $this->response->body($resultJ);
                                        return $this->response;
                                    } else {

                                        if($label_type == 'classes') {
                                            $message = "Check-in the ".$name." class.";
                                        } else {
                                            $message = "Check-in the ".$name;
                                        }

                                        $resultJ = json_encode(
                                            array(
                                                'status' => 'true',
                                                'message' => $message,
                                                'data' => array(
                                                    'label' => "Check-in",
                                                    'label_type' => $label_type
                                                )
                                            )
                                        );
                                        $this->response->type('json');
                                        $this->response->body($resultJ);
                                        return $this->response;
                                    }
                                } else {
                                    if($label_type == 'classes') {
                                        $message = "Already Check-in & Check-Out the ".$name." class.";
                                    } else {
                                        $message = "Already Check-in & Check-Out the ".$name;
                                    }

                                    $resultJ = json_encode(
                                        array(
                                            'status' => 'false',
                                            'message' => $message,
                                            'data' => array(
                                                'label' => "Already Check-in & Check-Out",
                                                'label_type' => $label_type
                                            )
                                        )
                                    );
                                    $this->response->type('json');
                                    $this->response->body($resultJ);
                                    return $this->response;
                                }
                            } else {
                                if($label_type == 'classes') {
                                    $message = "Check-in the ".$name." class.";
                                } else {
                                    $message = "Check-in the ".$name;
                                }
                                $resultJ = json_encode(
                                    array(
                                        'status' => 'true',
                                        'message' => $message,
                                        'data' => array(
                                            'label' => "Check-in",
                                            'label_type' => $label_type
                                        )
                                    )
                                );
                                $this->response->type('json');
                                $this->response->body($resultJ);
                                return $this->response;
                            }

                        } else {
                            if($label_type == 'classes') {
                                $message = $name." class is closed or not open.";
                            } else {
                                $message = $name." is closed or not open.";
                            }
                            $resultJ = json_encode(
                                array(
                                    'status' => 'false',
                                    'message' => $message,
                                    'data' => array(
                                        'label' => "Closed",
                                        'label_type' => $label_type
                                    )
                                )
                            );
                            $this->response->type('json');
                            $this->response->body($resultJ);
                            return $this->response;

                        }
                    } else {

                        if(!empty($classid)) {
                            $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                            $querySclassesSql = $conn->execute($querySclasses);

                            $querySclasses_response = $querySclassesSql->fetch('assoc');

                            $label_type = isset($querySclasses_response['label_type']) ? $querySclasses_response['label_type'] : "";
                        }

                        if($label_type == 'classes') {
                            $message = "class";
                        } else {
                            $message = "university";
                        }

                        $resultJ = json_encode(
                            array(
                                'status' => 'false',
                                'message' => "You are not registered in this ".$message.".",
                                'data' => array(
                                    'label' => "You are not registered in this ".$message.".",
                                )
                            )
                        );
                        $this->response->type('json');
                        $this->response->body($resultJ);
                        return $this->response;

                    }

                } catch(Exception $e) {

                    $resultJ = json_encode(
                        array(
                            'status' => 'false',
                            'message' => "Error: According EDU Server!",
                            'data' => array(
                                'label' => "Error: Server"
                            )
                        )
                    );
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;

                }
            } else {

                if(!empty($classid)) {
                    $querySclasses = 'SELECT *  FROM sclasses where soft_delete = 0 and id = '.$classid;

                    $querySclassesSql = $conn->execute($querySclasses);

                    $querySclasses_response = $querySclassesSql->fetch('assoc');

                    $label_type = isset($querySclasses_response['label_type']) ? $querySclasses_response['label_type'] : "";
                }

                if($label_type == 'classes') {
                    $message = "class";
                } else {
                    $message = "university";
                }

                $resultJ = json_encode(
                    array(
                        'status' => 'false',
                        'message' => "You are not registered in this ".$message.".",
                        'data' => array(
                            'label' => "You are not registered in this ".$message.".",
                        )
                    )
                );
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
                
            } else if($component == 'guestpass') {

                $message = "Guest Pass";

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

                        $this->loadModel('GuestPass');

                        if($deeplink_type == 'document') {
                    
                            $idcard = $this->Docs->get($getSendID->fk_idcard_id);
                            
                            $message = "Document";
                            
                        } else if($deeplink_type == 'idcard') {
                    
                            $idcard = $this->IDCard->get($getSendID->fk_idcard_id);
                            
                            $message = "ID Card";
                            
                        } else if($deeplink_type == 'guestpass') {

                            $idcard = $this->GuestPass->get($getSendID->fk_idcard_id);

                            $message = "Guest Pass";

                        } else if($deeplink_type == 'eform') {
                    
                            $idcard = $this->Eform->get($getSendID->fk_idcard_id);
                            
                            $message = "Eform";
                            
                        } 

                        if($deeplink_type == 'document' || $deeplink_type == 'idcard' || $deeplink_type == 'guestpass') {

                            if($deeplink_type == 'guestpass')
                            {
                                $fk_users_id = $idcard->id;

                            //$user = $this->Users->get($fk_users_id, [
                            //  'conditions' => ['soft_delete' => 0]
                            //]);

                                $user = array();
                                $fileUrl = $idcard->fileUrl;

                                $dir  = Router::url("/", true) . "uploads/guestpass/" . $fk_users_id . "/" . $fileUrl;

                            }
                            else
                            {
                                $fk_users_id = $idcard->fk_users_id;

                                $user = $this->Users->get($fk_users_id, [
                                    'conditions' => ['soft_delete' => 0]
                                ]);

                                $fileUrl = $idcard->fileUrl;

                                $dir  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $fileUrl;

                            }

                            if($deeplink_type == 'guestpass')
                            {
                                $akcessId = isset($idcard->akcessID) && $idcard->akcessID ? $idcard->akcessID : '';
                            }
                            else
                            {
                                $akcessId = isset($idcard->AkcessID) && $idcard->AkcessID ? $idcard->AkcessID : '';
                            }


                            $response['idNo'] = isset($idcard->idNo) && $idcard->idNo ? $idcard->idNo : '';
                            $response['expiryDate'] = isset($idcard->idCardExpiyDate) && $idcard->idCardExpiyDate ? date("Y-m-d H:i:s", strtotime($idcard->idCardExpiyDate)) : null;
                            $response['document_hash'] = isset($idcard->documentHash) && $idcard->documentHash ? [$idcard->documentHash] : '';
                            $response['transaction'] = isset($idcard->transactionID) && $idcard->transactionID ? [$idcard->transactionID] : '';
                            $response['channel_name'] = isset($idcard->channelName) && $idcard->channelName ? $idcard->channelName : '';
                            $response['document_ID'] = isset($idcard->documentId) ? $idcard->documentId : '';
                            $response['file_url'] = $dir;
                            $response['file_name'] = isset($idcard->fileName) ? $idcard->fileName : '';
                            $response['akcessId'] = $akcessId;
                            $response['signatureHash'] = isset($idcard->signatureHash) ? $idcard->signatureHash : '';
                            $response['verifier_name'] = isset($idcard->verifier_name) ? $idcard->verifier_name : '';
                            $response['domain_name'] = ORIGIN_URL; 
                            $response['_id'] = isset($getSendID->id) ? $getSendID->id : '';
                            $response['id'] = isset($getSendID->id) ? $getSendID->id : '';
                            $response['deeplink_type'] = $deeplink_type;  
                            $response['recievedType'] = isset($getSendID->recievedType) ? $getSendID->recievedType : '';

                            if($deeplink_type == 'guestpass')
                            {
                                $firstName = isset($idcard->first_name) ? $idcard->first_name : "";
                                $lastName = isset($idcard->last_name) ? $idcard->last_name : "";
                            }
                            else
                            {
                                $name = isset($user->name) ? explode(" ", $user->name) : "";
                                $firstName = isset($name[0]) ? $name[0] : "";
                                $lastName = isset($name[1]) ? $name[1] : "";
                            }


                            $response['firstName'] = $firstName;  
                            $response['lastName'] = $lastName;  
                            $response['DOB'] = isset($user->dob) && $user->dob ? date("Y-m-d H:i:s", strtotime($user->dob)) : null;

                            if($deeplink_type == 'guestpass')
                            {
                                $response['email'] = isset($idcard->email) ? $idcard->email : '';
                                $response['phone'] = isset($idcard->mobile) ? $idcard->mobile : '';
                            }
                            else
                            {
                                $response['email'] = isset($user->email) ? $user->email : '';
                                $response['phone'] = isset($user->mobileNumber) ? $user->mobileNumber : '';
                            }

                            
                            
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

        // $myfile = fopen("/var/www/html/edu/logs/saveEformresponse.txt", "a") or die("Unable to open file!");
        // $txt = json_encode($_POST);
        // fwrite($myfile, $txt);
        // fclose($myfile); 

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

//            if($facematch == 'yes') {
//
//                if(!empty($_FILES['profilepic']['name'])) {
//
//                    $file_tmpname_profilepic = $_FILES['profilepic']['tmp_name'];
//
//                    $file_name_profilepic = $_FILES['profilepic']['name'];
//
//                    $file_size_profilepic = $_FILES['profilepic']['size'];
//
//                    $file_ext_profilepic = pathinfo($file_name_profilepic, PATHINFO_EXTENSION);
//
//                    $flname = $_POST['eformId'];
//
//                    $profilepic_file_name = $file_name_profilepic;
//
//                    $profilepic_image_name = explode('.', $profilepic_file_name);
//
//                    unset($profilepic_image_name[count($profilepic_image_name) - 1]);
//
//                    $profilepic_image_fullname = $dir . implode('.', $profilepic_image_name) . ".jpeg";
//
//                    $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";
//
//                    chmod($dir, 0777);
//
//                    if( is_dir($dir) === false )
//                    {
//                        mkdir($dir, 0777, true);
//                    }
//
//                    $uploadFile_profilepic = $dir . $profilepic_file_name;
//
//                    move_uploaded_file($file_tmpname_profilepic, $uploadFile_profilepic);
//
//                    chmod($uploadFile_profilepic, 0777);
//
//                    $pdf = new \Spatie\PdfToImage\Pdf($uploadFile_profilepic);
//
//                    $pdf->saveImage($profilepic_image_fullname);
//
//                    chmod($profilepic_image_fullname, 0777);
//                    // Read image path, convert to base64 encoding
//                    $profilepic_imgData = base64_encode(file_get_contents($profilepic_image_fullname));
//                }
//
//            }
            // profilepic


            $fields = isset($_POST['fields']) ? $_POST['fields'] : '';
            if(isset($_FILES['profilepic']['name']) && !empty($_FILES['profilepic']['name'])) {

                $file_tmpname_profilepic = $_FILES['profilepic']['tmp_name'];

                $file_name_profilepic = $_FILES['profilepic']['name'];

//                $file_size_profilepic = $_FILES['profilepic']['size'];
//
//                $file_ext_profilepic = pathinfo($file_name_profilepic, PATHINFO_EXTENSION);

                $flname = $_POST['eformId'];

                $profilepic_file_name = $file_name_profilepic;

                $profilepic_image_name = explode('.', $profilepic_file_name);

                unset($profilepic_image_name[count($profilepic_image_name) - 1]);


                $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";

                chmod($dir, 0777);

                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
                $profilepic_image_fullname = $dir . implode('.', $profilepic_image_name) . ".jpeg";

                $uploadFile_profilepic = $dir . $profilepic_file_name;

                $profile_pic = "/uploads/eforms/" . $flname . "/".$profilepic_file_name;


                $fId = isset($fields[0]['id']) && $fields[0]['id'] ? $fields[0]['id'] : strtotime(date('Y-m-d H:i:s'));
                $fields[] = array(
                    "id"=> $fId,
                    "labelname"=> "Profile Pic",
                    "instructions"=> "",
                    "ismandatory"=> "no",
                    "datetype"=> "both",
                    "options"=> "[]",
                    "key"=> "profilepic",
                    "keytype"=> "file",
                    "sectionfields"=> "",
                    "sectionid"=> "",
                    "value"=>$profile_pic,
                    "iseditable"=> "true",
                    "verifystatus"=> "false"
                );

                move_uploaded_file($file_tmpname_profilepic, $uploadFile_profilepic);

                chmod($uploadFile_profilepic, 0777);

//                $pdf = new \Spatie\PdfToImage\Pdf($uploadFile_profilepic);
//
//                $pdf->saveImage($profilepic_image_fullname);
//
//                chmod($profilepic_image_fullname, 0777);
                // Read image path, convert to base64 encoding
                $profilepic_imgData = base64_encode(file_get_contents($profilepic_image_fullname));
            }


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
            $responseSendID = isset($_POST['responseSendID']) ? $_POST['responseSendID'] : 0;
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
            $eformResponse->responseSendID = $responseSendID;
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
                'eformResponseId' => $eformResponseId,
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
                'eformasfile_approval' => $eformasfile_file_name,
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

            $sql_last_query1 = "SELECT id FROM `users` WHERE `akcessId` LIKE '".$akcessId."'";
            $sql_last_query_id1 = $conn->execute($sql_last_query1);
            $sql_last_id1       = $sql_last_query_id1->fetch('assoc');
            $userId             = isset($sql_last_id1['id']) ? $sql_last_id1['id'] : 0;

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
                'eformasfile_approval' => $eformasfile,
                'facialMatch' => $facialMatch,
                'date' => $date,
                'approved' => $approved, 
                'status' => $status,
                'pulldata' => $pulldata,
                'facematch' => $facematch,
                'mobile_local_id' => $mobile_local_id,
                'strict' => $strict,
                'user_id' => $userId,
                'fk_user_id' => $userId
            );


            $this->Global->auditTrailApi($eformResponseId, 'eformresponse', 'eFormSubmit', null, $after);
            //$this->Global->auditTrailApi($eformResponseId, 'eformresponse', 'insert', null, $after);

//            $fields = isset($_POST['fields']) ? $_POST['fields'] : '';

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
                    $expiryDate = isset($fieldsValues['expiryDate']) ? $fieldsValues['expiryDate'] : date('Y-m-d h:i:s',strtotime('+1 year'));
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


//                    $sql_last_query1 = "SELECT id FROM `users` WHERE `akcessId` LIKE '".$akcessId."'";
//                    $sql_last_query_id1 = $conn->execute($sql_last_query1);
//                    $sql_last_id1       = $sql_last_query_id1->fetch('assoc');
//                    $userId             = isset($sql_last_id1['id']) ? $sql_last_id1['id'] : 0;

//                    $after = array(
//                        'user_id' => $userId,
//                        'fk_user_id' => $userId,
//                        'fk_eformresponse_id' => $fk_eformresponse_id,
//                        'fk_eform_id' => $fk_eform_id,
//                        'labelname' => $labelname,
//                        'keyfields' => $keyfields,
//                        'keytype' => $keytype,
//                        'value' => $value,
//                        'file' => $verify_status,
//                        'verify_status' => $file_verified,
//                        'expiryDate' => $expiryDate,
//                        'isverified' => $isverified,
//                        'docuementType' => $docuementType,
//                        'isDocFetched' => $isDocFetched,
//                        'signature_required' => $signature_required,
//                        'options' => $options,
//                        'verification_grade' => $verification_grade,
//                        'section_id' => $section_id,
//                        'section_color' => $section_color,
//                        'sectionfields' => $fieldsResponse->sectionfields,
//                        'akcessId' => $akcessId
//                    );


//                    if($is_log_save == 0)
//                    {
//                    $this->Global->auditTrailApi($fieldsResponseId, 'fieldsresponse', 'eFormSubmit', null, $after);
//                        $is_log_save = 1;
//                    }



                }

            }    

            $fieldsp = isset($_POST['publicFields']) ? $_POST['publicFields'] : '';

            // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
            // $txt = json_encode($fieldsp);
            // fwrite($myfile, "\n". $txt);
            // fclose($myfile); 

            $user_invitation = array();

            if($fieldsp) {

                $this->loadModel('FieldsResponse');

                $fieldspRes = $this->FieldsResponse->newEntity();

                $fieldspResponse = $this->FieldsResponse->patchEntity($fieldspRes, $_POST);
                $is_public = 1;
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
                    $expiryDate = isset($fieldspValues['expiryDate']) ? $fieldspValues['expiryDate'] : date('Y-m-d h:i:s',strtotime('+1 year'));
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


                    $user_invitation[$keyfields] = $value;

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
                    $fieldspResponse->is_public = $is_public;
                    $fieldspResponse->docuementType = $docuementType;
                    $fieldspResponse->isDocFetched = $isDocFetched;
                    $fieldspResponse->signature_required = $signature_required;
                    $fieldspResponse->options = $options;
                    $fieldspResponse->verification_grade = $verification_grade;
                    $fieldspResponse->section_id = isset($section_id) ? serialize($section_id) : "";
                    $fieldspResponse->section_color = isset($section_color) ? serialize($section_color) : "";
                    $fieldspResponse->sectionfields = isset($sectionfields) ? serialize($sectionfields) : "";

                    $sql_query = "INSERT INTO `fieldsresponse` (`fk_eformresponse_id`, `fk_eform_id`, `labelname`, `keyfields`,
                     `keytype`, `value`, `file`, `verify_status`, `file_verified`, `expiryDate`, `isverified`, `is_public`, `docuementType`,
                      `documentData`,`isDocFetched`, `signature_required`, `options`, `verification_grade`, `section_id`, `section_color`,
                       `sectionfields`) VALUES (".$fk_eformresponse_id.",'".$fk_eform_id."','".$labelname."','".$keyfields."',
                       '".$keytype."','".$value."','".$file."','".$verify_status."','".$file_verified."','".$expiryDate."',
                       '".$isverified."','".$is_public."','".$docuementType."','".$valuefile_jsonData."','".$isDocFetched."','".$signature_required."',
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
                        'is_public' => $is_public,
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

            // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
            // $txt = json_encode($user_invitation);
            // fwrite($myfile, "\n". $txt);
            // fclose($myfile); 
            
            if(isset($_POST['responseSendID']) && $_POST['responseSendID'] != "") {

                $sql_fields_users_by_akcess = "SELECT id FROM `users_by_akcess` WHERE fk_senddata_id= ".$_POST['responseSendID']." ORDER BY id DESC LIMIT 0,1";

                $sql_fields__users_by_akcess = $conn->execute($sql_fields_users_by_akcess);

                $sql_last_fields_users_by_akcess = $sql_fields__users_by_akcess->fetch('assoc');

                // $myfile = fopen("/var/www/html/edu/logs/sql_fields_users_by_akcess.txt", "a") or die("Unable to open file!");
                //  $txt = json_encode($sql_fields_users_by_akcess);
                //  fwrite($myfile, "\n". $txt);
                //  fclose($myfile); 

                $id = !empty($sql_last_fields_users_by_akcess['id']) ? $sql_last_fields_users_by_akcess['id'] : 0;                

                foreach($user_invitation as $key => $value){    
                    
                    if ($key == 'firstname') {
                        $firstname = $value;
                    }
                    if ($key == 'lastname') {
                        $lastname = $value;
                    }
                    if ($key == 'email') {
                        $email = $value;
                    }
                    if ($key == 'mobile') {
                        $mobileNumber = $value;
                    }
                    if ($key == 'dateofbirth') {
                        $dob = $value;
                    }
                    if ($key == 'place of birth') {
                        $place_of_birth = $value;
                    }
                    if ($key == 'gender') {
                        $gender = $value;
                    }
                    if ($key == 'faculty') {
                        $faculty = $value;
                    }
                    if ($key == 'courses') {
                        $courses = $value;
                    }
                    if ($key == 'admission date') {
                        $adminssion_date = $value;
                    }
                    if ($key == 'address') {
                        $address = $value;
                    }
                    if ($key == 'countryofresidence') {
                        $nationality = $value;
                    }
                    if ($key == 'staff type') {
                        $staff_type = $value;
                    }
                    if ($key == 'academic personal type') {
                        $academic_personal_type = $value;
                    }
    
                }
    
                $firstname = isset($firstname) ? $firstname : "";
                $lastname = isset($lastname) ? $lastname : "";
                $akcessId = isset($akcessId) ? $akcessId : "";
                $companyName = isset($companyName) ? $companyName : "";
                $address = isset($address) ? $address : "";
                $city = isset($place_of_birth) ? $place_of_birth : "";
                $country = isset($nationality) ? $nationality : "";
                $email = isset($email) ? $email : "";
                $mobileNumber = isset($mobileNumber) ? $mobileNumber : "";
                $gender = isset($gender) ? $gender : "";
                $dob = isset($dob) ? $dob : "";
                $otherdetails = isset($otherdetails) ? $otherdetails : "";
                $active = isset($active) ? $active : 1;
                $faculty = isset($faculty) ? $faculty : "";
                $courses = isset($courses) ? $courses : "";
                $academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : 0;
                $staff_type = isset($staff_type) ? $staff_type : 0;
                $usertype = isset($usertype) ? ucfirst($usertype) : "";
                $adminssion_date = isset($adminssion_date) ? $adminssion_date : "";
                $loginOpt = isset($loginOpt) ? $loginOpt : 'pin';
                $siteStatus = isset($siteStatus) ? $siteStatus : 'Development';
                $status = 1;
    
                $name = $firstname . " " . $lastname;
    
                $insert_query = 'INSERT INTO `users_invitation` (`name`,`akcessId`,`companyName`,`address`,`city`,`country`,`email`,`mobileNumber`,`gender`,`dob`,`otherdetails`,`active`,`faculty`,`courses`,`academic_personal_type`,`staff_type`,`usertype`,`adminssion_date`,`loginOpt`,`siteStatus`,`status`,`users_by_akcess_id`) VALUES ("'.$name.'","'.$akcessId.'","'.$companyName.'","'.$address.'","'.$city.'","'.$country.'","'.$email.'","'.$mobileNumber.'","'.$gender.'","'.$dob.'","'.$otherdetails.'","'.$active.'","'.$faculty.'","'.$courses.'","'.$academic_personal_type.'","'.$staff_type.'","'.$usertype.'","'.$adminssion_date.'","'.$loginOpt.'","'.$siteStatus.'","'.$status.'",'.$id.')';        
                
                // $myfile = fopen("/var/www/html/edu/logs/sql_fields_users_invitation.txt", "a") or die("Unable to open file!");
                // $txt = json_encode($insert_query);
                // fwrite($myfile, "\n". $txt);
                // fclose($myfile); 
    
                $conn->execute($insert_query);
    
                $sql_fields = "SELECT id FROM `users_invitation` ORDER BY id DESC LIMIT 0,1";
    
                $sql_fields_id = $conn->execute($sql_fields);
    
                $sql_last_fields = $sql_fields_id->fetch('assoc');
    
                $insertedId = $sql_last_fields['id'];
    
                $idcard_random = $this->Global->random_string('numeric', 10) . $insertedId;
    
                $update_query = "UPDATE `users_invitation` set `idcardno`='".$idcard_random."' WHERE id=".$insertedId;
    
                $conn->execute($update_query);

            }

            if(isset($_POST['responseSendID']) && $_POST['responseSendID'] != "") {

                $responseSendID = $_POST['responseSendID'];

                $update_query = "UPDATE `users_by_akcess` set `status`=0 WHERE fk_senddata_id=".$responseSendID;

                $conn->execute($update_query);

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

    public function genRandomNumber($length = 15, $formatted = true) {
        $nums = '0123456789';
    
       // First number shouldn't be zero
        $out = $nums[mt_rand( 1, strlen($nums)-1 )];  
    
       // Add random numbers to your string
        for ($p = 0; $p < $length-1; $p++)
            $out .= $nums[mt_rand( 0, strlen($nums)-1 )];
    
      // Format the output with commas if needed, otherwise plain output
        if ($formatted)
            return number_format($out);
        return $out;
    }

    public function saveEformResponseApprovalProcess() {

        $conn = ConnectionManager::get("default"); // name of your database connection     

        if(isset($_POST['akcessId'])) {
            $_POST['akcessId'] = $_POST['akcessId'];
        } else if(isset($_POST['akcessid'])) {
            $_POST['akcessId'] = $_POST['akcessid'];
        }

        $_POST['eformResponseId'] = $_POST['eformResponseId'];
        $_POST['status'] = $_POST['status'];

        if($_POST && (isset($_POST['akcessId']) && isset($_POST['eformResponseId']))) {

            $this->loadModel('EformResponse');

            $eformResponseId = $_POST['eformResponseId'];
            $akcessid = $_POST['akcessId'];
            $status = $_POST['status'];

            $queryEform = 'SELECT *  FROM eformresponse where soft_delete = 0 and id = '.$eformResponseId;

            $queryEformSql = $conn->execute($queryEform);

            $queryEform_response = $queryEformSql->fetch('assoc');

            $eformId = isset($queryEform_response['eformId']) ? $queryEform_response['eformId'] : "";

            // eformasfile

            if(!empty($_FILES['eformasfile']['name'])) {

                $file_tmpname_eformasfile = $_FILES['eformasfile']['tmp_name'];
            
                $file_name_eformasfile = $_FILES['eformasfile']['name'];
                
                $flname = $eformId;

                $filename_without_ext  = pathinfo($file_name_eformasfile, PATHINFO_FILENAME);

                $file_ext_eformasfile = pathinfo($file_name_eformasfile, PATHINFO_EXTENSION);
        
                $eformasfile_file_name = $filename_without_ext . $this->genRandomNumber(14,false) . '.' . $file_ext_eformasfile;
        
                $dir  = WWW_ROOT . "/uploads/eforms/" . $flname . "/";

                chmod($dir, 0777);

                if( is_dir($dir) === false )
                {
                    mkdir($dir, 0777, true);
                }
        
                $uploadFile_eformasfile = $dir . $eformasfile_file_name;
        
                move_uploaded_file($file_tmpname_eformasfile, $uploadFile_eformasfile);

                $update_query = "UPDATE `eformresponse` set `eformasfile_approval`='".$eformasfile_file_name."' WHERE id=".$eformResponseId;

                $conn->execute($update_query); 

            }

            // eformasfile    
            
            $date = date('Y-m-d H:i:s');   

            $insert_query = "INSERT INTO `approvalresponse` (
                `fk_eformresponse_id`, 
                `fk_eform_id`, 
                `approval_akcess_id`, 
                `eformasfile`,
                `status`,
                `created`
            ) VALUES (
                ".$eformResponseId.",
                '".$eformId."',
                '".$akcessid."',
                '".$eformasfile_file_name."',
                '".$status."',
                '".$date."'
            )";

            $conn->execute($insert_query);

            $sql_last_query_fields = "SELECT id FROM `approvalresponse` ORDER BY id DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];

            $data = array(
                "akcessId" => $akcessid,
                "fk_eformresponse_id" => $eformResponseId,
                "status" => $status,
                "requestId" => $lastfieldsInsertedId
            ); 

            
            $response['data'] = $data;
            $response['message'] = 'Eform response successfully status update.';
            $response['status'] = 1;

            return $response;
        } else {
            if (empty($params['akcessid'])) {
                $response['data'] = [];
                $response['message'] = 'AKcess ID not found.';
                $response['status'] = 0;
            } else if (empty($params['eformResponseId'])) {
                $response['data'] = [];
                $response['message'] = 'PDF file not found.';
                $response['status'] = 0;
            }
        }

        return $response;
    }
    
    public function getEformResponseApprovalProcess() {

        $params = (array) json_decode(file_get_contents('php://input'), TRUE);

        $params['akcessId'] = $_POST['akcessId'];
        $params['eformResponseId'] = $_POST['eformResponseId'];

        if(isset($params['akcessId'])) {
            $params['akcessid'] = $params['akcessId'];
        } else if(isset($params['akcessid'])) {
            $params['akcessid'] = $params['akcessid'];
        }

        $params['eformResponseId'] = $params['eformResponseId'];

        if($params && (isset($params['akcessid']) && isset($params['eformResponseId']))) {
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('EformResponse');

            $eformResponseId = $params['eformResponseId'];
            $akcessid = $params['akcessid'];

            $queryEform = 'SELECT *  FROM eformresponse where soft_delete = 0 and id = '.$eformResponseId;

            $queryEformSql = $conn->execute($queryEform);

            $queryEform_response = $queryEformSql->fetch('assoc');

            $eformasfile_approval = isset($queryEform_response['eformasfile_approval']) ? $queryEform_response['eformasfile_approval'] : "";
            $eformasfile = isset($queryEform_response['eformasfile']) ? $queryEform_response['eformasfile'] : "";
            
            $eformId = isset($queryEform_response['eformId']) ? $queryEform_response['eformId'] : "";

            if(!empty($eformasfile_approval) && $eformasfile_approval) {
            $pdf_file_response_link  = Router::url("/", true) . "uploads/eforms/" . $eformId . "/" . $eformasfile_approval;
            } else {
                $pdf_file_response_link  = Router::url("/", true) . "uploads/eforms/" . $eformId . "/" . $eformasfile;
            }

            if(!empty($eformasfile_approval) && $eformasfile_approval) {
            $dir  = WWW_ROOT . "/uploads/eforms/" . $eformId . "/" . $eformasfile_approval;
            } else {
                $dir  = WWW_ROOT . "/uploads/eforms/" . $eformId . "/" . $eformasfile;
            }

            
            // A few settings
            $img_file = $dir;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $pdf_file_response_src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

            $responseEformData = array(
                'eformResponseId' => $eformResponseId,
                'akcessid' => $akcessid,
                'pdf_file_response_link' => $pdf_file_response_link,
                //'pdf_file_response_src' => $pdf_file_response_src
            );

            $response['data'] = $responseEformData;
            $response['message'] = 'Eform response latest PDF file found it.';
            $response['status'] = 1;

            return $response;
        } else {
            if (empty($params['akcessid'])) {
                $response['data'] = [];
                $response['message'] = 'AKcess ID not found.';
                $response['status'] = 0;
            } else if (empty($params['eformResponseId'])) {
                $response['data'] = [];
                $response['message'] = 'PDF file not found.';
                $response['status'] = 0;
            }
        }

        return $response;
    }

    public function getDocumentByAkcessID() {

        //$params = (array) json_decode(file_get_contents('php://input'), TRUE);
       
        if(isset($_REQUEST['akcessId'])) {
            $params['akcessid'] = $_REQUEST['akcessId'];
        } else if(isset($_REQUEST['akcessid'])) {
            $params['akcessid'] = $_REQUEST['akcessid'];
        }

        if($params && (isset($params['akcessid']))) {
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('Users');

            $this->loadModel('IDCard');

            $this->loadModel('Docs');

            $akcessId = $params['akcessid'];

            $sql_query_docs = "SELECT `idcard`.*,`users`.* FROM `users` 
            LEFT JOIN `idcard` ON `users`.`id`=`idcard`.`fk_users_id`
            WHERE `users`.`akcessId`='".$akcessId."' AND `idcard`.`soft_delete`=0 
            AND `users`.`soft_delete`=0";

            $sql_query_data_docs = $conn->execute($sql_query_docs);

            $result_sql_docs = $sql_query_data_docs->fetchAll('assoc');

            $response = array();
           
            if($result_sql_docs) {
           
                foreach ($result_sql_docs as $t) { 

                    $idcard_fileurl = $t['fileUrl'];
                    $fk_users_id = $t['fk_users_id'];

                    $idcard_file_response_link  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $idcard_fileurl;
        
                    $idcard_dir  = WWW_ROOT . "/uploads/attachs/" . $fk_users_id . "/" . $idcard_fileurl;        
                    
                    // A few settings
                    $idcard_file = $idcard_dir;
                    // Read image path, convert to base64 encoding
                    $idCardData = base64_encode(file_get_contents($idcard_file));
        
                    // Format the image SRC:  data:{mime};base64,{data};
                    $idcard_file_response_src = 'data:' . mime_content_type($idcard_file) . ';base64,' . $idCardData;

                    $response['idcard'][] = array(
                        'akcessID' => $akcessId,
                        'idNo' => $t['idNo'],
                        'idCardExpiyDate' => $t['idCardExpiyDate'],
                        'documentHash' => $t['documentHash'],
                        'signatureHash' => $t['signatureHash'],
                        'Title' => $t['Title'],
                        'transactionID' => $t['transactionID'],
                        'timeStamp' => $t['timeStamp'],
                        'channelName' => $t['channelName'],
                        'documentId' => $t['documentId'],
                        'fileUrl' => $t['fileUrl'],
                        'fileName' => $t['fileName'],
                        'image_fileName' => $t['image_fileName'],
                        'idcard_file_response_link' => $idcard_file_response_link,
                        //'idcard_file_response_src' => $idcard_file_response_src
                    );
                }
            }

            $sql_query_docs = "SELECT `docs`.*,`users`.*,`documenttypelist`.`name` as doc_type FROM `users` 
            LEFT JOIN `docs` ON `users`.`id`=`docs`.`fk_users_id`
            LEFT JOIN `documenttypelist` ON `documenttypelist`.`id`=`docs`.`fk_documenttype_id`
            WHERE `users`.`akcessId`='".$akcessId."' AND `docs`.`soft_delete`=0 
            AND `users`.`soft_delete`=0";

            $sql_query_data_docs = $conn->execute($sql_query_docs);

            $result_sql_docs = $sql_query_data_docs->fetchAll('assoc');

            if($result_sql_docs) {
           
                foreach ($result_sql_docs as $t) { 

                    $doc_fileurl = $t['fileUrl'];
                    $fk_users_id = $t['fk_users_id'];

                    $doc_file_response_link  = Router::url("/", true) . "uploads/attachs/" . $fk_users_id . "/" . $doc_fileurl;
        
                    $doc_dir  = WWW_ROOT . "/uploads/attachs/" . $fk_users_id . "/" . $doc_fileurl;        
                    
                    // A few settings
                    $doc_file = $doc_dir;
                    // Read image path, convert to base64 encoding
                    $docData = base64_encode(file_get_contents($doc_file));
        
                    // Format the image SRC:  data:{mime};base64,{data};
                    $doc_file_response_src = 'data:' . mime_content_type($doc_file) . ';base64,' . $docData;

                    $response['doc'][] = array(
                        'akcessID' => $akcessId,
                        'name' => $t['name'],
                        'doc_type' => $t['doc_type'],
                        'idCardExpiyDate' => $t['idCardExpiyDate'],
                        'documentHash' => $t['documentHash'],
                        'signatureHash' => $t['signatureHash'],
                        'Title' => $t['Title'],
                        'transactionID' => $t['transactionID'],
                        'timeStamp' => $t['timeStamp'],
                        'channelName' => $t['channelName'],
                        'documentId' => $t['documentId'],
                        'fileUrl' => $t['fileUrl'],
                        'fileName' => $t['fileName'],
                        'image_fileName' => $t['image_fileName'],
                        'doc_file_response_link' => $doc_file_response_link,
                        //'doc_file_response_src' => $doc_file_response_src
                    );
                }
            }

            $data['data'] = $response;
            $data['message'] = 'ID cards & Document file found it.';
            $data['status'] = 1;
        } else {            
            $data['data'] = [];
            $data['message'] = 'AKcess ID not found.';
            $data['status'] = 0;
        }

        return $data;
    }

    public function getUserDataByAKcessID() {

        $params = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(isset($params['akcessId'])) {
            $params['akcessid'] = $params['akcessId'];
        } else if(isset($params['akcessid'])) {
            $params['akcessid'] = $params['akcessid'];
        }

        if($params && (isset($params['akcessid']))) {
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('Users');
            
            $akcessId = $params['akcessid'];

            $sql_query_docs = "SELECT `users`.* FROM `users`
            WHERE `users`.`akcessId`='".$akcessId."' AND `users`.`soft_delete`=0";

            $sql_query_data_docs = $conn->execute($sql_query_docs);

            $result_sql_docs = $sql_query_data_docs->fetchAll('assoc');

            $response = array();
           
            if($result_sql_docs) {
           
                foreach ($result_sql_docs as $t) { 
                    $idcardno = $t['idcardno'];
                    $name = $t['name'];
                    $companyName = $t['companyName'];
                    $address = $t['address'];
                    $city = $t['city'];
                    $country = $t['country'];
                    $email = $t['email'];
                    $mobileNumber = $t['mobileNumber'];
                    $gender = $t['gender'];
                    $dob = $t['dob'];
                    $akcessId = $t['akcessId'];

                    $country_name = 0;
                    if (isset($country) && $country != "") {                       
                        $query_data = $conn->execute('SELECT * FROM countries where id = "' . $country . '"');
                        $query_country = $query_data->fetch('assoc');
                        $country_name = isset($query_country['country_name']) ? $query_country['country_name'] : "";
                    }

                    $response = array(
                        'akcessId' => $akcessId,
                        'idcardno' => $idcardno,
                        'name' => $name,
                        'companyName' => $companyName,
                        'address' => $address,
                        'city' => $city,
                        'country_name' => $country_name,
                        'email' => $email,
                        'mobileNumber' => $mobileNumber,
                        'gender' => $gender,
                        'dob' => $dob,
                    );
                }
            }

            $data['results'] = $response;
            $data['message'] = 'Users data found it.';
            $data['status'] = 1;
        } else {            
            $data['results'] = [];
            $data['message'] = 'AKcess ID not found.';
            $data['status'] = 0;
        }

        return $data;
    }

    public function getEformByAkcessID() {

        $params = (array) json_decode(file_get_contents('php://input'), TRUE);

        if(isset($params['akcessId'])) {
            $params['akcessid'] = $params['akcessId'];
        } else if(isset($params['akcessid'])) {
            $params['akcessid'] = $params['akcessid'];
        }

        if($params && (isset($params['akcessid']))) {
               
            $conn = ConnectionManager::get("default"); // name of your database connection     

            $this->loadModel('Users');
            
            $akcessId = $params['akcessid'];

            $sql_query_eformresponse = "SELECT 
            `eformresponse`.`id`,`eformresponse`.`formName`,`eformresponse`.`name`,`eformresponse`.`eformId`,`eformresponse`.`description`,`eformresponse`.`device_token`,`eformresponse`.`akcessId`,`eformresponse`.`documentHash`,`eformresponse`.`signatureHash`,`eformresponse`.`filedata`,`eformresponse`.`filename`,`eformresponse`.`otp`,`eformresponse`.`bankId`,`eformresponse`.`api`,`eformresponse`.`signaturefile`,`eformresponse`.`faceMatchPic`,`eformresponse`.`profilepic`,`eformresponse`.`eformasfile`,`eformresponse`.`eformasfile_approval`,`eformresponse`.`facialMatch`,`eformresponse`.`date`,`eformresponse`.`approved`,`eformresponse`.`status`,`eformresponse`.`pulldata`,`eformresponse`.`facematch`,`eformresponse`.`mobile_local_id`,`eformresponse`.`strict`,`eformresponse`.`created`,`eformresponse`.`modified`
            FROM `eformresponse`
            WHERE `eformresponse`.`akcessId`='".$akcessId."' AND `eformresponse`.`soft_delete`=0";

            $sql_query_data_eformresponse = $conn->execute($sql_query_eformresponse);

            $result_sql_eformresponse = $sql_query_data_eformresponse->fetchAll('assoc');

            $response_eformresponse = array();
           
            if($result_sql_eformresponse) {
           
                foreach ($result_sql_eformresponse as $t) {
                    
                    $id = $t['id'];
                    $formName = $t['formName'];
                    $name = $t['name'];
                    $eformId = $t['eformId'];
                    $description = $t['description'];
                    $device_token = $t['device_token'];
                    $akcessId = $t['akcessId'];
                    $documentHash = unserialize($t['documentHash']);
                    $signatureHash = $t['signatureHash'];
                    $filedata = $t['filedata'];
                    $filename = $t['filename'];

                    $otp = $t['otp'];
                    $bankId = $t['bankId'];
                    $api = $t['api'];
                    $signaturefile = $t['signaturefile'];
                    $faceMatchPic = $t['faceMatchPic'];
                    $profilepic = $t['profilepic'];
                    $eformasfile = $t['eformasfile'];
                    $eformasfile_approval = $t['eformasfile_approval'];
                    $facialMatch = $t['facialMatch'];
                    $date = $t['date'];
                    $approved = $t['approved'];

                    $status = $t['status'];
                    $pulldata = $t['pulldata'];
                    $facematch = $t['facematch'];
                    $mobile_local_id = $t['mobile_local_id'];
                    $strict = $t['strict'];
                    $created = $t['created'];

                    $eformasfile_link = '';
                    if(isset($eformasfile) && $eformasfile != '') {
                        $eformasfile_link  = Router::url("/", true) . "uploads/eforms/" . $eformId . "/" . $eformasfile;
                    }

                    $profilepic_link = '';
                    if(isset($profilepic) && $profilepic != '') {
                        $profilepic_link  = Router::url("/", true) . "uploads/eforms/" . $eformId . "/" . $profilepic;
                    }

                    $signaturefile_link = '';
                    if(isset($signaturefile) && $signaturefile != '') {
                        $signaturefile_link  = Router::url("/", true) . "uploads/eforms/" . $eformId . "/" . $signaturefile;
                    }

                    $faceMatchPic_link = '';
                    if(isset($faceMatchPic) && $faceMatchPic != '') {
                        $faceMatchPic_link  = Router::url("/", true) . "uploads/eforms/" . $eformId . "/" . $faceMatchPic;  
                    }

                    $sql_query_fieldsresponse = "SELECT *
                    FROM `fieldsresponse`
                    WHERE `fk_eformresponse_id`='".$id."'";

                    $sql_query_data_fieldsresponse = $conn->execute($sql_query_fieldsresponse);

                    $result_sql_fieldsresponse = $sql_query_data_fieldsresponse->fetchAll('assoc');

                    $response_eformresponse_fieldsresponse = array();
           
                    if($result_sql_fieldsresponse) {
                
                        foreach ($result_sql_fieldsresponse as $ft) {

                            $id = $ft['id'];
                            $fk_eformresponse_id = $ft['fk_eformresponse_id'];
                            $fk_eform_id = $ft['fk_eform_id'];
                            $labelname = $ft['labelname'];
                            $keyfields = $ft['keyfields'];
                            $keytype = $ft['keytype'];
                            $value = $ft['value'];
                            $file = $ft['file'];
                            $verify_status = $ft['verify_status'];
                            $file_verified = $ft['file_verified'];
                            $expiryDate = $ft['expiryDate'];
                            $isverified = $ft['isverified'];
                            $docuementType = $ft['docuementType'];
                            $documentData = $ft['documentData'];
                            $isDocFetched = $ft['isDocFetched'];
                            $signature_required = $ft['signature_required'];
                            $options = json_decode($ft['options']);
                            $verification_grade = $ft['verification_grade'];
                            $section_id = unserialize($ft['section_id']);
                            $section_color = unserialize($ft['section_color']);
                            $sectionfields = unserialize($ft['sectionfields']);

                            if($keytype == 'file') {
                                $value  = Router::url("/", true) . "uploads/eforms/" . $fk_eform_id . "/" . $value;
                            }

                            $response_eformresponse_fieldsresponse[] = array(
                                'id' => $id,
                                'fk_eformresponse_id' => $fk_eformresponse_id,
                                'fk_eform_id' => $fk_eform_id,
                                'labelname' => $labelname,
                                'keyfields' => $keyfields,
                                'keytype' => $keytype,
                                'value' => $value,
                                'file' => $file,
                                'verify_status' => $verify_status,
                                'file_verified' => $file_verified,
                                'expiryDate' => $expiryDate,
                                'isverified' => $isverified,
                                'docuementType' => $docuementType,
                                'documentData' => $documentData,
                                'isDocFetched' => $isDocFetched,
                                'signature_required' => $signature_required,
                                'options' => $options,
                                'verification_grade' => $verification_grade,
                                'section_id' => $section_id,
                                'section_color' => $section_color,
                                'sectionfields' => $sectionfields
                            );

                        }

                    }


                    $response_eformresponse[] = array(
                        'id' => $id,
                        'formName' => $formName,
                        'name' => $name,
                        'eformId' => $eformId,
                        'description' => $description,
                        'device_token' => $device_token,
                        'akcessId' => $akcessId,
                        'documentHash' => $documentHash,
                        'signatureHash' => $signatureHash,
                        'filedata' => $filedata,
                        'filename' => $filename,
                        'otp' => $otp,
                        'bankId' => $bankId,
                        'api' => $api,
                        'signaturefile' => $signaturefile_link,
                        'faceMatchPic' => $faceMatchPic_link,
                        'profilepic' => $profilepic_link,
                        'eformasfile' => $eformasfile_link,
                        'eformasfile_approval' => $eformasfile_approval,
                        'facialMatch' => $facialMatch,
                        'date' => $date,
                        'approved' => $approved,
                        'status' => $status,
                        'pulldata' => $pulldata,
                        'facematch' => $facematch,
                        'mobile_local_id' => $mobile_local_id,
                        'strict' => $strict,
                        'created' => $created,
                        'fields' => $response_eformresponse_fieldsresponse
                    );
                }
            }

            $data['results'] = $response_eformresponse;
            $data['message'] = 'Users data found it.';
            $data['status'] = 1;
        } else {            
            $data['results'] = [];
            $data['message'] = 'AKcess ID not found.';
            $data['status'] = 0;
        }

        return $data;
    }

    public function saveUserResponse() {

        $conn = ConnectionManager::get("default"); // name of your database connection    

        $params = (array) json_decode(file_get_contents('php://input'), TRUE);  

        if(isset($params['akcessId'])) {
            $params['akcessId'] = $params['akcessId'];
        } else if(isset($params['akcessid'])) {
            $params['akcessId'] = $params['akcessid'];
        }

        // $myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        // $txt = json_encode($params);
        // fwrite($myfile, "\n". $txt);
        // fclose($myfile);

        if($params && isset($params['akcessId'])) {

            $akcess_id = $params['akcessId'];
            $id = $params['requestId'];
            $fields = $params['fields']; 

            $sql_fields = "SELECT id FROM `users_by_akcess` WHERE id=".$id." AND status=1 AND soft_delete=0";

            $sql_fields_id = $conn->execute($sql_fields);

            $sql_last_fields = $sql_fields_id->fetch('assoc');

            $insertedId = isset($sql_last_fields['id']) ? $sql_last_fields['id'] : 0;

            $usertype = isset($sql_last_fields['type']) ? $sql_last_fields['type'] : "";

            if ($insertedId != 0) {
                
                foreach ($fields as $values) {
                    $key = isset($values['key']) ? $values['key'] : "";

                    $value = isset($values['value']) ? $values['value'] : "";

                    if ($key == 'firstname') {
                        $firstname = $value;
                    }
                    if ($key == 'lastname') {
                        $lastname = $value;
                    }
                    if ($key == 'email') {
                        $email = $value;
                    }
                    if ($key == 'mobile') {
                        $mobileNumber = $value;
                    }
                    if ($key == 'dateofbirth') {
                        $dob = $value;
                    }
                    if ($key == 'place of birth') {
                        $place_of_birth = $value;
                    }
                    if ($key == 'gender') {
                        $gender = $value;
                    }
                    if ($key == 'faculty') {
                        $faculty = $value;
                    }
                    if ($key == 'courses') {
                        $courses = $value;
                    }
                    if ($key == 'admission date') {
                        $adminssion_date = $value;
                    }
                    if ($key == 'address') {
                        $address = $value;
                    }
                    if ($key == 'countryofresidence') {
                        $nationality = $value;
                    }
                    if ($key == 'staff type') {
                        $staff_type = $value;
                    }
                    if ($key == 'academic personal type') {
                        $academic_personal_type = $value;
                    }
                }

                $firstname = isset($firstname) ? $firstname : "";
                $lastname = isset($lastname) ? $lastname : "";
                $akcessId = isset($akcess_id) ? $akcess_id : "";
                $companyName = isset($companyName) ? $companyName : "";
                $address = isset($address) ? $address : "";
                $city = isset($place_of_birth) ? $place_of_birth : "";
                $country = isset($nationality) ? $nationality : "";
                $email = isset($email) ? $email : "";
                $mobileNumber = isset($mobileNumber) ? $mobileNumber : "";
                $gender = isset($gender) ? $gender : "";
                $dob = isset($dob) ? $dob : "";
                $otherdetails = isset($otherdetails) ? $otherdetails : "";
                $active = isset($active) ? $active : 1;
                $faculty = isset($faculty) ? $faculty : "";
                $courses = isset($courses) ? $courses : "";
                $academic_personal_type = isset($academic_personal_type) ? $academic_personal_type : 0;
                $staff_type = isset($staff_type) ? $staff_type : 0;
                $usertype = isset($usertype) ? ucfirst($usertype) : "";
                $adminssion_date = isset($adminssion_date) ? $adminssion_date : "";
                $loginOpt = isset($loginOpt) ? $loginOpt : 'pin';
                $siteStatus = isset($siteStatus) ? $siteStatus : 'Development';
                $status = isset($status) ? $status : 1;

                $name = $firstname . " " . $lastname;

                $insert_query = 'INSERT INTO `users_invitation` (`name`,`akcessId`,`companyName`,`address`,`city`,`country`,`email`,`mobileNumber`,`gender`,`dob`,`otherdetails`,`active`,`faculty`,`courses`,`academic_personal_type`,`staff_type`,`usertype`,`adminssion_date`,`loginOpt`,`siteStatus`,`status`,`users_by_akcess_id`) VALUES ("'.$name.'","'.$akcessId.'","'.$companyName.'","'.$address.'","'.$city.'","'.$country.'","'.$email.'","'.$mobileNumber.'","'.$gender.'","'.$dob.'","'.$otherdetails.'","'.$active.'","'.$faculty.'","'.$courses.'","'.$academic_personal_type.'","'.$staff_type.'","'.$usertype.'","'.$adminssion_date.'","'.$loginOpt.'","'.$siteStatus.'","'.$status.'","'.$id.'")';

                $conn->execute($insert_query);

                $sql_fields = "SELECT id FROM `users_invitation` ORDER BY id DESC LIMIT 0,1";

                $sql_fields_id = $conn->execute($sql_fields);

                $sql_last_fields = $sql_fields_id->fetch('assoc');

                $insertedId = $sql_last_fields['id'];

                $idcard_random = $this->Global->random_string('numeric', 10) . $insertedId;

                $update_query = "UPDATE `users_invitation` set `idcardno`='".$idcard_random."' WHERE id=".$insertedId;

                $conn->execute($update_query);

                $update_query = "UPDATE `users_by_akcess` set `status`=0 WHERE id=".$id;

                $conn->execute($update_query);

                $data['status'] = 1;
                $data['message'] = 'Record successfully saved';
                $data['data'] = 'Record successfully saved';
            }
            else {
                $data['status'] = 0;
                $data['message'] = 'Record already saved';
                $data['data'] = 'Record already saved';
            }
            
        }
        else {
            $data['status'] = 0;
            $data['message'] = 'AKcess Id Not found!';
            $data['data'] = 'AKcess Id Not found!';
        }
        return $data;
    }
    
}

?>
<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Cake\Datasource\ConnectionManager;

class GlobalComponent extends Component {

    public $components = ['Amazon'];
   
    public function userIdEncode($value) {
        $salt = "A#_++:{(*&&K#Y!ccess";
        $key = sha1('dfg#$$$_++:{"@$$$segd@#$%pT^&*%::{}?>n$(*&&E#T!rfgRTD' . $salt);
        if (!$value) {
            return false;
        }
        $strLen = strlen($value);
        $keyLen = strlen($key);
        $j = 0;
        $crypttext = '';
        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($value, $i, 1));
            if ($j == $keyLen) {
                $j = 0;
            }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $crypttext .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
        }
        return $crypttext;
    }

    public function userIdDecode($value) {
        $salt = "A#_++:{(*&&K#Y!ccess";
        if (!$value) {
            return false;
        }
        $key = sha1('dfg#$$$_++:{"@$$$segd@#$%pT^&*%::{}?>n$(*&&E#T!rfgRTD' . $salt);
        $strLen = strlen($value);
        $keyLen = strlen($key);
        $j = 0;
        $decrypttext = '';
        for ($i = 0; $i < $strLen; $i+=2) {
            $ordStr = hexdec(base_convert(strrev(substr($value, $i, 2)), 36, 16));
            if ($j == $keyLen) {
                $j = 0;
            }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $decrypttext .= chr($ordStr - $ordKey);
        }

        return $decrypttext;
    }
    
    public function getDocumentList(){
        
        $conn = ConnectionManager::get("default"); // name of your database connection        
        
        $documenttypelist_data = $conn->execute("SELECT * FROM documenttypelist");
            
        $documenttypelist = $documenttypelist_data->fetchAll('assoc');
        
        return $documenttypelist;
        
    }

    public function curlGetPost($method, $type, $api, $origin_url, $api_url, $data = array(), $origin = array()) {

        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            $urlencode = '';
            if ($method == 'GET') {
                $urlencode = $data;
                $data = array();
            }

            $curl_array = array(
                CURLOPT_URL => $api_url . $type . $urlencode,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $origin,
            );

            curl_setopt_array($curl, $curl_array);

            $response = curl_exec($curl);

            curl_close($curl);

            $this->response->type('json');
            $this->response->body($response);

            return $this->response;
        }
    }

    public function curlPostEformResponseVerifyField($method, $type, $api, $origin_url, $api_url, $data = array(), $origin = array()) {

        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            $curl_array = array(
                CURLOPT_URL => $api_url . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $origin,
            );
   
            curl_setopt_array($curl, $curl_array);

            $response = curl_exec($curl);

            curl_close($curl);

            return $response;
        }
    }

    public function curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, $data = array(), $origin = array()) {

        $curl = curl_init();

        
        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            $curl_array = array(
                CURLOPT_URL => $api_url . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $origin,
            );
   
            curl_setopt_array($curl, $curl_array);

            $response = curl_exec($curl);

            curl_close($curl);

            return json_decode($response);
        }
    }
    

    public function curlPostEformResponseVerify($method, $type, $api, $origin_url, $api_url, $data = array(), $origin = array()) {

        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            $curl_array = array(
                CURLOPT_URL => $api_url . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $origin,
            );
   
            curl_setopt_array($curl, $curl_array);

            $response = curl_exec($curl);

            curl_close($curl);

            $resultJ = json_decode($response);

            $array_reponse = array();

            if($resultJ->statusCode == 200){
                
                foreach($resultJ->data as $key => $value) {
                    $docType = $value->veriier->docType;
                    $akcessid = $value->veriier->akcessid;
                    $verifierName = $value->veriier->verifierName;
                    $verifierGrade = $value->veriier->verifierGrade;
                    $expiryDate = $value->expiryDate;
                    $array_reponse[] = array(                        
                        $akcessid,
                        $verifierName, 
                        $verifierGrade,
                        $expiryDate
                    );
                }
            }
            
            $resultJ = json_encode($array_reponse);
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        }
    }
    
    public function curlGetPostEform($method, $type, $api, $origin_url, $api_url, $data = array(), $origin = array()) {

        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            $curl_array = array(
                CURLOPT_URL => $api_url . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $origin,
            );
                        
            curl_setopt_array($curl, $curl_array);

            $response = curl_exec($curl);

            curl_close($curl);

            $this->response->type('json');
            $this->response->body($response);

            return $this->response;
        }
    }

    public function getToken() {

        $api = isset($_POST['api']) ? $_POST['api'] : API_KEY;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : BLOCKCHAIN_ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_API_BASE_URL;
        $type = 'auth/generate-token';

        $origin_array = array(
            'Content-Type: application/json',
            'Origin: ' . $origin_url,
            'apikey: ' . $api,
        );

        $data_array = '{
            "apikey" : "' . $api . '"
        }';

        $method = 'POST';

        $response_data = $this->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);

        $response_akcess_Data = json_decode($response_data);  

        $response_data = $response_akcess_Data->data->token;

        //$response_data = $this->request->session()->read('akcessToken');

        return $response_data;
    }

    public function curlGetVerifyDocument($type, $api, $origin_url, $api_url, $AKcessToken = '', $data = array()) {

        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Origin: ' . BLOCKCHAIN_ORIGIN_URL
                ),
                    )
            );

            $response = curl_exec($curl);

            curl_close($curl);

            $this->response->type('json');
            $this->response->body($response);

            return $this->response;
            
        }
    }

    public function curlGetPostDocument($type, $api, $origin_url, $api_url, $AKcessToken = '', $data = array()) {

        $response = '';

        if (isset($origin_url) && isset($api)) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api_url . $type,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Origin: ' . BLOCKCHAIN_ORIGIN_URL
                ),
                    )
            );

            $response = curl_exec($curl);

            curl_close($curl);

            $this->response->type('json');
            $this->response->body($response);

            return $this->response;
            
        }
    }

    public function getVerifyDoc($data) {

        $api = isset($_POST['api']) ? $_POST['api'] : API_KEY;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : BLOCKCHAIN_ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : BLOCKCHAIN_API_BASE_URL;
        $AKcessToken = $data['response'];
        $type = 'document/verifydoc';

        $data_array = array(
            'userKey' => new \CURLFILE($data['file_Ak']),
            'documentId' => $data['documentID'],
            'akcessId' => $data['verifierAkcessId'],
            'expiryDate' => $data['expiryDate'],
            'authorization' => $AKcessToken,
            'apikey' => $api
        );

        $response_data = $this->curlGetVerifyDocument($type, $api, $origin_url, $api_url, $AKcessToken, $data_array);

        return $response_data;
    }

    public function getDocumentAdd($data) {

        $type = 'document/add';
        $api = isset($_POST['api']) ? $_POST['api'] : API_KEY;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : BLOCKCHAIN_ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : BLOCKCHAIN_API_BASE_URL;
        $AKcessToken = $data['response'];

        $data_array = array(
            'documentID' => $data['documentID'],
            'documentHash' => '["' . $data['fileNameHash'] . "','" . $data['akcessIdHash'] . '"]',
            'akcessId' => $data['verifierAkcessId'],
            'authorization' => $AKcessToken,
            'apikey' => $api,
            'channelId' => $data['channelName'],
            'userKey' => new \CURLFILE($data['file_Ak']),
        );

        $response_data = $this->curlGetPostDocument($type, $api, $origin_url, $api_url, $AKcessToken, $data_array);
       
        return $response_data;
    }
    
    public function clientIp($defaultIP = '127.0.0.1') {
            $ipaddr = null;
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ipaddr = $_SERVER['REMOTE_ADDR'];
            }
            $ipaddr = trim($ipaddr);
            if ($ipaddr == '::1') {
                $ipaddr = $defaultIP;
            }
            return $ipaddr;
    }

    public function auditTrailApi($tableId, $tableName, $action, $before = null, $after = null, $tableIdName = 'id', $sroleID = 0, $sID = 0, $logout_reference_id = 0) {
        
        $conn = ConnectionManager::get("default"); // name of your database connection      
        
        

        if (isset($after['fk_user_id']) && $after['fk_user_id'] != null) {
            $user_id = $after['fk_user_id'];
        } else {
            $user_id = 0;
        }

        $ip = $this->clientIp();

        if (isset($after['user_id']) && $after['user_id'] != null) {
            $sID = $after['user_id'];
        } else {
            $sID = 0;
        }

        if (isset($after['role_id']) && $after['role_id'] != null) {
            $sroleID = $after['role_id'];
        } else {
            $sroleID = 0;
        }

        $device_id = "";
        if (isset($after['device_id']) && $after['device_id'] != null) {
            $device_id = $after['device_id'];
        }
        
        unset($after['user_id']);
        unset($after['role_id']);

        

        $success = 0;

        if ($action == 'view') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'eform-view') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'send_to_portal') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
       
        if ($action == 'insert') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'fieldsresponse') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'eformresponse') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'ackess') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'emailEform') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'phoneEform') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'akcessEform') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'notifications') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'Invitation') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'email') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'akcess') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'phone') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'register') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'login') {
            $before = null;
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'copy') {            
            $before = json_encode($before);
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'update') {            
            $before = json_encode($before);
            $after = json_encode($after);
            $success = 1;
        }
        
        if ($action == 'logout') {            
            $before = json_encode($before);
            $after = json_encode($after);
            $success = 1;
        }

        if ($action == 'delete') {            
            $before = json_encode($before);
            $after = json_encode($after);
            $success = 1;
        }

        $latlong = '';

        $browser_latlong = $this->getBrowserLatlong();

        $browser_ip = $this->getBrowserIp($browser_latlong);

        $insert = [
            'reference_id' => $tableId,
            'table_name' => $tableName,
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'user_id' => $user_id,
            'ip' => $ip,
            'success' => $success,
            'by_user_role' => $sroleID,
            'by_user_id' => $sID,
            'logout_reference_id' => $logout_reference_id,
            'browser_ip' => $browser_ip,
            'browser_latlong' => $browser_latlong,
            'latlong' => $latlong,
            'os' => $os,
            'device_id' => $device_id,
            'system_method' => 'portal'
        ];
       
        try {
            $sql_audit_trail = "INSERT INTO `audit_trail` (`reference_id`, `table_name`, `action`, `before`, `after`, `user_id`, `ip`, `success`, `by_user_role`, `by_user_id`, `logout_reference_id`, `browser_ip`, `browser_latlong`, `latlong`, `os`, `device_id`, `system_method`) VALUES (".$tableId.",'".$tableName."','".$action."','".$before."','".$after."','".$user_id."','".$ip."','".$success."','".$sroleID."','".$sID."','".$logout_reference_id."','".$browser_ip."','".$browser_latlong."','".$latlong."','".$os."','".$device_id."','WEB')";
            
            $sqlAudit = $conn->execute($sql_audit_trail);
            
            $sql_last_query = "SELECT id FROM `audit_trail` ORDER BY ID DESC LIMIT 0,1";
            
            $sql_last_query_id = $conn->execute($sql_last_query);
            $sql_last_id = $sql_last_query_id->fetch('assoc');
            $lastInsertedId = $sql_last_id['id'];

            
            
            $recovery_table = "";
            if ($lastInsertedId > 0) {

                $ip_insert = [
                    'fk_audit_id' => $lastInsertedId,
                    'fk_user_id' => $user_id,
                    'ip' => $ip,
                    'recovery_id' => 0,
                    'browser_ip' => $browser_ip,
                    'browser_latlong' => $browser_latlong,
                    'recovery_table' => $recovery_table
                ];
                
                $sql_ip_audit = "INSERT INTO `ip_audit` (`fk_audit_id`, `fk_user_id`, `ip`, `recovery_id`, `browser_ip`, `browser_latlong`, `recovery_table`) VALUES (".$lastInsertedId.",'".$user_id."','".$ip."',0,'".$browser_ip."','".$browser_latlong."','".$recovery_table."')";
            
                $sqlIPAudit = $conn->execute($sql_ip_audit);
            }

            

            return $lastInsertedId;

            
            
        } catch (PDOEXCEPTION $e) {
            return 'error';
        }
    }

    public function auditTrailApiSuccess($lastInsertedId, $success = 0) {
        
        $conn = ConnectionManager::get("default"); // name of your database connection      
        
        $update = array('success' => $success);
        
        $sql_audit_trail = "UPDATE `audit_trail` SET success='".$success."' WHERE id = ".$lastInsertedId;
            
        $sqlAudit = $conn->execute($sql_audit_trail);
    }
    
    public function getBrowserLatlong() {

        $browser_latlong = "";

        if ($_SESSION['locationseiData']) {

            $explodelatlong = explode(",", $_SESSION['locationseiData']);
            $locationseiDataDecode_latitude = trim($explodelatlong[0]);
            $locationseiDataDecode_longitude = trim($explodelatlong[1]);

            $latitude = "";
            if (isset($locationseiDataDecode_latitude) && trim($locationseiDataDecode_latitude) != "") {
                $latitude = $locationseiDataDecode_latitude;
            }
            $longitude = "";
            if (isset($locationseiDataDecode_longitude) && trim($locationseiDataDecode_longitude) != "") {
                $longitude = $locationseiDataDecode_longitude;
            }

            $browser_latlong = "";
            if ((isset($latitude) && isset($longitude)) && (trim($latitude) != "" && trim($longitude) != "")) {
                $browser_latlong = $latitude . ' , ' . $longitude;
            }
        }

        return $browser_latlong;
    }

    public function getBrowserIp($browser_latlong) {

        $browser_ip = "";

        if ($_SESSION['locationseiDataLatLong']) {
            $browser_ip = $_SESSION['locationseiDataLatLong'];
        }
        if (isset($browser_latlong) && $browser_latlong != "") {
            $browser_ip = $browser_ip;
        } else {
            $browser_ip = "";
        }

        return $browser_ip;
    }
    
    /**
     * Create a "Random" String
     *
     * @param   string  type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
     * @param   int number of characters
     * @return  string
     */
    function random_string($type = 'alnum', $len = 8)
    {
        switch ($type)
        {
            case 'basic':
                return mt_rand();
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
                switch ($type)
                {
                    case 'alpha':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyz';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyz';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }
                return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'sha1':
                return sha1(uniqid(mt_rand(), TRUE));
        }
    }

}

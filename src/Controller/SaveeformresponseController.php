<?php

namespace App\Controller;

use App\Controller\AppController;



class SaveeformresponseController extends AppController {
    
    
    public function initialize()
    {        
        // include a core PHP file       
        parent::initialize();
        
        $this->index();
    }
    public function index() {

       echo "hi";
       exit;
    }

}

?>
 
<?php
namespace App\Controller;
use App\Controller\AppController;
class SecondController extends AppController
{
    public function fun2()
    {
        echo "H";exit;
        print_r($this->request->params['pass']);                                                        
    }
}
?> 
 
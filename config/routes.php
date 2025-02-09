<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use App\Controller\ApiController;
/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {
    
   
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
   
    // Search substring 
    
    if('/api/save-eformresponse' != $url || '/api/incoming-received' != $url || '/api/fetch-component-detail' != $url) {
        // Register scoped middleware for in scopes.
        $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
            'httpOnly' => true
        ]));

        /**
         * Apply a middleware to the current route scope.
         * Requires middleware to be registered via `Application::routes()` with `registerMiddleware()`
         */
        $routes->applyMiddleware('csrf');

        /**
        * Here, we are connecting '/' (base path) to a controller called 'Pages',
        * its action called 'display', and we pass a param to select the view file
        * to use (in this case, src/Template/Pages/home.ctp)...
        */
       $routes->connect('/', ['controller' => 'Users', 'action' => 'login']);

       /**
        * ...and connect the rest of 'Pages' controller's URLs.
        */
       $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'index']);

       /**
        * ...and connect the rest of 'Pages' controller's URLs.
        */
       Router::connect('/getDetails', array('controller' => 'Users', 'action' => 'login'));
        // $routes->connect('/getDetails', ['controller' => 'Users', 'action' => 'login']);

       

       /**
        * Connect catchall routes for all controllers.
        *
        * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
        *
        * ```
        * $routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);
        * $routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);
        * ```
        *
        * Any route class can be used with this method, such as:
        * - DashedRoute
        * - InflectedRoute
        * - Route
        * - Or your own route class
        *
        * You can remove these routes once you've connected the
        * routes you want in your application.
        */
    }
   
    $routes->fallbacks(DashedRoute::class);
});

//Router::scope('/api/save-eformresponse', function (RouteBuilder $routes) {   
    
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

    
    
    if('/api/get-eformresponse-approval-process' === $url) {

        $api = new ApiController();   

        $data = $api->getEformResponseApprovalProcess();    

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);  

        echo json_encode($data);

        exit;
        
    } else if('/api/save-eformresponse-approval-process' === $url) {

        $api = new ApiController();   

        $data = $api->saveEformResponseApprovalProcess();    

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);  

        echo json_encode($data);

        exit;
        
    } else if('/api/save-eformresponse' === $url) {

        $api = new ApiController();     

        $response = $api->saveEformresponse();    

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);  

        $data['status'] = 1;
        $data['message'] = 'Record successfully saved';
        $data['data'] = $response;

        echo json_encode($data);

        exit;
        
    } else if('/api/incoming-received' === $url) {

        $api = new ApiController();     

        $response = $api->incomingReceived();  
        
        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);

        $data['status'] = 1;
        $data['message'] = 'Record successfully saved';
        $data['data'] = array();

        echo json_encode($data);

        exit;
        
    } else if('/api/fetch-component-detail' === $url) {
       
        $api = new ApiController();     
        
        $response = $api->getResponseLink();   

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);
        
        echo $response;

        exit; 
        
    } else if('/api/attendance' === $url) {
       
        $api = new ApiController();     
        
        $response = $api->attendance();   
        

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);
        
        echo $response;

        exit; 
        
    } else if('/api/getattendance' === $url) {
       
        $api = new ApiController();     
        
        $response = $api->getattendance();   
        

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);
        
        echo $response;

        exit; 
        
    } 
    // else if('/api/document/byId' === $url) {
       
    //     $api = new ApiController();     
        
    //     $response = $api->getDocumentByAkcessID();  

    //     //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
    //     //$txt = json_encode($response);
    //     //fwrite($myfile, "\n". $txt);
    //     //fclose($myfile);

    //     echo json_encode($response);

    //     exit; 
        
    // } 
    else if('/api/eform/list-by-akcessId' === $url) {
       
        $api = new ApiController();     
        
        $response = $api->getEformByAkcessID();  

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);

        echo json_encode($response);

        exit; 
        
    } 
    else if('/api/getuserdatabyakcessid' === $url) {
       
        $api = new ApiController();     
        
        $response = $api->getUserDataByAKcessID();  

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);

        echo json_encode($response);

        exit; 
        
    } 
    else if('/api/save-userresponse' === $url) {

        $api = new ApiController();     

        $data = $api->saveUserResponse();    

        //$myfile = fopen("/var/www/html/edu/logs/logs.txt", "a") or die("Unable to open file!");
        //$txt = json_encode($response);
        //fwrite($myfile, "\n". $txt);
        //fclose($myfile);  

        // $data['status'] = 1;
        // $data['message'] = 'Record successfully saved';
        // $data['data'] = $response;

        echo json_encode($data);

        exit;
        
    } 
//});
if (str_contains($url, '/api/document/byId')) {
    Router::scope('/api/document/byId', function () {

        $api = new ApiController();     
        
        $response = $api->getDocumentByAkcessID();  

        echo json_encode($response);

        exit; 
    });
} else if (str_contains($url, '/api/eform/')) {
    Router::scope('/api/:type/:eformname/:id', function (RouteBuilder $routes) {
        $routes->connect('template', ['controller'=>'Api','action'=>'qrcodeApi']);
        $routes->fallbacks();
    });
} else if (str_contains($url, '/api/idcard/')) {
    Router::scope('/api/:type/:eformname/:id', function (RouteBuilder $routes) {
        $routes->connect('template', ['controller'=>'Api','action'=>'idcardApi']);
        $routes->fallbacks();
    });
}
else if (str_contains($url, '/api/guestpass/')) {
    Router::scope('/api/:type/:eformname/:id', function (RouteBuilder $routes) {
        $routes->connect('template', ['controller'=>'Api','action'=>'guestpassApi']);
        $routes->fallbacks();
    });
}
else if (str_contains($url, '/api/document/')) {
    Router::scope('/api/:type/:eformname/:id', function (RouteBuilder $routes) {
        $routes->connect('template', ['controller'=>'Api','action'=>'documentApi']);
        $routes->fallbacks();
    });
} else if (str_contains($url, '/qrcode/')) {
    Router::scope('/qrcode/:eformid/:saveid/:id', function (RouteBuilder $routes) {
        $routes->connect('template', ['controller'=>'Api','action'=>'qrcode']);
        $routes->fallbacks();
    });
} else {
    Router::scope('/api/:type', function (RouteBuilder $routes) {
        $routes->connect('template', ['controller'=>'Api','action'=>'qrcode']);
        $routes->fallbacks();
    });
}

//Router::scope('/api/save-eformresponse', function (RouteBuilder $routes) {    
//    $routes->extensions(['json','xml']);
//    $routes->resources('Saveeformresponse');
//});
/**
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * Router::scope('/api', function (RouteBuilder $routes) {
 *     // No $routes->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */
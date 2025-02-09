<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */




require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\Utility\Inflector;
use Cake\Utility\Security;

/**
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.default to `config/.env` and set/modify the
 * variables as required.
 *
 * It is HIGHLY discouraged to use a .env file in production, due to security risks
 * and decreased performance on each request. The purpose of the .env file is to emulate
 * the presence of the environment variables like they would be present in production.
 */
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

// GLOBAL USER
define('AK_API_BASE_URL','https://ak-api-v2.akcess.dev/');
// define('BASE_ORIGIN_URL','https://edu.akcess.app/');

define('AK_ORIGIN_URL','https://ak-api-v2.akcess.dev/');
define('AK_ORIGIN_URL_GLOBAL','https://ak-api-v2.akcess.dev/');
define('AK_FIELD_URL','https://mobile.akcess.dev:3000/v5/');
define('VERSION','1.0');

// Kuwait USER
// define('AK_API_BASE_URL','https://api-kw.akcess.app/');
// define('BASE_ORIGIN_URL','https://edu.akcess.app/');
// define('AK_ORIGIN_URL','https://api-kw.akcess.app/');
//define('AK_ORIGIN_URL_GLOBAL','https://ak-api-v2.akcess.dev/');
// define('AK_FIELD_URL','https://mobile.akcess.dev:3000/v5/');

//BlockChain API_KEY
define('API_KEY','fed024f83afd05b3eb5d63d42ac6bcdb');
define('BLOCKCHAIN_API_BASE_URL','https://blockchain-ak.akcess.io/api/v1/');
define('BLOCKCHAIN_ORIGIN_URL','blockchain-ak.akcess.io');

define('COMP_NAME','edu');
 define('PATH_URL_PREFIX','https://edu.akcess.app');

 define('ORIGIN_URL','edu.akcess.app');
// define('SITE_API_KEY_URL','18683ae4a4ae3fd6fa7e1dc18bca6d5d');
define('SITE_API_KEY_URL','a53fcc8e1bc7581cfd691cce0772eb0c');

define('COMP_NAME_TITLE','AKcess: EDU');
define('COMP_NAME_MESSAGE', 'AKcess_EDU');

define('AKCESS_NOTIFICATION', 'Hello, '.COMP_NAME_MESSAGE.' sent you a message');
define('EMAIL_NOTIFICATION', 'Hello, '.COMP_NAME_MESSAGE.' sent you a Invitation.');
define('AKCESS_ID_NOTIFICATION', 'Hello, '.COMP_NAME_MESSAGE.' sent you the University ID Card.');

$array['notification']['update'] = 'Notification record updated successfully.';
$array['notification']['insert'] = 'Notification record sent successfully.';
$array['notification']['delete'] = 'Notification record deleted successfully.';
$array['notifications']['notifications'] = 'Notification record sent successfully.';
$array['notifications']['Notifications'] = 'Notification record sent successfully.';

$array['sclasses']['update'] = 'Classes record updated successfully.';
$array['sclasses']['insert'] = 'Classes record added successfully.';
$array['sclasses']['delete'] = 'Classes record deleted successfully.';
$array['class_attends']['update'] = 'Class attends record updated successfully.';
$array['class_attends']['insert'] = 'Class attends record added successfully.';
$array['class_attends']['delete'] = 'Class attends record deleted successfully.';
$array['locations']['update'] = 'Location record updated successfully.';
$array['locations']['insert'] = 'Location record added successfully.';
$array['locations']['delete'] = 'Location record deleted successfully.';
$array['users']['update'] = 'User record updated successfully.';
$array['users']['insert'] = 'User record added successfully.';
$array['users']['delete'] = 'User record deleted successfully.';
$array['docs']['delete'] = 'Docs record deleted successfully.';
$array['docs']['update'] = 'Docs record updated successfully.';
$array['docs']['insert'] = 'Docs record added successfully.';
$array['idcard']['delete'] = 'ID Card record deleted successfully.';
$array['idcard']['insert'] = 'ID Card record added successfully.';
$array['verifeddocs']['insert'] = 'Docs record verified successfully.';
$array['senddata']['phone'] = 'ID Card send successfully to register phone no.';
$array['senddata']['email'] = 'ID Card send successfully to register email address.';
$array['senddata']['send-email'] = 'send successfully to register email address.';
$array['senddata']['akcess'] = 'ID Card send successfully to register Akcess ID.';
$array['senddata']['notifications'] = 'Sent you notification to register Akcess ID.';
$array['senddata']['Invitation'] = 'Invitation sent successfully.';
$array['users']['login'] = 'User login successfully.';
$array['users']['logout'] = 'User logout successfully.';
$array['users']['register'] = 'User register successfully.';
$array['eform']['update'] = 'The eform has been successfully updated.';
$array['eform']['insert'] = 'The eform has been successfully saved.';
$array['eform']['delete'] = 'The eform has been successfully deleted.';
$array['eform']['copy'] = 'The eform has been successfully copied.';
$array['fields_options']['copy'] = 'The eform fields has been successfully copied.';
$array['fields']['copy'] = 'The eform field type has been successfully copied.';
$array['fields_options']['update'] = 'The eform fields has been successfully updated.';
$array['fields']['update'] = 'The eform field type has been successfully updated.';
$array['senddata']['phoneEform'] = 'The eform has been successfully send by Akcess ID.';
$array['senddata']['emailEform'] = 'The eform has been successfully send by Akcess ID.';
$array['senddata']['akcessEform'] = 'The eform has been successfully send by Akcess ID.';
$array['response_eform']['akcessEform'] = 'The eform send response successfully.';
$array['send_to_portal']['send_to_portal'] = 'The eform send response successfully.';

$this->addPlugin('DebugKit');
define('ARRAY_ACTIVITY', $array);
// Configure::write('debug', 2);
Configure::write('Cache.disable', true);
/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */
//Configure::load('app_local', 'default');

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Email::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
//Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});

/*
 * Enable immutable time objects in the ORM.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/3.0/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
Type::build('time')
    ->useImmutable();
Type::build('date')
    ->useImmutable();
Type::build('datetime')
    ->useImmutable();
Type::build('timestamp')
    ->useImmutable();

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);
/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */

$conn = ConnectionManager::get("default"); // name of your database connection   

$settings_data = $conn->execute("SELECT key_value FROM settings WHERE key_name = 'timezone'");
$timezone = $settings_data->fetch('assoc');

if((isset($timezone['key_value']) && $timezone['key_value'] != "")) {      
    date_default_timezone_set($timezone['key_value']);
} else {
    date_default_timezone_set('Europe/London');
}

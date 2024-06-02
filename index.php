<?php
@ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
@ini_set('display_errors', 1);

define("NOT_CHECK_PERMISSIONS", true);
define("EXTRANET_NO_REDIRECT", true);
define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);

// Путь к корневой директории Bitrix
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . "/../../..");
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once $DOCUMENT_ROOT . '/bitrix/modules/main/include/prolog_before.php';

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/autoload.php'; // Подключаем автозагрузку Bitrix


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @var string $DBHost
 * @var string $DBLogin
 * @var string $DBPassword
 * @var string $DBName
 */
$jsonText = file_get_contents('php://input');
$thisarParams = $jsonText ? json_decode($jsonText, true) : null;

$app = new Application();

/** @var array $user */
/** @var array $debug */
$ext_func = function ($app, $request, $thisarParams) use ($user, $debug) {

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/dbconn.php");
    // Подключаемся к базе данных MySQL
    /**
     * @var string $DBHost
     * @var string $DBLogin
     * @var string $DBPassword
     * @var string $DBName
     * @var $lastPage
     */
    $mysqli = new mysqli($DBHost, $DBLogin, $DBPassword, $DBName);


    $arRes = [
        'data' => [],
        'count' => count([]),
        'last_page' => $lastPage < 1 ? 1 : $lastPage
    ];

    return $arRes;


};

$app->GET('/', function (Application $app, Request $request) use ($ext_func) {

    $_REQUEST = $_REQUEST ?? ['ok'];
    $thisarParams = $_REQUEST;

    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Credentials', true);
    $arRes = $ext_func($app, $request, $thisarParams);
    $response->setContent(json_encode($arRes, JSON_UNESCAPED_UNICODE));
    return $response;
});

$app->POST('/', function (Application $app, Request $request) use ($ext_func) {

    $_REQUEST = $_REQUEST ?? ['ok'];
    $thisarParams = $_REQUEST;

    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Credentials', true);
    $arRes = $ext_func($app, $request, $thisarParams);
    $response->setContent(json_encode($arRes, JSON_UNESCAPED_UNICODE));


    return $response;
});

$app->GET('/echo', function (Application $app, Request $request) {


    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Credentials', true);
    $response->setContent(json_encode([
        'status' => 'ok',
    ]));


    return $response;
});

$app->run();

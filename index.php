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
// Подключение Bitrix ORM

use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\DatetimeField;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Bitrix\Main\ORM\Data\DataManager;

class UserTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_user';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Entity\StringField('LOGIN'),
            new Entity\StringField('NAME'),
            new Entity\StringField('LAST_NAME'),
            new Entity\DatetimeField('TIMESTAMP_X', [
                'default_value' => new Type\DateTime,
            ]),
        ];
    }
}


Loader::includeModule('main');

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
    $users = UserTable::getList([
        'select' => ['ID', 'LOGIN', 'NAME', 'LAST_NAME'],
        'filter' => ['ACTIVE' => 'Y'],
        'limit' => 10,
    ])->fetchAll();



    $arRes = [
        'data' => $users,
        'count' => count($users)
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

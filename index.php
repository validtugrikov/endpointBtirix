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

$app = new Application();

$app->get('/', function () {
    global $USER;

    // Используем функциональность Bitrix
    if ($USER->IsAuthorized()) {
        $userName = $USER->GetFullName();
        return "Hello, $userName!";
    } else {
        return "Hello, guest!";
    }
});

$app->run();

<?php
use Router\Router;
$appConfig = require 'appConfig.php';
$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$url = strtolower($url);
$url = trim(str_ireplace($appConfig['prjName'],'',$url), '/');

// db connection
require 'Database/DBconn.php';
$dataBaseConfig = require "Database/databaseConfig.php";
$db = Database\DBconn::getDB($dataBaseConfig);

require "Router/Router.php";
$routerClass = new \Router\Router();
$routerClass->addController('GET', 'mvc/home','HomeController','PaginaHome');
$routerClass->addController('GET', 'mvc/products','ProductController','PaginaProdotto');
$routerClass->addController('GET', 'mvc/login','LoginController','PaginaLogin');
$routerClass->addController('GET', 'mvc/carrello','CarrelloController','PaginaCarrello');

$reValue = $routerClass->match($url,$method);

if (empty($reValue))
{
    http_response_code(404);
    header('location: App/View/error_page.php');
}

$controller = 'App\Controller\\'.$reValue["controller"];
$action = $reValue["action"];
require $controller.".php";
$controller = new $controller($db);
$controller->$action();
echo "<br>";

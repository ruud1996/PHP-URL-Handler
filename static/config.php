<?php
define("ROOT_PATH",         $_SERVER['DOCUMENT_ROOT'] . "/");

define("PAGES_PATH",        $_SERVER['DOCUMENT_ROOT'] . "/pages/");

define("WEBSITE_PATH",      "http://domein.nl/");
define("CSS_PATH",          "//domein.nl/misc/css/");
define("JS_PATH",           "//domein.nl/misc/js/");
define("IMG_PATH",          "//domein.nl/misc/img/");

define("COOCKIE_USER",      "userInfo");


if(!isset($_SESSION)) {
    session_start();
}

$settings = array(
    'activepage'    => '',
    'extraParam'    => ''
);

//Kijkt of er al een taal is gezet zoniet de default lang zetten
if (!isset($_SESSION['site']['lang'])) {
    $_SESSION['site']['lang'] = "nl";
}

//De lang file includen
if (isset($_SESSION['site']['lang']) && $_SESSION['site']['lang'] != "") {
    require(ROOT_PATH . 'misc/lang/' . $_SESSION['site']['lang'] . '.php');
}

//Alle default dingen die je wilt hebben bij elke pagina
//Zoals: DB connectie, lang files
require(ROOT_PATH . 'data/connection.php');

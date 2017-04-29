<?php

session_start();

require_once 'vendor/autoload.php';
require_once 'models/Cabinet.php';
require_once 'models/DataBase.php';

$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader);

$action = (isset($_GET['action'])) ? $_GET['action'] : 'login';

$data = [
    'action' => $action,
    'error' => Cabinet::$action(),
    'login' => Cabinet::$login,
    'password' => Cabinet::$password,
    'session' => isset($_SESSION['logged_user']) ? $_SESSION['logged_user'] : '',
    'checked' => (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on' ? 'checked' : '')
];

echo $twig->render("skeleton.html", $data);

var_dump($_POST, $_GET, $_SESSION);
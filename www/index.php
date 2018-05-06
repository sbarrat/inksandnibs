<?php
/**
 * Project: inksandnibs.
 * User: ruben
 * Date: 16/08/17
 * Time: 16:59
 */
require_once __DIR__ . '/../vendor/autoload.php';
use Inks\App;
session_start();
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates');
$twig = new Twig_Environment($loader);
$data = [];
$file = 'login.twig';
// Comprueba el estado de la session
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && isset($_SESSION['token'])) {
    $data = ['token' => $_SESSION['token']];
    $file = 'form.twig';
}
// Logea al usuario
if (isset($_POST['email']) && isset($_POST['password'])) {
    $app = new App();
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $results = $app->login($email, $password);
    $data = ['error' => 'Usuario o Contraseña Erroneos'];
    if (count($results) === 1) {
        $result = current($results);
        if ($result->email === $email && $result->password === sha1($password)) {
            $_SESSION['token'] = sha1($result->email . "ñññ" . $result->password);
            $_SESSION['user'] = $result->email;
            $_SESSION['loggedIn'] = true;
            $data = ['token' => $_SESSION['token']];
            $file = 'form.twig';
        }
    }
}
echo $twig->render($file, $data);

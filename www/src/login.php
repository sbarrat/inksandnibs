<?php
/**
 * Created by PhpStorm.
 * User: ruben
 * Date: 15/8/17
 * Time: 13:54
 */
require_once __DIR__.'/../../vendor/autoload.php';
use Inks\App;
use Inks\Wordpress;

session_start();
$html = false;
$loader = new Twig_Loader_Filesystem(__DIR__.'/../../templates');
$twig = new Twig_Environment($loader);
$app = new App();
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $results = $app->login($email, $password);
    foreach ($results as $result) {
        if ($result->email === $email && $result->password === sha1($password)) {
            $_SESSION['token'] = sha1($result->email."ñññ".$result->password);
            $_SESSION['user'] = $result->email;
            $template = $twig->load('form.twig');
            $html = $template->render(['token' => $_SESSION['token']]);
        }
    }
}
echo $html;

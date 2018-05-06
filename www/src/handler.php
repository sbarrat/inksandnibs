<?php
/**
 * Created by PhpStorm.
 * User: ruben
 * Date: 14/8/17
 * Time: 11:43
 */
require_once __DIR__.'/../../vendor/autoload.php';
use Inks\App;
use Inks\Wordpress;

session_start();
session_regenerate_id();
$html = false;
if (isset($_POST['token']) && $_SESSION['loggedIn'] && $_SESSION['token'] === $_POST['token']) {
    $params = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    $app = new App();
    unset($params['token']);
    $insert = $app->insertRecord($params);
    $html = '<div class="alert alert-danger">Datos duplicados</div>';
    if ($insert === 1) {
        $html = '<div class="alert alert-success">Datos Insertados</div>';
        $wordpress = new Wordpress();
        $result = $wordpress->addContent($params);
        $response = $app->updateRecord(
            ['imgUrl' => $params['imgUrl']],
            ['postID' => $result->ID, 'date' => $result->date]
        );
    }
}
echo $html;

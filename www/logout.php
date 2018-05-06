<?php
/**
 * Project: inksandnibs.
 * User: ruben
 * Date: 16/08/17
 * Time: 18:08
 */
session_start();
session_destroy();
header("Location:index.php");
exit();
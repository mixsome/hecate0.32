<?php
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'GET'){
	$action = $_GET['action'] == '' ? 'index' : $_GET['action'];
}
else if($method == 'POST'){
	$action = $_POST['action'] == '' ? 'index' : $_POST['action'];
}
include('' . $action . '.php');

?>
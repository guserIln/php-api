<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);


require __DIR__ . "/inc/bootstrap.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


$uri = explode( "/", $uri );



if ((isset($uri[3]) && $uri[3] != 'item') || !isset($uri[4])) {
	//var_dump($uri);
	echo "HTTP/1.1 404 Not Found";
    header("HTTP/1.1 404 Not Found");
    exit();
}

require PROJECT_ROOT_PATH . "/Controller/Api/ItemController.php";
$objFeedController = new ItemController();
$strMethodName = $uri[4] . 'Action';
$objFeedController->{$strMethodName}();

?>
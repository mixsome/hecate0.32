<?php
require 'mon.php';
$file = '1';
$handler = opendir('../uploadFile/');
var_dump(3123);
while(($filename = readdir($handler)) !== false ) {
	if($filename != "." && $filename != ".."){
		$str = explode(".",$filename);
		if ($str[0] == $file){
			$ProfilePicture = "upload/".$str[0].".".$str[1];
		}
	}
}
echo json_encode($ProfilePicture);
closedir($handler);
?>

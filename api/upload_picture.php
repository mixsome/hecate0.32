<?php


require 'mon.php';

$userid = $_POST['UserID'];
$TokenID = $_POST['TokenID'];
$UserName = $_POST['UserName'];
//var_dump($UserName);
if(isset($_FILES["image"])){
	//var_dump('aaaa');
	$obj = new serverinfo();
	$conn = $obj->db_connect();
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$temp = explode(".", $_FILES["image"]["name"]);
	//var_dump($_FILES["image"]["name"]);
	//var_dump($_FILES["image"]["size"]);
	$extension = end($temp);     
	if ((($_FILES["image"]["type"] == "image/gif")
	|| ($_FILES["image"]["type"] == "image/jpeg")
	|| ($_FILES["image"]["type"] == "image/jpg")
	|| ($_FILES["image"]["type"] == "image/pjpeg")
	|| ($_FILES["image"]["type"] == "image/x-png")
	|| ($_FILES["image"]["type"] == "image/png"))
	&& ($_FILES["image"]["size"] < 204800)   // 小于 200 kb
	&& in_array($extension, $allowedExts))
	{
		if ($_FILES["image"]["error"] > 0)
		{
			echo "error: " . $_FILES["image"]["error"] . "<br>";
		}
		else
		{
			//var_dump($_FILES['image']["name"]);
			if (file_exists("../uploadFile/" . $_FILES["image"]["name"]))
			{
				echo $_FILES["image"]["name"] . " file exist。 ";
				echo "<script type='text/javascript'>parent.uploadSuccess('$filepath')</script>";
			}
			else
			{
				$handler = opendir('../uploadFile/');

				while(($filename = readdir($handler)) !== false ) {

					if($filename != "." && $filename != ".."){
						$str = explode(".",$filename);
						if ($str[0] == $userid){
							$sql = "select profilePicture from Users where UserID = '$userid'";
		                    $result = $conn->query($sql);
		                    $row = $result->fetch_array(MYSQLI_ASSOC);
		                    $profilePicture = $row['profilePicture'];

							unlink("../".$profilePicture);

						}
					}
				}
				closedir($handler);
				move_uploaded_file($_FILES["image"]["tmp_name"], "../uploadFile/" . $userid . "." . $extension);
				$filepath = "uploadFile/" . $userid . "." . $extension;

				
				$sql = "update Users set profilePicture='$filepath' where UserID = '$userid'";

				$conn->query($sql);
				$conn->commit();
				$Status = '0';
				// $data  = array("Status"=>$Status,"");
				// $data = json_encode($data);
				echo $data;
				echo "<script type='text/javascript'>parent.uploadSuccess('$filepath')</script>";
			}
		}
	}
	else{
		$Status = '202';
		$data  = array("Status"=>$Status);
		echo json_encode($data);
		echo "<script type='text/javascript'>parent.uploadError('gif/loading.png')</script>";
	}
}
else
{
	$status = '201';
	$data  = array("Status"=>$Status);
	echo $data;
	echo "<script type='text/javascript'>parent.uploadError('gif/loading.png')</script>";
	
}






?>
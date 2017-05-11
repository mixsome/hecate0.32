<?php

require 'mon.php';

function user_login($username,$passwd)  {
    $obj = new serverinfo();
    $conn = $obj->db_connect();
    $conn->query('SET NAMES UTF8');

    $sql = "select UserID,GroupID,Password,TrueName,ProfilePicture from Users where UserName = '$username'";

     
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc() ){
       // foreach ($row as $key=>$value){
       //     $row[$key] = urlencode($value);
       // }

        $Password = $row["Password"];
        //var_dump($Password);
        $GroupID = $row["GroupID"];
        $UserID = $row["UserID"];
        $TrueName = $row["TrueName"];
	    $ProfilePicture = $row["ProfilePicture"];
        
	    $obje = new Cryp();
	    $Passwd = $obje->decrypt($passwd);
        if ($Password == $Passwd){
            $status = '0';
            $tokenid = $obj->timestamp();
            $tokenid .= $obj->random();
            $logintime = $obj->timestamp();
            $timeout = (int)$logintime + 600;
            $timeout = (string)$timeout;
            $opertime =  $logintime;
            $sql = "select LastLoginTime from UsersLoginLog where UserID = '$UserID'";
            $result = $conn->query($sql);
            $row = $result->fetch_array(); 
            $lastlogintime = $row['LastLoginTime'];
            if(empty($lastlogintime)){
                $Lastlogintime = $obj->timestamp();
                $sql = "insert into UsersLoginLog values('$UserID','$tokenid','$Lastlogintime','$logintime','$timeout','$opertime')";
                
                $conn->query($sql);
                $conn->commit();
            }
                    
            
            else {
                $Lastlogintime = $obj->timestamp();
                $sql = "update UsersLoginLog set TokenID = '$tokenid',LoginTime = '$logintime',TimeOut = '$timeout',OperationTime = '$opertime',Lastlogintime = '$Lastlogintime' where UserID = '$UserID'";
                $conn->query($sql);
                $conn->commit();
            }
            $sql = "select GroupName from Groups where GroupID = '$GroupID'";
            $result = $conn->query($sql);
            $row = $result->fetch_row();
            $GroupName = $row[0]; 
			//$handler = opendir('../uploadFile/');
			//while(($filename = readdir($handler)) !== false ) {
			//	if($filename != "." && $filename != ".."){
			//		$str = explode(".",$filename);
			//		if ($str[0] == "1"){
			//			$ProfilePicture = "uploadFile/".$str[0].".".$str[1];
			//		}
			//	}
			//}
			//closedir($handler);			
            $data = array(array("TrueName"=>$TrueName,"UserGroup"=>$GroupName,"LastLoginTime"=>$lastlogintime,"ProfilePicture"=>$ProfilePicture));
            $jsondata = array("UserName"=>$username,"TokenID"=>$tokenid,"Status"=>$status,"Data"=>$data);
            #echo  urldecode(json_encode($jsondata));
            echo  json_encode($jsondata);
        }
        else{
	           $data = array("Status"=>"101");
               echo json_encode($data);   
        }
           
    }
    else{
	     $data = array("Status"=>"103");
         echo json_encode($data);
    }
    $conn->close();  
}

$arr = json_decode($_POST['data'],true);
$username = $arr['UserName'];
$passwd = $arr['Passwd'];
//var_dump($passwd);
//echo $username;
//echo $passwd;
////$username = 'sisi';
////$passwd = '123dfdf';
user_login($username,$passwd);


?>

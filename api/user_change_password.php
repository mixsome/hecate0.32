<?php
require 'mon.php';
function  user_change_password($UserName,$Password,$NewPassword,$TokenID){
    $obj = new serverinfo();
    $userid = $obj->get_userid($UserName);
    $flag = $obj->check_tokenid($userid,$TokenID);
	//$obje = new Cryp();
	//$Password = $obje->decrypt($Password);
	//$NewPassword = $obje->decrypt($NewPassword);
    if ($flag == '0'){
        $conn = $obj->db_connect();
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
        }
        sql = "select Password from Users where UserID = '$userid'";
        $result = $conn->query($sql);
        if ($row = $result->fetch_row() ){
                $OldPassword = $row[0];
        }
        #先判断是否超时再更新操作时间
        $timeout_flag = $obj->check_timeout($userid);               
        if($timeout_flag == '0'){            
            $obj->operation_time($userid);
            if ($Password == $OldPassword){
                $sql = "update Users set Password = '$NewPassword'";
                $conn->query($sql);
				$conn->commit();
                $status = '0';
                $jsondata = array("UserName"=>"$UserName","TokenID"=>"$TokenID","Status"=>"$status");
                echo json_encode($jsondata);
            }
            else{
            //    $conn->close();
                $status = '101';
                echo json_encode($status);
            }            
                          
        }
        
        else{
        //    $conn->close();
            $status = '104';
            echo json_encode($status);
        }       
    }
    else{
    //    $conn->close();
        $status = '103';
        echo json_encode($status);
    }
	$obj->operation_time($userid);
    $conn->close();   
}
}

$arr = json_decode($_POST['data'],true);
$UserName = $arrT["UserName"];
$Password = $arr["Password"];
$NewPassword = $arr["NewPassword"];
$TokenID = $arr["TokenID"];
user_change_password($UserName,$Password,$NewPassword,$TokenID);

?>
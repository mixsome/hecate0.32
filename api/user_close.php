<?php
require 'mon.php';


function  user_close($UserName,$TokenID){
    $obj = new serverinfo();
    //var_dump(2312);
    $userid = $obj->get_userid($UserName);
    $flag = $obj->check_tokenid($userid,$TokenID);
    if ($flag == '0'){
        $Staus =0;
        $timenum = $obj->timestamp();
        $tokenid = $obj->timestamp();
        $tokenid .= $obj->random();
        $conn = $obj->db_connect();
        if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
        } 
        $sql = "update UsersLoginLog set LastLoginTime = '$timenum',TokenID = '$tokenid' where UserID = '$userid'";
        //var_dump($sql);
        $conn->query($sql);
        $conn->commit();
		$obj->operation_time($userid);
        $conn->close();
        $Status = '0';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;
    }
    else{
        $Status = '201';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;
    }         
}

$arr = json_decode($_GET['data'],true);
$UserName = $arr["UserName"];
//var_dump($UserName);
$TokenID = $arr["TokenID"];
user_close($UserName,$TokenID);
?>
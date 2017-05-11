<?php
require 'mon.php';
function update_job($TokenID,$UserName,$Data){
        $obj = new serverinfo();
        $data = $data[0];
        $ID = $data["JobID"];
        $Name = $data["JobName"];
        //$IndicatorIDs = $data["IndicatorIDs"];
        //$IDs = implode(",",$IndicatorIDs);    
        $Remarks = $data["Remarks"];
		$userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
		if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){  
                $Status = '0';
                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');

                $sql = "update Job set JobName = '$Name',Remarks = '$Remarks' where JobID = '$ID'";
                $conn->query($sql);
                $conn->commit();
                $jsondata = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                echo json_encode($jsondata);
            }
            else {
            $Status = '103';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;

        }

    } else {
        $Status = '103';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;
		$obj->operation_time($userid);
        $conn->close(); 
     }
 }
$arr = json_decode($_POST['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];    
$data = $arr["Data"];
update_job($TokenID,$UserName,$Data);
?>
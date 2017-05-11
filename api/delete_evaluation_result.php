<?php
require 'mon.php'; 

function delete_evaluation_result($TokenID,$UserName,$Data){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');

     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $Status = '0';
                $EvaluationID = $Data["EvaluationID"];
                $ExamineeID = $Data["ExamineeID"];
                $sql = "delete from EvaluationInfo where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID'";
                $conn->query($sql);
				$conn->commit();
                $sql = "delete from EvaluationState where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID'";
                $conn->query($sql);
                $conn->commit();
                $sql = "delete from MailInfo where EvaluationID = '$EvaluationID'";
                $conn->query($sql);
                $conn->commit();
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                echo json_encode($data);                   
            }   
            else{
               $Status='104';
			   $data = array("Status"=>$Status);
               echo json_encode($data);               
            }
        }
        else{        
           $Status = '103';
           $data = array("Status"=>$Status);
           echo json_encode($data);   
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_POST['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$Data = $arr["Data"];

// $UserName = 'chenkai';
// $TokenID = '14924095624085266265';
// $Data = array("EvaluationID"=>2, "ExamineeID"=>7);

delete_evaluation_result($TokenID,$UserName,$Data);

?>

<?php


require 'mon.php';


function delete_evaluation_type($TokenID,$UserName,$EvaluationID){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');

     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $Status = '0';
                $sql = "select EvaluationStatus from EvaluationType where EvaluationID = '$EvaluationID'";
                $result = $conn->query($sql);
                $row = $result->fetch_array(); 
                $EvaluationStatus = $row['EvaluationStatus'];    
                if ( $EvaluationStatus == '3' ){
                    $Status = '201';
                    echo json_encode($Status);
                    $obj->operation_time($userid);
                    $conn->close();               
                    exit;
                }
                $sql = "delete from EvaluationInfo where EvaluationID = '$EvaluationID'";
                $conn->query($sql); 
                $conn->commit();
                $sql = "delete from EvaluationType where EvaluationID = '$EvaluationID'";
                $conn->query($sql);
                $conn->commit();
                $sql = "delete from EvaluationState where EvaluationID = '$EvaluationID'";
                $conn->query($sql);
                $conn->commit();
                $sql = "delete from MailInfo where EvaluationID = '$EvaluationID'";
                $conn->query($sql);
                $conn->commit();
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                echo json_encode($data);                   
            }   
            else{
               $status='104';
               echo json_encode($status);
            }
        }
        else{        
           $status = '103';
           echo json_encode($status);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_POST['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$EvaluationID = $arr['EvaluationID'];    
delete_evaluation_type($TokenID,$UserName,$EvaluationID);


?>

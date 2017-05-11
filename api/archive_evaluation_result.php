<?php

require 'mon.php';


function archive_evaluation_result($TokenID,$UserName,$EvaluationID){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');
     //var_dump(341231);

     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $Ask = 'Evaluation';     

                $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
                if($Permission == 'Y'){
                    $Status = '0';
                    $sql = "select EvaluationName,StartTime,Deadline from EvaluationType where EvaluationID = '$EvaluationID'";
                    $result = $conn->query($sql);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $EvaluationName = $row['EvaluationName'];
    				$StartTime = $row['StartTime'];
                    $Deadline = $row['Deadline'];
                    $sql = "select ExamineeID,SumScore from EvaluationState where EvaluationID = '$EvaluationID'";
                    //var_dump($sql);
                    $result = $conn->query($sql);
                    while($row = $result->fetch_array(MYSQLI_ASSOC)){

                        $ExamineeID = $row['ExamineeID'];
                        $SumScore = $row['SumScore'];
                        //var_dump($ExamineeID);
                        $sql = "select TrueName from Users where UserID = '$ExamineeID'";
                        //var_dump($sql);
                        $result1 = $conn->query($sql);
                        $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                        $TrueName = $row1['TrueName'];
                        //var_dump($TrueName);
                        
                        //var_dump($SumScore);
                        $sql2 = "insert into Archive (EvaluationName,ExamineeName,SumScore,StartTime,Deadline) values('$EvaluationName','$TrueName','$SumScore','$StartTime','$Deadline')";
                        //var_dump($sql);
                        $conn->query($sql2);
                        $conn->commit();                    
                    }
    				$sql = "update EvaluationType set EvaluationStatus = '3' where EvaluationID = '$EvaluationID'";
    				$conn->query($sql);
                    $conn->commit();
                    $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                    echo json_encode($data);
                }
                else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $Data = json_encode($data);
                    echo $Data;
                }                   
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
$arr = json_decode($_GET['data'],true);
//var_dump($arr);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$EvaluationID = $arr["EvaluationID"];

archive_evaluation_result($TokenID,$UserName,$EvaluationID);


?>

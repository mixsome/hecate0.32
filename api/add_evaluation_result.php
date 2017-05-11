<?php
require 'mon.php'; 


function add_evaluation_result($TokenID,$UserName,$Data){
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
                $ExamineeIDs = $Data["ExamineeID"];
                $ExaminerIDs = $Data["ExaminerID"];
                $IsMasters = $Data["IsMaster"];
                $AddTime = $obj->timestamp();
                for($i=0;$i<count($ExaminerIDs);$i++){
                    foreach ($ExamineeIDs as $item){
                        $sql = "insert into EvaluationInfo (EvaluationID,ExamineeID,ExaminerID,IsMaster,AddTime) values('$EvaluationID','$item','$ExaminerIDs[$i]','$IsMasters[$i]','$AddTime')";
                        //var_dump($sql);
                        $conn->query($sql);
						$conn->commit();
                        
                    }
                }
                foreach ($ExamineeIDs as $key => $value) {
                    $sql = "select count(ExaminerID) from EvaluationInfo where EvaluationID='$EvaluationID' and ExamineeID='$value'";
                    //var_dump($sql);
                    $result = $conn->query($sql);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $ExaminerSum = $row['count(ExaminerID)']; 
                    //var_dump($ExaminerSum);
                    $sql = "insert into EvaluationState (EvaluationID,ExamineeID,ExaminerSum) values('$EvaluationID','$value','$ExaminerSum')";
                    //var_dump($sql);
                    $conn->query($sql);
                    $conn->commit();
                }          
                $conn->close();                     
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                echo json_encode($data);                   
            }   
            else{
               $data = array("Status"=>"104");
               echo json_encode($data);
            }
        }
        else{        
           $data = array("Status"=>"103");
           echo json_encode($data);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_POST['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$Data = $arr["Data"][0];

// $UserName = 'chenkai';
// $TokenID = '14924095624085266265';
// $Data = array("EvaluationID"=>2, "ExamineeID"=>[6,7,8,9,10],"ExaminerID"=>[1,4],"IsMaster"=>["Y","N"]);

add_evaluation_result($TokenID,$UserName,$Data);

?>

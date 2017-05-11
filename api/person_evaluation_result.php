<?php
require 'mon.php'; 
function person_evaluation_result($TokenID,$UserName,$EvaluationID,$ExamineeID){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');

     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){

            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){

                $Status = '0';
                $sql = "select Template.IndicatorAndWeight from EvaluationType,Template where EvaluationType.EvaluationID = '$EvaluationID' and EvaluationType.TemplateID = Template.TemplateID";

                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $IndicatorAndWeight = json_decode($row['IndicatorAndWeight']);

                $sql = "select ExaminerID,IsMaster from EvaluationInfo where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID'";
                $result = $conn->query($sql);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $ExaminerID[] = $row['ExaminerID'];
                    $IsMaster[] = $row['IsMaster'];
                }
                
                $jsondata = array("EvaluationID"=>$EvaluationID,"ExamineeID"=>$ExamineeID,"ExaminerID"=>$ExaminerID,"IsMaster"=>$IsMaster,"IndicatorAndWeight"=>$IndicatorAndWeight);                           
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>$jsondata);
                echo json_encode($data);                   
            }   
            else{
               $status='104';
			   $data = array("Status"=>"$status");
               echo json_encode($data);
            }
        }
        else{        
           $status = '103';
           $data = array("Status"=>"$status");
           echo json_encode($data);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_GET['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$EvaluationID = $arr["EvaluationID"];
$ExamineeID = $arr["ExamineeID"];

// $UserName = 'chenkai';
// $TokenID = '14924095624085266265';
// $EvaluationID = 1;
// $ExamineeID = 3;

person_evaluation_result($TokenID,$UserName,$EvaluationID,$ExamineeID);

?>

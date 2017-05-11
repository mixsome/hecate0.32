<?php
require 'mon.php'; 


function add_evaluation_type($TokenID,$UserName,$Data){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');

     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){                        
            $timeout_flag = $obj->check_timeout($userid);
              
            if($timeout_flag == '0'){
                $EvaluationName = $Data['EvaluationName'];
                $TemplateID = $Data['TemplateID'];
                $StartTime = $Data['StartTime'];
                $Deadline = $Data['Deadline'];

                $Status = '0';
                $CreateTime = $obj->timestamp();

                if ($Deadline < $CreateTime){
                    $data = array("Status"=>"104");
                    echo json_encode($data);
                }
                elseif($CreateTime >$StartTime){
                    $EvaluationStatus = '1';
                }
                else{
                  $EvaluationStatus = '0';
                }
                $sql = "insert into EvaluationType (EvaluationName,TemplateID,StartTime,Deadline,CreateTime,EvaluationStatus) values('$EvaluationName','$TemplateID','$StartTime','$Deadline','$CreateTime','$EvaluationStatus')";

                $conn->query($sql);
				$conn->commit();
                $sql = "select EvaluationID from EvaluationType order by CreateTime desc";

                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $EvaluationID = $row['EvaluationID'];

                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"EvaluationID"=>$EvaluationID);
                echo json_encode($data);                   
            }   
            else{
	             $data = array("Status"=>'104');
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

//var_dump($Data);
// $UserName = 'chenkai';
// $TokenID = '14921498141455623706';
// $Data = array("EvaluationName"=>'2017考核',"TemplateID"=>2,"StartTime"=>"1493155432","Deadline"=>"1499155432");
  
add_evaluation_type($TokenID,$UserName,$Data);


?>

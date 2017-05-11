<?php
require 'mon.php'; 
function add_evaluation($tokenid,$username,$data){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){                        
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $EvaluationName = $data['EvaluationName'];
                $TemplateID = $data['TemplateID'];
                $StartTime = $data['StartTime'];
                $Deadline = $data['Deadline'];
                $status = '0';
               //$sql = "select TemplateID from Template where TemplateName = '$TemplateName'";                              
               //$result = $conn->query($sql);
               //$row = $result->fetch_row();
               //$TemplateID = $row[0];
                $CreateTime = $obj->timestamp();
                $EvaluationStatus = '0';
                $sql = "insert into EvaluationType (EvaluationName,TemplateID,StartTime,Deadline,CreateTime,EvaluationStatus) values('$EvaluationName','$TemplateID','$StartTime','$Deadline','$CreateTime','$EvaluationStatus')";
                $conn->query($sql);
				$conn->commit();
                $data = array("UserName"=>"$username","TokenID"=>"$tokenid","Status"=>"$status");
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
$Data = $arr["Data"];  
add_evaluation($TokenID,$UserName,$Data);


?>

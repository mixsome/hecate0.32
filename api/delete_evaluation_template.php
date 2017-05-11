<?php

require 'mon.php'; 



function delete_evaluation_template($TokenID,$UserName,$TemplateID){
     $obj = new serverinfo();
     $conn = $obj->db_connect();

     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
            	//$TemplateID = $Data['TemplateID'];
            	//var_dump($TemplateID);
                $Status = '0';
				$sql = "select Deadline,StartTime from EvaluationType where TemplateID = '$TemplateID'";
				$result = $conn->query($sql);
				while($row = $result->fetch_array()){				   
					
						$deadline = $row['Deadline'];
						$StartTime = $row['StartTime'];
						$time = $obj->timestamp();
						if ($time < $deadline and $time >$StartTime){
						    $Status = '201';
							$data = array("Status"=>$Status);
							echo json_encode($data);							
							$obj->operation_time($userid);
							$conn->close();					
							exit;
						}
					
				}    
				$sql = "select EvaluationID from EvaluationType where TemplateID = '$TemplateID'";
				$result = $conn->query($sql);
				while($row = $result->fetch_array()){
					
						$EvaluationID = $row['EvaluationID'];
						$sql = "delete from EvaluationInfo where EvaluationID = '$EvaluationID'";
		                $conn->query($sql); 
						$conn->commit();
		    //             $sql = "delete from EvaluationType where EvaluationID = '$EvaluationID'";
		    //             $conn->query($sql);
			// 			   $conn->commit();
		                $sql = "delete from EvaluationState where EvaluationID = '$EvaluationID'";
		                $conn->query($sql);
		                $conn->commit();
		                $sql = "delete from MailInfo where EvaluationID = '$EvaluationID'";
		                $conn->query($sql);
		                $conn->commit();
					}
				$sql = "delete from EvaluationType where TemplateID = '$TemplateID'";
				$conn->query($sql);
				$conn->commit();
				
				//var_dump(1111);			
                $sql = "delete from Template where TemplateID = '$TemplateID'";
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
$TemplateID = $arr["TemplateID"];

// $UserName = 'chenkai';
// var_dump($UserName);
// $TokenID = '14920644872777833903';
// $Data = array("TemplateID"=>8);
// var_dump($Data);

delete_evaluation_template($TokenID,$UserName,$TemplateID);


?>

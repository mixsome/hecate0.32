<?php
require 'mon.php';
function get_job_list($TokenID,$UserName){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');
     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){                        
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $EvaluationName = $data['EvaluationName'];
                $TemplateName = $data['TemplateName'];
                $StartTime = $data['StartTime'];
                $Deadline = $data['Deadline'];
                $status = '0';
                $sql = "select * from Job";
                $result = $conn->query($sql);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Array_data[] = $row; 
                }
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$status,"Data"=>$Array_data);
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
get_job_list($TokenID,$UserName);


?>

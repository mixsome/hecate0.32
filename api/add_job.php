<?php
require 'mon.php';
function add_job($TokenID,$UserName,$Data){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');
     $userid = $obj->get_userid($UserName);
     //var_dump($userid);
        $flag = $obj->check_tokenid($userid,$TokenID);
        //var_dump($flag);
        if ($flag == '0'){                        
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){                
                $Status = '0';
                $JobName = $Data[0]['JobName'];
                //var_dump($JobName);
                $Remarks = $Data[0]['Remarks'];
                //var_dump($Remarks);
                $sql = "select JobID from Job where JobName = '$JobName'";
                $result = $conn->query($sql);
                if ($row = $result->fetch_row()){
                    //var_dump($row);
                    $status = '201';
                    echo json_encode($status);
                    $obj->operation_time($userid);
                    $conn->close();
                    exit; 
                }
                $sql = "insert into job (JobName,Remarks) values('$JobName','$Remarks')";
                $conn->query($sql);
				$conn->commit();				
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
$Data = $arr['Data'];
//var_dump($Data);
add_job($TokenID,$UserName,$Data);


?>

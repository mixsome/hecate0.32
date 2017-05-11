<?php
require 'mon.php';
function delete_job($TokenID,$UserName,$Data){
        $obj = new serverinfo();
        $n=0;
        foreach ($Data as $item){
            foreach ($item as $key=>$value){
                $items[$n] = $value;                                                     
            }
            $n += 1;        
        }
		$userid = $obj->get_userid($UserName);
		$flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){  
                $status = '0';
                $conn = $obj->db_connect();
                foreach ($items as $item){
                    $sql = "delete from Job where JobID = '$item'";
                    $conn->query($sql);
					$conn->commit();
                }
                $data = array("UserName"=>"$UserName","TokenID"=>"$TokenID","Status"=>"$status");
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
$Data = $arr["Data"];
get_job_list($TokenID,$UserName,$Data);
?>
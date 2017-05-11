<?php
require 'mon.php';
    
function get_evaluation_type($TokenID,$UserName){
        $obj = new serverinfo();
        $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){  
                $status = '0';
                $conn = $obj->db_connect();
                $sql = "select EvaluationID,EvaluationName,EvaluationYear from EvaluationType";
                $result = $conn->query($sql);
                $row = $result->fetch_all(MYSQLI_ASSOC);
//                $n=0;
//                foreach ($row as $item){
//                    foreach ($item as $key=>$value){
//                        $item[$key] = urlencode($value);        
//                    }                                  
//                    $arr[$n] = $item;
//                    $n += 1;       
//                }
                $jsondata = array("UserName"=>"$UserName","TokenID"=>"$TokenID","Status"=>"$status","Data"=>$row);
                echo  urldecode(json_encode($jsondata));
            
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
get_evaluation_type($TokenID,$UserName);
?>
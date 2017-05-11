<?php
require 'mon.php'; 
function get_examiner_name($TokenID,$UserName,$Keyword){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');

     $userid = $obj->get_userid($UserName);
     $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $Status = '0';
                if ($Keyword == ''){
                    $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>[]);
                    echo json_encode($data); 
                }
                else{
                $Keyword = '%'.$Keyword.'%';
                $sql = "select UserID,TrueName from Users where TrueName like '$Keyword'";
                //var_dump($sql);
                $result = $conn->query($sql);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Data[] = $row;
                }
                $Data = $obj->nonull($Data);                                                                         
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>$Data);
                echo json_encode($data);
                }                   
            }   
            else{
               $Satus='104';
               echo json_encode($Status);
            }
        }
        else{        
           $Status = '103';
           echo json_encode($Status);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_GET['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$Keyword = $arr["TrueName"];

// $UserName = 'chenkai';
// $TokenID = '14923958733399744200';
// $Keyword = '钟';

get_examiner_name($TokenID,$UserName,$Keyword);

?>

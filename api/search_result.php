<?php



require 'mon.php';



function search_result($TokenID,$UserName,$Data){
		$obj = new serverinfo();
    $userid = $obj->get_userid($UserName);
    $flag = $obj->check_tokenid($userid,$TokenID);
		if ($flag == '0'){
          $timeout_flag = $obj->check_timeout($userid);               
          if($timeout_flag == '0'){  
            $SearchType = $Data["SearchType"];
            $key = $Data["key"];
            $value = $Data["value"];
            $Status = '0';
            $conn = $obj->db_connect();
            $conn->query('SET NAMES UTF8');

            $sql = "select * from '$SearchType' where '$key' = '$value'";
            $result = $conn->query($sql);
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
              $search_result = $row;
            }
            $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>$search_result);
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
$Data =  $arr["Data"];

search_result($TokenID,$UserName,$Data);
?>
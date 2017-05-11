<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/28
 * Time: 11:06
 */
require 'mon.php';


function update_user_page($UserName,$TokenID,$UserID)
{
    $obj = new serverinfo();

    
    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);
            $pinyin = new serverinfo();

            $Ask = 'UsersUpdate';

            $Permission = $obj->get_permission($UserName,$Ask);
            

            if($Permission == 'Y'){
            	$conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
            	$sql = "select * from Users,SecretInfo where Users.UserID = SecretInfo.UserID and Users.UserID = '$UserID'";
            	
            	$result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $row['ContractPeriod'] = json_decode($row['ContractPeriod'],true);
                $JobID=$row['JobID'];
                $sql1 = "select JobName from Job where JobID = '$JobID'";
                //var_dump($sql1);
                $result1 = $conn->query($sql1);
                $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                $row['JobName'] = $row1['JobName'];
                

                
                


          
                
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status,"Data"=>$row);


                $Data = json_encode($data);
                echo $Data;
                $conn->close();
            }
            else{
                $Status = '404';
                $data = array("Status" => $Status);
                $Data = json_encode($data);
                echo $Data;
            }
        } else {
            $Status = '201';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;

        }

    } else {
        $Status = '201';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;

    }
}
$req_data = $_GET['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$UserID = $deal_data['UserID'];

//var_dump($Array_data);
update_user_page($UserName,$TokenID,$UserID);
//$pin = new serverinfo();
//echo $pin->Pinyin('陈凯','UTF8');
?>
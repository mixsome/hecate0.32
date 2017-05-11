<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 11:46
 */
require 'mon.php';



function get_user_secretinfo($UserName,$TokenID,$UserID)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);
            
            $Ask = 'SecretInfo';   //SecretSelect    

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                $sql = "select * from SecretInfo where UserID='$UserID'";
                $result = $conn->query($sql);
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $row['ContractPeriod'] = json_decode($row['ContractPeriod'],true);
                    $Array_data[] = $row;
                }
                $conn->close();
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data);


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
$Arr = $deal_data['Data'];

$UserID = $Arr[0]['UserID'];


get_user_secretinfo($UserName,$TokenID,$UserID);
?>
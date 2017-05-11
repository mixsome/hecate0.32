<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 15:55
 */


require 'mon.php';


#session start();

function update_asset($UserName,$TokenID,$Array_Assets)
{
    $obj = new serverinfo();


    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'AssetsUpdate';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                foreach ($Array_Assets as $Assets) {
                    $AssetsID = $Assets['AssetsID'];
                    foreach ($Assets as $key => $value) {

                        $sql = "update  Assets set $key = '$value' where AssetsID='$AssetsID'";
                        $conn->query($sql);
                    }
                }

                $conn->close();
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);


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
$req_data = $_POST['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];

$Array_Assets = $deal_data['Assets'];

update_asset($UserName,$TokenID,$Array_Assets);
?>
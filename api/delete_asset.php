<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 15:46
 */

require 'mon.php';

function delete_asset($UserName,$TokenID,$Array_AssetsID)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'AssetsDelete';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){


                $conn = $obj->db_connect();



                foreach ($Array_AssetsID as $key => $AssetsID ) {
                    $sql = "delete from Assets where AssetsID ='$AssetsID '";
                    $conn->query($sql);
    				$conn->commit();
                }

                $conn->close();
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);


                $Data = json_encode($data);
                echo $Data;
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
$Array_AssetsID = $deal_data['AssetsID'];
delete_asset($UserName,$TokenID,$Array_AssetsID);
?>
<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 17:27
 */


require 'mon.php';


function get_addasset_page($UserName,$TokenID)
{   
    //var_dump(43534);
    $obj = new serverinfo();
    //ar_dump(333);
    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        //var_dump(222);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $conn = $obj->db_connect();
            $conn->query('SET NAMES UTF8');

            $sql = "select * from AssetsType";
            $result = $conn->query($sql);
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $AssetsType[] = $row;
            }
            //var_dump($AssetsType);
            $sql = "select BatchID from Batch";
            $result = $conn->query($sql);
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $BatchID[] = $row['BatchID'];
            }
            $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "BatchID" => $BatchID, "AssetsType" => $AssetsType);
            $Data = json_encode($data);
            echo $Data;
            $conn->close();
            }
        else {
            $Status = '201';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;

        }
    }

    else {
        $Status = '201';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;

    }

}
$req_data = $_GET['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
//var_dump($UserName);
$TokenID = $deal_data['TokenID'];
//var_dump(1111);
get_addasset_page($UserName,$TokenID);
?>



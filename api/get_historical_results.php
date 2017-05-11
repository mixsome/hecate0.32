<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 11:20
 */
require 'mon.php';



function get_historical_results($UserName,$TokenID)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'Evaluation';     

            $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                $sql = "select * from Archive order by Deadline desc";
                $result = $conn->query($sql);
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $Array_data[] = $row;
                }
                $conn->close();
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data);


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
$req_data = $_GET['data'];
//var_dump($req_data);
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
//var_dump($UserName);
$TokenID = $deal_data['TokenID'];
//var_dump(23123);

get_historical_results($UserName,$TokenID);
?>
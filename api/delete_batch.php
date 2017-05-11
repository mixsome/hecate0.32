<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 15:51
 */
require 'mon.php';

function delete_batch($UserName,$TokenID,$BatchID)
{
    $obj = new serverinfo();
    //var_dump(111);
    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'BatchDelete';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();

                //$BatchID = $Batch['BatchID'];
                $sql = "delete from Batch where BatchID ='$BatchID'";
                //var_dump($sql);
                $conn->query($sql);
                $conn->commit();

            
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
//var_dump($deal_data);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$BatchID = $deal_data['BatchID'];
//var_dump(111);
delete_batch($UserName,$TokenID,$BatchID);
?>
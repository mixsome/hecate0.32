<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 17:49
 */

require 'mon.php';




function asset_recycle($UserName,$TokenID,$Array_Data)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $conn = $obj->db_connect();
            $conn->query('SET NAMES UTF8');

            $Ask = 'BatchUpdate';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){


                $AssetsID = $Array_Data['AssetsID'];
;
                $sql = "select Status from Assets where AssetsID = '$AssetsID'";

                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $Asset_Status = $row['Status'];


                $sql = "select LogID,EndTime from AssetsChangeLog where AssetsID = '$AssetsID' order by LogID desc";

                $result = $conn->query($sql);
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                $Asset_EndTime = $row['EndTime'];

                $Asset_LogID = $row['LogID'];

                if ($Asset_Status == "启用"){

                    if (empty($Asset_EndTime)){

                        $Asset_EndTime = time();
                        $sql = "update AssetsChangeLog set EndTime = '$Asset_EndTime' where LogID = '$Asset_LogID'";

                        $conn->query($sql);
                        $conn->commit();
                        $sql = "update Assets set Status = '未用',UsedBy = '' where AssetsID = '$AssetsID'";

                        $conn->query($sql);
                        $conn->commit();
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                    else{
                        $conn->close();
                        $Status = '402';
                        $data = array("Status" => $Status);
                        $Data = json_encode($data);
                        echo $Data;
                    }

                }


                else {
                    $Status = '401';
                    $data = array("Status" => $Status);
                    $Data = json_encode($data);
                    echo $Data;

                }
            }
            else{
                    $Status = '404';
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

    else {
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
$Array_Data =$deal_data['Data'][0];
asset_recycle($UserName,$TokenID,$Array_Data);
?>
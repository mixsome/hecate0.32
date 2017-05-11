<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 17:49
 */

require 'mon.php';




function asset_release($UserName,$TokenID,$Array_Data)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'BatchUpdate';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');



                $AssetsID = $Array_Data['AssetsID'];
                $UserID = $Array_Data['UserID'];
                $TrueName = $Array_Data['TrueName'];
                var_dump($TrueName);
                $sql = "select Status from Assets where AssetsID = '$AssetsID' ";
                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $Asset_Status = $row['Status'];
                //var_dump($Asset_Status);
                if ($Asset_Status == "未用" or $Asset_Status =="存库"){
                    //var_dump(1111);
                    $sql = "update Assets set Status = '启用',UsedBY='$TrueName' where AssetsID = '$AssetsID'";
                    $conn->query($sql);
                    $conn->commit();
                    $StartTime=time();
                    // $sql = "select TrueName from Users where UserName = '$UserName'";
                    // $result = $conn->query($sql);
                    // $row = $result->fetch_array(MYSQLI_ASSOC);
                    // $TrueName = $row['TrueName'];
                    $sql = "insert into AssetsChangeLog (AssetsID,UserID,StartTime,Operator) VALUES('$AssetsID','$UserID','$StartTime','$TrueName')";
                    var_dump($sql);
                    $conn->query($sql);
                    $conn->commit();
                    //var_dump(2222);
                    $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);
                    $Data = json_encode($data);
                    echo $Data;
                    $conn->close();
                }
            



                elseif ($Asset_Status == "返修" or $Asset_Status =="启用") {
                    var_dump(31232);
                    $Status = '401';
                    $data = array("Status" => $Status);
                    $Data = json_encode($data);
                    echo $Data;
                 } 
                    

                
            }else{
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
// $UserName = 'chenkai';
// $TokenID = '14919773756391668481';
// $Array_Data = array("UserID"=>'4',"AssetsID"=>123123);


asset_release($UserName,$TokenID,$Array_Data);

?>

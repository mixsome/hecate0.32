<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 15:56
 */

require 'mon.php';

function add_asset($UserName,$TokenID,$Array_Assets)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'AssetsInsert';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                foreach ($Array_Assets as $Assets) {
                    $AssetsID = $Assets['AssetsID'];
                    //var_dump($AssetsID);
                    $AssetsTypeID= $Assets['AssetsTypeID'];
                    $BatchID= $Assets['BatchID'];
                    $SerialNumber= $Assets['SerialNumber'];
                    $BrandModel= $Assets['BrandModel'];
                    $Specification= $Assets['$Specification'];
                    $Price= $Assets['Price'];
                    $Position= $Assets['Position'];
                    $PutTime= $Assets['PutTime'];


                    $sql = "insert into Assets (AssetsID,AssetsTypeID,BatchID,SerialNumber,BrandModel,Specification,Price,Position,PutTime) values('$AssetsID','$AssetsTypeID','$BatchID','$SerialNumber','$BrandModel','$Specification','$Price','$Position','$PutTime');";
                    $conn->query($sql);
    				$conn->commit();

                }

                $conn->close();
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);


                $Data = json_encode($data);
                echo $Data;
                $conn->close();
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
// $UserName = 'chenkai';
// $TokenID = '14919682083755717459';
// $Array_Assets = array(array("AssetsID"=>123123));

add_asset($UserName,$TokenID,$Array_Assets);
?>
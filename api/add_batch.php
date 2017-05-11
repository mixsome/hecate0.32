<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 15:57
 */

require 'mon.php';

function add_batch($UserName,$TokenID,$Array_Batch)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'BatchInsert';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                
                $BatchID = $Array_Batch['BatchID'];
                //var_dump($BatchID);
                $AssetsNum = $Array_Batch['AssetsNum'];
                $TotalCost= $Array_Batch['TotalCost'];
                $Supplier= $Array_Batch['Supplier'];
                $Contacts= $Array_Batch['Contacts'];
                $PhoneNumber= $Array_Batch['PhoneNumber'];
                $Project= $Array_Batch['Project'];
                $InfoProject= $Array_Batch['InfoProject'];
                $Principal = $Array_Batch['Principal'];
                $StorageTime = time();
                //var_dump($StorageTime);

                $sql = "insert into Batch (BatchID,AssetsNum,TotalCost,Supplier,Contacts,PhoneNumber,Project,InfoProject,Principal,StorageTime) values('$BatchID','$AssetsNum','$TotalCost','$Supplier','$Contacts','$PhoneNumber','$Project','$InfoProject','$Principal','$StorageTime')";
                $conn->query($sql);
    			$conn->commit();

                

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
var_dump($deal_data);
$UserName = $deal_data['UserName'];
//$UserName = 'chenkai';
$TokenID = $deal_data['TokenID'];
//$TokenID = '14919607683559733735';
$Array_Batch = $deal_data['Data'];
//$Array_Batch = array("BatchID"=>123123);
add_batch($UserName,$TokenID,$Array_Batch);
?>
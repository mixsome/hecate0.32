<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 9:46
 */

require 'mon.php';




function get_batch_list($UserName,$TokenID,$TypeData)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        //var_dump($Status);
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);
            
            $Ask = 'BatchSelect';      

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                $ArrivalTime = $TypeData['ArrivalTime'];


                    if (empty($ArrivalTime)) {


                        $sql = "select * from Batch";
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $row['StorageTime'] = date('Y年m月d日',$row['StorageTime']);
                            $Array_data[] = $row;
                            //var_dump($row['Project']);

                        }
                        $sql = "select StorageTime from Batch";
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            //$StorageTime = $row['StorageTime'];
                            //var_dump($StorageTime);
                            $storageYear[] = date('Y',$row['StorageTime']);
                            //var_dump($storageYear);
                            
                        }
                        $storageYear=array_unique($storageYear);
                        $StorageYear = $obj->toIndexArr($storageYear);
                        //var_dump($StorageYear);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data,"StorageYear"=>$StorageYear);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    } else {
                        $sqlwhere = "";
                        //var_dump(111);
                        foreach ($ArrivalTime as $key => $value) {
                            $sqlwhere .= "Batch.StorageTime between UNIX_TIMESTAMP('$value-01-01 00:00:00') and UNIX_TIMESTAMP('$value-12-31 00:00:00') or ";
                        }
                        $sqlwhere = rtrim($sqlwhere, "or ");
                        $sql = "select * from Batch where "."($sqlwhere)";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $row['StorageTime'] = date('Y年m月d日',$row['StorageTime']);
                            $Array_data[] = $row;

                        }

                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data,);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }

                }
                else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $Data = json_encode($data);
                    echo $Data;
                }
            

        }else {
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
//$UserName = 'chenkai';
$TokenID = $deal_data['TokenID'];
//$TokenID = '14919029824143934541';
$TypeData =$deal_data['TypeData'];
//$TypeData = array("ArrivalTime"=>[2017,2015]);
get_batch_list($UserName,$TokenID,$TypeData);
?>

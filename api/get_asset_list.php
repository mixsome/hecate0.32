<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 9:46
 */

require 'mon.php';




function get_asset_list($UserName,$TokenID,$TypeData)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'AssetsSelect';   //SecretSelect    

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                $AssetsTypeID = $TypeData['AssetsTypeID'];
                $ArrivalTime = $TypeData['ArrivalTime'];
                //var_dump($ArrivalTime);
                
                //var_dump($Array_data);
                $sql = "select * from AssetsType";
                $result = $conn->query($sql);
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $AssetsType[] = $row;
                }
                    $sql = "select StorageTime from Batch ";
                    $result = $conn->query($sql);
                    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                        $StorageTime = $row['StorageTime'];
                        $storageYear[] = date('Y',$StorageTime);
                            
                    }
                    $storageYear=array_unique($storageYear);
                    $StorageYear = $obj->toIndexArr($storageYear);
                if (empty($AssetsTypeID)) {
                    //var_dump(111);
                    if (empty($ArrivalTime)) {
                        //var_dump(222);
                        
                        //var_dump($StorageYear);
                        //var_dump($AssetsType);
                        $sql = "select Assets.SerialNumber,AssetsType.AssetsTypeName,Assets.AssetsID,Assets.AssetsTypeID,Assets.BatchID,Assets.BrandModel,Assets.Specification,Assets.Price,Assets.Position,Assets.Status,Assets.UsedBy,Assets.PutTime,Batch.StorageTime from AssetsType,Assets,Batch where AssetsType.AssetsTypeID = Assets.AssetsTypeID and Assets.BatchID = Batch.BatchID group by Assets.AssetsID";
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $row['StorageTime'] = date('Y年m月d日',$row['StorageTime']);
                            $row['PutTime'] = date('Y年m月d日',$row['PutTime']);
                            $Array_data[] = $row;

                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "AssetsType" => $AssetsType,"ArrivalTime"=>$StorageYear);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    } else {
                        $sqlwhere = "";
                        foreach ($ArrivalTime as $key => $value) {
                            $sqlwhere .= "Batch.StorageTime between UNIX_TIMESTAMP('$value-01-01 00:00:00') and UNIX_TIMESTAMP('$value-12-31 00:00:00') or ";
                        }
                        $sqlwhere = rtrim($sqlwhere, "or ");
                        $sql = "select Assets.SerialNumber,AssetsType.AssetsTypeName,Assets.AssetsID,Assets.AssetsTypeID,Assets.BatchID,Assets.BrandModel,Assets.Specification,Assets.Price,Assets.Position,Assets.Status,Assets.UsedBy,Assets.PutTime,Batch.StorageTime from AssetsType,Assets,Batch where AssetsType.AssetsTypeID = Assets.AssetsTypeID and Assets.BatchID = Batch.BatchID and "."($sqlwhere)"."group by Assets.AssetsID";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $row['StorageTime'] = date('Y年m月d日',$row['StorageTime']);
                            $row['PutTime'] = date('Y年m月d日',$row['PutTime']);
                            $Array_data[] = $row;
    						echo json_encode($Array_data);

                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "AssetsType" => $AssetsType,"ArrivalTime"=>$StorageYear);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                } else {
                    if (empty($ArrivalTime)) {
                        //var_dump(11111111111);
                        $sqlwhere = "";
                        foreach ($AssetsTypeID as $key => $value) {
                            $sqlwhere .= "AssetsType.AssetsTypeID ='$value' or ";
                        }
                        $sqlwhere = rtrim($sqlwhere, "or ");

                        $sql = "select Assets.SerialNumber,AssetsType.AssetsTypeName,Assets.AssetsID,Assets.AssetsTypeID,Assets.BatchID,Assets.BrandModel,Assets.Specification,Assets.Price,Assets.Position,Assets.Status,Assets.UsedBy,Assets.PutTime,Batch.StorageTime from AssetsType,Assets,Batch where AssetsType.AssetsTypeID = Assets.AssetsTypeID and Assets.BatchID = Batch.BatchID and "."($sqlwhere)"."group by Assets.AssetsID";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $row['StorageTime'] = date('Y年m月d日',$row['StorageTime']);
                            $row['PutTime'] = date('Y年m月d日',$row['PutTime']);
                            $Array_data[] = $row;

                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "AssetsType" => $AssetsType,"ArrivalTime"=>$StorageYear);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    } else {
                        $sqlwhere_assetstype = "";
                        $sqlwhere_arrivaltime = "";
                        foreach ($AssetsTypeID as $key => $value) {
                            $sqlwhere_assetstype .= "AssetsType.AssetsTypeID ='$value' or ";
                        }
                        $sqlwhere_assetstype= rtrim($sqlwhere_assetstype, "or ");
                        foreach ($ArrivalTime as $key => $value) {
                            $sqlwhere_arrivaltime .= "Batch.StorageTime between UNIX_TIMESTAMP('$value-01-01 00:00:00') and UNIX_TIMESTAMP('$value-12-31 00:00:00') or ";
                        }
                        $sqlwhere_arrivaltime= rtrim($sqlwhere_arrivaltime, "or ");
                        $sqlwhere = "($sqlwhere_assetstype)"." and "."($sqlwhere_arrivaltime)";
                        $sql = "select Assets.SerialNumber,AssetsType.AssetsTypeName,Assets.AssetsID,Assets.AssetsTypeID,Assets.BatchID,Assets.BrandModel,Assets.Specification,Assets.Price,Assets.Position,Assets.Status,Assets.UsedBy,Assets.PutTime,Batch.StorageTime from AssetsType,Assets,Batch where AssetsType.AssetsTypeID = Assets.AssetsTypeID and Assets.BatchID = Batch.BatchID and "."$sqlwhere"." group by Assets.AssetsID";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $row['StorageTime'] = date('Y年m月d日',$row['StorageTime']);
                            $row['PutTime'] = date('Y年m月d日',$row['PutTime']);
                            $Array_data[] = $row;

                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "AssetsType" => $AssetsType,"ArrivalTime"=>$StorageYear);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                }
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
//$UserName =chenkai;
$TokenID = $deal_data['TokenID'];
//$TokenID = '14919773756391668481';
$TypeData =$deal_data['TypeData'];
//$TypeData = array("ArrivalTime"=>[2017,2014]);
//var_dump($TypeData);
get_asset_list($UserName,$TokenID,$TypeData);
?>

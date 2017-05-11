<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/29
 * Time: 17:48
 */

require 'mon.php';




function asset_release_search($UserName,$TokenID,$KeyWord)
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
                foreach ($KeyWord as $key=>$value){
                    $SearchKey = $key;
                    $SearchValue = $value;
                }
                //var_dump($SearchValue);
                //var_dump($SearchKey);

                


                    //设备资产号
                    if ($SearchKey == 'AssetsID'){
                        $sql = "select * from Assets,AssetsType where Assets.AssetsTypeID = AssetsType.AssetsTypeID and AssetsID= '$SearchValue'";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Assets[]= $row;
                        }
                        $Assets = $obj->nonull($Assets);
                        $sql = "select * from AssetsChangeLog where AssetsID = '$SearchValue'";
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            
                            $UserID = $row['UserID'];
                            $sql1 = "select TrueName from Users where UserID = '$UserID'";
                            $result1 = $conn->query($sql1);
                            $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                            $TrueName= $row1['TrueName'];
                            $row['TrueName']=$TrueName;
                            $AssetsChangeLog[]= $row;
                        }
                        
                        $AssetsChangeLog = $obj->nonull($AssetsChangeLog);
                        
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Assets" => $Assets,"AssetsChangeLog" => $AssetsChangeLog);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();

                        }
                        //批次编号
                    elseif($SearchKey == 'BatchID'){
                        $sql = "select * from Assets where BatchID = '$SearchValue'";
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Assets[]= $row;
                        }
                        $Assets = $obj->nonull($Assets);
                        $sql = "select * from Batch where BatchID = '$SearchValue'";
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Batch[]= $row;
                        }
                        $Batch= $obj->nonull($Batch);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Batch" => $Batch,"Assets" => $Assets);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                    elseif($SearchKey == 'TrueName'){
                        // $sql = "select UserID from Users where TrueName = '$SearchValue";
                        // $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
                        // $UserID = $row['UserID'];
                        $sql = "select UserID from Users where TrueName = '$SearchValue'";
                        //var_dump($sql);
                        //var_dump(1111);

                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $User[]= $row;
                        }
                        
                        $User = $obj->nonull($User);
                        
                        $sql = "select Assets.UsedBy,AssetsType.AssetsTypeName,Assets.AssetsID,AssetsChangeLog.StartTime from Assets,AssetsType,AssetsChangeLog where Assets.UsedBy = '$SearchValue' and AssetsType.AssetsTypeID = Assets.AssetsTypeID and AssetsChangeLog.AssetsID = Assets.AssetsID group by Assets.AssetsID";
                        //var_dump(1111);
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Assets[]= $row;
                        }
                        $Assets = $obj->nonull($Assets);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "User"=>$User, "Assets" => $Assets);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                    


                // } else {
                //     $sql = "select * from AssetsChangeLog where '$SearchKey' = '$SearchValue'";
                //     $result = $conn->query($sql);
                //     while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                //         $AssetsChangeLog[]= $row;
                //     }
                    
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
$TokenID = $deal_data['TokenID'];
$KeyWord =$deal_data['KeyWord'];
// $UserName = 'chenkai';
// $TokenID = '14919773756391668481';
// $KeyWord = array("TrueName"=>'陈凯');

asset_release_search($UserName,$TokenID,$KeyWord);
?>

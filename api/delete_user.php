<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/28
 * Time: 10:59
 */
require 'mon.php';




function delete_user($UserName,$TokenID,$Array_UserID)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'UsersDelete';

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');

                foreach ($Array_UserID as $key => $UserID) {
                    //var_dump($UserID);
                
                    $sql1 = "select TrueName from Users where UserID = '$UserID'";
                    $result1 = $conn->query($sql1);
                    $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                    $TrueName = $row1['TrueName'];

                    $sql2 = "select Status from Assets where Usedby = '$TrueName'";
                    $result2 = $conn->query($sql2);
                    while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
                        $Asset_Status = $row2['Status'];
                        if($Asset_Status=='启用'){
                            $conn->close();
                            $Status = '403';
                            $data = array("Status" => $Status);
                            $Data = json_encode($data);
                            echo $Data;
                        }
                    }
                    $sql = "update SecretInfo set WorkingStatus= '3' where UserID='$UserID'";
                    $conn->query($sql);
    				$conn->commit();
                    
                }
                    


                    
                

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
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$Array_UserID = $deal_data['UserID'];
//var_dump($Array_UserID);
delete_user($UserName,$TokenID,$Array_UserID);
?>
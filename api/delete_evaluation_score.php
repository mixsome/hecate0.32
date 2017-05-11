<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 13:28
 */

require 'mon.php';

function delete_evaluation_score($UserName,$TokenID,$EvaluationID,$ExamineeID)
{

    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {

        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            //var_dump('aaa');
            $obj->operation_time($userid);

            $conn = $obj->db_connect();


                $sql = "update EvaluationInfo set IndicatorScore = NULL where EvaluationID ='$EvaluationID' and ExamineeID= '$ExamineeID'";
                //var_dump($sql);
                $conn->query($sql);
                $conn->commit();
                $sql = "update EvaluationState set SumScore = 0 , DetailScore =NULL, ExaminedNum=0  where EvaluationID ='$EvaluationID' and ExamineeID= '$ExamineeID'";
                //var_dump($sql);
                $conn->query($sql);
                $conn->commit();

            $conn->close();
            $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);


            $Data = json_encode($data);
            echo $Data;
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
$EvaluationID = $deal_data['EvaluationID'];
$ExamineeID = $deal_data['ExamineeID'];
//var_dump($ExamineeID);
delete_evaluation_score($UserName,$TokenID,$EvaluationID,$ExamineeID);
?>
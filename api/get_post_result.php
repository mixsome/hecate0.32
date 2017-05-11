<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 9:35
 */

require 'mon.php';



function get_post_result($UserName,$TokenID,$EvaluationID)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $Ask = 'Evaluation';     

            $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
            if($Permission == 'Y'){
            //var_dump(123123);
                $obj->operation_time($userid);

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');

                $sql = "select EvaluationName from EvaluationType where EvaluationID = '$EvaluationID' ";
                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $EvaluationName = $row['EvaluationName'];
               // var_dump($EvaluationName);
                $sql = "select ExaminerID from EvaluationInfo where EvaluationID = '$EvaluationID' group by ExaminerID";
                $result = $conn->query($sql);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $ExaminerIDs[] = $row['ExaminerID'];
                }
                //var_dump($ExaminerIDs);

                foreach ($ExaminerIDs as $key =>$value){
                    $ExamineeIDs = [];
                    $ExamineeName = [];
                    $sql = "select TrueName from Users where UserID = '$value'";
                    $result1 = $conn->query($sql);
                    $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                    $ExaminerName = $row1['TrueName'];
                    //var_dump($ExaminerName);


                    $sql = "select ExamineeID,SendMailTime from EvaluationInfo where EvaluationID = '$EvaluationID' and ExaminerID = '$value'";
                    //var_dump($sql);

                    $result2 = $conn->query($sql);
                    while($row2=$result2->fetch_array(MYSQLI_ASSOC)){

                        $ExamineeID = $row2['ExamineeID'];
                        //var_dump($ExamineeID);
                        $ExamineeIDs[] = $row2['ExamineeID'];
                        $SendMailTime = $row2['SendMailTime'];
                        //var_dump($SendMailTime);
                        $sql = "select TrueName from Users where UserID = '$ExamineeID' ";
                        $result3 = $conn->query($sql);
                        $row3 = $result3->fetch_array(MYSQLI_ASSOC);
                        $ExamineeName[] = $row3['TrueName'];
                        //var_dump($ExamineeName);


                    }
                    $Examiner[] = array("ExaminerID"=>$value,"ExaminerName"=>$ExaminerName,"ExamineeID"=>$ExamineeIDs,"ExamineeName"=>$ExamineeName,"SendMailTime" =>$SendMailTime);
                    //var_dump($Examiner);

                }
                $conn->close();
                $Array_data = array("EvaluationID"=>$EvaluationID,"EvaluationName"=>$EvaluationName,"Examiner"=>$Examiner);

                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data);


                $Data = json_encode($data);
                echo $Data;
            }
            else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $data = json_encode($data);
                    echo $data;
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
$req_data = $_GET['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$EvaluationID = $deal_data['EvaluationID'];

// $UserName = 'chenkai';
// $TokenID = '14924796096267578914';
// $EvaluationID = 1;

get_post_result($UserName,$TokenID,$EvaluationID);
?>



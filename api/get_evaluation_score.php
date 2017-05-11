<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 11:17
 */
require 'mon.php';



function get_evaluation_score($UserName,$TokenID,$EvaluationID)
{
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
                    $obj->operation_time($userid);

                    $conn = $obj->db_connect();
                    $conn->query('SET NAMES UTF8');

                    $sql = "select EvaluationName from EvaluationType where EvaluationID = '$EvaluationID' ";
                    $result = $conn->query($sql);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $EvaluationName = $row['EvaluationName'];
                    //var_dump($EvaluationName);
                    $sql = "select * from EvaluationState where EvaluationID = '$EvaluationID' ";
                    //var_dump($sql);
                    $result = $conn->query($sql);
                    
                    while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        $a[] =$row;

                        //var_dump($row);
                        $ExamineeID = $row['ExamineeID'];
                        $sql5 = "select TrueName from Users where UserID = '$ExamineeID'";

                        $result5 = $conn->query($sql5);
                        $row5 = $result5->fetch_array(MYSQLI_ASSOC);
                        $ExamineeName = $row5['TrueName'];
                        //var_dump($ExamineeName);
                        $sql6 = "select ExaminerID,IndicatorScore from EvaluationInfo where EvaluationID = '$EvaluationID' and ExamineeID='$ExamineeID'";
                        //var_dump($sql6);

                        $result6 = $conn->query($sql6);
                        $NoScoreName = [];
                        while($row6 = $result6->fetch_array(MYSQLI_ASSOC)){
                            
                            if(empty($row6['IndicatorScore'])){
                                $ExaminerID = $row6['ExaminerID'];
                                $sql7 = "select TrueName from Users where UserID = '$ExaminerID'";

                                $result7 = $conn->query($sql7);
                                $row7 = $result7->fetch_array(MYSQLI_ASSOC);
                                $NoScoreName[] = $row7['TrueName'];
                            }
                        }

                        //var_dump($ExaminerName);
                        $ExaminedNum = $row['ExaminedNum'];      //已评人个数
                        $DetailScore = $row['DetailScore'];
                        
                        $DetailScore = json_decode($DetailScore,true);     //个人总评分
                        //var_dump($DetailScore);
                        $SumScore = $row['SumScore'];          //总分
                           //考评人个数
                        $sql1 = "select count(ExaminerID) from EvaluationInfo where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID' ";

                        $result1 = $conn->query($sql1);
                        $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                        $ExaminerSum = $row1['count(ExaminerID)'];
                        //var_dump($ExaminerSum);

                        $sql2 = "update EvaluationState set ExaminerSum = '$ExaminerSum' where EvaluationID = '$EvaluationID' and  ExamineeID = '$ExamineeID'";
                        $conn->query($sql2);
                        $conn->commit(); 
                        
                        
                        $DetailName = [];
                        $Score = [];
                        foreach ($DetailScore as $key => $value) {

                            $sql3 = "select TrueName from Users where UserID = '$key' ";

                            $result3 = $conn->query($sql3);
                            $row3 = $result3->fetch_array(MYSQLI_ASSOC);
                            $ExaminerName = $row3['TrueName'];

                            $DetailName['ExaminerName'] = $ExaminerName;
                            $DetailName['Score'] = $value;
                            $Score[] = $DetailName;

                        }

                        //var_dump($Score);

                        $Examinee[] = array("ExamineeID" => $ExamineeID, "ExamineeName"=>$ExamineeName,"NoScoreName"=>$NoScoreName,"ExaminerSum" => $ExaminerSum, "ExaminedNum" => $ExaminedNum, "DetailScore" => $Score,"SumScore"=>$SumScore);
                        //var_dump($Examinee);
                    }
                    //var_dump($a);
                    //var_dump($Examinee);
                    $Array_data = array("EvaluationID" => $EvaluationID, "EvaluationName" => $EvaluationName, "Examinee" => $Examinee);
                    $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data);


                    $Data = json_encode($data);
                    echo $Data;
                    $conn->close();
                }
                else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $data = json_encode($data);
                    echo $data;
                } 

//

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
}
$req_data = $_GET['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$EvaluationID = $deal_data['EvaluationID'];



// $UserName = 'chenkai';
// $TokenID = '14924796096267578914';
// $EvaluationID = 1;


get_evaluation_score($UserName,$TokenID,$EvaluationID);
?>
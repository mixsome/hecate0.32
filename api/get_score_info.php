<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 18:37
 */

require 'mon.php';



function get_score_info($UserName,$TokenID,$EvaluationID,$ExamineeID)
{
    {
        $obj = new serverinfo();

        $userid = $obj->get_userid($UserName);
        $Status = $obj->check_tokenid($userid, $TokenID);
        if ($Status == '0') {
            $Status = $obj->check_timeout($userid);
            if ($Status == '0') {
                $obj->operation_time($userid);
                //var_dump(213123);
                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');

                $sql = "select Template.IndicatorAndWeight,EvaluationType.EvaluationName from EvaluationType,Template where EvaluationID = '$EvaluationID' and EvaluationType.TemplateID = Template.TemplateID";
                //var_dump($sql);
                $result = $conn->query($sql);

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $EvaluationName = $row['EvaluationName'];
                $IndicatorAndWeight = $row['IndicatorAndWeight'];
                //var_dump($IndicatorAndWeight);
                //json解析
                $IndicatorAndWeight = json_decode($IndicatorAndWeight,true);
                foreach ($IndicatorAndWeight as $key => $value) {
                    $IndicatorName[] = $value['IndicatorName'];
                }
                //var_dump($Indicator);
                //var_dump($EvaluationName);
                $sql = "select TrueName from Users where UserID = '$ExamineeID'";
                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $ExamineeName = $row['TrueName'];
                //var_dump($ExamineeName);
                $sql = "select SumScore,DetailScore from EvaluationState where EvaluationID = '$EvaluationID' and ExamineeID='$ExamineeID'";
                //var_dump($sql);
                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);

                $SumScore = $row['SumScore'];          //总分
                $DetailScore = $row['DetailScore'];
                $DetailScore = json_decode($DetailScore,true);
                //var_dump($DetailScore);


                $sql = "select IndicatorScore,ExaminerID,IsMaster from EvaluationInfo  where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID'";
                //var_dump($sql);
                $result = $conn->query($sql);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    //$IndicatorName = [];
                    $Score = [];


                    $IndicatorScore = $row['IndicatorScore'];
                    $IndicatorScore = json_decode($IndicatorScore,true);
                    //var_dump($IndicatorScore);
                    if(empty($IndicatorScore)){
                        //var_dump(3333);
                        foreach ($IndicatorName as $key => $value) {
                            
                            
                            $Score[] = ''; 
                        }
                    }

                    else{
                        //遍历数组更改指标顺序
                        $Sort = $IndicatorScore['Sort'];
                        $Sort = explode(",", $Sort);
                        foreach ($Sort as $key => $value) {
                            foreach ($IndicatorScore as $k => $v) {
                                if ($value == $k) {
                                    $Indicator[$value] = $v;
                                }
                            }
                        }
                    
                        foreach ($Indicator as $key => $value) {
                            
                            $Score[] = $value;
                        }
                    }
                    //var_dump($Score);
                    $ExaminerID = $row['ExaminerID'];
                    $IsMaster = $row['IsMaster'];
                    //var_dump($IsMaster);
                    //$MasterAverageScore = [];
                    //$MasterScore = [];
                    //$WorkerAverageScore = [];
                    //$WorkerScore = [];

                    if ($IsMaster == 'Y'){
                        $sql1 = "select TrueName from Users where UserID = '$ExaminerID'";
                        $result1 = $conn->query($sql1);
                        $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                        $ExaminerName = $row1['TrueName'];
                        //var_dump($ExaminerName);
                        $MasterAverageScore[] = $DetailScore[$ExaminerID];
                        //var_dump($MasterAverageScore);
                        $MasterScore[] = array("ExaminerName"=>$ExaminerName,"IsMster"=>"Y","Score"=>$Score);
                        //var_dump($Score);
                        
                    }
                    
                    elseif($IsMaster == 'N'){
                        $sql2 = "select TrueName from Users where UserID = '$ExaminerID'";
                        $result2 = $conn->query($sql2);
                        $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                        $ExaminerName = $row2['TrueName'];
                        //var_dump($ExaminerName);
                        $WorkerAverageScore[]  = $DetailScore[$ExaminerID];
                        $WorkerScore[] = array("ExaminerName"=>$ExaminerName,"IsMster"=>"N","Score"=>$Score);
                    }

                }
                
                //var_dump($MasterScore);
                //var_dump($WorkerScore);
                
                $MasterAverageScore = array_sum($MasterAverageScore)/count($MasterAverageScore);
                $WorkerAverageScore = array_sum($WorkerAverageScore)/count($MasterAverageScore);
                




                $Array_data = array("EvaluationID" => $EvaluationID, "EvaluationName" => $EvaluationName,"IndicatorAndWeight"=>$IndicatorAndWeight,"ExamineeID" => $ExamineeID,"ExamineeName"=>$ExamineeName,"SumScore"=>$SumScore,"MasterScore"=>$MasterScore,"WorkerScore"=>$WorkerScore,"MasterAverageScore"=>$MasterAverageScore,"WorkerAverageScore"=>$WorkerAverageScore);
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data);


                $Data = json_encode($data);
                echo $Data;
                $conn->close();


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
$ExamineeID = $deal_data['ExamineeID'];


// $UserName = 'chenkai';
// $TokenID = '14925008819829742340';
// $EvaluationID = 1;
// $ExamineeID = 4;

get_score_info($UserName,$TokenID,$EvaluationID,$ExamineeID);
?>
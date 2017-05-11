<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 11:30
 */

require 'mon.php';


function mark_evaluation($ExamineeID,$TokenID,$IndicatorScore,$IndicatorName,$AverageScore)
{

    $obj = new serverinfo();
    $Status = '0';
    $conn = $obj->db_connect();
    $conn->query('SET NAMES UTF8');
    $sql = "select EvaluationID,ExaminerID from MailInfo where TokenID='$TokenID'";
    //var_dump($sql);
    $result = $conn->query($sql);
    if($result==null){
        $conn->close();
        $Status = '201';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;
    }
    else {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $EvaluationID = $row['EvaluationID'];
        $ExaminerID = $row['ExaminerID'];
        $sql = "select EvaluationType.Deadline,Template.MasterSumWeight from EvaluationType,Template where EvaluationID ='$EvaluationID' and Template.TemplateID = EvaluationType.TemplateID";
        //var_dump($sql);
        $result = $conn->query($sql);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $Deadline = $row['Deadline'];
        $MasterSumWeight = $row['MasterSumWeight'];
        //var_dump($MasterSumWeight);
        $nowTime = $obj->timestamp();
        if ($nowTime > $Deadline) {
            $conn->close();
            $Status = '202';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;
        } else {
            //var_dump(23123);
            for ($i=0; $i < count($IndicatorName); $i++) { 
                // for ($j=0; $j < count($IndicatorScore) ; $j++) { 
                    // var_dump($IndicatorName[$i]);
                    // var_dump($IndicatorScore[$i]);
                    $NewIndicatorScore[$IndicatorName[$i]] = $IndicatorScore[$i];
                // }
            }

            //var_dump($NewIndicatorScore);
            //增加“Sort”用于存储指标顺序;
            foreach ($NewIndicatorScore as $key => $value) {
                $Sort.="$key,";
            }
            $Sort = rtrim($Sort, ",");
            //var_dump($Sort);
            $NewIndicatorScore['Sort']=$Sort;
            //var_dump($NewIndicatorScore);
            $NewIndicatorScore = json_encode($NewIndicatorScore,JSON_UNESCAPED_UNICODE);
            //var_dump($NewIndicatorScore);

            $sql = "update EvaluationInfo set IndicatorScore = '$NewIndicatorScore' where EvaluationID = '$EvaluationID' and ExaminerID='$ExaminerID' and ExamineeID = '$ExamineeID' ";
            //var_dump($sql);
            $conn->query($sql);
            $conn->commit();

            $sql = "select DetailScore,ExaminedNum from EvaluationState where EvaluationID = '$EvaluationID' and ExamineeID='$ExamineeID'";
            //var_dump($sql);
            $result = $conn->query($sql);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $DetailScore = $row['DetailScore'];
            //var_dump($DetailScore);
            $ExaminedNum = $row['ExaminedNum'];
            //var_dump($DetailScore);

            //json解析
            $DetailScore = json_decode($DetailScore, true);
            $DetailScore[$ExaminerID] = $AverageScore * 20;
            //var_dump($DetailScore);
            $ExaminedNum = count($DetailScore);
            //var_dump($ExaminedNum);
            $DetailScore = json_encode($DetailScore);
            //var_dump($DetailScore);
            
            
            $sql = "update EvaluationState set DetailScore = '$DetailScore',ExaminedNum = '$ExaminedNum' where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID' ";
            //var_dump($sql);
            $conn->query($sql);
            $conn->commit();
            $sql = "select DetailScore from EvaluationState where EvaluationID = '$EvaluationID' and ExamineeID='$ExamineeID'";
            $result = $conn->query($sql);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $DetailScore = $row['DetailScore'];
            //json解析
            $DetailScore = json_decode($DetailScore, true);
            //var_dump($DetailScore);

            $MasterScore = floatval(0);
            $WorkerScore = floatval(0);
            $MasterCount = floatval(0);
            $WorkerCount = floatval(0);
            foreach ($DetailScore as $key => $value) {
                $value = floatval($value);
                //var_dump($value);
                //var_dump($value);
                $sql = "select IsMaster from EvaluationInfo where EvaluationID = '$EvaluationID' and ExaminerID='$ExaminerID' and ExaminerID = '$key'";
                //var_dump($sql);
                $result = $conn->query($sql);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $IsMaster = $row['IsMaster'];
                //var_dump($IsMaster);

                if ($IsMaster == 'Y') {
                    $MasterScore = $MasterScore + $value;
                    //var_dump($MasterScore);
                    $MasterCount++;
                } elseif ($IsMaster == 'N') {
                    $WorkerScore = $WorkerScore + $value;
                    //var_dump($WorkerScore);
                    $WorkerCount++;
                }
            }
            if($MasterCount==0){
                $MasterScore = floatval(0);
            }
            else{
                $MasterScore = ($MasterScore / $MasterCount) * $MasterSumWeight ;
                //$MasterScore = floatval($MasterScore);
                //var_dump(11111);
                //var_dump($MasterScore);
            }
            if ($WorkerCount==0) {
                $WorkerScore = floatval(0);
            }
            else{
                $WorkerScore = ($WorkerScore / $WorkerCount) * (1 - $MasterSumWeight);
                //var_dump(2222);
                //var_dump($WorkerScore);
            }
            // var_dump($MasterScore);
            // var_dump($WorkerScore);
            // var_dump($MasterCount);
            // var_dump($WorkerCount);
            
            $New_SumScore =  $MasterScore+$WorkerScore;
            //var_dump($New_SumScore);
            $sql = "update EvaluationState set SumScore = '$New_SumScore' where EvaluationID = '$EvaluationID' and ExamineeID = '$ExamineeID' ";
            $conn->query($sql);
            $conn->commit();
        }


        $data = array("TokenID" => $TokenID, "Status" => $Status);


        $Data = json_encode($data);
        echo $Data;
        $conn->close();
    }



}
//var_dump(23123);
//var_dump($_POST['data']);
$req_data = $_POST['data'];
//var_dump($req_data);
$deal_data = json_decode($req_data, true);

$TokenID = $deal_data['TokenID'];
$ExamineeID = $deal_data['ExamineeID'];
$IndicatorScore = $deal_data['IndicatorScore'];
$IndicatorName = $deal_data['IndicatorName'];
    
$AverageScore = $deal_data['AverageScore'];


    // $TokenID = '14933470803874536669';
    // $ExamineeID = 17;
    // $IndicatorScore = [4,''];
    // $AverageScore = 1.6;
    // $IndicatorName = ["工作态度","工作完成度"];
    // var_dump(3123);
    // var_dump($IndicatorScore);
    // var_dump($IndicatorName);
mark_evaluation($ExamineeID,$TokenID,$IndicatorScore,$IndicatorName,$AverageScore); 
?>
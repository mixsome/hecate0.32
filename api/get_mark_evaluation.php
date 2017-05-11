<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/30
 * Time: 11:30
 */


require 'mon.php';



function get_mark_evaluation($TokenID)
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
        else{
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $EvaluationID = $row['EvaluationID'];
            $ExaminerID = $row['ExaminerID'];
            $sql = "select Deadline from EvaluationType where EvaluationID ='$EvaluationID'";
            $result = $conn->query($sql);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $Deadline = $row['Deadline'];
            $nowTime = $obj->timestamp();
            if($nowTime >Deadline){
                $conn->close();
                $Status = '202';
                $data = array("Status" => $Status);
                $Data = json_encode($data);
                echo $Data;
            }
            else{
                $sql = "select Template.IndicatorAndWeight from EvaluationType,Template where EvaluationID = '$EvaluationID' and EvaluationType.TemplateID = Template.TemplateID";
                //var_dump($sql);
                $result = $conn->query($sql);

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $IndicatorAndWeight = $row['IndicatorAndWeight'];
                //json解析
                $IndicatorAndWeight = json_decode($IndicatorAndWeight,true);
                //var_dump($IndicatorAndWeight);
                //$IndicatorSum=0;
                $NoScoreCount=0;
                $HasScoreCount=0;
                $AlreadyScoreCount=0;

                
                $IndicatorSum = count($IndicatorAndWeight);

                $sql = "select ExamineeID,IsMaster from EvaluationInfo where EvaluationID = '$EvaluationID' and ExaminerID = '$ExaminerID'";
                //var_dump($sql);
                $result = $conn->query($sql);

                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $ExamineeID =$row['ExamineeID'];
                    //var_dump($ExamineeID);
                    $ExamineeIDs[] = $row['ExamineeID'];
                    $IsMaster = $row['IsMaster'];
                
                    $sql1 = "select TrueName from Users where UserID = '$ExamineeID'";
                    $result1 = $conn->query($sql1);
                    $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                    $ExamineeName = $row1['TrueName'];
                    $sql2 = "select IndicatorScore from EvaluationInfo where EvaluationID = '$EvaluationID' and ExaminerID='$ExaminerID' and ExamineeID = '$ExamineeID'";
                    //var_dump($sql2);
                    $result2 = $conn->query($sql2);
                    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                    $IndicatorScore =$row2['IndicatorScore'];

                    if($IndicatorScore==null){
                        $Score = [];
                        $NoScoreCount++;
                    }
                    else{
                        
                        $Score= [];
                                    //json解析
                        $IndicatorScore = json_decode($IndicatorScore,true);
                                    //var_dump(count($IndicatorScore));
                        //增加“Sort”存储数组顺序
                        $Sort = $IndicatorScore['Sort'];
                        $Sort = explode(",", $Sort);
                        foreach ($Sort as $key => $value) {
                            foreach ($IndicatorScore as $k => $v) {
                                if ($value == $k) {
                                    $Indicator[$value] = $v;
                                }
                            }
                        }
                        //var_dump($Indicator);
                        foreach ($Indicator as $key => $value) {
                            $Score[] = $value;
                        }
                        $count = 0;
                        foreach ($Indicator as $key=>$value){
                            if($value==''){
                                $count++;
                            }
                        }
                        
                        if ($count == $IndicatorSum){
                            $NoScoreCount++;
                        }
                        elseif($count>0 and $count < $IndicatorSum){
                            $HasScoreCount++;
                        }
                        elseif($count == 0){
                            $AlreadyScoreCount++;
                        }
                    }
                        $Array_data[] = array("ExamineeID"=>$ExamineeID,"IsMaster"=>$IsMaster,"ExamineeName"=>$ExamineeName,"IndicatorScore"=>$Score);
                    
                }   
                $Allcount = count($ExamineeIDs);         //需评价总人数


                $data = array("TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data,"ExaminerID" => $ExaminerID,"Allcount"=>$Allcount, "AlreadyScoreCount" => $AlreadyScoreCount,"NoScoreCount"=>$NoScoreCount,"HasScoreCount"=>$HasScoreCount,"IndicatorAndWeight"=>$IndicatorAndWeight,"Data"=>$Array_data);

                $Data = json_encode($data);
                echo $Data;
                $conn->close();

            }
        }

        
                

        

 
    
}
$req_data = $_GET['data'];
//var_dump($req_data);
$deal_data = json_decode($req_data, true);

$TokenID = $deal_data['TokenID'];



// $TokenID = '89709980';
//var_dump($TokenID);

get_mark_evaluation($TokenID);
?>
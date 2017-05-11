<?php
require 'mon.php'; 


function get_evaluation_result($TokenID,$UserName,$EvaluationID){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');


     $userid = $obj->get_userid($UserName);
     $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                $Ask = 'Evaluation';     

                $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
                if($Permission == 'Y'){
                    $Status = '0';
                    $sql = "select EvaluationType.EvaluationName,Template.MasterSumWeight,Template.IndicatorAndWeight from EvaluationType,Template where EvaluationType.EvaluationID = '$EvaluationID' and EvaluationType.TemplateID = Template.TemplateID";
                    $result = $conn->query($sql);
                    $row = $result->fetch_array();
                    $EvaluationName = $row['EvaluationName'];
                    $TemplateID = $row['TemplateID '];
                    $MasterSumWeight = $row['MasterSumWeight'];
                    $IndicatorAndWeight = $row['IndicatorAndWeight'];
                    $IndicatorAndWeight = json_decode($IndicatorAndWeight);
                    $WorkerSumWeight = 1 - $MasterSumWeight;
                    //$WorkerSumWeight = (string)($WorkerSumWeight);
                    //var_dump($MasterSumWeight);

                    //人员信息
                    $sql = "select ExamineeID from EvaluationInfo where EvaluationID = '$EvaluationID'";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_array()){
                        $ExamineeIDs[] = $row['ExamineeID'];
                    }
                    $ExamineeIDs = array_unique($ExamineeIDs);
                    //var_dump($ExamineeIDs);

                    foreach ($ExamineeIDs as $key => $value) {
                        $MasterName = [];
                        $WorkerName = [];
                        $sql = "select TrueName from Users where UserID = '$value'";
                        $result2 = $conn->query($sql);
                        $row2 = $result2->fetch_array();
                        $ExamineeName = $row2['TrueName'];
                        $sql = "select ExaminerID,IsMaster,AddTime from EvaluationInfo where EvaluationID = '$EvaluationID' and ExamineeID = '$value'";
                        //var_dump($sql);
                        $result1 = $conn->query($sql);
                        while($row1 = $result1->fetch_array()){
                            $IsMaster = $row1['IsMaster'];
                            //var_dump($IsMaster);
                            $ExaminerID = $row1['ExaminerID'];

                            $AddTime = $row1['AddTime'];
                            
                            $sql = "select TrueName from Users where UserID = '$ExaminerID'";
                            $result3 = $conn->query($sql);
                            $row3 = $result3->fetch_array();
                            if ($IsMaster == "Y"){
                                $MasterName[] = $row3['TrueName'];
                            }
                            elseif($IsMaster =="N"){
                                $WorkerName[] = $row3['TrueName'];
                            }

                    }
                    $ExamineeIDs = $obj->nonull($ExamineeIDs);
                    $ExamineeName = $obj->nonull($ExamineeName);
                    $IsMaster = $obj->nonull($IsMaster);
                    $ExaminerID = $obj->nonull($ExaminerID);
                    $AddTime = $obj->nonull($AddTime);
                    $MasterName = $obj->nonull($MasterName);
                    $WorkerName = $obj->nonull($WorkerName);

                    $Array_data[] = array("ExamineeID"=>$value,"ExamineeName"=>$ExamineeName,"MasterName"=>$MasterName,"WorkerName"=>$WorkerName,"AddTime"=>$AddTime);   
                    }
                    $conn->close();
                    $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "EvaluationID"=>$EvaluationID,"EvaluationName"=>$EvaluationName,"MasterSumWeight"=>$MasterSumWeight,"WorkerSumWeight"=>$WorkerSumWeight,"IndicatorAndWeight"=>$IndicatorAndWeight,"Data" => $Array_data);


                    $Data = json_encode($data);
                    echo $Data;
                }
                else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $data = json_encode($data);
                    echo $data;
                } 
                              
            }   
            else{
               $Status='104';
               echo json_encode($Status);
            }
        }
        else{        
           $Status = '103';
           echo json_encode($Status);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_GET['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$EvaluationID = $arr["EvaluationID"];

// $UserName = 'chenkai';
// $TokenID = '14924204009652880633';
// $EvaluationID = 1;

get_evaluation_result($TokenID,$UserName,$EvaluationID);

?>

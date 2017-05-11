<?php



require 'mon.php';

function get_evaluation_list($TokenID,$UserName){
     $obj = new serverinfo();
     
     //var_dump(111);
     $userid = $obj->get_userid($UserName);
        $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            //var_dump(555);
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){

                $Ask = 'Evaluation';     

                $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
                if($Permission == 'Y'){

                    $conn = $obj->db_connect();
                    $conn->query('SET NAMES UTF8');
                    $Status = '0';
                    $sql = "select * from EvaluationType";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //var_dump(223123);
                        $TemplateID = $row['TemplateID'];
                        $EvaluationID = $row['EvaluationID'];

                        $StartTime = $row['StartTime'];

                        $Deadline = $row['Deadline'];
                        $EvaluationStatus = $row['EvaluationStatus'];
                        //var_dump($EvaluationStatus);

                        $nowtime = $obj->timestamp();


                        //var_dump($nowtime);
                        if($EvaluationStatus == 3){
                            $row['EvaluationStatus'] = '已归档';
                        }
                        elseif ($nowtime < $StartTime){
                            $row['EvaluationStatus'] = '未开始';
                            $sql = "update EvaluationType set EvaluationStatus = '0' where EvaluationID = '$EvaluationID'";
                            $conn->query($sql); 
                            $conn->commit();
                        }
                        elseif ($StartTime < $nowtime and $nowtime < $Deadline){

                            $row['EvaluationStatus'] = '考核中';
                            $sql = "update EvaluationType set EvaluationStatus = '1' where EvaluationID = '$EvaluationID'";
                            $conn->query($sql); 
                            $conn->commit();
                        }
                        elseif ($nowtime > $Deadline){
                            $row['EvaluationStatus'] = '已结束';
                            $sql = "update EvaluationType set EvaluationStatus = '2' where EvaluationID = '$EvaluationID'";
                            $conn->query($sql); 
                            $conn->commit();
                        }
                        
                        $sql = "select TemplateName from Template where TemplateID = '$TemplateID'";
                        $result1 = $conn->query($sql);
                        $row1 = $result1->fetch_row(); 
                        $TemplateName = $row1[0];                            
                            
                            
                        $sql = "select ExamineeID from EvaluationInfo where EvaluationID = '$EvaluationID'";
                        //var_dump($sql);
                        $result2 = $conn->query($sql);
                        while($row2 = $result2->fetch_array()){
                            $ExamineeIDs[] = $row2['ExamineeID'];
                            
                        }
                        $ExamineeIDs = array_unique($ExamineeIDs);
                        $SettingNum = count($ExamineeIDs);
                             
                        unset($row['TemplateID']);
                        $row['TemplateName'] = $TemplateName;
                        $row['SetttingNum'] = $SettingNum;
                        $EvaluationType[] = $row;                  
                        }
                        
                                   
                    $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>$EvaluationType);
                    echo json_encode($data);   
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
//$UserName = 'chenkai';
//$TokenID = '14921498141455623706';

get_evaluation_list($TokenID,$UserName);


?>

<?php

require 'mon.php';

function get_evaluation_template($UserName,$TokenID){
     $obj = new serverinfo();
     
     $userid = $obj->get_userid($UserName);
     //var_dump($userid);
        $flag = $obj->check_tokenid($userid,$TokenID);
        //var_dump($flag);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid); 
            //var_dump(111);              
            if($timeout_flag == '0'){

                $Ask = 'Evaluation';     

                $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
                if($Permission == 'Y'){
                    $conn = $obj->db_connect();
                    $conn->query('SET NAMES UTF8');
                    $Status = '0';
                    $sql = "select TemplateID,TemplateName,CreateTime,Remarks from Template";
                    $result = $conn->query($sql);
                    //var_dump(222);
                    while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        //var_dump($row);
                        $TemplateID = $row['TemplateID'];
                        $row['EvaluationStatus'] = '未使用';
                        //var_dump($TemplateID);
                        $sql = "select Deadline,EvaluationStatus from EvaluationType where TemplateID = '$TemplateID'";
                        $result1 = $conn->query($sql);
                        $EvaluationStatus = [];
                        while($row1 = $result1->fetch_array()){
                        
                        $EvaluationStatus[] = $row1['EvaluationStatus'];
                        //var_dump($deadline);
                        //var_dump($StartTime);
                        //$time = $obj->timestamp();
                        //var_dump($time);
                        //if ($time < $deadline and $time >$StartTime){
                            //var_dump('aaaa');
                        if (array_unique($EvaluationStatus) == [3]){
                            $row['EvaluationStatus'] = '未使用';
                        }
                        else {
                            $row['EvaluationStatus'] = '正在使用';
                        }
                        }
                        //var_dump($EvaluationStatus);
                        
                        
                        //var_dump($row['EvaluationStatus']);
                         
                        
                        //var_dump($row);
                        $Template[] = $row;

                        
                    }
                    //var_dump($Template);
                                                           
                               
                    
                        
                    
                    $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>$Template);
                    echo json_encode($data);                   
                }
                else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $Data = json_encode($data);
                    echo $Data;
                }

            }else{
               $Status='104';
			   $data = array("Status"=>$Status);
               echo json_encode($data);               
            }
        }
        else{        
           $Status = '103';
		   $data = array("Status"=>$Status);
           echo json_encode($data);              
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 

$arr = json_decode($_GET['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
//$UserName = 'chenkai';
//$TokenID = '14921610715845277535';
get_evaluation_template($UserName,$TokenID);
?>

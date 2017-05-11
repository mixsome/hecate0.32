<?php
require 'mon.php'; 
function get_template_info($TokenID,$UserName,$ID){
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
                    $sql = "select TemplateID,MasterSumWeight,TemplateName,IndicatorAndWeight from Template where TemplateID = '$ID'";
                    //var_dump($sql);
                    $result = $conn->query($sql);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $TemplateName = $row["TemplateName"];
                    $IndicatorAndWeight = $row['IndicatorAndWeight'];
                    $TemplateID = $row['TemplateID'];
                    $MasterSumWeight = $row['MasterSumWeight'];
    				$IndicatorAll = json_decode($IndicatorAndWeight,true);
                    //var_dump($IndicatorAll);								
                    $jsondata = array("TemplateName"=>$TemplateName,"IndicatorAndWeight"=>$IndicatorAll,"TemplateID"=>$TemplateID,"MasterSumWeight"=>$MasterSumWeight);            
                    $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Data"=>$jsondata);
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
               $status='104';
			   $data = array("Status"=>"$status");
               echo json_encode($data);               
            }
        }
        else{        
           $status = '103';
           $data = array("Status"=>"$status");
           echo json_encode($data);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_GET['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$ID = $arr["TemplateID"];
get_template_info($TokenID,$UserName,$ID);

?>

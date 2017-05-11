<?php


require 'mon.php'; 

function update_template_indicator($TokenID,$UserName,$Data){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');
     $userid = $obj->get_userid($UserName);
     $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            var_dump($flag);
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){
                var_dump($timeout_flag);
                $Status = '0';
                $TemplateID = $Data['TemplateID'];
                var_dump($TemplateID);
                $TemplateName = $Data['TemplateName'];
                $IndicatorAndWeight = $Data['IndicatorAndWeight'];
                $IndicatorAndWeight= json_encode($IndicatorAndWeight);
                $Data['IndicatorAndWeight'] = $IndicatorAndWeight;               
                $sql = "select Deadline from EvaluationType where TemplateID = 'TemplateID'";
                $result = $conn->query($sql);
                while($row = $result->fetch_array()){                  
                    
                        $deadline = $row['Deadline'];
                        $time = $obj->timestamp();
                        if ($time < $deadline){
                            $Status = '201';
                            $data = array("Status"=>$Status);
                            echo json_encode($data);                            
                            $obj->operation_time($userid);
                            $conn->close();                 
                            exit;
                        }
                    
                }    
                var_dump(1111111111111111111);
                foreach ($Data as $key => $value) {
                    $sql = "update Template set $key = '$value' where TemplateID = '$TemplateID'";
                    var_dump($sql);
                    $conn->query($sql);
                    $conn->commit();
                }
                
                
                                 
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                echo json_encode($data);                   
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
$Data = $arr["Data"];

// $UserName = 'chenkai';
// var_dump($UserName);
// $TokenID = '14920644872777833903';
// $Data = array("TemplateID"=>8,"TemplateName"=>"考核考核","IndicatorAndWeight"=>array(array("IndicatorName"=>"workertitule","CreateTime"=>231231),array("IndicatorName"=>"xxxxxxxx","CreateTime"=>231231)));
// var_dump($Data);
update_template_indicator($TokenID,$UserName,$Data);

?>


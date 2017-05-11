<?php



require 'mon.php';  



function add_evaluation_template($TokenID,$UserName,$Data){
     $obj = new serverinfo();
     $conn = $obj->db_connect();
     $conn->query('SET NAMES UTF8');
     //var_dump($UserName);
     $userid = $obj->get_userid($UserName);
     //var_dump($userid);
     $flag = $obj->check_tokenid($userid,$TokenID);
        if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);
            //var_dump($timeout_flag);               
            if($timeout_flag == '0'){
                $Status = '0';
				$TemplateName = $Data['TemplateName'];
                $MasterSumWeight = $Data['MasterSumWeight'];
                $IndicatorAndWeight = $Data['IndicatorAndWeight'];
                //var_dump($IndicatorAndWeight);
				$IndicatorAndWeight = json_encode($IndicatorAndWeight,JSON_UNESCAPED_UNICODE);
                //var_dump($IndicatorAndWeight);				
                $CreateTime = $obj->timestamp();                
                $sql = "insert into Template (TemplateName,IndicatorAndWeight,MasterSumWeight,CreateTime) values('$TemplateName','$IndicatorAndWeight','$MasterSumWeight','$CreateTime')";
                //var_dump($sql);
                $conn->query($sql);
				$conn->commit();
                $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status);
                echo json_encode($data);                   
            }   
            else{
               $data = array("Status"=>"104");
               echo json_encode($data);
            }
        }
        else{        
           $data = array("Status"=>"103");
           echo json_encode($data);    
        }
        $obj->operation_time($userid);
        $conn->close();                                              
} 
$arr = json_decode($_POST['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];    
$Data = $arr["Data"];

// $UserName = 'chenkai';
// var_dump($UserName);
// $TokenID = '14920479314967407246';
// var_dump($TokenID);
// $Data = array("TemplateName"=>'s23231',"MasterSumWeight"=>0.7,"IndicatorAndWeight"=>array("InicatorName"=>'工作态度',"CreateTime"=>12312332));

add_evaluation_template($TokenID,$UserName,$Data);

?>

<?php


require 'mon.php';


function get_setting_page($TokenID,$UserName,$EvaluationID)
{
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
              //指标名称及占比
              $sql = "select Template.IndicatorAndWeight,EvaluationType.EvaluationName from EvaluationType,Template where EvaluationType.EvaluationID = '$EvaluationID' and EvaluationType.TemplateID=Template.TemplateID";
              //var_dump($sql);

              $result = $conn->query($sql);
              $row = $result->fetch_array(MYSQLI_ASSOC);
              $EvaluationName = $row['EvaluationName'];
              $IndicatorAndWeight = $row['IndicatorAndWeight'];
              
              $IndicatorAndWeight = json_decode($IndicatorAndWeight,true);
              $Evaluation = array("EvaluationID"=>$EvaluationID,"EvaluationName"=>$EvaluationName,"IndicatorAndWeight"=>$IndicatorAndWeight);


              //总人数
              $sql = "select count(UserID) from Users";
              $result = $conn->query($sql);
              $row = $result->fetch_array(MYSQLI_ASSOC);
              $UserSum = $row['count(UserID)'];
              //已配置人数
              $sql5 = "select ExamineeID from EvaluationInfo where EvaluationID = '$EvaluationID' group by ExamineeID";
              //var_dump($sql5);
              $result5 = $conn->query($sql5);
              while($row5 = $result5->fetch_array(MYSQLI_ASSOC)){
                  $Examineenum[] = $row5['ExamineeID'];
              }
              $Examineenum=$obj->nonull($Examineenum);
              //var_dump($Examineenum);
              $SetNum = count($Examineenum);    
              //未配置人数
              $UnsetNum = $UserSum - $SetNum;
              $UserList = array("UserSum"=>$UserSum,"SetNum"=>$SetNum,"UnsetNum"=>$UnsetNum);

              //Job
              
              $sql = "select JobID,JobName from Job";
              $result = $conn->query($sql);
              while($row = $result->fetch_array(MYSQLI_ASSOC)){
                  $SumUserList = [];
                  $UserJobNum = [];
                  $UnsetUserList = [];
                  $JobName = $row['JobName'];
                  $JobID = $row['JobID'];

                  $sql6 = "select UserID,TrueName from Users where JobID = '$JobID'";
                  //var_dump($sql6);
                  $result6 = $conn->query($sql6);
                  while($row6 = $result6->fetch_array(MYSQLI_ASSOC)){
                      $SumUserList[] = $row6['TrueName'];
                      $UserJobNum[] = $row6['UserID'];
                  }
                      $SumNum = count($UserJobNum);
                      //被考核人与工作类别交集
                      //var_dump($Examineenum);
                      //var_dump($UserJobNum);
                      $TempList = array_intersect($Examineenum,$UserJobNum);   
                      //var_dump($TempList);
                      //该工作已进行配置   
                      $SetNum = count($TempList);                                      
                  
                  //该工作未进行配置
                  $UnsetUserIDs = array_diff($UserJobNum,$TempList);

  				        $UnsetUserIDs = array_values($UnsetUserIDs);

                  $UnsetUserIDs = $obj->nonull($UnsetUserIDs);
                  //var_dump($UnsetUserIDs);
                  foreach ($UnsetUserIDs as $key => $value) {
                      //var_dump($value);
                      $sql8 = "select TrueName from Users where UserID = '$value'";
                      //var_dump($sql8);
                      $result8 = $conn->query($sql8);
                      $row8 = $result8->fetch_array();
                      $UnsetUserList[] = $row8['TrueName'];
                  }
                //  $item["SumNum"] = $SumNum;
                //  $item["SetNum"] = $SetNum;
                //  $item["SumUserList"] = $SumUserList;
                //  $item["UnsetUserList"] = $UnsetUserList;
                  $JobList[] = array("JobID"=>$JobID,"JobName"=>$JobName,"SumNum"=>$SumNum,"SetNum"=>$SetNum,"SumUserList"=>$SumUserList,"UserIDList"=>$UserJobNum,"UnsetUserIDs"=>$UnsetUserIDs,"UnsetUserList"=>$UnsetUserList);
              }
              
              

              $data = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$Status,"Evaluation"=>$Evaluation,"JobList"=>$JobList,"UserList"=>$UserList);
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
$EvaluationID = $arr["EvaluationID"];


// $UserName = 'chenkai';
// $TokenID = '14925201999088766191';
// $EvaluationID = 1;
// var_dump($EvaluationID);
get_setting_page($TokenID,$UserName,$EvaluationID);

?>

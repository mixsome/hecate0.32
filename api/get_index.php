<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/5
 * Time: 9:36
 */
//header("Content-type:text/html;charset=utf8");  
require 'mon.php';



function get_index($UserName,$TokenID)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    //ar_dump($Status);
    if ($Status == '0') {
	//var_dump($Status);
        $Status = $obj->check_timeout($userid);

        if ($Status == '0') {

            $obj->operation_time($userid);

            $conn = $obj->db_connect();
            $conn->query('SET NAMES UTF8');


            $sql = "select Users.UserID,Users.TrueName,Users.profilePicture,SecretInfo.ContractPeriod,Users.Birthday from Users,SecretInfo where Users.UserID = SecretInfo.UserID ";
            $result = $conn->query($sql);


            while($row = $result->fetch_array(MYSQLI_ASSOC)){
        
                $UserID = $row['UserID'];


                $TrueName = $row['TrueName'];




                $profilePicture = $row['profilePicture'];

                if (file_exists('../'.$profilePicture))
                     {

                         $profilePicture = $row['profilePicture'];
                     }
                     else
                     {
          
                        $profilePicture = "uploadFile/default-icon.png" ;

                     }
                $birthday = $row['Birthday'];
                if($birthday==''){
                    continue;
                }
                
                $Json_ContractPeriod = $row['ContractPeriod'];
                //Json解析

                $ContractPeriod = json_decode($Json_ContractPeriod,true);

                $Contract = end($ContractPeriod);
                

                $ContractStart = $Contract['StartTime'];
                if($ContractStart==''){
                    continue;
                }
                

                $ContractEnd = $Contract['EndTime'];
                

				$nowtime = $obj->timestamp();
				$nowtime = (int)($nowtime);
				$ContractValidity = $ContractEnd - $nowtime;
                
				$datenum = floor($ContractValidity/(60*60*24));
                
				
                $ExpireDate = date('m月d日',$ContractEnd);   //到期时间
                
   
                //$RemindTime = time() - $ContractEnd;
                //var_dump($RemindTime);
                //$year = $obj->time2year($ContractValidity);     //合同期
                //var_dump($year);
                //$day = $obj->time2day($RemindTime);       //   合同剩余天数
				$day = $datenum;
                
				$year = floor($datenum/365);
                

                $Birthday = date('m月d日',$birthday);              //生日
                $AnotherBirthday = date('m-d',$birthday);

  

                $nowyear = date('Y',$nowtime);
                

                $nowdate = date("Y-m-d",$nowtime);


                //计算生日剩余天数
                $nowbirthday = strtotime("$nowyear".'-'."$AnotherBirthday");


                if($nowbirthday < $nowtime){

                    
                    $nextyear = $nowyear + 1;

                    $datetime1 = new DateTime("$nowdate");

                    $datetime2 = new DateTime("$nextyear".'-'."$AnotherBirthday");

                    $interval = $datetime1->diff($datetime2); 

                    $Remindbirthday = $interval->format('%a ');  //生日剩余天数
                    

                }
                else{

                    // $date = new DateTime();
                    // var_dump($date);

                    // $time = "$nowyear".'-'."$AnotherBirthday";
                    // var_dump($time);
                    $datetime1 = new DateTime("$nowyear".'-'."$AnotherBirthday");


                    $datetime2 = new DateTime("$nowdate");


                    $interval = $datetime1->diff($datetime2); 

                    $Remindbirthday = $interval->format('%a');  //生日剩余天数


                }

                

                
                $Constellation = $obj->zodiac($birthday);          //星座

   
                $ContractData[] = array("TrueName"=>$TrueName,"ProfilePicture"=>$profilePicture,"ContractPeriod"=>$year,"ExpireDate"=>$ExpireDate,"RemainDay"=>$day);
       
                $BirthData[] = array("TrueName"=>$TrueName,"ProfilePicture"=>$profilePicture,"Constellation"=>$Constellation,"Birthday"=>$Birthday,"Remainbirthday"=>$Remindbirthday);
                //var_dump($BirthData);
                
            }
            

            //数组排序
            foreach ($BirthData as $val){
                $key_Arrays[] = $val['Remainbirthday'];
            }
            array_multisort($key_Arrays,SORT_ASC,SORT_NUMERIC,$BirthData);  


            foreach ($ContractData as $val){
                $key[] = $val['RemainDay'];
            }

            array_multisort($key,SORT_ASC,SORT_NUMERIC,$ContractData);


            $conn->close();

            $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "ContractData" => $ContractData,"BirthData"=>$BirthData);


            $Data = json_encode($data);
            echo $Data;


        } else {
            $Status = '201';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;

        }

    } else {
        $Status = '202';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;

    }
}
$req_data = $_GET['data'];

$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];



$TokenID = $deal_data['TokenID'];
// $UserName = 'dushuai';
// $TokenID = "14939707793814311044";


get_index($UserName,$TokenID);
?>

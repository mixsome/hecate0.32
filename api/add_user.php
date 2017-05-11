<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/28
 * Time: 11:06
 */
require 'mon.php';
require 'pinyin.php';



function add_user($UserName,$TokenID,$Array)
{
    $obj = new serverinfo();

    
    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);
            $PinYin = new PinYin();

            $Ask = 'UsersInsert';

            $Permission = $obj->get_permission($UserName,$Ask);
            //var_dump($Permission);
            if($Permission == 'Y'){
            
                
                    //var_dump($Array);
                    $GroupID = 4;
                    $Password = 123456;
                    $TrueName = $Array['TrueName'];
                    //var_dump($TrueName);
                    $Gender = $Array['Gender'];
                    $Email = $Array['Email'];
                    $PhoneNumber = $Array['PhoneNumber']; 
                    $NativePlace = $Array['NativePlace'];
                    
                    $Birthday = $Array['Birthday'];

                    $date_birth = date("Y-m-d",$Birthday);
                    $Age = $obj->birthday2($date_birth);

                    $UserType = $Array['UserType'];
                    $Department = $Array['Deparment'];
                    $JobID = $Array['JobID'];

                    $IDCard = $Array['IDCard'];
                    $EducationalLevel = $Array['EducationalLevel'];
                    $Specialty = $Array['Specialty'];
                    $Graduatetime = $Array['Graduatetime'];
                    $HireDate = $Array['HireDate'];
                    $LeaveDate = $Array['LeaveDate'];
                    $TrialPeriodStart = $Array['TrialPeriodStart'];
                    $TrialPeriodEnd = $Array['TrialPeriodEnd'];
                    $EmergencyContactName1 = $Array['EmergencyContactName1'];
                    $EmergencyContactPhone1 = $Array['EmergencyContactPhone1'];
                    $EmergencyContactName2 = $Array['EmergencyContactName2'];
                    $EmergencyContactPhone2 = $Array['EmergencyContactPhone2'];
                    $ContractPeriod = $Array['ContractPeriod'];
                    $ContractPeriod = json_encode($ContractPeriod);
                    //var_dump($ContractPeriod);
                    $Bank = $Array['Bank'];
                    $BankAccount = $Array['BankAccount'];
                    $PayGrade = $Array['PayGrade'];
                    $Salary = $Array['Salary'];
                    //$Remarks = $Array['Remarks'];

                    $Name = $PinYin->pinyin($TrueName);

                    $sqlName = $Name.'%';
                    $conn = $obj->db_connect();
                    $conn->query('SET NAMES UTF8');
                    $sql = "select count(UserName) from Users where UserName like '$sqlName'";
                    $result = $conn->query($sql);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $Count = $row['count(UserName)'];
                    //var_dump($Count);
                    if($Count == 0){
                        $Name = $Name;
                    }
                    else{
                        
                        $Name = "$Name"."$Count";
                        //var_dump($Name);
                    }
                    



                    $sql = "insert into Users (GroupID,UserName,Password,TrueName,Gender,Email,PhoneNumber,NativePlace,Age,Birthday,UserType,Department,JobID) values('$GroupID','$Name','$Password','$TrueName','$Gender','$Email','$PhoneNumber','$NativePlace','$Age','$Birthday','$UserType','$Department','$JobID')";
                    //var_dump($sql);
                    $conn->query($sql);
                    $conn->commit();
                    //var_dump('bbbb');
                    $sql = "select UserID from Users where UserName = '$Name'";
                    $result = $conn->query($sql);
                    if ($row = $result->fetch_assoc()) {
                        $UserID = $row['UserID'];
                        //var_dump($UserID);
                    }
                    $sql = "insert into SecretInfo (UserID,IDCard,EducationalLevel,Specialty,Graduatetime,HireDate,LeaveDate,TrialPeriodStart,TrialPeriodEnd,EmergencyContactName1,EmergencyContactPhone1,EmergencyContactName2,EmergencyContactPhone2,ContractPeriod,Bank,BankAccount,PayGrade,Salary) 
                            values('$UserID','$IDCard','$EducationalLevel','$Specialty','$Graduatetime','$HireDate','$LeaveDate','$TrialPeriodStart','$TrialPeriodEnd','$EmergencyContactName1','$EmergencyContactPhone1','$EmergencyContactName2','$EmergencyContactPhone2','$ContractPeriod','$Bank','$BankAccount','$PayGrade','$Salary')";
                    //var_dump($sql);
                    $conn->query($sql);
                    $conn->commit();
                    $conn->close();
                
                


                //重名问题！
                
                $data = array("UserName" => $UserName,"TokenID" => $TokenID, "Status" => $Status);


                $Data = json_encode($data);
                echo $Data;
                $conn->close();
            }
            else{
                $Status = '404';
                $data = array("Status" => $Status);
                $Data = json_encode($data);
                echo $Data;
            }
        } else {
            $Status = '201';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;

        }

    } else {
        $Status = '201';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;

    }
}
$req_data = $_POST['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$Array = $deal_data['Data'][0];

add_user($UserName,$TokenID,$Array);
//$pin = new serverinfo();
//echo $pin->Pinyin('陈凯','UTF8');
?>
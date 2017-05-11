<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/28
 * Time: 11:00
 */

require 'mon.php';


#session start();

function update_user($UserName,$TokenID,$Array)
{
    $obj = new serverinfo();



    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);
    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'UsersUpdate';

            $Permission = $obj->get_permission($UserName,$Ask);


            if($Permission == 'Y'){
                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');

                
                    $UserID = $Array['UserID'];
                    //var_dump($UserID);
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
                    

                $sql = "update  Users set TrueName='$TrueName',Gender='$Gender',EMail='$Email',PhoneNumber='$PhoneNumber',NativePlace='$NativePlace',Age='$Age',Birthday='$Birthday',UserType='$UserType',Department='$Department',JobID='$JobID'  where UserID='$UserID'";
                //var_dump($sql);

                $conn->query($sql);
                $conn->commit();
                    
                
                

                $sql = "update  SecretInfo set IDCard='$IDCard',EducationalLevel='$EducationalLevel',Specialty='$Specialty',Graduatetime='$Graduatetime',HireDate='$HireDate',LeaveDate='$LeaveDate',TrialPeriodStart='$TrialPeriodStart',TrialPeriodEnd='$TrialPeriodEnd',EmergencyContactName1='$EmergencyContactName1',EmergencyContactName2='$EmergencyContactName2',EmergencyContactPhone1='$EmergencyContactPhone1',EmergencyContactPhone2='$EmergencyContactPhone2',ContractPeriod='$ContractPeriod',Bank='$Bank',BankAccount='$BankAccount',PayGrade='$PayGrade',Salary='$Salary' where UserID='$UserID'";
                //var_dump($sql);
                $conn->query($sql);
                $conn->commit();
                


                $conn->close();
                $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status);


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
//var_dump($Array);

update_user($UserName,$TokenID,$Array);
?>
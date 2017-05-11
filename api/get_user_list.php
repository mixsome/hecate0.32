<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/24
 * Time: 15:06
 */

require 'mon.php';





function get_user_list($UserName,$TokenID,$TypeData)
{
    $obj = new serverinfo();

    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);


    if ($Status == '0') {
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'UsersSelect';
            //var_dump($Ask);
            $Permission = $obj->get_permission($UserName,$Ask);
            //var_dump($Permission);
            if($Permission == 'Y'){
                //echo 2123;

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');

                $TypeUser = $TypeData['TypeUser'];
                $TypeSecret = $TypeData['TypeSecret'];
                $sql = "select Users.UserID,Users.GroupID,Users.UserName,Users.TrueName,Users.Gender,Users.Email,Users.PhoneNumber,Users.NativePlace,Users.Age,Users.Birthday,Users.UserType,Users.Department,Users.JobID,Users.Position,Users.profilePicture,SecretInfo.IDCard,SecretInfo.EducationalLevel,SecretInfo.Specialty,SecretInfo.GraduateSchool,SecretInfo.GraduateTime,SecretInfo.WorkingStatus from Users,SecretInfo where Users.UserID = SecretInfo.UserID group by Users.UserID";
                $result = $conn->query($sql);
                //var_dump('123123');
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $Arr_data[] = $row;
                    //var_dump($row['Gender']);
                    $Gender[] = $row['Gender'];
                    $Department[] = $row['Department'];
                    $UserType[] = $row['UserType'];
                    $EducationalLevel[] = $row['EducationalLevel'];
                    $WorkingStatus[] = $row['WorkingStatus'];

                }

                $Arr_data = $obj->nonull($Arr_data);
                $gender=array_unique($Gender);

                $Gender = $obj->toIndexArr($gender);

                $department=array_unique($Department);
                $Department = $obj->toIndexArr($department);
                //var_dump($Department);

                $UserType=array_unique($UserType);
                $UserType = $obj->toIndexArr($UserType);

                $educationalLevel=array_unique($EducationalLevel);
                $EducationalLevel = $obj->toIndexArr($educationalLevel);

                $workingStatus=array_unique($WorkingStatus);
                $WorkingStatus = $obj->toIndexArr($workingStatus);

                //var_dump($Gender);
                $sql = "select JobID,JobName from Job";
                //var_dump('111');
                $result = $conn->query($sql);
                //var_dump('222');
                while($row= mysqli_fetch_array($result,MYSQLI_ASSOC)){
                    $Job[] = $row;
                }
                //var_dump('2222');

                $TypeData = array(array( "Job" => $Job,"Gender"=>$Gender,"Department"=>$Department,"EducationalLevel"=>$EducationalLevel,"WorkingStatus"=>$WorkingStatus,"UserType"=>$UserType));
                //echo 111;
                if (empty($TypeUser)) {
                    //echo 1111;
                    if (empty($TypeSecret)) {
                        //var_dump('333');

                        //var_dump(99999);

                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Arr_data, "TypeData" => $TypeData);
                        $Data = json_encode($data,true);
                        echo $Data;
                        $conn->close();
                    } else {
                        $sqlwhere = "";
                        //var_dump('qqqq');
                        foreach ($TypeSecret as $key => $value) {
                            $sqlwhere .= "SecretInfo.$key ='$value' and ";
                        }
                        $sqlwhere = rtrim($sqlwhere, "and ");
                        $sql = "select Users.UserID,Users.GroupID,Users.UserName,Users.TrueName,Users.Gender,Users.Email,Users.PhoneNumber,Users.NativePlace,Users.Age,Users.Birthday,Users.UserType,Users.Department,Users.JobID,Users.Position,Users.profilePicture,SecretInfo.IDCard,SecretInfo.EducationalLevel,SecretInfo.Specialty,SecretInfo.GraduateSchool,SecretInfo.GraduateTime,SecretInfo.WorkingStatus from Users,SecretInfo where Users.UserID = SecretInfo.UserID and " . "$sqlwhere";

                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Array_data[] = $row;
                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "TypeData" => $TypeData);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                } else {
                    if (empty($TypeSecret)) {
                        //var_dump('wwwww');
                        $sqlwhere = "";
                        foreach ($TypeUser as $key => $value) {
                            $sqlwhere .= "Users.$key ='$value' and ";
                        }
                        $sqlwhere = rtrim($sqlwhere, "and ");
                        //var_dump($sqlwhere);
                        $sql = "select Users.UserID,Users.GroupID,Users.UserName,Users.TrueName,Users.Gender,Users.Email,Users.PhoneNumber,Users.NativePlace,Users.Age,Users.Birthday,Users.UserType,Users.Department,Users.JobID,Users.Position,Users.profilePicture,SecretInfo.IDCard,SecretInfo.EducationalLevel,SecretInfo.Specialty,SecretInfo.GraduateSchool,SecretInfo.GraduateTime,SecretInfo.WorkingStatus from Users,SecretInfo where Users.UserID = SecretInfo.UserID and " . "$sqlwhere";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Array_data[] = $row;
                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "TypeData" => $TypeData);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    } else {
                        //var_dump('eeee');
                        $sqlwhere_user = "";
                        $sqlwhere_secret = "";
                        foreach ($TypeUser as $key => $value) {
                            $sqlwhere_user .= "Users.$key ='$value' and ";
                        }
                        //$sqlwhere_user = rtrim($sqlwhere_user, "and ");
                        //var_dump($sqlwhere_user);
                        foreach ($TypeSecret as $key => $value) {
                            $sqlwhere_secret .= "SecretInfo.$key ='$value' and ";
                        }
                        $sqlwhere = $sqlwhere_user . $sqlwhere_secret;
                        $sqlwhere = rtrim($sqlwhere, "and ");
                        //var_dump($sqlwhere);
                        $sql = "select Users.UserID,Users.GroupID,Users.UserName,Users.TrueName,Users.Gender,Users.Email,Users.PhoneNumber,Users.NativePlace,Users.Age,Users.Birthday,Users.UserType,Users.Department,Users.JobID,Users.Position,Users.profilePicture,SecretInfo.IDCard,SecretInfo.EducationalLevel,SecretInfo.Specialty,SecretInfo.GraduateSchool,SecretInfo.GraduateTime,SecretInfo.WorkingStatus from Users,SecretInfo where Users.UserID = SecretInfo.UserID and " . "$sqlwhere";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while($row=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                            $Array_data[] = $row;
                        }
                        $Array_data = $obj->nonull($Array_data);
                        $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Data" => $Array_data, "TypeData" => $TypeData);
                        $Data = json_encode($data);
                        echo $Data;
                        $conn->close();
                    }
                }
            }
        } else {
            $Status = '201';
            $data = array("Status" => $Status);
            $Data = json_encode($data);
            echo $Data;

        }
    }

    else {
        $Status = '202';
        $data = array("Status" => $Status);
        $Data = json_encode($data);
        echo $Data;

    }

}
$req_data = $_GET['data'];
$deal_data = json_decode($req_data, true);
//var_dump($deal_data);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$TypeData =$deal_data['TypeData'];
get_user_list($UserName,$TokenID,$TypeData);
?>
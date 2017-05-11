<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/5
 * Time: 15:28
 */
require 'mon.php';



function get_statistics_info($UserName,$TokenID,$SearchType)
{
    $obj = new serverinfo();
    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid, $TokenID);	
    if ($Status == '0') {		
        $Status = $obj->check_timeout($userid);
        if ($Status == '0') {
            $obj->operation_time($userid);

            $Ask = 'UsersSelect';   //SecretSelect    

            $Permission = $obj->get_permission($UserName,$Ask);

            if($Permission == 'Y'){

                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');
                $sql = "Select users.UserID,Users.Birthday,Users.Gender,Users.UserType,SecretInfo.EducationalLevel from Users,SecretInfo where Users.UserID = SecretInfo.UserID";
                $result = $conn->query($sql);
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    				
                    $Birthday[] = $row['Birthday'];
                    $Gender[] = $row['Gender'];
                    $EducationalLevel[] = $row['EducationalLevel'];
                    $UserID[] = $row['UserID'];
                    $UserType[] =$row['UserType'];
                }
                //男女统计
                $AllNum = count($UserID);
                //var_dump($AllNum);
                $male = 0;
                $female = 0;
                foreach ($Gender as $key => $value) {
                    if ($value == '男') {
                        $male++;
                    } elseif ($value == '女') {
                        $female++;
                    }
                }
                $Gender = array("Man"=>$male,"Woman"=>$female);
                //男女占比
                // $Man = (($male / $AllNum) * 100) . "%";
                // $Woman = (($female / $AllNum) * 100) . "%";
                //员工及学生统计
                $worker = 0;
                $student = 0;
                foreach ($UserType as $key => $value) {
                    if ($value == '员工'){
                        $worker++;

                    }
                    elseif ($value == '学生') {
                        $student++;
                    }
                }
                $UserType= array("Worker"=>$worker,"Student"=>$student);
                //学历统计
                $Doctor = 0;
                $Master = 0;
                $College = 0;
                $Specilaist = 0;
                $Another = 0;
    			//var_dump($EducationalLevel);
                foreach ($EducationalLevel as $key=>$value) {

                    if ($value == "博士") {
                        $Doctor++;
                    } elseif ($value == "硕士") {
                        //echo $value;
                        $Master++;
                    } elseif ($value == "本科") {
                        //echo $value;
                        $College++;
                    } elseif ($value == "大专") {
                        $Specilaist++;
                    } else {
                        $Another++;
                    }
                }
                
                $EducationalLevel = array("Doctor" => $Doctor, "Master" => $Master, "College" => $College, "Specilaist" => $Specilaist, "Another" => $Another);

                //生日月份统计
                $Jan = 0;
                $Feb = 0;
                $Mar = 0;
                $Apr = 0;
                $May = 0;
                $Jun = 0;
                $Jul = 0;
                $Aug = 0;
                $Sept = 0;
                $Oct = 0;
                $Nov = 0;
                $Dec = 0;
                //var_dump($Birthday);
                foreach ($Birthday as $key => $value) {
                    $month = date("m", $value);
                    //var_dump($month);
                    if ($month == '01') {
                        $Jan++;
                    }
                    elseif ($month == '02') {
                        $Feb++;
                    }
                    elseif ($month == '03') {
                        $Mar++;
                    }
                    elseif ($month == '04') {
                        $Apr++;
                    }
                    elseif ($month == '05') {
                        $May++;
                    }
                    elseif ($month == '06') {
                        $Jun++;
                    }
                    elseif ($month == '07') {
                        $Jul++;
                    }
                    elseif ($month == '08'){
                        $Aug++;
                    }
                    elseif ($month == '09') {
                        $Sept++;
                    }
                    elseif ($month == '10') {
                        $Oct++;
                    }
                    elseif ($month == '11') {
                        $Nov++;
                    }
                    elseif ($month == '12') {
                        $Dec++;
                    }

                }

                //岗位统计
                $sql = "select JobID,JobName from Job";
                $result = $conn->query($sql);
                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $JobID = $row['JobID'];
                    $JobName = $row['JobName'];
                    $sql1 = "select count(UserID) from Users where JobID = '$JobID'";
                    $result1 = $conn->query($sql1);
                    $row1 = $result1->fetch_array(MYSQLI_ASSOC);
                    $job['JobCount'] = $row1['count(UserID)'];
                    $job['JobName'] = $JobName;
                    $Job[] = $job;
                }

                $BirthdaySum = [$Jan, $Feb, $Mar, $Apr, $May, $Jun, $Jul, $Aug, $Sept, $Oct, $Nov, $Dec];
                $BasicData = array("Gender" => $Gender,"UserType"=>$UserType,"BirthdaySum" => $BirthdaySum,"EducationalLevel"=>$EducationalLevel,"Job"=>$Job);

    			
                //月份
                if ($SearchType == 'month') {
                    $oldtime = strtotime("-3 year", $obj->timestamp());
                    $oldtime = date("Y-m-d",$oldtime);
                    $nowtime = date("Y-m-d",$obj->timestamp());
                    $timeArr=array();
                    $t1=$oldtime;
                    $t2=$obj->getmonths($t1)['1'];
                    //var_dump($t2);
                    while($t2<$nowtime || $t1<=$nowtime){//月为粒度的时间数组
                        $timeArr[]=$t1.','.$t2;
                        $t1=date('Y-m-d',strtotime("$t2 +1 day"));
                        $t2=$obj->getmonths($t1)['1'];
                        $t2=$t2>$nowtime?$nowtime:$t2;
                    }
                    //var_dump($timeArr);
                    
                    foreach ($timeArr as $mon => $value) {
                        $monarr = explode(',', $value);
                        $mon = date("Y-m",strtotime($monarr[0]));

                        $WorkerNum =0 ;
                        $StudentNum = 0;
                        $UserSum = 0;
                        $User = [];
                        $LastUser = [];
                        $HireName = [];
                        $Hire = [];
                        $HirePicture = [];
                        $HireSum = 0;
                        $LeaveName = [];
                        $Leave  = [];
                        $LeavePicture = [];
                        $LeaveSum = 0;
                        $LastSum = 0;
                        $sql = "select Users.UserID,Users.UserType from Users,SecretInfo where Users.UserID = SecretInfo.UserID and (SecretInfo.HireDate < UNIX_TIMESTAMP('$monarr[0] 00:00:00') and (SecretInfo.LeaveDate >= UNIX_TIMESTAMP('$monarr[0] 00:00:00') or SecretInfo.LeaveDate=''))";
                        //var_dump($sql);

                        $result = $conn->query($sql);

                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            // if ($row['UserType'] == '员工') {
                            //     $WorkerNum++;
                            //     //var_dump($WorkerNum);
                            // } elseif ($row['UserType'] == '学生') {
                            //     $StudentNum++;
                            //     //var_dump($StudentNum);
                            // }
    						
                            $User[] = $row['UserID'];
                        }
                        $UserSum = count($User);      //起始人数
                        //var_dump($User);


                        $sql = "select Users.TrueName,Users.profilePicture,Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and SecretInfo.HireDate between UNIX_TIMESTAMP('$monarr[0] 00:00:00') and UNIX_TIMESTAMP('$monarr[1] 00:00:00') ";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                            $HireName[] = $row['TrueName'];
                            if($row['profilePicture'] == ''){
                                $row['profilePicture'] = './uploadFile/default-icon.png';
                            }
                            $HirePicture[] = $row['profilePicture'];
                            $Hire[] = $row['UserID'];
                        }
                        //var_dump($Hire);
                        $HireSum = count($Hire);       //入职人数



                        $sql = "select Users.TrueName,Users.profilePicture,Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and SecretInfo.LeaveDate between UNIX_TIMESTAMP('$monarr[0] 00:00:00') and UNIX_TIMESTAMP('$monarr[1] 00:00:00') ";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $LeaveName[] = $row['TrueName'];
                            if($row['profilePicture'] == ''){
                                $row['profilePicture'] = './uploadFile/default-icon.png';
                            }
                            $LeavePicture[] = $row['profilePicture'];
                            $Leave[] = $row['UserID'];
                        }
                        //var_dump($Leave);
                        $LeaveSum = count($Leave);       //离职人数
                        

                        $sql = "select Users.UserID,Users.UserType from Users,SecretInfo where Users.UserID = SecretInfo.UserID and (SecretInfo.HireDate < UNIX_TIMESTAMP('$monarr[1] 00:00:00') and (SecretInfo.LeaveDate > UNIX_TIMESTAMP('$monarr[1] 00:00:00') or SecretInfo.LeaveDate=''))";
                        //var_dump($sql);

                        $result = $conn->query($sql);

                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            if ($row['UserType'] == '员工') {
                                $WorkerNum++;
                                //var_dump($WorkerNum);
                            } elseif ($row['UserType'] == '学生') {
                                $StudentNum++;
                                //var_dump($StudentNum);
                            }
                            $LastUser[] = $row['UserID'];
                        }
                            //var_dump($LastUser);
                            $LastSum =  count($LastUser);    //最终人数
                        
                        $Statistics[] = array("Time"=>$mon,"UserSum" => $UserSum, "WorkerNum" => $WorkerNum, "StudentNum" => $StudentNum, "HireSum" => $HireSum, "HireName" => $HireName, "HirePicture" => $HirePicture, "LeaveSum" => $LeaveSum, "LeaveName" => $LeaveName, "LeavePicture" => $LeavePicture ,"LastSum"=>$LastSum);
                        //var_dump($EveryMonth);
                        // foreach ($EveryMonth[$value] as $key => $val) {
                        //     $value = $obj->nonull($value);
                        // }

                    }
                    $Status = 0;
                    $conn->close();

                    $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Statistics" => $Statistics, "BasicData" => $BasicData);

                    $Data = json_encode($data);
                    echo $Data;

                } 
            
                //季度
                elseif ($SearchType == 'quarter') {
    				$nowtime = $obj->timestamp();    //当前时间
                    
                    $oldtime = strtotime("-3 year", $nowtime);
                    $nowtime = date("Y-m-d",$nowtime);
                    $oldtime = date("Y-m-d",$oldtime);
                    $tStr=explode('-',$oldtime);

                    $month=$tStr['1'];
                    if($month<=3){
                        $t2=date("$tStr[0]-03-31");
                    }else if($month<=6){
                        $t2=date("$tStr[0]-06-30");
                    }else if($month<=9){
                        $t2=date("$tStr[0]-09-30");
                    }else{
                        $t2=date("$tStr[0]-12-31");
                    }
                    $t1=$oldtime;

                    $t2=$t2>$nowtime?$nowtime:$t2;
                    $timeArr=array();
                    while($t2<$nowtime || $t1<=$nowtime){     //季度为粒度的时间数组

                        $timeArr[]=$t1.','.$t2;
                        $t1=date('Y-m-d',strtotime("$t2 +1 day"));
                        $t2=date('Y-m-d',strtotime("$t1 +3 months -1 day"));
                        $t2=$t2>$nowtime?$nowtime:$t2;
                    }
                    foreach ($timeArr as $key => $value) {
                        $quarterarr = explode(',', $value);
                        $endtime = explode('-', $quarterarr[1]);
                        $year= $endtime['0'];
                        $month = $endtime['1'];
                        if($month<=3){
                            $quarter = $year.'-01';
                        }else if($month<=6){
                            $quarter = $year.'-02';
                        }else if($month<=9){
                            $quarter = $year.'-03';
                        }else{
                            $quarter = $year.'-04';
                        }
                        $WorkerNum =0 ;
                        $StudentNum = 0;
                        $UserSum = 0;
                        $User = [];
                        $LastUser = [];
                        $HireName = [];
                        $Hire = [];
                        $HirePicture = [];
                        $HireSum = 0;
                        $LeaveName = [];
                        $Leave  = [];
                        $LeavePicture = [];
                        $LeaveSum = 0;
                        $LastSum = 0;
                        $sql = "select Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and (SecretInfo.HireDate < UNIX_TIMESTAMP('$quarterarr[0] 00:00:00') and (SecretInfo.LeaveDate >= UNIX_TIMESTAMP('$quarterarr[0] 00:00:00') or SecretInfo.LeaveDate=''))";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            // if ($row['UserType'] == '员工') {
                            //     $WorkerNum++;
                            // } elseif ($row['UserType'] == '学生') {
                            //     $StudentNum++;
                            // }
                            //var_dump($row['UserID']);
                            $User[] = $row['UserID'];
                            //
                        }
                        //var_dump($User);
                        $UserSum = count($User);      //起始人数

                        $sql = "select Users.TrueName,Users.profilePicture,Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and SecretInfo.HireDate between UNIX_TIMESTAMP('$quarterarr[0] 00:00:00') and UNIX_TIMESTAMP('$quarterarr[1] 00:00:00') ";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $HireName[] = $row['TrueName'];
                            if($row['profilePicture'] == ''){
                                $row['profilePicture'] = './uploadFile/default-icon.png';
                            }
                            $HirePicture[] = $row['profilePicture'];
                            $Hire[] = $row['UserID'];
                        }
                        //var_dump($Hire);
                        $HireSum = count($Hire);       //入职人数


                        $sql = "select Users.TrueName,Users.profilePicture,Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and SecretInfo.LeaveDate between UNIX_TIMESTAMP('$quarterarr[0] 00:00:00') and UNIX_TIMESTAMP('$quarterarr[1] 00:00:00') ";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $LeaveName[] = $row['TrueName'];
                            if($row['profilePicture'] == ''){
                                $row['profilePicture'] = './uploadFile/default-icon.png';
                            }
                            $LeavePicture[] = $row['profilePicture'];
                            $Leave[] = $row['UserID'];
                        }
                        //var_dump($Leave);
                        $LeaveSum = count($Leave);       //离职人数
                        


                        $sql = "select Users.UserID,Users.UserType from Users,SecretInfo where Users.UserID = SecretInfo.UserID and (SecretInfo.HireDate < UNIX_TIMESTAMP('$quarterarr[1] 00:00:00') and (SecretInfo.LeaveDate > UNIX_TIMESTAMP('$quarterarr[1] 00:00:00') or SecretInfo.LeaveDate=''))";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            if ($row['UserType'] == '员工') {
                                $WorkerNum++;
                            } elseif ($row['UserType'] == '学生') {
                                $StudentNum++;
                            }
                            //var_dump($row['UserID']);
                            $LastUser[] = $row['UserID'];
                            //
                        }
                        //var_dump($LastUser);
                        $LastSum = count($LastUser);    //最终人数
                        $Statistics[] = array("Time"=>$quarter,"UserSum" => $UserSum, "WorkerNum" => $WorkerNum, "StudentNum" => $StudentNum, "HireSum" => $HireSum, "HireName" => $HireName, "HirePicture" => $HirePicture, "LeaveSum" => $LeaveSum, "LeaveName" => $LeaveName, "LeavePicture" => $LeavePicture,"LastSum"=>$LastSum);

                    }
                    $Status = 0;
                    $conn->close();

                    $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Statistics" => $Statistics, "BasicData" => $BasicData);

                    $Data = json_encode($data);
                    echo $Data;

                } 


                //年份
                elseif ($SearchType == 'year') {
                    $nowtime = $obj->timestamp();    //当前时间
                    $nowyear = date("Y", $nowtime);

                    $nowdate = strtotime("$nowyear-01-01");


                    $oldtime = strtotime("-3 year", $nowdate);
                    //echo $oldtime;

                    while (($oldtime = strtotime('+1 year', $oldtime)) <= $nowdate) {
                        $yeararr[] = date('Y', $oldtime);      // 取得递增年;
                    }
                    //var_dump($yeararr);

                    foreach ($yeararr as $key => $value) {
                        $WorkerNum =0 ;
                        $StudentNum = 0;
                        $UserSum = 0;
                        $User = [];
                        $LastUser = [];
                        $HireName = [];
                        $Hire = [];
                        $HirePicture = [];
                        $HireSum = 0;
                        $LeaveName = [];
                        $Leave  = [];
                        $LeavePicture = [];
                        $LeaveSum = 0;
                        $LastSum = 0;
                        $sql = "select Users.UserID,Users.UserType from Users,SecretInfo where Users.UserID = SecretInfo.UserID and (SecretInfo.HireDate < UNIX_TIMESTAMP('$value-01-01 00:00:00') and (SecretInfo.LeaveDate >= UNIX_TIMESTAMP('$value-01-01 00:00:00') or SecretInfo.LeaveDate=''))";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            if ($row['UserType'] == '员工') {
                                $WorkerNum++;
                            } elseif ($row['UserType'] == '学生') {
                                $StudentNum++;
                            }
                            $User[] = $row['UserID'];
                        }
                        $UserSum = count($User);      //起始人数
                        //var_dump($User);

                        $sql = "select Users.TrueName,Users.profilePicture,Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and SecretInfo.HireDate between UNIX_TIMESTAMP('$value-01-01 00:00:00') and UNIX_TIMESTAMP('$value-12-31 00:00:00') ";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $HireName[] = $row['TrueName'];
                            if($row['profilePicture'] == ''){
                                $row['profilePicture'] = './uploadFile/default-icon.png';
                            }
                            $HirePicture[] = $row['profilePicture'];
                            $Hire[] = $row['UserID'];
                        }
                        $HireSum = count($Hire);       //入职人数
                        //var_dump($Hire);

                        $sql = "select Users.TrueName,Users.profilePicture,Users.UserID from Users,SecretInfo where Users.UserID = SecretInfo.UserID and SecretInfo.LeaveDate between UNIX_TIMESTAMP('$value-01-01 00:00:00') and UNIX_TIMESTAMP('$value-12-31 00:00:00') ";
                        //var_dump($sql);
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            $LeaveName[] = $row['TrueName'];
                            if($row['profilePicture'] == ''){
                                $row['profilePicture'] = './uploadFile/default-icon.png';
                            }
                            $LeavePicture[] = $row['profilePicture'];
                            $Leave[] = $row['UserID'];
                        }
                        $LeaveSum = count($Leave);       //离职人数
                        //var_dump($Leave);

                        $sql = "select Users.UserID,Users.UserType from Users,SecretInfo where Users.UserID = SecretInfo.UserID and (SecretInfo.HireDate < UNIX_TIMESTAMP('$value-12-31 00:00:00') and (SecretInfo.LeaveDate > UNIX_TIMESTAMP('$value-12-31 00:00:00') or SecretInfo.LeaveDate=''))";
                        //var_dump($sql);

                        $result = $conn->query($sql);
                        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                            if ($row['UserType'] == '员工') {
                                $WorkerNum++;
                            } elseif ($row['UserType'] == '学生') {
                                $StudentNum++;
                            }
                            $LastUser[] = $row['UserID'];
                        }
                        $LastSum = count($LastUser);    //最终人数
                        //var_dump($LastUser);

                        $Statistics[] = array("Time"=>$value,"UserSum" => $UserSum, "WorkerNum" => $WorkerNum, "StudentNum" => $StudentNum, "HireSum" => $HireSum, "HireName" => $HireName, "HirePicture" => $HirePicture, "LeaveSum" => $LeaveSum, "LeaveName" => $LeaveName, "LeavePicture" => $LeavePicture,"LastSum"=>$LastSum);


                    }
                    $Status = 0;
                    $conn->close();

                    $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status, "Statistics" => $Statistics, "BasicData" => $BasicData);

                    $Data = json_encode($data);
                    echo $Data;
                }
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
$req_data = $_GET['data'];
$deal_data = json_decode($req_data, true);
$UserName = $deal_data['UserName'];
$TokenID = $deal_data['TokenID'];
$SearchType = $deal_data['SearchType'];

// $UserName = 'dushuai';
// $TokenID = '14932575244129343165';
// $SearchType = 'quarter';

get_statistics_info($UserName,$TokenID,$SearchType);
?>
<?php
require 'mon.php';
function  get_fuzzy_search($TokenID,$UserName,$Keyword){
    $obj = new serverinfo();
	$conn = $obj->db_connect();
	$conn->query('SET NAMES UTF8');
    $key = '%'.$Keyword.'%';
	$userid = $obj->get_userid($UserName);
	$flag = $obj->check_tokenid($userid,$TokenID);
    if ($flag == '0'){
            $timeout_flag = $obj->check_timeout($userid);               
            if($timeout_flag == '0'){  
                $status = '0';
                $conn = $obj->db_connect();
                $conn->query('SET NAMES UTF8');


                $sql = "select TrueName from Users where TrueName like '$key'";
                $result = $conn->query($sql);
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Search_result[] = $row;
                }
                if (!empty($Search_result)){
                    if (count($Search_result)<2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }
                        $Usertable = array("Users"=>array($arr1));                       
                    }
                    else if(count($Search_result)==2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>$key,"value"=>$value);
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>$key,"value"=>$value);
                        }
                        $Usertable = array("Users"=>array($arr1,$arr2)); 
                    }                  
                    else{
                        $num = count($Search_result) - 1;
                        while ($num > 2){
                            unset($Search_result[$num]);
                            $num -= 1;
                        }
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $Usertable = array("Users"=>array($arr1,$arr2));                         
                    }
                                       
                } 
                $sql = "select UsedBy from Assets where UsedBy like '$key'";
                $result = $conn->query($sql);
				$Search_result = '';
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Search_result[] = $row;
                }
                if (!empty($Search_result)){
                    if (count($Search_result)<2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }
                        $Assetstable = array("Assets"=>array($arr1));                       
                    }
                    else if(count($Search_result)==2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $Assetstable = array("Assets"=>array($arr1,$arr2)); 
                    }                  
                    else{
                        $num = count($Search_result) - 1;
                        while ($num > 2){
                            unset($Search_result[$num]);
                            $num -= 1;
                        }
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $Assetstable = array("Assets"=>array($arr1,$arr2));                         
                    }
                                       
                } 
                $sql = "select Department from Users where Department like '$key'";
                $result = $conn->query($sql); 
				$Search_result = '';
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Search_result[] = $row;
                }
                if (!empty($Search_result)){
                    if (count($Search_result)<2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }
                        $Userdeptable = array("Users"=>array($arr1));                       
                    }
                    else if(count($Search_result)==2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $Userdeptable = array("Users"=>array($arr1,$arr2)); 
                    }                  
                    else{
                        $num = count($Search_result) - 1;
                        while ($num > 2){
                            unset($Search_result[$num]);
                            $num -= 1;
                        }
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $Userdeptable = array("Users"=>array($arr1,$arr2));                         
                    }
                                       
                } 
                $sql = "select AssetsTypeName from AssetsType where AssetsTypeName like '$key'";
                $result = $conn->query($sql);
				$Search_result = '';
                while($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $Search_result[] = $row;
                }
                if (!empty($Search_result)){
                    if (count($Search_result)<2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }
                        $AssetsTypetable = array("AssetsType"=>array($arr1));                       
                    }
                    else if(count($Search_result)==2){
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $AssetsTypeptable = array("AssetsType"=>array($arr1,$arr2)); 
                    }                  
                    else{
                        $num = count($Search_result) - 1;
                        while ($num > 2){
                            unset($Search_result[$num]);
                            $num -= 1;
                        }
                        $arr = $Search_result[0];
                        foreach ($arr as $key=>$value){
                            $arr1 = array("key"=>"$key","value"=>"$value");
                        }    
                        $arr = $Search_result[1];
                        foreach ($arr as $key=>$value){
                            $arr2 = array("key"=>"$key","value"=>"$value");
                        }
                        $AssetsTypetable = array("AssetsType"=>array($arr1,$arr2));                         
                    }
                                       
                }
				//echo json_encode($Usertable);
				$data = array();
                if (!empty($Usertable) or !empty($Assetstable) or !empty($AssetsTypetable) or !empty($Userdeptable)){   
                    if (!empty($Usertable) and empty($Assetstable) and empty($AssetsTypetable) and empty($Userdeptable)){
                        $data = array($Usertable);    
					
                    }
                    else if(!empty($Usertable) and !empty($Assetstable) and empty($AssetsTypetable) and empty($Userdeptable)){
                        $data = array($Usertable,$$Assetstable);
					
                    
                    }
                    else if(empty($Usertable) and empty($Assetstable) and !empty($AssetsTypetable) and empty($Userdeptable)){
                        $data = array($AssetsTypetable);
						
                    }
                    else if (empty($Usertable) and empty($Assetstable) and empty($AssetsTypetable) and !empty($Userdeptable)){
                        $data = array($Userdeptable);
					
                    }
				}
                    $jsondata = array("UserName"=>$UserName,"TokenID"=>$TokenID,"Status"=>$status,"Data"=>$data);
                    echo json_encode($jsondata);
                
                
                //echo json_encode('123');
            }
            else{
                $status='104';
				$data = array("Status"=>$status);
                echo json_encode($data);
            }
        }
        else{
            $status = '103';
            $data = array("Status"=>$status);
            echo json_encode($data);     
        }
		$obj->operation_time($userid);
        $conn->close();
    
    }

$arr = json_decode($_GET['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];    
$Keyword = $arr["Keyword"];
get_fuzzy_search($TokenID,$UserName,$Keyword);
?>	
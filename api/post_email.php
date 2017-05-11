<?php
/*发送邮件方法
 *@param $to：接收者 $title：标题 $content：邮件内容
 *@return bool true:发送成功 false:发送失败
 */
// function post_email($to,$title,$content){



// $arr = json_decode($_POST['data'],true);
// $TokenID = $arr["TokenID"];
// $UserName = $arr["UserName"];
// $EvaluationID = $arr["EvaluationID"];
// $ExaminerID = $arr['ExaminerID'];

// $flag = post_email($TokenID,$Username,$ExaminerID,$EvaluationID);
// if($flag){
//     echo "发送邮件成功！";
// }else{
//     echo "发送邮件失败！";
// }

// function post_email($to,$title,$content){

//     //引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
//     require_once("phpmailer/class.phpmailer.php"); 
//     require_once("phpmailer/class.smtp.php");
//     //实例化PHPMailer核心类
//     $mail = new PHPMailer();

//     //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//     $mail->SMTPDebug = 1;

//     //使用smtp鉴权方式发送邮件
//     $mail->isSMTP();

//     //smtp需要鉴权 这个必须是true
//     $mail->SMTPAuth=true;

//     //链接qq域名邮箱的服务器地址
//     $mail->Host = ' smtp.mxhichina.com';

//     //设置使用ssl加密方式登录鉴权
//     $mail->SMTPSecure = 'ssl';

//     //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
//     $mail->Port = 465;

//     //设置smtp的helo消息头 这个可有可无 内容任意
//     // $mail->Helo = 'Hello smtp.qq.com Server';

//     //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
//     $mail->Hostname = 'http://localhost';

//     //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
//     $mail->CharSet = 'UTF-8';

//     //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
//     $mail->FromName = '测试实验室';

//     //smtp登录的账号 这里填入字符串格式的qq号即可
//     $mail->Username ='chenkai@sibat.cn';

//     //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
//     
//     //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
//     $mail->Password = 'Cc!5293657';

//     //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
//     $mail->From = 'chenkai@sibat.cn';

//     //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
//     $mail->isHTML(true); 

//     //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
//     $mail->addAddress($to,'在线通知');

//     //添加多个收件人 则多次调用方法即可
//     // $mail->addAddress('xxx@163.com','lsgo在线通知');

//     //添加该邮件的主题
//     $mail->Subject = $title;

//     //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
//     $mail->Body = $content;

//     //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
//     // $mail->addAttachment('./d.jpg','mm.jpg');
//     //同样该方法可以多次调用 上传多个附件
//     // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

//     $status = $mail->send();

//     //简单的判断与提示信息
//     if($status) {
//         return true;
//     }else{
//         return false;
//     }
// }


// $flag = post_email('guoshusheng@sibat.cn','在线通知','!!!!!!!!!!1');
// if($flag){
//     echo "发送邮件成功！";
// }else{
//     echo "发送邮件失败！";
// }
// require_once("phpmailer/class.phpmailer.php");  
// function sendMail($receiver, $sender, $sender_name, $subject, $content) { 
  
  
//   if(empty($receiver) || empty($sender) || empty($subject) || empty($content)){ 
//     return false; 
//   } 
    
//   $mail = new PHPMailer();  
  
//   //$mail->IsSMTP();        // 经smtp发送  
//   //$mail->Host = "smtp.gmail.com"; // SMTP 服务器 
//   //$mail->Port = 465;       // SMTP 端口 
//   //$mail->SMTPSecure = 'ssl';   // 加密方式 
//   //$mail->SMTPAuth = true;     // 打开SMTP认证 
//   //$mail->Username = "username";  // 用户名 
//   //$mail->Password = "password";  // 密码 
  
//   $mail->IsMail();         // using PHP mail() function 有可能會出現這封郵件可能不是由以下使用者所傳送的提示 
        
//   $mail->From = $sender;      // 发信人  
//   $mail->FromName = $sender_name;  // 发信人别名  
//   $mail->AddReplyTo($sender);    // 回覆人 
//   $mail->AddAddress($receiver);   // 收信人  
  
//   // 以html方式发送 
//   if($ishtml){ 
//     $mail->IsHTML(true); 
//   } 
  

//   $mail->Subject = $subject; // 邮件标题 
//   $mail->Body   = $content; // 邮件內容 
//   return $mail->Send();  
// } 
  
// // DEMO示例如下： 
// $receiver = '183059226@qq.com'; 
// $sender = 'chenkaidasdsa@sibat.cn'; 
// $sender_name = 'sender name'; 
// $subject = 'subjecct'; 
// $content = 'content'; 
  
// // 四种格式都可以 
// // $attachments = 'attachment1.jpg'; 
// // $attachments = array('path'=>'attachment1.jpg', 'name'=>'附件1.jpg'); 
// // $attachments = array('attachment1.jpg','attachment2.jpg','attachment3.jpg'); 
// // $attachments = array( 
// //   array('path'=>'attachment1.jpg', 'name'=>'附件1.jpg'), 
// //   array('path'=>'attachment2.jpg', 'name'=>'附件2.jpg'), 
// //   array('path'=>'attachment3.jpg', 'name'=>'附件3.jpg'), 
// // ); 
// $flag = sendMail($receiver, $sender, $sender_name, $subject, $content); 
// if($flag){
//     echo "发送邮件成功！";
// }else{
//     echo "发送邮件失败！";
// }
// echo $flag; 


// $from = 'eqwwe@sdd.cn';
// $to = "chenkai@sibat.cn";
// $title = 'sdsdasd';
// $subject = "=?UTF-8?B?".base64_encode($title)."?="; //解决标题中文乱码
// var_dump($subject);
// $body = '<a href="http://www.baidu.com" target="_blank">link</a>';
// // 实现邮件内容支持html
// $headers[] = "From: $from";
// $headers[] = "X-Mailer: PHP";
// $headers[] = "MIME-Version: 1.0";
// $headers[] = "Content-type: text/html; charset=utf8";
// $headers[] = "Reply-To: $from"; 
// mail($to, $subject, $body, implode("\r\n", $headers), "-f $from");




require 'mon.php';

function post_email($TokenID,$UserName,$ExaminerID,$EvaluationID){

	$obj = new serverinfo();
    $conn = $obj->db_connect();
    $conn->query('SET NAMES UTF8');


    $userid = $obj->get_userid($UserName);
    $Status = $obj->check_tokenid($userid,$TokenID);
        if ($Status == '0'){
            $Status = $obj->check_timeout($userid);               
            if($Status == '0'){

                $Ask = 'Evaluation';     

                $Permission = $obj->get_permission($UserName,$Ask);
                //var_dump($Permission);
                if($Permission == 'Y'){

                // //发送人邮件地址
                // $sql = "select EMail from Users where UserName = '$UserName'";
                // $result = $conn->query($sql);
                // $row = $result->fetch_array(MYSQLI_ASSOC);
                // $fromEMail = $row['EMail'];

                //接收人邮件地址
                    foreach ($ExaminerID as $key => $value) {
                        # code...
                    
                        $sql = "select EMail from Users where UserID = '$value'";
                        $result = $conn->query($sql);
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $toEMail = $row['EMail'];

                        $sql = "select EvaluationInfo.SendMailTime,EvaluationType.EvaluationName from EvaluationInfo,EvaluationType where EvaluationType.EvaluationID = EvaluationInfo.EvaluationID and EvaluationInfo.EvaluationID = '$EvaluationID' and EvaluationInfo.ExaminerID = '$value'";
                        $result = $conn->query($sql);
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $SendMailTime = $row['SendMailTime'];
                        $EvaluationName = $row['EvaluationName'];
                        $tokenid = $obj->timestamp();
                    	$tokenid .= $obj->random();
                        if($SendMailTime == ''){

        					$sql = "insert into mailinfo values('$EvaluationID','$value','$tokenid','$toEMail')";

        					$conn->query($sql);
        					$conn->commit();
                        }
                        else{

                        	$sql = "update mailinfo set TokenID = '$tokenid' where EvaluationID='$EvaluationID' and ExaminerID = '$value'";
        					$conn->query($sql);
        					$conn->commit();
                        }

                        


                        $from = 'evaluation@score.com';
        				$to = $toEMail;
        				//var_dump($to);
        				//$to = "chenkai@sibat.cn";
        				
        				$title = $EvaluationName;
        				//var_dump($title);
        				$subject = "=?UTF-8?B?".base64_encode($title)."?=";
        				//var_dump($subject);
        				$link = "http://192.168.40.7/grade/grade.html?TokenID=$tokenid";


        				$body = '<a href="'.$link . '" target="_blank">考核界面</a>';
        				//var_dump($body);

        				// 实现邮件内容支持html
        				$headers[] = "From: $from";
        				$headers[] = "X-Mailer: PHP";
        				$headers[] = "MIME-Version: 1.0";
        				$headers[] = "Content-type: text/html; charset=utf8";
        				$headers[] = "Reply-To: $from";
        				mail($to, $subject, $body, implode("\r\n", $headers), "-f $from");

        				$now  = $obj->timestamp();

                        $sql = "update  EvaluationInfo set SendMailTime = '$now'  where EvaluationID = '$EvaluationID'  and ExaminerID = '$value'";
                        $conn->query($sql);
                        $conn->commit();

        				$conn->close();

        	            $data = array("UserName" => $UserName, "TokenID" => $TokenID, "Status" => $Status,"SendMailTime"=>$now);

        	            $Data = json_encode($data);
        	            echo $Data;
        	            $conn->close();
                    }
                }
                else{
                    $Status = '404';
                    $data = array("Status" => $Status);
                    $data = json_encode($data);
                    echo $data;
                } 
            }
        else {
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
	$conn->close();
}
$arr = json_decode($_POST['data'],true);
$TokenID = $arr["TokenID"];
$UserName = $arr["UserName"];
$EvaluationID = $arr["EvaluationID"];
$ExaminerID = $arr['ExaminerID'];


// $UserName= "dushuai";
// $TokenID="14933621333396400902";
// $EvaluationID = 1;
// $ExaminerID = 2;

// var_dump($ExaminerID);
// var_dump(31232);
post_email($TokenID,$UserName,$ExaminerID,$EvaluationID);

?> 
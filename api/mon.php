<?php
class serverinfo{
    public $servername = "172.20.104.33";
    public $user = "root";
    public $password = "root123";
    public $dbname = "infosystem";
    public $port = 3306;

    function  db_connect(){
        $conn = new mysqli($this->servername, $this->user, $this->password, $this->dbname);
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        // $program_char = "utf8" ;
        // mysqli_set_charset( $con , $program_char );
        return $conn;
    }

    function timestamp(){
        $timenum = strtotime('now');
        return (string)$timenum;

    }

    function random(){
        $randnum = rand(1000000000,9999999999);
        return (string)$randnum;
    }

    function get_userid($UserName){
        $conn = $this->db_connect();
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        $sql = "select UserID from Users where UserName = '$UserName'";
        $result = $conn->query($sql);
        if($row = $result->fetch_row()){
            $userid = $row[0];
        }
        $conn->close();
        return $userid;
    }
    function get_permission($UserName,$Ask){
        $conn = $this->db_connect();
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        $sql = "select GroupID from Users where UserName = '$UserName'";
        $result = $conn->query($sql);
        if($row = $result->fetch_row()){
            $groupid = $row[0];
        }

        $sql = "select $Ask from Groups where GroupID = '$groupid'";

        $result = $conn->query($sql);
        $row = $result->fetch_array();
        $Permission = $row[0];

        $conn->close();
        return $Permission;
    }

    function get_logintime($userid){
        $conn = $this->db_connect();
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        $sql = "select LoginTime from UsersLoginLog where UserID = '$userid'";
        $result = $conn->query($sql);
        if($row = $result->fetch_row()){
            $logintime = $row[0];
        }
        $conn->close();
        return (string)$logintime;
    }

    function check_tokenid($userid,$tokenid){
        $conn = $this->db_connect();
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        $sql = "select TokenID from UsersLoginLog where UserID = '$userid'";
        $result = $conn->query($sql);
        if($row = $result->fetch_row()){
            $TokenID = $row[0];
        }
        if ($tokenid == $TokenID){
            $status = '0';
        }
        else{
            $status = '201';
        }
        $conn->close();
        return $status;
    }


    function operation_time($userid){
        $conn = $this->db_connect();
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        $opertime = $this->timestamp();
        //$timeout = (int)$opertime + 600;
        $timeout = (int)$opertime + 6000;
        $timeout = (string)$timeout;
        $sql = "update UsersLoginLog set TimeOut = '$timeout',OperationTime = '$opertime' where UserID = '$userid'";
        $conn->query($sql);
		$conn->commit();
        $conn->close();
        //return '0';
    }


    function check_timeout($userid){
        $conn = $this->db_connect();
        if ($conn->connect_error) {
            die("����ʧ��: " . $conn->connect_error);
        }
        $sql = "select TimeOut from UsersLoginLog where UserID = '$userid'";
        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()){
            $timeout = $row["TimeOut"];
        }
        $nowtime = $this->timestamp();
        if($nowtime > (int)$timeout){
            $status = '1';
        }
        else{
            $status ='0';
        }
        $conn->close();
        return $status;
    }


    function getmonths($day){//指定月的第一天和最后一天
        $firstday = date('Y-m-01',strtotime($day));
        $lastday = date('Y-m-d',strtotime("$firstday +1 month -1 day"));
        return array($firstday,$lastday);
    }
    function birthday2($birthday){
      list($year,$month,$day) = explode("-",$birthday);
      $year_diff = date("Y") - $year;
      $month_diff = date("m") - $month;
      $day_diff  = date("d") - $day;
      if ($day_diff < 0 || $month_diff < 0)
       $year_diff--;
      return $year_diff;
    }




    // function Pinyin($_String, $_Code='gb2312'){

    //     $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
    //         "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
    //         "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
    //         "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
    //         "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
    //         "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
    //         "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
    //         "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
    //         "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
    //         "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
    //         "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
    //         "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
    //         "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
    //         "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
    //         "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
    //         "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

    //     $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
    //         "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
    //         "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
    //         "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
    //         "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
    //         "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
    //         "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
    //         "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
    //         "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
    //         "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
    //         "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
    //         "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
    //         "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
    //         "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
    //         "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
    //         "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
    //         "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
    //         "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
    //         "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
    //         "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
    //         "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
    //         "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
    //         "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
    //         "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
    //         "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
    //         "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
    //         "|-10270|-10262|-10260|-10256|-10254";

    //     $_TDataKey = explode('|', $_DataKey);
    //     $_TDataValue = explode('|', $_DataValue);
    //     $_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : $this->Arr_Combine($_TDataKey, $_TDataValue);
    //     arsort($_Data);
    //     reset($_Data);
    //     if($_Code != 'gb2312') $_String = $this->U2_Utf8_Gb($_String);
    //     $_Res = '';
    //     for($i=0; $i<strlen($_String); $i++){
    //         $_P = ord(substr($_String, $i, 1));
    //         if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
    //         $_Res .= $this->Pinyins($_P, $_Data);
    //     }
    //     return $_Res;
    //     //return preg_replace("/[^a-z0-9]*/", '', $_Res);
    // }

    // function Pinyins($_Num, $_Data){
    //     if ($_Num>0 && $_Num<160 ) return chr($_Num);
    //     elseif($_Num<-20319 || $_Num>-10247) return '';
    //     else {
    //         foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
    //         return $k;
    //     }
    // }



    function U2_Utf8_Gb($_C){
        $_String = '';
        if($_C < 0x80){
            $_String .= $_C;
        }elseif($_C < 0x800){
            $_String .= chr(0xC0 | $_C>>6);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x10000){
            $_String .= chr(0xE0 | $_C>>12);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x200000) {
            $_String .= chr(0xF0 | $_C>>18);
            $_String .= chr(0x80 | $_C>>12 & 0x3F);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }
        return iconv('UTF-8', 'GB2312', $_String);
    }
    function Arr_Combine($_Arr1, $_Arr2){
        for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
        return $_Res;
    }
    function time2day($chai){
        $day = floor($chai/(3600*24));

        //返回字符串
        return $day;
    }
    function time2year($chai){
        $year= floor($chai/(3600*24*365));

        //返回字符串
        return $year;
    }
    // function time2birthday($chai){
    //     $remind= floor($chai/(3600*24)%365);

    //     //返回字符串
    //     return $remind;
    // }
    function zodiac($DOB){
        $DOB = date("m-d",$DOB);
        //var_dump($DOB);
        list($month,$day) = explode("-",$DOB);
        //echo $month,$day;
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;

        // 星座名称以及开始日期
        $constellations = array(
            array( "20" => "水瓶座"),
            array( "19" => "双鱼座"),
            array( "21" => "白羊座"),
            array( "20" => "金牛座"),
            array( "21" => "双子座"),
            array( "22" => "巨蟹座"),
            array( "23" => "狮子座"),
            array( "23" => "处女座"),
            array( "23" => "天秤座"),
            array( "24" => "天蝎座"),
            array( "22" => "射手座"),
            array( "22" => "摩羯座")
        );

        list($constellation_start, $constellation_name) = each($constellations[(int)$month-1]);

        if ($day < $constellation_start) list($constellation_start, $constellation_name) = each($constellations[($month -2 < 0) ? $month = 11: $month -= 2]);
        //var_dump($constellation_name);
        return $constellation_name;
    }
    function toIndexArr($arr){
        $i =0;
        foreach ($arr as $key => $value) {
            $newArr[$i] = $value;
            $i++;
        }
        return $newArr;
    }
    function nonull($aaa){
        if ($aaa == null) {
            $aaa = [];
        }
        return $aaa;
    }

}

class Cryp{
  
    private static $iv = "1234567890123412";//16
 
    const KEY = '1253689456212356';//16

    public static function init($iv = '')
    {
        self::$iv = $iv;
    }

    public static function encrypt($data = '', $key = self::KEY)
    {
        //var_dump($data);
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, self::$iv);
        return base64_encode($encrypted);
    }

    public static function decrypt($data = '', $key = self::KEY)
    {
        //var_dump(self::$iv);
       // var_dump($key);
        //var_dump($data);
        $decode = base64_decode($data);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $decode, MCRYPT_MODE_CBC, self::$iv);
	    //echo $decrypted;
       // var_dump(rtrim($decrypted, "\0"));   
        return rtrim($decrypted, "\0");
    }
}

?>

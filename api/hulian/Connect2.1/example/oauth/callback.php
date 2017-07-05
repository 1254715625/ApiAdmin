<?php
require_once("../../API/qqConnectAPI.php");
$qc = new QC();
$acs = $qc->qq_callback();//callback主要是验证 code和state,返回token信息，并写入到文件中存储，方便get_openid从文件中读
$oid = $qc->get_openid();//根据callback获取到的token信息得到openid,所以callback必须在openid前调用
setcookie('token',$acs,time()+3600,'/','');
setcookie('openid',$oid,time()+3600,'/','');

$qc = new QC($acs,$oid);

$uinfo = $qc->get_user_info();

if($uinfo){
    $mysqli=new mysqli('118.178.224.188','root','web6328','haohuisua');
    $mysqli->set_charset("utf8");
    $sql="select * from mcs_qq WHERE qq_name =  '".$uinfo['nickname']."' ";

    $obj_result= $mysqli->query($sql);
    $result= $obj_result->fetch_assoc();
    if(!$result){
        $sql="insert into mcs_qq (qq_name,cookie) VALUES ( '".$uinfo['nickname']."',1 )";
        $status=$mysqli->query($sql);
    }
    setcookie('qq_name',$uinfo['nickname'],time()+86400,'/','.lianmon.com');
    setcookie('qq_auth','1',time()+86400,'/','.lianmon.com');

    header("location:http://".$_SERVER['SERVER_NAME']."/home.php");

}


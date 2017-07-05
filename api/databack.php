<?php
require_once("qqConnectAPI.php");
$qc = new QC();
$_SESSION['qq_data']['access_token']=$qc->qq_callback();
$_SESSION['qq_data']['openid']=$qc->get_openid();
$qc = new QC($_SESSION['qq_data']['access_token'],$_SESSION['qq_data']['openid']);
$arr = $qc->get_user_info();
print_r($arr);
?>
<?php
@SESSION_START();
require_once("qqConnectAPI.php");
$qc = new QC();  
$acs = $qc->qq_callback();  
$oid = $qc->get_openid();  
$qc = new QC($acs,$oid);  
$uinfo = $qc->get_user_info();
print_r($uinfo);
?>
<?php

/**
 * 获取HTTP全部头信息
 */
if (!function_exists('apache_request_headers')) {
	function apache_request_headers(){
		$arh = array();
		$rx_http = '/\AHTTP_/';
		foreach ($_SERVER as $key => $val) {
			if (preg_match($rx_http, $key)) {
				$arh_key = preg_replace($rx_http, '', $key);
				$rx_matches = explode('_', $arh_key);
				if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
					foreach ($rx_matches as $ak_key => $ak_val)
						$rx_matches[$ak_key] = ucfirst($ak_val);
					$arh_key = implode('-', $rx_matches);
				}
				$arh[$arh_key] = $val;
			}
		}

		return $arh;
	}
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @param  string $auth_key 要加密的字符串
 * @return string
 * @author jry <598821125@qq.com>
 */
function user_md5($str, $auth_key = ''){
    if(!$auth_key){
        $auth_key = C('AUTH_KEY');
    }
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * @param     $url
 * @param int $timeOut
 * @return bool|mixed
 */
if (!function_exists('curlGet')) {
	function curlGet($url, $timeOut = 10){
		$oCurl = curl_init();
		if (stripos($url, "https://") !== false) {
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($oCurl, CURLOPT_TIMEOUT, $timeOut);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if (intval($aStatus["http_code"]) == 200) {
			return $sContent;
		} else {
			return false;
		}
	}
}


//上传文件

function my_file(){

	$upload = new \Think\Upload();// 实例化上传类
	$upload->maxSize   =     3145728 ;// 设置附件上传大小
	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','xml','xls');// 设置附件上传类型
	$upload->rootPath  =     'Public/Uploads/'; // 设置附件上传根目录
	$upload->savePath  =     ''; // 设置附件上传（子）目录
	// 上传文件

	$info   =   $upload->upload();
    foreach($info as $key=>$val){}
	$file_name=$upload->rootPath .$info[$key]['savepath'].$info[$key]['savename'];
    return $file_name;
}


//自动生成注册二维码
function qrcode(){
    Vendor('phpqrcode.phpqrcode');

    $api_user=M('api_user');
    $username=mt_rand('100000','9999999');

    $password=mt_rand('100000','9999999');
    $passwordes=user_md5($password);
    $date=array(
        'username'=>$username,
        'password'=>$passwordes,
    );
    $status=$api_user->add($date);
    if($status){
        $value='用户名是:'.$username . '密码是:'.$password;
    }else{
        $value='失败';
    }

    $errorCorrectionLevel = "M"; // 纠错级别：L、M、Q、H
    $matrixPointSize = "8"; // 点的大小：1到10

    $qrcode = new \QRcode();
    ob_clean();
    $png=$qrcode::png($value,false, $errorCorrectionLevel, $matrixPointSize, 2);

    if($png){
		$date=array(
			'status'=>1,
			'png'=>$png
		);
	}else{
		$date=array(
			'status'=>0,
			'png'=>''
		);
	}
	return json_encode($date);

}


//只根据主键值进行删除
/**
 * @param $table 表明
 * @param $id	  主键值
 */
function del_tables_id($table = '',$id =''){
	if(!is_string($table)){
		$date=array(
			'msg'=>'类型不对',
			'code'=>0,
		);
		return json_encode($date);die;
	}

	$tab=M($table);
	$status=$tab->where(array('id'=>$id))->delete();
	if($status){
		$date=array(
			'msg'=>'操作成功',
			'code'=>1,
		);
	}else{
		$date=array(
			'msg'=>'操作失败',
			'code'=>-1,
		);
	}

	return json_encode($date);


}

/**
 * geetest检测验证码
 */
function geetest_chcek_verify($data){
	$geetest_id=C('GEETEST_ID');
	$geetest_key=C('GEETEST_KEY');
	$geetest=new \Org\Xb\Geetest($geetest_id,$geetest_key);
	$user_id=$_SESSION['geetest']['user_id'];
	if ($_SESSION['geetest']['gtserver']==1) {
		$result=$geetest->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $user_id);
		if ($result) {
			return true;
		} else{
			return false;
		}
	}else{
		if ($geetest->fail_validate($data['geetest_challenge'],$data['geetest_validate'],$data['geetest_seccode'])) {
			return true;
		}else{
			return false;
		}
	}
}


//获取用户ip
function getIP() {
	if (getenv('HTTP_CLIENT_IP')) {
		$ip = getenv('HTTP_CLIENT_IP');
	}elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	}elseif (getenv('HTTP_X_FORWARDED')) {
		$ip = getenv('HTTP_X_FORWARDED');
	}elseif (getenv('HTTP_FORWARDED_FOR')) {
		$ip = getenv('HTTP_FORWARDED_FOR');
	}elseif (getenv('HTTP_FORWARDED')) {
		$ip = getenv('HTTP_FORWARDED');
	}else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}



//用于简单显示的
function my_print_r($date){
	echo "<pre>";
	var_dump($date);
	echo "</pre>";
}



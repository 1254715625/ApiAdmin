<?php

namespace Admin\Controller;

/**
 * 邮箱管理控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class EmailController extends BaseController {


    public function index(){
        $this->display();
    }

    /**
     * @param int $sex  判断类型
     * @param string $to 发给谁
     * @param string $subject 主题
     * @param string $body 内容
     */
    public function send_email($sex = 1,$to ='',$subject='',$body=''){

        if(!$subject){
            $subject = I('subject');
        }
        if(!$body){
            $body = I('body');
        }


        //单个用户发送邮箱
        if(I('auto') ==0){
            if(!$sex){
                $sex=I('sex','1');
            }

            if($sex == 1){
                $hz='@qq.com';
            }else{
                $hz='@163.com';
            }

            if(!$to){
                $to = I('to').$hz;
            }else{
                $to = $to.$hz;
            }

            $email=explode('@',$to);
            if($email[1] == 'qq.com'){
                for($i=1;$i<=1;$i++){
                    $this->qq_mail($to,$subject,$body);
                }
            }elseif($email[1] == '163.com'){

                $this->wangyi_email($to,$subject,$body);
            }

        //所有用户发送邮箱
        }elseif(I('auto') == 1){
            $this->all($subject,$body);
        }

    }

    //发送给所有人
    /**
     * @param $subject 主题
     * @param $body    内容
     */
    public function all($subject ='测试',$body='test' ){
        my_print_r($_SERVER);die;
        $api_client=M('api_client');
        $res=$api_client->field('email,linkman')->select();
        foreach($res as $re){
            $email=explode('@',$re['email']);

            if($email[1] == 'qq.com'){

                $this->qq_mail($re['email'],$subject,$body);


            }elseif(explode('@',$re['email'][1] == '163.com')){

                $this->wangyi_email($re['email'],$subject,$body);

            }


        }
    }


    public function add(){
        $this->display();
    }


    /*
    *网易邮箱
    *$to 收件人邮箱, $subject 标题, $body 内容
    *return bool 邮件发送是否成功
    *todo 配置信息未改
    */
    function wangyi_email($to,$subject,$body)
    {

        vendor('PHPMailer.class#phpmailer');
        vendor('PHPMailer.class#smtp');
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true; //开启认证
        $mail->Port = 25;
        $mail->Host = "smtp.163.com";
        $mail->Username = "yushuaige_aini@163.com"; //yuxi_chen00@163.com
        $mail->Password = "a123456789"; //a123456789
        $mail->AddReplyTo("yushuaige_aini@163.com", $mail->Username);//回复地址
        $mail->From = "yushuaige_aini@163.com";
        $mail->FromName = $mail->Username;
        $mail->AddAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->WordWrap = 80; // 设置每行字符串的长度
        $mail->IsHTML(true);
        $res = $mail->Send();
        if($res){
            echo 'send message success';
        }
    }


    //QQ邮箱
    function qq_mail($to,$subject,$body)
    {

        //引入加载
        vendor("PHPMailer.PHPMailerAutoload");
        $mail = new\PHPMailer;
        //$mail->SMTPDebug = 3; // Enable verbose debug output
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.qq.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = '1254715625@qq.com'; // SMTP username
        $mail->Password = 'goflmopileclfjjf'; // SMTP password
        $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465; // TCP port to connect to
        $mail->setFrom('1254715625@qq.com', $mail->Username);
        $mail->addAddress($to); // Add a recipient
        // Name is optional
        $mail->addReplyTo('1254715625@qq.com', 'php');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = 'o';
        if(!$mail->send()) {
            //输出错误信息
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo '发送成功'; //成功输出

        }
    }


    //定时测试
    public function time(){
        $sex=1;
        $to='1254715625';
        $subject='测试';
        $body='这是一个测试';
        $this->send_email($sex,$to,$subject,$body);
    }


}
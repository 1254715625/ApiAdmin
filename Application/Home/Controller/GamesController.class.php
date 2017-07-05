<?php
/**
 * 工程基类
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\Controller;


use Think\Controller;

class GamesController extends Controller
{

    public function index()
    {
        header("location:/Public/");
    }

    //判读是否登录
    public function is_login()
    {
        if (@!isset($_SESSION['auth']) && @$_SESSION['auth'] != 1) {
            $_SESSION['auth'] = 0;
        }

        $game = $_GET['game'] ? $_GET['game'] : 'mml';
        $auth = $_SESSION['auth'];

        switch ($auth) {
            case '0':

                //生成二维码
               /* include('/phpqrcode/phpqrcode.php');
                $data = 'http://www.baidu.com';
                $model = new \QRcode();
                $level = 'L';
                $size = 4;
                //第二个参数设置false，不保存直接显示，如果定了位置，则显示位置图片即可
                $model::png($data, './ma.png', $level, $size, 2);
                echo '<img src="/ma.png"/>';*/

                $this->display('login');
                break;
            case  '1';
                header("location:/Public/games/$game");
                break;
        }
    }

    //关于我们
    public function about(){
        if (isset($_SESSION['auth']) && @$_SESSION['auth'] == 1) {
            $this->display('user');
        }else{
            $this->display('login');
        }
    }


    //用户登录
    public function login(){
        if(IS_POST){
            $user=M('user');
            $username=I('username');
            $password=I('password');

            $res=$user->query("select * from user WHERE ( mobile = ".$username."  or email = ".$username." ) and password = ".$password."  ")[0];
            if($res){
                $_SESSION['auth'] = 1;
                $_SESSION['mobile']=$res['mobile'];
                $_SESSION['email']=$res['email'];
                $_SESSION['qq']=$res['qq'];
                header("location:/Home/Games");
            }else{
                echo "<script> alert('密码错误')</script>";
                $this->display();
            }
        }else{
            $this->display();
        }
    }

    //用户注册
    public function reg(){
        if(IS_POST){
            $user=M('user');
            $data['mobile']=I('mobile');
            $data['email']=I('email');
            $data['password']=I('password');
            $data['last_time']=date('Y-m-d H:i:s',time());
            $data['ip']=getIP();
            $status=$user->add($data);
            if($status){
                header("location:/Home/Games/login");
            }
        }else{
            $this->display();
        }
    }


    //退出登录
    public function login_out()
    {
        session_destroy();
        $this->display('login');
    }
}
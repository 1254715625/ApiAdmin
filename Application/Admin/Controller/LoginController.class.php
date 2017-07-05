<?php

namespace Admin\Controller;

/**
 * 登录控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class LoginController extends BaseController {

    public function index() {
        $this->display();
    }

    public function login() {
        $pass = user_md5(I('post.password'));
        $user = I('post.username');
        $userInfo = D('ApiUser')->where(array('username' => $user, 'password' => $pass))->find();
        if (!empty($userInfo)) {
            if ($userInfo['status']) {

                //保存用户信息和登录凭证
                S($userInfo['id'], session_id(), C('ONLINE_TIME'));
                /*session('uid', $userInfo['id']);
                session('mobile', $userInfo['mobile']);
                session('username', $userInfo['username']);
                session('gold', $userInfo['gold']);
                session('money', $userInfo['money']);*/
                $_SESSION=array(
                    'uid'=>$userInfo['id'],
                    'mobile'=>$userInfo['mobile'],
                    'username'=>$userInfo['username'],
                    'gold'=>$userInfo['gold'],
                    'money'=>$userInfo['money'],
                );

                //更新用户数据
                $userData = D('ApiUserData')->where(array('uid' => $userInfo['id']))->find();
                $data = array();
                if ($userData) {
                    $data['loginTimes'] = $userData['loginTimes'] + 1;
                    $data['lastLoginIp'] = get_client_ip(1);
                    $data['lastLoginTime'] = NOW_TIME;
                    D('ApiUserData')->where(array('uid' => $userInfo['id']))->save($data);
                } else {
                    $data['loginTimes'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['lastLoginIp'] = get_client_ip(1);
                    $data['lastLoginTime'] = NOW_TIME;
                    D('ApiUserData')->add($data);
                }
                $this->ajaxSuccess('登录成功');
            } else {
                $this->ajaxError('用户已被封禁，请联系管理员');
            }
        } else {
            $this->ajaxError('用户名密码不正确');
        }
    }

    public function logOut() {
        S(session('uid'), null);
        session('[destroy]');
        $this->success('退出成功', U('Login/index'));
    }

    public function changeUser() {
        if (IS_POST) {
            $data = I('post.');
            $newData = array();
            if (!empty($data['nickname'])) {
                $newData['nickname'] = $data['nickname'];
            }
            if (!empty($data['password'])) {
                $newData['password'] = user_md5($data['password']);
                $newData['updateTime'] = time();
            }
            $res = D('ApiUser')->where(array('id' => session('uid')))->save($newData);
            if ($res === false) {
                $this->ajaxError('修改失败');
            } else {
                $this->ajaxSuccess('修改成功');
            }
        } else {
            $userInfo = D('ApiUser')->where(array('id' => session('uid')))->find();
            $this->assign('uname', $userInfo['username']);
            $this->display('add');
        }
    }



    //用于postman测试用
    public function test(){
        $pass = user_md5(I('post.password','123456'));
        $user = I('post.username','root');
        $userInfo = D('ApiUser')->where(array('username' => $user, 'password' => $pass))->find();

        //保存用户信息和登录凭证
        S($userInfo['id'], session_id(), C('ONLINE_TIME'));
        session('uid', $userInfo['id']);

        var_dump($userInfo);

    }

}
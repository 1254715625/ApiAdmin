<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {


    protected $userid = '';
    protected $username = '';

    //首页弹框
    public function index(){

            $this->display();

    }

    /**
     * geetest生成验证码
     */
    public function geetest_show_verify(){
        $geetest_id=C('GEETEST_ID');
        $geetest_key=C('GEETEST_KEY');
        $geetest=new \Org\Xb\Geetest($geetest_id,$geetest_key);
        $user_id = "test";
        $status = $geetest->pre_process($user_id);
        $_SESSION['geetest']=array(
            'gtserver'=>$status,
            'user_id'=>$user_id
        );
        echo $geetest->get_response_str();
    }

    /**
     * geetest ajax 验证
     */
    public function geetest_ajax_check(){
        $data=I('post.');
        echo json_encode($data);die;
        echo intval(geetest_chcek_verify($data));
    }



    //检查是否够条件
    public function check(){

        //测试数据
        $this->userid=$_SESSION['user_id'] = 4;
        $this->username=$username=I('username','hehe');


        $api_user=M('api_user');
        $api_record =M('api_record');
        $re=$api_record->where(array('user_id'=>$this->userid,'ip'=>$_SERVER['SERVER_ADDR']))->find();

        $condition=array(
            'id'=>$this->userid,
            'username'=>$this->username
        );
        $user=$api_user->where($condition)->find();

        if($user && !$re){
            if($user['money'] >= 1000){

                $this->redirect('/Home/index/wjdc1');

            }else{
                $this->error('平台投注金额不够');
            }
        }elseif(!$user){
            $this->error('用户不存在');
        }elseif($re){
            $this->error('你已填写过,请勿重复填写');
        }
    }

    //调查详细页面
      function wjdc1(){

        $api_answers=M('api_answers');
        $count=$api_answers->count();
        $arr=$api_answers->select();

        foreach($arr as $k=>$v){
            //切割字符串 方便直接调用
            $res1=explode('|--|',$v['question1']);
            $arr[$k]['question1']=$res1[1];
            $arr[$k]['head1']=$res1[0];

            $res2=explode('|--|',$v['question2']);
            $arr[$k]['question2']=$res2[1];
            $arr[$k]['head2']=$res2[0];

            $res3=explode('|--|',$v['question3']);
            $arr[$k]['question3']=$res3[1];
            $arr[$k]['head3']=$res3[0];

            $res4=explode('|--|',$v['question4']);
            $arr[$k]['question4']=$res4[1];
            $arr[$k]['head4']=$res4[0];

            $res5=explode('|--|',$v['question5']);
            $arr[$k]['question5']=$res5[1];
            $arr[$k]['head5']=$res5[0];

            $res6=explode('|--|',$v['question6']);
            $arr[$k]['question6']=$res6[1];
            $arr[$k]['head6']=$res6[0];

            $res7=explode('|--|',$v['question7']);
            $arr[$k]['question7']=$res7[1];
            $arr[$k]['head7']=$res7[0];

            $res8=explode('|--|',$v['question8']);
            $arr[$k]['question8']=$res8[1];
            $arr[$k]['head8']=$res8[0];

            $res9=explode('|--|',$v['question9']);
            $arr[$k]['question9']=$res9[1];
            $arr[$k]['head9']=$res9[0];

            $res10=explode('|--|',$v['question10']);
            $arr[$k]['question10']=$res10[1];
            $arr[$k]['head10']=$res10[0];

            }
            $this->assign('count',$count);
            $this->assign('arr',$arr);
            $this->display();

    }


    public function submit(){

        $api_record =M('api_record');
        $api_user=M('api_user');

        //$api_user->startTrans(); 是否开启事物
        //$api_record->startTrans();

        $date=I();

        foreach($date as $key=>$val){

            //插入复选框值
            foreach($val as $r){

                $r=explode('|--|',$r);
                $dat=array(
                    'user_id'=>$_SESSION['user_id'],
                    'answers_id'=>$r[0],
                    'record_id'=>$r[1],
                    'time'=>time(),
                    'describe'=>I('desc'),
                    'ip'=>$_SERVER['SERVER_ADDR'],
                );

               $api_record->add($dat);
            }

            //插入单选框值
            $re=explode('|--|',$val);
            if($re){
                $dates=array(
                    'user_id'=>$_SESSION['user_id'],
                    'answers_id'=>$re[0],
                    'record_id'=>$re[1],
                    'time'=>time(),
                    'describe'=>I('desc'),
                    'ip'=>$_SERVER['SERVER_ADDR'],
                );
                if($dates['answers_id'] != '' && $dates['record_id'] != ''){
                    $status=$api_record->add($dates);
                }
            }
        }

        $sta=$api_user->where(array('id'=>$_SESSION['user_id']))->setInc('gold',5);


        if($status && $sta){
            $this->success('成功','/Home/index/index');
        }else{
            //$api_user->rollback(); 事物失败
            //$api_record->rollback();
        }
    }



    public function suggest(){
        $api_record=M('api_record');
        $api_user=M('api_user');
        $Model=M('');
        //$sql="select a.answers_id ,a.describe ,b.username from api_record as a ,api_user as b where a.user_id=b.id ";
        $res=$api_record->join("api_user  on  api_record.user_id = api_user.id ",'RIGHT')->select();
        var_dump($res);
    }


}
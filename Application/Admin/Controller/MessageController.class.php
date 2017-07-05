<?php

namespace Admin\Controller;
use Think\ServerAPI;


/**
 * 信息管理控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class MessageController extends BaseController {

    protected $AppKey='8e6b7339730f05662c16d5ef2b8ea5d5';
    protected $AppSecret='b3b22c21ccc7';


    //首页展示
    public function index(){

        $this->display();

    }

    /**
     * 验证码短信
     * @param $mobile 手机号
     */
    public function sendCode($mobile = ''){
        if($mobile == ''){
            $mobile=I('mobile',$_SESSION['mobile']);
        }

        $obj=new ServerAPI($this->AppKey,$this->AppSecret,'curl');
        $res=$obj->sendSmsCode($mobile,'');
        if( $res['code'] == 200){
            $arr=array(
                'status'=>1,
                'message'=>'发送成功',
                'ojb'=>$res['obj'],
            );
        }else{
            $arr=array(
                'status'=>0,
                'message'=>'发送失败',
            );
        }

        echo json_encode($arr);

    }

    /**
     * 通知类短信
     * @param string $temp 模板ID
     * @param $mobile      手机号
     * @param array $arr   所要传的参数，数字索引数组形式
     */
    public function sendTemplate($temp ='',$mobile =array(),$arr=array()){

        $re=explode('，',$arr);

        if(!$temp ==''){

            $temp=I('temp','3049566');
            $mobile=array(0=>I('mobile'));
            $arr=[0=>$re[0],1=>$re[1]];
        }


        $obj=new ServerAPI($this->AppKey,$this->AppSecret,'curl');

        $status=$obj->sendSMSTemplate($temp ,$mobile,$arr);

       // $status['code']=200;

        if($status['code'] ==200){
            $re=[
                'code'=>200,
                'msg'=>'操作成功'
            ];
        }

        echo json_encode($re);
    }



    //短信配置展示

    public function set_message(){

        $api_config=M('api_config');

        $results=$api_config->field('')->order('id asc')->select();

        if(IS_POST){

        }
        foreach($results as $k=>$v){

            $results[$k]['AppKey']=unserialize($v['value'])['AppKey'];

            $results[$k]['AppSecret']=unserialize($v['value'])['AppSecret'];
        }
        $this->assign('results',$results);

        $this->display();
    }


    //添加或修改配置
    public function set_message_add(){

        $api_config=M('api_config');

        $menu=M('api_menu');

        if(IS_POST && IS_AJAX){

            $da['AppKey']=I('AppKey');
            $da['AppSecret']=I('AppSecret');

            $re=$menu->where(array('url'=>CONTROLLER_NAME))->find();

            $date=array(
                'mid'=>0,
                'name'=>'网易云信',
                'title'=>'网易云信',
                'create_time'=>time(),
                'value'=>serialize($da),
            );

            $status=$api_config->add($date);

            if($status){
                $re=[
                    'code'=>200,
                    'msg'=>'操作成功'
                ];
            }
            echo json_encode($re);

        }else{

            $this->display();

        }
    }

    //删除配置
    public function del($id =''){
        if(!$id){
            $id=I('id');
        }
        $api_config=M('api_config');

        $status=$api_config->where(array('id'=>trim($id)))->delete();

        if($status ){
            $re['msg']='成功';
            $re['code']=1;
        }

        echo json_encode($re);

    }


}
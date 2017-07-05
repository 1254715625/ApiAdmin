<?php

namespace Admin\Controller;

/**
 * 及时通信控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class CheckController extends BaseController {

    public function index() {

        $this->display();

    }

    //获得用户数据
    public function get_date(){
        $arres="{
    \"code\": 0,
    \"msg\": \"\",
    \"data\": {
        \"mine\": {
            \"username\": \"纸飞机\",
            \"id\": \"100000\",
            \"status\": \"online\",
            \"sign\": \"在深邃的编码世界，做一枚轻盈的纸飞机\",
            \"avatar\": \"a.jpg\"
        },
        \"friend\": [
            {
                \"groupname\": \"前端码屌\",
                \"id\": 1,
                \"list\": [
                    {
                        \"username\": \"贤心\",
                        \"id\": \"100001\",
                        \"avatar\": \"a.jpg\",
                        \"sign\": \"这些都是测试数据，实际使用请严格按照该格式返回\",
                        \"status\": \"online\"
                    }
                ]
            }
        ],
        \"group\": [
            {
                \"groupname\": \"前端群\",
                \"id\": \"101\",
                \"avatar\": \"a.jpg\"
            }
        ]
    }
}";
        $user=M('api_user');
        $re=$user->where(array('id'=>$_SESSION['uid']))->find();
        $arr=[];
        if($re){
            $date=[];
            $mine=[];
            $friend=[];

            $std=new \stdClass();
            $arr['code']=0;
            $arr['msg']='';
            $mine['id']=$re['id'];
            $mine['username']=$re['nickname'];
            $mine['status']="online";
            $mine['sign']="老子是测试签名";



            $std->group['groupname']='测试群';
            $std->group['id'] ='1';
            $std->group['avatar'] ='b.jpg';

            $date['mine']=$mine;
            $date['group']=$std->group;
            $arr['data']=$date;
        }
        //echo $arres;
        echo json_encode($arr);
        //my_print_r(json_decode($arr),true);
    }


}
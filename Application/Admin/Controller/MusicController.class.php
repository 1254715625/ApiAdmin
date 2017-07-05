<?php

namespace Admin\Controller;

/**
 * 调查统计控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class MusicController extends BaseController {

    public function index() {

        $this->display();

    }

    //获取全部歌曲
    public function get_all_music(){
        $date="http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.billboard.billList&format=json&type=1&offset=0&size=50";
        $music=json_decode(file_get_contents($date),true);
        $data=array(
            'data'=>$music['song_list'],
        );

        return $this->ajaxReturn($data,'json');

    }

    //获取歌曲的信息
    public function getMusic($keyword,$flog = 1){

        $keyword=urlencode(I('keyword'));
        if($flog ==1){
            $musicapi="http://s.music.163.com/search/get/?type=1&s=".$keyword."=&limit=1";
        }else{
            $musicapi="http://s.music.163.com/search/get/?type=1&s=".$keyword."&limit=1";
        }

        $content=file_get_contents($musicapi);
        $res=json_decode($content,true);
        if($res['code'] ==200){
            $data=array(
                'data'=>$res['result']['songs'][0],
                'object'=>$res['result']['songs'][0]['audio'],
                //<embed src=".$res['audio']." autostart=\"true\" loop=\"true\" width=\"200\" height=\"200\"></embed>
            );
        }

        //my_print_r($data);die;
        echo json_encode($data);
        //echo "<embed src=".$data['audio']." autostart=\"true\" loop=\"true\" width=\"200\" height=\"200\"></embed>";
    }

    //获取音乐列表
    public function getList($flog){
        switch($flog){
            case 1: //新歌
                $url="http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.billboard.billList&format=json&type=1&offset=0&size=50";
                break;
            case 2://热歌
                $url="http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.billboard.billList&format=xml&type=2&offset=0&size=50";
                break;
            case 3://Hito中文榜
                $url="http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.billboard.billList&format=json&type=18&offset=0&size=50";
                break;
            default:
                $url="http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.radio.getCategoryList&format=json";
                break;
        }

        $content=file_get_contents($url);
        return json_decode($content,true);
    }


    public function get_lry($id){
        $arr=json_decode(file_get_contents("http://music.163.com/api/song/media?id=".$id),true);
        if($arr['lry']){
            return $arr['lry'];
        }else{

        }
    }

}
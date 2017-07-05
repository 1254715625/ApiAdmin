<?php

namespace Admin\Controller;

/**
 * 调查统计控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class LookController extends BaseController {

    public function index() {
        $api_answers = D('api_answers');
        $page=I('page',1);
        $limit=I('limit',20);
        $listInfo=$api_answers->page($page,$limit)->select();

        $this->assign('list', $listInfo);

        $this->display();
    }

    public function add() {

        if (IS_POST) {
            $date=array(
                'status'=>I('status'),
                'title'=>I('title'),
                'question1'=>"1|--|".I('question1'),
                'question2'=>"2|--|".I('question2'),
                'question3'=>"3|--|".I('question3'),
                'question4'=>"4|--|".I('question4'),
                'question5'=>"5|--|".I('question5'),
                'question6'=>"6|--|".I('question6'),
                'question7'=>"7|--|".I('question7'),
                'question8'=>"8|--|".I('question8'),
                'question9'=>"9|--|".I('question9'),
                'question10'=>"10|--|".I('question10'),
            );

            $res = M('api_answers')->add($date);
            if ($res === false) {
                $this->ajaxError(L('_OPERATION_FAIL_'));
            } else {
                $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
            }
        } else {
            $this->display();
        }
    }

    public function edit() {
        if (IS_GET) {
            $detail = D('api_answers')->where(array('id' => I('get.id')))->find();
            $this->assign('detail', $detail);
            $this->display('add');
        } elseif (IS_POST) {
            $date=array(
                'status'=>I('status'),
                'question1'=>"1|--|".I('question1'),
                'question2'=>"2|--|".I('question2'),
                'question3'=>"3|--|".I('question3'),
                'question4'=>"4|--|".I('question4'),
                'question5'=>"5|--|".I('question5'),
                'question6'=>"6|--|".I('question6'),
                'question7'=>"7|--|".I('question7'),
                'question8'=>"8|--|".I('question8'),
                'question9'=>"9|--|".I('question9'),
                'question10'=>"10|--|".I('question10'),
            );
            $res = M('api_answers')->where(array('id' => I('post.id')))->save($date);
            if ($res === false) {
                $this->ajaxError(L('_OPERATION_FAIL_'));
            } else {
                $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
            }
        } else {
            $this->ajaxError(L('_ERROR_ACTION_'));
        }
    }

    //统计详情
    public function total(){
        $api_answers=M('api_answers');
        $api_record=M('api_record');
        $id=I('id');
        $date=$api_answers->where(array('id'=>$id))->select();

        foreach($date as $k=>$v){

            for($i=1;$i<=10;$i++){
                $arr=explode('|--|',$v["question".$i.""]);
                if($arr[1] != ''){
                    $total=$api_record->where(array('answers_id'=>$id))->count();
                    $proportion=$api_record->where(array('answers_id'=>$id,'record_id'=>$arr[0]))->count();

                    $arr[0]=floor(($proportion/$total)*100);
                    $arr[2]=$proportion;

                    $date[$k]["question".$i.""]=$arr;
                }else{
                    $date[$k]["question".$i.""]='';
                }


            }
        }
        foreach ($date as $da) {}

        //my_print_r($da);

        $this->assign('date',$da);
        $this->display();
    }

    //详情
    function detail(){
        $detail = D('api_answers')->where(array('id' => I('get.id')))->find();
        $this->assign('detail', $detail);
        $this->display('detail');
    }

    public function del() {
        $id = I('post.id');
        if ($id == C('ADMIN_GROUP')) {
            $this->error(L('_VALID_ACCESS_'));
        }
        $res = D('api_answers')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError(L('_OPERATION_FAIL_'));
        } else {
            $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
        }
    }



    public function close() {
        $id = I('post.id');
        if ($id == C('ADMIN_GROUP')) {
            $this->ajaxError(L('_VALID_ACCESS_'));
        }
        $res = D('api_answers')->where(array('id' => $id))->save(array('status' => 0));
        if ($res === false) {
            $this->ajaxError(L('_OPERATION_FAIL_'));
        } else {
            $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
        }
    }

    public function open() {
        $id = I('post.id');
        $res = D('api_answers')->where(array('id' => $id))->save(array('status' => 1));
        if ($res === false) {
            $this->ajaxError(L('_OPERATION_FAIL_'));
        } else {
            $this->ajaxSuccess(L('_OPERATION_SUCCESS_'));
        }
    }


}
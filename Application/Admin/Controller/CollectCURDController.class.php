<?php
namespace Admin\Controller;

use Think\Controller;

//数据操作控制器
class CollectCURDController extends Controller {


    public function del($table='',$id=''){
        if($table =='' || $id ==''){
            $table=I('table');
            $id=I('id');
        }

        echo  del_tables_id($table,$id);

    }

}



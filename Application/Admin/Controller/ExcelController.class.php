<?php
namespace Admin\Controller;
use Think\Upload;

class ExcelController extends BaseController {


    //************************************************************************************
    // excel导入数据库开始
    //展示
    public function Insert(){
        vendor('PHPExcel.Classes.PHPExcel');

        if(IS_POST){

            $upload=$this->upload();
            echo $upload;

        }else{
            $this->display();
        }

    }

    //文件上传和excel写入数据库
    function upload(){
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','xml','xls');// 设置附件上传类型
        $upload->rootPath  =     'Public/Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件

        $info   =   $upload->upload();
        $file_name=$upload->rootPath .$info['file']['savepath'].$info['file']['savename'];



        /*判别是不是.xls文件，判别是不是excel文件*/
        if($info){
            $filePath = $file_name;//Excel文件路径
            $sheet = 0;//默认表格
            if(empty($filePath) or !file_exists($filePath)){die('file not exists');}
            $PHPReader = new \PHPExcel_Reader_Excel2007();        //建立reader对象

            if(!$PHPReader->canRead($filePath)){
                $PHPReader = new \PHPExcel_Reader_Excel5();
                if(!$PHPReader->canRead($filePath)){
                    $date=array(
                        'msg'=>'插入失败，请检查文件是否按照要求的格式',
                        'status'=>0,
                    );
                    return json_encode($date);
                }
            }
            $PHPExcel = $PHPReader->load($filePath);        //建立excel对象

            $currentSheet = $PHPExcel->getSheet($sheet);        //**读取excel文件中的指定工作表*/
            $allColumn = $currentSheet->getHighestColumn();        //**取得最大的列号*/
            $allRow = $currentSheet->getHighestRow();        //**取得一共有多少行*/
            $excelData = array();
            for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){        //循环读取每个单元格的内容。注意行从1开始，列从A开始
                for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
                    $addr = $colIndex.$rowIndex;
                    $cell = $currentSheet->getCell($addr)->getValue();
                    if($cell instanceof \PHPExcel_RichText){ //富文本转换字符串
                        $cell = $cell->__toString();
                    }
                    $excelData[$rowIndex][$colIndex] = $cell; //生成的数据
                }
            }
            $output = array_slice($excelData, 1, count($excelData));
            //表格数据
            $api_user=M('api_user');

            $array=[];
            foreach($output as $v ){

                $arr['user_name']=$v['A'];
                $arr['nickname']  =$v['B'];
                $arr['password']  =user_md5($v['C']);
                $res=$api_user->where(array('username'=>$v['A']))->select();
                if($res){
                    $date=array(
                        'regIp'=>$_SERVER['SERVER_ADDR'],
                        'password'=>$arr['password'],
                        'updateTime'=>time(),
                    );
                    $status = $api_user->where(array('nickname'=>$v['B']))->save($date);
                }else{
                    $date=array(
                        'username'=> $arr['user_name'],
                        'nickname'=>$arr['nickname'],
                        'password'=>$arr['password'],
                        'regIp'=>$_SERVER['SERVER_ADDR'],
                        'updateTime'=>time(),
                    );
                    $status = $api_user->add($date);
                }
            }


            if($status ){
                $date=array(
                    'msg'=>'插入成功',
                    'status'=>1,
                    'file_name'=>$file_name
                );
            }else{
                $date=array(
                    'msg'=>'插入失败',
                    'status'=>0,
                    'file_name'=>'插入失败，请检查文件是否按照要求的格式'
                );
            }
        }else{
            $date=array(
                'msg'=>'上传失败',
                'status'=>-1,
                'file_name'=>'上传失败'
            );
        }

        return json_encode($date);

    }


    // excel导入数据库结束
    //***********************************************************************************








   //***********************************************************************************
    //数据库导出到excel开始

    public function index()
    {
        $this->display();

    }


    //查询数据库
    public function get_tables(){

        $keyword=trim(I('keyword',''));

        $sql="show databases";
        if($keyword !=''){
            $sql ="show databases like '%".$keyword."%'  ";
        }
        $results=M()->query($sql);
        foreach($results as $key=>$val){
            $results[$key][$val['Tables_in_'.C('DB_NAME')]]=$val['Tables_in_'.C('DB_NAME')];

            if($keyword != ''){
                $results[$key]['Database']=$val['Database '."(%".$keyword."%)"];
            }

            $results[$key]['id']=$key;

        }
        $data=array(
            'data'=>$results,
        );

        $this->ajaxReturn($data, 'json');
    }


    //生成具体数据库结构
    public function remove(){

        $database=trim(I('keyword','test'));
        if($database!=''){
            C('DB_NAME',$database);
        }

        //读取库里所有的表
        $sql="show tables from ".C('DB_NAME')." ";
        $result=M()->query($sql);

        foreach ($result as $k=>$v) {
            $k++;
            $_sql="SHOW FULL COLUMNS FROM ".C('DB_NAME').'.'.$v['Tables_in_'.C('DB_NAME')];
            $data[][0]=array($v['Tables_in_'.C('DB_NAME')].$v['tables_in_'.C('DB_NAME')]."表",'','','','','','');
            $data[][1]=array("字段","类型","校对","NULL","键","默认","额外","权限","注释");
            $data[]=M()->query($_sql);
            $data[][]=array();
        }


        vendor('PHPExcel.Classes.PHPExcel');
        vendor('PHPExcel.Classes.PHPExcel.Writer.Classes');
        vendor('PHPExcel.Classes.PHPExcel.IOFactory.php');
        $filename="test_excel";

        $this->getExcel($filename,$data);

    }

    private function getExcel($fileName,$data){
        //对数据进行检验
        if(empty($data)||!is_array($data)){
            die("data must be a array");
        }

        $date=date("Y_m_d",time());
        $fileName.="_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();
        $objProps=$objPHPExcel->getProperties();

        $column=2;
        $objActSheet=$objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);//设置默认高度

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('5');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('40');//设置列宽

        //设置边框
        $sharedStyle1=new \PHPExcel_Style();
        $sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));

        foreach ($data as $ke=>$row){

            foreach($row as $key=>$rows){

                if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:J{$column}");//设置边框
                    array_unshift($rows,$rows['0']);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:J{$column}");//合并单元格
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFont()->setSize(12);//字体
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFont()->setBold(true);//粗体

                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');

                }else{
                    if(!empty($rows)){
                        array_unshift($rows,$key+1);
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:J{$column}");//设置边框
                    }
                }

                if($rows['1']=='字段'){
                    $rows[0]='ID';
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
                }

                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:J{$column}")->getAlignment()->setWrapText(true);//换行
                //行写入
                $span = ord("A");
                foreach($rows as $keyName=>$value){
                    // 列写入
                    $j=chr($span);
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }
                $column++;
            }
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }


    //数据库导出到excel结束
    //*****************************************************************
}
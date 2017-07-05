<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Think\QueryList;


//数据采集控制器
class CollectController extends Controller {

    //首页展示
    public function index(){

        $limit=I('limit',10);
        $api_client=M('api_client');

        $count=$api_client->count();
        $page=new Page($count,$limit);
        $res=$api_client->field('id,linkman,tel,cid,money,address,time')->limit($page->firstRow,$page->listRows)->order('id desc')->select();
        $this->assign('res',$res);
        $this->assign('page',$page);
        $this->display();

    }


    //模拟登陆
    public function login(){
        $curl = curl_init();
        $memberName='18256984210';
        $password='yb1992515';
        $cookie_jar =  tempnam('./tmp','cookie');

        curl_setopt($curl, CURLOPT_URL,'http://login.goodjobs.cn/index.php/action/UserLogin');//这里写上处理登录的界面
        curl_setopt($curl, CURLOPT_POST, 1);
        $request = 'memberName='.$memberName.'&password='.$password.'&backUrl:='.''.' ';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);//传 递数据
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);// 把返回来的cookie信息保存在$cookie_jar文件中
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//设定返回 的数据是否自动显示
        curl_setopt($curl, CURLOPT_HEADER, false);//设定是否显示头信 息
        curl_setopt($curl, CURLOPT_NOBODY, false);//设定是否输出页面 内容
        curl_exec($curl);//返回结果
        my_print_r($cookie_jar);
        //curl_close($curl); //关闭


        $curl2 = curl_init();

        curl_setopt($curl2, CURLOPT_URL, 'http://user.goodjobs.cn/dispatcher.php/module/Personal/');//登陆后要从哪个页面获取信息
        curl_setopt($curl2, CURLOPT_HEADER, false);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl2, CURLOPT_COOKIEFILE, $cookie_jar);
        $content = curl_exec($curl2);

    }

    //判断类型
    public function type($type ='',$keyword =''){
        if($type =='' || $keyword ==''){
            $type=I('type');
            $keyword=I('keyword');
        }

        //1  58同城  2赶集网  3智联招聘  4中华英才  5新安  6顺企网  7 114黄页

        switch($type){
            case '1':
                echo $type;
                break;
            case '2':
                break;
            case '3':
                break;
            case '4':
                break;
            case '5':
                break;
            case '6':
                if($keyword ==''){
                    echo '关键词不能为空';die;
                }
                $this->shunqi($keyword);
                break;
            case '7':
                $this->cai_114($keyword);
                break;
            default:
                break;
        }


    }

    //采集114黄页数据
    public function cai_114($keyword =''){

        if($keyword ==''){
            $keyword=I('keyword');
        }

        new QueryList('','');
        $arr=[];

        $url="http://search.114chn.com/searchresult.aspx?type=1&key=".$keyword."&pattern=2&page=";

        echo '待修复';die;

        for($i=1;$i<=1;$i++){
            \phpQuery::newDocumentFile($url.$i);
            $artlist = pq(".f");
            foreach($artlist as $key => $v){
                $date=[];
                $a = pq($v)->find('a')->text();
                $href = pq($v)->find('a')->attr('href');
                $content = pq($v)->find('.ss_jianjie')->text();
                $address = pq($v)->find('.ss_dizhi')->text();
                \phpQuery::newDocumentFile($href);
                $list2=pq("#nei-right1");
                //my_print_r(\phpQuery::newDocumentFile("http://wenxi.114chn.com/m/web/shop/index.aspx?shopid=1408230910080001"));die;
                foreach($list2 as $li){

                    $linkman = pq($li)->find('div:eq(1)')->text(); //联系人
                    $tel = pq($li)->find('div:eq(2)')->text(); //电话
                    $email = pq($li)->find('div:eq(4)')->text(); //邮箱
                    $date['linkman']=$linkman;
                    $date['email']=$email;
                    $date['tel']=trim($tel);

                }

                $date['cid']=$a;
                $date['content']=$content;
                $date['address']=$address;
                $date['money']='无';
                array_push($arr,$date);


            }
        }


        $first=[];

        $this->remove($arr,$first);
    }



    //采集顺企网数据
    public function shunqi($keyword = ''){
        if($keyword ==''){
            $keyword=I('keyword','540880a5');
        }

        new QueryList('','');
        $arr=[];

        $url="http://b2b.11467.com/search/-".$keyword."-";
        $api_client=M('api_client');

        for($i=1;$i<=5;$i++){
            \phpQuery::newDocumentFile($url."pn".$i.".htm");
            $artlist = pq(".companylist li");

            foreach($artlist as $key => $v){
                $date=[];
                $a = pq($v)->find('.f_l a')->text(); //公司名
                $href = pq($v)->find('.f_l a')->attr('href');  //二级页面链接
                $content = pq($v)->find('.f_l > div:eq(0) ')->text();  //经营品种
                $address = pq($v)->find('.f_l > div:eq(1)')->text();  //地址
                $money = pq($v)->find('.f_l > div:eq(2)')->text();  //注册资本
                $time = pq($v)->find('.f_r > div:eq(0)')->text();  //成立时间
                if($time =='电话：'){
                    $time='未填写';
                }

                \phpQuery::newDocumentFile($href);

                $list2=pq("#contact");
                //my_print_r(\phpQuery::newDocumentFile($href));die;
                foreach($list2 as $li){

                    $tel     = pq($li)->find('dl > dt:eq(1)')->text().(pq($li)->find('dl > dd:eq(1)')->text());  //电话
                    $linkman = pq($li)->find('dl > dt:eq(2)')->text().(pq($li)->find('dl > dd:eq(2)')->text()); //联系人
                    $email   = pq($li)->find('dl > dt:eq(4)')->text().(pq($li)->find('dl > dd:eq(4)')->text()); //电子邮件
                    $date['linkman']=$linkman;   //联系人
                    $date['email']=trim($email); //邮箱
                    $date['tel']=trim($tel);     //电话

                }


                if($a){
                    $date['cid']=$a; //公司
                    $date['content']=$content; //内容
                    $date['address']=$address; //住址
                    $date['money']= $money == '' ? '未填写':$money ;     //金额
                    $date['time']=$time;       //时间
                }


                //有数据时才插入
                if($list2){

                    $now['cid']  =$date['cid'];
                    $now['linkman']  =$date['linkman'];
                    $now['email']=explode('电子邮件：',$date['email'])[1];
                    $now['tel']  =$date['tel'];//explode('联系电话：',$date['tel'])[1]
                    $now['money']  =explode('工商注册：',$date['money'])[1];
                    $now['content']  =explode('主营产品：',$date['content'])[1];
                    $now['content']  =explode('主营产品：',$date['content'])[1];
                    $now['address']  =explode('地址： ',$date['address'])[1];
                    $now['time']  =explode('成立时间：',$date['time'])[1];

                    if($now['cid'] != ''){

                        $res=$api_client->where(array('cid'=>$now['cid']))->find();
                        if(!$res){
                            $api_client->add($now);
                        }else{
                            $api_client->where(array('cid'=>$now['cid']))->save($now);
                        }

                    }

                    //echo M('')->getLastSql();
                    array_push($arr,$date);
                    //my_print_r($now);
                }

            }
        }


        $first=array(
            'cid'=>'公司',
            'address'=>'住址',
            'money'=>'金额',
            'linkman'=>'联系人',
            'tel'=>'电话',
            'email'=>'邮箱',
            'time'=>'时间',
            'content'=>' 内容',
        );

        $this->remove($arr,$first);

    }



    //生成表格
    public function remove($arr,$first = '')
        {

            $mulit_arr=$arr;

            vendor("PHPExcel.Classes.PHPExcel");

            array_unshift($mulit_arr,$first);


            $obpe=new \PHPExcel();

            /* @func 设置文档基本属性 */
            $obpe_pro = $obpe->getProperties();
            $obpe_pro->setCreator('1254715625')//设置创建者
            ->setLastModifiedBy(date('Y-m-d',time()))//设置时间
            ->setTitle('data')//设置标题
            ->setSubject('beizhu')//设置备注
            ->setDescription('miaoshu')//设置描述
            ->setKeywords('keyword')//设置关键字 | 标记
            ->setCategory('catagory');//设置类别


            /* 设置宽度 */
            //$obpe->getActiveSheet()->getColumnDimension()->setAutoSize(true);
            $obpe->getActiveSheet()->getColumnDimension('A')->setWidth(40);
            $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(55);
            $obpe->getActiveSheet()->getColumnDimension('C')->setWidth(35);
            $obpe->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $obpe->getActiveSheet()->getColumnDimension('E')->setWidth(40);
            $obpe->getActiveSheet()->getColumnDimension('F')->setWidth(40);
            $obpe->getActiveSheet()->getColumnDimension('G')->setWidth(40);
            $obpe->getActiveSheet()->getColumnDimension('H')->setWidth(300);
            //设置当前sheet索引,用于后续的内容操作
            //一般用在对个Sheet的时候才需要显示调用
            //缺省情况下,PHPExcel会自动创建第一个SHEET被设置SheetIndex=0

            //设置SHEET
            $obpe->setactivesheetindex(0);
            //写入多行数据
            foreach($mulit_arr as $k=>$v){
               // my_print_r($v);
                $k = $k+1;
                /* @func 设置列 */
                $obpe->getactivesheet()->setcellvalue('A'.$k, $v['cid']);     //标题
                $obpe->getactivesheet()->setcellvalue('B'.$k, $v['address']);  //公司
                $obpe->getactivesheet()->setcellvalue('C'.$k, $v['money']); //地址
                $obpe->getactivesheet()->setcellvalue('D'.$k, $v['linkman']);   //金额
                $obpe->getactivesheet()->setcellvalue('E'.$k, $v['tel']); //联系人
                $obpe->getactivesheet()->setcellvalue('F'.$k, $v['email']);     //电话
                $obpe->getactivesheet()->setcellvalue('G'.$k, $v['time']); //邮箱
                $obpe->getactivesheet()->setcellvalue('H'.$k, $v['content']);     //时间

               // $obpe->getactivesheet()->setcellvalue('H'.$k, $v['']);

            }
           // die;

            $obwrite = \PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
            ob_end_clean();
            $file_name=date('Y-m-d H-i',time());

            //是否在本地项目根路径也下载一份
            $obwrite->save(date('Y-m-d H-i',time()).'.xls');


            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Content-Type:application/force-download');
            header('Content-Type:application/vnd.ms-execl');
            header('Content-Type:application/octet-stream');
            header('Content-Type:application/download');
            header("Content-Disposition:attachment;filename='".$file_name.".xls'");
            header('Content-Transfer-Encoding:binary');
            $obwrite->save('php://output');

        }







    //采集新安同城数据
    function search_xinan(){
        /*$search='工作';
       $search=urlencode($search);
       */

        new QueryList('','');
        $arr=[];

        $url="http://search.goodjobs.cn/index.php?keyword=&kt=0&boxwp=c1043&industrytype=0&boxft=0";


        \phpQuery::newDocumentFile($url);
        $artlist = pq(".even");


        foreach($artlist as $key => $v){

            $date=[];
            $date['cid'] = trim(pq($v)->find('.cor_7')->text()); //标题
            $date['gongsi']= pq($v)->find('.a07')->text();    //公司
            $date['address']= pq($v)->find('td:eq(4)')->text();    //住址
            $date['money']= pq($v)->find('td:eq(3)')->text();    //金额
            $date['xingzhi']= '无';    //性质
            $people=trim(pq($v)->find('.fl1:eq(0))')->text());    //人数
            $replace=str_replace('招聘人数：','',$people);
            $date['people']=str_replace(' |','',$replace).'人';
            $date['linkman']= pq($v)->find('.tac')->text();    //联系人
            $date['tel']= pq($v)->find('.tac')->text();    //电话
            $date['time']= pq($v)->find('.tac')->text();    //时间

            $href= pq($v)->find('.cor_7')->attr('href');    //公司

            \phpQuery::newDocumentFile($href);
            $list2=pq(".w706");

            foreach($list2 as $li){

                $score = pq($li)->find('.duol img')->attr('src'); //积分
                $name = pq($li)->find('.name ')->text(); //用户名
                $level = pq($li)->find('.promulgator span:eq(2)')->text(); //等级
                $date['score']=$score;
                $date['level']=$level;
                $date['name']=trim($name);

                my_print_r($list2);die;
            }


            array_push($arr,$date);
        }


        // my_print_r($arr);
        die;

        $first=array(
            'cid'=>'标题',
            'gongsi'=>'公司',
            'address'=>'住址',
            'money'=>'金额',
            'xingzhi'=>'性质',
            'people'=>'人数',
            'linkman'=>'联系人',
            'tel'=>'电话',
            'time'=>'时间',
        );

        $this->remove($arr,$first);
    }







    //采集中华英才网
    public function zhonghua(){


        /*$search='工作';
        $search=urlencode($search);
        */

        new QueryList('','');
        $arr=[];

        $url="http://www.thinkphp.cn/";

        for($i=1;$i<=1;$i++){
            \phpQuery::newDocumentFile("http://www.chinahr.com/sou/?orderField=relate&city=18,193&page=".$i);
            $artlist = pq(".resultList .jobList");

            foreach($artlist as $key => $v){
                //my_print_r($v);
                $date=[];
                $cid = pq($v)->find('.e1 > a')->text();
                $time= pq($v)->find('li:eq(0) .e2')->text();
                $gongsi= pq($v)->find('.e3 a')->text();
                $money= pq($v)->find('li:eq(1) .e2')->text();
                $address= pq($v)->find('li:eq(1) .e1')->text();
                $xingzhi= pq($v)->find('li:eq(1) .e3  em:eq(1)')->text();
                $people= pq($v)->find('li:eq(1) .e3  em:eq(2)')->text();

                $date['cid']=$cid; //标题
                $date['gongsi']=$gongsi; //公司
                $date['address']=trim($address); //地址
                $date['money']=$money;  //金额
                $date['xingzhi']=trim($xingzhi); //性质
                $date['people']=trim($people); //人数
                $date['time']=$time;  //时间


                /*          \phpQuery::newDocumentFile($url.$href);
                            $list2=pq(".sidebar");

                            foreach($list2 as $li){

                                $score = pq($li)->find('.mr')->text(); //积分
                                $name = pq($li)->find('.name ')->text(); //用户名
                                $level = pq($li)->find('.promulgator span:eq(2)')->text(); //等级
                                $date['score']=$score;
                                $date['level']=$level;
                                $date['name']=trim($name);
                            }*/

                array_push($arr,$date);

            }
        }

        // my_print_r($arr);

        $first=array(
            'cid'=>'标题',
            'gongsi'=>'公司',
            'address'=>'住址',
            'money'=>'金额',
            'xingzhi'=>'性质',
            'people'=>'人数',
            'time'=>'时间',
        );

        $this->remove($arr,$first);

    }



}


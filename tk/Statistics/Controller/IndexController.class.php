<?php
/**
 * 统计
 * @author demo 15-12-28
 */
namespace Statistics\Controller;
use Common\Controller\DefaultController;
header('content-Type:text/html;charset=utf8');
class IndexController extends DefaultController{
    public function index(){
		$pageName = '教师列表';
		$user=M('user');
		// dump($_COOKIE);
		$post=I('post.');
		// dump($post);
		//从cookie中获取用户名，判断是哪所学校的。
		// $uid = $_COOKIE['_UID'];
		// $sid = $user->field('SchoolID')
		// ->where('UserID='.$uid)->find();
		// dump($sid['SchoolID']);
		// $userName = $this->getCookieUserName();
		// dump($userName);die;
		if($post == null){
			$where=array('whois=1','SchoolID=1');
			// dump($where);die;
			$data=$user->field('UserName,RealName,Logins,Whois,Phonecode,Email')
			// ->where('whois=1 and SchoolID=1')
			->where($where)
			->select();
			 // dump($data);
		}else{
			$where=array('whois=1','SchoolID=1','username='.$post['name']);
			// dump($where);die;
			$data=$user->field('UserName,RealName,Logins,Whois,Phonecode,Email')
			// ->where('whois=1 and SchoolID=1')
			->where($where)
			->select();
			 // dump($data);
		}
		
		 // $page_length=C('WLN_PERPAGE');
		 // $page_length=3;
         // $num=count($data);
         // $page_total=new \Think\Page($num,$page_length);
         // $cart_data=array_slice($data,$page_total->firstRow,$page_total->listRows);
         // $show=$page_total->show();
		
		$count=count($data); //获取总数
		// $perpage = C('WLN_PERPAGE'); //每页显示数
		$perpage = 2; //每页显示数
        $page=page($count,$_GET['p'],$perpage); //分页条件
		// dump($page);
        $startPage=($page-1)*$perpage; //计算开始位置
        $cart_data=array_slice($data,$startPage,$perpage);
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->pageList($count, $perpage);
		
		
		
		// dump($data);
		//处理成json数据
		foreach($cart_data as $i=>$idata){
			$arr0[]=$idata['UserName'];
			$arr1[]=$idata['RealName'];
			$arr2[]=(int)$idata['Logins'];
		}
		// dump($arr0);
		$data0=array(
			'1'=>'1',
			"UserName"=>$arr0[0]
		);
		$data1=array(
			'1'=>'1',
			"UserName"=>$arr0[1]
		);
		$data2=array(
			'1'=>'1',
			"UserName"=>$arr0[2]
		);
		$data3=array(
			'1'=>'1',
			"UserName"=>$arr0[3]
		);
		$data4=array(
			'1'=>'1',
			"UserName"=>$arr0[4]
		);
		$data5=array(
			'1'=>'1',
			"UserName"=>$arr0[5]
		);
		$data6=array(
			'1'=>'1',
			"UserName"=>$arr0[6]
		);
		$data7=array(
			'1'=>'1',
			"UserName"=>$arr0[7]
		);
		$data8=array(
			'1'=>'1',
			"UserName"=>$arr0[8]
		);
		$data9=array(
			'1'=>'1',
			"UserName"=>$arr0[9]
		);
		
		$count0=$this->getModel('UserWork')->unionSelect('userWorkCount', $data0);
		$count1=$this->getModel('UserWork')->unionSelect('userWorkCount', $data1);
		$count2=$this->getModel('UserWork')->unionSelect('userWorkCount', $data2);
		$count3=$this->getModel('UserWork')->unionSelect('userWorkCount', $data3);
		$count4=$this->getModel('UserWork')->unionSelect('userWorkCount', $data4);
		$count5=$this->getModel('UserWork')->unionSelect('userWorkCount', $data5);
		$count6=$this->getModel('UserWork')->unionSelect('userWorkCount', $data6);
		$count7=$this->getModel('UserWork')->unionSelect('userWorkCount', $data7);
		$count8=$this->getModel('UserWork')->unionSelect('userWorkCount', $data8);
		$count9=$this->getModel('UserWork')->unionSelect('userWorkCount', $data9);
	
		
		$str=array(
			'0'=>(int)$count0,
			'1'=>(int)$count1,
			'2'=>(int)$count2,
			'3'=>(int)$count3,
			'4'=>(int)$count4,
			'5'=>(int)$count5,
			'6'=>(int)$count6,
			'7'=>(int)$count7,
			'8'=>(int)$count8,
			'9'=>(int)$count9,
		);
		// dump($str);die;
		
		$datanum=json_encode($str);
		
		
		$dataname=json_encode($arr1);
		
		$datalogins=json_encode($arr2);
		// dump($dataname);
		$total=count($data);
		// $this->setBack($arr0);
        $this->assign('datanum',$datanum);
        $this->assign('pageName',$pageName);
        $this->assign('dataname',$dataname);
        $this->assign('datalogins',$datalogins);
        $this->assign('total',$total);
        // $this->assign('page',$show);
        $this->assign('cart_data',$cart_data);
        $this->display();
		
    }
	

    /**
     * 首页ajax返回更新的数据
     * @author demo
    */
    public function home(){
        //手工更新统计信息
        if(isset($_GET['handwork'])){
            D('StatisticsHome')->getUpgradeData1();
            exit;
        }
        $stat = D('StatisticsHome')->getUpgradeData();
        if(empty($stat)){
            $testTjArr = array('stat'=>'error');
        }else{
            $testTjArr = array(
              'TjTotalTest' => $stat['last']['StatTest'],
              'TjTotalDoc' => $stat['last']['StatDoc'],
              'TjTotalZj' =>  $stat['last']['StatZj'],
              'TjVisit' =>  $stat['last']['StatVisit'],
              'bTotalTest' => $stat['month']['StatTest'],
              'bTotalDoc' => $stat['month']['StatDoc'],
              'bTotalZj' => $stat['month']['StatZj'],
              'sTotalTest' => $stat['week']['StatTest'],
              'sTotalDoc' => $stat['week']['StatDoc'],
              'sTotalZj' => $stat['week']['StatZj'],
              'yTotalTest' => $stat['yesterday']['StatTest'],
              'yTotalDoc' => $stat['yesterday']['StatDoc'],
              'yTotalZj' => $stat['yesterday']['StatZj']
            );
        }
        $this->setBack($testTjArr);
    }

    /**
     * ajax 获取统计数据
     */
    public function totalMsg(){
        if(!empty($_POST['flag'])){
            $flagStr=$_POST['flag'];
            $result=D('Statistics')->totalSystem($flagStr);
            $this->setBack($result);
        }
    }

    /**
     * 写入历史数据，仅限于首次更新
     * @author demo
    */
    public function upgrade(){
        D('StatisticsHome')->insertHistory();
    }
}
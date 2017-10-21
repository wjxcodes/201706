<?php
/**
 * 统计
 * @author demo 15-12-28
 */
namespace Statistics\Controller;
use Common\Controller\DefaultController;

class IndexController extends DefaultController{
    public function index(){
		// echo 222;
        $user=M('user');
		// dump($_COOKIE);
		
		// $uid = $_COOKIE['_UID'];
		// $sid = $user->field('SchoolID')
		// ->where('UserID='.$uid)->find();
		
		// dump($sid['SchoolID']);
		// $userName = $this->getCookieUserName();
		// dump($userName);die;
		// $where=array('whois=1','SchoolID='.$sid['SchoolID']);
		$data=$user->field('UserName,RealName,Logins,Whois,Phonecode,Email')
		->where('whois=1 and SchoolID=1')
		->select();
		dump($data);die;
		
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
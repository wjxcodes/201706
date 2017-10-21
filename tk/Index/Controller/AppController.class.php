<?php
/**
 * @author demo
 * @date 2015年9月11日
 */
/**
 * 题库官网专题
 */
namespace Index\Controller;
class AppController extends BaseController{
    /**
     * 安卓登录
     * @author demo
     */
    public function AppLogin(){
        if(!$_POST){
            $info['status']=0;
            $info['info'] ='缺少用户登录数据';
            exit(json_encode($info));
        }
        $info = R('Aat/App/AppLogin');
        exit($info);
    }
	
	/*
     *移动端返回地区信息
     */
    public function Area(){
        $areaID = $_REQUEST['id'];
        $areaID = 0;
        $IData=$this->getApiAat('User/areaByID',$areaID);
        if($IData[0] == 1){
            exit(json_encode($IData[1]));
        }else{
            $this->setError($IData[1],1);
        }
    }
    /**
     * 2015中秋节
     * @author demo
     */
    public function zhongqiu(){
        $this->display();
    }

    public function awards(){
        $this->display();
    }

    /**
     * 描述：感恩节活动页面
     * @author demo
     */
    public function thinksgiving(){

    }

    /**
     * 原创试题
     * @author demo
     */
    public function originalAward(){
        $this->display();
    }

    /**
     * 原创试题历史获奖名单
     * @author demo
     */
    public function originalAwardList(){
        $this->display();
    }

    /**
     * 16春节活动
     * @author demo
     */
    public function springFestival(){
        $this->display();
    }

    /**
     * 预测表
     * @author demo
     */
    public function yucebiao(){
        $urlStr='/Index/Special/yucebiao/yuwen_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/yuwen');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/wenkeshuxue_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/wenkeshuxue');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/likeshuxue_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/likeshuxue');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/yingyu_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/yingyu');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/wuli_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/wuli');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/huaxue_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/huaxue');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/shengwu_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/shengwu');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/zhengzhi_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/zhengzhi');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/lishi_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/lishi');
            exit();
        }
        $urlStr='/Index/Special/yucebiao/dili_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/yucebiao/dili');
            exit();
        }
        $this->display('Special/yucebiao/index');
    }

    public function mijuan(){
        $urlStr='/Index/Special/mijuan/2017/yucebiao/yuwen_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/yuwen');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/wenkeshuxue_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/wenkeshuxue');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/likeshuxue_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/likeshuxue');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/yingyu_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/yingyu');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/wuli_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/wuli');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/huaxue_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/huaxue');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/shengwu_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/shengwu');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/zhengzhi_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/zhengzhi');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/lishi_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/lishi');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/yucebiao/dili_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/yucebiao/dili');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/guide_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/guide');
            exit();
        }
        $urlStr='/Index/Special/mijuan/2017/kaobaliansai_html';
        if(isset($_GET[$urlStr])){
            $this->display('Special/mijuan/2017/kaobaliansai');
            exit();
        }
        $this->display('Special/mijuan/2017/index');
    }

    /**
     * 密卷
     * @author demo
     */
    public function mijuan2016(){
        $this->display();
    }
    /**
     * 密卷帮助
     * @author demo
     */
    public function mijuanHelp(){
        $this->display();
    }

    /**
     * 真题2016
     * @author demo 16-6-1
     */
    public function zhenti2016(){
        $this->display('Special/zhenti2016/zhenti2016');
    }


    public function zhentiUpload(){
        $user = $this->getCookieUserID('Home');
        if(empty($user)){
            $user = $this->getCookieUserID('Aat');
            if(empty($user)){
                header('location:'.U('User/Index/passport', array('url'=>urlencode('Index/Special/zhentiUpload'))));
                exit;
            }
        }
        $data = $this->getModel('SpecialImg')->unionSelect('getImages', 1);
        $this->assign('data', $data[1]);
        $this->display('Special/zhenti2016/zhentiUpload');
    }


    /**
     * 押题报告
     * @author demo
     */
    public function report(){
        $pageName='2016高考押中报告全国甲卷';
        $subName='全国甲卷';
        $subject='语文';
        if(!empty($_GET['part1'])){
            $subName='全国甲卷';
            $pageName='2016高考押中报告全国甲卷';
            $part='part1';
            $subjectName=$_GET['part1'];
        }
        if(!empty($_GET['part2'])){
            $subName='全国乙卷';
            $pageName='2016高考押中报告全国乙卷';
            $part='part2';
            $subjectName=$_GET['part2'];
        }
        if(!empty($_GET['part3'])){
            $subName='全国丙卷';
            $pageName='2016高考押中报告全国丙卷';
            $part='part3';
            $subjectName=$_GET['part3'];
        }

        switch($subjectName){
            case 'yuwen':
                $pageName.='语文';
                $subject='语文';
                break;
            case 'likeshuxue':
                $pageName.='理科数学';
                $subject='理科数学';
                break;
            case 'wenkeshuxue':
                $pageName.='文科数学';
                $subject='文科数学';
                break;
            case 'yingyu':
                $pageName.='英语';
                $subject='英语';
                break;
            case 'zhengzhi':
                $pageName.='政治';
                $subject='政治';
                break;
            case 'lishi':
                $pageName.='历史';
                $subject='历史';
                break;
            case 'dili':
                $pageName.='地理';
                $subject='地理';
                break;
            case 'wuli':
                $pageName.='物理';
                $subject='物理';
                break;
            case 'huaxue':
                $pageName.='化学';
                $subject='化学';
                break;
            case 'shengwu':
                $pageName.='生物';
                $subject='生物';
                break;
            default:
                $this->display('Special/report/index');
                exit();
        }

        $this->assign('subName', $subName); //试卷名称
        $this->assign('subject', $subject); //学科名称
        $this->assign('part', $part);
        $this->assign('subjectName', $subjectName);
        $this->assign('pageName', $pageName);
        $this->display('Special/report/'.$part.'/'.$subjectName);
    }

    /**
     * 考霸联赛top2017
     * @author demo
     */
    public function kblsTop2017(){
        $this->display();
    }
}
<?php
/**
 * @author demo 
 * @date 2014年8月5日
 * @update 2015年1月13日 2015年9月28日
 */
/**
 * 题库官网
 */
namespace Index\Controller;
class AboutController extends BaseController{
    /**
     * 联系我们
     * @author demo
     */
    public function about(){
        if(empty($_GET['param'])) $_GET['param'] = 'aboutOnlineS';
        switch ($_GET['param']){
            case 'aboutOnlineS':
                $tag = 'aboutOnlineS';
                $arr = array('eq'=>0,'typeName'=>'联系我们');
                break;
            case 'aboutRecruit':
                $tag = 'aboutRecruit';
                $arr = array('eq'=>2,'typeName'=>'人才招聘');
                break;
            case 'aboutPrivacy':
                $tag = 'aboutPrivacy';
                $arr = array('eq'=>3,'typeName'=>'隐私条款');
                break;
            case 'aboutFriendLinkl':
                $tag = 'aboutFriendLinkl';
                $arr = array('eq'=>4,'typeName'=>'友情链接');
                break;
            default:
                emptyUrl();
                break;
        }
        $cached=$this->cachedTag($tag);
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag($tag,$arr);
    }

    /**
     * 离线留言
     * @author demo
     */
    public function leaveMess(){
        $cached=$this->cachedTag();
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag();
    }

    /**
     * 缓存模板数据
     * @param string $tag 标签名
     * @param array $array 模板分配的变量
     * @param string $page 页数 或者  其他参数
     * @return string 模板静态页面
     * @author demo
     */
    public function cacheTag($tag='',$array=array(),$page=''){
        $tag = empty($tag) ? ACTION_NAME : $tag;  //如果传空则为当前方法名
        if(!empty($page)) $page = (int)$page;
        if(!empty($array)) {
            foreach ($array as $key => $val) {
                $this->assign($key, $val);
            }
        }
        $data = $this->fetch();
        F('Index/'.$tag.$page,$data);
        return  $data;
    }
    /**
     * 已缓存过的
     * @param string $tag 标签名
     * @return string 模板静态页面 | false 未缓存
     * @author demo
     */
    public function cachedTag($tag='',$page=''){
        $tag = empty($tag) ? ACTION_NAME : $tag;  //如果传空则为当前方法名
        if(!empty($page)) $page = (int)$page;

        return  $data = F('Index/'.$tag.$page);
    }

    /**
     * 核心系统列表
     * @author demo
     */
    public function coreSystem(){
        $cached=$this->cachedTag('coreSystem');
        if($cached){
            exit($cached);
        }

        //核心系统
        $coreSys = $this->getModel('CoreSystem')->getInfo('Status = 1','ImgTitle,Description,Title,Http,CSID', 'OrderID DESC');
        foreach($coreSys as $i=>$iCoreSys){
            $coreSys[$i]['Description'] = preg_replace('/<[^>]*>|<\/[^>]*>/', '', $iCoreSys['Description']);//过滤html标签
        }

        echo $this->cacheTag('',array('coreSys'=>$coreSys));
    }

    /**
     * 核心系统详情
     * @author demo
     */
    public function coreSystemInfo(){
        $param = $_GET['param'];
        $csid  = $_GET['csid']&&is_numeric($_GET['csid'])?$_GET['csid']:1;

        $cached=$this->cachedTag('coreSystemInfo'.$param,$csid);
        if($cached){
            exit($cached);
        }


        $coreSysInfo = $this->getModel('CoreSystem')->selectData(
                  'Title,Http',
                  ['CSID'=>$csid],
                  '',
                  1
        );
        $coreSysInfo = $coreSysInfo[0];
        $title = '题库';
        $http  = '/';
        if($coreSysInfo['Title']){
            $title = $coreSysInfo['Title'];
        }

        if($coreSysInfo['Http']){
            $http  = $coreSysInfo['Http'];
        }
        $arr = [];//传递到前台的变量数组

        switch($param)
        {
          case 'detail':
              $arr = array('type'=>'Detail','csid'=>$csid,'typeName'=>'系统简介','title'=>$title,'http'=>$http);
              break;
          case 'flow':
              $arr = array('type'=>'Flow','csid'=>$csid,'typeName'=>'流程展示','title'=>$title,'http'=>$http);
              break;
          case 'faq':
              $arr = array('type'=>'Faq','csid'=>$csid,'typeName'=>'常见问题FAQ','title'=>$title,'http'=>$http);
              break;
          default:
              emptyUrl();
              exit();
        }

        echo $this->cacheTag('coreSystemInfo'.$param,$arr,$csid);
    }

    /**
     * 获得FAQ 答案
     * @author demo
     */
    public function getFaqAnswer(){
        if(IS_AJAX && $_REQUEST['FAQID']){
            $FAQID = (int)$_REQUEST['FAQID'];
            $data = $this->cachedTag('Index/coreSystemFaqAnswer'.$FAQID);
            if(empty($data)){
                $data = $this->getModel('CoreSystemFaq')->getInfo('FAQID ='.(int)$_REQUEST['FAQID'],'Answer')[0];
                $data['Answer'] = str_replace(array("\r\n", "\r", "\n"), '<br>', $data['Answer']);
                $data['Answer']=stripslashes_deep(formatString('IPReturn',$data['Answer']));
                F('Index/coreSystemFaqAnswer'.$FAQID,$data);
            }
            $this->setBack($data);
        }
    }

    /**
     * 平台动态 - 新闻公告
     * @author demo
     */
    public function dynamicNews(){
        $this->display();
    }

    /**
     * 平台动态 - 更新版本
     * @author demo
     */
    public function dynamicVersion(){
        $isCacheDate = $this->cachedTag('dynamicVersion');
        if($isCacheDate){
            echo $isCacheDate;
        }else{
            $list=$this->getModel('SystemEditionLog','getSystemEditionLog');
            foreach($list as $i=>$iList){
                $year=date('Y',$iList['ShowTime']);
                $iList['ShowTime']=date('n月d日',$iList['ShowTime']);
                $newList[$year]['time']=$year.'年';
                $newList[$year]['content'][]=$iList;
            }
            echo $this->cacheTag('dynamicVersion',array('newList'=>$newList));
        }

    }

    /**
     * 平台动态 - 研讨会邀请
     * @author demo
     */
    public function dynamicInvi(){
        $cached=$this->cachedTag();
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag();
    }

    /**
     * 认识题库
     * @author demo
     */
    public function knowMe(){
        if(empty($_GET['param'])) $_GET['param'] = 'knowMeIntro';
        switch ($_GET['param']){
            case 'knowMeIntro':
                $tag = 'knowMeIntro';
                $arr = array('eq'=>0,'typeName'=>'平台简介');
                break;
            case 'knowMeOrientation':
                $tag = 'knowMeOrientation';
                $arr = array('eq'=>1,'typeName'=>'平台定位');
                break;
            case 'knowMeFeature':
                $tag = 'knowMeFeature';
                $arr = array('eq'=>2,'typeName'=>'平台特色');
                break;
            case 'knowMeSupport':
                $tag = 'knowMeSupport';
                $arr = array('eq'=>3,'typeName'=>'理论支撑');
                break;
            case 'knowMeSystemModel':
                $tag = 'knowMeSystemModel';
                $arr = array('eq'=>4,'typeName'=>'平台主要系统模块');
                break;
            default:
                emptyUrl();
                break;
        }
        $cached=$this->cachedTag($tag);
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag($tag,$arr);
    }

    /**
     * 申请开通
     * @author demo
     */
    public function operApply(){
        $ip = get_client_ip(0,true);//获取ip
        //验证用户ip与用户开通ip是否一致并且没有到期
        $searchIP=ip2long($ip);
        $result = $this->getModel('UserIp')->userIp(
            '*','IfReg=1 AND IPAddress = '.$searchIP.' AND LastTime >'.time()
        );

        $arr = array();
        if(!empty($result)){
            //存在IP，跳转注册页
            $this->showSuccess('恭喜，您当前的IP，可以直接注册！',"location='".U('User/Index/register')."'");
        }else{
            $cached=$this->cachedTag('operApply');
            if(!$cached){
                $buffer = SS('areaChildList'); // 缓存子类list数据
                $areaArray = $buffer[0]; //省份数据集
                $arr = array('ip'=>$ip,'areaArray'=>$areaArray);  //省份数据集 省份数据集
                $cached=$this->cacheTag('operApply',$arr);
            }
            echo $cached;
        }
    }

    /**
     * 用户权限信息
     * @author demo
     */
    public function operLevelDetail(){
        $slogan = '';//活动标语
        if(C('IS_PROMOTION')){//活动中
           if(time()>=C('PROM_BEGIN_TIME') && time()<=C('PROM_END_TIME')){//判断活动时间
                  $slogan = C('PROMOTION_SLOGAN');
                  $this->assign('slogan',$slogan);
            }
        }
        /*
        $cahced = $this->cachedTag('operLevelDetail');
        if($cahced){
            if(!$slogan) {
                echo $cahced;
                exit();
            }
        }
        */
        //获取权限价格
        $vipPrice = 20;
        $superVipPrice = 40;
        $map['PUID'] = array('in','43,44');
        $res = $this->getModel('PowerUser')->selectData(
            'PUID,Price',
            $map,
            '',
            2
        );
        foreach($res as $i=>$iRes){
            if($iRes['PUID']==43){//专项会员
                $vipPrice = $iRes['Price'];
            }
            if($iRes['PUID']==44){//至尊会员
                $superVipPrice = $iRes['Price'];
            }
        }
        //获取活动价格
        if(C('IS_PROMOTION')){//活动中
            //判断活动时间
            if(time()>=C('PROM_BEGIN_TIME') && time()<=C('PROM_END_TIME')){
                $vipPrice      = C('VIP_PRICE');
                $superVipPrice = C('SUPER_VIP_PRICE');
            }
        }

        $powerUser = $this->getModel('PowerUser');
        $group = $powerUser->getTable(array(1,2,3));
        $user = $powerUser->getTable(array(4,5,6));

        $array=array(
            'userHeader'=>array_shift($user),
            'user'=> $user,
            'typeName'=> '用户权限',
            'group'=> $group,
            'groupHeader'=>array_shift($group)
        );

        $this->assign('vipPrice',$vipPrice);
        $this->assign('superVipPrice',$superVipPrice);

        echo $this->cacheTag('operLevelDetail',$array);

    }

    /**
     * 开通流程
     * @author demo
     */
    public function operProcedure(){
        if(empty($_GET['param'])) $_GET['param'] = 'slvePrivate';
        switch ($_GET['param']){
            case 'slvePrivate':
                $tag = 'slvePrivate';
                $arr = array('eq'=>1,'typeName'=>'区域/校园私有云');
                break;
            case 'solvePublic':
                $tag = 'solvePublic';
                $arr = array('eq'=>2,'typeName'=>'智能教学公有云');
                break;
            case 'solveRetail':
                $tag = 'solveRetail';
                $arr = array('eq'=>3,'typeName'=>'系统零售服务');
                break;
            default:
                emptyUrl();
                break;
        }
        $cached=$this->cachedTag($tag);
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag($tag,$arr);
    }

    /**
     * 新闻内容
     * @author demo
     */
    public function content(){
        $cached=$this->cachedTag('',(int)$_GET['id']);
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag('',array(),(int)$_GET['id']);
    }

    /**
     * 数据分析
     * @author demo
     */
    public function dataAnalysis(){
        $cached=$this->cachedTag();
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag();
    }

    /**
     * 视频培训
     * @author demo
     */
    public function videoStudy(){
        if(!empty($_GET['param'])){
            if(!in_array($_GET['param'],[1,2,3,4])){
                $_GET['param'] = 1;
            }
        }else{
            $_GET['param'] = 0;
        }
        $arr = array(
          0 => '',//默认不显示
          1 => '准备篇',
          2 => '组卷篇',
          3 => '作业篇',
          4 => '提分篇'
        );
        $cached=$this->cachedTag('',$_GET['param']);
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag('',['param'=>$_GET['param'], 'title'=>$arr[$_GET['param']]],$_GET['param']);
    }

    /**
     * 视频培训内容
     * @author demo
     */
    public function videoContent(){
        $where['NewID'] = (int)$_GET['id'];
        $buffer = D('Manage/News')->newsDate('NewTitle',$where);
        $cached=$this->cachedTag('',(int)$_GET['id']);
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag('',array('title'=>$buffer[0]['NewTitle']),(int)$_GET['id']);
    }

    /**
     * 合作学校
     * @author demo
     */
    public function partnerSchools(){
        /*--------------------------数据库里还没有合作的数据，目前是在html中直接写的--------------------------------------
        $perPage    = 33;//如需改动 请查看前台css样式
        $count      = $this->dbConn->selectCount('PartnerSchool','1=1','SchoolID');
        $page       = page($count,$_GET['p'],$perPage).','.$perPage;
        $schoolList = $this->dbConn->pageData(
            'PartnerSchool',
            'SchoolName',
            '1=1',
            'IfRecom DESC,SchoolID ASC',//推荐降序 添加时间升序
            $page
        );
        $pageOb = $this->getPage($count, $perPage,'',U('About/partnerSchools',array('p'=>'')));
        //分页样式定义
        $pageOb->setConfig('header','个学校');
        $pageOb->setConfig('theme','%first% %prePage% %upPage% %linkPage% %downPage% %nextPage% %end% 共%totalRow% %header% %nowPage%/%totalPage% 页 %goto%');
        $this->assign('page',$pageOb->show()); // 赋值分页输出
        $this->assign('schoolList',$schoolList);
        */
        $cached=$this->cachedTag();
        if($cached){
            exit($cached);
        }
        echo $this->cacheTag();
    }

    /**
     * 核心系统
     * @author demo
     */
    /* public function coreSystem(){
        $data = F('Index/coreSystem');
        if (!$data) {
            $data = $this->fetch();
            F('Index/coreSystem', $data);
        }
        echo $data;
    } */

    /**
     * 开通流程
     * @author demo
     */
    /* 修改
     public function operProcedure(){
        $ip = get_client_ip(0,true);//获取ip
        //验证用户ip与用户开通ip是否一致并且没有到期
        $searchIP=ip2long($ip);
        $result=$this->dbConn->selectData(
            'UserIp',
            '*',
            'IfReg=1 AND IPAddress = '.$searchIP.' AND LastTime >'.time()
        );
        if(!empty($result)){
            //存在IP，跳转注册页
            $this->showSuccess('恭喜，您当前的IP，可以直接注册！',"location='".U('User/Index/registerIndex')."'");
        }else{
            $data = F('Index/operProcedure');
            $buffer = SS('areaChildList'); // 缓存子类list数据
            $areaArray = $buffer[0]; //省份数据集
            $this->assign('ip', $ip); //省份数据集
            $this->assign('areaArray', $areaArray); //省份数据集
            if (!$data) {
                $data = $this->fetch();
                F('Index/operProcedure', $data);
            }
            echo $data;
        }
    } */

    /**
     * 核心系统详情
     * @author demo
     */
    /* public function coreSysL(){
        $data = F('Index/coreSysL');
        if (!$data) {
            $data = $this->fetch();
            F('Index/coreSysL', $data);
        }
        echo $data;
    } */

    /**
     * 网站快速导航[网站地图]
     * @author demo
     */
    public function sitemap(){
        //重写SEO信息
        $title = '网站导航';
        $keywords = '网站导航,网站地图,sitemap';
        $description = '智慧云题库云平台网站导航（网站地图、sitemap页），是智慧云题库云平台最全的网站产品导航页，提供智能组卷、智能提分、智能提分、智能作业、试卷资源、考试预订系统、任务大厅、智能提分App等产品的导航。';

        $this->assign('title',$title);
        $this->assign('keywords',$keywords);
        $this->assign('description',$description);
        $this->display();
    }

    /**
     * 活动展示页面
     * @author demo
     */
    public function activity(){
        $perPage=10;//默认查找的记录数量为10条
        $activityModel = $this->getModel('ActivitySpecial');
        $activityCount=$activityModel->selectCount('1=1','ActivityID');
        $curPage = $_GET['p'];
        if(empty($curPage)){
            $curPage = 1;
        }
        $pageLinks = $this->fillPagtion($this->params, $activityCount, $curPage,$perPage, U('Index/About/activity','', false));
        $pageCount=ceil($activityCount/$perPage);
        if($curPage>$pageCount) $curPage=$pageCount;
        if($curPage<1 || !is_numeric($curPage)) $curPage=1;
        $activityList = $activityModel->selectData('*', '1=1','StartTime desc',($curPage-1)*$perPage.','.$perPage);
        if($activityList){
            $host=C('WLN_DOC_HOST');
            foreach($activityList as $i => $iActivityList){
                $activityList[$i]['ImgUrl'] = $host.$iActivityList['ImgUrl'];
                $activityList[$i]['StartTime'] = date('Y-m-d',$iActivityList['StartTime']);
                $activityList[$i]['EndTime'] = date('Y-m-d',$iActivityList['EndTime']);
                if($iActivityList['StartTime']>time()){
                    $activityList[$i]['Status'] = '未开始';
                }else if($iActivityList['StartTime']<time() && $iActivityList['EndTime']>time()){
                    $activityList[$i]['Status'] = '进行中';
                }else{
                    $activityList[$i]['Status'] = '已结束';
                }
            }
        }
        $this->assign('activityList',$activityList);
        $this->assign('pages', $pageLinks['pages']);
        $this->assign('pageLinks', $pageLinks);
        $this->display();
    }
}
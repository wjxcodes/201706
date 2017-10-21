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
class IndexController extends BaseController{
    /**
     * 题库官网首页
     * @author demo
     */
    public function index(){
//        $this->assign('subject',array_slice(SS('subject'), 1, 9));

//        //--------------------查询试卷更新------------------- 2015-12-22
//        $doc = $this->getModel('Doc');
//        $field=array('docid','docname','subjectname','typename','docyear','areaname','loadtime','introfirsttime','introtime');
//        $where['SubjectID']=12;
//        $order=array('docyear DESC,introfirsttime Desc');
//        $page=array('page'=>1,'perpage'=>9);
//        $result = $doc->getDocIndex($field,$where,$order,$page);
//        $count = count($result[0]);
//        while($count < 9){
//            $count++;
//            $result[0][] = array();
//        }
//        $this->assign('docUpgradeResult', $result[0]);
//
//        $flagStr='testNum,selfTestNum,zujuanNum,shijuanNum,classNum,homeWorkNum,caseDownNum,caseHomeWorkNum,studentAnswerNum,userMoneyList,moneyTotal,appNum';
//        $this->assign('result', $this->getModel('Statistics')->totalSystem($flagStr));
//
//        //----------------查询用户动态--------------------- 15-12-21
//        $this->assign('userDynamicType', \User\Model\UserDynamicModel::$dynamicType);
//        $this->assign('userDynamicResult', $this->getModel('UserDynamic')->getHomePageList());
        $this->assign('hideLogin',1);
        $this->assign('userInfo',json_encode($this->userInfo()));
        $this->display();
    }

    /**
     * 获取cookie提交失败时,表单显示提交前的信息,方便修改
     * @author demo
     */
    public function getInfo(){
        $buffer = unserialize(stripslashes_deep(cookie('indexInfo')));
        $info['school'] = $buffer['school'];
        $info['userName'] = $buffer['username'];
        $info['phone'] = $buffer['phone'];
        $info['email'] = $buffer['email'];
        $info['address'] = $buffer['address'];
        $this->setBack($info);
    }

    /**
     * 统计点击量
     * @author demo
     * @update 2015年9月28日
     */
    public function getHits(){
        $id= $_GET['id'];
        $this->getModel('News')->hitsAddOne(
            $id
        );
    }


    /**
     * 获取地区和学校
     * @author demo
     * @update 2015年9月28日
     */
    public function getArea(){
        $areaID=$_GET['id'];
        if(!is_numeric($areaID) || $areaID<0){
            exit ();
        }
        $areaArray=SS('areaChildList');
        if(!$areaArray[$areaID]){
            //载入学校
            $buffer = $this->getModel('School')->getSchoolFieldByAreaID(
                'SchoolID,SchoolName,Type',
                'AreaID='.$areaID.' and Status=2 and Type<3',
                'SchoolID ASC'
            );
            if($buffer){
                $outPut=array();
                foreach($buffer as $i=>$iBuffer){
                    $outPut[$i]['AreaID']=$iBuffer['SchoolID'];
                    $outPut[$i]['AreaName']=$iBuffer['SchoolName'];
                    $outPut[$i]['end']=1;
                }
                $this->setBack(array('school',$outPut));
            }
            else $this->setBack(array('school',''));
        }else{
            $this->setBack(array('area',$areaArray[$areaID]));
        }
    }

    /**
     * 获取excel模板
     * @author demo
     */
    public function excelUser(){
        header('Location:'.C('WLN_HTTP').'/user.xls');
    }

    /**
     * 获取IP
     * @author demo
     */
    public function getIP(){
        $ip = get_client_ip(0,true);//获取ip
        $this->setBack($ip);
    }

    /**
     * 对外接口输出
     * @author demo
     */
    public function getInterface(){
        if(C('WLN_OPEN_INTERFACE')!==2){
            exit('');
        }
        $model=$_POST['md'];
        $param=unserialize(stripslashes($_POST['pm']));
        $module = $_POST['mod'];
        if($module != MODULE_NAME){
            C(load_config(APP_PATH.$module.'/Conf/config'.CONF_EXT));
        }
        $model='getApi'.$model;
        exit(serialize(call_user_func_array(array($this, $model), $param)));
    }
    //测试用
    public function downloadAudio(){
        $docId = (int)$_GET['docId'];
        if(empty($docId)){
            $this->setError('文档编号不能为空！'); //数据标识不能为空！
        }
        $result = $this->getModel('DocHearing')->downloadAudioFile($docId);
        if($result === false){
            $this->setError('下载失败！'); //数据标识不能为空！
        }
    }
}
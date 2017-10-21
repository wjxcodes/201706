<?php
/**
 * @author demo 
 * @date 2014年12月26日
 */
 /**
  *能力管理控制器类，用于处理能力管理相关操作
  */
namespace Manage\Controller;
class QcodeController extends BaseController  {
    private $moduleName = '二维码生成';
    /**
     * 二维码生成导航；
     * @author demo
     */
    public function index() {
        $pageName='二维码生成管理';
        /*载入模板标签*/
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 专项练习二维码查询；
     * @author demo
     */
    public function kl() {
        $pageName = '专项练习二维码查询';
        $act = 'add'; //模板标识

        if(IS_POST){

            $size=$_POST['size'];
            if(empty($size) || !is_numeric($size)) $size=100;
            $subjectID=$_POST['SubjectID'];
            $klID=$_POST['KlID'];
            if(is_array($klID)){
                $klID=$klID[0];
            }
            $klID=str_replace('t','',$klID);
            //获取知识点子类
            $klBuffer=$this->getApiCommon('Knowledge/knowledgeCache',array('pID'=>$klID));

            foreach($klBuffer as $i=>$iKlBuffer){
                $klBuffer[$i]['Url']=C('WLN_HTTP').U('/Aat/App/index').'?action=3&subID='.$iKlBuffer['SubjectID'].'&type=1&klID='.$iKlBuffer['KlID'];
                $klBuffer[$i]['Img']=U('Manage/Qcode/qcode',array('str'=>urlencode($klBuffer[$i]['Url']),'size'=>$size));
            }

            $mainBuffer=$this->getApiCommon('Knowledge/knowledge');
            $url=C('WLN_HTTP').U('/Aat/App/index').'?action=3&subID='.$subjectID.'&type=1&klID='.$klID;

            $main=array(
                'KlID'=>$klID,
                'KlName'=>$mainBuffer[$klID]['KlName'],
                'Url'=>$url,
                'Img'=>U('Manage/Qcode/qcode',array('str'=>urlencode($url),'size'=>$size))
            );

            $this->assign('main', $main); //页面标题
            $this->assign('list', $klBuffer); //页面标题
        }


        //获取学科数据集
        $subjectArray=SS('subjectParentId');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('edit', 0); //页面标题
        $this->display();
    }

    /**
     * 专项练习二维码使用详细列表；
     * @author demo
     */
    public function klTjList() {
        $pageName='知识点统计列表';
        $subjectID=$_REQUEST['SubjectID'];
        $klID=$_REQUEST['KlID'];
        $phoneStyle=$_REQUEST['PhoneStyle'];
        $start=$_REQUEST['Start'];
        $end=$_REQUEST['End'];

        $where=' 1=1 ';

        if(is_numeric($subjectID)){
            $map['SubjectID']=$subjectID;
            $where.=' and SubjectID='.$subjectID;
        }

        if($klID){
            if(is_array($klID)){
                $count=count($klID);
                $newKlID=$klID[$count-1];
                if(empty($newKlID)) $klID=$klID[$count-2];
                else $klID=$newKlID;
            }
            $map['KlID']=$klID;

            $klChild=SS('klList');
            $klID=empty($klChild[$klID]) ? $klID : $klChild[$klID].','.$klID;

            $where.=' and KlID in ('.$klID.')';
        }

        if(is_numeric($phoneStyle)){
            $map['PhoneStyle']=$phoneStyle;
            $where.=' and PhoneStyle='.$phoneStyle;
        }

        if(strstr($start,'-')){
            $start=strtotime($start);
        }
        if(strstr($end,'-')){
            $end=strtotime($end);
        }
        if ($start) {
            if (empty ($end)) $end = time();
            $map['Start'] = $start;
            $map['End'] = $end;
            $_REQUEST['Start']=date('Y-m-d',$start);
            $_REQUEST['End']=date('Y-m-d',$end);
            $where .= ' AND AddTime between ' . ($start) . ' and ' . ($end) . ' ';
        }
        $ecodeTj=$this->getModel('EcodeTj');
        $count=$ecodeTj->selectCount($where,'TjID');

        $page=$_GET['page'];
        $perpage=C('WLN_PERPAGE');
        $perpage=2;
        $page=page($count,$page,$perpage);
        $page=$page.','.$perpage;
        $list=$ecodeTj->pageData('*',$where,'TjID Desc',$page);

        $subjectBuffer=SS('subject');
        $klBuffer=SS('knowledge');
        $phoneBuffer=[
            1=>'Android',
            3=>'IOS'
        ];

        $userIDArr=array();
        $userNameArr=array();
        foreach($list as $i=>$iList){
            $userIDArr[]=$iList['UserID'];
        }

        if($userIDArr){
            $user=$this->getModel('User');
            $buffer=$user->selectData('UserID,UserName','UserID in ('.implode(',',$userIDArr).')');
            foreach($buffer as $iBuffer){
                $userNameArr[$iBuffer['UserID']]=$iBuffer['UserName'];
            }
        }

        foreach($list as $i=>$iList){
            $list[$i]['UserName']=$userNameArr[$iList['UserID']];
            $list[$i]['SubjectName']=$subjectBuffer[$iList['SubjectID']]['SubjectName'];
            $list[$i]['KlName']=$klBuffer[$iList['KlID']]['KlName'];
            $list[$i]['PhoneName']=$phoneBuffer[$iList['PhoneStyle']];
            $list[$i]['AddTime']=date('Y-m-d H:i:s',$iList['AddTime']);
        }

        $this->pageList($count,$perpage,$map);

        //获取学科数据集
        $subjectArray=SS('subjectParentId');

        /*载入模板标签*/
        $this->assign('phoneBuffer', $phoneBuffer); //学科数据集
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
}

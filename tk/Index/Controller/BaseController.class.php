<?php
/**
 * @author demo 
 * @date 2014年8月5日
 * @date 2015年1月13日
 */
 /**
 * 基础控制器类，用于处理官网基础数据相关操作
 */
namespace Index\Controller;
use Common\Controller\DefaultController;
class BaseController extends DefaultController{
    /**
     * 用于对官网显示信息判断
     * @author demo
     */
    public function ajaxCheckLogin(){
        if(IS_AJAX){
            $result = $this->checkLogin('Index', 1);
            if (is_array($result) && $result[0] == 1) {
                $userInfo[0] = array(
                    'UserName' => $result[1][0]['UserName'],
                    'Whois' => $result[1][0]['Whois']
                );
                $this->setBack($userInfo);
            }
            exit();
        }
        header('Location:'.U('Index/Index/index'));
    }
    /**
     * 公共登录
     * @author demo
     * @date 2015-6-5
     */
    public function login(){
        R('Common/UserLayer/loginHtml');
    }

    /**
     * 验证码显示
     * @author demo
     */
    public function verify() {
        R('Common/UserLayer/verify');
    }

    /**
     * 用户个人信息
     * @author demo 
     * @update 2015年9月28日
     */
    public function userInfo(){
        $check = $this->checkLogin("Index",1);
        if(is_numeric($check)){
            return false;
        }
        $userInfo=$check[1][0];
        $groupName = 1;
        if($userInfo['Whois']==0){
            $groupName = 2;
        }
        //用户组权限
        $powerUserArr = SS('powerUserId');
        foreach($powerUserArr as $i=>$iPowerUserArr){
            if($iPowerUserArr['IfDefault']=='1' && $iPowerUserArr['GroupName']==$groupName){
                $defaultGroup=$iPowerUserArr;
            }
            if($iPowerUserArr['ListID']=='all'){
                $allPowerArr = $iPowerUserArr['PUID'];
            }
        }
        //获取当前用户权限组ID
        $field='GroupName,GroupID,LastTime';
        $where=array(
            'UserID'=>$userInfo['UserID'],
            'GroupName'=>$groupName
        );
        $userGroupArr = $this->getModel('UserGroup')->getGroupByWhere(
            $where,$field
        );
        //用户权限组名称
        if(empty($userGroupArr[0]['GroupID']) || (($userGroupArr[0]['GroupID']!=$allPowerArr) && $userGroupArr[0]['LastTime']<time())){//过期后，降为免费用户
            $userInfo['ChargeMode'] = $defaultGroup['UserGroup'];
        }else{
            $userInfo['ChargeMode'] = $powerUserArr[$userGroupArr[0]['GroupID']]['UserGroup'];
        }

        //用户剩余点数
        $userInfo['ChargeLeave'] = $userInfo['Cz']?$userInfo['Cz']:0;

        if($userInfo['Whois'] == 1) {//教师有可能在以IP开通的学校内登陆
            $nowIP = ip2long(get_client_ip(0,true));
            $ipMsg = $this->getModel('UserIp')->userIp(
                'PUID','IPAddress=' . $nowIP.' and LastTime>'.time()
            );
            if ($ipMsg) {
                $homePower = SS('powerUserGroup')[1];
                $thisGroup = explode(',', $ipMsg[0]['PUID']);
                $userInfo['ChargeMode'] = $homePower['groupList'][$thisGroup[0]]['UserGroup'];
            }
        }
        if($userInfo['ChargeMode'] == ''){
            $userInfo['ChargeMode'] = '普通用户';
        }
        $userInfo['LoginTime'] = date('Y-m-d H:i', $userInfo['LastTime']);//查询上次登录信息
        //更改首页信息
        if($userInfo['UserPic']){
            if(!preg_match('/^http:.*/i',$userInfo['UserPic'])){//判断是不是QQ头像
                $userInfo['UserPic'] = C('WLN_DOC_HOST') . $userInfo['UserPic'];//非QQ头像
            }
        }else{//调用默认头像
            $userInfo['UserPic'] = __ROOT__ . '/Public/index/imgs/icon/photo.jpg';
        }

        //$userInfo['UserPic'] = $userInfo['UserPic'] ? (C('WLN_DOC_HOST') . $userInfo['UserPic']) : (__ROOT__ . '/Public/index/imgs/icon/photo.jpg');//用户图像

        //unset($userInfo['Nickname']);
        //unset($userInfo['RealName']);
        //unset($userInfo['UserID']); 前台引用
        return $userInfo;
    }
}
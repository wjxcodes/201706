<?php
/**
 * @author demo
 * @date 2015年1月19日
 */
/**
 * 公共控制器类，用于处理不需要验证的相关操作
 */
namespace Manage\Controller;
class PublicController extends BaseController  {
    /**
     * 管理员登陆页面
     * @author demo
     */
    public function login() {
        $pageName = '管理员登陆';
        $rnd=rand(0,99999999) . rand(0,99999999);
        session('rnd',$rnd);
        $this->assign('pageName',$pageName);
        $this->assign('keyRnd',$rnd);
        $this->assign('openKeysoft',C('WLN_OPEN_SOFTKEY'));
        $this->display();
    }
    /**
     * 用户登录验证
     * @author demo
     */
    public function check() {
        $loginUrl = __APP__ . '/Manage/Public/login.html';
        $admin = $this->getModel('Admin'); // 实例化对象

        if (!checkToken($_POST)) {
            // 令牌验证错误
            $this->setError('30113',0,$loginUrl); //数据验证错误，请重新操作
        }

        if (empty ($_POST['account'])) {
            $this->setError('30201',0,$loginUrl);
        }
        if (empty ($_POST['password'])) {
            $this->setError('30202',0,$loginUrl);
        }
        if (empty ($_POST['verify'])) {
            $this->setError('30105',0,$loginUrl);
        }
        if (md5($_POST['verify'])!=session('verify')) {
            $this->setError('30101',0,$loginUrl);
        }
        if(C('WLN_OPEN_SOFTKEY')==1){
            $keyID=$_POST['KeyID'];
            $encData=$_POST['EncData']; //加密狗加密数据
            $softkey=$this->getModel('SoftKey');
            $buffer=$softkey->checkData($keyID,$encData,session('rnd'),$_POST['account'],1);
            if($buffer!=1){
                $this->setError($buffer);
            }
            $softkey->setCookieStr($keyID);
        }
        $adminArray = $admin->selectData(
            '*',
            'AdminName="' . $_POST['account'] . '"',
            '',
            1
        );
        if(!$adminArray){
            $this->setError('30204',0,$loginUrl);  //您填写的账户和密码不正确！
        }
        if($adminArray[0]['Status']){
            $this->setError('30203',0,$loginUrl);
        }
        //没有安全码添加一个
        if(empty($adminArray[0]['SaveCode'])){
            $data=array();
            $data['SaveCode']=$admin->saveCode();
            $data['AdminID']=$adminArray[0]['AdminID'];
            if($admin->updateData(
                $data,
                'AdminID='.$adminArray[0]['AdminID']
                )===false){
                $this->setError('30305',0,$loginUrl);
            }
            $adminArray[0]['SaveCode']=$data['SaveCode'];
        }

        if ($adminArray && ($adminArray[0]['Password']==md5($_POST['password']) || $adminArray[0]['Password']==md5($_POST['account'].$_POST['password']))) {
            //修改最后一次登录ip
            //成功登陆验证密码强度
            $bool = true;
            if($_POST['password']){
                $bool = checkString('checkUserPassWord',$_POST['password']);
            }
            $data=array();
            $data['AdminID']=$adminArray[0]['AdminID'];
            $data['LastIP']=get_client_ip(0,true);
            if($bool) $data['IfWeak']=1;
            if($admin->updateData($data,'AdminID='.$adminArray[0]['AdminID'])===false){
                $this->setError('30305',0,$loginUrl);
            }
            $time=C('WLN_COOKIE_TIMEOUT');//cookie记录超时
            $this->setCookieUserName($adminArray[0]['AdminName'],$time);
            $this->setCookieUserID($adminArray[0]['AdminID'],$time);
            $this->setCookieCode(md5($adminArray[0]['AdminID'].$adminArray[0]['AdminName'].$adminArray[0]['SaveCode'].ceil(time()/$time)),$time);
            if(!$bool){
                $this->showSuccess('登录成功！密码安全度过低，请修改！',U('Index/index',array('pw'=>'low')));
            }
            //写入日志
            $this->adminLog('用户登录','【'.$adminArray[0]['AdminName'].'】登录后台系统');
            $this->showSuccess('登录成功！下面转入后台管理首页', __MODULE__);
        } else {
            $this->setError('30204',0,$loginUrl);
        }
    }
    /**
     * 退出登陆
     * @author demo
     */
    public function logout() {
        //写入日志
        $userName=$this->getCookieUserName();
        if($userName){
            $this->adminLog('用户登录','【'.$userName.'】退出后台系统');
            $this->setCookieUserName(null);
            $this->setCookieCode(null);
        }
        //清除加密狗cookie
        if(C('WLN_OPEN_SOFTKEY')==1){
            cookie('encData', null);
        }
        $this->showSuccess('你已经退出了登录！', __MODULE__);
    }
    /**
     * 修改密码资料
     * @author demo
     */
    public function password() {
        $pageName='修改信息';
        $AdminName=$this->getCookieUserName();
        if(!$AdminName){
            $this->showerror('您没有权限进行修改');
            exit;
        }
        $admin     = $this->getModel('Admin');//下面用到saveCode方法
        $edit= $admin->selectData(
            '*',
            'AdminName="'.$AdminName.'" AND Status="0"',
            '',
            '1'
        );

        if(!$edit){
            $this->showerror('该账号数据异常，操作失败！');
            exit;
        }
        if(IS_POST){
            $data=array();
            if($_POST['Passwordy']){
                if(!checkString('checkUserPassWord',$_POST['Password'])){
                    $this->showerror('密码不符合规范！');
                    exit;
                }
                if(md5($_POST['Passwordy'])!=$edit[0]['Password'] && md5($edit[0]['AdminName'].$_POST['Passwordy'])!=$edit[0]['Password']){
                    $this->showerror('原密码错误，请确认！');
                    exit;
                }
                if($_POST['Password']=='' || $_POST['Password2']==''){
                    $this->showerror('请输入新密码！');
                    exit;
                }
                if($_POST['Password']!=$_POST['Password2']){
                    $this->showerror('两次输入的新密码不一致！');
                    exit;
                }
                $data['Password']=md5($edit[0]['AdminName'].$_POST['Password']);
                $data['SaveCode']=$admin->saveCode();
            }
            $data['Email']=$_POST['Email'];
            $data['RealName']=$_POST['RealName'];
            $data['IfWeak']=1;
            if($admin->updateData(
                $data,
                ' AdminName="'.$AdminName.'" '
            )===false){
                $this->showerror('修改失败！');
            }else{
                //写入日志
                $this->adminLog('用户管理','【'.$this->getCookieUserName().'】修改个人信息');
                $this->showSuccess('修改成功！');
            }
        }

        $this->assign('edit',$edit[0]);
        $this->assign('pageName',$pageName);
        $this->display();
    }
    // 验证码显示
    public function verify() {
        R('Common/UserLayer/verify');
    }
    public function top() {
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }

    //显示菜单项
    public function menu(){
        C('SHOW_PAGE_TRACE', false);
        $adminName=$this->getCookieUserName();
        //读取数据库模块列表生成菜单项
        $node=array();
        $menu=$this->getModel('Menu');
        $buffer=$menu->unionSelect('adminSelectPower',$adminName);
        $list=$menu->selectData(
            '*',
            '1=1',
            'MenuOrder asc');
        if($buffer[0]['ListID']=='all'){
            $listArrayTmp = SS('powerAdminList');
            foreach($listArrayTmp as $i=>$iListArrayTmp){
                $listArray[$i] = $iListArrayTmp['ListID'];
            }
        }else{
            $listArray = explode(',',$buffer[0]['ListID']);
        }
        $where['ListID'] = array('in',$listArray);
        $powerArray = SS('powerAdminList');
        foreach($listArray as $i=>$iListArray){
            if($powerArray[$iListArray]){
                $powerList[]=$powerArray[$iListArray];
            }
        }
        $menuArray = array();
        $parentArr=array();//记录父id
        $childArr=array();//记录子id
        $parentTmpArr = array();
        $childTmpArr = array();
        foreach($powerList as $i => $iBuffer){
            $tmpArr[str_replace('/','-',$iBuffer['PowerTag'])] = str_replace('/','-',$iBuffer['PowerTag']);
        }
        foreach($list as $i=>$iList){
            if($iList['PMenu']==0){
                $parentArr[$iList['MenuID']] = $iList;
            }
        }
        foreach ($list as $i => $iList) {
            if($iList['PMenu']==0){
                continue;
            }else{
                //将'-' 变 '/'
                $iList['NewMenuUrl']=str_replace('-','/',$iList['MenuUrl']);
                $childTmpArr[$tmpArr[$iList['MenuUrl']]] = $iList;
                $childArr = $childTmpArr[$iList['MenuUrl']];
                if(!empty($childArr)){
                    $parentTmpArr[$iList['PMenu']] = $parentArr[$iList['PMenu']];
                    $menuArray[$iList['PMenu']][] = $childArr;

                }
            }
        }
        $pid=0;
        foreach($parentArr as $i=>$iParentArr){
            if(!isset($parentTmpArr[$i])){
                continue;
            }
            $node[$pid]=$parentTmpArr[$i];
            $node[$pid]['sub']=$menuArray[$i];
            $pid++;
        }
        $this->assign('menu',$node);
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }

    public function drag() {
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
    public function ends() {
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
    public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
        );
        $this->assign('info',$info);
        $this->display();
    }
    public function footer() {
        C('SHOW_PAGE_TRACE', false);
        $this->display();
    }
    /*上传文件 文档批量上传docInner*/
    public function uploadify() {
        if($_GET['s']!=md5(C('DOC_HOST_KEY').$_GET['u'].date("Y.m.d",time()))){
            exit('error|验证失败');
        }
        if (!empty($_FILES)) {
            $urlPath=R('Common/UploadLayer/uploadWord',array('work','work'));
            if(is_array($urlPath)){
                if(strstr($urlPath[1],'error|')) exit($urlPath[1]);
                exit('error|'.$urlPath[1]);
            }
            $path=$urlPath;

            $filename = $_FILES['Filedata']['name'];
            $ext = pathinfo($filename);
            $ext = $ext['extension'];

            //记录路径
            $data=array();
            $data['FilePath']=$path;
            $data['AddTime']=time();
            $data['FileName']=$filename;
            $y=$_POST['y'];
            $b=$_POST['b'];
            $p=$_POST['p'];
            $r=$_POST['r'];
            $t=$_POST['t'];
            $c=$_POST['c'];
            $cp=$_POST['cp'];
            $gd=$_POST['gd'];
            $docSource=$_POST['ds']; //文档来源


            $data['TypeID']=$t;
            switch($t){
                case 0:
                    $data['Attr']=$t;
                    break;
                case 1:
                    $data['Attr']=$y.'|'.$b.'|'.$p.'|'.$r.'|'.$c.'|'.$gd.'|'.$docSource;
                    break;
                case 2:
                    $data['Attr']=$y.'|'.$b.'|'.$p.'|'.$cp.'|'.$c.'|'.$gd.'|'.$docSource;
                    break;
            }
            $data['Style']=substr($ext,0,3);
            $data['Status']='upload';
            $data['UserName']=$_GET['u'];
            $this->getModel('TmpFilePath')->insertData($data);
            echo 'success|'.$filename;
        }
    }
}
?>
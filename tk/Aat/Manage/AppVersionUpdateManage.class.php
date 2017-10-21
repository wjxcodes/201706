<?php
/**
 * @author demo
 * @date 2015年9月8日
 */
/**
 * APP 版本更新管理
 */
namespace Aat\Manage;
class AppVersionUpdateManage extends BaseController  {
    protected $moduleName = 'APP版本更新';
    /**
     * 浏览版本列表
     * @author demo
     */
    public function index() {
        $appVersionUpdateModel = $this->getModel('AppVersionUpdate');
        $pageName = '版本管理';
        $where = [];
        $versionName = $_REQUEST['VersionName'];
        $appType = $_REQUEST['AppType'];
        //版本号查询
        if($versionName){
            $where['VersionName'] = ['like','%'.$versionName.'%'];
        }
        //AppType查询
        if ($appType) {
            $where['AppType'] = $appType;
        }
        $perPage = C('WLN_PERPAGE');
        $count = $appVersionUpdateModel->selectCount(
            $where,
            '*'); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $page = page($count,$_GET['p'],$perPage);
        $page=$page.','.$perPage;
        $list = $appVersionUpdateModel->pageData(
            '*',
            $where,
            'CreateTime DESC',
            $page);
        $this->pageList($count,$perPage,$where);
        if($list){
            $userIDs = [];
            foreach($list as $iList){
                $iList['CreateUserID']?($userIDs[]=$iList['CreateUserID']):null;
                $iList['EditUserID']?($userIDs[]=$iList['EditUserID']):null;
            }
            $userDb = $this->getModel('Admin')->selectData(
                'AdminID,AdminName',
                ['AdminID'=>['in',$userIDs]]
            );
            foreach($userDb as $user){
                $userIDs[$user['AdminID']] = $user['AdminName'];
            }
            foreach($list as $i=>$iList){
                if($iList['CreateUserID']){
                    $list[$i]['CreateUserName'] = $userIDs[$iList['CreateUserID']];
                }
                if($iList['EditUserID']){
                    $list[$i]['EditUserName'] = $userIDs[$iList['EditUserID']];
                }
            }
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->theme('Wln')->display();
    }
    /**
     * 添加版本
     * @author demo
     */
    public function add() {
        $pageName = '添加版本';
        $act = 'add'; //模板标识

        $this->assign('act', $act); //模板标识
        $this->assign('pageName', $pageName); //页面标题
        $this->theme('Wln')->display();
    }
    /**
     * 编辑版本
     * @author demo
     */
    public function edit() {
        $versionID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (!$versionID) {
            $this->setError('30301');//数据标识不能为空！
        }
        $pageName = '编辑版本';
        $act = 'edit'; //模板标识
        $editDb = $this->getModel('AppVersionUpdate')->findData(
            '*',
            ['VersionID'=>$versionID]);
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $editDb);
        $this->assign('pageName', $pageName);
        $this->theme('Wln')->display('AppVersionUpdate/add');
    }
    /**
     * 保存版本
     * @author demo
     */
    public function save() {
        $act = $_POST['act']; //获取模板标识
        if (!$act) {
            $this->setError('30223');//模板标识不能为空！
        }
        $versionID = $_POST['VersionID']; //获取数据标识
        //判断数据标识
        if (!$versionID && $act == 'edit') {
            $this->setError('30301');//数据标识不能为空！
        }
        $appType= $_POST['AppType'];
        $versionName = $_POST['VersionName'];
        $versionCode = $_POST['VersionCode'];
        $updateType = $_POST['UpdateType'];
        $needUpdateCodeStart = $_POST['NeedUpdateCodeStart'];
        $needUpdateCodeEnd = $_POST['NeedUpdateCodeEnd'];
        $fileUrl = $_POST['FileUrl'];
        $updateContent = $_POST['UpdateContent'];
        $userName = $this->getCookieUserName();
//        $userName = cookie(C('WLN_WLN_USER_AUTH_KEY'));
        $userID = $this->getModel('Admin')->findData('AdminID',['AdminName'=>$userName])['AdminID'];
        if (!$appType || !$versionName || !$versionCode || !isset($updateType) || !isset($needUpdateCodeStart) || !$needUpdateCodeEnd ||
            !$fileUrl || !$updateContent || !$userID
        ) {
            $this->setError('50001');//全部内容必须填写！
        }
        if(!is_numeric($versionCode)||!is_numeric($needUpdateCodeStart)||!is_numeric($needUpdateCodeEnd)){
            $this->setError('50002');//VersionCode必须是数字！
        }
        if($needUpdateCodeEnd>=$versionCode){
            $this->setError('50003');//更新VersionCode结束值必须小于VersionCode
        }
        //versionCode不能重复的检查
        $checkWhere = $act=='add'?['VersionCode'=>$versionCode]:['VersionCode'=>$versionCode,'VersionID'=>['neq',$versionID]];
        if($this->getModel('AppVersionUpdate')->findData(
            '*',
            $checkWhere
        )){
            $this->setError('50004');//版本Code不能重复！
        }
        $time = time();
        $data = [
            'AppType'=>$appType,
            'VersionName'=>$versionName,
            'VersionCode'=>$versionCode,
            'UpdateType'=>$updateType,
            'NeedUpdateCodeStart'=>$needUpdateCodeStart,
            'NeedUpdateCodeEnd'=>$needUpdateCodeEnd,
            'FileUrl'=>$fileUrl?$fileUrl:'',
            'UpdateContent'=>$updateContent,
        ];
        if ($act == 'add') {
            $data['CreateUserID'] = $data['EditUserID'] = $userID;
            $data['CreateTime'] = $data['EditTime'] = $time;
            if($this->getModel('AppVersionUpdate')->insertData(
                    $data) === false){
                $this->setError('30310');//添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加版本【'.$versionName.'】');
                $this->showSuccess('添加成功！',__URL__);
            }
        } else if ($act == 'edit') {
            $data['EditUserID'] = $userID;
            $data['EditTime'] = $time;
            if($this->getModel('AppVersionUpdate')->updateData(
                    $data,
                    ['VersionID'=>$versionID])===false){
                $this->setError('30311');//修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改版本为【'.$versionName.'】的数据');
                $this->showSuccess('修改成功！', __URL__);
            }
        }
    }
    /**
     * 删除版本
     * @author demo
     */
    public function delete() {
        $versionID = $_POST['id']; //获取数据标识
        if (!$versionID) {
            $this->setError('30301','',__URL__);//数据标识不能为空！
        }
        if ($this->getModel('AppVersionUpdate')->deleteData(
            ['VersionID'=>['in',$versionID]]) === false) {
            $this->setError('30302');//删除失败！
        } else {
            //写入日志
            $this->adminLog($this->moduleName,'删除版本ID为【'.$versionID.'】的数据');
            $this->showSuccess('删除成功！', __URL__);
        }
    }
}
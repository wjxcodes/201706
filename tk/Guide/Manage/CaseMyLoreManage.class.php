<?php
/**
 * 个人知识管理
 * @author demo 2015-6-10
 */
namespace Guide\Manage;
class CaseMyLoreManage extends BaseController  {

    protected $moduleName = '个人知识管理';
    
    private $subjectid = 0; //学科

    private $ajaxResponse = 0; //响应方式

    private $forums = array(); //板块

    /**
     * 初始化时运行的函数
     */
    public function __construct(){
        parent::__construct();
        $this->forums = $this->getModel('CaseMenu')->getCaseForum();
        $this->subjectid = cookie('SubjectId');
        if(IS_AJAX){
            $this->ajaxResponse = 1;
        }
    }

    /**
     * 个人知识后台首页列表
     * @author demo
     */
    public function index(){
        $perpage = C('WLN_PERPAGE'); //每页 页数
        $page = 1;
        $map = array();
        $requestParams = empty($_POST) ? $_GET : $_POST;
        if(array_key_exists('p', $requestParams)){
            $page = $requestParams['p'];
        }
        $menu = '';
        if(array_key_exists('MenuID', $requestParams)){
            $menu = $requestParams['MenuID'];
            $map['MenuID'] = $menu;
        }
        $loreid = '';
        if(array_key_exists('LoreID', $requestParams)){
            $loreid = $requestParams['LoreID'];
        }
        $data = $this->getModel('CaseCustomLore')->getListByParams(array(
            'subjectid' => $this->subjectid,
            'menuid' => $menu,
            'id' => $loreid,
            'page' => $page
        ), $perpage);
        $param['style']='caseMenu';
        $param['subjectID']=$this->subjectid;
        $param['return']=2;
        $menuArray = $this->getData($param);
        $this->assign('menuArray', json_encode($menuArray));
        $result = $this->dispose($data['result']);
        $this->pageList($data['page'][0], $data['page'][1], $map);
        $this->assign('forums', json_encode($this->forums));
        $this->assign('list', $result);
        $this->assign('pageName', '个人知识管理');
        $this->display();
    }

    /**
     * 编辑个人知识
     * @author demo
     */
    public function edit(){
        $id = (int)$_GET['LoreID'];
        if(empty($id)){
            $this->setError('30301',$this->ajaxResponse);//数据标识不能为空
        }
        $param = array(
            'id' => $id,
            'subjectid' => $this->subjectid
        );
        $data = $this->getModel('CaseCustomLore')->getListByParams($param);
        $data = $this->dispose($data['result']);
        $this->assign('edit', $data[0]);
        $this->assign('forums', $this->forums);
        $this->assign('pageName', '编辑个人知识');
        $this->setBack($this->fetch('CaseMyLore/edit'));
    }

    /**
     * 修改个人知识
     * @author demo
     */
    public function save(){
        $id = (int)$_POST['LoreID'];
        if(empty($id)){
            $this->setError('30301',$this->ajaxResponse);//数据标识不能为空
        }
        $data['Lore'] = formatString('IPReplace',$_POST['Lore']);
        $data['Answer'] = formatString('IPReplace',$_POST['Answer']);
        $data['MenuID'] = $_POST['MenuID'];
        $data['ChapterID'] = $_POST['ChapterID'];
        $model = $this->getModel('CaseCustomLore');
        if($model->saveLore($data, $id) === false){
            $this->setError('30303', $this->ajaxResponse);
        }
        $param['style']='chapterList';
        $param['ID'] = $data['ChapterID'];
        $param['return'] = 2;
        $chapterArray = $this->getData($param);
        $this->adminLog($this->moduleName,'修改LoreID为【'.$id.'】的数据');
        $this->setBack(array(
            'ChapterName' => $chapterArray[0]['ChapterName']
        ));
    }

    /**
     * 删除个人知识
     * @author demo
     */
    public function delete(){
        $id = R('Common/TestLayer/formatIds',array($_POST['id']));
        if(empty($id)){
            $this->setError('30301',$this->ajaxResponse);//数据标识不能为空
        }
        if($this->getModel('CaseCustomLore')->delLore($id)){
            $this->adminLog($this->moduleName,'删除LoreID为【'.$id.'】的数据');
            $this->setBack('success', $this->ajaxResponse);
        }
        $this->setError('30302', $this->ajaxResponse);
    }

    /**
     * 处理数据
     * @param array $data 需要处理的数据
     * @return array
     * @author demo
     */
    private function dispose($data){
        $subject = SS('subject');
        $menuArray=SS('caseMenu');
        $result = array();

        $parent=SS('chapterParentPath');// 获取章节路径防止重复调用
        $self=SS('chapterList');

        $param=array();
        $param['style']='chapterList';
        $param['parent']=$parent;
        $param['self']=$self;
        $param['return'] = 2;
        //组合出数据
        foreach($data as $key=>$value){
            $value['Lore'] = formatString('IPReturn',stripslashes_deep($value['Lore']));
            $value['Answer'] = formatString('IPReturn',stripslashes_deep($value['Answer']));
            $value['SubjectName'] = $subject[$value['SubjectID']]['SubjectName'];
            $value['MenuName'] = '';
            if(array_key_exists($value['MenuID'], $menuArray)){
                $value['MenuName'] = $menuArray[$value['MenuID']]['MenuName'];
            }
            $value['ForumName'] = '';
            if(array_key_exists($value['ForumID'], $this->forums)){
                $value['ForumName'] = $this->forums[$value['ForumID']]['name'];
            }

            $param['ID'] = $value['ChapterID'];
            $chapterArray = $this->getData($param);
            $value['ChapterName'] = $chapterArray[0]['ChapterName'];
            $result[$key] = $value;
        }
        return $result;
    }
}
<?php
/**
 * @author demo
 * @date 2015年5月12日
 */
/**
 * 知识管理类，用于管理知识的相关操作
 */
namespace Guide\Manage;
class CaseLoreManage extends BaseController  {
    var $moduleName = '知识管理';

    /**
     * 浏览知识列表
     * @author demo
     */
    public function index() {
        $pageName = '知识管理';
        $data=' 1=1 '; //初始化条件
        $map=array(); //分页条件
        $perpage = C('WLN_PERPAGE'); //每页 页数
        $orderby=' LoreID desc '; //默认排序

        //浏览谁的试题
        if($this->ifSubject && $this->mySubject){
            $data .= 'and SubjectID in ('.$this->mySubject.') ';
        }elseif($this->ifDiff){
            $data .= ' and Admin="'.$this->getCookieUserName().'" ';
        }

        //获取查询条件
        if ($_REQUEST['name']) {
            //简单查询
            if(is_numeric($_REQUEST['name'])){
                $map['name'] = $_REQUEST['name'];
                $data .= ' and LoreID='.$_REQUEST['name'];
            }else{
                $this->setError('30502');
            }
        } else {
            //高级查询
            if ($_REQUEST['LoreID']) {
                if(is_numeric($_REQUEST['LoreID'])){
                    $map['LoreID'] = $_REQUEST['LoreID'];
                    $data .= ' and LoreID='.$_REQUEST['LoreID'];
                }else{
                    $this->setError('30502');
                }
            }
            if ($_REQUEST['SubjectID']) {
                if($this->ifSubject && !in_array($_REQUEST['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712',0); //您不能搜索非所属学科知识！
                    return;
                }
                $map['SubjectID'] = $_REQUEST['SubjectID'];
                $data .= ' and SubjectID='.$_REQUEST['SubjectID'];
            }
            if ($_REQUEST['DocID']) {
                $map['DocID'] = $_REQUEST['DocID'];
                $data .= ' and DocID='.$_REQUEST['DocID'];
                $orderby=' NumbID ASC';
            }
            if (isset($_REQUEST['IfIntro']) && $_REQUEST['IfIntro']!='') {
                $map['IfIntro'] = $_REQUEST['Intro'];
                $data .= ' and IfIntro='.$_REQUEST['IfIntro'];
            }
        }
        $count = $this->getModel('CaseLoreAttr')->selectCount(
            $data,
            'LoreID'
        ); // 查询满足要求的总记录数
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $this->pageList($count, $perpage, $map);
        $page = page($count,$_GET['p'],$perpage).','.$perpage; //格式化分页
        $caseLoreAttr = $this->getModel('CaseLoreAttr');
        $list = $caseLoreAttr->pageData(
            '*',
            $data,
            $orderby,
            $page
        );
        //根据文档查询不在进行分页
        if(!empty($_REQUEST['DocID'])){
            $list=$caseLoreAttr->selectData(
                '*',
                $data,
                $orderby
            );
        }
        if($list){
            $subject = SS('subject');
            $menuArray=SS('caseMenu');
            //获取list下试题id
            foreach($list as $i=>$iList){
                $testIDArray[]=$iList['LoreID'];
            }
            $lore = $this->getModel('CaseLore');
            $loreList = $lore->getDataByLoreID($testIDArray);
            $host=C('WLN_DOC_HOST');

            $parent=SS('chapterParentPath');// 获取章节路径防止重复调用
            $self=SS('chapterList');

            $param=array();
            $param['style']='chapterList';
            $param['parent']=$parent;
            $param['self']=$self;
            foreach($list as $i=>$iList){
                $list[$i]['Content']=$loreList[$iList['LoreID']]['Lore'];
                $list[$i]['SubjectName']=$subject[$iList['SubjectID']]['ParentName'].$subject[$iList['SubjectID']]['SubjectName'];

                $param['ID']=$iList['ChapterID'];
                $chapterArray=$this->getData($param);
                foreach($chapterArray as $j=>$jChapterArray){
                    $list[$i]['ChapterName'].='<br>'.$jChapterArray['ChapterName'];
                }
                $list[$i]['MenuName']=$menuArray[$iList['MenuID']]['MenuName'];
                $docData = $this->getModel('CaseLoreDoc')->selectData(
                    'DocName',
                    'DocID='.$iList['DocID']
                );
                $list[$i]['DocName']=$docData[0]['DocName'];
                if($host){
                    $list[$i]['Content']=R('Common/TestLayer/strFormat',array($list[$i]['Content']));
                }
            }
        }
        //学科
        $subjectArray=SS('subjectParentId'); //获取学科数据集

        /*载入模板标签*/
        $this->assign('list', $list); // 赋值数据集
        $this->assign('subjectArray', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 知识入库
     * @author demo
     */
    public function replace(){
        $data = array ();
        $loreID = $_POST['wid']; //获取数据标识
        $status = $_POST['status'];
        $tplIDArray = explode(',',$loreID);
        $statusArray = explode(',',$status);
        $data['IfIntro']=1;
        if($statusArray[0]==1){
            $data['IfIntro']=0;
        }

        if (!$loreID) {
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if ($this->getModel('CaseLoreAttr')->updateData(
                $data,
                'LoreID in (' . $loreID . ')') === false) {
            $this->setError('30311'); //修改失败！
        } else {
            //统计该文档下的知识总数
            $caseLore = $this->getModel('CaseLore');
            $docIdArr = $caseLore->selectData(
                'DocID',
                'LoreID in (' . $loreID . ')'
            );
            foreach($docIdArr as $i=>$iDocIdArr){
                $uniqueId[]=$iDocIdArr['DocID'];
            }
            $docArr=array_values(array_unique($uniqueId));
            $docId=implode(',',$docArr);
            $noIntro = $caseLore->groupData( //统计总数共多多少
                'count(*) as loreTotal',
                'DocID in('.$docId.')',
                'DocID'
            );
            //统计该文档下的入库知识总数
            $intro = $this->getModel('CaseLoreAttr')->groupData( //统计总数共多多少
                'count(*) as loreTotal',
                'DocID in('.$docId.') and IfIntro=1',
                'DocID'
            );
            //比对两个总数是否相等
            foreach($noIntro as $i => $iNoInstro){
                if($noIntro[$i]['loreTotal'] == $intro[$i]['loreTotal']){
                    $updateDocId[]=$docArr[$i];
                }
            }
            if(!empty($updateDocId)){
                $updateIntro=implode(',',$updateDocId);
                $result=$this->getModel('CaseLoreDoc')->updateData(
                    'DocIntro=1',
                    'DocID in ('.$updateIntro.')'
                );
            }
            //写入日志
            $this->adminLog($this->moduleName, '修改导学案知识编号为【' . $loreID . '】的状态【' . ($data['IfIntro'] == 1 ? '入库' : '未入库') . '】');
            $this->setBack('更改状态成功！');
        }
    }

    /**
     * 编辑知识信息
     * @author demo
     */
    public function edit() {
        $loreID = $_GET['LoreID']; //获取数据标识
        //判断数据标识
        if (empty ($loreID)) {
            $this->setError('30301',1);//数据标识不能为空
        }
        $pageName = '编辑知识';
        $lore = $this->getModel('CaseLore');
        $edit = $this->getModel('CaseLoreAttr')->selectData(
            '*',
            'LoreID='.$loreID
        );
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject && !in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
            $this->setError('30712',0);//不能编辑费所属学科知识
        }
        $loreData = $lore->getDataByLoreID($loreID);
        $host=C('WLN_DOC_HOST');
        if($host){
            foreach($loreData as $i=>$iLoreData){
                $edit[0]['Lore']=R('Common/TestLayer/strFormat',array($iLoreData['Lore']));
                $edit[0]['Answer']=R('Common/TestLayer/strFormat',array($iLoreData['Answer']));
            }
        }
        //获取学科数据
        $subject = SS('subject');
        $edit[0]['SubjectName']=$subject[$edit[0]['SubjectID']]['ParentName'].$subject[$edit[0]['SubjectID']]['SubjectName'];

        //获取栏目属性
        $param['style']='caseMenu';
        $param['subjectID']=$edit[0]['SubjectID'];
        $param['return']=2;
        $menuArray = $this->getData($param);
        /*载入模板标签*/
        $this->assign('edit', $edit[0]);
        $this->assign('menuArray',$menuArray);//栏目数据
        $this->assign('pageName', $pageName);
        $this->setBack($this->fetch('CaseLore/edit'));
    }

    /**
     * 知识修改保存
     * @author demo
     */
    public function save(){
        $loreID=$_POST['LoreID'];
        if (empty($loreID)) {
            $this->setError('30301',0);//数据标识不能为空
        }
        $loreData = $this->getModel('CaseLoreAttr')->selectData(
            'SubjectID',
            'LoreID='.$loreID
        );
        //编辑谁的文档
        if($this->ifSubject && $this->mySubject && !in_array($loreData[0]['SubjectID'],explode(',',$this->mySubject))){
            $this->setError('30712');//需修改  不能编辑非所属学科知识
        }
        if(empty($_POST['MenuID'])){
            $this->setError('23006');//请选择所属栏目
        }
        $data = array();
        $data['MenuID']=$_POST['MenuID'];
        $data['ChapterID']=$_POST['chapterList'];
        $data['IfIntro']=$_POST['IfIntro'];
        if($this->getModel('CaseLoreAttr')->updateData(
            $data,
            'LoreID='.$loreID
        )===false){
            $this->setError('23007');//修改知识失败
        }else{
            $this->adminLog($this->moduleName, '修改导学案知识LoreID为【' . $_POST['LoreID'] . '】的数据');
            $param['style']='chapterList';
            $param['ID']=$data['ChapterID'];
            $param['return']=2;
            $chapterArray=$this->getData($param);
            $result=array();
            foreach($chapterArray as $i=>$iChapterArray){
                $result['ChapterName'].='<br>'.$iChapterArray['ChapterName'];
            }
            $result['LoreID']=$loreID;
            $result['IfIntro']=$data['IfIntro'];
            $menuArray=SS('caseMenu');
            $result['MenuName']=$menuArray[$data['MenuID']]['MenuName'];
            $this->setBack($result);
        }
    }

    /**
     * 替换知识
     * @author demo
     */
    public function replaceLore(){
        $pageName='知识替换';
        $loreID=$_REQUEST['LoreID'];
        if(empty($loreID)){
            $this->setError('30301',0);//模板标识不能为空
        }
        $lore=$this->getModel('CaseLore');
        $caseLoreDoc=$this->getModel('CaseLoreDoc');
        $caseLoreAttr = $this->getModel('CaseLoreAttr');
        //判断替换权限
        if($this->ifSubject && $this->mySubject){
            $subject = $caseLoreAttr->selectData(
                'SubjectID',
                'LoreID='.$loreID
            );
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',0);//您不能编辑非所属学科知识
            }
        }elseif($this->ifDiff){
            $edit = $caseLoreAttr->selectData(
                'Admin',
                'LoreID='.$loreID
            );
            //判断是否可以编辑
            if($edit[0]['Admin']!=$this->getCookieUserName()){
                $this->setError('23008',0);//您不能编辑其他人的文档
            }
        }
        if(IS_POST){
            //替换试题数据
            if (!empty ($_FILES['photo']['name']) && !empty ($_FILES['photo']['size'])) {
                //此处不检测word文档
                C('WLN_DOC_OPEN_CHECK',0);
                $data = array ();

                //上传并检测word文档
                $output=R('Common/UploadLayer/uploadWordAndCheck');
                if(is_numeric($output[0]) && !empty($output[0])) $this->setError($output[0],0,'',$output[1]);
                $data['DocPath']=$output[0];
                $docHtmlPath=$data['DocHtmlPath']=$output[1];
                $data['DocFilePath']=$output[2];

                $data['LoreID'] = $loreID;
                $data['Admin'] = $this->getCookieUserName();
                $data['LoadTime'] = time();

                //记录替换文档
                $caseLoreReplace = $this->getModel('CaseLoreReplace');
                $buffer = $caseLoreReplace->selectData(
                    '*',
                    'LoreID=' . $loreID,1);
                if ($buffer) {
                    $caseLoreReplace->updateData(
                        $data,
                        'ReplaceID='.$buffer[0]['ReplaceID']);
                    $this->adminLog($this->moduleName, '修改替换知识LoreID为【' . $loreID . '】');
                } else {
                    $caseLoreReplace->insertData($data);
                    $this->adminLog($this->moduleName, '添加替换知识LoreID为【' . $loreID . '】');
                }
                //替换知识内容
                $data = array ();
                $data['LoreID'] = $loreID;

                $buffer = SS('lorelabel');
                if (!$buffer)
                    $this->setError('30707',0, __MODULE__ . '/CaseLoreDocLabel/index');


                $start = array ();
                $testField = array ();
                foreach ($buffer as $iBuffer) {
                    $start[] = $iBuffer['DefaultStart'];
                    $testField[] = $iBuffer['LoreField'];
                }
                $html = $caseLoreDoc->getDocContent($docHtmlPath);  //获取html数据

                $arrDoc = $caseLoreDoc->division($html, $start,1); //分割
                $arrHtml = $caseLoreDoc->division($html, $start,2); //分割
                $newArrDoc = $caseLoreDoc->changeArrFormatDoc($arrDoc); //doc过滤
                $newArr = $caseLoreDoc->changeArrFormat($arrHtml); //html过滤

                $testFieldArr = $caseLoreDoc->getTestField(); //数据表字段对应数组

                $dataX=array();
                $dataX['LoreID']=$loreID;
                //单条数据记录
                foreach ($newArr[0] as $i => $iNewArr) {
                    if($testFieldArr[$testField[$i]]=='Test'){
                        $data['Lore']=$iNewArr;
                        continue;
                    }
                    if(!strstr($testField[$i],'属性')){
                        $data[$testFieldArr[$testField[$i]]] = $iNewArr;
                        $dataX['Doc' . $testFieldArr[$testField[$i]]] = $newArrDoc[0][$i];
                    }
                }
                $this->getModel('CaseLore')->updateData(
                    $data,
                    'LoreID='.$loreID);
                $this->showSuccess('知识替换成功！', U('CaseLore/replaceLore?LoreID='.$loreID));
            }

        }
        $loreData = $lore->getDataByLoreID($loreID);
        $host=C('WLN_DOC_HOST');
        if($host){
            foreach($loreData as $i=>$iLoreData){
                $loreData[$i]['Lore']=R('Common/TestLayer/strFormat',array($iLoreData['Lore']));
                $loreData[$i]['Answer']=R('Common/TestLayer/strFormat',array($iLoreData['Answer']));
            }
        }
        $this->assign('pageName',$pageName);
        $this->assign('loreData',$loreData);
        $this->display('CaseLore/replaceLore');
    }

    /**
     * 下载文档
     * @author demo
     */
    public function loreDown(){
        $loreID = $_GET['id']; //获取数据标识
        //判断数据标识
        if (empty ($loreID)) {
            $this->setError('30301',0);
        }
        if($this->ifSubject && $this->mySubject){
            $subject = $this->getModel('CaseLoreAttr')->selectData(
                'SubjectID',
                'LoreID='.$loreID);
            if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('23009',0);//不能下载非所属学科文档
            }
        }
        $lore = $this->getModel('CaseLore');
        $edit = $lore->selectData(
            'LoreID,Lore,Answer,DocID,NumbID',
            'LoreID='.$loreID);
        $buffer = $this->getModel('CaseLoreDoc')->selectData(
            '*',
            'DocID="'.$edit[0]['DocID'].'"');
        $subjectList=SS('subject');
        $buffer=$subjectList[$buffer[0]['SubjectID']];
        $fontSize=10.5;
        if($buffer[0]['FontSize']>1) $fontSize=$buffer[0]['FontSize'];
        $str = $lore->getSingleDoc($edit[0],$fontSize);

        $style=$_GET['style'];
        if(!$style) $style=".docx";

        $host=C('WLN_DOC_HOST');
        if($host){
            $urlPath=R('Common/UploadLayer/setWordDocument',array( $str ,substr($style,1)));
            if(strstr($urlPath,'error')){
                $this->setError('30405',0); //下载异常！请稍联系管理员。
            }

            $url=$host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down','mht','知识文档-'.$loreID));

            header('Location:'.$url);
        }
    }

    /**
     * 知识删除
     * @author demo
     */
    public function delete(){
        $id=$_POST['id'];
        if(empty($id)){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if($this->ifSubject && $this->mySubject){
            $buffer = $this->getModel('CaseLoreAttr')->selectData(
                '*',
                'LoreID in (' . $id . ')');
            foreach($buffer as $i=>$iBuffer){
                if(!in_array($iBuffer['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507',0); //您没有权限删除非所属学科内容！
                }
            }
        }
        $this->getModel('CaseLore')->deleteData(
            'LoreID in ('.$id.')'
        );
        $this->getModel('CaseLoreAttr')->deleteData(
            'LoreID in ('.$id.')'
        );
        $this->getModel('CaseLoreReplace')->deleteData(
            'LoreID in ('.$id.')'
        );
        //写入日志
        $this->adminLog($this->moduleName, '删除知识LoreID为【' . $id . '】的数据');
        $this->showSuccess('删除成功！');
    }
}
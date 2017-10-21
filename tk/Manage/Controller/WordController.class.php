<?php
/**
 * @author demo
 * @date 2015-2-26
 */
/**
 * 分词管理，分词记录的相关操作
 */
namespace Manage\Controller;
class WordController extends BaseController  {
    var $moduleName = '分词管理';
   
    /*浏览分词*/
    public function index() {
        $pageName = '分词管理';
        $word = $this->getModel('Word');
        $subjectArrayTmp = SS('subjectParentId');
        $data='1=1';
        if($_REQUEST['name']){
            $map['Word'] = $_REQUEST['name'];
            $data.= " AND a.Word='".$_REQUEST['name']."' ";
        }
        if($_REQUEST['Word']){
            $map['Word'] = $_REQUEST['Word'];
            $data.= " AND a.Word='".$_REQUEST['Word']."' ";
        }
        if(is_array($_REQUEST['KlID'])){
            $endKlID=str_replace('t','',end($_REQUEST['KlID']));
        }else{
            $endKlID=$_REQUEST['KlID'];
        }
        if($endKlID){
            $map['KlID'] = $endKlID;
            $data.=" AND b.KlID='".$endKlID."'";
        }
        $perpage =C('WLN_PERPAGE');
        $count = $word->unionSelect('wordSelectCount',$data);//统计总数
        $page=page($count,$_GET['p'],$perpage).','.$perpage;
        $wordArray = $word->unionSelect('wordPageData',
            $data,
            $page);//分页内容信息
        //执行分页
        $this->pageList($count, $perpage, $map);
        /*根据WordID获取知识点名称*/
        if($wordArray){
            //获取知识点 学科缓存数据
            $subjectArray=SS('subject');
            $knowledgeArray=SS('knowledge');
            $wordID=array(); //存储当前显示wordid用于查询klid
            $WordKl = $this->getModel('WordKl');
            //获取wordid
            foreach($wordArray as $i=>$iWordArray){
                $wordID[]=$iWordArray['WordID'];
            }
            $wordKlArray=$WordKl->selectData(
                "*",
                'WordID in ('.implode(',',$wordID).')'); //获取klid
            //wordid和klid对应
            foreach($wordArray as $i=>$iWordArray){
                foreach($wordKlArray as $jWordKlArray){
                    if($iWordArray['WordID']==$jWordKlArray['WordID']){
                        $wordArray[$i]['KlID']=$jWordKlArray['KlID'];
                    }
                }
            }
            //根据klid获取对应名称
            foreach($wordArray as $i=>$iWordArray){
                $pid='a';//存储知识点的父类
                while($pid){
                    if($pid=='a') $pid=$iWordArray['KlID'];
                    $wordArray[$i]['KlName']=' >> '.$knowledgeArray[$pid]['KlName'] . $wordArray[$i]['KlName'];
                    $pid=$knowledgeArray[$pid]['PID'];
                }
                $wordArray[$i]['SubjectName']=$subjectArray[$knowledgeArray[$iWordArray['KlID']]['SubjectID']]['ParentName'].$subjectArray[$knowledgeArray[$iWordArray['KlID']]['SubjectID']]['SubjectName'];
            }
        }
        /*载入模板标签*/
        $this->assign('word_array', $wordArray); //分词数据集
        $this->assign('subject_array', $subjectArrayTmp); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /*添加分词*/
    public function add() {
        $pageName = '添加分词';
        $act = 'add'; //模板标识
        $subjectArray =  SS('subjectParentId');
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subject_array', $subjectArray);
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /*编辑分词*/
    public function edit() {
        $wordID=$_GET['id'];    //获取数据标识
        //判断数据标识
        if(empty($wordID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑分词';
        $act = 'edit'; //模板标识
        $knowledge=SS('knowledge');
        $wordArray = $this->getModel('Word')->selectData(
            '*',
            'WordID='.$wordID,
            '',
            1);
        $wordKlArray =$this->getModel('WordKl')->selectData(
            '*',
            'WordID='.$wordID,
            '',
            1);
        $edit=$wordArray;
        $knowledgeParent=SS('knowledgeParent');
        $edit=$wordArray;        
        $buffer =SS('knowledge');
        $edit[0]['SubjectID']=$buffer[$wordKlArray[0]['KlID']]['SubjectID'];
                //查找父类id
        $buffer=SS('knowledgeParent');  // 缓存父类路径数据
        $bufferx=SS('klBySubject3');  // 缓存子类list数据
        $knowledgeArray=$bufferx[$edit[0]['SubjectID']];//获取省份
        $knowledgeParentStr='';//父类路径包括自己
        $bufferTmp=array();
        if($buffer[$wordKlArray[0]['KlID']]) krsort($buffer[$wordKlArray[0]['KlID']]);
        if($buffer[$wordKlArray[0]['KlID']]){
            foreach($buffer[$wordKlArray[0]['KlID']] as $iBuffer){
                $bufferTmp[]=$iBuffer['KlID'];
            }
            $knowledgeParentStr='|'.implode('|',$bufferTmp).'|t'.$wordKlArray[0]['KlID'].'|';
        }else{
            $knowledgeParentStr='|'.$wordKlArray[0]['KlID'].'|';
        }
        $subjectArray = SS('subjectParentId');
        /*载入模板标签*/

        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);
        $this->assign('subject_array', $subjectArray);
        $this->assign('knowledgeArray', $knowledgeArray);
        $this->assign('knowledgeParentStr', $knowledgeParentStr);
        $this->assign('pageName', $pageName);
        $this->display('Word/add');
    }
    /*保存分词*/
    public function save() {
        $wordID=$_POST['WordID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($wordID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30301'); //模板标识不能为空！
        }
        $wordStr=$_POST['Word'];
        $wordArr=explode(' ',$wordStr);
        $weightStr=$_POST['Weight2'];
        $weight2Str=$_POST['Weight'];
        $knowledgeStr=str_replace('t','',end($_POST['KlID']));
        if($weightStr>1 || $weightStr<=0){
            $this->setError('13101'); //请输入正确的权重值！
            exit();
        }
        $word=$this->getModel('Word');
        $wordKl=$this->getModel('WordKl');
        if($act=='add'){
            //判断分词是否添加过
            foreach($wordArr as $i=>$iWordArr){
                $buffer= $word->selectData(
                    '*',
                    'Word = "'.$iWordArr.'"');
                if($buffer){
                    foreach($buffer as $iBuffer){
                        $wordKlBuff= $wordKl->selectData(
                            '*',
                            'WordID ='.$iBuffer['WordID']);
                        if($wordKlBuff){
                            foreach($wordKlBuff as $jWordKlBuffer){
                                if($jWordKlBuffer['KlID']==$knowledgeStr){
                                    unset($wordArr[$i]);
                                    break;
                                }
                            }
                        }
                    }
                }
                if(!$wordArr[$i]) continue;

                $data=array();
                $data['Word']=$iWordArr;
                if($weightStr!=$weight2Str){
                    $data['Weight']=$weightStr;
                }

                if(($lastid=$word->insertData(
                        $data))===false){
                    $error='13102'; //添加分词失败！
                }else{
                    //添加分词与知识点对应
                    $data=array();
                    $data['KlID']=$knowledgeStr;
                    $data['WordID']=$lastid;
                    $wordKl->insertData(
                        $data);
                    //写入日志
                    $this->adminLog($this->moduleName,'添加分词【'.$_POST['Word'].'】');
                    $success='添加分词成功！';
                }
                if($error){
                    break;
                }
            }
            if(!$wordArr) $error='13103';//$error='分词已经存在！';
            if($error) $this->setError($error);//$this->showerror($error);
            if($success) $this->showSuccess($success,__URL__);
        }else if($act=='edit'){
            $wordID=$_POST['WordID'];
            $newWord=$_POST['Word'];
            $weight=$_POST['Weight2'];

            $data['WordID']=$wordID;
            $data['Word']=$newWord;
            $data['Weight']=$weight;
            $whe='WordID='.$wordID;
            if($word->updateData(
                    $data,
                    $whe)===false){
                $this->setError('13104'); //修改分词失败！
            }else{
                //修改word与知识点对应
                $data=array();
                $data['KlID']=$knowledgeStr;

                $wordKlBuff= $wordKl->selectData(
                    '*',
                    'WordID='.$wordID);
                if($wordKlBuff){
                    $wordKl->updateData($data,
                        'WordID in ('.$wordID.')');
                }else{
                    $data['WordID']=$wordID;
                    $$wordKl->insertData(
                        $data);
                }
                //写入日志
                $this->adminLog($this->moduleName,'修改WordID为【'.$_POST['WordID'].'】的数据');
                $this->showSuccess('修改分词成功！',__URL__);
            }
        }
    }
    /*删除分词*/
    public function delete(){
        $wordID=$_POST['id'];    //获取数据标识
        if(!$wordID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        if( $this->getModel('Word')->deleteData(
                'WordID in ('.$wordID.')')===false){
            $this->setError('13105','',__URL__); //删除分词失败！
        }else{
            //删除分词对应知识点
            $this->getModel('WordKl')->deleteData(
                'WordID in ('.$wordID.')');
            
            //写入日志
            $this->adminLog($this->moduleName,'删除分词WordID为【'.$wordID.'】的数据');
            $this->showSuccess('删除分词成功！',__URL__);
        }
    }
}
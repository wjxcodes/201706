<?php
/**
 * @author demo
 * @date 2014年11月12日
 */
/**
 * 章节管理类，用于章节管理的相关操作
 */
namespace Manage\Controller;
class ChapterController extends BaseController  {
    var $moduleName='章节配置';
    /**
     * 章节浏览
     * @author demo
     */
    public function index() {
        $pageName = '章节管理';
        $chapter = $this->getModel('Chapter');
        $pID=$_GET['pID'];
        $ifShow=$_GET['ifShow'];//父类是否是隐藏的
        $lft=0; //要查询的同级的左值
        $pLft=0; //pID左值
        $pRgt=0; //pID右值
        if(!$pID) $pID=0;
        else{
            $buffer=$chapter->selectData(
                '*',
                'ChapterID='.$pID);
            if(!$buffer){
                $this->setError('30301'); //数据标识不存在！
            }
            $lft=$buffer[0]['Lft']+1;
            $pLft=$buffer[0]['Lft'];
            $pRgt=$buffer[0]['Rgt'];
        }
        $subjectArray=SS('subject');

        //获取左值为索引的数据
        $buffer=$chapter->selectData(
            '*',
            '1=1',
            'Lft ASC');
        $left=array();
        foreach($buffer as $i=>$iBuffer){
            $left[$iBuffer['Lft']]=$iBuffer;
        }
        $chapterArray = $chapter->getSimilar($left,$lft); //获取同级数据集
		//dump($left);die;
        if($this->ifSubject && $this->mySubject){
            foreach($chapterArray as $i=>$iChapterArray){
                if(!in_array($iChapterArray['SubjectID'],explode(',',$this->mySubject))){
                    unset($chapterArray[$i]);
                }
            }
        }
        if($chapterArray){
            foreach($chapterArray as $i=>$iChapterArray){
                if($iChapterArray['Lft']<$iChapterArray['Rgt']-1)
                $chapterArray[$i]['HaveChild']='＋ ';
                else
                $chapterArray[$i]['HaveChild']='－ ';
                if($ifShow===0){
                    $chapterArray[$i]['IfShow']=0;
                }
                $chapterArray[$i]['SubjectName']=$subjectArray[$subjectArray[$chapterArray[$i]['SubjectID']]['PID']]['SubjectName'].' >> '.$subjectArray[$chapterArray[$i]['SubjectID']]['SubjectName'];
                
                if($i==0){
                    $chapterArray[$i]['Order']='<a href="javascript:void(0);" class="orderdown" url="'.U('Chapter/order?id='.$iChapterArray['ChapterID'].'&down=1').'"> ↓ </a>';
                }else if($i==count($chapterArray)-1){
                    $chapterArray[$i]['Order']='<a href="javascript:void(0);" class="orderup" url="'.U('Chapter/order?id='.$iChapterArray['ChapterID'].'&up=1').'"> ↑ </a>';
                }else{
                    $chapterArray[$i]['Order']='<a href="javascript:void(0);" class="orderup" url="'.U('Chapter/order?id='.$iChapterArray['ChapterID'].'&up=1').'"> ↑ </a> <a href="javascript:void(0);" class="orderdown" url="'.U('Chapter/order?id='.$iChapterArray['ChapterID'].'&down=1').'"> ↓ </a>';
                }
            }
        }
        if($pID){
            //父类路径
            $bufferx=$chapter->selectData(
                '*',
                'Lft<='.$pLft.' AND Rgt>='.$pRgt,
                'Lft asc');
            $chapterParent=array();
            foreach($bufferx as $iBufferx){
                $chapterParent[]='<a href="'.U('Chapter/index?pID='.$iBufferx['ChapterID']).'">'.$iBufferx['ChapterName'].'</a>';
            }
            $chapterPath='<a href="'.__URL__.'">顶级分类</a> >> '.$subjectArray[$bufferx[0]['SubjectID']]['SubjectName'].'>>'.implode(' >> ',$chapterParent);
        }else{
            $chapterPath='<a href="'.__URL__.'">顶级分类</a>';
        }

        /*载入模板标签*/
        $this->assign('chapterPath', $chapterPath); //章节路径
        $this->assign('chapterArray', $chapterArray); //章节数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 章节排序
     * @author demo
     */
    public function order(){
        $id=$_GET['id'];
        $up=$_GET['up'];
        $down=$_GET['down'];
        if(!$id || (!$up && !$down)){
            $this->setError('30301',1); // 数据标识不能为空！
        }
           $chapter=$this->getModel('Chapter');
        $buffer=$chapter->selectData(
            '*',
            'ChapterID='.$id);
        if(!$buffer){
            $this->setError('10108',1); //提交数据不存在！
        }
        //向上 等于上面的id向下
        if($up){
            $buffer=$chapter->selectData(
                '*',
                'Rgt='.($buffer[0]['Lft']-1));
            if(!$buffer){
                $this->setError('10502',1); // 提交数据已经在最上面！，无法向上移动
            }
            $down=1;
        }
        if($down){
            $bufferx=$chapter->selectData(
                '*',
                'Lft='.($buffer[0]['Rgt']+1));
            if(!$bufferx){
                $this->setError('10503',1); //提交数据已经在最下面！，无法向下移动
            }
            //获取下一移动项数据集合
            $bufferList=$chapter->selectData(
                '*',
                'Lft>='.$bufferx[0]['Lft'].' AND Rgt<='.$bufferx[0]['Rgt']);
            $idList=array();
            foreach($bufferList as $iBufferList){
                $idList[]=$iBufferList['ChapterID'];
            }
            //修改本移动项数据集合
            $yiDong=$bufferx[0]['Rgt']-$bufferx[0]['Lft']+1;//移动次数
            $chapter->conAddData(
                'Lft=Lft+'.$yiDong,
                'Lft>='.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt']);
            $chapter->conAddData(
                'Rgt=Rgt+'.$yiDong,
                'Lft>='.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt']);
            //修改下一移动项数据集合
            $yiDong=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;//移动次数
            $ChapterIDArr=implode(',',$idList);
            $chapter->conAddData(
                'Lft=Lft-'.$yiDong,
                'ChapterID in ('.$ChapterIDArr.')');
            $chapter->conAddData(
                'Rgt=Rgt-'.$yiDong,
                'ChapterID in ('.$ChapterIDArr.')');
        }
        //写入日志
        if($up){
            $this->adminLog($this->moduleName,'向上移动chapterID为【'.$id.'】的数据');
        }else{
            $this->adminLog($this->moduleName,'向下移动chapterID为【'.$id.'】的数据');
        }
        $this->setBack('success');
    }
    /**
     * 添加章节
     * @author demo
     */
    public function add() {
        $pageName = '添加章节';
        $act = 'add'; //模板标识
        $chapter = $this->getModel('Chapter');
        $subjectArray =  SS('subjectParentId'); //获取学科数据集
        $pID=$_GET['pid'];
        if($pID){
            $buffer=$chapter->selectData(
                '*',
                'ChapterID='.$pID);
            if($buffer){
                $edit['SubjectID']=$buffer[0]['SubjectID'];
                $buffer=$chapter->selectData(
                    '*',
                    'Lft<='.$buffer[0]['Lft'].' and Rgt>='.$buffer[0]['Rgt'],
                    'Lft asc');
                if(count($buffer)==1){
                    $edit['TID']=$pID;
                    $edit['PID']=0;
                }else{
                    $edit['TID']=$buffer[0]['ChapterID'];
                    $edit['PID']=$pID;
                }
            }
        }
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('edit', $edit); //学科数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑章节
     * @author demo
     */
    public function edit() {
        $chapterID=$_GET['id']; //获取数据标识
        //判断数据标识
        if(empty($chapterID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑章节';
        $act = 'edit'; //模板标识
        $chapter = $this->getModel('Chapter');
        $edit = $chapter->selectData(
            '*',
            'ChapterID='.$chapterID); //当前数据集
        if($this->ifSubject && $this->mySubject){
            if(!in_array($edit[0]['SubjectID'],explode(',',$this->mySubject))){
                $this->setError('30712',1); //您不能编辑非所属学科章节！
            }
        }
        //获取章节对应知识点
        $buffer=$this->getModel('ChapterKl')->selectData(
            '*',
            'CID='.$chapterID);
        $edit[0]['KlID']=0;
        if($buffer){
            $tmpKl=array();
            foreach($buffer as $iBuffer){
                $tmpKl[]=$iBuffer['KID'];
            }
            $edit[0]['KlID']=implode(',',$tmpKl);
        }
        unset($chapterKl);
        //获取章节对应关键字
        $chapterKey = $this->getModel('ChapterKey');
        $buffer=$chapterKey->selectData(
            '*',
            'ChapterID='.$chapterID);
        $edit[0]['Keyword']='';
        if($buffer){
            $tmpKey=array();
            foreach($buffer as $iBuffer){
                $tmpKey[]=$iBuffer['Keyword'];
            }
            $edit[0]['Keyword']=implode("\n",$tmpKey);
        }
        unset($chapterKey);
        //获取学科对应章节模式
        $subjectArray=SS('subject');
        $edit[0]['ChapterSet']= $subjectArray[$edit[0]['SubjectID']]['ChapterSet'];
        unset($subjectArray);
        //查找父类id
        $pID=0;
        $tID=0;
        $buffer=$chapter->selectData(
            '*',
            ' Lft< '.$edit[0]['Lft'].' AND Rgt>'.$edit[0]['Rgt'],
            'Lft desc');
        if($buffer){
            if(count($buffer)==1) $tID=$buffer[0]['ChapterID'];
            else{
                $pID=$buffer[0]['ChapterID'];
                $tID=$buffer[count($buffer)-1]['ChapterID'];
            }
        }
        $edit[0]['PID']=$pID;
        $edit[0]['TID']=$tID;
        $subjectArray =  SS('subjectParentId'); //获取学科数据集
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('subjectArray', $subjectArray); //学科数据集
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign('pageName', $pageName);
        $this->display('Chapter/add');
    }
    /**
     * 保存章节
     * @author demo 
     */
    public function save() {
        $chapterID=$_POST['ChapterID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($chapterID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $chapter = $this->getModel('Chapter');
        $subjectID=$_POST['SubjectID'];
        $data=array();
        $data['ChapterName']=formatString('changeStr2Html',$_POST['ChapterName']);
        $data['SubjectID']=$subjectID;
        $data['IfShow'] = $_POST['ifShow'];
        $pID=$_POST['PID'];//父ID
        $tID=$_POST['TID'];//版本
        if($chapterID==$pID){
            $this->setError('10115'); //不能选择当前章节！
        }
        if($chapterID==$tID){
            $this->setError('10116'); //不能选择当前版本！
        }

        $kl=$_POST['kl'];
        $keyWord=$_POST['keyword'];
        if($act=='add'){
            if($this->ifSubject && $this->mySubject){
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能选择非所属学科章节！
                }
            }
            if($tID==0){
                $buffer=$chapter->maxData(
                    'Rgt');
                if($buffer){
                    $data['Lft']=$buffer+1;
                    $data['Rgt']=$buffer+2;
                }else{
                    $data['Lft']=0;
                    $data['Rgt']=1;
                }
            }else{
                if($pID==0) $chapterID=$tID;
                else $chapterID=$pID;
                $buffer=$chapter->selectData(
                    '*',
                    'ChapterID='.$chapterID);
                $chapter->conAddData(
                    'Lft=Lft+2',
                     'Lft>'.$buffer[0]['Rgt']);
                $chapter->conAddData(
                    'Rgt=Rgt+2',
                    'Rgt>='.$buffer[0]['Rgt']
                    );
                $data['Lft']=$buffer[0]['Rgt'];
                $data['Rgt']=$buffer[0]['Rgt']+1;
            }
            if(($chapterID=$chapter->insertData(
                    $data))===false){
                $this->setError('30310'); //添加失败！
            }else{
                if($kl){
                    //添加知识点章节中间表
                    $chapterKl = $this->getModel('ChapterKl');
                    if(!is_array($kl)) $kl=array($kl);
                    foreach($kl as $iKl){
                        $data=array();
                        $data['CID']=$chapterID;
                        $data['KID']=$iKl;
                        $chapterKl->insertData(
                            $data);
                    }
                    unset($chapterKl);
                    unset($kl);
                }
                //添加关键字章节中间表
                $subjectArray=SS('subject');
                if($subjectArray[$subjectID]['ChapterSet']==1){
                    $chapterKey = $this->getModel('ChapterKey');
                    $tmpArray = array_filter(explode(PHP_EOL,$keyWord));
                    foreach($tmpArray as $iTmpArray){
                        $iTmpArray=trim($iTmpArray);
                        if(empty($iTmpArray)) continue;
                        $data=array();
                        $data['ChapterID']=$chapterID;
                        $data['Keyword']=$iTmpArray;
                        $data['SubjectID']=$subjectID;
                        $chapterKey->insertData(
                            $data);
                    }
                    unset($chapterKey);
                    unset($tmpArray);
                }
                //写入日志
                $this->adminLog($this->moduleName,'添加章节【'.$_POST['ChapterName'].'】');
                $this->showSuccess('添加成功！请更新缓存后生效',__URL__);
            }
        }else if($act=='edit'){
            if($this->ifSubject && $this->mySubject){
                $subject = $chapter->selectData(
                    'SubjectID',
                    'ChapterID='.$chapterID);
                if(!in_array($subject[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑非所属学科章节！
                }
                if(!in_array($data['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能编辑为非所属学科章节！
                }
            }
            if(empty($pID)) $pID=$tID;
            if(empty($pID)) $pID=0;
            
            //获取当前值
            $buffer=$chapter->selectData(
                '*','ChapterID = '.$chapterID);
            //获取父类
            $bufferx=$chapter->selectData(
                '*',
                'Lft< '.$buffer[0]['Lft'].' and Rgt>'.$buffer[0]['Rgt'],
                'Lft desc',
                1);
            $selfParentId=$bufferx[0]['ChapterID'];
            if(empty($selfParentId)) $selfParentId=0;
            //获取子类
            $buffery=$chapter->selectData(
                '*',
                'Lft>= '.$buffer[0]['Lft'].' and Rgt<='.$buffer[0]['Rgt'],
                'Lft asc');
            $idList=array();
            foreach($buffery as $bufferyn){
                $idList[]=$bufferyn['ChapterID'];//子类列表
            }
            $idListStr=implode(',',$idList);
            
            //父类不能调到子类下
            if(!empty($pID)){
                if(in_array($pID,$idList)){
                    $this->setError('10117'); //父类不能调整到子类下！
                    exit;
                }
            }
            
            //父类id不相同
            if($selfParentId!=$pID){
                if(!empty($pID)){
                    //获取新父类
                    $newbuffer=$chapter->selectData(
                        '*',
                        'ChapterID = '.$pID);
                    $yidong=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;
                    if($newbuffer[0]['Rgt']>$buffer[0]['Rgt']){
                        $chapter->conAddData(
                            'Lft=Lft-'.$yidong ,
                            'Lft>'.$buffer[0]['Rgt'].' and Rgt<='.$newbuffer[0]['Rgt']);
                        $chapter->conAddData(
                            'Rgt=Rgt-'.$yidong ,
                            'Rgt>'.$buffer[0]['Rgt'].' and Rgt<'.$newbuffer[0]['Rgt']);
                        $tmp_yidong=$newbuffer[0]['Rgt']-$buffer[0]['Rgt']-1;
                        $chapter->conAddData(
                            'Lft=Lft+'.$tmp_yidong .',Rgt=Rgt+'.$tmp_yidong ,
                            'ChapterID in ('.$idListStr.')');
                    }else{
                        $chapter->conAddData(
                            'Lft=Lft+'.$yidong ,
                            'Lft>'.$newbuffer[0]['Rgt'].' and Lft<'.$buffer[0]['Lft']);
                        $chapter->conAddData(
                            'Rgt=Rgt+'.$yidong ,
                            'Rgt>='.$newbuffer[0]['Rgt'].' and Rgt<'.$buffer[0]['Lft']);
                        $tmp_yidong=$buffer[0]['Lft']-$newbuffer[0]['Rgt'];
                        $chapter->conAddData(
                            'Lft=Lft-'.$tmp_yidong .',Rgt=Rgt-'.$tmp_yidong ,
                            'ChapterID in ('.$idListStr.')');
                    }
                }else{
                    //获取数据最大右值
                    $maxrgt=$chapter->maxData(
                            'Rgt');
                    //移动到最后
                    $yidong=$maxrgt+1-$buffer[0]['Lft'];
                    $chapter->conAddData('Lft=Lft+'.$yidong.',Rgt=Rgt+'.$yidong,'ChapterID in ('.$idListStr.')');
                    //原位置下之后的数据减
                    $yidong=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;
                    $chapter->conAddData('Lft=Lft-'.$yidong , 'Lft>='.$buffer[0]['Rgt']);
                    $chapter->conAddData('Rgt=Rgt-'.$yidong , 'Rgt>='.$buffer[0]['Rgt']);
                }
            }
            if($chapter->updateData(
                    $data,
                    'ChapterID='.$chapterID)===false){
                $this->showerror('修改失败！');
            }else{
                //修改章节知             点中间表
                $chaterKl=$this->getModel('ChapterKl');
                $buffer=$chaterKl->selectData(
                        '*',
                        'CID='.$chapterID);
                if($kl){ //存在post kl数据
                    if(!is_array($kl)) $kl=array($kl);
                    if($buffer){ //数据表中存在数据
                        $delID=array(); //数据表中多余的数据id集合
                        for($i=0;$i<count($buffer);$i++){
                            if($i>=count($kl)){
                                $delID[]=$buffer[$i]['CkID'];
                                continue;
                            }
                            $data=array();
                            $data['CID']=$chapterID;
                            $data['KID']=$kl[$i];
                            $data['CkID']=$buffer[$i]['CkID'];
                            $chaterKl->updateData($data,'CkID='.$buffer[$i]['CkID']);
                        }
                        if($delID) $chaterKl->deleteData(
                            'CkID in('.implode(',',$delID).')');
                        if($i<count($kl)){ //数据表中数据不够则添加
                            for(;$i<count($kl);$i++){
                                $data=array();
                                $data['CID']=$chapterID;
                                $data['KID']=$kl[$i];
                                $chaterKl->insertData(
                                    $data);
                            }
                        }
                    }else{ //不存在则添加
                        foreach($kl as $iKl){
                            $data=array();
                            $data['CID']=$chapterID;
                            $data['KID']=$iKl;
                            $chaterKl->insertData(
                                $data);
                        }
                    }
                }else if($buffer){ //不存在则删除已有数据
                    $delID=array();
                    foreach($buffer as $iBuffer){
                        $delID[]=$iBuffer['CkID'];
                    }
                    $chaterKl->deleteData(
                        'CkID in('.implode(',',$delID).')');
                }
                //修改章节关键字中间表
                $subjectArray=SS('subject');
                $chapterKey=$this->getModel('ChapterKey');
                if($subjectArray[$subjectID]['ChapterSet']==1){
                    $buffer=$chapterKey->selectData(
                        '*',
                        'ChapterID='.$chapterID);
                    $tmpArray = array_filter(explode(PHP_EOL,$keyWord));
                    //处理$tmpArray
                    if($tmpArray){
                        foreach($tmpArray as $i=>$iTmpArray){
                            $tmpArray[$i]=trim($iTmpArray);
                            if(empty($iTmpArray)) unset($tmpArray[$i]);
                        }
                    }
                    if($tmpArray){ //存在post keyword数据
                        if($buffer){ //存在数据表keyword数据
                            $delID=array(); //多余数据表keyword数据id集合
                            //修改
                            for($i=0;$i<count($buffer);$i++){
                                if($i>=count($tmpArray)){
                                    $delID[]=$buffer[$i]['KeyID'];
                                    continue;
                                }
                                $data=array();
                                $data['ChapterID']=$chapterID;
                                $data['Keyword']=$tmpArray[$i];
                                $data['SubjectID']=$subjectID;
                                $data['KeyID']=$buffer[$i]['KeyID'];
                                $chapterKey->updateData(
                                    $data,
                                    'KeyID='.$buffer[$i]['KeyID']);
                            }
                            if($delID) $chapterKey->deleteData(
                                'KeyID in('.implode(',',$delID).')');
                            if($i<count($tmpArray)){ //数据表中数据不够则添加
                                for(;$i<count($tmpArray);$i++){
                                    $data=array();
                                    $data['ChapterID']=$chapterID;
                                    $data['Keyword']=$tmpArray[$i];
                                    $data['SubjectID']=$subjectID;
                                    $chapterKey->insertData(
                                        $data);
                                }
                            }
                        }else{ //不存在则添加
                            foreach($tmpArray as $i=>$iTmpArray){
                                $data=array();
                                $data['ChapterID']=$chapterID;
                                $data['Keyword']=$iTmpArray;
                                $data['SubjectID']=$subjectID;
                                $this->getModel('ChapterKey')->insertData(
                                    $data);
                            }
                        }
                    }else if($buffer){ //删除多余数据
                        $delID=array();
                        foreach($buffer as $iBuffer){
                            $delID[]=$iBuffer['KeyID'];
                        }
                        $chapterKey->deleteData(
                            'KeyID in('.implode(',',$delID).')');
                    }
                }
                //写入日志
                $this->adminLog($this->moduleName,'修改ChapterID为【'.$chapterID.'】的数据');
                $this->showSuccess('修改成功，请更新缓存后生效！',__URL__);
            }
        }
    }

    /**
     * 删除章节
     * @author demo 
     */
    public function delete() {
        $chapterID=$_POST['id'];    //获取数据标识
        if(!$chapterID){
            $this->setError('30301','',__URL__,''); //数据标识不能为空！
        }
        $chapter=$this->getModel('Chapter');
        $chapterList=explode(',',$chapterID);
        foreach($chapterList as $iChapterList){
            $buffer=$chapter->selectData(
                '*',
                'ChapterID = '.$iChapterList);
            if($this->ifSubject && $this->mySubject){
                if(!in_array($buffer[0]['SubjectID'],explode(',',$this->mySubject))){
                    $this->setError('30507'); //您不能删除非所属学科章节！
                }
            }
            $yiDong=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;

            //获取章节所有子类
            $childChapter=$chapter->selectData(
                'ChapterID',
                'Lft >='.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt']);
            $childChapterID=array();
            foreach($childChapter as $jChildChapter){
                $childChapterID[]=$jChildChapter['ChapterID'];
            }
            $childChapterID=implode(',',$childChapterID);
            //删除对应知识点中间表
            $this->getModel('ChapterKl')->deleteData(
                'CID in ('.$childChapterID.')');
            //删除对应关键字中间表
            $this->getModel('ChapterKey')->deleteData(
                'ChapterID in ('.$childChapterID.')');
            //删除对应试题中间表
            $this->getModel('TestChapter')->deleteData(
                'ChapterID in ('.$childChapterID.')');
            //删除对应试题入库中间表
            $this->getModel('TestChapterReal')->deleteData(
                'ChapterID in ('.$childChapterID.')');
            //删除对应个人试题中间表
            $this->getModel('CustomTestChapter')->deleteData(
                'ChapterID in ('.$childChapterID.')');
            //删除对应个人临时试题中间表
            $this->getModel('CustomTestChapterCopy')->deleteData(
                'ChapterID in ('.$childChapterID.')');
            //删除对应用户测试版本选择中间表
            $this->getModel('UserChapter')->deleteData(
                'ChapterID in ('.$childChapterID.')');

            //删除数据
            if($chapter->deleteData(
                    'Lft >='.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt'])===false){
                $this->setError('30302'); //删除失败！
            }
            //改变左右值
            $chapter->conAddData('Lft=Lft-'.$yiDong,'Lft>'.$buffer[0]['Rgt']);
            $chapter->conAddData('Rgt=Rgt-'.$yiDong,'Rgt>'.$buffer[0]['Rgt']);
        }
        //写入日志
        $this->adminLog($this->moduleName,'删除ChapterID为【'.$chapterID.'】的数据');
        $this->showSuccess('删除成功！请更新缓存后生效',__URL__);
    }

    /**
     * 生成章节缓存
     * @author demo
     */
    public function updateCache() {
        $chapter=$this->getModel('Chapter');
        $chapter->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('生成缓存成功！',__URL__);
    }

    /**
     * 获取章节
     */
    public function getzsd($param){
        if(!empty($param)){
            extract($param);
        }else{
            $subjectID=$_GET['s'];
            $tID=$_GET['t'];
            $chapterID=$_GET['z'];
        }
        if(!$subjectID) exit();
        if(!$chapterID){
            $chapterID=0;
        }
        $chapter=$this->getModel('Chapter');
        if(!$tID){
            $buffer=$chapter->selectData(
                'Lft',
                'SubjectID='.$subjectID,
                'Lft asc',
                '0,1');
            if($buffer){
                $arr=array();
                $buffer=$chapter->get_similar($arr,$buffer[0]['Lft'],$subjectID);
                foreach($buffer as $i=>$buffern){
                    if($buffern['SubjectID']!=$subjectID){
                        unset($buffer[$i]);
                    }
                }
            }
        }else{
            $buffer=$chapter->selectData(
                '*',
                'chapterID='.$tID);
            $buffer=$chapter->selectData(
                '*',
                'Lft>'.$buffer[0]['Lft'].' and Rgt<'.$buffer[0]['Rgt'],
                'Lft asc');
        }
        return $chapter->getListOption($buffer,$chapterID);
    }
    /**
     * ajax一次请求返回多种数据
     * @author demo
     */
    public function getMsg(){
        $subjectID=$_GET['s'];
        $tID=$_GET['t'];
        $chapterID=$_GET['z'];
        if(!$chapterID){
            $chapterID=0;
        }
        if($subjectID){
            $paramArr[0]['subjectID']=$subjectID;
            $paramArr[0]['chapterID']=$chapterID;
        }
        if($subjectID && $tID){
            $paramArr[1]['subjectID']=$subjectID;
            $paramArr[1]['chapterID']=$chapterID;
            $paramArr[1]['tID']=$tID;
        }
        foreach($paramArr as $iParamArr){
            $result[]=$this->getzsd($iParamArr);
        }
        $this->setback($result);
    }
}
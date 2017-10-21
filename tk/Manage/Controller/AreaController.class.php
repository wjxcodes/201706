<?php
/**
 * @author demo
 * @date 2014年12月26日
 */
/**
 * 地区控制器类，用于处理地区相关操作
 */
namespace Manage\Controller;
class AreaController extends BaseController  {
    var $moduleName='地区配置';
    /**
     * 浏览地区列表；
     * @author demo
     */
    public function index() {
        $pageName = '地区管理';
        $area = $this->getModel('Area');
        $pid=$_GET['pid'];
        $lft=0; //要查询的同级地区的左值
        $pLft=0; //pid左值
        $pRgt=0; //pid右值
        if(!$pid){
            $pid=0;
        }else{
            //判断地区是否存在
            $buffer=$area->selectData(
                '*',
                'AreaID='.$pid);
            if(!$buffer){
                $this->setError('30301'); //数据标识不存在！
            }
            $lft=$buffer[0]['Lft']+1;
            $pLft=$buffer[0]['Lft'];
            $pRgt=$buffer[0]['Rgt'];
        }
        //获取左值为索引的数据
        $buffer=$area->selectData(
            '*',
            '1=1',
            'Lft ASC');
        $left=array();
        foreach($buffer as $i=>$iBuffer){
            $left[$iBuffer['Lft']]=$iBuffer;
        }
        $areaArray = $area->getSimilar($left,$lft,1); //获取同级数据集
        //为数据增加是否有子类标记
        if($areaArray){
            foreach($areaArray as $i=>$iAreaArray){
                if($iAreaArray['Lft']<$iAreaArray['Rgt']-1)
                $areaArray[$i]['HaveChild']='＋ ';
                else
                $areaArray[$i]['HaveChild']='－ ';
                
                if($i==0){
                    $areaArray[$i]['Order']='<a href="'.U('Area/order?id='.$iAreaArray['AreaID'].'&down=1').'"> ↓ </a>';
                }else if($i==count($areaArray)-1){
                    $areaArray[$i]['Order']='<a href="'.U('Area/order?id='.$iAreaArray['AreaID'].'&up=1').'"> ↑ </a>';
                }else{
                    $areaArray[$i]['Order']='<a href="'.U('Area/order?id='.$iAreaArray['AreaID'].'&up=1').'"> ↑ </a> <a href="'.U('Area/order?id='.$iAreaArray['AreaID'].'&down=1').'"> ↓ </a>';
                }
            }
        }
        //为数据增加父类路径
        if($pid){
            $buffer=$area->selectData(
                '*',
                'Lft<='.$pLft.' AND Rgt>='.$pRgt,
                'Lft asc');
            $areaParent=array();
            foreach($buffer as $i=>$iBuffer){
                $areaParent[]='<a href="'.U('Area/index?pid='.$iBuffer['AreaID']).'">'.$iBuffer['AreaName'].'</a>';
            }
            $areaPath='<a href="'.__URL__.'">顶级分类</a> >> '.implode(' >> ',$areaParent);
        }else{
            $areaPath='<a href="'.__URL__.'">顶级分类</a>';
        }
        
        /*载入模板标签*/
        $this->assign('areaPath', $areaPath); //路径
        $this->assign('areaArray', $areaArray); //数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 排序地区数据；
     * @author demo
     */
    public function order(){
        $id=$_GET['id'];
        $up=$_GET['up'];
        $down=$_GET['down'];
        if(!$id || (!$up && !$down)){
            $this->setError('30301'); //数据标识不能为空！
        }
           $area=$this->getModel('Area');
        $buffer=$area->selectData(
            '*',
            'AreaID='.$id);
        if(!$buffer){
            $this->setError('10501'); //提交数据不存在！
        }
        //向上 等于上面的id向下
        if($up){
            $buffer=$area->selectData(
                '*',
                'Rgt='.($buffer[0]['Lft']-1));
            if(!$buffer){
                $this->setError('10502'); //提交数据已经在最上面！，无法向上移动;
            }
            $down=1;
        }
        if($down){
            $bufferx=$area->selectData(
                '*',
                'Lft='.($buffer[0]['Rgt']+1));
            if(!$bufferx){
                $this->setError('10503'); //提交数据已经在最下面！，无法向下移动'
            }
            //获取下一移动项数据集合
            $bufferList=$area->selectData(
                '*',
                'Lft>='.$bufferx[0]['Lft'].' AND Rgt<='.$bufferx[0]['Rgt']);
            $idList=array();
            foreach($bufferList as $i=>$iBufferList){
                $idList[]=$iBufferList['AreaID'];
            }
            //修改本移动项数据集合
            $move=$bufferx[0]['Rgt']-$bufferx[0]['Lft']+1;//移动次数
            $area->conAddData(
                'Lft=Lft+'.$move.',Rgt=Rgt+'.$move,
                'Lft>='.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt']);
            //修改下一移动项数据集合
            $move=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;//移动次数
            $area->conAddData(
                'Lft=Lft-'.$move.',Rgt=Rgt-'.$move,
                'AreaID in ('.implode(',',$idList).')');
        }
        //写入日志
        if($up){
            $this->adminLog($this->moduleName,'向上移动AreaID为【'.$id.'】的数据');
        }else{
            $this->adminLog($this->moduleName,'向下移动AreaID为【'.$id.'】的数据');
        }
        
        //获取当前移动数据的父类id 做返回跳转页面用
        $bufferx=$area->selectData(
            '*',
            'Lft<'.$buffer[0]['Lft'].' AND Rgt>'.$buffer[0]['Rgt'],'Lft desc',
            1);
        if(!$bufferx) $pid=0;
        else $pid=$bufferx[0]['AreaID'];
        $this->showSuccess('移动成功！',U('Area/index',array('pid'=>$pid)));
    }
    /**
     * 添加地区数据；
     * @author demo
     */
    public function add() {
        $pageName = '添加地区';
        $act = 'add'; //模板标识
        $area = $this->getModel('Area');
        $areaOption = $area->getListOption(); //地区数据集
        
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('areaOption', $areaOption); //地区数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 编辑地区数据；
     * @author demo
     */
    public function edit() {
        $areaID=$_GET['id']; //获取数据标识
        //判断数据标识
        if(empty($areaID)){
            $this->setError('30301'); //数据标识不能为空！
        }
        $pageName = '编辑地区';
        $act = 'edit'; //模板标识
        $area = $this->getModel('Area');
        $edit = $area->selectData(
            '*',
            'AreaID='.$areaID);//当前数据集
        
        //查找父类id
        $pID=0;
        $buffer=$area->selectData(
            '*',
            ' Lft< '.$edit[0]['Lft'].' AND Rgt>'.$edit[0]['Rgt'],'Lft DESC',
            1);
        if($buffer){
            $pID=$buffer[0]['AreaID'];
        }
        $areaOption = $area->getListOption(0,$pID); //地区数据集
        
        /*载入模板标签*/
        $this->assign('act', $act); //模板标识
        $this->assign('edit', $edit[0]);//当前数据集
        $this->assign('areaOption', $areaOption); //权限数据集
        $this->assign('pageName', $pageName);
        $this->display('Area/add');
    }
    /**
     * 保存地区数据；
     * @author demo
     */
    public function save() {
        $areaID=$_POST['AreaID'];    //获取数据标识
        $act = $_POST['act'];    //获取模板标识
        //判断数据标识
        if(empty($areaID) && $act=='edit'){
            $this->setError('30301'); //数据标识不能为空！
        }
        if(empty($act)){
            $this->setError('30223'); //模板标识不能为空！
        }
        $data=array();
        $data['AreaName']=$_POST['AreaName'];
        $pID=$_POST['PID'];
        $area = $this->getModel('Area');
        if($act=='add'){
            if($pID==0){
                $buffer=$area->maxData(
                    'Rgt');
                if($buffer){
                    $data['Lft']=$buffer+1;
                    $data['Rgt']=$buffer+2;
                }else{
                    $data['Lft']=0;
                    $data['Rgt']=1;
                }
            }else{
                $buffer=$area->selectData(
                    '*',
                    'AreaID='.$pID);
                $area ->conAddData(
                    'Lft=Lft+2',
                    'Lft>'.$buffer[0]['Rgt']);
                $area ->conAddData(
                    'Rgt=Rgt+2',
                    'Rgt>='.$buffer[0]['Rgt']);
                $data['Lft']=$buffer[0]['Rgt'];
                $data['Rgt']=$buffer[0]['Rgt']+1;
            }
            if($area->insertData($data)===false){
                $this->setError('30310'); //添加失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'添加地区【'.$_POST['AreaName'].'】');
                $this->showSuccess('添加成功！请更新缓存后生效',__URL__);
            }
        }else if($act=='edit'){
            if(empty($pID)) $pID=0;
            
            //获取当前值
            $buffer=$area ->selectData(
                '*',
                'AreaID = '.$areaID);
            //获取父类
            $bufferx=$area ->selectData(
                '*',
                'Lft< '.$buffer[0]['Lft'].' AND Rgt>'.$buffer[0]['Rgt'],
                'Lft DESC',
                1);
            $selfParentID=$bufferx[0]['AreaID'];
            if(empty($selfParentID)) $selfParentID=0;
            //获取子类
            $buffery=$area ->selectData(
                '*',
                'Lft>= '.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt'],
                'Lft ASC');
            $idList=array();
            foreach($buffery as $iBuffery){
                $idList[]=$iBuffery['AreaID'];//子类列表
            }
            $idListStr=implode(',',$idList);
            
            //父类不能调到子类下
            if(!empty($pID)){
                if(in_array($pID,$idList)){
                    $this->setError('10117'); //父类不能调整到子类下！
                }
            }
            
            //父类id不相同
            if($selfParentID!=$pID){
                if(!empty($pID)){
                    //获取新父类
                    $newbuffer=$area ->selectData(
                        '*',
                        'AreaID = '.$pID);
                    $move=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;
                    if($newbuffer[0]['Rgt']>$buffer[0]['Rgt']){
                        $area ->conAddData(
                            'Lft=Lft-'.$move ,
                            ' Lft>'.$buffer[0]['Rgt'].' and Rgt<='.$newbuffer[0]['Rgt']);
                        $area ->conAddData(
                            'Rgt=Rgt-'.$move ,
                            ' Rgt>'.$buffer[0]['Rgt'].' and Rgt<'.$newbuffer[0]['Rgt']);
                        $tmpMove=$newbuffer[0]['Rgt']-$buffer[0]['Rgt']-1;
                        $area ->conAddData(
                            'Lft=Lft+'.$tmpMove .',Rgt=Rgt+'.$tmpMove ,
                            ' AreaID in ('.$idListStr.')');
                    }else{
                        $area ->conAddData(
                            'Lft=Lft+'.$move ,
                            ' Lft>'.$newbuffer[0]['Rgt'].' and Lft<'.$buffer[0]['Lft']);
                        $area ->conAddData(
                            'Rgt=Rgt+'.$move ,
                            ' Rgt>='.$newbuffer[0]['Rgt'].' and Rgt<'.$buffer[0]['Lft']);
                        $tmpMove=$buffer[0]['Lft']-$newbuffer[0]['Rgt'];
                        $area ->conAddData(
                            'Lft=Lft-'.$tmpMove .',Rgt=Rgt-'.$tmpMove ,
                            ' AreaID in ('.$idListStr.')');
                    }
                }else{
                    //获取数据最大右值
                    $maxrgt=$area ->maxData(
                        'Rgt');
                    //移动到最后
                    $move=$maxrgt+1-$buffer[0]['Lft'];
                    $area ->conAddData(
                        'Lft=Lft+'.$move.',Rgt=Rgt+'.$move,
                        ' AreaID in ('.$idListStr.')');
                    //原位置下之后的数据减
                    $move=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;
                    $area ->conAddData(
                        'Lft=Lft-'.$move ,
                        ' Lft>='.$buffer[0]['Rgt']);
                    $area ->conAddData(
                        'Rgt=Rgt-'.$move ,
                        ' Rgt>='.$buffer[0]['Rgt']);
                }
            }
            if($area ->updateData(
                    $data,
                    'AreaID ='.$areaID)===false){
                $this->setError('30311'); //修改失败！
            }else{
                //写入日志
                $this->adminLog($this->moduleName,'修改AreaID为【'.$areaID.'】的数据');
                $this->showSuccess('修改成功，请更新缓存后生效！',__URL__);
            }
        }
    }
    /**
     * 删除地区数据；
     * @author demo
     */
    public function delete() {
        $areaID=$_POST['id'];    //获取数据标识
        if(!$areaID){
            $this->setError('30301','',__URL__); //数据标识不能为空！
        }
        $area = $this->getModel('Area');
        $areaList=explode(',',$areaID);
        foreach($areaList as $iAreaList){
            $buffer=$area->selectData(
                '*',
                'AreaID ='.$iAreaList);
            $move=$buffer[0]['Rgt']-$buffer[0]['Lft']+1;
            //删除数据
            if($area->deleteData(
                'Lft >='.$buffer[0]['Lft'].' AND Rgt<='.$buffer[0]['Rgt'])===false){
                $this->setError('30302'); //删除失败！
            }
            //改变左右值
            $area ->conAddData('Lft=Lft-'.$move,' Lft>'.$buffer['Rgt']);
            $area ->conAddData('Rgt=Rgt-'.$move,' Rgt>'.$buffer['Rgt']);
        }
        //写入日志
        $this->adminLog($this->moduleName,'删除AreaID为【'.$areaID.'】的数据');
        $this->showSuccess('删除成功！请更新缓存后生效',__URL__);
    }
    /**
     * 更新地区缓存；
     * @author demo
     */
    public function updateCache() {
        $area=$this->getModel('Area');
        $area->setCache();
        //写入日志
        $this->adminLog($this->moduleName,'更新缓存');
        $this->showSuccess('生成缓存成功！',__URL__);
    }
}
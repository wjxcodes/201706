<?php
/**
 * @author demo
 * @date 2014年12月26日
 */
/**
 * 地区模型类，用于处理地区相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class AreaModel extends BaseModel{
    
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'Lft ASC');
    }
    /**
     * 更新缓存
     * @return array
     * @author demo
     */
    public function setCache(){
        $parentPathList=array(); // 父类路径path数据
        $childList=array(); // 缓存子类list数据 以id为键
        $areaIDList=array(); // 缓存子类list数据 以逗号间隔
        $areaArray=$this->selectData(
            '*',
            '1=1',
            'Lft ASC');
        if($areaArray){
            $left=array(); //以左值为索引的数据集 有小到大排序
            $right=array(); //以右值为索引的数据集 有小到大排序
            foreach($areaArray as $i=>$iAreaArray){
                //为省份增加是否是最后一级的属性
                $iAreaArray['Last']=0;
                if($iAreaArray['Lft']==$iAreaArray['Rgt']-1) $iAreaArray['Last']=1;
                $areaArray[$i]=$iAreaArray;

                $left[$iAreaArray['Lft']]=$iAreaArray;
                $right[$iAreaArray['Rgt']]=$iAreaArray;
            }
            ksort($right);

            $childList[0]=$this->getSimilar($left,0);
            foreach($areaArray as $i=>$iAreaArray){
                //根据id查找name
                $areaList[$iAreaArray['AreaID']]['AreaID']=$iAreaArray['AreaID'];
                $areaList[$iAreaArray['AreaID']]['AreaName']=$iAreaArray['AreaName'];
                $areaList[$iAreaArray['AreaID']]['Last']=$iAreaArray['Last'];
                //生成父类路径path缓存
                $parentPathList[$iAreaArray['AreaID']]=$this->getParent($left,$right,$iAreaArray['Lft']);

                if($iAreaArray['Lft']!=$iAreaArray['Rgt']-1){
                    //生成所有子类list缓存
                    $childList[$iAreaArray['AreaID']]=$this->getSimilar($left,$iAreaArray['Lft']+1);
                    //生成所有子类list缓存
                    $areaIDList[$iAreaArray['AreaID']]=$this->getSimilarID($left,$iAreaArray['Lft']+1);
                }
            }
        }
        S('areaParentPath',$parentPathList);  // 缓存父类路径path数据
        S('areaChildList',$childList);  // 缓存子类list数据
        S('areaList',$areaList);  // 缓存list数据 以id为键
        S('areaIDList',$areaIDList);  // 缓存子类id以逗号间隔
    }
    /**
     * 获取缓存；
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo,
     */
    public function getCache($str='areaList',$num=0){
        switch($str){
            case 'areaList':
                $buffer=S('areaList');  // 缓存子类list数据
                break;
            case 'areaParentPath':
                $buffer=S('areaParentPath');  // 缓存父类路径path数据
                break;
            case 'areaChildList':
                $buffer=S('areaChildList');  // 缓存子类list数据
                break;
            case 'areaIDList':
                $buffer=S('areaIDList');  // 缓存子类list数据
                break;
            default :
                return false;
                break;
        }
        if(empty($buffer) and $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
    /**
     * 获取同一级数据集
     * @param array $left 以Lft为索引的数组
     * @param int $lft 左值 必须从父类左值加1
     * @param int $more 是否需要全部数据字段 0不需要 1需要
     * @return array
     * @author demo
     */
    public function getSimilar($left,$lft,$more=0){
        if(!$left[$lft]){
            return '';
        }
        $output=array();
        if($more){
            $output[]=$left[$lft];
        }else{
            $output[]=array(
                'AreaID'=>$left[$lft]['AreaID'],
                'AreaName'=>$left[$lft]['AreaName'],
                'Last'=>$left[$lft]['Last'],
            );
        }

        $newLft=$left[$lft]['Rgt']+1;
        if($left[$newLft]){
            $tmpArr=$this->getSimilar($left,$newLft,$more);
            if(!empty($tmpArr)) $output=array_merge($output,$tmpArr);
        }
        return $output;
    }
    /**
     * 获取所有子类id以逗号间隔
     * @param array $left 以Lft为索引的数组
     * @param int $lft 左值 必须从父类左值加1
     * @return array
     * @author demo
     */
    public function getSimilarID($left,$lft){
        if(!$left[$lft]){
            return '';
        }
        $output=array();
        $output[]=$left[$lft]['AreaID'];
        //获取同级
        if($left[$left[$lft]['Rgt']+1]){
            $output[]=$this->getSimilarID($left,$left[$lft]['Rgt']+1);
        }
        //获取子类
        if($lft==$left[$lft]['Rgt']-1){
            return implode(',',$output);
        }else if($left[$lft+1]){
            $output[]=$this->getSimilarID($left,$lft+1);
        }
        return implode(',',$output);
    }
    /**
     * 获取父类路径
     * @param int $left 左值数据集
     * @param int $right 右值数据集
     * @param int $lft 左值
     * @return array
     * @author demo
     */
    public function getParent($left,$right,$lft){
        if($lft<1) return '';
        $newLeft=$lft-1;
        if($left[$newLeft]){
            $output=array();
            $output[]=array(
                'AreaID'=>$left[$newLeft]['AreaID'],
                'AreaName'=>$left[$newLeft]['AreaName'],
                'Last'=>$left[$newLeft]['Last'],
            );
            $tmpArr=$this->getParent($left,$right,$left[$newLeft]['Lft']);
            if(!empty($tmpArr)){
                $output=array_merge($tmpArr,$output);
            }
            return $output;
        }else{
            return $this->getParent($left,$right,$right[$newLeft]['Lft']);
        }
    }
    /**
     * 获取父类路径
     * @param int $parentID 父类id 为0代表顶级分类
     * @param int $thisID 当前id 为0代表没有当前id
     * @return array
     * @author demo
     */
    public function getListOption($parentID=0,$thisID=0){
        //获取节点的左右值
        $lft=0;$rgt=0;
        if($parentID==0){
            $buffer=$this->maxData(
                'Rgt');
            if(!$buffer) return;
            $rgt=$buffer;
        }else{
            $buffer=$this->selectData(
                '*',
                ' AreaID= '.$parentID);
            if(!$buffer) return ;
            $lft=$buffer[0]['Lft'];
            $rgt=$buffer[0]['Rgt'];
        }
        $right=array(); //存储右值的堆栈
        $buffer=$this->selectData(
            '*',
            'Lft>='.$lft.' AND Rgt<='.$rgt ,
            'Lft asc');

        $output=''; //要输出的数据集
        // 显示每一行
        foreach($buffer as $i=>$iBuffer){
            // only check stack if there is one
            if (count($right) > 0) {
                // 检查我们是否应该将节点移出堆栈
                while ($right[count($right) - 1] < $iBuffer['Rgt'] && count($right)>0) {
                    array_pop($right);
                }
            }
            $select='';
            if($thisID!=0 && $thisID==$iBuffer['AreaID']) $select=' selected="selected"';
            // 缩进显示节点的名称
            $output.='<option value="'.$iBuffer['AreaID'].'"'.$select.'>'.str_repeat('　',count($right)) . $iBuffer['AreaName'] . "</option>";
            // 将这个节点加入到堆栈中
            $right[] = $iBuffer['Rgt'];
        }
        return $output;
    }
    /**
     * 根据id获取select数据；
     * @param int $AreaID 地区id
     * @return string
     * @author demo
     */
    public function areaSelectByID($AreaID){
        $buffer2=$this->getAreaParentById($AreaID);
        $areaChildArray=$this->getCache('areaChildList');
        $areaInit='';
        if($buffer2){
            ksort($buffer2);
            $tmpArr=array();
            foreach($buffer2 as $iBuffer2){
                $tmpArr[]=$iBuffer2;
            }
            $buffer2=$tmpArr;
            $buffer2[]['AreaID']=$AreaID;
            foreach($buffer2 as $i=>$iBuffer2){
                if($i==0) $tmpId=0;
                else $tmpId=$buffer2[$i-1]['AreaID'];
                $buffer3=$areaChildArray[$tmpId];
                $areaInit.='<select name="AreaID[]" class="AreaID">';
                foreach($buffer3 as $j=>$jBuffer3){
                    $iflast=0;
                    $select='';
                    if($jBuffer3['Last']==1) $iflast=1;
                    if($jBuffer3['AreaID']==$iBuffer2['AreaID']) $select=' selected="selected" ';
                    $areaInit.='<option value="'.$jBuffer3['AreaID'].'" iflast="'.$iflast.'"'.$select.'>'.$jBuffer3['AreaName'].'</option>';
                }
                $areaInit.='</select>';
            }
        }
        return $areaInit;
    }
    //根据id获取area数据
    public function getAreaById($id){
        $buffer=$this->getCache('areaList');
        return $buffer[$id];
    }
    //根据id获取area路径数据
    public function getAreaParentById($id){
        $buffer=$this->getCache('areaParentPath');
        return $buffer[$id];
    }
    //根据id获取area路径
    /**
     * 描述：
     * @param int $id 地区ID
     * @param string $separate 分隔字符串
     * @return string 地区
     * @author
     */
    public function getAreaPathById($id,$separate = ' > '){
        $buffer=$this->getAreaParentById($id);
        $output=array();
        if($buffer){
            foreach($buffer as $buffern){
                $output[]=$buffern['AreaName'];
            }
            $buffer=$this->getAreaById($id);
            ksort($output);
            $output[]=$buffer['AreaName'];
        }
        return implode($separate,$output);
    }

    /**
     * 根据地区ID获取父类id以|间隔
     * @param AreaID 地区ID
     * @return string
     * @author demo
     */
    public function getAreaStr($areaID){
        //查找父类id
        $buffer=SS('areaParentPath');  // 缓存父类路径数据
        $bufferx=SS('areaChildList');  // 缓存子类list数据
        $areaArray=$bufferx[0];//获取省份

        $areaParent='';//父类路径包括自己
        $bufferTmp=array();
        if($buffer[$areaID]) krsort($buffer[$areaID]);
        if($buffer[$areaID]){
            foreach($buffer[$areaID] as $iBuffer){
                $bufferTmp[]=$iBuffer['AreaID'];
            }
            $areaParent='|'.implode('|',$bufferTmp).'|'.$areaID.'|';
        }else{
            $areaParent='|'.$areaID.'|';
        }
        return $areaParent;
    }
}
?>
<?php
/**
 * @author mabo
 * @date 2014年11月11日
 */
/**
 * 能力模型类，用于处理能力相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class CapacityModel extends BaseModel{
    private $primary='Capacity';
    private $primaryKey;
    private $primaryName;

    function __construct() {
        parent::__construct();
        $this->primaryKey=$this->primary.'ID';
        $this->primaryName=$this->primary.'Name';
    }

    /**
     * 获取所有数据
     * @return array
     * @author mabo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'OrderID asc,'.$this->primaryKey.' asc');
    }

    /**
     * 获取父类列表；
     * @author mabo
     */
    public function getParList($paramArr){
        if($paramArr['SubjectID']){
            $s = $paramArr['SubjectID'];
        }else{
            $s = 0;
        }
        if($s) {
            return $this->selectData(
                        '*',
                        'PID=0 AND SubjectID='.$s,
                        'OrderID ASC,'.$this->primaryKey.' ASC'
            );
        }else{
            return $this->selectData(
                    '*',
                    'PID=0',
                    'OrderID ASC,'.$this->primaryKey.' ASC'
            );
        }
    }

    /**
     * 获取多级列表树形结构
     * @param array $children 根据知识点id为索引的下一级数据集
     * @param array $klID 知识点id
     * @return mabo
     */
    public function getArrList3($children,$klID){
        $output = array();
        if(!$children[$klID]) return '';
        foreach($children[$klID] as $i => $iChildren){
            $output[$i]=$iChildren;
            if($children[$iChildren[$this->primaryKey]]){
                $tmpArr = $this->getArrList3($children,$iChildren[$this->primaryKey]);
                if(!empty($tmpArr))  $output[$i]['sub'] = $tmpArr;
            }
        }
        return $output;
    }

    /**
     * 缓存数组；
     * @author mabo
     */
    public function setCache(){
        //获取所有数据
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc,'.$this->primaryKey.' asc');
        if(!$buffer) return false;
        $pid = array(); //获取所有父类id数组 用途：判断当前id是否是父类id
        $children = array(); //获取下一级子类数据集 以当前id为索引
        $self = array(); //获取所有数据集 以当前id为索引
        $data = array();//存储所有子类id以逗号间隔
        $parent = array();//存储所有父类数据集
        $next = array();//存储下一级数据集
        $subject = array();//根据学科存储知识点树形结构
        foreach($buffer as $iBuffer){
            $pid[] = $iBuffer['PID'];
        }
        foreach($buffer as $iBuffer){
            if(in_array($iBuffer[$this->primaryKey],$pid)){
                $iBuffer['Last']=0;
            }else{
                $iBuffer['Last']=1;
            }
            $self[$iBuffer[$this->primaryKey]] = $iBuffer;
            $children[$iBuffer['PID']][] = $iBuffer;
            if($iBuffer['PID'] == 0) $subject[$iBuffer['SubjectID']][] = $iBuffer;
        }

        $pid = array_unique(array_filter($pid)); //去重，去空
        //获取以知识点id为索引的数据，除基础数据外获取是否是最后一级；获取当前数据的下一级；获取当前数据的父类路径
        $knowledgeArr = array();
        foreach($buffer as $iBuffer){
            $id=$iBuffer[$this->primaryKey];
            $knowledgeArr[$id][$this->primaryName] = $iBuffer[$this->primaryName];
            $knowledgeArr[$id]['PID'] = $iBuffer['PID'];
            $knowledgeArr[$id]['SubjectID'] = $iBuffer['SubjectID'];
            if(in_array($id,$pid)){
                $knowledgeArr[$id]['Last'] = 0;
                $buff = $children[$id];
                if($buff){
                    foreach($buff as $j => $jBuffn){
                        if(in_array($jBuffn[$this->primaryKey],$pid)) $buff[$j]['Last']=0;
                        else $buff[$j]['Last']=1;
                    }
                }
                $next[$id] = $buff; //下一级数据集
            }
            else $knowledgeArr[$id]['Last']=1;

            //所有父类数据集
            if($iBuffer['PID'] != 0){
                $parentID = $iBuffer['PID'];
                $parentPath = array();
                while(!empty($parentID)){
                    $parentPath[] = $self[$parentID];
                    $parentID = $self[$parentID]['PID'];
                }
                krsort($parentPath);
                $parent[$id] = $parentPath;
            }

            //所有子类以逗号间隔
            if($knowledgeArr[$id]['Last']!=1){
                $data[$id] = $this->getChildListByCache($children,$id);
            }
        }
        //生成学科树形结构
        foreach($subject as $i => $iSubject){
            foreach($iSubject as $j => $jSubject){
                $tmpstr=$this->getArrList3($children,$jSubject[$this->primaryKey]);
                if(!empty($tmpstr)) $subject[$i][$j]['sub'] = $tmpstr;
            }
        }
        S($this->primary.'List',$data);  // 缓存所有子类id以逗号间隔数据
        S($this->primary,$knowledgeArr);//所有知识点缓存 以id为索引
        S($this->primary.'Parent',$parent);//所有知识点对应父类路径数据集缓存
        S($this->primary.'Next',$next);//所有知识点对应下一级数据集缓存
        S($this->primary.'BySubject3',$subject);//根据学科区分知识点
    }

    /**
     * 获取缓存；
     * @author mabo
     */
    public function getCache($str = '',$num = 0){
        if(empty($str)) $str=$this->primary;
        switch ($str) {
            case $this->primary:
                $buffer = S($this->primary);
                break;
            case $this->primary.'BySubject3':
                $buffer = S($this->primary.'BySubject3');
                break;
            case $this->primary.'List':
                $buffer = S($this->primary.'List');
                break;
            case $this->primary.'Parent':
                $buffer = S($this->primary.'Parent');
                break;
            case $this->primary.'Next':
                $buffer = S($this->primary.'Next');
                break;
            default :
                return false;
        }
        if(empty($buffer) && $num == 0){
            $this->setCache();
            $buffer = $this->getCache($str,1);
        }
        return $buffer;
    }

    /**
     * 递归获取子类 by array
     * @param array $buffer 所有带有子类的数据集
     * @param int $id 知识点id
     * @return string
     * @author mabo
     */
    public function getChildListByCache($buffer,$id){
        $thisArr = $buffer[$id];
        if($thisArr){
            $output = array();
            foreach($thisArr as $iThisArr){
                $id=$iThisArr[$this->primaryKey];
                $output[] = $id;
                if($buffer[$id]){
                    $str = $this->getChildListByCache($buffer,$id);
                    if($str != '') $output[]=$str;
                }
            }
            return implode(',',$output);
        }
        return '';
    }

    /**
     * 根据id 获取知识点路径
     * @param array $paramArr 数组类型
     * @param index parent 知识点父类缓存
     * @param index self 知识点当前缓存
     * @param index ID 需要显示的知识点id 支持数组和逗号间隔字符串
     * @param index ReturnString 返回字符串的间隔方式 为空则返回数据
     * @return string|Array
     * @author mabo
     */
    public function getPath($paramArr){
        $parent=$paramArr['parent'];
        $self=$paramArr['self'];
        $output=array();

        $ID=$paramArr['ID'];
        if(is_array($ID)) $idList=$ID;
        else $idList=explode(',',$ID);

        foreach($idList as $i=>$iIdList){
            $output[$i][$this->primaryKey]=$iIdList;
            if($parent[$iIdList]){
                foreach($parent[$iIdList] as $j=>$jParent){
                    $output[$i][$this->primaryName].='>>'.$jParent[$this->primaryName];
                }
            }
            $output[$i][$this->primaryName].='>>'.$self[$iIdList][$this->primaryName];
        }

        if(!empty($paramArr['ReturnString'])){
            $tmp='';
            foreach($output as $i=>$iOutput){
                $tmpStr='('.($i+1).')';
                $tmp.=$tmpStr.$iOutput[$this->primaryName].$paramArr['ReturnString'];
            }
            $output=$tmp;
        }

        return $output;
    }
    //获取二级列表 添加新内容的时候会用到，所以不能删除
    public function getArrList($SubjectID=0){
        $node = array();
        if($SubjectID)
            $list = $this->selectData(
                '*',
                'PID=0 AND SubjectID='.$SubjectID,
                'OrderID ASC,'.$this->primaryKey.' ASC');
        else
            $list = $this->selectData(
                '*',
                'PID=0',
                'OrderID ASC,'.$this->primaryKey.' ASC');
        foreach ($list as $i => $iList) {
            $node[$i] = $iList;
            $listn = $this->selectData(
                '*',
                'PID='. $iList[$this->primaryKey],'OrderID ASC,'.$this->primaryKey.' ASC');
            $node[$i]['sub']=$listn;
        }
        return $node;
    }

    /**
     * 数组变option
     * @param $arr 需要处理的数组数据
     * @param $id=0 默认为0 选中的option项
     * @return string
     * @author mabo
     */
    public function setOption($arr,$id=0){
        $output='';
        foreach($arr as $iArr){
            $id=$iArr[$this->primaryKey];
            $output.='<option value="'.$id.'" '.($id==$id ? "selected=\"selected\"" : "") .'>'.$iArr[$this->primaryName].'</option>';
            if($iArr['sub'])
                foreach($iArr['sub'] as $jArr){
                    $output.='<option value="'.$jArr[$this->primaryKey].'" '.($jArr[$this->primaryKey]==$id ? "selected=\"selected\"" : "") .'>　　'.$jArr[$this->primaryName].'</option>';
                }
        }
        return $output;
    }

    /**
     * 获取知识点名称
     * @param array $arr 知识点id数组
     * @param array $cache 缓存数据
     * @return array
     * @author mabo
     */
    public function getNameOnly($arr,$cache) {
        $output=array();
        if(!$cache) $cache[0]=SS($this->primary);
        if(!$arr) return ;
        foreach($arr as $iArr){
            $output[]=$cache[0][$iArr][$this->primaryName];
        }
        return $output;
    }

    /**
     * 获取对应路径 列表
     * @param array $arr id数组
     * @param array $cache 缓存数据
     * @return array
     * @author mabo
     */
    public function getAll($arr,$cache) {
        $output=array();
        if(!$arr) return ;
        //判断知识点缓存是否存在
        if(!$cache){
            $cache[0]=SS($this->primary);
            $cache[1]=SS($this->primary.'Parent');
        }

        foreach($arr as $iArr){
            if(!$iArr) continue;
            $tmpStr=array();
            $tmpArr=$cache[1][$iArr];
            if($tmpArr){
                foreach($tmpArr as $jTmpArr){
                    $tmpStr[]=$jTmpArr[$this->primaryName];
                }
            }
            $tt=$cache[0][$iArr][$this->primaryName];
            $tmpStr[]=$tt;
            $output[]=implode(' >> ',$tmpStr);
        }
        return $output;
    }
}
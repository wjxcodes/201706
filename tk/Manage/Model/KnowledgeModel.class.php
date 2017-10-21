<?php
/**
 * @author demo
 * @date 2014年11月11日
 */
/**
 * 知识点模型类，用于处理知识点相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class KnowledgeModel extends BaseModel{
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'OrderID asc,KlID asc');
    }

    /**
     * 获取父类列表；
     * @author demo
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
                        'OrderID ASC,KlID ASC'
            );
        }else{
            return $this->selectData(
                    '*',
                    'PID=0',
                    'OrderID ASC,KlID ASC'
            );
        }
    }

    /**
     * 获取三级列表树形结构
     * @param array $children 根据知识点id为索引的下一级数据集
     * @param array $klID 知识点id
     * @return 
     */
    public function getArrList3($children,$klID){
        $output = array();
        if(!$children[$klID]) return '';
        foreach($children[$klID] as $i => $iChildren){
            $output[$i]=$iChildren;
            if($children[$iChildren['KlID']]){
                $tmpArr = $this->getArrList3($children,$iChildren['KlID']);
                if(!empty($tmpArr))  $output[$i]['sub'] = $tmpArr;
            }
        }
        return $output;
    }

    /**
     * 缓存数组；
     * @author demo
     */
    public function setCache(){
        //获取所有数据
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc,KlID asc');
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
            if(in_array($iBuffer['KlID'],$pid)){
                $iBuffer['Last']=0;
            }else{
                $iBuffer['Last']=1;
            }
            $self[$iBuffer['KlID']] = $iBuffer;
            $children[$iBuffer['PID']][] = $iBuffer;
            if($iBuffer['PID'] == 0) $subject[$iBuffer['SubjectID']][] = $iBuffer;
        }

        $pid = array_unique(array_filter($pid)); //去重，去空
        //获取以知识点id为索引的数据，除基础数据外获取是否是最后一级；获取当前数据的下一级；获取当前数据的父类路径
        $knowledgeArr = array();
        foreach($buffer as $iBuffer){
            $knowledgeArr[$iBuffer['KlID']]['KlName'] = $iBuffer['KlName'];
            $knowledgeArr[$iBuffer['KlID']]['PID'] = $iBuffer['PID'];
            $knowledgeArr[$iBuffer['KlID']]['SubjectID'] = $iBuffer['SubjectID'];
            $knowledgeArr[$iBuffer['KlID']]['IfInChoose'] = $iBuffer['IfInChoose'];
            if(in_array($iBuffer['KlID'],$pid)){
                $knowledgeArr[$iBuffer['KlID']]['Last'] = 0;
                $buff = $children[$iBuffer['KlID']];
                if($buff){
                    foreach($buff as $j => $jBuffn){
                        if(in_array($jBuffn['KlID'],$pid)) $buff[$j]['Last']=0;
                        else $buff[$j]['Last']=1;
                    }
                }
                $next[$iBuffer['KlID']] = $buff; //下一级数据集
            }
            else $knowledgeArr[$iBuffer['KlID']]['Last']=1;
            
            //所有父类数据集
            if($iBuffer['PID'] != 0){
                $parentID = $iBuffer['PID'];
                $parentPath = array();
                while(!empty($parentID)){
                    $parentPath[] = $self[$parentID];
                    $parentID = $self[$parentID]['PID'];
                }
                krsort($parentPath);
                $parent[$iBuffer['KlID']] = $parentPath;
            }

            //所有子类以逗号间隔
            if($knowledgeArr[$iBuffer['KlID']]['Last']!=1){
                $data[$iBuffer['KlID']] = $this->getChildListByCache($children,$iBuffer['KlID']);
            }
        }
        //生成学科树形结构
        foreach($subject as $i => $iSubject){
            foreach($iSubject as $j => $jSubject){
                $tmpstr=$this->getArrList3($children,$jSubject['KlID']);
                if(!empty($tmpstr)) $subject[$i][$j]['sub'] = $tmpstr;
            }
        }
        S('klList',$data);  // 缓存所有子类id以逗号间隔数据
        S('knowledge',$knowledgeArr);//所有知识点缓存 以id为索引
        S('knowledgeParent',$parent);//所有知识点对应父类路径数据集缓存
        S('knowledgeNext',$next);//所有知识点对应下一级数据集缓存
        S('klBySubject3',$subject);//根据学科区分知识点
    }

    /**
     * 获取缓存；
     * @author demo
     */
    public function getCache($str = 'knowledge',$num = 0){
        switch ($str) {
            case 'knowledge':
                $buffer = S('knowledge');
                break;
            case 'klBySubject3':
                $buffer = S('klBySubject3');
                break; 
            case 'klList':
                $buffer = S('klList');
                break;
            case 'knowledgeParent':
                $buffer = S('knowledgeParent');
                break;
            case 'knowledgeNext':
                $buffer = S('knowledgeNext');
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
     * @author demo
     */
    public function getChildListByCache($buffer,$id){
        $thisArr = $buffer[$id];
        if($thisArr){
            $output = array();
            foreach($thisArr as $iThisArr){
                $output[] = $iThisArr['KlID'];
                if($buffer[$iThisArr['KlID']]){
                    $str = $this->getChildListByCache($buffer,$iThisArr['KlID']);
                    if($str != '') $output[]=$str;
                }
            }
            return implode(',',$output);
        }
        return '';
    }

    /**
     * 获取选中的知识点 gk；
     * @author demo
     */
    public function getKnowledgeed($paramArr){
            $array = $paramArr['buffer'];
            $s = $paramArr['SubjectID'];
            $ID = $paramArr['ID'];
            $output = array();
            if(!$array[$ID]['Parent']){
                $output[0] = $this->setoption($this->getParList($s),$ID);
            }else{
                foreach($array[$ID]['Parent'] as $i => $bn){
                    if(count($array[$ID]['Parent'])-1 == $i){
                        $output[0] = $this->setoption($this->getParList($s),$array[$ID]['Parent'][count($array[$ID]['Parent'])-1]['KlID']);
                        if($array[$bn['KlID']]['Sub']){
                            $select = '';
                            foreach($array[$bn['KlID']]['Sub'] as $a){
                                $ifselect = '';
                                $tmpKid = $array[$ID]['Parent'][$i-1]['KlID'];
                                if(!$tmpKid) $tmpKid=$ID;
                                if($a['KlID'] == $tmpKid) $ifselect = ' selected = "selected" ';
                                $select .= '<option value = "'.$a['KlID'].'"'.$ifselect.'>'.$a['KlName'].'</option>';
                            }
                            $output[1] .= '<select class = "knowledge" name="knowledge[]">'.$select.'</select>';
                        }
                    }else{
                        if($array[$bn['KlID']]['Sub']){
                            $select = '';
                            foreach($array[$bn['KlID']]['Sub'] as $a){
                                $ifselect = '';
                                if($a['KlID'] == $ID) $ifselect = ' selected = "selected" ';
                                $select .= '<option value = "'.$a['KlID'].'"'.$ifselect.'>'.$a['KlName'].'</option>';
                            }
                            $output[1] .= '<select class = "knowledge" name = "knowledge[]">'.$select.'</select>';
                        }
                    }
                }
            }
            return $output;
    }

    /**
     * 根据id 获取知识点路径
     * @param array $paramArr 数组类型
     * @param index parent 知识点父类缓存
     * @param index self 知识点当前缓存
     * @param index ID 需要显示的知识点id 支持数组和逗号间隔字符串
     * @param index ReturnString 返回字符串的间隔方式 为空则返回数据
     * @return string|Array
     * @author demo
     */
    public function getKnowledgePath($paramArr){
        $parent=$paramArr['parent'];
        $self=$paramArr['self'];
        $output=array();

        $ID=$paramArr['ID'];
        if(is_array($ID)) $idList=$ID;
        else $idList=explode(',',$ID);

        foreach($idList as $i=>$iIdList){
            $output[$i]['KlID']=$iIdList;
            if($parent[$iIdList]){
                foreach($parent[$iIdList] as $j=>$jParent){
                    $output[$i]['KlName'].='>>'.$jParent['KlName'];
                }
            }
            $output[$i]['KlName'].='>>'.$self[$iIdList]['KlName'];
        }

        if(!empty($paramArr['ReturnString'])){
            $tmp='';
            foreach($output as $i=>$iOutput){
                $tmpStr='('.($i+1).')';
                $tmp.=$tmpStr.$iOutput['KlName'].$paramArr['ReturnString'];
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
                'OrderID ASC,KlID ASC');
        else
            $list = $this->selectData(
                '*',
                'PID=0',
                'OrderID ASC,KlID ASC');
        foreach ($list as $i => $iList) {
            $node[$i] = $iList;
            $listn = $this->selectData(
                '*',
                'PID='. $iList['KlID'],'OrderID ASC,KlID ASC');
            $node[$i]['sub']=$listn;
        }
        return $node;
    }

    /**
     * 数组变option
     * @param $arr 需要处理的数组数据
     * @param $id=0 默认为0 选中的option项
     * @return string
     * @author demo
     */
    public function setOption($arr,$id=0){
        $output='';
        foreach($arr as $iArr){
            $output.='<option value="'.$iArr['KlID'].'" '.($iArr['KlID']==$id ? "selected=\"selected\"" : "") .'>'.$iArr['KlName'].'</option>';
            if($iArr['sub'])
                foreach($iArr['sub'] as $jArr){
                    $output.='<option value="'.$jArr['KlID'].'" '.($jArr['KlID']==$id ? "selected=\"selected\"" : "") .'>　　'.$jArr['KlName'].'</option>';
                }
        }
        return $output;
    }

    /**
     * 返回同级知识点最大Order的KlID
     * @param string $ids 多个KlID，该值必须为最终的节点值
     * @return string
     * @author demo 2015-11-19
     */
    public function getMaxValueBySibling($ids){
        $knowledge = $this->selectData(
            'KlID, PID, OrderID',
            'KlID IN ('.$ids.')'
        );
        $parents = array();
        foreach($knowledge as $value){
            $parents[$value['PID']][] = $value['KlID'];
        }
        $knowledge = array();
        foreach($parents as $key=>$value){
            $knowledge[] = max($value);
        }
        return implode(',', $knowledge);
    }

    /**
     * 获取知识点名称
     * @param array $arr 知识点id数组
     * @param array $knowledge 知识点缓存数据
     * @return array
     * @author demo
     */
    public function getKlOnly($arr,$knowledge) {
        $output=array();
        if(!$knowledge) $knowledge[0]=SS('knowledge');
        if(!$arr) return ;
        foreach($arr as $iArr){
            $output[]=$knowledge[0][$iArr]['KlName'];
        }
        return $output;
    }

    /**
     * 获取知识点对应路径 列表
     * @param array $arr 知识点id数组
     * @param array $knowledge 知识点缓存数据
     * @return array
     * @author demo
     */
    public function getKlAll($arr,$knowledge) {
        $output=array();
        if(!$arr) return ;
        //判断知识点缓存是否存在
        if(!$knowledge){
            $knowledge[0]=SS('knowledge');
            $knowledge[1]=SS('knowledgeParent');
        }

        foreach($arr as $iArr){
            if(!$iArr) continue;
            $tmpStr=array();
            $tmpArr=$knowledge[1][$iArr];
            if($tmpArr){
                foreach($tmpArr as $jTmpArr){
                    $tmpStr[]=$jTmpArr['KlName'];
                }
            }
            $tt=$knowledge[0][$iArr]['KlName'];
            $tmpStr[]=$tt;
            $output[]=implode(' >> ',$tmpStr);
        }
        return $output;
    }
}
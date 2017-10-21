<?php
/**
 * @author demo
 * @date 2015-07-10
 */
/**
 * 导学案个人和系统知识接口类，用于导学案个人和系统知识处理的操作
 */
namespace Guide\Model;
use Common\Model\BaseModel;
class CaseLoreQueryModel extends BaseModel{
    public $sysPre = 'l'; //查询区分字符系统
    public $myPre = 'u'; //查询区分字符个人

    /**
     * 获取知识数据
     * @param string $field 知识字段
     * @param array $where 知识字段
     * @param array $order 知识字段
     * @param array $page 知识字段
     * @param int $ifSystem 查询知识类型 默认0查询系统和个人 1查询系统 2查询个人
     * @param int $convert 是否转换结构数组 包括加上知识点标识符  0不做转换,1 转换为知识点id为索引的数组 2转换并合并
     * @return array
     * @author demo
     */
    public function getLore($field,$where,$order='',$page='',$ifSystem=0,$convert=0){
        if(isset($where['LoreID'])){
            $id=$where['LoreID'];
            if(is_string($id)) $id=explode(',',$id);
            $idArr=R('Common/TestLayer/cutIDStrByChar',array($id)); //分割id号
        }

        $list=array(); //结果集

        //查询系统知识
        if($ifSystem!=2){
            $newWhere=$where;
            if(isset($newWhere['LoreID'])){
                $newWhere['LoreID']=$idArr[$this->sysPre];
                if(empty($newWhere['LoreID'])) unset($newWhere['LoreID']);
            }
            if(!empty($newWhere)){
                $caseLore = $this->getModel('CaseLore');
                $list[$this->sysPre] = $caseLore->getLore($field,$newWhere,$order,$page);
            }
        }

        //查询自建知识
        if($ifSystem!=1){
            if(isset($where['LoreID'])){
                $where['LoreID'] = $idArr[$this->myPre];
                if(empty($where['LoreID'])) unset($where['LoreID']);
            }
            if(!empty($where)){
                $caseCustomLore = $this->getModel('CaseCustomLore');
                $list[$this->myPre] = $caseCustomLore->getLore($field,$where,$order,$page);
            }
        }
        //转换数组,格式化数据
        if($convert!=0){
            $list=$this->changeArray($list); //转换testid为键

            if($convert==2){
                $list=$this->mergeArray($list); //合并
            }
        }
        return $list;
    }
    /**
     * 转换查询结果以试题id为索引
     * @param array $array 数据集
     * @return array
     * @author demo
     */
    private function changeArray($array){
        $newList = array();
        if($array[$this->sysPre]){
            $newList[$this->sysPre] = R('Common/TestLayer/reloadTestArr',array($array[$this->sysPre][0],'LoreID',$this->sysPre,0));
        }

        if($array[$this->myPre]){
            $newList[$this->myPre] =  R('Common/TestLayer/reloadTestArr',array($array[$this->myPre][0],'LoreID',$this->myPre,0));
        }

        return $newList;
    }
    /**
     * 合并查询结果
     * @param array $array 数据集
     * @return array
     * @author demo
     */
    private function mergeArray($array){
        $newList = array();
        $merge=0; //0使用array_merge 1使用+    合并
        if($array[$this->sysPre]){
            foreach($array[$this->sysPre] as $i=>$iArray){
                if($i!=0) $merge=1;
                break;
            }
        }
        if($merge==1){
            $newList=(array)$array[$this->sysPre]+(array)$array[$this->myPre];
        }else{
            $newList=array_merge((array)$array[$this->sysPre],(array)$array[$this->myPre]);
        }

        return $newList;
    }
}
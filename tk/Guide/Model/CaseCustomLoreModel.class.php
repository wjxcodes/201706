<?php
/**
 * 我的知识model
 * @author demo
 * @date 2015-5-28
 */
namespace Guide\Model;
use Common\Model\BaseModel;
class CaseCustomLoreModel extends BaseModel{

    protected $id = 0;

    /**
     * 保存知识，当$id参数不为空，将进行修改操作
     * @param array $data 数据保存
     * @param int $id 知识id
     * @return boolean 成功返回true
     * @author demo
     */
    public function saveLore($data, $id=0){
        $operate = (((int)$id) === 0); //为true时，为新增操作
        $lore = array(
            'Lore' => $data['Lore'],
            'Answer' => $data['Answer']
        );
        $attributes = array(
            'ChapterID' => $data['ChapterID'],
            'MenuID' => $data['MenuID'],
        );
        if($data['SubjectID']){
            $attributes['SubjectID'] = $data['SubjectID'];
        }
        if($data['UserName']){
            $attributes['UserName'] = $data['UserName'];
        }
        $time = time();
        if($operate){
            //新增时相关知识信息
            $lore['Remark'] = '';  
            $lore['Equation'] = '';
            $this->id = $this->insertData(
                $lore);
            if(!$this->id){
                return false;
            }
            //新增时的相关属性信息
            $attributes['AddTime'] = $time;
            $attributes['IfIntro'] = 1;
            $attributes['LoreID'] = $this->id;
            $result = $this->getModel('CaseCustomLoreAttr')->insertData(
                $attributes
            );
            if($result === false){
                return false;
            }
            return true;
        }
        $this->id = $id;
        //修改操作
        $attributes['LastUpdateTime'] = $time;
        $result = $this->updateData(
            $lore,
            array('LoreID'=>$this->id)
        );
        if($result === false){
            return false;
        }
        $this->getModel('CaseCustomLoreAttr')->updateData(
            $attributes,
            array('LoreID'=>$this->id)
        );
        return true;
    }

    /**
     * 删除我的知识
     * @param int $id 知识id
     * @return boolean 成功返回true
     * @author demo
     */
    public function delLore($id){
        //删除知识属性
        $result =$this->getModel('CaseCustomLoreAttr')->deleteData(
            'LoreID in ('.$id.')'
        );
        if($result === false){
            return $result;
        }
        $result = $this->deleteData(
            'LoreID in ('.$id.')'
        );
        if($result === false){
            return $result;
        }
        return true;
    }

    /**
     * 组卷前台更具相关参数查询试题
     * @param array $params 参数
     * @param int $pageRecord 每页显示记录数
     * @return array
     * @author demo
     */
    public function getListByParams($params=array(), $pageRecord=10){
        $sql = "SELECT %s FROM zj_case_custom_lore `lore` LEFT JOIN zj_case_custom_lore_attr `attr` ON `attr`.LoreID=`lore`.LoreID LEFT JOIN zj_case_menu `menu` ON `menu`.MenuID=`attr`.MenuID WHERE `attr`.SubjectID={$params['subjectid']}";
        if($params['UserName']){
            $sql .= " AND `attr`.UserName='{$params['UserName']}'";
        }
        if($params['chapterid']){
            $chapter = SS('chapterIDList');
            $chapter = explode(',', $chapter);
            $chapter[] = $params['chapterid'];
            $chapter = implode(',', $chapter);
            $sql .= " AND ChapterID IN ({$chapter})";
        }
        if($params['menuid']){
            $sql .= " AND `attr`.MenuID={$params['menuid']}";
        }
        //直接返回数据，不在进行分页查询
        if($params['id']){
            $sql .= ' AND `lore`.LoreID='.$params['id'];
            $result = M('Base')->query(sprintf($sql, '`lore`.LoreID,Lore,Answer,`attr`.SubjectID,ChapterID,`attr`.MenuID,AddTime,ForumID').' ORDER BY AddTime DESC');
            return array('result'=>$result, 'page'=>array(1, $pageRecord));
        }
        $fields = 'COUNT(`lore`.LoreID) as total';
        $result = M('Base')->query(sprintf($sql, $fields));
        $total = $result[0]['total']; //获取总页数
        $page = page($total, $params['page'], $pageRecord);
        $fields = '`lore`.LoreID,Lore,Answer,`attr`.SubjectID,ChapterID,`attr`.MenuID,AddTime,ForumID';
        $result = M('Base')->query(sprintf($sql, $fields).' ORDER BY AddTime DESC LIMIT '.(($page-1)*$pageRecord).','.$pageRecord);
        return array(
            'result' => $result,
            'page' => array($total, $pageRecord)
        ); 
    }

    /**
     * 修改保存时返回试题的id
     * @return int
     */
    public function getId(){
        return $this->id;
    }
    /**
     * 前台查询知识
     * @param array $field 返回字段
     * @param array $where 条件
     *  array('DocID'=>文档id（支持逗号间隔）,
     *
     *  );
     * @param array $order 排序 array('字段1 DESC','字段2 ASC',...)
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array
     * @author demo
     */
    public function getLore($field,$where,$order='',$page=''){
        if(is_string($field)) $field=explode(',',$field);

        //分析字段  对不同表相同字段进行归类
        $sameField=array('LoreID','SubjectID','UserName','MenuID','ChapterID','AddTime');

        foreach($field as $i=>$iField){
            if(in_array($iField,$sameField)){
                $field[$i]='CLA.'.$iField;
            }
        }

        foreach($where as $i=>$iWhere){
            if(is_string($iWhere) && strpos($iWhere,',')!==false){
                $iWhere=array('IN',explode(',',$iWhere));
            }else if(is_array($iWhere)){
                $iWhere=array('IN',$iWhere);
            }

            if(in_array($i,$sameField)){
                unset($where[$i]);
                $where['CLA.'.$i]=$iWhere;
            }else{
                $where[$i]=$iWhere;
            }
        }
        $where['IfIntro']=1; //默认是入库数据

        foreach($order as $i=>$iOrder){
            $tmpOrder=explode(' ',trim($iOrder));
            if(in_array($tmpOrder[0],$sameField)){
                $order[$i]='CLA.'.$iOrder;
            }
        }
        $order=implode(',',$order);

        //默认分页
        if($page['page']<1 || !is_numeric($page['page'])) $page['page']=1;
        //默认每页数量
        if($page['perpage']<1 || !is_numeric($page['perpage'])) $page['perpage']=C('WLN_PERPAGE');
        if(empty($page['limit'])) $page['limit']=100000;

        $off = ($page['page'] -1) * $page['perpage'];//查询起始位置
        $page=$off.','.$page['perpage'];


        $output=$this->unionSelect('caseCustomLoreJoinSelect',$field,$where,$order,$page);
        $count=$this->getModel('CaseCustomLoreAttr')->selectCount($where,'CLA.LoreID','CLA');

        //对知识进行处理
        $array=R('Common/TestLayer/changeUrlByField',array($output,array('Lore','Answer')));

        //对取出的知识做多余标记处理
        $array=R('Common/TestLayer/changeMoreTagByField',array($array,array('Lore','Answer')));

        $output[0]=$array;
        $output[1]=$count;
        $output[2]=$page['perpage'];

        return $output;
    }
}
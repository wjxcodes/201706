<?php
/**
 * @author demo
 * @date 15-5-4 上午11:28
 */
/**
 * 导学案知识类，用于导学案知识的操作
 */
namespace Guide\Model;
use Doc\Model\HandleWordModel;
class CaseLoreModel extends HandleWordModel{

    /**
     * 通过知识编号获取知识数据
     * @param int $loreID 知识编号
     * @return array
     * @author demo
     */
    public function getDataByLoreID($loreID){
        if(is_array($loreID)){
            $loreID=join(',',$loreID);
            };
        $loreData = $this->selectData(
            '*',
            'LoreID in ('.$loreID.')',
            '',
            ''
        );
        $loreArrayByID=array();
        foreach($loreData as $i=>$iLoreData){
            $loreArrayByID[$iLoreData['LoreID']]=$iLoreData;
        }
        return $loreArrayByID;
    }

    /**
     * 单个知识过滤数据
     * @param $filterArray array 需过滤的数组
     * @param float $fontSize 试题字体大小
     * @return array
     * @author demo
     */
    public function getSingleDoc($filterArray,$fontSize){
        $testModel = $this->getModel('Test');
        $filterArray['Lore']=$testModel->filterP($filterArray['Lore']);
        $filterArray['Answer']=$testModel->filterP($filterArray['Answer']);
        $filterArray['Remark']=$testModel->filterP($filterArray['Remark']);

        $str = '<p>编号：'.$filterArray['LoreID'].'-'.$filterArray['NumbID'].'</p><p><font color="blue">【题文】</font>'.$filterArray['Lore'];
        $str .= '<p><font color="blue">【答案】</font>' . $filterArray['Answer'];
        $str .= '<p><font color="blue">【结束】</font></p>';

        $str=mb_convert_encoding($str,'gbk','utf-8');
        $str=$this->html2word($str,$fontSize);
        return $str;
    }

    /**
     * 组卷前台更具相关参数查询试题
     * @param array $params 参数
     * @param int $pageRecord 每页显示记录数
     * @return array
     * @author demo
     */
    public function getListByParams($params=array(), $pageRecord=10){
        $sql = "SELECT %s FROM {$params['db']} `lore` LEFT JOIN {$params['db']}_attr `attr` ON `attr`.LoreID=`lore`.LoreID{$params['join']}WHERE `attr`.SubjectID={$params['subjectid']}";
        if($params['custom']==1){
            $sql .= " AND `attr`.UserName ='{$params['userName']}'";
        }else{//只搜索已经入库的知识点
            $sql .= " AND `attr`.IfIntro =1";
        }
        if($params['chapterid']){
            $chapter = SS('chapterIDList');
            $chapter = explode(',', $chapter);
            $chapter[] = $params['chapterid'];
            $chapter = implode(',', $chapter);
            $sql .= " AND attr.ChapterID IN ({$chapter})";
        }
        if($params['menuid']){
            $sql .= " AND attr.MenuID={$params['menuid']}";
        }
        if($params['id']){
            $sql .= ' AND `lore`.LoreID='.$params['id'];
        }
        $fields = 'COUNT(`lore`.LoreID) as total';
        $result = M('Base')->query(sprintf($sql, $fields));
        $total = $result[0]['total']; //获取总页数
        $page = page($total, $params['page'], $pageRecord);
        $fields = $params['fields'];
        $result = M('Base')->query(sprintf($sql, $fields).' ORDER BY attr.AddTime DESC LIMIT '.(($page-1)*$pageRecord).','.$pageRecord);
        return array(
            'result' => $result,
            'page' => array($total, $pageRecord)
        ); 
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
        $sameField=array('LoreID','NumbID','DocID','SubjectID','Admin','MenuID','ChapterID','AddTime');

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


        $output=$this->unionSelect('caseLoreJoinSelect',$field,$where,$order,$page);
        $count=$this->getModel('CaseLoreAttr')->selectCount($where,'CLA.LoreID','CLA');

        //对知识进行处理
        $array=R('Common/TestLayer/changeUrlByField',array($output,array('Lore','Answer')));

        //对取出的知识做多余标记处理
        $array=R('Common/TestLayer/changeMoreTagByField',array($array,array('Lore','Answer'),1));
        
        $output[0]=$array;
        $output[1]=$count;
        $output[2]=$page['perpage'];

        return $output;
    }
}
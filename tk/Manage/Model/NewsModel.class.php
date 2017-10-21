<?php
/**
 * @author demo 
 * @date 2014年8月13日
 * @update 2015年1月21日
 */
/**
 * news新闻表
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class NewsModel extends BaseModel{

    /**
     * 按条件获取新闻内容（标签分页）
     * @param string $where 查询条件
     * @param string $limit    起始位置
     * @param string $pageSize 偏移量
     * @return array 新闻信息
     * @author demo
     */
    public function newsPage($where,$limit,$pageSize){
        $page=$limit.','.$pageSize;
        $newsDb = $this->pageData(
            '*',
            $where,
            'LoadDate DESC',
            $page);
        return stripslashes_deep($newsDb);
    }
    
    /**
     * 点击次数加一
     * @param int $id 新闻ID
     * @author demo
     */
    public function hitsAddOne($id){
        $this->conAddData(
            'Hits=Hits+1',
            'NewID='.$id
        );
    }
    
    /**
     * 新闻查询
     * @param string $field 查询字段 默认全部
     * @param string $where 查询条件
     * @param string $order 排序
     * @param string $limit 查询的数量
     * @return array 新闻信息
     * @author demo
     */
    public function newsDate($field='*',$where,$order='',$limit=''){
        $rs =  $this->selectData(
                    '*',
                    $where,
                    $order,
                    $limit
                );
        return stripslashes_deep($rs);
    }
    /**
     * 新闻分页查询
     * @param string $field 查询字段 默认全部
     * @param string $where 查询条件
     * @param string $order 排序
     * @param string $page 分页
     * @return array 新闻信息
     * @author demo
     */
    public function newsDatePage($field='*',$where,$order='',$page){
        $rs = $this->pageData(
                    '*',
                    $where,
                    $order,
                    $page
                );
        return stripslashes_deep($rs);
    }
    
    /**
     * 按添加查询新闻总数
     * @param string $where 查询条件
     * @param string $field 聚合字段
     * @return array 此条件下新闻总数
     * @author demo
     */
    public function newsCountField($where,$field='NewID'){
        return $this->selectCount($where,'NewID');
    }

}
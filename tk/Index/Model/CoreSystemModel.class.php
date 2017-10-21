<?php
/**
 * 核心系统模块
 * @author demo 2015-12-7 
 */
namespace Index\Model;
class CoreSystemModel extends BaseModel{
    
    /**
     * 删除
     * @param $ids
     * @author demo 
     */
    public function delete($where){
        return $this->deleteData($where);
    }
    
    /**
     * 获得信息
     * @param string $field
     * @return array | bool
     * @author demo
     */
    public function getInfo($where='',$field = '*',$order='OrderID DESC',$limit=''){
        return $this->selectData($field, $where,$order,$limit);
    }
    
    /**
     * 图片对应路径
     * @return array
     * @author demo 
     */
    public function returnImgSrc(){
        return array(
            1=>'智能组卷icon',
            2=>'智能作业icon',
            3=>'高效同步课堂icon',
            4=>'智能提分icon',
            5=>'智能提分APPicon',
            6=>'协同原创平台icon',
            7=>'考试预订系统icon',
            8=>'校本题库icon'
        );
    }
    
    /**
     * 保存
     * @param array $data
     * @param int $CSID 传递则为修改操作
     * @return bool
     * @author demo
     */
    public function save($data,$CSID=''){
        if(empty($CSID)){
            $data['AddTime'] = time();//添加时间
            $result = $this->insertData($data);
        }else{
            $data['LastTime'] = time();//修改时间
            $result = $this->updateData($data, 'CSID='.$CSID);
        }
        return $result;
    }
    
}
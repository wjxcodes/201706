<?php
/**
 * 核心系统模块 附属常见问答
 * @author demo 2015-12-7 
 */
namespace Index\Model;
class CoreSystemFaqModel extends BaseModel{
    
    /**
     * 删除
     * @param string $where
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
    public function getInfo($where,$field = '*',$order='',$limit=''){
        return $this->selectData($field, $where,$order,$limit);
    }
    
    /**
     * 保存
     * @param array $data
     * @param int $FAQID 传递则为修改操作
     * @return bool
     * @author demo
     */
    public function save($data,$FAQID=''){
        if(empty($FAQID)){
            $data['AddTime'] = time();//添加时间
            $result = $this->insertData($data);
        }else{
            $data['LastTime'] = time();//修改时间
            $result = $this->updateData($data, 'FAQID='.$FAQID);
        }
        return $result;
    }
    
    /**
     * 批量保存 删除失败时重新添加
     * @author demo
     */
    public function allSave($data){
        $this->addAllData($data);
    }
    
}
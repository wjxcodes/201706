<?php
/**
 * @author demo
 * @date 2015年9月30日
 */
/**
 * 教师信息，用于处理认证
 */
namespace User\Model;
use Common\Model\BaseModel;
class TeacherAuthinfoModel extends BaseModel{
    /**
     * 构造函数（主要设置了此模型modelName）
     * @author demo
     */
    function __construct() {
        parent::__construct();
        $this->modelName = 'TeacherAuthinfo';
    }
    
    /**
     * 删除认证信息
     * @param string $where
     * @return int | bool 
     * @author demo
     */
    public function delete($where){
        $this->deleteAuthPic($where);//先删除图片
        return $this->deleteData($where,$this->modelName);
    }
    
    /**
     * 删除认证图片
     * @param string $where
     * @author demo
     */
    private function deleteAuthPic($where,$delPicArray=array()){
        $result = $this->getModel('TeacherAuthinfo')->getList('QuaPicSrc,GradePicSrc',$where);
        if(empty($delPicArray)){
            unlink(realpath('./') . $result[0]['QuaPicSrc']);
            unlink(realpath('./') . $result[0]['GradePicSrc']);
        }else{
            if($delPicArray['QuaPicSrc']){
                unlink(realpath('./') . $result[0]['QuaPicSrc']);
            }
            if($delPicArray['GradePicSrc']){
                unlink(realpath('./') . $result[0]['GradePicSrc']);
            }
        }
    }
    
    /**
     * 教师认证
     * @param array $data 认证信息
     * @param int $id 修改id
     * @author demo
     */
    public function save($data,$id=''){
        if(empty($id)){
            return $this->insertData($data,$this->modelName);
        }else{
            $delPicArray['GradePicSrc'] = $data['GradePicSrc'] ? true : false;
            $delPicArray['QuaPicSrc'] = $data['QuaPicSrc'] ? true : false;
            $this->deleteAuthPic('ID =' .$id,$delPicArray);//删除修改的图片
            return $this->updateData($data,'ID='.$id,$this->modelName);
        }
    }
    
    /**
     * 查询认证记录
     * @param string $field 字段
     * @param string $where 条件
     * @param string $order 查询
     * @return array 查询后的数据集 或 false
     * @author demo
     */
    public function getList($field='*',$where='',$order='',$limit=''){
        return $this->selectData($field, $where ,$order, $limit);
    }
    
    
}

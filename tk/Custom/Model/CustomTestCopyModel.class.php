<?php
//程序生成的文件  2015-12-18
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestCopyModel extends BaseModel{

    public function customTestCopySelectCount($where){
        return $this->unionSelect('customTestCopySelectCount',$where);
    }

    public function customTestCopySelectByPage($where,$page){
        return $this->unionSelect('customTestCopySelectByPage',$where,$page);
    }

    public function customTestCopySelectById($testID){
        return $this->unionSelect('customTestCopySelectById',$testID);
    }
}
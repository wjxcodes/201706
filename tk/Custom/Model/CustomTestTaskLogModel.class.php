<?php
//程序生成的文件  2015-12-18
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestTaskLogModel extends BaseModel{
    
    public function customTestTaskLogSelectCount($where){
        return $this->unionSelect('customTestTaskLogSelectCount',$where);
    }

    public function customTestTaskLogSelectByPage($where,$page){
        return $this->unionSelect('customTestTaskLogSelectByPage', $where,$page);
    }
}
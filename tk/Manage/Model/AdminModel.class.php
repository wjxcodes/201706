<?php
/**
 * @author demo
 * @date 2014年8月4日
 */
/**
 * 管理员模型类，用于处理管理员相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class AdminModel extends BaseModel{
    /**
     * 生成安全码
     * @param int $length 安全码长度
     * @return String
     * @author demo
     */
    public function saveCode($length=15){
        return formatString('saveCode',$length);
    }
}
<?php
/**
 * 通用方法数据接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class SystemApi extends CommonApi{
    /**
     * 所用用户操作日记录入操作
     * @param array $data 需要录入的参数
     * @return bool
     * @author demo
     */
    public function addLog($data){
        $data['LoadDate']=time();
        $data['IP']=get_client_ip(0,true);

        $this->getModel('Log')->insertLog($data);
    }
}
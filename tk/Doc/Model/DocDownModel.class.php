<?php
/**
 * @author demo  
 * @date 2014年10月28日  
 * @updatetime 2015.1.16
 */
 /** 
  * 试卷、答题、卡作业下载Model类，下载相关操作
  */
namespace Doc\Model;
class DocDownModel extends BaseModel{
    /**
     * @覆盖父类方法。
     * @author demo 2015-12-18
     */
    public function insertData($data, $modelName=''){
        if(!empty($data['CookieStr'])){
            $data['CookieHash'] = md5($data['CookieStr']);
        }
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $sc = $this->getModel('StatisticsCounter');
            $sc->increase('shijuanNum');
            if(3 == $data['DownStyle']){
                $sc->increase('caseDownNum');
            }
        }
        return $result;
    }

    /**
     * 查询数据总数；
     * @param $where string 统计条件
     * @param $count string 统计字段
     * @return int
     * @author demo
     */
    public function getTotalRow($where='',$count=''){
        if($count==''){
            $count='DownID';
        }
        return $this->selectCount(
            $where,
            $count);
    }

    /**
     * 验证指定的cookie是否存在
     * @param string $cookie
     * @return array 存在则返回一行数据
     * @author demo 16-7-8
     */
    public function isExistTheCookie($cookie){
        $cookie = md5($cookie);
        //使用selectData主要是兼容之前可能下载失败而产生错误DataPath问题
        $data = (array)$this->selectData('DownID,UserName,DocPath', "CookieHash='{$cookie}'", 'DownID DESC', '50');
        foreach($data as $value){
            if(preg_match('/^\/(?:[A-Za-z0-9]+\/)+.*\.\w{3,}$/m', $value['DocPath'])){
                return $value;
            }
        }
        return '';
    }
}

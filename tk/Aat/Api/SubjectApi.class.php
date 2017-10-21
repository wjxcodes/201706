<?php
/**
 * @author demo
 * @date 2015年12月26日
 */

namespace Aat\Api;
class SubjectApi extends BaseApi
{
    private $subjectCache = null;

    public function getCache(){
        if(!$this->subjectCache){
//            $this->subjectCache = SS('subject');
            $this->subjectCache = $this->getApiCommon('Subject/subject');
        }
        return $this->subjectCache;
    }

    /**
     * 描述：根据ID获取学科名称
     * @param $id
     * @return mixed
     * @author demo
     */
    public function getNameByID($id){
        $cache = $this->getCache();
        if ($cache[$id]) {
            return $cache[$id]['SubjectName'];
        } else {
            return $id;
        }
    }

    /**
     * 描述：判断学科ID是否合法
     * @param $id
     * @return bool
     * @author demo
     */
    public function checkValidID($id){
        if (!$id) {
            return false;
        }
        $cache = $this->getCache();
        if (!array_key_exists($id, $cache) || $cache[$id]['PID'] == 0) {
            return false;
        }
        return true;
    }

}
<?php
/**
 * 校本题库文档上传model
 * @author demo 2015-9-29
 */
namespace Custom\Model;
use \Doc\Model\HandleWordModel;
class CustomDocUploadModel extends HandleWordModel{
    /**
     * 返回数据信息
     * @param array $params 参数列表，当params中包含page值时将运用分页
     * @return array
     * @author demo 
     */
    public function getList($params){
        //此处运用多个条件
        return $this->query($params);
    }


    /**
     * 试题提取
     * @param string $ids 文档id
     * @return boolean 成功返回true
     * @author demo 
     */
    public function extractTest($id, $tags){
        $result = $this->findData('DocPath,Title', 'DUID='.$id);
        $data = $this->getContent($result['DocPath'], $result['Title'], $tags);
        return $data;
        // $customTestDoc = D("CustomTestDoc");
        // foreach($ids as $value){
        //     // $testArr = array(1,2,3,4,5,6);
        //     // $result = $customTestDoc->selectCount('DocID='.$doc,'CTDID');
        //     // if(0 == $result){
        //     //     $customTestDoc->add($doc, $testArr);
        //     // }else{
        //     //     $customTestDoc->update($doc, $testArr,1);
        //     // }
        // }
    }

    private function getContent($path, $name, $tags){
        $urlHtml=C('WLN_DOC_HOST_IN').R('Common/UploadLayer/getDocServerUrl',array($path,'getWordFile','word',$name));
        $html = file_get_contents($urlHtml);
        $html = mb_convert_encoding($html,'utf-8','gbk');
        $path = strstr($path, '.', true);
        $path = strstr($path, strrchr($path, '/'), true).'/';
        $path = C('WLN_DOC_HOST').$path;
        $html = R('Common/TestLayer/strFormat',array($html,$path));
        return $this->changeArrFormat($this->division($html, $tags,2)); //html过滤
    }

    /**
     * 校本题库文档上传保存数据
     * @param array $data
     * @param int $id 主键id 当id不为空时为修改数据
     * @return int|boolean
     * @author demo 
     */
    public function save($data, $id=0){
        $id = (int)$id;
        $currentTime = time();
        $result = true;
        //新增数据
        $data['ModifiedTime'] = $currentTime;
        if(0 == $id){
            $data['SubjectID'] = (int)cookie('SubjectId');
            $data['AddTime'] = $currentTime;
            $result = $this->insertData($data);
        }else{
            //检查当前文档状态
            $status = $this->getStatus($id);
            if(2 == $status || 1 == $status){
                return false;
            }
            $result = $this->updateData($data, 'DUID='.$id);
        }
        if($result === false){
            return false;
        }
        return $result;
    }


    /**
     * 校本题库文档删除
     * @param int $id 主键id
     * @return boolean
     * @author demo 
     */
    public function delete($id){
        $status = $this->getStatus($id);
        if(2 == $status || 1 == $status){
            return false;
        }
        $data = $this->findData('Path', 'DUID='.$id);
        $this->appendDelFile($data['Path']);
        //删除主键$id的数据
        $result = $this->deleteData("DUID=".$id);
        if($result === false){
            return false;
        }
        return true;
    }


    /**
     * 校本题库设置状态
     * @param int $id 主键id
     * @param int $status 状态值
     * @return boolean
     * @author demo 
     */
    public function setStatus($id, $status){
        $status = (int)$status;
        $result = $this->updateData(
            array(
                'Status'=>$status,
                'ModifiedTime' => time()
            ),
            'DUID='.(int)$id
        );
        if($result === false){
            return false;
        }
        return true;
    }

    /**
     * 返回状态
     * @param int $id
     * @return int
     * @author demo 
     */
    public function getStatus($id){
        $result = $this->findData('Status', 'DUID='.(int)$id);
        return (int)$result['Status'];
    }

    /**
     * 判断该文档是否符合编辑或者删除的条件
     * @param int $id
     * @return boolean 不可以返回true
     * @author demo 
     */
    public function isCanOperate($id){
        $result = $this->getStatus($id);
        return (1 == $result || 2 == $result);
    }

    /**
     * 通过审核
     * @param int $id
     * @return boolean
     * @author demo 
     */
    public function adopt($id){
        return $this->setStatus($id, 2);
    }

    /**
     * 不通过审核
     * @param int $id
     * @return boolean
     * @author demo 
     */
    public function notAdopt($id){
        return $this->setStatus($id, 3);
    }

    /**
     * 将选删除的文件放入delfile表中
     * @param string $path 文件路径
     * @return void
     * @author demo 
     */
    public function appendDelFile($path){
        //写入del_file，删除远程文件
        $delFile = array();
        $delFile['FilePath'] = $path;
        $delFile['DelTimes'] = 0;
        $delFile['AddTime'] = time();
        $delFile['Style'] = 'customDoc';
        $this->getModel('DelFile')->insertData($delFile);
    }

    /**
     * apidb查询书
     * @author demo 
     */
    public function getCustomDocUploadList($where,$page){
        return $this->unionSelect('getCustomDocUploadList', $where, $page);
    }

    /**
     * 返回结果集
     * @param array $params 可能指：username 关联user表，status,title,userid,subjectid(支持in()查询)
     * @author demo 
     */
    protected function query($params){
        $isPagtion = array_key_exists('p', $params);
        $sql = "SELECT %s FROM `zj_custom_doc_upload` cdu";
        //指定该条件时，将关联user表进行查询
        $joinUserTbl = array(
            'username' => "u.UserName='%s'",
        );
        //指定的条件
        $where = array(
            'status' => 'cdu.Status=%s',
            'title' => 'cdu.Title LIKE "%%%%%s%%%%"',
            'userid' => 'cdu.UserID=%s',
            'subjectid' => "cdu.SubjectID IN (%s)"
        );
        if(array_key_exists(array_keys($joinUserTbl), $params)){
            $sql .= ' LEFT JOIN `zj_user` u ON u.UserID=cdu.UserID';
        }
        $where = $joinUserTbl + $where;
        $string = array();
        foreach($where as $key=>$value){
            if(array_key_exists($key, $params) && !empty($params[$key]) || $params[$key] === 0){
               $string[] = sprintf($value, $params[$key]);
            }
        }
        $string = implode(' AND ', $string);
        if(!$string){
            $string = '1=1';
        }
        $sql .= ' WHERE '.$string .' ORDER BY DUID DESC';
        //如果无需分页，则直接返回结果
        $model = M();
        if(!$isPagtion){
            return $model->query(sprintf($sql, 'cdu.*'));
        }
        $page = (int)$params['p'];
        if(0 === $page){
            $page = 1;
        }
        // exit(sprintf($sql, 'count(DUID) as num'));
        $count = $model->query(sprintf($sql, 'count(DUID) as num'));
        if(!$count){
            $count[0]['num'] = 0;
        }
        $count = $count[0]['num'];
        $prepage = $params['prepage'];
        if(!$prepage){
            $prepage = 10;
        }
        $page = page($count, $page, $prepage);
        $sql .= ' LIMIT '.(($page-1) * $prepage).','.$prepage;
        // exit(sprintf($sql, 'cdu.*'));
        return array($count, $model->query(sprintf($sql, 'cdu.*')));
    }
}
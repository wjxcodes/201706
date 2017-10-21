<?php
/**
 * @author demo  
 * @date 2014年10月31日
 */
/**
 * 解析分配model
 */
namespace Doc\Model;
class DocFileModel extends BaseModel {

    /**
     * 添加上传次数
     * @param int $id doc_file 主键
     * @return boolean
     * @author demo 16-1-25
     */
    public function addUploadTimes($id){
        return $this->unionSelect('conAddData','DocFile', 'uploadTimes=uploadTimes+1', 'FileID='.$id);
    }

    /**
     * 对文档进行审核
     * @param int $id 解析id
     * @param int $status 状态
     * @return boolean
     * @author demo 2015-10-19
     */
    public function check($id, $status){
        return $this->updateData(array(
                'CheckStatus' => (int)$status
            ), 'FileID='.$id);
    }
    
    /**
     * 下载文档
     * @param int $fid 文档ID
     * @param int $ifAdd=true 是否记录下载次数
     * @return string
     * @author demo 
     */
    public function fileDown($fid,$ifAdd=true){
        $where = 'FileID = '.(int)$fid;
        $result = $this->selectData(
            '*',
            $where);
        $file = $result[0]['DocPath'];
        if($ifAdd){
            $data['Points'] = (int)$result[0]['Points']+1;
            $data['LastLoad'] = time();
            if((int)$result[0]['IfDown'] !== 1){
                $data['IfDown'] = 1;
            }
            $this->updateData(
                $data,
                $where);
        }
        return $file;
    }
    /**
     * 验证指定用户是否可操作指定$id(type:doc表或者docfile表)
     * @param string $ids
     * @param string userName
     * @param string $type 查询的类型 doc：doc表，file：docfile表
     * @return boolean|string 可以返回true
     */
    public function isAccess($ids,$userName,$type = 'file',$rules = array()){
        if(empty($ids)){
            return array('30301');
        }
        $where = '`file`.FileID IN('.$ids.')';
        if($type == 'doc'){
            $where = '`file`.DocID IN('.$ids.')';
        }
        $result = $this->unionSelect('docFileSelectByWhere',$where);
        if($type == 'doc' &&  1 == (int)$result[0]['CheckStatus']){
            return array('30708');
        }
        $error = array();
        foreach($result as $iResult){
            if($iResult['UserName'] != $userName){
                $error['userName'] = '您没有操作的权限';
                break;
            }
            if((int)$iResult['IntroFirstTime'] > 0){
                $error['doc'][] = $iResult['DocID'];
            } 
        }
        if(empty($error))
            return true;
        if(isset($error['userName'])){
            return array('30604');
        }
        return array('30705','【'.implode(',', $error['doc']).'】');
    }
    /**
     * 前台首页显示任务信息
     * @param string $userName
     * @return array
     */
    public function getTaskInfo($userName){
        $where = 'UserName = \''.$userName.'\' AND LastLoad = 0';
        return $this->selectCount(
            $where,
            'FileID');
    }
    /**
     * 根据doc编号批量生成任务
     * @param string ids 文档id
     * @param string $usename 用户名
     * @param string $content 任务描述
     * @return array 返回生成的workid数组
     * @author demo
     */
    public function createTask($ids,$userName=''){
        $result = $this->unionSelect('docFileSelectById',$ids);
        $result = $this->divideData($result);
        $arr = array();
        //按学科
        $doc = $this->getModel('Doc');
        foreach($result as $i=>$iResult){
            $description = '';
            //按标引用户
            foreach($iResult as $j=>$jResult){
                if($description == ''){
                    $description = $this->getIntroDescript($iResult[$j]);
                }
                $data = array();
                $data['SubjectID'] = $i;
                $data['Admin'] = $j;
                $data['UserName'] = $userName;
                $data['Content'] = '';
                $data['AddTime'] = $data['LastTime'] = time();
                $data['Status'] = 0;
                $data['IfTask'] = 0;
                $data['CheckTimes'] = 0;
                $data['Content'] = $description;
                $id = $this->getModel('TeacherWork')->insertData(
                    $data);
                if(empty($id)){
                    return $arr;
                }
                //生成一个任务多个文档
                foreach($jResult as $k=>$kResult){  
                    $this->getModel('TeacherWorkList')->insertData(array(
                        'WorkID' => $id,
                        'DocID' => $kResult['DocID'],
                        'Status' => 0,
                        'CheckStatus' => 0
                    ));
                    $this->getModel('Doc')->updateData(
                        array('IfTask'=>1),
                        'DocID = '.$kResult['DocID']);
                }
                $arr[] = $id;
            }   
        }
        return $arr;
    }
    /**
     * 按照学科区分数据
     * @param array $arr 需处理的数据
     * @return array
     * @author demo
     */
    private function divideData($arr){
        $temp = array();
        //提权数组所存在的学科
        foreach($arr as $iArr){
            $temp[$iArr['SubjectID']][$iArr['Admin']][] = $iArr;
        }
        return $temp;
    }
    /**
     * 提取文档标引描述
     * @param array $arr 提取描述的数组
     * @return string
     * @author demo
     */
    private function getIntroDescript($arr){
        $des = '';
        foreach($arr as $iArr){
            if($des == ''){
                $des = $iArr['FileDescription'];
            }else{
                break;
            }
        }
        return $des;
    }
    /**
     * 删除文件
     * @param string $id 需处理的数据
     * @return array
     * @author demo
     */
    public function deleteFile($id){
        $buffer = $this->selectData(
            '*',
            'FileID in (' . $id . ')');
        if ($buffer){
            foreach($buffer as $iBuffer){
                $this->deleteAllFile($iBuffer['DocPath']);
            }
        }
    }
    /**
     * 记录删除文件
     * @param string $filePath 需处理的路径
     * @return array
     * @author demo
     */
    public function deleteAllFile($filePath){
        //记录要删除的文件
        $data=array();
        $data['FilePath'] = $filePath;
        $data['DelTimes'] = 0;
        $data['AddTime'] = time();
        $data['Style'] = 'Doc';
        $this->insertData(
            $data);
    }

    /**
     * 是否已经生成指定文档id的任务
     * @param int $docid 文档id
     * @return boolean
     * @author demo 15-12-23
     */
    public function isCreateTaskByDocId($docid){
        $result = $this->findData('FileID', 'DocID='.(int)$docid);
        if(empty($result)){
            return false;
        }
        return true;
    }
}
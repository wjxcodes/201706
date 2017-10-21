<?php
/**
 * 文档听力管理model
 * @author demo 16-4-21
 */
namespace Doc\Model;
class DocHearingModel extends BaseModel{

    private static $EXTS_MAPPING = array(
        'mp3' => 'audio/mpeg mp3',
        'wav' => 'audio/x-wav',
        'wma' => 'audio/x-ms-wma',
    );

    /**
     * 判断试卷是否存在听力
     * @param int $docid
     * @param boolean 存在返回true
     * @author demo 16-4-25 
     */
    public function isContainHearing($docid){
        $data = $this->findData('Path', 'DocID='.$docid);
        return (!empty($data) && !empty($data['Path']) && strpos($data['Path'], '/Uploads/audio') === 0);
    }

    /**
     * 文件上传
     * @param int $docId 文档编号
     * @param int $adminId 管理员编号
     * @param array $config，
     * @return string|array
     * @author demo 
     */
    public function fileUpload($docId, $adminId, $config=array()){
        if (empty ($_FILES['audio']['name']) || empty ($_FILES['audio']['size'])) {
            return 'success';  //当没有需要上传的文件时，则忽略
        }
        $result = R('Common/UploadLayer/uploadAudioFile',array('audio','audio', $config));
        if(!is_string($result)){
            $result[1] = $result[1].'  听力文件上传失败！';
            return $result;
        }
        $docId = (int)$docId;
        $data = $this->findData('DocID, Path', 'DocID='.$docId);
        if(empty($data)){
            $data = array();
            $data['DocID'] = $docId;
            $data['AdminID'] = $adminId;
            $data['Times'] = 0;
            $data['Path'] = $result;
            $data['AddTime'] = time();
            $result = $this->insertData($data);
        }else{
            $this->getModel('DelFile')->insertData(array(
                'FilePath'=>$data['Path'],
                'DelTimes'=>0,
                'AddTime'=>time(),
                'Style'=>'Audio'
            ));
            $result = $this->updateData(array(
                'Path' => $result,
                'AdminID' => $adminId
            ), 'DocID='.$docId);
        }
        if($result !== false){
            return 'success';
        }
        return 'failure';
    }

    /**
     * 提供数据下载
     * @param int $docId
     * @return boolean 失败时返回false
     * @author demo 16-4-21
     */
    public function downloadAudioFile($docId){
        $data = $this->findData('Path', "DocID={$docId}");
        $host = C('WLN_DOC_HOST');
        $header = get_headers($host.$data['Path'], 1);
        if(empty($data) || $header === false || strpos($header[0], '200') === false){
            return false;
        }
        $this->conAddData('Times=Times+1', 'DocID='.$docId);
        list($filename, $ext) = $this->getFileInfo($data['Path']);
        $url=$host.R('Common/UploadLayer/downloadAudioFile',array($data['Path'], 'audio', $filename));
        $ctype = static::$EXTS_MAPPING[strtolower($ext)];
        $len = $header['Content-Length'];
        header("Content-Type: {$ctype}");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename={$filename}.{$ext};");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: '.$len);
        exit(file_get_contents($url));
    }

    /**
     * 返回指定文件名的名称及扩展名
     * @return array 索引0为名称，1为拓展名
     * @author demo 16-4-21
     */
    public function getFileInfo($file){
        $fileName = basename(strstr($file, '.', true));
        return array($fileName, ltrim(strrchr($file, '.'), '.'));
    }
}
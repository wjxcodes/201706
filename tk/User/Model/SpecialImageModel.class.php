<?php
/**
 * 专题图片
 * @author demo 16-6-1
 */
namespace User\Model;
use Common\Model\BaseModel;

class SpecialImageModel extends BaseModel{


    /**
     * 上传文件
     * @param array $data 上传需要的数据
     * @param int $user 用户id
     * @return array
     * @author demo 16-6-3
     */
    public function uploadFile($files, $user, $title){
        $time = time(); //一批图片共用一个时间点
        $upload = useToolFunction('UploadFile'); // 实例化上传类
        $uploadToServer = $this->getModel('Upload');
        $upload->maxSize  = pow(1024,2)*2; // 设置附件上传大小
        $upload->allowExts= array('jpg', 'png', 'gif', 'jpeg'); // 设置附件上传类型
        $path = './Uploads/bbs/'.date('Y').'/'.date('md').'/';
        $error = array();
        $size = (int)count($files['name']);
        if(empty($size)){
            $data = array();
            $data['status'] = 'empty';
            $data['data'] = '上传的数据为空或者存在大于2MB的文件！';
            return $data;
        }
        for($i=0; $i<$size; $i++){
            if(4 == $files['error'][$i] || $files['size'][$i] <= 0){
                continue;
            }
            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );
            $info = $upload->uploadOne($file, $path);
            if($info === false){
                $error[$i] = $upload->getErrorMsg();
                continue;
            }
            $realPath = $info[0]['savepath'].$info[0]['savename'];
            $urlPath = $uploadToServer->upFileToServer($realPath, 'bbs', 'bbs');
            if(strpos($urlPath, 'error') === 0){
                $error[$key] = '上传文件失败';
                unlink($realPath);
                continue;
            }
            unlink($realPath);
            $records = array(
                'UserID' => $user,
                'LoadTime' => $time,
                'Title' => $title,
                'Path' => $urlPath,
                'Status' => 2
            );

            $result = $this->insertData($records);
            if($result === false){
                $error[$key] = '保存数据失败';
            }
        }
        $data = array();
        $data['status'] = 'success';
        if(!empty($error)){
            $data['status'] = 'error';
            $data['data'] = $error;
        }
        return $data;
    }

    /**
     * 上传
     * @param array $data 上传需要的数据
     * @param int $user 用户id
     * @return array
     * @author demo 16-6-2
     */
    public function upload($data, $user){
        $time = time(); //一批图片共用一个时间点
        $error = array();
        $title = $data['Title'];
        $exts = array('png', 'jpg', 'gif', 'jpeg');
        $upload = $this->getModel('Upload');
        foreach($data['Images'] as $key=>$value){
            if(empty($value)){
                continue;
            }
            $value = str_replace('data:image/jpeg;base64,', '', $value);
            $value = base64_decode($value);
            if($value  === false){
                $error[$key] = '不是有效的图片！';
                continue;
            }
            $name = md5(mt_rand().mt_rand().mt_rand().time());
            $path = './Uploads/bbs/'.date('Y').'/'.date('md').'/';
            if(!is_dir($path)){
                mkdir($path,0755,true);
            }
            $ext = strtolower($data['Ext'][$key]);
            if(empty($ext) || !in_array($ext, $exts)){
                $error[$key] = '上传图片不支持【'.$ext.'】格式';
                continue;
            }
            $realPath = $path.$name.'.'.$ext;
            if(file_put_contents($realPath, $value) === false){
                $error[$key] = '无法保存文件';
                continue;
            }
            $imgSize = filesize($realPath);
            if($imgSize > pow(1024,2)*2){
                $error[$key] = '不能上传大于2MB的图片';
                unlink($realPath);
                continue;
            }
            
            $urlPath = $upload->upFileToServer($realPath, 'bbs', 'bbs');
            if(strpos($urlPath, 'error') === 0){
                $error[$key] = '上传文件失败';
                unlink($realPath);
                continue;
            }
            unlink($realPath);
            $records = array(
                'UserID' => $user,
                'LoadTime' => $time,
                'Title' => $title,
                'Path' => $urlPath,
                'Status' => 2
            );
            $result = $this->insertData($records);
            if($result ===  false){
                $error[$key] = '保存数据失败';
            }
        }
        $data = array();
        $data['status'] = 'success';
        if(!empty($error)){
            $data['status'] = 'error';
            $data['data'] = $error;
        }
        return $data;
    }

    /**
     * 添加需删除的文件
     * @author demo 
     */
    public function appendDelFile($path){
        //写入del_file，删除远程文件
        $delFile = array();
        $delFile['FilePath'] = $path;
        $delFile['DelTimes'] = 0;
        $delFile['AddTime'] = time();
        $delFile['Style'] = 'bbs';
        $this->getModel('DelFile')->insertData($delFile);
    }
}
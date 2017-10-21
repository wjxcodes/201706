<?php
/**
 * @date 2014年9月26日
 * @author demo
 */
/**
 * 用户章节模型类
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserChapterModel extends BaseModel
{
    /**
     * 检测用户学科下是否存在章节信息
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @return bool 是否存在记录
     */
    public function ifExist($userName,$subjectID){
        $db = $this->findData('UserChapter','*',['UserName'=>$userName,'SubjectID'=>$subjectID]);
        return $db?true:false;
    }
    /**
     * 更新用户学科下的章节数据
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param array $data 章节数组，数组每一项为章节ID
     * @return bool 是否更新成功
     * @author demo
     */
    public function update($userName,$subjectID,$data){
        //验证章节数据是否合法 是否属于章节表是否和学科对应
        $chapterCache = SS('chapterList');
        foreach($data as $iData){
            if(array_key_exists($iData,$chapterCache)){
                if($chapterCache[$iData]['SubjectID']!=$subjectID){
                    return false;
                }
            }else{
                return false;
            }
        }
        //删除原来数据
        $this->deleteData(
            ['UserName'=>$userName,'SubjectID'=>$subjectID]);
        //添加新数据
        foreach($data as $iData){
            $add = $this->insertData(
                ['UserName'=>$userName,'SubjectID'=>$subjectID,'ChapterID'=>$iData,'Date'=>time()]);
            if($add == false){
                return false;
            }
        }
        return true;
    }


    /**
     * 获取用户的所有章节数据
     * 用于通过章节推题时章节条件 用于判断用户学科下是否选择章节
     * @param string $userName 用户名
     * @param int $subjectID 学科ID
     * @param bool $isAll 是否需要子章节的所有数据
     * @return string|bool isAll=true用户学科下所有章节的子章节ID（英文逗号分隔）isAll=false 用户所选的章节ID，没有子章节数据
     * @author demo
     */
    public function getUserAllChapter($userName,$subjectID,$isAll = true){
        $db = $this->selectData(
            '*',
            ['UserName'=>$userName,'SubjectID'=>$subjectID]);
        if($db == false){
            return false;
        }

        if($isAll === true){
            $result = '';
            $chapterCache = SS('chapterList');
            foreach($db as $iDb){
                if(!$chapterCache[$iDb['ChapterID']]){
                    return false;
                }
                $result .= $chapterCache[$iDb['ChapterID']]['ChildList'].',';
            }
        }else{
            $result = [];
            foreach($db as $iDb){
                $result[] = $iDb['ChapterID'];
            }
            $result = implode(',',$result);
        }

        return $result;
    }

    /**
     * 获取用户章节数据用于前台显示列表
     * 数据只显示用户选择的章节
     * @param $userName
     * @param $subjectID
     * @return array 3级数组结构
     */
    public function getUserChapterList($userName,$subjectID){
        $db = $this->selectData(
            '*',
            ['UserName'=>$userName,'SubjectID'=>$subjectID]);
        if($db == false){
            return false;
        }
        $result = '';
        $chapterCache = SS('chapterChildList');
        $chapterListCache = SS('chapterList');
        if(!$chapterCache||!$chapterListCache){
            return false;
        }
        foreach($db as $i=>$iDb){
            $book = $chapterListCache[$iDb['ChapterID']];//课本
            $result[$i] = [
                'chapterID'=>$iDb['ChapterID'],
                'chapterName'=>$book['ChapterName']
            ];
            $unit = $chapterCache[$iDb['ChapterID']];//单元
            foreach($unit as $j=>$jUnit){
                $result[$i]['sub'][$j] = [
                    'chapterID'=>$jUnit['ChapterID'],
                    'chapterName'=>$jUnit['ChapterName']
                ];
                $class = $chapterCache[$jUnit['ChapterID']];//每一课
                foreach($class as $k=>$kClass){
                    $result[$i]['sub'][$j]['sub'][$k] = [
                        'chapterID'=>$kClass['ChapterID'],
                        'chapterName'=>$kClass['ChapterName']
                    ];
                }
            }
        }
        return $result;
    }

}
<?php
/**
 * @author demo
 * @date 2016年01月02日
 */

namespace Aat\Api;

class ChapterApi extends BaseApi
{
    /**
     * 描述：
     * @param $version
     * @param $username
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function check($version,$username,$subjectID){
        if($version == 2){
            //如果同步版本检查章节是否选择
//            $chapter = $this->getModel('UserChapter')->findData(
//                'UserChapterID',
//                ['UserName'=>$username,'SubjectID'=>$subjectID]
//            );
            $chapter = $this->getApiUser('User/findData','UserChapterID',['UserName'=>$username,'SubjectID'=>$subjectID],'','UserChapter');
            if(!$chapter){
                return [0,'50019']; //请先选择课本！
            }
            return [1,$chapter];
        }else{
            //高考版本不需要选择章节
            return [1,''];
        }
    }

    /**
     * 描述：章节类型
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function types($subjectID){
        $data = $this->getData(['style'=>'chapter','return'=>2,'subjectID'=>$subjectID]);
        $result = [];
        foreach($data as $iData){
            $result[] = [
                'chapterID' => $iData['ChapterID'],
                'chapterName' => $iData['ChapterName'],
            ];
        }
        if($result){
            return [1,$result];
        }else{
            return [0,'教材版本获取失败，请重试！'];
        }
    }

    /**
     * 描述：获取章节课本
     * @param $chapterID
     * @param $username
     * @param $subjectID
     * @return array
     * @author demo
     */
    public function book($chapterID,$username,$subjectID){
//        $userChapterModel=$this->getModel('UserChapter');
        $data = $this->getData([
            'style'=>'chapter',
            'return'=>2,
            'pID'=>$chapterID,
            'haveLayer'=>1
        ]);
        if($data){
            $books = [];
            foreach($data as $iData){
                $books[] = [
                    'chapterID' => $iData['ChapterID'],
                    'chapterName' => $iData['ChapterName'],
                    'chapterPic' => $iData['ChapterPic']?(C('WLN_DOC_HOST').$iData['ChapterPic']):'',
                ];
            }
//            $info=$userChapterModel->getUserAllChapter($username,$subjectID,false);
            $info = $this->getApiUser('User/getUserAllChapter', $username,$subjectID,false);
            $result['book']=$books;
            $result['selectedBook']=$info;
            return [1,$result];
        }else{
            return [0,'教材获取失败，请重试！'];
        }
    }

    /**
     * 描述：更新用户章节
     * @param string $chapterIDString 逗号分隔的章节ID
     * @param $username
     * @param int $subjectID
     * @return array
     * @author demo
     */
    public function update($chapterIDString,$username,$subjectID){
        if(!is_array($chapterIDString)){
            $chapterIDString = explode(',',$chapterIDString);
        }
        if(count($chapterIDString)==0){
            return [0,'请选择您所学的课本教材！'];
        }
        if(!$subjectID){
            return [0,'请先选择学科！'];
        }
//        $result = $this->getModel('UserChapter')->update($username,$subjectID,$chapterIDString);
        $result = $this->getApiUser('User/updateUserChapter', $username,$subjectID,$chapterIDString);
        if($result == false){
            return [0,'更新失败，请重试！'];
        }
        return [1,''];
    }

}
<?php
/**
 * @author demo
 * @date 2014年10月10日
 */
/**
 * 用户章节控制器
 */
namespace AatApi\Controller;
class UserChapterController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 检测用户某学科是否选择章节
     * @author demo
     */
    public function check() {
        $IData = $this->getApiAat('Chapter/check', $this->getVersion());
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 描述：返回APP选择教材版本第一个界面所需数据
     * @return array
     *    [
     *   "data"=>[
     *       "12"=>[
     *           "subjectID"=>12, 
     *           "subjectName"=>"语文", 
     *           "typeName"=>"请选择教材版本", //当前学科选择的教材版本（如果没有选择，显示“请选择教材版本”） 
     *           "bookName"=>""//当前学科选择的课本名称（如果没有选择，现实空字符串）
     *       ], 
     *       ...
     *   ], 
     *   "info"=>"success", 
     *   "status"=>1
     * ]
     * @author demo
     */
    public function getUserSubjectAndChapter(){
        $this->checkRequest();
        $userChapterDb = $this->getModel('UserChapter')->selectData(
            'UserName,SubjectID,ChapterID',
            ['UserName'=>$this->getUserName()]
        );
        $userChapter = [];//用户章节ID数据
        $userSubjectChapter = [];//用户学科章节数据
        $chapterCache = SS('chapterBySubjectN');
        foreach($userChapterDb as $iUserChapter){
            $userChapter[]= $iUserChapter['ChapterID'];
        }
        foreach($chapterCache as $subjectChapter){
            foreach($subjectChapter as $type){
                foreach($type['sub'] as $book){
                    if(in_array($book['ChapterID'],$userChapter)){
                        $userSubjectChapter[$book['SubjectID']]['type'] = $type;
                        $userSubjectChapter[$book['SubjectID']]['book'][] = $book['ChapterName'];
                    }

                }
            }
        }
        $subjectPID = SS('grade')[$this->getGradeID()]['SubjectID'];
        $result = [];
        foreach(SS('subject') as $i=>$iSubject){
            if($iSubject['PID'] == $subjectPID){
                $result[$i]['subjectID'] = $i;
                $result[$i]['subjectName'] = $iSubject['SubjectName'];
                if($userChapterDb){
                    $result[$i]['typeName'] = $userSubjectChapter[$i]['type']['ChapterName']?$userSubjectChapter[$i]['type']['ChapterName']:'';
                    $result[$i]['bookName'] = $userSubjectChapter[$i]['book']?implode('、',$userSubjectChapter[$i]['book']):'请选择';
                }else{
                    $result[$i]['typeName'] = '';
                    $result[$i]['bookName'] = '请选择';
                }
            }
        }
        $this->setBack($result);
    }

    /**
     * 根据学科获取教材版本
     * @param int subjectID 学科ID true
     * @return array
     *    [
     *   "data"=>[
     *       [
     *           "chapterID"=>"111", 
     *           "chapterName"=>"人教A版"
     *       ], 
     *       [
     *           "chapterID"=>"274", 
     *           "chapterName"=>"人教B版"
     *       ]
     *   ], 
     *   "info"=>"success", 
     *   "status"=>1
     * ]
     * @author demo
     */
    public function getType() {
        $this->checkRequest();
        $IData = $this->getApiAat('Chapter/types', $this->getSubjectID());
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 根据教材版本获取教材书本信息
     * @param int id 教材版本ID true
     * @return array
     *    [
     *   "data"=>[
     *      "book"=>[
     *                  [
     *                      "chapterID"=>"111", 
     *                      "chapterName"=>"人教A版",
     *                      "chapterPic"=> ""
     *                  ], 
     *                  [
     *                      "chapterID"=>"274", //教材课本ID 
     *                      "chapterName"=>"人教B版",
     *                      "chapterPic"=> ""// 教材课本的封面图片，冗余字段
     *                  ],
     *                  ...
     *              ],
     *     "selectedBook"=> "171,154"//用于已经选择的课本的ID，英文逗号分隔 
     *   ], 
     *   "info"=>"success", 
     *   "status"=>1
     * ]
     * @author demo
     */
    public function getBook() {
        $this->checkRequest();
        $chapterID = $_POST['id'];//ChapterID
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Chapter/book', $chapterID,$username,$subjectID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

    /**
     * 更新用户不同学科下的教材版本信息
     * @param array chapterIDString 教材课本ID的数组  true
     * @return array
     *   [
     *   "data"=>null, 
     *   "info"=>"success", 
     *   "status"=>1
     *   ]
     * @author demo
     */
    public function update() {
        $this->checkRequest();
        $chapterIDString = $_POST['chapterIDString'];//逗号分隔的章节ID
        $username = $this->getUserName();
        $subjectID = $this->getSubjectID();
        $IData = $this->getApiAat('Chapter/update', $chapterIDString,$username,$subjectID);
        if($IData[0] == 1){
            return $this->setBack($IData[1]);
        }
        return $this->setError($IData[1], 1);
    }

}
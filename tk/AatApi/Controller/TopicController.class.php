<?php
/**
 * @author demo
 * @date 2015年05月13日
 */
/**
 * Class TopicAction
 * 目前只有手机端使用此类方法
 * 专题类
 */
namespace AatApi\Controller;
class TopicController extends BaseController
{
    /**
     * 初始化方法
     * @author demo
     */
    public function _initialize() {
    }

    /**
     * 获取专题试卷
     * @author demo
     */
    public function topicPaper(){
        $topicID = $_POST['topicID'];
        $topicPaperName = $_POST['paperName'];
        if(!$topicID){
            return $this->setError('51003', 1); //专题ID为空！
        }
        $topicDb = $this->getModel('Topic')->findData(
            'TopicName',
            ['TopicID'=>$topicID,'Status'=>1,'Type'=>'aat']
        );
        if(!$topicDb){
            return $this->setError('51004', 1); //专题不存在或锁定！
        }
        $subjectCache = SS('subject');
        $where = [
            'TopicID'=>$topicID,'Status'=>1
        ];
        if($topicPaperName){
            $where['TopicPaperName'] = ['like','%'.$topicPaperName.'%'];
        }
        $paperDb = $this->getModel('TopicPaper')->selectData(
            'TopicPaperID,TopicPaperName,TopicPaperDesc,SubjectID,IfDown,TestTimes,AddTime',
            $where
        );
        if(!$paperDb){
            return $this->setError('51005', 1); //暂时没有试卷！
        }
        $result = [];
        foreach($paperDb as $iPaperDb){
            $result[] = [
                'topicPaperID'=>$iPaperDb['TopicPaperID'],
                'topicPaperName'=>$iPaperDb['TopicPaperName'],
                'topicPaperDesc'=>$iPaperDb['TopicPaperDesc'],
                'subjectName'=>$subjectCache[$iPaperDb['SubjectID']]['subjectName'],
                'ifDown'=>$iPaperDb['IfDown'],
                'testTimes'=>$iPaperDb['TestTimes'],
                'addTime'=>$iPaperDb['AddTime']
            ];
        }
        $this->setBack($result);
    }

}
<?php
/**
 * @author demo
 * @date 15-5-4 上午11:28
 */
/**
 * 导学案栏目类，用于导学案栏目的操作
 */
namespace Guide\Model;
use Common\Model\BaseModel;
class CaseMenuModel extends BaseModel{
    /**
     * 生成缓存数组
     * @author demo
     */
    public function getCaseForum(){
        return array('1' => array('name'      => '预习案',
                                  'otherName' => '课前预习'),
                     '2' => array('name'      => '探究案',
                                  'otherName' => '知识拓展'),
                     '3' => array('name'      => '练习案',
                                  'otherName' => '课后练习'));
    }
    /**
     * 生成缓存数组
     * @author demo
     */
    public function setCache(){
        $menuArray=array();
        $buffer=$this->selectData(
            '*',
            '1=1',
            'SubjectID asc,ForumID asc,OrderID asc'
        );
        foreach($buffer as $i=>$iBuffer){
            $menuArray[$iBuffer['MenuID']]=$iBuffer;
            $subjectMenu[$iBuffer['SubjectID']][$iBuffer['MenuID']]=$iBuffer;
            $subjectForumMenu[$iBuffer['SubjectID']][$iBuffer['ForumID']][$iBuffer['MenuID']]=$iBuffer;
        }
        S('caseMenu',$menuArray);//ID作键的导学案栏目缓存
        S('menuSubject',$subjectMenu);//学科做键的导学案栏目缓存
        S('subjectForumMenu',$subjectForumMenu);//学科做键的导学案栏目缓存
    }

    /**
     * 获取缓存
     * @author demo
     */
    public function getCache($str='casemenu',$num=0){
        switch($str){
            case 'caseMenu':
                $buffer=S('caseMenu');
                break;
            case 'menuSubject':
                $buffer=S('menuSubject');
                break;
            case 'subjectForumMenu':
                $buffer=S('subjectForumMenu');
                break;
            default :
                return false;
        }
        if(empty($buffer) && $num==0){
            $this->setcache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}
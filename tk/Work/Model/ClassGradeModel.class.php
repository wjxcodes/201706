<?php
/**
 * @author demo
 * @date 2014年8月10日
 */
/**
 * 年级管理模型类，用于年级管理相关操作
 */
namespace Work\Model;
class ClassGradeModel extends BaseModel{
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'OrderID asc,GradeID asc');
    }

    /**
     * 缓存数组；
     * @author demo
     */
    public function setCache(){
        $subjectArr=array();
        $subjectGradeArr=array();

        $subjectPar=$this->getModel('Subject')->selectData(
            '*',
            'PID=0'
        );
        $subjectParentId=array();
        foreach($subjectPar as $iSubjectPar){
            $subjectParentId[$iSubjectPar['SubjectID']]=$iSubjectPar;
        }

        $subjectAll=$this->selectData(
            '*',
            '1=1',
            'OrderID asc,GradeID asc'
        );
        if($subjectAll){
            foreach($subjectAll as $isubjectAll){
                $subjectArr[$isubjectAll['GradeID']]['GradeName']=$isubjectAll['GradeName'];
                $subjectArr[$isubjectAll['GradeID']]['SubjectID']=$isubjectAll['SubjectID'];
                $subjectGradeArr[$isubjectAll['SubjectID']]['sub'][]=$isubjectAll;
                $subjectGradeArr[$isubjectAll['SubjectID']]['GradeName']=$subjectParentId[$isubjectAll['SubjectID']]['SubjectName'];
            }
        }

        S('grade',$subjectArr);//年级所有缓存，ID作为键
        S('gradeListSubject',$subjectGradeArr);//根据学科ID作键，获取年级
    }
    /**
     * 判断缓存是否存在
     * @param string 缓存标识 去掉死循环
     * @return string
     * @author demo 
     */
    public function getCache($str='grade',$num=0){
        switch($str){
            case 'grade':
                $buffer=S('grade');
                break;
            case 'gradeListSubject':
                $buffer=S('gradeListSubject');
                break;
            default:
                return false;
                
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }

}
?>
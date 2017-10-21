<?php
/**
 * @author demo
 * @date 2014-12-26
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class SubjectModel extends BaseModel{
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'OrderID asc,SubjectID asc');
    }
    //缓存数组
    public function setCache(){
        $subjectChild=array();//学科子类的数据集 以父类id为索引
        $subjectTree=array();//学科id为数组索引的树形结构
        $subjectArr=array();//学科id为数组索引的数据集
        $parentArr=array(); //父类id数据集
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc,SubjectID asc');
        if($buffer){
            //获取学科子类的数据集 以父类id为索引
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['PID']!=0){
                    $subjectChild[$iBuffer['PID']][]=$iBuffer;
                }
            }
            //父类id数据集
            //学科数据树形结构
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['PID']==0){
                    $subjectTree[$iBuffer['SubjectID']]=$iBuffer;
                    $subjectTree[$iBuffer['SubjectID']]['FullName']=$subjectTree[$iBuffer['SubjectID']]['SubjectName'].'题库';
                    if($subjectChild[$iBuffer['SubjectID']]){
                        //为数据添加父类名称 即高中数学
                        foreach($subjectChild[$iBuffer['SubjectID']] as $j=>$jSubjectChild){
                            $subjectChild[$iBuffer['SubjectID']][$j]['SubjectName']=$iBuffer['SubjectName'].$jSubjectChild['SubjectName'];
                        }
                        $subjectTree[$iBuffer['SubjectID']]['sub']=$subjectChild[$iBuffer['SubjectID']];
                    }
                    $parentArr[]=$iBuffer;
                }
            }
            foreach($buffer as $iBuffer){
                $subjectArr[$iBuffer['SubjectID']]['SubjectID']=$iBuffer['SubjectID'];
                $subjectArr[$iBuffer['SubjectID']]['SubjectName']=$iBuffer['SubjectName'];
                if($iBuffer['PID']!=0) $subjectArr[$iBuffer['SubjectID']]['ParentName']=$subjectTree[$iBuffer['PID']]['SubjectName'];
                $subjectArr[$iBuffer['SubjectID']]['PID']=$iBuffer['PID'];
                $subjectArr[$iBuffer['SubjectID']]['Style']=$iBuffer['Style'];
                $subjectArr[$iBuffer['SubjectID']]['TotalScore']=$iBuffer['TotalScore'];
                $subjectArr[$iBuffer['SubjectID']]['TestTime']=$iBuffer['TestTime'];
                $subjectArr[$iBuffer['SubjectID']]['ChapterSet']=$iBuffer['ChapterSet'];
                $subjectArr[$iBuffer['SubjectID']]['FormatDoc']=$iBuffer['FormatDoc'];
                $subjectArr[$iBuffer['SubjectID']]['MoneyStyle']=$iBuffer['MoneyStyle'];
                $subjectArr[$iBuffer['SubjectID']]['PayMoney']=$iBuffer['PayMoney'];
                $subjectArr[$iBuffer['SubjectID']]['Layout']=$iBuffer['Layout'];
            }
        }
        S('subject',$subjectArr);//ID作键，的学科缓存
        S('subjectParent',$parentArr);//分父级学科缓存
        S('subjectParentId',$subjectTree);//以学科id为索引的树形结构
    }
    //获取缓存
    public function getCache($str='subject',$num=0){
        if($str=='subject'){
            $buffer=S('subject');
        }elseif($str=='subjectParent'){
            $buffer=S('subjectParent');
        }elseif($str=='subjectParentId'){
            $buffer=S('subjectParentId');
        }
        if(empty($buffer) && $num==0){
            $this->setCache();
            $buffer=$this->getCache($str,1);
        }
        return $buffer;
    }
}
?>
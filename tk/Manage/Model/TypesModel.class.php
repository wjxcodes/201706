<?php
/**
 * @author demo 
 * @date 2015年1月4日
 * @update 2015年1月13日
 */
/**
 * 文档类型管理模型类，用于文档类型管理相关操作
 *
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class TypesModel extends BaseModel{
    /**
     * 获取所有数据
     * @return array
     * @author demo
     */
    public function getAllData(){
        return $this->selectData(
            '*',
            '1=1',
            'OrderID asc,TypesID asc');
    }

    /**
     * 设置缓存
     * @author demo
     */
    public function setCache(){
        $typesArr=array();
        $subjectArr=array();
        $buffer = $this->selectData(
            '*',
            '1=1',
            'OrderID asc,TypesID asc');
        if($buffer)
        foreach($buffer as $iBuffer){
            $typesArr[$iBuffer['TypesID']]['TypesName']=$iBuffer['TypesName'];
            $typesArr[$iBuffer['TypesID']]['SubjectID']=$iBuffer['SubjectID'];
            $typesArr[$iBuffer['TypesID']]['Volume']=$iBuffer['Volume'];
            $typesArr[$iBuffer['TypesID']]['DScore']=$iBuffer['DScore'];
            $typesArr[$iBuffer['TypesID']]['TypesScore']=$iBuffer['TypesScore'];
            $typesArr[$iBuffer['TypesID']]['MaxScore']=$iBuffer['MaxScore'];
            $typesArr[$iBuffer['TypesID']]['OrderID']=$iBuffer['OrderID'];
            $typesArr[$iBuffer['TypesID']]['TypesStyle']=$iBuffer['TypesStyle'];
            $typesArr[$iBuffer['TypesID']]['Num']=$iBuffer['Num'];
            $typesArr[$iBuffer['TypesID']]['IfPoint']=$iBuffer['IfPoint'];
            $typesArr[$iBuffer['TypesID']]['IfSingle']=$iBuffer['IfSingle'];
            $typesArr[$iBuffer['TypesID']]['IfSearch']=$iBuffer['IfSearch'];
            $typesArr[$iBuffer['TypesID']]['IfDo']=$iBuffer['IfDo'];
            $typesArr[$iBuffer['TypesID']]['IfChooseType']=$iBuffer['IfChooseType'];
            $typesArr[$iBuffer['TypesID']]['IfChooseNum']=$iBuffer['IfChooseNum'];
            $typesArr[$iBuffer['TypesID']]['SelectType']=$iBuffer['SelectType'];
            $typesArr[$iBuffer['TypesID']]['ScoreNormal']=$iBuffer['ScoreNormal'];
            $typesArr[$iBuffer['TypesID']]['ScoreIntro']=$iBuffer['ScoreIntro'];
            $typesArr[$iBuffer['TypesID']]['ScoreMiss']=$iBuffer['ScoreMiss'];
            $typesArr[$iBuffer['TypesID']]['IntelName']=$iBuffer['IntelName'];
            $iBuffer['IntelNum']=$typesArr[$iBuffer['TypesID']]['IntelNum']=explode(',',$iBuffer['IntelNum']);
            $typesArr[$iBuffer['TypesID']]['ScorePic']=$iBuffer['ScorePic'];
            $typesArr[$iBuffer['TypesID']]['Underline']=$iBuffer['Underline'];
            $typesArr[$iBuffer['TypesID']]['CardIfGetTest']=$iBuffer['CardIfGetTest'];

            $subjectArr[$iBuffer['SubjectID']][]=$iBuffer;
        }
        //题型按照分卷调整顺序
        foreach($subjectArr as $i=>$iSubjectArr){
            for($j=0;$j<count($iSubjectArr);$j++){
                for($k=$j+1;$k<count($iSubjectArr);$k++){
                    if($subjectArr[$i][$j]['Volume']>$subjectArr[$i][$k]['Volume']){
                        $tmpArr=array();
                        $tmpArr=$subjectArr[$i][$j];
                        $subjectArr[$i][$j]=$subjectArr[$i][$k];
                        $subjectArr[$i][$k]=$tmpArr;
                    }
                }
            }
        }
        S('types',$typesArr);//ID作键的题型缓存
        S('typesSubject',$subjectArr);//根据学科ID，分组题型
    }
    /**
     * 获取缓存数据
     * @author demo
     */
    public function getCache($str='types',$num=0){
        switch($str){
            case 'types':
                $buffer=S('types');
                break;
            case 'typesSubject':
                $buffer=S('typesSubject');
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
    //获取父类列表    待删除
    public function getParList($paramArr){
        if($paramArr['SubjectID']){
            $SubjectID=$paramArr['SubjectID'];
        }
        if($SubjectID){
            $subjectTypes=SS('typesSubject');
            return $subjectTypes[$SubjectID];
        }else{
            return SS('types');
        }
    }
    //数组变option 待删除
    public function setOption($arr,$id=0){
        $output='';
        foreach($arr as $iArr){
            $output.='<option value="'.$iArr['TypesID'].'" '.($iArr['TypesID']==$id ? "selected=\"selected\"" : "") .'>'.$iArr['TypesName'].'</option>';
            if($iArr['sub'])
            foreach($iArr['sub'] as $jArr){
                $output.='<option value="'.$jArr['TypesID'].'" '.($jArr['TypesID']==$id ? "selected=\"selected\"" : "") .'>　　'.$jArr['TypesName'].'</option>';
            }
        }
        if(!$output) return '<option value="">请添加题型</option>';
        return $output;
    }
}
?>
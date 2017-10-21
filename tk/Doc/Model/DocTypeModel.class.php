<?php
/**
 * @author demo
 * @date 2014年12月29日
 */
/**
 * 文档类型管理模型类，用于文档类型管理相关操作
 */
namespace Doc\Model;
class DocTypeModel extends BaseModel{

    /**
     * 设置缓存
     * @author demo
     */
    public function setCache(){
        $docTypeArr=array();
        $docType = $this->selectData(
            '*',
            '1=1',
            'OrderID asc,TypeID asc');
        if($docType)
            foreach($docType as $iDocTypeArr){
                $docTypeArr[$iDocTypeArr['TypeID']]['TypeID']=$iDocTypeArr['TypeID'];
                $docTypeArr[$iDocTypeArr['TypeID']]['TypeName']=$iDocTypeArr['TypeName'];
                $docTypeArr[$iDocTypeArr['TypeID']]['Tag']=$iDocTypeArr['Tag'];
                $docTypeArr[$iDocTypeArr['TypeID']]['DefaultTest']=$iDocTypeArr['DefaultTest'];
                $docTypeArr[$iDocTypeArr['TypeID']]['GradeList']=$iDocTypeArr['GradeList'];
                $docTypeArr[$iDocTypeArr['TypeID']]['IfHidden']=$iDocTypeArr['IfHidden'];
                $docTypeArr[$iDocTypeArr['TypeID']]['LimitDown']=$iDocTypeArr['LimitDown'];
            }
        $dcoChapterArr = $this->selectData(
            '*',
            '1=1',
            'ChapterOrder asc,TypeID asc');
        $docChapter=array();
        if($dcoChapterArr)
            foreach($dcoChapterArr as $iDcoChapterArr){
                $docChapter[$iDcoChapterArr['TypeID']]['TypeName']=$iDcoChapterArr['TypeName'];
                $docChapter[$iDcoChapterArr['TypeID']]['Tag']=$iDcoChapterArr['Tag'];
                $docChapter[$iDcoChapterArr['TypeID']]['DefaultTest']=$iDcoChapterArr['DefaultTest'];
                $docChapter[$iDcoChapterArr['TypeID']]['GradeList']=$iDcoChapterArr['GradeList'];
                $docTypeArr[$iDcoChapterArr['TypeID']]['IfHidden']=$iDcoChapterArr['IfHidden'];
                $docTypeArr[$iDcoChapterArr['TypeID']]['LimitDown']=$iDcoChapterArr['LimitDown'];
            }
        S('docType',$docTypeArr);//按照文档排序，文档类型
        S('docChapterOrder',$docChapter);//按照章节排序，文档类型
    }
    /**
     * 获取缓存数据
     * @author demo
     */
    public function getCache($str='docType',$num=0){
        switch($str){
            case 'docType':
                $buffer=S('docType');
                break;
            case 'docChapterOrder':
                $buffer=S('docChapterOrder');
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

    /**
     * ajax获取试卷属性
     * @param int $style
     * @param int $ifHidden
     * @return array
     */
    public function getDocAttr($style=0,$ifHidden=0) {
        $output = array ();
        if($style==1) $buffer=SS('docChapterOrder');
        else $buffer = SS('docType');
        if($buffer){
            $j=0;
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['IfHidden']==1 && $ifHidden==1){
                    continue;
                }
                $output[$j]['TypeID']=$i;
                $output[$j]['TypeName']=$iBuffer['TypeName'];
                $output[$j]['GradeList']=$iBuffer['GradeList'];
                $j++;
            }
        }
        return $output;
    }
}
?>
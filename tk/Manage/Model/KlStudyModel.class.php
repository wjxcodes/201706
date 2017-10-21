<?php
/**
 * @author demo 
 * @date 
 * @update 2015年1月26日
 */
/**
 * 考点学习管理Model类，用于自定义打分的相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class KlStudyModel extends BaseModel{
    /**
     * 获取知识点学习对应视频列表
     * @param array $arr 知识点id数组
     * @param array $knowledge 知识点缓存数据
     * @return array
     * @author demo
     */
    public function getKlList($arr,$knowledge) {
        $output=array();
        if(!$arr) return ;
        //获取知识点学习的数据
        $klStudyBuffer=$this->selectData(
            'VideoList,KlID',
            'KlID in ('.implode(',',$arr).')',
            'StudyID ASC'
        );
        $klStudyID=array();
        if($klStudyBuffer){
            $ii=0;
            foreach($klStudyBuffer as $iKlStudyBuffer){
                if($iKlStudyBuffer['VideoList']){
                    $tmpArr=explode('#@#',stripslashes($iKlStudyBuffer['VideoList']));
                    foreach($tmpArr as $j=>$jTmpArr){
                        $tmpArr2=explode('#$#',$jTmpArr);
                        if($tmpArr2[0] && $tmpArr2[1]){
                            $klStudyID[$iKlStudyBuffer['KlID']][$ii]['VideoID']=$tmpArr2[0];
                            $klStudyID[$iKlStudyBuffer['KlID']][$ii]['VideoName']=$tmpArr2[1];
                            $klStudyID[$iKlStudyBuffer['KlID']][$ii]['VideoOrder']=$j;
                            $ii++;
                        }
                    }
                }
            }
        }
        //是否传递了知识点缓存
        if(!$knowledge){
            $knowledge[0]=SS('knowledge');
            $knowledge[1]=SS('knowledgeParent');
        }
        $phone = $_REQUEST['phone'];
        foreach($arr as $iArr){
            $tmpStr=array();
            $tmpArr=$knowledge[1][$iArr];
            if($tmpArr){
                foreach($tmpArr as $iTmpArr){
                    $tmpStr[]=$iTmpArr['KlName'];
                }
            }
            $tt=$knowledge[0][$iArr]['KlName'];
            if($klStudyID[$iArr]){
                foreach($klStudyID[$iArr] as $iKlStudyID){
                    $kID = $iArr;
                    $tID = $iKlStudyID['VideoOrder'];
                    //如果是手机端增加交互的js
                    $onClick = $phone?' onclick="javascript:aat.loadVideo(\''.
                        U('AatApi/KnowledgeStudy/video',['kID'=>$kID,'tID'=>$tID],false,true).'\',\''.$iKlStudyID['VideoName'].'\');"':'';
                    $tt.='<span class="videolist" kid="'.$kID.'" tid="'.$tID.'" '.$onClick.
                        ' tname="'.htmlspecialchars($iKlStudyID['VideoName']).'" >'.$iKlStudyID['VideoName'].'</span>';
                }
            }
            $tmpStr[]=$tt;
            $output[]=implode(' >> ',$tmpStr);
        }
        return $output;
    }

}
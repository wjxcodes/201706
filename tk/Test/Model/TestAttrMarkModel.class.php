<?php
/**
 * @author demo 
 * @date 
 * @update 2015年1月26日
 */
/**
 * 自定义打分管理类Model，用于自定义打分的相关操作
 */
namespace Test\Model;
class TestAttrMarkModel extends BaseModel{

    /**
     * 分离试题打分属性；
     * @author demo
     */
    public function resetMarkAttr($model='TestAttr',$where='1=1',$limit='0,1000'){
        $testAttr=$this->getModel($model);
        $buffer=$testAttr->selectData('TestID,Mark,DfStyle',$where,'TestID ASC',$limit);
        foreach($buffer as $i=>$iBuffer){
            if($iBuffer['DfStyle']==1){
                continue; //主观打分
            }
            $testID=$iBuffer['TestID'];
            $mark=explode('@',$iBuffer['Mark']);

            $markArray=array();
            $len=count($mark);
            foreach($mark as $j=>$jMark){
                $tmpArray=explode('#',$jMark);
                $tmpArray=array_filter($tmpArray);
                $tmpArray=array_values($tmpArray);
                if(empty($tmpArray)) continue;

                foreach($tmpArray as $kTmpArray){
                    $tmp=explode('|',$kTmpArray);
                    $tmp[]=count($len==1)? $j : $j+1;
                    $markArray[]=$tmp;
                }
            }
            if(empty($markArray)) continue;

            $testMarkBuffer=$this->selectData('*','TestID ='.$testID);
            $j=0;
            $len=count($markArray);
            if($testMarkBuffer){
                $delID=array();
                foreach($testMarkBuffer as $j=>$jTestMarkBuffer){
                    if($j>=$len){
                        $delID[]=$jTestMarkBuffer['TMID'];
                        continue;
                    }

                    $data=array();
                    $data['TestID']=$testID;
                    $data['MarkID']=$markArray[$j][0];
                    $data['Score']=$markArray[$j][1];
                    $data['OrderID']=$markArray[$j][2];
                    $this->updateData($data,'TMID='.$jTestMarkBuffer['TMID']);
                }
                if($delID)
                    $this->deleteData('TMID in('.implode(',',$delID).')');
            }
            if($j<$len){
                $dataAll=array();
                for(;$j<$len;$j++){
                    $data=array();
                    $data['TestID']=$testID;
                    $data['MarkID']=$markArray[$j][0];
                    $data['Score']=$markArray[$j][1];
                    $data['OrderID']=$markArray[$j][2];
                    echo print_r($data);
                    $dataAll[]=$data;
                }
                if($dataAll)
                    $this->addAllData($dataAll);
            }
        }
    }
}
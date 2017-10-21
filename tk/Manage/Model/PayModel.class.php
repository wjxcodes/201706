<?php
/**
 * @author demo
 * @date 15-9-23
 * @支出记录相关操作
 */
/**
 * 支出记录类，用于支出记录的操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class PayModel extends BaseModel{

    /**
     * @覆盖父类方法。
     * @author demo 2015-12-21
     */
    public function insertData($data, $modelName=''){
        $result = parent::insertData($data,$modelName);
        if($result !== false){
            $this->getModel('StatisticsCounter')->increase('moneyTotal', (float)$data['PayMoney']);
        }
        return $result;
    }

    /**
     * 添加支出记录
     * @param array $param Pay表所有字段
     * @return bool
     * @author demo
     */
    public function addPayLog($param){
        return $this->insertData(
            $param
        );
    }

    /**
     * 根据不同学科，获取金额值不一致
     * @param array $testArr 试题ID数组
     * @param int $userID 用户ID
     * @param int $docID 文档ID
     * @param int $subjectID 学科ID
     * @return bool
     * @author demo
     */
    public function addPayBySubject($testArr,$userID,$docID,$subjectID){
        $subject=SS('subject');
           $docFile=$this->getModel('DocFile');
        $docStatus=$docFile->selectData(
            'PayStatus',
            'DocID in('.$docID.')'
        );
        if($docStatus[0]['PayStatus']==0){
            //按题计算
            if(!empty($testArr)){
                //按照试题计算
                if($subject[$subjectID]['MoneyStyle']==0){
                    $testTotal=count($testArr);
                    $payData['PayMoney']=$testTotal*$subject[$subjectID]['PayMoney'];
                }else{
                    $payData['PayMoney']=$subject[$subjectID]['PayMoney'];
                }
                $payData['UserID']=$userID;
                $payData['PayName']='解析试题';
                $payData['AddTime']=time();
                $payData['PayDesc']="教师[".$userID."]解析文档[".$docID."]";
                $ifSuccess=$this->getModel('Pay')->addPayLog($payData);
                if($ifSuccess){
                    $status['PayStatus']=1;
                    $docFile->updateData(
                        $status,
                        'DocID in ('.$docID.')'
                    );
                    return $ifSuccess;
                }
            }
        }
    }
}
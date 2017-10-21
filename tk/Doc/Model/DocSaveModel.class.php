<?php
/**
 * @author demo  
 * @date 2014年10月16日
 * @update 2015年1月14日
 */
/**
 * 试卷存档表Model类，用于处理用户存档相关操作
 */
namespace Doc\Model;
class DocSaveModel extends BaseModel{

    /**
     * 获取指定学提取码的存档信息
     * @param string $code 提取码
     * @return array
     * @author demo
     */
    public function getDataByCode($code){
        $buffer=$this->findData(
            '*',
            'SaveCode="'.$code.'"');
        if(!$buffer){
            return [0,'数据不存在！'];
        }
        return $buffer;
    }
    /**
     * 获取指定存档数据的试卷信息
     * @param array $buffer 试卷存档信息
     * @return array
     * @author demo
     */
    public function paperContentByCode($testList){

        //获取试题数据
        $field=array('testid','testnormal','answernormal','analyticnormal','remarknormal','subjectid','typesid','klid','chapterid','xtnum','ifchoose','diff','teststyle','mark','optionwidth','optionnum','judge');
        $where=array('TestID'=>$testList);
        $order=array();
        $page=array('page'=>1,'perpage'=>100,'limit'=>100);

        $tmpStr=$this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,0,2);
        if(empty($tmpStr)){
             return [0,'试题数据不存在！'];
        }

        //调整试题的顺序
        $testArray=explode(',',$testList);
        $output=array();
        foreach($testArray as $i=>$iTestArray){
            $output[$iTestArray]=$tmpStr[$iTestArray];
        }

        return [1,$output];
    }


    /**
     * 更新存档里的cookie里的存档id，和提取码
     * @param int $saveID 存档id
     * @param int $oldSaveID cookie里的旧存档id
     * @param string $cookieStr 存档cookie字符串
     * @return null
     * @author demo
     */
    public function saveCodeAndID($saveID,$oldSaveID,$cookieStr){
        $data=array();
        $data['SaveCode']=md5($saveID.C('REG_KEY'));
        $data['SaveCode']=$saveID.substr($data['SaveCode'],0,3).substr($data['SaveCode'],-2);

        //为cookie更换saveid
        if(!is_numeric($oldSaveID)) $oldSaveID=0;
        $data['CookieStr']=str_replace('@#@attr@$@'.$oldSaveID,'@#@attr@$@'.$saveID,$cookieStr);
        $this->updateData($data,'SaveID='.$saveID);
    }

    /**
     * 生成考试试题信息；
     * @param string $saveID 存档id
     * @return array
     * @author demo 
     */
    public function createTest($saveID){
        $buffer=$this->selectData(
            '*',
            'SaveID='.$saveID);
        $arr=R('Common/DocLayer/formatPaperCookie',array($buffer[0]['CookieStr']));
        $result = array();
        $num = 0;
        foreach($arr['parthead'] as $i=>$iArr){
            foreach($iArr['questypehead'] as $j=>$jArr){
                if($jArr[5]){
                    foreach($jArr[5] as $k=>$kArr){
                        $data = array();
                        $data['AddTime'] = time();
                        $optionNum = $this->getModel('TestAttrReal')->selectData(
                            'OptionNum,TestStyle',
                            'TestID='.$kArr[0]);
                        if($optionNum[0]['OptionNum']=='0' || $optionNum[0]['TestStyle']=='3'){
                            $data['ChooseType'] = 0;
                            $data['Type'] = 2;
                        }else{
                            $answer = $this->getModel('TestReal')->selectData(
                                'Answer',
                                'TestID='.$kArr[0]);
                            $data['Type'] = 1;
                        }
                        $data['ChooseNo'] = $kArr[4];
                        $data['TestID'] = $kArr[0];
                        $data['SaveID'] = $saveID;
                        if(!$kArr[3]){
                            $data['ChooseNum'] = 0;
                        }else{
                            $data['ChooseNum'] = $kArr[3];
                        }
                        if($kArr[1]>1 && $data['ChooseType'] != '0'){   //选择题有小题
                            for($l=0;$l<$kArr[1];$l++){
                                $data['MixedNo'] = $l+1;
                                $data['Score'] = $kArr[2][$l];
                                $answerArr = array();
                                $answerArr = explode('【小题】',$answer[0]['Answer']);
                                $smallTest = explode(',',$optionNum[0]['OptionNum']);
                                if($smallTest[$l]==0){     //复合题小题带选择题
                                    $data['ChooseType'] = 0;
                                }else{
                                    $data['ChooseType'] = $smallTest[$l].','.(strlen(preg_replace('/[\<\/p\>|\<i\>|\<\/i\>|\s+]/','',$answerArr[$l+1])));
                                }
                                $result[$num] = $data;
                                $num++;
                            }
                        }else if($kArr[1]==1 && $data['ChooseType'] != '0'){ //选择题无小题
                            $data['MixedNo'] = 0;
                            $data['Score'] = $kArr[2];
                            $answer = strlen(preg_replace('/[\<\/p\>|\<i\>|\<\/i\>|\s+]/','',$answer[0]['Answer']));
                            $data['ChooseType'] = $optionNum[0]['OptionNum'].','.$answer;
                            $result[$num] = $data;
                            $num++;
                        }else if($kArr[1]==1 && $data['ChooseType'] == '0'){  //非选择题无小题
                            $data['MixedNo'] = 0;
                            $data['Score'] = $kArr[2];
                            $result[$num] = $data;
                            $num++;
                        }else{     //非选择题有小题
                            for($l=0;$l<$kArr[1];$l++){
                                $data['MixedNo'] = $l+1;
                                $data['Score'] = $kArr[2][$l];
                                $result[$num] = $data;
                                $num++;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
}

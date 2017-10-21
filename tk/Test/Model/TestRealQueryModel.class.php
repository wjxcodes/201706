<?php
/**
 * 混合试题查询model
 * @author demo
 * @date 2015-7-28
 */
namespace Test\Model;
class TestRealQueryModel extends BaseModel{
    public $sysPre = '0'; //查询区分字符系统
    public $myPre = 'c'; //查询区分字符个人

    /**
     * 根据试题id获取下载数据
     * @param array $field 字段
     * @param array $where 条件
     * @param int $convert 是否转换结构数组(转化为试题id为索引的数组)  0 不转换 , 1 转换 , 2 转换并合并
     * @return
     ############$convert=0时###############
     *      array(
     *           $sysPre=>array(0=>array(
     *                              0=>array(),
     *                              1=>array(),
     *                            }
     *                      ),
     *          $myPre=>array(0=>array(
     *                              0=>array(),
     *                              1=>array(),
     *                            )
     *                       )
     *          );
    ##############$convert=1时#################
     *      array(
     *               $sysPre=>array(
     *                              试题id=>array(),
     *                              试题id=>array(),
     *                      ),
     *               $myPre=>array(
     *                              c试题id=>array(),
     *                              c试题id=>array(),
     *                       )
     *          );
    ##############$convert=2时#################
     *      array(
     *              试题id=>array(),
     *              试题id=>array(),
     *              c试题id=>array(),
     *              c试题id=>array(),
     *          );
     * @author demo
     */
    public function getDownTest($field,$where,$convert=0){
        //字段转换
        $filter=array('TestID'); //对多个表相同字段进行前缀匹配
        $filter2=array('Test','Analytic','Answer', 'Remark'); //对不同的字段名称表述进行相应替换
        $newField=array(); //存储需要查询的字段名
        $newChangeField=array(); //存储需要转换字符串的字段例如DocTest

        foreach($field as $i=>$iField){
            if(in_array($iField,$filter)) $newField[$i]='tar.'.$iField;
            else if(in_array($iField,$filter2)){
                $newChangeField[]='Doc'.$iField;
                $newField[$i]='Doc'.$iField;
            }
            else $newField[$i]=$iField;
        }

        //系统试题需要格式化，下面是必须的参数，如果没有这些参数会自动加入
        $filter3=array('OptionWidth','OptionNum','TestNum','IfChoose','Duplicate');
        foreach($filter3 as $iFilter3){
            if(!in_array($iFilter3,$field)){
                $newField[]=$iFilter3;
            }
        }

        //是否附加复合题数据
        $judge=$this->ifJudge($newField);
        if($judge===0){
            $judge=0;
        }else if(is_array($judge)){
            $newField=$judge;
            $judge=1;
        }

        //处理where[testid]
        if($where['TestID']){
            $testIDArr=R('Common/TestLayer/cutIDStrByChar',array($where['TestID'],1)); //切割字母开头的字符串id为数组;
        }

        $newWhere=$this->getTestDownWhere($where,$testIDArr[$this->sysPre],$filter);

        if(!empty($newWhere)){
            //获取系统试题
            $result=$this->unionSelect('testDocRealForDown',$newField,$newWhere);

            //处理系统试题重题
            if($result){
                $testIDTmp=array();//重复试题id
                foreach($result as $iResult){
                    if($iResult['Duplicate']) $testIDTmp[]=$iResult['Duplicate'];
                }
                if($testIDTmp){
                    $newWhere=$this->getTestDownWhere($where,$testIDTmp,$filter);
                    $resultDuplicate=$this->unionSelect('testDocRealForDown',$newField,$newWhere);
                    $testIDArrayTmp=array(); //存储以id为键值的数据
                    if($resultDuplicate){
                        foreach($resultDuplicate as $iResultDuplicate){
                            $testIDArrayTmp[$iResultDuplicate['TestID']]=$iResultDuplicate;
                        }
                        $test=$this->getModel('Test');
                        foreach($result as $i=>$iResult){
                            if($iResult['Duplicate']){
                                //对部分数据进行替换
                                $result[$i]=$test->changeArrayValue($iResult,$testIDArrayTmp[$iResult['Duplicate']],0);
                            }
                        }
                    }
                }
                //对试题进行处理
                $testArray=R('Common/TestLayer/changeUrlByField',array($result,$newChangeField));

                //大写字母转小写字母
                $testArray=formatString('upperToLowerForArray',$testArray);

                //附加系统judge数据
                if($judge==1){
                    $testID=$this->getTestID($testArray,'testid');

                    //获取judge数据 以试题id为键值
                    $result=$this->getJudgeData($testID);
                    $result=formatString('upperToLowerForArray',$result,3);

                    $testArray=$this->replaceJudge($testArray,$result); //合并judge数据
                }
            }
        }

        //获取个人试题
        $newField=array();
        foreach($field as $i=>$iField){
            if(in_array($iField,$filter2)) $newField[$i]=strtolower($iField).'old';
            else $newField[$i]=strtolower($iField);
        }

        //系统试题需要格式化，下面是必须的参数，如果没有这些参数会自动加入
        $filter3=array('OptionWidth','OptionNum','TestNum','IfChoose','Duplicate');
        foreach($filter3 as $iFilter3){
            if(!in_array($iFilter3,$field)){
                $newField[]=strtolower($iFilter3);
            }
        }

        //处理试题id
        $newWhere=$where;
        if($where['TestID'] && $testIDArr[$this->myPre]){
            $newWhere['TestID']=implode(',',$testIDArr[$this->myPre]); //切割字母开头的字符串id为数组;
        }else if($where['TestID']){
            unset($newWhere['TestID']);
        }

        if(!empty($newWhere)){
            $page=array('prepage'=>100);
            $order=array();
            $customArray = $this->getIndexTest($newField,$newWhere,$order,$page,2,1);
            $customArray = $customArray[$this->myPre];

            if($customArray){
                //转换字段名称
                foreach($customArray as $i=>$iCustomArray){
                    foreach($filter2 as $jFilter2){
                        $tmpField=strtolower($jFilter2);
                        if($customArray[$i][$tmpField.'old']){
                            $customArray[$i]['doc'.$tmpField]=$customArray[$i][$tmpField.'old'];
                            unset($customArray[$i][$tmpField.'old']);
                        }
                    }
                }

                //附加个人judge数据
                if($judge){
                    $testID=implode(',',$testIDArr[$this->myPre]);

                    //获取judge数据 以试题id为键值
                    $result=$this->getJudgeData($testID,1);
                    $result=formatString('upperToLowerForArray',$result,3);

                    $customArray=$this->replaceJudge($customArray,$result); //合并judge数据
                }
            }
        }
        $list=array($this->sysPre=>$testArray,$this->myPre=>$customArray);

        if($convert==-1){
            $list=$this->mergeArray($list); //合并
        }else if($convert!=0){
            $list=$this->changeArray($list); //转换testid为键

            if($convert==2){
                $list=$this->mergeArray($list); //合并
            }
        }
        return $list;
    }

    /**
     * 替换judge数据
     * @param array $testArray 试题数据集
     * @param array $judgeArray 试题小题属性数据集以试题id为键值
     * @return array
     * @author demo
     */
    private function replaceJudge($testArray,$judgeArray){

        foreach($testArray as $i=>$iTestArray){
            if($testArray[$i]['duplicate'] && $judgeArray[$iTestArray['duplicate']])
                $testArray[$i]['judge']=$judgeArray[$iTestArray['duplicate']];
            else if($judgeArray[$iTestArray['testid']])
                $testArray[$i]['judge']=$judgeArray[$iTestArray['testid']];
        }

        return $testArray;
    }
    /**
     * 获取试题下载时的查询条件
     * @param array $where 查询条件
     * @param array $testIDArray 试题id数组
     * @param array $filter 过滤条件
     * @return array
     * @author demo
     */
    private function getTestDownWhere($where,$testIDArray,$filter){
        $newWhere=array();
        foreach($where as $i=>$iWhere){

            //处理where中的
            if($testIDArray && $i=='TestID'){
                $iWhere=implode(',',$testIDArray);
            }else{
                continue;
            }

            if(is_string($iWhere) && strpos($iWhere,',')!==false){
                $iWhere=array('IN',explode(',',$iWhere));
            }else if(is_array($iWhere)){
                $iWhere=array('IN',$iWhere);
            }

            if(in_array($i,$filter)){
                $newWhere['tar.'.$i]=$iWhere;
            }else{
                $newWhere[$i]=$iWhere;
            }
        }
        return $newWhere;
    }

    /**
     * 根据参数获取索引数据 参数同查询索引
     * @param string $field 字段  如judge表数据请传递judge字段 返回数据为与TestID并列的字段['Judge'][]
     * @param array $where 查询条件
     * @param array $order 排序信息
     * @param array $page 分页信息
     * @param int $auto 0查询全部数据  1查询系统 2查询个人
     * @param int $convert 是否转换结构数组(转化为试题id为索引的数组)  -1仅合并 0不转换不合并 , 1 转换不合并 , 2 转换并合并
     * @return array
     * @author demo 
     */
    public function getIndexTest($field,$where,$order,$page,$auto=0,$convert=0){
        $ifTestOnly=0;

        if($where['TestID']){
            $testIDArr=R('Common/TestLayer/cutIDStrByChar',array($where['TestID'])); //切割字母开头的字符串id为数组;
            $ifTestOnly=1;
        }

        //是否附加复合题数据
        $judge=$this->ifJudge($field);
        if($judge===0){
            $judge=0;
        }else if(is_array($judge)){
            $field=$judge;
            $judge=1;
        }

        $list=array(); //结果集

        //获取系统试题
        if($auto!=2){
            $newWhere=$where;
            if($testIDArr[$this->sysPre]){
                $newWhere['TestID']=implode(',',$testIDArr[$this->sysPre]);
            }else{
                unset($newWhere['TestID']);
            }
            if(!($ifTestOnly==1 && !$newWhere['TestID'])){
                $pubModel = $this->getModel('TestReal');

                if($newWhere){
                    $list[$this->sysPre] = $pubModel->getTestIndex($field,$newWhere,$order,$page);
                }
                //附加复合题数据
                if($judge==1 && $list[$this->sysPre][0]){
                    $testID=$this->getTestID($list[$this->sysPre][0],'testid');

                    //获取judge数据 以试题id为键值
                    $result=$this->getJudgeData($testID);

                    $list[$this->sysPre][0]=$this->replaceJudge($list[$this->sysPre][0],$result); //合并judge数据
                }
            }
        }

        //获取个人试题
        if($auto!=1){unset($where['AatTestStyle']);
            $newWhere=$where;
            if($testIDArr[$this->myPre]){
                $newWhere['TestID']=implode(',',$testIDArr[$this->myPre]);
            }else{
                unset($newWhere['TestID']);
            }


            if(!($ifTestOnly==1 && !$newWhere['TestID'])){
                if($newWhere){
                    $list[$this->myPre] = $this->getModel('CustomTest')->getIndex($field,$newWhere,$order,$page);
                }

                //附加复合题数据
                if($judge==1 && $list[$this->myPre][0]){

                    $testID=$newWhere['TestID'];

                    //获取judge数据 以试题id为键值 ？？？？是否要为试题添加前缀c
                    $result=$this->getJudgeData($testID,1);
                    $list[$this->myPre][0]=$this->replaceJudge($list[$this->myPre][0],$result); //合并judge数据
                }
            }
        }

        $list[$this->sysPre]=$list[$this->sysPre][0];
        $list[$this->myPre]=$list[$this->myPre][0];

        if($convert==-1){
            $list=$this->mergeArray($list); //合并
        }else if($convert!=0){
            $list=$this->changeArray($list); //转换testid为键

            if($convert==2){
                $list=$this->mergeArray($list); //合并
            }
        }

        return $list;
    }

    /**
     * 根据参数获取数据表内数据
     * @param $field
     * @param $where
     * @param $order
     * @param $page
     * @author demo
     */
    public function getDataTest($field,$where,$order,$page){

    }


    /**
     * 根据查询到的数据返回相应字段的字符串连接 【如果是试题id则获取对应重复id的数据】
     * @param array $array 试题数据集
     * @param array $field 字段名
     * @return string
     * @author demo
     */
    private function getTestID($array,$field='testid'){
        $output=array();
        foreach($array as $iArray){
            if($field=='testid' && $iArray['duplicate']) $output[]=$iArray['duplicate'];
            else $output[]=$iArray[$field];
        }
        return implode(',',$output);
    }

    /**
     * 转换查询结果以试题id为索引
     * @param array $array 数据集
     * @return array
     * @author demo
     */
    private function changeArray($array){
        $newList = array();
        if($array[$this->sysPre]){
            $newList[$this->sysPre] = R('Common/TestLayer/reloadTestArr',array($array[$this->sysPre],'testid','',0));
        }

        if($array[$this->myPre]){
            $newList[$this->myPre] =  R('Common/TestLayer/reloadTestArr',array($array[$this->myPre],'testid','',0));
        }

        return $newList;
    }
    /**
     * 合并查询结果
     * @param array $array 数据集
     * @return array
     * @author demo
     */
    private function mergeArray($array){
        $newList = array();
        $merge=0; //0使用array_merge 1使用+    合并
        if($array[$this->sysPre]){
            foreach($array[$this->sysPre] as $i=>$iArray){
                if($i!=0) $merge=1;
                break;
            }
        }
        if($merge==1){
            $newList=(array)$array[$this->sysPre]+(array)$array[$this->myPre];
        }else{
            $newList=array_merge((array)$array[$this->sysPre],(array)$array[$this->myPre]);
        }

        return $newList;
    }

    /**
     * 是否需要获取judge表数据
     * @param array $field 字段数组
     * @return array|int
     * @author demo
     */
    private function ifJudge($field){
        if(in_array('judge',$field)){
            unset($field[array_search('judge',$field)]);
            if(empty($field)) $field=array('TestID');
            return $field;
        }
        return 0;
    }

    /**
     * 根据试题id获取judge数据 以试题id为键值
     * @param string $testID 试题id
     * @param string $ifCustom 是否是个人试题 0系统试题 1个人试题
     * @return array
     * @author demo
     */
    private function getJudgeData($testID,$ifCustom=0){
        $pre='';
        if($ifCustom==0){
            $result = $this->getModel('TestJudge')->selectData(
                'JudgeID, TestID, OrderID, IfChoose',
                'TestID IN ('.$testID.')',
                'OrderID ASC'
            );
        }else{
            $pre=$this->myPre;
            $thisTestID=str_replace($this->myPre,'',$testID);
            $result = $this->getModel('CustomTestJudge')->selectData(
                'JudgeID, TestID, OrderID, IfChoose',
                'TestID IN ('.$thisTestID.')',
                'OrderID ASC'
            );
        }

        if(empty($result)) return ;
        $result=R('Common/TestLayer/reloadTestArr',array($result,'TestID',$pre,1));
        return $result;
    }

    /**
     * 根据试题id验证试题哪些题型超出限制
     * @param string|array $testID 试题id
     * @return array
     * @author demo
     */
    public function checkTypeLimit($testID){
        $field=array('testid','typesid','testnum','duplicate');
        $where=array('TestID'=>$testID);
        $order=array('TestID ASC');
        $page=array('perpage'=>100);
        $buffer=$this->getIndexTest($field,$where,$order,$page,0,2);

        //统计试题数量
        $data = array();
        if($buffer){
            foreach($buffer as $iBuffer){
                $typesID = $iBuffer['typesid'];
                if(!isset($data[$typesID]['TestNum'])){
                    $data[$typesID]['TestNum'] = 0;
                }
                if($iBuffer['testnum']<1){
                    $iBuffer['testnum']=1;
                }
                $data[$typesID]['TestNum']+=$iBuffer['testnum'];
            }
        }

        //返回试题数量情况
        $result = array();
        $typesArray = SS('types');
        foreach($data as $i=>$iData){
            if($iData['TestNum'] > $typesArray[$i]['Num']){
                $result[] = array(
                    'TypesName'=>$typesArray[$i]['TypesName'],
                    'TestNum'=>$iData['TestNum'],
                    'LimitNum'=>$typesArray[$i]['Num']);
            }
        }
        return $result;
    }

    /**
     * 验证并记录试题id是否超出限制
     * @param string|array $testList 试题id
     * @return 未超出返回true 超出返回$data=array(0=>'false','msg'=>'30738','replace'=>超出数据信息);
     * @author demo
     */
    public function checkAndLogTypeLimit($testList){
        $limitTest=$this->checkTypeLimit($testList);

        $testArray=explode(',',$testList);
        if(!empty($limitTest)){

            $outputLimit=''; //错误内容
            foreach($limitTest as $iLimitTest){
                $outputLimit='【'.$iLimitTest['TypesName'].'('.$iLimitTest['TestNum'].'/'.$iLimitTest['LimitNum'].')】';
            }

            $data=array(
                0=>'false',
                'msg'=>'30738',
                'replace'=>$outputLimit
            );

            $errorData=array(
                'msg'=>'试题超出最大限制',
                'sql'=>'空',
                'description'=>'试题超出最大限制,详情：'.$outputLimit,
            );

            $this->addErrorLog($errorData);
            return $data; //您选择的试题超出最大限制！

        }
        return true;
    }


    /**
     * 根据试题id获取试题数据集
     * @param string|array $testList 试题id
     * @return 存在返回数据集 不存在返回$data=array(0=>'false','msg'=>'30703');
     * @author demo
     */
    public function getTestArrayByID($testList){
        $testArray=explode(',',$testList);

        $field=array('TestID','Test','Analytic','Answer', 'Remark','judge');
        $where=array('TestID'=>$testArray);
        $data = $this->getDownTest($field,$where,2);

        if(empty($data)){

            $data=array(
                0=>'false',
                'msg'=>'30703'
            );

            $errorData=[
                'msg'=>'您选择的试题已不存在！试题ID:'.$testList,
                'sql'=>'空',
                'description'=>'试题ID缺失！下载失败！'
            ];
            $this->addErrorLog($errorData);
            return $data; //您选择的试题已不存在！请选择试题后组卷！
        }

        //增加错误试题判断 参数数量跟结果数量不一致时，记录错误记录
        if(count($testArray)>count($data)){
            $errorTestID=array();
            foreach($testArray as $i=>$iTestArray){
                if(empty($data[$iTestArray])){
                    $errorTestID[]=$iTestArray;
                }
            }
            $errorTest=implode(',',$errorTestID);

            if(!empty($errorTest)){
                $errorData=array(
                    'msg'=>'试题ID为：'.$errorTest.',的试题，不存在！请管理员查看',
                    'sql'=>'空',
                    'description'=>'部分试题出现不存在问题！'
                );
                $this->addErrorLog($errorData);
            }
        }
        return $data;
    }
}
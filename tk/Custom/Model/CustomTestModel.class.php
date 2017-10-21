<?php
/**
 * 自建题库Model类
 * @author demo
 * @update  添加原创关联试题
 * @date 2015-1-6
 */
namespace Custom\Model;
use Common\Model\BaseModel;
class CustomTestModel extends BaseModel{
    protected $modelName = 'CustomTest';

    private $lastId = 0; //新增数据，产生的主键id


    public function getLastId(){
        return $this->lastId;
    }

    /**
     * 数据保存
     * @param array $data 保存的数据内容
     *           key               value
     *           Test              '<p mode="mark">test</p>'
     *           Analytic          '<p mode="mark">teste</p>''
     *           Answer            '<p mode="mark">test</p>''
     *           Diff              0.601
     *           GradeID           2
     *           ChapterID         array(131,301)
     *           KlID              array(131,1849)
     *           Remark            '1111111111111'
     *           Source            '111111111'
     *           SubjectID         13
     *           TestID            空|2
     *           TypesID           79
     *           UserID            11
     *           act               edit
     *           attributes        array('IfChoose'=>3, 'OptionNum'=>0)
     *           verifyCode        '438ef0c3f3eeb9e748034b27bafed001'
     * @param object $attrModel 属性model
     * @param object $knowledgeModel 知识点model
     * @param object $chapterModel 章节model
     * @param object $judgeModel 小题model
     * @return boolean 成功返回true
     */
    public function saveData($data, $attrModel, $knowledgeModel, $chapterModel, $judgeModel){
        //原创模板试题添加
        $ttid = (int)$data['TTID'];
        //获取试题信息
        $test['Test'] = formatString('IPReplace',$data['Test']);
        $test['Answer'] = formatString('IPReplace',$data['Answer']);
        $test['Analytic'] = formatString('IPReplace',$data['Analytic']);
        $test['Source'] = (String)$data['Source'];
        $test['Remark'] = (String)$data['Remark'];
        unset($data['Test'], $data['Answer'], $data['Analytic'], $data['Source'], $data['Remark']);
        //提取出复合题内容
        $attributes = $data['attributes'];
        $complex = $attributes['complex'];
        $style = array();
        foreach($complex as $k=>$v){
            $style[] = $v['type'];  //获取小题题型
        }
        unset($data['complex'], $data['TTID']);
        $isSuccess = true;
        //处理试题
        $testid = 0;
        $originalityRTID = 0;//是否原创协同命制 0 不是
        if('add' == $data['act']){
            ///---------------
            $testid = $this->insertData($test);
            if($testid === false){
                $isSuccess = false;
            }else{
                $this->lastId = $testid;
                if($ttid != 0){
                    //cookie判断当前用户是否添加原创试题
                    $rtmodel = $this->getModel('OriginalityRelateTest');
                    $rtdata['UserID'] = (int)$data['UserID'];//用户ID
                    $rtdata['TTID'] = $ttid;//模板试题ID
                    $rtdata['TestID'] = (int)$testid;//试题ID
                    $err = $rtmodel->insert($rtdata);
                    if($err !== false){//设置错误信息
                        $originalityRTID = $rtmodel->getId();//是否原创协同命制  关联试题ID
                    }
                    unset($rtmodel,$err);
                }
            }
        }else{
            $testid = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $data['TestID']);
            $test['TestID'] = $testid;
            $isSuccess = $this->updateData($test);
        }
        if($isSuccess === false){
            return $isSuccess;
        }
        $test = $test['Test'];
        //获取试题属性
        $complexSize = count($complex);
        //提取选项数量
        $optionWidths = $this->getModel('Test')->getOptionWidth($test);
        $attr=array();
        foreach($optionWidths as $value){
            $attr['OptionWidth'] .= $value[1].',';
        }
        $attr['OptionWidth'] = rtrim($attr['OptionWidth'], ',');
        $attr['TestID'] = $testid;
        $attr['UserID'] = $data['UserID'];
        $attr['SubjectID'] = $data['SubjectID'];
        $attr['TypesID'] = $data['TypesID'];
        $attr['Diff'] = $data['Diff'];
        $attr['IfChoose'] = $attributes['IfChoose'];
        $attr['OptionNum'] = $attributes['OptionNum'];
        $attr['TestNum'] = $complexSize;
        $attr['GradeID'] = $data['GradeID'];
        $attr['IsTpl'] = $originalityRTID;
        //计算试题包含小题的类型(styles)
        $styleSize = count($style);
        if(in_array(0,$style) && $styleSize > 1){
            $attr['TestStyle'] = 2;
        }else if($styleSize > 1 || $styleSize == 0){
            $attr['TestStyle'] = 3;
        }else{
            $attr['TestStyle'] = 1;
        }
        //处理试题属性
        if('add' == $data['act']){
            $isSuccess = $attrModel->insertData($attr);
        }else{
            $isSuccess = $attrModel->updateData($testid, $attr);
        }
        unset($data['UserID'], $data['SubjectID'], $data['TypesID'], $data['Diff'], $style, $styleSize,$attr);

        if($isSuccess === false){
            return $isSuccess;
        }
        //处理知识点
        $knowledge = $data['KlID'];
        if(empty($knowledge)){ //如果没有传递知识点，则清空该试题关联知识点
            $this->getModel('CustomTestKnowledge')->deleteData('TestID in ('.$testid.')');
        }else{
            $result = $knowledgeModel->saveData(array('testid'=>$testid,'knowledge'=>$knowledge));
        }
        unset($knowledge,$data['KlID']);

        //处理章节
        $chapter = $data['ChapterID'];
        if(empty($chapter)){ //如果没有传递章节，则清空该试题关联章节
            $testid = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testid);
            $this->getModel('CustomTestChapter')->deleteData('TestID in ('.$testid.')');
        }else{
            $result = $chapterModel->saveData(array('testid'=>$testid,'chapter'=>$chapter));
        }
        unset($chapter, $data['ChapterID']);

        //处理技能
        $skill = $data['SkillID'];
        $skillM = D('CustomTestSkill');
        if(empty($skill)){ //如果没有传递技能，则清空该试题关联技能   
            $testid = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testid);
            $skillM->deleteData('TestID in ('.$testid.')');
        }else{
            $result = $skillM->saveData(array('testid'=>$testid,'skill'=>$skill));
        }
        unset($skill, $data['SkillID']);

        //处理能力
        $capacity = $data['CapacityID'];
        $capacityM = D('CustomTestCapacity');
        if(empty($capacity)){ //如果没有传递能力，则清空该试题关联能力   
            $testid = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testid);
            $capacityM->deleteData('TestID in ('.$testid.')');
        }else{
            $result = $capacityM->saveData(array('testid'=>$testid,'capacity'=>$capacity));
        }
        unset($capacity, $data['CapacityID']);

        //处理小题内容
        $result = $judgeModel->saveData($testid,$complex);
        return true;
    }
    /**
     * 插入数据；
     * @param array $data 插入数据表字段数组
     * @return maxed 插入数据后的自增主键 或 false
     * @author demo
     */
    public function insertData($data){
        $id = parent::insertData(
            $data
        );
        if(empty($id)){
            return false;
        }
        $result = $this->getModel('CustomTestTaskStatus')->insertData(array(
            'TestID' => $id
        ));
        if(empty($result)){
            return false;
        }
        return $id;
    }

    /**
     * 更新数据；
     * @param array $data 更新数据表字段数组
     * @return bool
     * @author demo
     */
    public function updateData($data){
        $data['TestID'] = ltrim($data['TestID'], \Test\Model\TestQueryModel::DIVISION);
        $id = (int)$data['TestID'];
        unset($data['TestID']);
        $this->repeatPathCheck($id, $data);
        $cta = $this->getModel('CustomTestAttr');
        $result = $cta->getTestAttributes($id);
        if(-1 == $result['Status']){
            $current = time();
            $cta->updateData($id,array('LastUpdateTime'=>$current));
            $taskExpiresTime = time() + (int)C('WLN_TASK_TIMEOUT');
            $sql = "Status=0,TaskTime={$taskExpiresTime},EditTimes = EditTimes+1, BackTimes=BackTimes+1,IfDel=0,ErrorMsg='',DocPath='',IfLock=0";
            return $this->getModel('CustomTestTaskStatus')->conAddData($sql, 'TestID=1');
        }
        return parent::updateData(
            $data,
            'TestID='.$id
        );
    }

    /**
     * 删除数据
     * @param string $id 要删除的id字符串以英文逗号间隔
     * @return boolean
     * @author demo
     */
    public function deleteData($id){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        $attrModel = $this->getModel('CustomTestAttr');
        //此处删除需进行判断  试题属性表状态为1时 修改试题表status状态为-2 隐藏试题
        $result = $attrModel->getTestAttributes($id);
        $result = $result[0];
        $indexModel = $this->getModel('Index');
        $indexModel->initCustomTest();
        if(1 == $result['Status']){
            $indexModel->deleteIndex($id, array(-2));
            return $attrModel->updateData($id, array('Status' => -2));
        }
        //(Status == -1 || Status == 0) && IsTpl > 0 的时候删除关联试题表的数据。
        if(($result['Status'] == -1 || $result['Status'] == 0) && $result['IsTpl'] > 0){
            //删除试题id
            $rtModel = $this->getModel('OriginalityRelateTest');
            $rtModel->deleteData(' TestID = '.$id);
        }
        $this->repeatPathCheck($id);
        $result = $attrModel->deleteData($id);
        if(false === $result){
            return false;
        }
        $result = parent::deleteData(
            'TestID in ('.$id.')');
        if($result === false){
            return false;
        }
        $indexModel->deleteIndex($id);
        $this->getModel('CustomTestKnowledge')->deleteData($id);
        $this->getModel('CustomTestChapter')->deleteData($id);
        $this->getModel('CustomTestJudge')->deleteData($id);
        $this->getModel('CustomTestTaskStatus')->deleteData($id);
        $this->getModel('CustomTestTaskList')->deleteData($id);
        return true;
    }


    /**
     * 查询试卷下载时试题
     * @param string $testId 试题id，多个id逗号分隔
     * @return array
     * @author demo
     */
    public function getDownloadData($testId, $fields=''){
        $result = $this->customTestSelectByTestId($testId, $fields);
        $data = array();
        foreach($result as $value){
            $value['DocTest'] = R('Common/TestLayer/replaceText',array(str_replace('{#$DocHost#}', '' , stripslashes_deep($value['DocTest']))));
            $value['DocAnalytic'] = R('Common/TestLayer/replaceText',array(stripslashes_deep($value['DocAnalytic'])));
            $value['DocAnswer'] = R('Common/TestLayer/replaceText',array(stripslashes_deep($value['DocAnswer'])));
            $value['TestID'] = \Test\Model\TestQueryModel::DIVISION.$value['TestID'];
            $data[$value['TestID']] = $value;
        }
        unset($result);
        return $data;
    }

    /**
     * 查询出指定试题的内容以及附属内容
     * @param int $id 试题id
     * @return array
     * @author demo
     */
    public function getDataById($id){
        $id = ltrim($id, \Test\Model\TestQueryModel::DIVISION);
        $data['basic'] = $this->unionSelect('customTestFindByTestId',$id);
        $where = 'TestID='.$id;
        $data['knowledge'] = $this->getModel('CustomTestKnowledge')->selectData(
            '*',
            $where);
        $data['chapter'] = $this->getModel('CustomTestChapter')->selectData(
            '*',
            $where);
        if($data['basic']['TestNum'] > 0){
           $data['judge'] = $this->getModel('CustomTestJudge')->selectData(
                '*',
                $where,
               'OrderID ASC');
        }
        return $data;
    }

    /**
     * 路径信息检查
     * @param int $testid 试题id
     * @param array $content 查找的内容
     * @return void
     * @author demo
     */
    public function repeatPathCheck($testid,$content=array()){
        $testid = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $testid);
        $content = $content + array(
            'Test'=>'',
            'Answer'=>'',
            'Analytic'=>''
        );
        $data = array();
        $result = $this->selectData(
            'Test,Analytic,Answer',
            'TestID='.$testid);
        $test = $this->getNotFoundPath($result[0]['Test'],$content['Test']);
        if(!empty($test)){
            $data = $test;
        }
        $answer = $this->getNotFoundPath($result[0]['Answer'],$content['Answer']);
        if(!empty($answer)){
            $data = array_merge($data,$answer);
        }
        $analytic = $this->getNotFoundPath($result[0]['Analytic'],$content['Analytic']);
        if(!empty($analytic)){
            $data = array_merge($data,$analytic);
        }
        if(!empty($data)){
            $this->delMarkup($data);
        }
    }

    /**
     * 在$old中查找$new的img src路径信息
     * @param string $old 数据库数据
     * @param string $new 新保存的数据
     * @return array
     * @author demo
     */
    public function getNotFoundPath($old, $new){
        $old = $this->getContentImgSrc($old);
        if(empty($old)){
            return array();
        }
        $new = $this->getContentImgSrc($new);
        //当当前的内容不存在图片时，直接返回$old中的图片路径信息
        if(empty($new)){
            return $old;
        }
        return array_merge(array_diff($old,$new));
    }

    /**
     * 获取指定内容的<img> src信息
     * @param string $content 内容
     * @return array
     * @author demo
     */
    public function getContentImgSrc($content){
        $pattern = '/<img\s.*?src=["|\']\{#\$DocHost#\}(\/?\w{1,}\/(?:\w{1,}\/){1,}\w{1,}\/\w{1,}\.\w{1,4})["|\']\s{1,}/im';
        $content = stripslashes_deep($content);
        preg_match_all($pattern, $content, $matches);
        if(empty($matches[1]))
            return array();
        $result = array();
        foreach($matches[1] as $value){
            $result[] = $value;
        }
        return $result;
    }

    //删除字段时的关系  -1:4294967295  -2:4294967294
    private $statusMapping = array(
        '-1' => 4294967295,
        '-2' => 4294967294
    );

    /**
     * 索引查询 参数参见IndexModel
     * @return array
     * @author demo
     */
    public function getIndex($field, $where, $order, $page){
        $index=$this->getModel('Index');
        $index->initCustomTest();
        return $index->getCustomTestIndex($field,$where,$order,$page);
    }

    /**
     * 前台查询数据列表
     * @param array $where 查询参数
     * @param string $order='' 排序参数
     * @param array $options=array() 其他在操作时需处理的参数
     * @return array
     * @author demo
     */
    // public function getDataList($where, $order='', $options=array()){
    //     $options = $options + array(
    //         'verify' => '', //生成验证码信息
    //         'disposeContent' => true, //将内容转换为正常格式
    //         'limitPage' => 0
    //     );
    //     $condition = '1=1';
    //     //存在TestID时加入查询条件
    //     if(isset($where['TestID'])){
    //         $where['TestID'] = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $where['TestID']);
    //         if(strpos($where['TestID'], ',') >= 0){
    //             $condition .= ' AND `test`.TestID IN('.$where['TestID'].')';
    //         }else{
    //             $condition .= ' AND `test`.TestID='.$where['TestID'];
    //         }
    //         unset($where['TestID']);
    //     }
    //     $page = $where['page'];
    //     unset($where['page']);
    //     //转换难度值，进行查询
    //     if($where['Diff']){
    //         $diff = R('Common/TestLayer/int2Html',array($where['Diff']));
    //         $diff = strstr($diff, '-', true);
    //         $where['Diff'] = $diff;
    //     }
    //     $wlid = (int)$where['KlID'];
    //     unset($where['KlID']);
    //     //处理知识点查询条件
    //     if($wlid){
    //         $list = SS('klList')[$wlid];
    //         $condition .= ' AND ';
    //         if($list){
    //             $condition .= '`knowledge`.KlID IN('.$wlid.','.$list.')';
    //         }else{
    //             $condition .= '`knowledge`.KlID='.$wlid;
    //         }
    //     }
    //     //获取排序规则
    //     $order = $this->getOrderRule($order);
    //     foreach($where as $k=>$v){
    //         $condition .= " AND {$k}='{$v}'";
    //     }
    //     $condition .= ' AND Status > -2';
    //     $result = array();
    //     $limit = '';    //分页
    //     $recordTotal = 0; //总记录数
    //     $total = 0;      //总页数
    //     //如果limitPage大于0，则进行分页操作
    //     if($options['limitPage']){
    //         if($page == 0){
    //             $page = 1;
    //         }
    //         if(!$wlid){
    //             $result = $this->dbConn->customTestFindBy($condition,$order);
    //         }else{
    //             $result = $this->dbConn->customTestFindBy($condition,$order,1);
    //         }
    //         $recordTotal = $result['num'];
    //         $total = ceil(($recordTotal) / $options['limitPage']);
    //         if($total < $page){
    //             $page = $total;
    //         }
    //         $page = $options['limitPage'] * ($page - 1);
    //         $limit = "{$page},{$options['limitPage']}";
    //     }
    //     if(!$wlid){
    //         $result = $this->dbConn->customTestSelectBy($condition,$limit,$order);
    //     }else{
    //         $result = $this->dbConn->customTestSelectBy($condition,$limit,$order,1);
    //     }
    //     $host = C('WLN_DOC_HOST');
    //     $subjectName = '';
    //     $typesCache = '';
    //     foreach($result as $key=>$value){
    //         if($subjectName === ''){
    //             $cacheData = SS('subject');
    //             $cacheData = $cacheData[$value['subjectid']];
    //             $subjectName = $cacheData['SubjectName'];
    //             $typesCache = S$this->getModel(array('style'=>'types','subjectID'=>$value['subjectid']));
    //         }
    //         $star = 1;
    //         $diffName = '容易';
    //         foreach($value as $k=>$v){
    //             if($k == 'test'){
    //                 $result[$key][$k] = formatString('IPReturn',stripslashes_deep($v));
    //             }else if($k == 'firstloadtime'){
    //                 $result[$key][$k] = date('Y-m-d',$v);
    //             }else if($k == 'diff'){
    //                 $val = $v * 1000;
    //                 switch ($val) {
    //                     case $val <= 300:
    //                         $diffName = '困难';
    //                         $star = 5;
    //                         break;
    //                     case $val <= 500 && $val > 300:
    //                         $diffName = '较难';
    //                         $star = 4;
    //                         break;
    //                     case $val <= 600 && $val > 500:
    //                         $diffName = '一般';
    //                         $star = 3;
    //                         break;
    //                     case $val <= 800 && $val > 600:
    //                         $diffName = '较易';
    //                         $star = 2;
    //                         break;
    //                     default:
    //                         break;
    //                 }
    //             }
    //         }
    //         foreach($typesCache as $cache){
    //             if($cache['TypesID'] == $value['typesid']){
    //                 $result[$key]['typesname'] = $cache['TypesName'];
    //                 break;
    //             }
    //         }
    //         $result[$key]['subjectname'] = $subjectName;
    //         $result[$key]['diffstar'] = R('Common/TestLayer/int2Html',array($star));
    //         $result[$key]['diffid'] = $star;
    //         $result[$key]['diffxing'] = R('Common/TestLayer/int2Xing',array($star));
    //         $result[$key]['diffname'] = $diffName;
    //         if($options['verify'] != '')
    //             $result[$key]['verify'] = md5($options['verify'].$where['UserName'].$value['testid']);
    //     }
    //     if($options['disposeContent'])
    //         return array('data'=>$this->dataHandle($result), 'total'=>$total, 'recordTotal'=>$recordTotal);
    //     return array('data'=>$result, 'total'=>$total , 'recordTotal'=>$recordTotal);

    // }

    /**
     * 获取答案解析内容
     * @param array $params 查询参数
     * @return array
     * @author demo
     */
    // public function getOtherData($params,$convert=true){
    //     $params['TestID'] = str_replace(\Test\Model\TestQueryModel::DIVISION, '', $params['TestID']);
    //     $data = $this->dbConn->customTestSelectByUserNameTestId($params['UserName'], $params['TestID']);
    //     $result['analytic'] = $data[0]['analytic'];
    //     if($convert)
    //         $result['analytic'] = $this->convertTagName($result['analytic'], '【小题】');
    //     $result['analytic'] = formatString('IPReturn',stripslashes_deep($result['analytic']));
    //     $result['answer'] = $data[0]['answer'];
    //     if($convert)
    //         $result['answer'] = $this->convertTagName($result['answer'], '【小题】');
    //     $result['answer'] = formatString('IPReturn',stripslashes_deep($result['answer']));
    //     $result['remark'] = $data[0]['remark'];
    //     return $result;
    // }




    /**
     * 返回排序内容
     * @param string $order
     * @return string
     * @author demo
     */
    private function getOrderRule($order){
        if($order == 'def'){
            return 'AddTime DESC';
        }
        if(strpos($order, 'saveTime') !== false){
            if(strpos($order, 'Desc')){
                return 'AddTime DESC';
            }
            return 'AddTime ASC';
        }
        if(strpos($order, 'diff') !== false){
            if(strpos($order, 'Desc')){
                return 'Diff DESC';
            }
            return 'Diff ASC';
        }
    }

    /**
     * 处理查询结果
     * @param array
     * @return array
     * @author demo
     */
    protected function dataHandle($data){
        foreach($data as $key=>$value){
            if($value['testnum'] == 0){
                continue;
            }
            $data[$key]['answer'] = $this->convertTagName($value['answer'],'【小题】');
            $data[$key]['test'] = $this->convertTagName($value['test'],'【小题】');
            $format = "<span style='text-decoration:underline;padding:0px 5px;'>&nbsp;&nbsp;%s&nbsp;&nbsp;</span>";
            $data[$key]['test'] = $this->convertTagName($data[$key]['test'],'【题号】', $format);
        }
        return $data;
    }

    /**
     * 转换试题标签
     * @param string $text 转换的内容
     * @param string $tagName 标签名称
     * @param string $format 自定义格式
     * @return string
     * @author demo
     */
    protected function convertTagName($text, $tagName, $format='  %s. '){
        $result = explode($tagName, $text);
        $size = count($result);
        for($i=1; $i<$size; $i++){
            $str = sprintf($format, $i);
            $result[$i] = $str.$result[$i];
        }
        return implode('', $result);
    }

    /**
     * 写入将删除的图片路径信息
     * @param array $iamges 图片路径信息，索引数组
     * @return boolean
     * @author demo
     */
    protected function delMarkup($images){
        $data=array();
        foreach($images as $key=>$value){
            $value = ('/'.ltrim($value,'/'));
            $data[$key]['FilePath']=$value;
            $data[$key]['DelTimes']=0;
            $data[$key]['AddTime']=time();
            $data[$key]['Style']='Image';
        }
        return $this->getModel('DelFile')->addAllData($data);
    }

    /**
     * 拷贝库内试题到自建试题主表
     * @param int $testID 自建试题id
     * @param int $introTestID 系统试题id
     * @return bool
     * @author demo
     */
    public function copyTestFromPublic($testID,$introTestID){

        $publicData=$this->getPublicTestData($introTestID); //获取系统库内试题数据

        $customData=$this->getTestDataByID($testID); //获取自建题库主表试题数据

        //拷贝数据到试题表
        $thisID=0;
        $data=array();
        if($customData['CustomTest']) $thisID=$testID;
        else $data['TestID']=$testID;

        if($publicData['TestReal']){
            $data=$this->resetFieldToCustomTest($publicData['TestReal'][0]); //重置试题表属性
        }
        $result=$this->copySingleData('CustomTest',$data,$thisID);
        if($result===false) return false;

        //拷贝数据到试题属性表
        $data=array();
        $thisID=0;
        if($customData['CustomTestAttr']) $thisID=$testID;
        else $data['TestID']=$testID;

        if($publicData['TestAttrReal']){
            $data=$this->resetFieldToCustomAttrTest($publicData['TestAttrReal'][0]); //重置试题属性表属性
        }
        $result=$this->copySingleData('CustomTestAttr',$data,$thisID);
        if($result===false) return false;

        //拷贝数据到试题知识点属性表
        $knowledgeInfo=$this->combinationData($publicData['TestKl'],'TklID');
        $result = $this->copyMoreData('CustomTestKnowledge',$knowledgeInfo, $testID);
        if($result===false) return false;

        //拷贝数据到试题章节属性表
        $chapterInfo=$this->combinationData($publicData['TestChapter'],'TCID');
        $result = $this->copyMoreData('CustomTestChapter',$chapterInfo, $testID);
        if($result===false) return false;

        //拷贝数据到试题选项属性表
        if ($customData['CustomTestAttr'][0]['IfChoose'] == 1 || $customData['CustomTestJudge']) {
            //复制小题属性
            $judgeInfo=$this->combinationData($publicData['TestJudge'],'JudgeID');
            $copyResult = $this->copyMoreData('CustomTestJudge',$judgeInfo, $testID);

            if($copyResult===false) return false;
        }

        return true;
    }

    /**
     * 按照自建题库主表模式整理数据 customTest
     * @param array $tmp 试题属性数组
     * @return array
     * @author demo
     */
    private function resetFieldToCustomTest($tmp){
        $data=array();
        $source=$tmp['Source'];
        if(!$source && $tmp['DocID']){
            $docArray=$this->getModel('Doc')->findData(
                'DocName',
                'DocID='.$tmp['DocID']
            );
            $source=$docArray['DocName'];
        }
        $data['Source']=$source;
        $field=array('Test','Analytic','Answer','Remark');
        foreach($field as $iField){
            $data[$iField]=$tmp[$iField];
        };
        return $data;
    }

    /**
     * 按照自建题库主表模式整理数据 customAttrTest
     * @param array $tmp 试题属性数组
     * @return array
     * @author demo
     */
    private function resetFieldToCustomAttrTest($tmp){
        $data=array();
        $data['LastUpdateTime']=time();
        $field=array(
            'TypesID',
            'SpecialID',
            'GradeID',
            'Diff',
            'Mark',
            'DfStyle',
            'IfChoose',
            'TestNum',
            'TestStyle',
            'OptionWidth',
            'OptionNum'
        );
        foreach($field as $iField){
            $data[$iField]=$tmp[$iField];
        };

        return $data;
    }


    /**
     * 获取系统试题表数据
     * @param int $testID 试题id
     * @return array
     * @author demo
     */
    private function getPublicTestData($testID){
        $param=array();
        $where=' TestID='.$testID;
        $param[0]=array(
            'model'=>'TestReal',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[1]=array(
            'model'=>'TestAttrReal',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[2]=array(
            'model'=>'TestKlReal',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[3]=array(
            'model'=>'TestChapterReal',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[4]=array(
            'model'=>'TestJudge',
            'field'=>'*',
            'where'=>$where,
            'order'=>'OrderID ASC'
        );
        return $this->getMoreDataByID($param);
    }

    /**
     * 写入数据到自建试题主表
     * @param array $param 试题属性
     * @param int $testID 自建试题id
     * @return bool
     * @author demo
     */
    public function copyTestFromData($param,$testID=0){
        //拷贝数据到试题表
        $data=$this->resetFieldToCustomTest($param['test']); //重置试题表属性
        $result=$this->copySingleData('CustomTest',$data,$testID);
        if($result===false) return false;
        //拷贝数据到试题属性表
        $data=$this->resetFieldToCustomAttrTest($param['attr']); //重置试题属性表属性

        $result=$this->copySingleData('CustomTestAttr',$data,$testID);
        if($result===false) return false;

        //拷贝数据到试题知识点属性表
        $knowledge = array();
        foreach($param['knowledge'] as $k=>$v){
            $knowledge[$k] = array(
                'TestID' => $testID,
                'KlID' => $v
            );
        }
        $result = $this->copyMoreData('CustomTestKnowledge',$knowledge, $testID);
        if($result===false) return false;

        //拷贝数据到试题章节属性表
        $chapter = array();
        foreach($param['chapter'] as $k=>$v){
            $chapter[$k] = array(
                'TestID' => $testID,
                'ChapterID' => $v
            );
        }
        $result = $this->copyMoreData('CustomTestChapter',$chapter, $testID);
        if($result===false) return false;

        //拷贝数据到试题选项属性表
        if ($param['IfChoose'] == 1 || $param['judge']) {
            if(empty($param['judge'])){
                $param['judge'] = array();
            }
            $judge = array();
            foreach($param['judge'] as $k=>$v){
                $judge[$k] = array(
                    'TestID' => (int)$testID,
                    'OrderID' => (int)$k,
                    'IfChoose' => (int)$v
                );
            }
            $copyResult = $this->copyMoreData('CustomTestJudge', $judge, $testID);
            if($copyResult===false) return false;
        }
        return true;
    }

    /**
     * 通过试题ID获取副表内容，用于检测副表中是否已存在数据
     * @param int $testID 试题ID
     * @return array
     * @author demo
     */
    public function getCopyDataByID($testID){
        $param=array();
        $where=' TestID='.$testID;
        $param[0]=array(
            'model'=>'CustomTestCopy',
            'field'=>'TestID',
            'where'=>$where,
            'order'=>''
        );
        $param[1]=array(
            'model'=>'CustomTestAttrCopy',
            'field'=>'TestID',
            'where'=>$where,
            'order'=>''
        );
        $param[2]=array(
            'model'=>'CustomTestKnowledgeCopy',
            'field'=>'KlID',
            'where'=>$where,
            'order'=>''
        );
        $param[3]=array(
            'model'=>'CustomTestChapterCopy',
            'field'=>'ChapterID',
            'where'=>$where,
            'order'=>''
        );
        $param[4]=array(
            'model'=>'CustomTestJudgeCopy',
            'field'=>'JudgeID',
            'where'=>$where,
            'order'=>'OrderID ASC'
        );
        return $this->getMoreDataByID($param);
    }

    /**
     * 获取主表中的数据
     * @param $testID
     * @return array
     * @author demo
     */
    public function getTestDataByID($testID){
        $param=array();
        $where=' TestID='.$testID;
        $param[0]=array(
            'model'=>'CustomTest',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[1]=array(
            'model'=>'CustomTestAttr',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[2]=array(
            'model'=>'CustomTestKnowledge',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[3]=array(
            'model'=>'CustomTestChapter',
            'field'=>'*',
            'where'=>$where,
            'order'=>''
        );
        $param[4]=array(
            'model'=>'CustomTestJudge',
            'field'=>'*',
            'where'=>$where,
            'order'=>'OrderID ASC'
        );
        return $this->getMoreDataByID($param);
    }

    /**
     * 重置试题内容
     * @param $testID
     * @return bool
     * @author demo
     */
    public function resetTestContent($testID){
        //获取主表数据
        $testInfo=$this->getTestDataByID($testID);

        //检测副表中是否存在数据
        $copyTestInfo=$this->getCopyDataByID($testID);
        //复制试题到副表
        $copyResult=$this->copySingleData('CustomTestCopy',$testInfo['CustomTest'][0],$copyTestInfo['CustomTestCopy'][0]['TestID']);
        if ($copyResult===false) return false; //试题复制成功后继续复制其他内容

        //复制试题属性
        unset($testInfo['CustomTestAttr'][0]['IsTpl']);//兼容原创模板的报错
        $copyResult = $this->copySingleData('CustomTestAttrCopy',$testInfo['CustomTestAttr'][0],$copyTestInfo['CustomTestAttrCopy'][0]['TestID']);
        if($copyResult===false) {
            //删除副表里写入的试题数据
            $deleteData=array('CustomTestCopy');
            $this->deleteMoreData($testID,$deleteData);
            return false;
        }

        if ($testInfo['attr']['IfChoose'] == 1 && $testInfo['judge']) {
            //复制小题属性
            $judgeInfo=$this->combinationData($testInfo['CustomTestJudge'],'JudgeID');
            $copyResult = $this->copyMoreData('CustomTestJudgeCopy',$judgeInfo, $testID);

            if($copyResult===false) {
                //删除副表里写入的数据
                $deleteData=array('CustomTestCopy','CustomTestAttrCopy');
                $this->deleteMoreData($testID,$deleteData);
                return false;
            }
        }

        //复制知识点
        $knowledgeInfo=$this->combinationData($testInfo['CustomTestKnowledge'],'TklID');
        $copyResult = $this->copyMoreData('CustomTestKnowledgeCopy',$knowledgeInfo, $testID);
        if($copyResult===false) {
            //删除副表里写入的数据
            $deleteData=array('CustomTestCopy','CustomTestAttrCopy','CustomTestJudgeCopy');
            $this->deleteMoreData($testID,$deleteData);
            return false;
        }

        //复制章节
        $chapterInfo=$this->combinationData($testInfo['CustomTestChapter'],'TCID');
        $copyResult = $this->copyMoreData('CustomTestChapterCopy',$chapterInfo, $testID);
        if($copyResult===false){
            //删除副表里写入的数据
            $deleteData=array('CustomTestCopy','CustomTestAttrCopy','CustomTestJudgeCopy','CustomTestKnowledgeCopy');
            $this->deleteMoreData($testID,$deleteData);
            return false;
        }

        return true;
    }

    /**
     * 删除多个单表中的数据
     * @param int $id 试题id
     * @param array $model 需要删除的模型数组
     * @return bool
     * @author demo
     */
    private function deleteMoreData($id,$model){
        $where=array('TestID'=>$id);
        foreach($model as $iModel){
            return $this->getModel($iModel)->deleteData(
                $where
            );
        }
    }

    /**
     * 更新单条数据到数据表 针对test attr表
     * @param string $model 模型名称
     * @param array $data 需要写入的数据 单条写入
     * @param int $id 试题id
     * @return bool
     * @author demo
     */
    private function copySingleData($model,$data,$id){
        $copyResult = false; //是否执行成功标识
        if ($id){//是更新还是新增
            $copyResult = $this->getModel($model)->updateData(
                $data,
                'TestID=' . $id
            );
        } else {
            $copyResult = $this->getModel($model)->insertData(
                $data
            );
        }
        return $copyResult;
    }

    /**
     * 更新多条数据到数据表 针对judge knowledge chapter表
     * @param string $model 模型名称
     * @param array $data 需要写入的数据 多条写入
     * @param int $id 试题id
     * @return bool
     * @author demo
     */
    private function copyMoreData($model,$data,$id){
        //删除之前的小题属性
        $this->getModel($model)->deleteData('TestID=' . $id);
        if(empty($data)){
            return '';
        }
        foreach($data as $value){
            $this->getModel($model)->insertData($value);
        }
        return;
        //循环插入数据
        $result = $this->getModel($model)->addAllData(
            $data
        );
    }

    /**
     * 组合数据
     * @param $data
     * @param $key
     * @return mixed
     * @author demo
     */
    public function combinationData($data,$key){
        $res=array();
        if(!empty($data)) {
            //循环数据
            foreach ($data as $i => $iData) {
                unset($iData[$key]);
                $res[] = $iData;
            }
        }
        return $res;
    }

    /**
     * 删除副表数据
     * @param int $testid 试题id
     * @return boolean
     * @date 2015-8-6
     */
    public function delCopyData($testid){
        $result = $this->getModel('CustomTestCopy')->deleteData( 'TestID='.$testid);
        if($result === false){
            return false;
        }
        $result = $this->getModel('CustomTestAttrCopy')->deleteData( 'TestID='.$testid);
        if($result === false){
            return false;
        }
        $result = $this->getModel('CustomTestChapterCopy')->deleteData( 'TestID='.$testid);
        if($result === false){
            return false;
        }
        $result = $this->getModel('CustomTestKnowledgeCopy')->deleteData( 'TestID='.$testid);
        if($result === false){
            return false;
        }
        $result = $this->getModel('CustomTestJudgeCopy')->deleteData( 'TestID='.$testid);
        if($result === false){
            return false;
        }
        return true;
    }

    /**
     * 保存图片试题
     * @param array $testData 试题信息
     * @param array $testAttrData 试题属性数据
     * @param int $ttID TTID
     * @return bool
     * @author demo
     */
    public function saveImgTest($testData,$testAttrData,$ttID){
        //1.保存试题
        $testID = $this->insertData($testData);
        if($testID === false) {
            return false;
        }
        //3.处理知识点
        $klID = $testAttrData['KlID'];
        unset($testAttrData['KlID']);
        (new CustomTestKnowledgeModel())->saveData(['testid'=>$testID,'knowledge'=>$klID]);
        //4.处理章节
        $chapterID = $testAttrData['ChapterID'];
        unset($testAttrData['ChapterID']);
        (new CustomTestChapterModel())->saveData(['testid'=>$testID,'chapter'=>$chapterID]);
        //4.处理原创题
        $originalityRTID = 0;
        if($ttID != 0){
            //cookie判断当前用户是否添加原创试题
            $rtModel = $this->getModel('OriginalityRelateTest');
            $rtData['UserID'] = (int)$testAttrData['UserID'];//用户ID
            $rtData['TTID'] = $ttID;//模板试题ID
            $rtData['TestID'] = $testID;//试题ID
            $rtModel->insert($rtData);
            $originalityRTID = $rtModel->getId();//是否原创协同命制  关联试题ID
        }
        //2.保存试题属性
        $testAttrData['TestID'] = $testID;
        if($originalityRTID){
            $testAttrData['IsTpl'] = $originalityRTID;
        }
        return (new CustomTestAttrModel())->insertData($testAttrData);
    }

    /**
     * 描述：试题投稿为原创题
     * @param int $userID 用户ID
     * @param int $ttID TTID
     * @param int $testID 试题ID
     * @return bool
     * @author demo
     */
    public function setTestToOriginality($userID,$ttID,$testID){
        //1.处理原创题
        $rtModel = $this->getModel('OriginalityRelateTest');
        $rtData['UserID'] = (int)$userID;//用户ID
        $rtData['TTID'] = $ttID;//模板试题ID
        $rtData['TestID'] = $testID;//试题ID
        $rtModel->insert($rtData);
        $originalityRTID = $rtModel->getId();//是否原创协同命制  关联试题ID
        //2.保存试题属性
        if($originalityRTID){
            $testAttrData['TestID'] = $testID;
            $testAttrData['IsTpl'] = $originalityRTID;
            return (new CustomTestAttrModel())->updateData($testID, $testAttrData);
        }else{
            return false;
        }
    }

    /**
     * 描述：检测是否已经投稿试题
     * @param int $userID 用户ID
     * @param int $ttID TTID
     * @param int $testID 试题ID
     * @return bool 已经投稿返回true，没有投稿返回false
     * @author demo
     */
    public function checkIfSetOriginality($userID,$ttID,$testID){
        $rtModel = $this->getModel('OriginalityRelateTest');
        $ifDb = $rtModel->selectData('RTID',['UserID'=>$userID,'TTID'=>$ttID,'TestID'=>$testID]);
        return $ifDb?true:false;
    }

    /**
     * apidb
     * @author demo
     */
    public function customTestSelectCount($where){
        return $this->unionSelect('customTestSelectCount',$where);
    }

    public function customTestSelectByPageList($where,$page,$doc=0){
        return $this->unionSelect('customTestSelectByPageList', $where,$page,$doc);
    }

    public function customTestSelectByTestId($testID, $fields=''){
        return $this->unionSelect('customTestSelectByTestId', $testID, $fields);
    }

    public function customTestSelectByTestIDDel($testID){
        return $this->unionSelect('customTestSelectByTestIDDel', $testID);
    }
}
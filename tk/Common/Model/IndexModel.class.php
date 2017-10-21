<?php
/**
 * 索引model
 * @author demo
 * @date 2015-4-21
 */
namespace Common\Model;
class IndexModel extends BaseModel{
    protected $indexName = ''; //索引名称
    protected $searcher = null; //实例化索引

    protected $field = array(); //查询字段
    protected $where = array(); //查询条件
    protected $order = array(); //排序
    protected $page = array(); //分页信息 array('page'=>当前页,'perpage'=>'每页几个');
    protected $showField = array(); //以字段为键值的数组
    protected $ifColorKey = array(); //需要加颜色的字段

    protected $keywords = array(); //关键词数组

    /**
     * 初始化索引
     * @param int $openBackIndex 是否使用备用索引
     * @return null
     * */
    protected function init($indexName='zjtest,delta_zjtest',$openBackIndex=0){
        $this->indexName=$indexName;

        if(!class_exists('SphinxClient')){
            require(COMMON_PATH . 'Tool/coreseek/api/sphinxapi.php');
        }
        $this->searcher = new \SphinxClient();
        $index=C('WLN_INDEX_HOST');
        if(C('WLN_INDEX_OPEN_BACK') && $openBackIndex)  $index=C('WLN_INDEX_HOST_BACK');

        $this->searcher->setServer($index, 9312);
        $this->searcher->setConnectTimeout(3);
        $this->searcher->setArrayResult(true);
        $this->searcher->setRankingMode(SPH_RANK_SPH04);

        return $this->searcher;
    }
    /**
     * 初始化试题索引
     * @param int $openBackIndex 是否使用备用索引
     * @return null
     */
    public function initTest($openBackIndex=0){
        $this->init('zjtest,delta_zjtest',$openBackIndex);
    }
    /**
     * 初始化文档索引
     * @param int $openBackIndex 是否使用备用索引
     * @return null
     */
    public function initDoc($openBackIndex=0){
        $this->init('zjdoc,delta_zjdoc',$openBackIndex);
    }
    /**
     * 初始化自建试题索引
     * @param int $openBackIndex 是否使用备用索引
     * @return null
     */
    public function initCustomTest($openBackIndex=0){
        $this->init('zjcustom,delta_zjcustom',$openBackIndex);
    }

    /**
     * 设置页码
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return null
     * */
    protected function setIndexPage(){
        $page=$this->page;
        //默认分页
        if($page['page']<1 || !is_numeric($page['page'])) $page['page']=1;
        //默认每页数量
        if($page['perpage']<1 || !is_numeric($page['perpage'])) $page['perpage']=C('WLN_PERPAGE');
        if(empty($page['limit'])) $page['limit']=100000;

        $off = ($page['page'] -1) * $page['perpage'];//查询起始位置
        $this->searcher->setLimits($off, $page['perpage'], $page['limit']);

        $this->page=$page;
    }
    /**
     * 设置排序
     * @param array $order 排序
     * @return null
     * */
    protected function setIndexOrder(){
        $order=$this->order;
        $tmp_order='@weight DESC, @id DESC';
        if($order){
            if(is_array($order)) $tmp_order=implode(',',$order);
            else $tmp_order=$order;
        }
        $this->searcher->SetSortMode(SPH_SORT_EXTENDED, $tmp_order);
    }
    /**
     * 设置试题查询条件
     * @return null
     * */
    protected function setIndexWhereForTest(){
        $where=$this->where;

        $this->searcher->SetFilter('status', array (2), true);//状态为2的不显示 作为试题删除后的临时状态

        //设定默认查询方式
        if(!isset($where['searchStyle'])){
            $where['searchStyle']='any';
            $this->searcher->SetFieldWeights(array('test' => 10));
        }

        //设定关键字查询方式
        if (isset($where['key']) && $where['searchStyle']=='any') {
            //任意匹配
            $this->searcher->SetMatchMode(SPH_MATCH_ANY);
        }else if(isset($where['key']) && $where['searchStyle']=='normal'){
            //特殊匹配
            $this->searcher->SetMatchMode(SPH_MATCH_EXTENDED2);
            if (isset($where['field']) && isset($where['key'])){
                $where['key']='@'.$where['field'].' '.$where['key'];
            }
        }else{
            //全匹配
            $this->searcher->SetMatchMode(SPH_MATCH_ALL);
        }

        //高级查询
        if (isset($where['DocID'])) {
            if(is_array($where['DocID'])) $tmpArr=$where['DocID'];
            else $tmpArr=explode(',',$where['DocID']);
            $this->searcher->SetFilter('docid', $tmpArr);
        }

        if (isset($where['AreaID']) && $where['AreaID']!=0) {
            if(is_array($where['AreaID'])) $tmpArr=$where['AreaID'];
            else $tmpArr=explode(',',$where['AreaID']);
            $this->searcher->SetFilter('areaid', $tmpArr);
        }

        if (isset($where['Duplicate'])) {
            $this->searcher->SetFilter('duplicate', array($where['Duplicate']));
        }
        if (isset($where['DocTypeID'])) {
            if(is_array($where['DocTypeID'])) $tmpArr=$where['DocTypeID'];
            else $tmpArr=explode(',',$where['DocTypeID']);
            $this->searcher->SetFilter('typeid', $tmpArr);
        }
        if (isset($where['TestID'])) {
            $bool=false;
            if($where['testfilter']==1)  $bool=true; //是否是排除 1排除 0包括

            if(is_array($where['TestID'])) $tmpArr=$where['TestID'];
            else $tmpArr=explode(',',$where['TestID']);

            $this->searcher->SetFilter('testid', $tmpArr,$bool);
        }
        if (isset($where['maxtestid'])) {
            $this->searcher->SetFilterRange('testid', 0,$where['maxtestid']);
        }
        if (isset($where['Diff'])) {
            $diff_array = C("WLN_TEST_DIFF");
            $this->searcher->SetFilterFloatRange('diff', $diff_array[$where['Diff']][3], $diff_array[$where['Diff']][4]);
        }
        if (isset($where['DiffNum'])) {
            $this->searcher->SetFilterFloatRange('diff', $where['DiffNum'][0], $where['DiffNum'][1]);
        }
        if (isset($where['TestNum'])) {
            $this->searcher->SetFilter('testnum', array($where['TestNum']));
        }
        if (isset($where['TestStyle'])) {
            $this->searcher->SetFilter('teststyle', array($where['TestStyle']));
        }
        if (isset($where['TypesID'])) {
            $bool = false;
            if($where['TypeFilter']==1) $bool = true; //是否是排除 1排除 0包括

            if(is_array($where['TypesID'])) $tmpArr=$where['TypesID'];
            else $tmpArr=explode(',',$where['TypesID']);

            $this->searcher->SetFilter('typesid', $tmpArr,$bool);
        }
        if (isset($where['SubjectID'])) {
            if(is_array($where['SubjectID'])) $tmpArr=$where['SubjectID'];
            else $tmpArr=explode(',',$where['SubjectID']);

            $this->searcher->SetFilter('subjectid',$tmpArr);
        }
        if (isset($where['SpecialID'])) {
            if(is_array($where['SpecialID'])) $tmpArr=$where['SpecialID'];
            else $tmpArr=explode(',',$where['SpecialID']);

            $this->searcher->SetFilter('specialid', $tmpArr);
        }
        if (isset($where['GradeID'])) {
            if(is_array($where['GradeID'])) $tmpArr=$where['GradeID'];
            else $tmpArr=explode(',',$where['GradeID']);

            $this->searcher->SetFilter('gradeid', $tmpArr);
        }
        /*
        if (isset($where['AreaID']) && $where['AreaID']!=0) {
            if(is_array($where['AreaID'])) $tmpArr=$where['AreaID'];
            else $tmpArr=explode(',',$where['AreaID']);

            $this->searcher->SetFilter('areaid', $tmpArr);
        }*/

        //知识id
        if (isset($where['KlID'])) {
            if(!is_array($where['KlID'])) $tmpArr=explode(',',$where['KlID']);
            else $tmpArr=$where['KlID'];
            //知识点缓存
            $klBuffer = SS('klList');
            foreach($tmpArr as $iTmpArr){
                //查找知识点下的子类
                if ($klBuffer[$iTmpArr])
                    $klArr = explode(',', $klBuffer[$iTmpArr]);
                else
                    $klArr = array ($iTmpArr);
                $tmpArr=array_merge($klArr,$tmpArr);
            }
            unset($klBuffer);
            $tmpArr=array_unique($tmpArr);
            $this->searcher->SetFilter('klid', $tmpArr);
        }

        //章节id
        if (isset($where['ChapterID'])) {
            if(!is_array($where['ChapterID'])) $tmpArr=explode(',',$where['ChapterID']);
            else $tmpArr=$where['ChapterID'];

            //章节缓存
            $chapterIDListBuffer = SS('chapterIDList');
            foreach($tmpArr as $iTmpArr){
                //查找章节下的子类
                if ($chapterIDListBuffer[$iTmpArr])
                    $chapterArr = explode(',', $chapterIDListBuffer[$iTmpArr]);
                else
                    $chapterArr = array ($iTmpArr);
                $tmpArr=array_merge($chapterArr,$tmpArr);
            }
            unset($chapterIDListBuffer);
            $tmpArr=array_unique($tmpArr);
            $this->searcher->SetFilter('chapterid', $tmpArr);
        }

        //按时间限制  正数是设定时间到当前时间  负数是从0到设定时间
        if (isset($where['LastTime'])) {
            $lasttime=$where['LastTime'];
            if ($lasttime > 0)
                $this->searcher->SetFilterRange('firstloadtime', $lasttime, time());
            else
                $this->searcher->SetFilterRange('firstloadtime', 0, -$lasttime);
        }
        //在哪里显示
        if (isset($where['ShowWhere'])) {
            if(!is_array($where['ShowWhere'])) $where['ShowWhere']=explode(',',$where['ShowWhere']);
            $this->searcher->SetFilter('showwhere',$where['ShowWhere']);
        }else{
            $this->searcher->SetFilter('showwhere',array(0,1,2));
        }

        //是否显示测试类型
        if($where['AatTestStyle']!=-1){
            if(empty($where['AatTestStyle'])) $where['AatTestStyle']=array(0);
            if (isset($where['AatTestStyle'])) {
                if(!is_array($where['AatTestStyle'])) $where['AatTestStyle']=array($where['AatTestStyle']);
                $this->searcher->SetFilter('aatteststyle',$where['AatTestStyle']);
            }
        }

        //来源
        if (isset($where['SourceID'])) {
            if(!is_array($where['SourceID'])) $where['SourceID']=explode(',',$where['SourceID']);
            $this->searcher->SetFilter('sourceid',$where['SourceID']);
        }

        $this->where=$where;
    }
    /**
     * 设置文档查询条件
     * @return null
     * */
    protected function setIndexWhereForDoc(){
        $where=$this->where;


        //设定默认查询方式
        if(!isset($where['searchStyle'])){
            $where['searchStyle']='any';
            $this->searcher->SetFieldWeights(array('test' => 10));
        }

        //设定关键字查询方式
        if (isset($where['key']) && $where['searchStyle']=='any') {
            //任意匹配
            $this->searcher->SetMatchMode(SPH_MATCH_ANY);
        }else if(isset($where['key']) && $where['searchStyle']=='normal'){
            //特殊匹配
            $this->searcher->SetMatchMode(SPH_MATCH_EXTENDED2);
            if (isset($where['field']) && isset($where['key'])){
                $where['key']='@'.$where['field'].' '.$where['key'];
            }
        }else{
            //全匹配
            $this->searcher->SetMatchMode(SPH_MATCH_ALL);
        }

        //排除 用于删除文档
        $this->searcher->SetFilter('ifintro', array (2), true);

        //高级查询
        if (isset($where['DocID']) && $where['DocID']) {
            $this->searcher->SetFilter('docid', explode(',',$where['DocID']));
        }
        if (isset($where['DocYear']) && $where['DocYear']) {
            $this->searcher->SetFilter('docyear', explode(',',$where['DocYear']));
        }
        if (isset($where['TypeID']) && $where['TypeID']) {
            $this->searcher->SetFilter('typeid', explode(',',$where['TypeID']));
        }
        if (isset($where['SubjectID']) && $where['SubjectID']) {
            $this->searcher->SetFilter('subjectid',explode(',',$where['SubjectID']));
        }
        if (isset($where['AreaID']) && $where['AreaID']) {
            $this->searcher->SetFilter('areaid', explode(',',$where['AreaID']));
        }
        if (isset($where['GradeID']) && $where['GradeID']) {
            $this->searcher->SetFilter('gradeid', explode(',',$where['GradeID']));
        }
        if (isset($where['IfTest']) && is_numeric($where['IfTest'])) {
            $this->searcher->SetFilter('iftest', array($where['IfTest']));
        }
        if (isset($where['IfRecom']) && is_numeric($where['IfRecom'])) {
            $this->searcher->SetFilter('ifrecom', array($where['IfRecom']));
        }

        //在哪里显示
        if (isset($where['ShowWhere'])){
            if(!is_array($where['ShowWhere'])) $where['ShowWhere']=array($where['ShowWhere']);
            $this->searcher->SetFilter('showwhere', $where['ShowWhere']);
        }else{
            $this->searcher->SetFilter('showwhere',array(0,1,2));
        }

        //是否显示测试类型
        if(empty($where['AatTestStyle'])) $where['AatTestStyle']=array(0);
        if (isset($where['AatTestStyle'])) {
            if(!is_array($where['AatTestStyle'])) $where['AatTestStyle']=array($where['AatTestStyle']);
            $this->searcher->SetFilter('AatTestStyle',$where['AatTestStyle']);
        }

        //添加指定日期区间  2014-10-27
        if(isset($where['IntroFirstTime']) && $where['IntroFirstTime']){
            $this->searcher->setFilterRange('introfirsttime',$where['IntroFirstTime']['start'],$where['IntroFirstTime']['end']);
        }

        //来源
        if (isset($where['SourceID']) && $where['SourceID']) {
            if(!is_array($where['SourceID'])) $where['SourceID']=explode(',',$where['SourceID']);
            $this->searcher->SetFilter('SourceID',$where['SourceID']);
        }

        $this->where=$where;
    }

    /**
     * 设置自建试题查询条件
     * @return null
     * */
    protected function setIndexWhereForCustomTest(){
        $where=$this->where;

        //状态为10：删除后的试题； -2为优化成功后的试题
        $this->searcher->SetFilter('status', array (4294967294, 10), true);

        if(!isset($where['searchStyle'])){
            $where['searchStyle']='any';
            $this->searcher->SetFieldWeights(array('test' => 10));
        }
        if (isset($where['key']) && $where['searchStyle']=='any') {
            $this->searcher->SetMatchMode(SPH_MATCH_ANY);
        }else if(isset($where['key']) && $where['searchStyle']=='normal'){
            $this->searcher->SetMatchMode(SPH_MATCH_EXTENDED2);
            if (isset($where['field']) && isset($where['key'])){
                $where['key']='@'.$where['field'].' '.$where['key'];
            }
        }else{
            $this->searcher->SetMatchMode(SPH_MATCH_ALL);
        }

        //查询条件仅限于
        $condition = array(
            'TestID' => 'testid',
            'SubjectID' => 'subjectid',
            'TypesID' => 'typesid',
            'Diff' => 'diff',
            'KlID' => 'klid',
            'UserID'=>'userid'
        );

        foreach($condition as $key=>$value){
            if(isset($where[$key]) && !empty($where[$key])){
                if('Diff' == $key){
                    list($s, $e) = $where['Diff'];
                    $this->searcher->setFilterFloatRange($value, $s, $e);
                }else{
                    if(is_string($where[$key])){
                        $where[$key] = explode(',', $where[$key]);
                    }
                    $this->searcher->setFilter($value, $where[$key]);
                }
            }
        }
    }
    /**
     * 设置查询字段
     * @param array $field 查询条件
     * @return null
     * */
    protected function setIndexFieldForTest(){
        $field=$this->field;

        $showField=array(); //存储以字段为键值
        $selectField=array(); //字段需要查询的关联字段

        $field[]='duplicate';
        foreach($field as $i=>$iField){
            $showField[$iField]=$iField;
            switch($iField){
                case 'weight':
                    break;
                case 'test':
                    $selectField[]='optionwidth';
                    $selectField[]='optionnum';
                    $selectField[]='testnum';
                    $selectField[]='ifchoose';
                    $selectField[]='test';
                    break;
                case 'testold':
                    $selectField[]='optionwidth';
                    $selectField[]='optionnum';
                    $selectField[]='testnum';
                    $selectField[]='ifchoose';
                    $selectField[]='test';
                    break;
                case 'testsplit':
                    $selectField[]='optionwidth';
                    $selectField[]='optionnum';
                    $selectField[]='testnum';
                    $selectField[]='ifchoose';
                    $selectField[]='test';
                    break;
                case 'testnormal':
                    $selectField[]='optionwidth';
                    $selectField[]='optionnum';
                    $selectField[]='testnum';
                    $selectField[]='ifchoose';
                    $selectField[]='test';
                    break;
                case 'answer':
                    $selectField[]='testnum';
                    $selectField[]='answer';
                    break;
                case 'answerold':
                    $selectField[]='testnum';
                    $selectField[]='answer';
                    break;
                case 'answersplit':
                    $selectField[]='testnum';
                    $selectField[]='answer';
                    break;
                case 'answernormal':
                    $selectField[]='testnum';
                    $selectField[]='answer';
                    break;
                case 'analytic':
                    $selectField[]='testnum';
                    $selectField[]='analytic';
                    break;
                case 'analyticold':
                    $selectField[]='testnum';
                    $selectField[]='analytic';
                    break;
                case 'analyticsplit':
                    $selectField[]='testnum';
                    $selectField[]='analytic';
                    break;
                case 'analyticnormal':
                    $selectField[]='testnum';
                    $selectField[]='analytic';
                    break;
                case 'remark':
                    $selectField[]='testnum';
                    $selectField[]='remark';
                    break;
                case 'remarkold':
                    $selectField[]='testnum';
                    $selectField[]='remark';
                    break;
                case 'remarksplit':
                    $selectField[]='testnum';
                    $selectField[]='remark';
                    break;
                case 'remarknormal':
                    $selectField[]='testnum';
                    $selectField[]='remark';
                    break;
                case 'klnameonly':
                    $selectField[]='klid';
                    break;
                case 'klnameall':
                    $selectField[]='klid';
                    break;
                case 'kllist':
                    $selectField[]='klid';
                    break;
                case 'chaptername':
                    $selectField[]='chapterid';
                    break;
                case 'chapternameall':
                    $selectField[]='chapterid';
                    break;
                case 'specialname':
                    $selectField[]='specialid';
                    break;
                case 'subjectname':
                    $selectField[]='subjectid';
                    break;
                case 'typesname':
                    $selectField[]='typesid';
                    break;
                case 'gradename':
                    $selectField[]='gradeid';
                    break;
                case 'loadtimeint':
                    $selectField[]='loadtime';
                    break;
                case 'firstloadtimeint':
                    $selectField[]='firstloadtime';
                    break;
                case 'diffid':
                    $selectField[]='diff';
                    break;
                case 'diffname':
                    $selectField[]='diff';
                    break;
                case 'xtnum':
                    $selectField[]='testnum';
                    break;
                default :
                    $selectField[]=$iField;
            }
        }
        if($selectField){
            $selectField=array_unique(array_filter($selectField));
            if(!in_array('chapterid',$selectField) && !in_array('klid',$selectField))
                $this->searcher->SetSelect (implode(',',$selectField));
        }

        $this->showField=$showField;
    }
    /**
     * 设置查询字段
     * @param array $field 查询条件
     * @return null
     * */
    protected function setIndexFieldForDoc(){
        $field=$this->field;

        $fieldArr=array();
        $inFieldArr=array();
        foreach($field as $i=>$iField){
            $inFieldArr[$iField]=$iField;
            switch($iField){
                case 'areaname':
                    $fieldArr[]='areaid';
                    break;
                case 'introtimeint':
                    $fieldArr[]='introtime';
                    break;
                case 'loadtimeint':
                    $fieldArr[]='loadtime';
                    break;
                case 'introfirsttimeint':
                    $fieldArr[]='introfirsttime';
                    break;
                case 'typename':
                    $fieldArr[]='typeid';
                    break;
                case 'gradename':
                    $fieldArr[]='gradeid';
                    break;
                case 'subjectname':
                    $fieldArr[]='subjectid';
                    break;
                default :
                    $fieldArr[]=$iField;
            }
        }

        if($fieldArr){
            $fieldArr=array_filter(array_unique($fieldArr));
            $this->searcher->SetSelect(implode(',',$fieldArr));
        }

        $this->showField=$inFieldArr;
    }


    /**
     * 设置查询字段
     * @param array $field 查询条件
     * @return null
     * */
    protected function setIndexFieldForCustomTest(){
        $field=$this->field;

        // 有效的字段信息，非此数组的字段将被去除
        $indexField = array(
            'testid'=>'',
            'typesid'=>'',
            'docname'=>array('source'),
            'chapterid'=>'',
            'typesname'=>array('typesid'),
            'subjectid'=>'',
            'subjectname'=>array('subjectid'),
            'specialid'=>'',
            'specialname'=>array('specialid'),
            'test'=> '',//'题文被table分割选项后的字符串并且序号化'
            'testold'=>array('test'), //题文被table分割选项后的字符串并且标签化
            'testsplit'=>array('test'),//分割复合题小题为数组
            'testnormal'=>array('test'),//题文未被处理
            'answer'=> '', //答案序号化
            'answerold'=>array('answer'), //答案标签化
            'answersplit'=>array('answer'),//分割答案为数组
            'answernormal'=>array('answer'), //答案未被处理
            'analytic'=>'', //解析序号化
            'analyticold'=>array('analytic'), //解析标签化
            'analyticsplit'=>array('analytic'), //分割解析为数组
            'analyticnormal'=>array('analytic'), //解析未被处理
            'remark'=>'', //解析序号化
            'remarksplit'=>array('remark'),//分割备注为数组
            'firstloadtime'=> array('addtime'), //第一次入库时间（格式 ：年/月/日）
            'firstloadtimeint'=>array('addtime'), //第一次unix入库时间
            'loadtime'=>array('lastupdatetime'), //最近一次入库时间（格式 ：年/月/日）
            'loadtimeint'=>array('lastupdatetime'), //最近一次unix入库时间
            'testnum'=>'',
            'diff'=>'',
            'diffid'=>array('diff'), //难度id（1-5共五档）
            'diffstar'=>array('diff'), //难度数据段标示（例如：0.001-0.300）
            'diffname'=>array('diff'), //难度名称
            'diffxing'=>array('diff'), //难度html星星标示（需要css）
            'klid'=>'',
            //'mark'=>打分细则
            'kllist'=>array('klid'),//知识点列表带知识点视频
            'klnameall'=>array('klid'), //知识点名称路径
            'klnameonly'=>array('klid'),//知识点名称
            'ifchoose'=>'', //试题类型（0非选择题 1复合体 2多选 3单选）
            'chapterid'=>'',
            'chapternameall'=>array('chapterid'), //章节名称路径
            'teststyle'=>'', //试题类型
            'optionwidth'=>'', //选项宽度
            'optionnum'=>'', //选项数量
            'status'=>'',
            'width'=>'',
            'gradeid'=>'',
            'gradename'=>array('gradeid'),
            'xtnum'=>array('testnum')
        );
        //去除field字段中不合法的字段信息
        $temp = array();
        $arr = array();
        foreach($field as $value){
            if(array_key_exists($value, $indexField)){
                $temp[] = $value;
                //如果值为数组，则提取相关的数据
                if(is_array($indexField[$value])){
                    $temp[] = $indexField[$value][0];
                    $arr[$indexField[$value][0]][] = $value;
                }
            }
        }
        $field = $temp;
        unset($temp);

        $showField=array();
        $selectField=array();
        $selectField[] = 'testid';
        $tmpArr=array();
        foreach($field as $i=>$iField){
            $showField[$iField]=$iField;
            switch($iField){
                case 'test':
                case 'testold':
                case 'testnormal':
                case 'testsplit':
                    $selectField[]='optionwidth';
                    $selectField[]='optionnum';
                    $selectField[]='testnum';
                    $selectField[]='ifchoose';
                    $selectField[]='test';
                    break;
                case 'answer':
                case 'answerold':
                case 'answernormal':
                case 'answersplit':
                    $selectField[]='testnum';
                    $selectField[]='answer';
                    break;
                case 'analytic':
                case 'analyticold':
                case 'analyticnormal':
                case 'analyticsplit':
                    $selectField[]='testnum';
                    $selectField[]='analytic';
                    break;
                case 'remark':
                case 'remarkold':
                case 'remarknormal':
                case 'remarksplit':
                    $selectField[]='testnum';
                    $selectField[]='remark';
                    break;
                default :
                    if(array_key_exists($iField, $indexField)){
                        $val = $indexField[$iField];
                        if(is_array($val))
                            $selectField[]=$val[0];
                        else
                            $selectField[] = $iField;
                    }
            }
        }
        if($selectField){
            $selectField=array_unique(array_filter($selectField));
            if(!in_array('chapterid',$selectField) && !in_array('klid',$selectField))
                $this->searcher->SetSelect (implode(',',$selectField));
        }

        $this->showField=$showField;
    }

    /**
     * 设置查询关键字高亮
     * @return null
     * */
    protected function setIndexColorForTest(){
        $where=$this->where;
        $showField=$this->showField;

        $ifColorKey=array();
        if(!isset($where['red'])){
            if(isset($where['key'])){
                if($showField['test'] and (!isset($where['field']) or $where['field']=='test')){
                    $ifColorKey['test']=1;
                }
                if($showField['answer'] and (!isset($where['field']) or $where['field']=='answer')){
                    $ifColorKey['answer']=1;
                }
                if($showField['analytic'] and (!isset($where['field']) or $where['field']=='analytic')){
                    $ifColorKey['analytic']=1;
                }
                if($showField['remark'] and (!isset($where['field']) or $where['field']=='remark')){
                    $ifColorKey['remark']=1;
                }
                if($showField['docname'] and (!isset($where['field']) or $where['field']=='docname')){
                    $ifColorKey['docname']=1;
                }
            }
        }

        $this->ifColorKey=$ifColorKey;
    }

    /**
     * 试题索引
     * @param array $field 返回字段
     * @param array $where 条件
     * @param array $order 排序
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array 返回数组
     * */
    public function getTestIndex($field,$where,$order,$page){
        if(!$this->searcher){
            return array();
        }

        //配置数据
        $this->field=$this->_lowercaseField($field);
        $this->where=$where;
        $this->order=$order;
        $this->page=$page;

        $this->setIndexPage(); //设置页码
        $this->setIndexWhereForTest(); //设置查询条件
        $this->setIndexOrder(); //设置排序
        $this->setIndexFieldForTest(); //设置查询字段
        $this->setIndexColorForTest(); //设置需要高亮的字段

        $res = array();
        $key='';
        if (isset($where['key'])) {
            $key = $this->filterStopWords($where['key']);
        }
        $res = $this->searcher->Query($key, $this->indexName);
        //记录搜索引擎中的错误和警告信息
        if($this->setIndexError() === false){
            return false;
        }

        //对有重复试题的数据进行真实数据替换
        $tmp=$res['matches'];
        $duplicate=array();
        $duplicateTestID=array();
        foreach($tmp as $i=>$iTmp){
            if($iTmp['attrs']['duplicate']!=0 && $iTmp['attrs']['duplicate']!=$iTmp['attrs']['testid']){
                $duplicateTestID[]=$iTmp['attrs']['duplicate'];
                $duplicate[]=$i;
            }
        }
        if(count($duplicateTestID)!=0){
            $test=$this->getModel('Test');
            $tmpSearch=$this->searcher;
            $tmpSearch->ResetFilters(); //重置方法
            $tmpSearch->SetFilter('testid', $duplicateTestID);
            $tmpRes = $tmpSearch->Query('', $this->indexName);
            $tmpResTestID=array();$tmpData=array();
            foreach($tmpRes['matches'] as $iTmpRes){
                $tmpResTestID[$iTmpRes['attrs']['testid']]=$iTmpRes['attrs'];
            }
            foreach($duplicate as $i=>$iDuplicate){
                //对部分数据进行替换
                $source=$res['matches'][$iDuplicate]['attrs'];
                $tag=$tmpResTestID[$res['matches'][$iDuplicate]['attrs']['duplicate']];
                $res['matches'][$iDuplicate]['attrs']=$test->changeArrayValue($source,$tag);
            }
        }

        return $this->getResultForTest($res);
    }

    /**
     * 文档索引
     * @param array $field 返回字段
     * @param array $where 条件
     * @param array $order 排序
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array 返回数组
     * */
    public function getDocIndex($field,$where,$order,$page){
        if(!$this->searcher){
            return array();
        }

        //配置数据
        $this->field=$this->_lowercaseField($field);
        $this->where=$where;
        $this->order=$order;
        $this->page=$page;

        $this->setIndexPage(); //设置页码
        $this->setIndexWhereForDoc(); //设置查询条件
        $this->setIndexOrder(); //设置排序
        $this->setIndexFieldForDoc(); //设置查询字段
        //$this->setIndexColorForDoc(); //设置需要高亮的字段

        $res = array();
        $key='';
        if (isset($where['key'])) {
            $key = $where['key'];
        }
        $res = $this->searcher->Query($key, $this->indexName);

        //记录搜索引擎中的错误和警告信息
        if($this->setIndexError() === false){
            return false;
        }
        return $this->getResultForDoc($res);
    }

    /**
     * 自建试题索引
     * @param array $field 返回字段
     * @param array $where 条件
     * @param array $order 排序
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array 返回数组
     * */
    public function getCustomTestIndex($field,$where,$order,$page){
        if(!$this->searcher){
            return array();
        }

        //配置数据
        $this->field=$this->_lowercaseField($field);
        $this->where=$where;
        $this->order=$order;
        $this->page=$page;

        $this->setIndexPage(); //设置页码
        $this->setIndexWhereForCustomTest(); //设置查询条件
        $this->setIndexOrder(); //设置排序
        $this->setIndexFieldForCustomTest(); //设置查询字段
        //$this->setIndexColorForCustomTest(); //设置需要高亮的字段

        $res = array();
        $key='';
        if (isset($where['key'])) {
            $key = $this->filterStopWords($where['key']);
        }
        $res = $this->searcher->Query($key, $this->indexName);

        //记录搜索引擎中的错误和警告信息
        if($this->setIndexError() === false){
            return false;
        }
        return $this->getResultForCustomTest($res);
    }
    /**
     * 对索引取出的数据格式化
     * *****说明*****
     * 格式化目前有四种方法,分别对应查询时field的四种方式:
     * 以test为例 answer,analytic,remark相同
    'test'=>试题序号化
    'testold'=>试题标签化
    'testsplit'=>分割试题为数组
    'testnormal'=> 试题未被处理
     * 如:$field=array('testid','answer','ifchoose','testnum','judge','analytic','test')表示获取序号化的数据
     * 如:$field=array('testid','answerold','ifchoose','testnum','judge','analyticold','testold')表示获取标签化的数据
     * 如:$field=array('testid','answersplit','ifchoose','testnum','judge','analyticsplit','testsplit')表示获取分割化的数据
     * split数据格式可查看TestModel->formatToArray方法说明
     * ...
     * **********
     * @param array $res 索引取出的数据
     * @return array
     * @author demo 
     */
    protected function getResultForTest($res){
        $tmpArr=array();
        $showField=$this->showField;
        $field=$this->field;
        $where=$this->where;
        $ifColorKey=$this->ifColorKey;

        if ($res['matches']) {
            if(isset($where['key']) && !isset($where['red'])) {
                $testCon = array ();
                $testTit = array ();
                $answerTit = array ();
                $analyticTit = array ();
                $remarkTit = array ();
                $docNameTit = array ();
                foreach($res['matches'] as $i=>$iRes) {
                    $testCon[]=$i;
                    if(isset($ifColorKey['test'])) $testTit[] = $this->replaceKeywords($iRes['attrs']['test']);
                    if(isset($ifColorKey['answer'])) $answerTit[] = $this->replaceKeywords($iRes['attrs']['answer']);
                    if(isset($ifColorKey['analytic'])) $analyticTit[] = $this->replaceKeywords($iRes['attrs']['analytic']);
                    if(isset($ifColorKey['remark'])) $remarkTit[] = $this->replaceKeywords($iRes['attrs']['remark']);
                    if(isset($ifColorKey['docname'])) $docNameTit[] = $this->replaceKeywords($iRes['attrs']['docname']);
                }
                // $indexName = explode(',', $this->indexName)[0];
                // $keywords = $this->searcher->BuildKeywords($where['key'], $indexName, false);
                // foreach($keywords as $iKeywords) {
                //     foreach($testCon as $j => $jTestCon) {
                //         //if(mb_strlen($iKeywords['tokenized'],'UTF-8')<2) continue;
                //         if(isset($ifColorKey['test'])) $testTit[$j] = preg_replace('/(?!<[^>]*)' . $iKeywords['tokenized'] . '(?![^<]*>)/', '<em>' . $iKeywords['tokenized'] . '</em>', $testTit[$j]);
                //         if(isset($ifColorKey['answer'])) $answerTit[$j] = preg_replace('/(?!<[^>]*)' . $iKeywords['tokenized'] . '(?![^<]*>)/', '<em>' . $iKeywords['tokenized'] . '</em>', $answerTit[$j]);
                //         if(isset($ifColorKey['analytic'])) $analyticTit[$j] = preg_replace('/(?!<[^>]*)' . $iKeywords['tokenized'] . '(?![^<]*>)/', '<em>' . $iKeywords['tokenized'] . '</em>', $analyticTit[$j]);
                //         if(isset($ifColorKey['remark'])) $remarkTit[$j] = preg_replace('/(?!<[^>]*)' . $iKeywords['tokenized'] . '(?![^<]*>)/', '<em>' . $iKeywords['tokenized'] . '</em>', $remarkTit[$j]);
                //         if(isset($ifColorKey['docname'])) $docNameTit[$j] = preg_replace('/(?!<[^>]*)' . $iKeywords['tokenized'] . '(?![^<]*>)/', '<em>' . $iKeywords['tokenized'] . '</em>', $docNameTit[$j]);
                //     }
                // }
            }
            if($showField['diff'] or $showField['diffid'] or $showField['diffname']){
                //难度属性
                $diff_array = C("WLN_TEST_DIFF");
            }
            if($showField['typesname']) $typesBuffer=SS('types');
            if($showField['subjectname']) $subjectBuffer=SS('subject');
            if($showField['specialname']) $specialBuffer=SS('special');
            if($showField['klnameonly'] or $showField['klnameall'] or $showField['kllist']){
                $knowledgeBuffer[0]=SS('knowledge');
                $knowledgeBuffer[1]=SS('knowledgeParent');
            }
            if($showField['chaptername']){
                $chapterParent=SS('chapterParentPath');
                $chapterList=SS('chapterList');
            }
            if($showField['gradename']) $gradeBuffer=SS('grade');
            //$host=C('WLN_DOC_HOST');
            $width=500;
            if(isset($where['width'])) $width=$where['width'];
            $test=$this->getModel('Test');

            //预处理
            if(in_array('kllist',$field)){
                $klStudy=$this->getModel('KlStudy');
            }
            if(in_array('chapternameall',$field)){
                $chapter=$this->getModel('Chapter');
            }
            if(in_array('klnameall',$field) || in_array('klnameonly',$field)){
                $knowledge=$this->getModel('Knowledge');
            }

            foreach ($res['matches'] as $i => $iRes) {
                foreach($field as $jField){
                    if($jField=='weight'){
                        $tmpArr[$i][$jField] = $iRes['weight'];
                    }else if($jField=='test' and isset($ifColorKey['test'])){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($testTit[$i],1,$width,0,1,$iRes['attrs']['optionwidth'],$iRes['attrs']['optionnum'],$iRes['attrs']['testnum'],$iRes['attrs']['ifchoose'],1)));
                    }else if($jField=='answer' and isset($ifColorKey['answer'])){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($answerTit[$i],1,0,0,0,0,0,$iRes['attrs']['testnum'],0,1)));
                    }else if($jField=='analytic' and isset($ifColorKey['analytic'])){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($analyticTit[$i],1,0,0,0,0,0,$iRes['attrs']['testnum'],0,1)));
                    }else if($jField=='remark' and isset($ifColorKey['remark'])){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($remarkTit[$i],1,0,0,0,0,0,$iRes['attrs']['testnum'],0,1)));
                    }else if($jField=='docname' and isset($ifColorKey['docname'])){
                        $tmpArr[$i][$jField]=$docNameTit[$i];
                    }else if($jField=='loadtime'){
                        $tmpArr[$i][$jField]=date('Y/m/d',$iRes['attrs'][$jField]);
                    }else if($jField=='loadtimeint'){
                        $tmpArr[$i]['loadtimeint']=$iRes['attrs']['loadtime'];
                    }else if($jField=='firstloadtime'){
                        $tmpArr[$i][$jField]=date('Y/m/d',$iRes['attrs'][$jField]);
                    }else if($jField=='firstloadtimeint'){
                        $tmpArr[$i]['loadtimeint']=$iRes['attrs']['firstloadtime'];
                    }else if($jField=='typesname'){
                        $tmpArr[$i][$jField]=$typesBuffer[$iRes['attrs']['typesid']]['TypesName'];
                    }else if($jField=='gradename'){
                        $tmpArr[$i][$jField]=$gradeBuffer[$iRes['attrs']['gradeid']]['GradeName'];
                    }else if($jField=='diff'){
                        $tmpArr[$i]['diff']=round($iRes['attrs']['diff'], 3);
                        $tmpArr[$i]['diffid']=R('Common/TestLayer/diff2Int',array($iRes['attrs']['diff'], $diff_array));
                        $tmpArr[$i]['diffstar']=R('Common/TestLayer/int2Html',array($tmpArr[$i]['diffid']));
                        $tmpArr[$i]['diffname']=$diff_array[$tmpArr[$i]['diffid']][0];
                        $tmpArr[$i]['diffxing']=R('Common/TestLayer/int2Xing',array($tmpArr[$i]['diffid']));
                    }else if($jField=='diffid'){
                        $tmpArr[$i]['diffid']=R('Common/TestLayer/diff2Int',array($iRes['attrs']['diff'], $diff_array));
                    }else if($jField=='kllist'){
                        $tmpArr[$i][$jField] = implode('<br/>',$klStudy->getKlList($iRes['attrs']['klid'],$knowledgeBuffer));
                    }else if($jField=='klnameall'){
                        $tmpArr[$i][$jField] = implode('<br/>',$knowledge->getKlAll($iRes['attrs']['klid'],$knowledgeBuffer));
                    }else if($jField=='klnameonly'){
                        $tmpArr[$i][$jField] = implode('<br/>',$knowledge->getKlOnly($iRes['attrs']['klid'],$knowledgeBuffer));
                    }else if($jField=='chapternameall'){
                        $tmpArr[$i][$jField] = implode('<br/>',$chapter->getChapterAll($iRes['attrs']['chapterid'],$chapterParent,$chapterList));
                    }else if($jField=='xtnum'){
                        if($iRes['attrs']['testnum']==0) $tmpArr[$i]['testnum'] = 1;
                        $tmpArr[$i][$jField] = $iRes['attrs']['testnum'];
                    }else if($jField=='subjectname'){
                        $tmpArr[$i][$jField] = $subjectBuffer[$iRes['attrs']['subjectid']]['ParentName'].$subjectBuffer[$iRes['attrs']['subjectid']]['SubjectName'];
                    }else if($jField=='specialname'){
                        $tmpArr[$i][$jField] = $specialBuffer[$iRes['attrs']['specialid']]['SpecialName'];
                    }else if($jField=='test'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['test'],1,$width,0,1,$iRes['attrs']['optionwidth'],$iRes['attrs']['optionnum'],$iRes['attrs']['testnum'],$iRes['attrs']['ifchoose'],1)));
                    }else if($jField=='testold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['test'],0,$width,0,1,$iRes['attrs']['optionwidth'],$iRes['attrs']['optionnum'],$iRes['attrs']['testnum'],$iRes['attrs']['ifchoose'])));
                    }else if($jField=='answerold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['answer'],0,0,0,0,0,0,$iRes['attrs']['testnum'],0)));
                    }else if($jField=='analyticold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['analytic'],0,0,0,0,0,0,$iRes['attrs']['testnum'],0)));
                    }else if($jField=='remarkold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['remark'],0,0,0,0,0,0,$iRes['attrs']['testnum'],0)));
                    }else if($jField=='answer' or $jField=='analytic' or $jField=='remark'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs'][$jField],1,0,0,0,0,0,$iRes['attrs']['testnum'],0,1)));
                    }else if($jField=='testsplit' || $jField=='answersplit' || $jField=='analyticsplit' || $jField=='remarksplit'){
                        $tmpArr[$i][$jField] = $test->formatToArray(R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs'][substr($jField,0,-5)],0,0,0,0,0,0,$iRes['attrs']['testnum'],0))),$iRes['attrs']['testnum'],$iRes['id'],$jField=='testsplit'?true:false);
                    }else if($jField=='testnormal' or $jField=='answernormal' or $jField=='analyticnormal' or $jField=='remarknormal'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($iRes['attrs'][str_replace('normal','',$jField)]));
                    }else{
                        $tmpArr[$i][$jField]=$iRes['attrs'][$jField];
                        if($iRes['attrs']['testnum']==0) $tmpArr[$i]['testnum'] = 1;
                    }
                }
            }
        }
        $output[0]=$tmpArr;
        $output[1]=$res['total'];
        $output[2]=$this->page['perpage'];
        unset($res);
        unset($tmpArr);
        return $output;
    }

    /**
     * 对文档索引结果进行处理
     * @param array $res 试题索引结果
     * @return mixed
     * @author demo
     */
    protected function getResultForDoc($res){
        $output=array();

        $showField=$this->showField;
        $field=$this->field;
        $where=$this->where;
        $ifColorKey=$this->ifColorKey;

        if ($res) {
            if ($res['matches']) {
                if (isset($where['key']) && $where['key']) {
                    $testTit = array ();
                    $testCon = array ();
                    foreach ($res['matches'] as $iRes) {
                        $testTit[] = $iRes['attrs']['docname'];
                        $testCon[] = $iRes['attrs']['description'];
                    }

                    foreach ($testCon as $i => $iTestCon) {
                        $testCon[$i] = $this->replaceKeywords($testCon[$i]);
                        $testTit[$i] = $this->replaceKeywords($testTit[$i]);
                    }
                }
                $tmpArr=array();
                if(isset($showField['areaname']) && $showField['areaname']) $arrBuffer=SS('areaList');
                if(isset($showField['subjectname']) && $showField['subjectname']) $subjectBuffer=SS('subject');
                if(isset($showField['typename']) && $showField['typename']) $typeBuffer=SS('docType');
                if(isset($showField['gradename']) && $showField['gradename']) $gradeBuffer=SS('grade');
                foreach ($res['matches'] as $i => $iRes) {
                    foreach($field as $jField){
                        if(isset($where['key']) and $jField=='docname'){
                            $tmpArr[$i][$jField]=$testTit[$i];
                        }else if(isset($where['key']) and $jField=='description'){
                            $tmpArr[$i][$jField]=$testCon[$i];
                        }else if($jField=='introtime'){
                            $tmpArr[$i][$jField]=date('Y/m/d',$iRes['attrs'][$jField]);
                        }else if($jField=='introtimeint'){
                            $tmpArr[$i][$jField]=$iRes['attrs']['introtime'];
                        }else if($jField=='loadtime'){
                            $tmpArr[$i][$jField]=date('Y/m/d',$iRes['attrs'][$jField]);
                        }else if($jField=='loadtimeint'){
                            $tmpArr[$i][$jField]=$iRes['attrs']['loadtime'];
                        }else if($jField=='introfirsttime'){
                            $tmpArr[$i][$jField]=date('Y/m/d',$iRes['attrs'][$jField]);
                        }else if($jField=='introfirsttimeint'){
                            $tmpArr[$i][$jField]=$iRes['attrs']['introfirsttime'];
                        }else if($jField=='typename'){
                            $tmpArr[$i][$jField]=$typeBuffer[$iRes['attrs']['typeid']]['TypeName'];
                        }else if($jField=='subjectname'){
                            $tmpArr[$i][$jField]=$subjectBuffer[$iRes['attrs']['subjectid']]['ParentName'].$subjectBuffer[$iRes['attrs']['subjectid']]['SubjectName'];
                        }else if($jField=='gradename'){
                            $tmpArr[$i][$jField]=$gradeBuffer[$iRes['attrs']['gradeid']]['GradeName'];
                        }elseif($jField=='ifrecom'){
                            $tmpArr[$i][$jField]=$iRes['attrs']['ifrecom'];
                        }else if($jField=='areaname'){
                            $areaArr=$iRes['attrs']['areaid'];
                            if($areaArr){
                                if(count($areaArr)>5){
                                    $tmpArr[$i][$jField]='全国';
                                    continue;
                                }
                                $areaTmp=array();
                                foreach($areaArr as $kAreaArr){
                                    $areaTmp[]=$arrBuffer[$kAreaArr]['AreaName'];
                                }
                                $tmpArr[$i][$jField]=implode(',',$areaTmp);
                                unset($areaTmp);
                            }else{
                                $tmpArr[$i][$jField]='';
                            }
                        }else{
                            $tmpArr[$i][$jField]=$iRes['attrs'][$jField];
                        }
                    }
                }
            }
        }
        $output[0]=$tmpArr;
        $output[1]=$res['total'];
        $output[2]=$this->page['perpage'];
        unset($res);
        unset($tmpArr);
        return $output;
    }

    /**
     * 对索引取出的数据格式化
     * *****说明*****
     * 格式化目前有四种方法,分别对应查询时field的四种方式:
     * 以test为例 answer,analytic,remark相同
    'test'=>试题序号化
    'testold'=>试题标签化
    'testsplit'=>分割试题为数组
    'testnormal'=> 试题未被处理
     * 如:$field=array('testid','answer','ifchoose','testnum','judge','analytic','test')表示获取序号化的数据
     * 如:$field=array('testid','answerold','ifchoose','testnum','judge','analyticold','testold')表示获取标签化的数据
     * 如:$field=array('testid','answersplit','ifchoose','testnum','judge','analyticsplit','testsplit')表示获取分割化的数据
     * ...
     * **********
     * @param array $res 索引取出的数据
     * @return array
     * @author demo 
     */
    public function getResultForCustomTest($res){

        $output=array();

        $showField=$this->showField;
        $field=$this->field;
        $where=$this->where;
        $ifColorKey=$this->ifColorKey;

        $tmpArr=array();
        if ($res['matches']) {
            if($showField['diff'] or $showField['diffid'] or $showField['diffname']){
                //难度属性
                $diff_array = C("WLN_TEST_DIFF");
            }
            if($showField['typesname']) $typesBuffer=SS('types');
            if($showField['subjectname']) $subjectBuffer=SS('subject');
            if($showField['specialname']) $specialBuffer=SS('special');
            if($showField['klnameonly'] or $showField['klnameall'] or $showField['kllist']){
                $knowledgeBuffer[0]=SS('knowledge');
                $knowledgeBuffer[1]=SS('knowledgeParent');
            }
            if($showField['chaptername']){
                $chapterParent=SS('chapterParentPath');
                $chapterList=SS('chapterList');
            }
            if($showField['gradename']) $gradeBuffer=SS('grade');
            $host=C('WLN_DOC_HOST');
            $width=500;
            if(isset($where['width'])) $width=$where['width'];
            $test = $this->getModel('Test');


            //预处理
            if(in_array('kllist',$field)){
                $klStudy=$this->getModel('KlStudy');
            }
            if(in_array('chapternameall',$field)){
                $chapter=$this->getModel('Chapter');
            }
            if(in_array('klnameall',$field) || in_array('klnameonly',$field)){
                $knowledge=$this->getModel('Knowledge');
            }

            foreach ($res['matches'] as $i => $iRes) {
                foreach($field as $jField){
                    if($jField=='weight'){
                        $tmpArr[$i][$jField] = $iRes['weight'];
                    }else if($jField=='loadtime'){
                        $tmpArr[$i]['loadtime']=date('Y/m/d',$iRes['attrs']['lastupdatetime']);
                    }else if($jField=='loadtimeint'){
                        $tmpArr[$i]['loadtime']=$iRes['attrs']['lastupdatetime'];
                    }else if($jField=='firstloadtime'){
                        $tmpArr[$i]['firstloadtime']=date('Y/m/d',$iRes['attrs']['addtime']);
                    }else if($jField=='firstloadtimeint'){
                        $tmpArr[$i]['firstloadtime']=$iRes['attrs']['addtime'];
                    }else if($jField=='typesname'){
                        $tmpArr[$i][$jField]=$typesBuffer[$iRes['attrs']['typesid']]['TypesName'];
                    }else if($jField=='gradename'){
                        $tmpArr[$i][$jField]=$gradeBuffer[$iRes['attrs']['gradeid']]['GradeName'];
                    }else if($jField=='diff'){
                        $tmpArr[$i]['diff']=round($iRes['attrs']['diff'], 3);
                        $tmpArr[$i]['diffid']=R('Common/TestLayer/diff2Int',array($iRes['attrs']['diff'], $diff_array));
                        $tmpArr[$i]['diffstar']=R('Common/TestLayer/int2Html',array($tmpArr[$i]['diffid']));
                        $tmpArr[$i]['diffname']=$diff_array[$tmpArr[$i]['diffid']][0];
                        $tmpArr[$i]['diffxing']=R('Common/TestLayer/int2Xing',array($tmpArr[$i]['diffid']));
                    }else if($jField=='diffid'){
                        $tmpArr[$i]['diffid']=R('Common/TestLayer/diff2Int',array($iRes['attrs']['diff'], $diff_array));
                    }else if($jField=='kllist'){
                        $tmpArr[$i][$jField] = implode('<br/>',$klStudy->getKlList($iRes['attrs']['klid'],$knowledgeBuffer));
                    }else if($jField=='klnameall'){
                        $tmpArr[$i][$jField] = implode('<br/>',$knowledge->getKlAll($iRes['attrs']['klid'],$knowledgeBuffer));
                    }else if($jField=='klnameonly'){
                        $tmpArr[$i][$jField] = implode('<br/>',$knowledge->getKlOnly($iRes['attrs']['klid'],$knowledgeBuffer));
                    }else if($jField=='chapternameall'){
                        $tmpArr[$i][$jField] = implode('<br/>',$chapter->getChapterAll($iRes['attrs']['chapterid'],$chapterParent,$chapterList));
                    }else if($jField=='xtnum'){
                        if($iRes['attrs']['testnum']==0) $tmpArr[$i]['testnum'] = 1;
                        $tmpArr[$i][$jField] = $iRes['attrs']['testnum'];
                    }else if($jField=='subjectname'){
                        $tmpArr[$i][$jField] = $subjectBuffer[$iRes['attrs']['subjectid']]['ParentName'].$subjectBuffer[$iRes['attrs']['subjectid']]['SubjectName'];
                    }else if($jField=='specialname'){
                        $tmpArr[$i][$jField] = $specialBuffer[$iRes['attrs']['specialid']]['SpecialName'];
                    }else if($jField=='test'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['test'],1,$width,0,1,$iRes['attrs']['optionwidth'],$iRes['attrs']['optionnum'],$iRes['attrs']['testnum'],$iRes['attrs']['ifchoose'],1)));
                        $tmpArr[$i][$jField] =  $this->replaceKeywords($tmpArr[$i][$jField]);
                    }else if($jField=='testold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/replaceText',array(R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['test'],0,$width,0,1,$iRes['attrs']['optionwidth'],$iRes['attrs']['optionnum'],$iRes['attrs']['testnum'], $iRes['attrs']['ifchoose'])))));
                    }else if($jField=='answerold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['answer'],0,0,0,0,0,0,$iRes['attrs']['testnum'],0)));
                    }else if($jField=='analyticold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['analytic'],0,0,0,0,0,0,$iRes['attrs']['testnum'],0)));
                    }else if($jField=='remarkold'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs']['remark'],0,0,0,0,0,0,$iRes['attrs']['testnum'],0)));
                    }else if($jField=='answer' or $jField=='analytic' or $jField=='remark'){
                        $tmpArr[$i][$jField] = R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs'][$jField],1,0,0,0,0,0,$iRes['attrs']['testnum'],0,1)));
                    }else if($jField=='testsplit' || $jField=='answersplit' || $jField=='analyticsplit' || $jField=='remarksplit'){
                        $tmpArr[$i][$jField] = $test->formatToArray(R('Common/TestLayer/strFormat',array($test->formatTest($iRes['attrs'][substr($jField,0,-5)],0,0,0,0,0,0,$iRes['attrs']['testnum'],0))),$iRes['attrs']['testnum'],$iRes['id'], $jField=='testsplit'?true:false);
                    }else if($jField=='testnormal' or $jField=='answernormal' or $jField=='analyticnormal' or $jField=='remarknormal'){
                        $tmpArr[$i][$jField] = formatString('IPReturn',$iRes['attrs'][str_replace('normal','',$jField)],$host);
                    }else if($jField=='status'){
                        $tmpArr[$i]['status']=$iRes['attrs']['status'];
                    }else if($jField=='source'){
                        $tmpArr[$i]['docname']=$iRes['attrs']['source'];
                        unset($res['matches'][$i]['attrs']['source']);
                    }else if($jField=='testid' && $iRes['attrs']['testid']){
                        $tmpArr[$i]['testid'] = 'c'.$iRes['attrs']['testid'];
                    }else{
                        $tmpArr[$i][$jField]=$iRes['attrs'][$jField];
                        if($iRes['attrs']['testnum']==0) $tmpArr[$i]['testnum'] = 1;
                    }
                }
            }
        }
        $output[0]=$tmpArr;
        $output[1]=$res['total'];
        $output[2]=$this->page['perpage'];
        unset($res);
        unset($tmpArr);
        return $output;
    }
    /**
     * 删除索引
     * @param string $id 试题id
     * @param array $val 相关的值
     * @param array $attrs 操作的属性
     * @author demo
     * @return boolean|int
     */
    public function deleteIndex($ids, $val=array(10), $attrs=array('status')){
        $ids = explode(',', $ids);
        $list = array();
        foreach($ids as $id){
            $list[$id] = $val;
        }
        return $this->searcher->updateAttributes($this->indexName, $attrs, $list);
    }

    /**
     * 记录搜索引擎中的错误和警告信息
     * @return boolean 在发生错误时返回false
     * @author demo
     */
    protected function setIndexError(){
        $error = $this->searcher->getLastError();
        $warning = $this->searcher->getLastWarning();
        if($error || $warning){
            $description = empty($error) ? '索引警告信息：'.$warning : '索引错误信息：'.$error;
            $saveErrorModel = $this->getModel('LogError');
            $saveErrorModel->setLine(array(
                'description'=> $description
            ));
            if($error)
                return false;
        }
        return true;
    }

    /**
     * 关键字查询时去除停词
     * @param string $keyword 通过$keyword获取相关停词数据
     * @return string
     * @author demo
     * @date 2015-6-29
     */
    protected function filterStopWords($keyword){
        $indexName = explode(',', $this->indexName)[0];


        $keyword=preg_replace('/【小题】|【题号】/','',$keyword);

        $keyword=formatString('symbol',$keyword);

        //判断是否为英文查询英文比重在50%
        $tmp=preg_replace('/[a-z]*[A-Z]*/i','',$keyword);
        $isEnglish=0; //不是英语
        if(strlen($tmp)/strlen($keyword)<=0.5 && strstr($keyword,' ')){
            $segement = str_replace("\r\n",' ',$keyword);
            $segement = explode(' ',$segement);
            $isEnglish=1;
        }else{
            $tmp = $this->searcher->buildKeywords($keyword, $indexName, false);
            $segement=array();
            foreach($tmp as $iTmp){
                $segement[]=$iTmp['tokenized'];
            }
        }
        $oldKeyword=$keyword;
        $segement=array_filter($segement);

        if(count($segement) <= 1){
            $this->keywords = array($keyword);
            return $keyword;
        }
        $keywords=array();
        if(count($segement)>30){
            //去除单个的字
            foreach($segement as $i=>$iSegement){
                if(count($keywords)>50) break;
                if(mb_strlen($iSegement,'utf-8')>1){
                    if(!in_array($iSegement,$keywords)){
                        $keywords[]=$iSegement;
                    }
                }
            }
        }else{
            //去除长度小于2的关键字
            $keywords = array_filter($segement, function($v){
                $tc=array("的", "地", "得", "了", "呀", "吗", "啊","a", "the", "in", "on");
                if(in_array($v,$tc)){
                    return false;
                }
                return true;
            });
        }

        $keyword = array('');
        $keyword=array_merge($keyword,$keywords);

        $this->keywords = $keyword;
        $this->keywords[0]=mb_substr($oldKeyword,0,30,'utf-8');

        $jg='';
        if($isEnglish) $jg=' ';
        return implode($jg,$keyword);
    }

    /**
     * 替换文本中的关键字
     * @parma string $text 需替换的文本
     * @return string
     * @author demo
     */
    protected function replaceKeywords($text){
        if(count($this->keywords) == 0){
            return $text;
        }
        $mode = '<em class=indexKeywords>%s</em>';
        $keywords = $toString = array();
        foreach($this->keywords as $key=>$value){
            $value = preg_quote($value);
            $keywords[] = '/(?!<[^>]*)' . $value . '(?![^<]*>)/';
            $toString[] = sprintf($mode, $value);
        }
        return preg_replace($keywords, $toString, $text);
    }

    /**
     * 重置索引方法
     * @author demo
     */
    public function resetFilters(){
        $this->searcher->ResetFilters(); //重置方法
    }

    /**
     * 传入字段转换为小写
     * @author demo
     */
    private function _lowercaseField($field){
        if(empty($field) || !is_array($field)){
            return [];
        }
        return array_change_key_case($field,CASE_LOWER);
    }
}
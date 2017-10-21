<?php
/**
 * 混合试题查询model
 * @author demo
 * @date 2015-7-24
 */
namespace Test\Model;
class TestQueryModel{
    const DIVISION = 'c';    //查询区分字符
    public static $BOTH = 0; //同时查询两种
    public static $PUB = 1;  //仅查询公有库
    public static $PRI = 2;  //仅查询私有库
    /**
     * 查询相关结果集
     * @author demo
     */
    private static $instances = array();

    /*
     * 字段-类关系
    */
    private static $map = array(
        'TestDownload' => 'TestDownload',
        'Archive' => 'Archive',
        'TestJudge' => 'TestJudge',
        'TestAttr' => 'TestAttr',
        'TestNumVerify' => 'TestNumVerify'
    );


    private static $apiDb = null;

    public static function getInstance($className='Base'){
        if(is_null(self::$apiDb)){
            self::$apiDb = new \Common\Model\ApiDbModel();
        }
        //兼容代码
        if('Query' !== strstr($className, 'Query'))
            $className .= 'Query';
        $className = '\Test\Model\\'.$className;
        if(class_exists($className)){
            return new $className(self::$apiDb);
        }
        return null;
    }

    /**
     * 返回结果集，键为model名称（参见self::$map中的键）=>值为参数（ids值在此参数里），各个参数参见各个model
     *  $params = array(
     *      field为字符串时中间用逗号隔开，支持数组形式。
     *          针对某个查询类，可以这样写：xxx.TestID,JudgeID,...
     *          当使用默认值时：xxx. 在进行多个查询类时，该写法必须存在
     *      'field' => array('testid','klid','typesid','typesname','testnum','diff','docname','firstloadtime','judgeQ.'),
     *       page目前仅用于索引查询
     *       'page' => array('page'=>1,'perpage'=>100),
     *       'where' => array('UserID'=>$this->userid),
     *
     *       需要对每个查询按照某个指定值为键进行处理时，中间用逗号隔开，支持数组形式
     *       'convert' => 'testid, judgeQ.JudgeID', 
     *       'ids' => implode(',',$testList)
     *   );
    *   $buffer = \Test\Model\TestQueryModel::query($params);
     * @author demo
     */
    public static function query($params=array()){
        self::$instances = array();
        $fieldParams = self::fetch($params['field'], 'field'); //提取相关field信息
        $convert = self::fetch($params['convert'], 'convert'); //提取相关convert信息
        $ids = '';
        if(isset($params['ids'])){
            $ids = $params['ids'];
            unset($params['ids']);
        }
        while(list($modelName, $param) = each($fieldParams)){
            $obj = self::getInstance($modelName);
            if(is_null($obj)){
                continue;
            }
            $c = ''; //转换数据为指定的键值关系
            //如果指定convert参数则提供该参数
            if(isset($convert[$modelName])){
                $c = $convert[$modelName]['convert'][0];
            }
            //索引查询
            if('Base' === $modelName){
                $params['convert'] = $c;
                $params['field'] = $param['field'];
                $obj->setParams($params, $ids);
            }else{
                $obj->setParams(array(
                    'field' => implode(',', $param['field']),
                    'convert' => $c
                ), $ids);
            }
            self::$instances[$modelName] = $obj;
        }

        $data = array();
        //对各个model执行查询获取结果，为空的数据将在此处被过滤
        foreach(self::$instances as $name=>$instance){
            $data[$name] = $instance->getResult();
            if($data[$name] === false){
                return false;
            }
        }
        return self::combine($data, array_keys($fieldParams));
    }

    /**
     * 组合数据
     * @param array $result 各个类查询的数据
     * @param array $key 本次查询包含哪些数据
     * @return array 返回组合后的数据
     */
    private static function combine($result, $key){
        //是否包含索引查询
        $containIndex = in_array('Base', $key);
        //如果查询的数据包含索引同时包含小题表的数据，则对结果进行组合（小题表为testid=>array()的形式，参见TestJudgeQuery）
        if($containIndex && in_array('TestJudge', $key)){
            //此处合并小题数据
            foreach($result['Base'][0] as $key=>$value){
                $result['Base'][0][$key]['judge'] = array();
                foreach($result['TestJudge'] as $k=>$v){
                    if($value['testid'] == $v['TestID']){
                        $result['Base'][0][$key]['judge'][$k] = $v;
                    }
                }
            }
            unset($result['TestJudge']);
        }
        return $result;
    }

    /**
     * 根据字段信息提取来区分需要查询操作哪些Query
     * @param string|array $fields 当前查询的params信息提取
     * @param string $type params：'field, convert, where, order'
     * @return array 返回需要运用到的数据 
     *              array('Base'=>array
     */
    private static function fetch($params, $type){
        if(is_string($params)){
            $params = explode(',', $params);
        }
        //过滤所有空格
        foreach($params as $k=>$v){
            $params[$k] = preg_replace('/\s+/s', '', $v);
        }
        $result = array(); //用于设置字段信息
        foreach($params as $param){
            $relation = self::getRelation($param);
            if(!empty($relation)){
                list($m, $f) = $relation;
                $result[$m][$type][] = $f;
            }
        }
        return $result;
    }

    /**
     * 返回字段与对象关联的字段和类
     * @return array   array(类名称, 类字段)，如果不是一个合法的数据，则返回空数组
     */
    private static function getRelation($param){
        $m = strstr($param, '.', true);
        if(array_key_exists($m, self::$map)){
            return array(self::$map[$m], ltrim(strstr($param, '.'), '.'));
        }
        if($m === false){
            return array('Base', $param);
        }
        return array();
    }
}


/********************************************************************
 * 试题查询
 * @author demo
 * @date 2015-4-15
 */
class BaseQuery{

    //查询中必须提供的字段信息 string|array array('testid', 'xxx', ...);
    protected $requireField = 'testid';

    //试题id
    protected $id = array();
    //查询条件
    protected $params = array(
        'field'=>'*', 
        'where'=>array(), 
        'order'=>array(), 
        'page'=>array(), 
        'convert'=> false, //转换指定的字段为key，默认为false
        'limit' => 100
    );

    protected $mode = 0;

    protected $db = null;

    public function __construct($db){
        $this->db = $db;
    }

    public function setMode($mode){
        $this->mode = $mode;
    }

    /**
     * 设置查询参数，field,where,order,page参数参见TestRealModel和CustomTestModel
     * @param array 查询参数
     *          array(
     *              'field'=>array(xx,xx,xx),
     *              'where'=>array(),  库类型不同，查询参数有差异
     *              'order'=>array(),
     *              'page' => '',
     *              'convert' => false  //是否将数据转换为 testid=>value格式
     *          )
     *          where可能指
     *          SubjectID,DocID,DocTypeID,Diff,TypesID,KlID,ChapterID,SpecialID,key,width
     * @param string $ids，试题id
     * @return void
     * @author demo
     */
    public function setParams($params, $ids = ''){
        $this->params = array_merge($this->params, $params);
        $this->id = $ids;
        if(!empty($this->id)){
            $this->id = self::formatId($this->id);
        }
    }

    /**
     * 查询结果集
     * @param boolean $division 区分操作各个库，为true，将根据试题id来判定操作哪个库
     * @return array
     */
    public function getResult($division = false){
        if($division){
            if(!empty($this->id[0])){
                $this->mode = \Test\Model\TestQueryModel::$PUB;
            }else if(!empty($this->id[1])){
                $this->mode = \Test\Model\TestQueryModel::$PRI;
            }
        }
        $prvData = array();
        if(\Test\Model\TestQueryModel::$BOTH == $this->mode || \Test\Model\TestQueryModel::$PRI == $this->mode)
            $prvData = $this->getPrvList();
        if($prvData === false){
            return false;
        }
        if($this->params['convert']){
            $prvData = $this->convertToRelevance($prvData);
        }

        $pubData = array();
        if(\Test\Model\TestQueryModel::$BOTH == $this->mode || \Test\Model\TestQueryModel::$PUB == $this->mode)
            $pubData = $this->getPubList();
        if($pubData === false){
            return false;
        }
        if($this->params['convert']){
            $pubData = $this->convertToRelevance($pubData);
        }
        if($this->params['convert']){
            $result = $prvData + $pubData;
        }else{
            $result = array_merge($prvData,$pubData);
        }
        unset($preData, $pubData);
        $limit = 1;
        if($this->params['limit']){
            $limit = $this->params['limit'];
        }
        $result = array($result,count($result),$limit);
        return $result;
    }

    /**
     * 返回共有数据信息 参数参见setParams();
     * @param array $params 查询参数
     * @param string $ids ID信息
     * @return array() 
     */
    public function getPubList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        $pubModel = new \Test\Model\TestRealModel();
        $paramsCopy = $this->params; //赋值给临时的变量，保证分库查询的正确性
        $isEmpty = empty($this->id[0]);
        if(!$isEmpty){
            $paramsCopy['where']['TestID'] = implode($this->id[0], ',');
        }
        if(empty($paramsCopy['where']) || ($isEmpty && !empty($this->id[1]))){
            return array();
        }
        $list = $pubModel->getTestIndex(
            $paramsCopy['field'],
            $paramsCopy['where'],
            $paramsCopy['order'],
            $paramsCopy['page']
        );
        if($list === false){
            return false;
        }
        return $list[0];
    }

    /**
     * 返回自有数据信息  参数参见setParams();
     * @param array $params 查询参数
     * @param string $ids ID信息
     * @return array() 
     */
    public function getPrvList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);

        $pubModel = new \Custom\Model\CustomTestModel();
        $paramsCopy = $this->params; //赋值给临时的变量，保证分库查询的正确性
        $isEmpty = empty($this->id[1]);
        if(!$isEmpty){
            $paramsCopy['where']['TestID'] = implode($this->id[1], ',');
        }
        if(empty($paramsCopy['where']) || ($isEmpty && !empty($this->id[0]))){
            return array();
        }
        $list = $pubModel->getIndex(
            $paramsCopy['field'],
            $paramsCopy['where'],
            $paramsCopy['order'],
            $paramsCopy['page']
        );
        if($list === false){
            return false;
        }
        return $list[0];
    }

    /**
     * 处理试题id，返回一个索引数组，0为公共题库，1为私有题库
     * @return array (0=>array(1,2,3), 1=>array(3,4,5))
     */
    public static function formatId($ids){
        if(empty($ids)){
            return;
        }
        if(is_string($ids)){
            if(strpos($ids, ',') >= 0){
                $ids = explode(',', $ids);
            }else{
                $ids = array($ids);
            }
        }
        $list = array(
            0 => array(),  //共有试题id序列
            1 => array()   //私有试题id序列
        );
        foreach($ids as $id){
            list($id, $classify) = self::getTestIdInfo($id);
            if(empty($id)){
                break;
            }
            $list[$classify][] = $id;
        }
        return $list;
    }

    /**
     * 将结果转换为 试题id=>value形式
     * @param array $data 转换的数据
     * @return array
     */
    protected function convertToRelevance($data){
        $temp = array();
        $convert = $this->params['convert'];
        foreach($data as $value){
            $temp[$value[$convert]] = $value;
        }
        return $temp;
    }

    /**
     * 返回试题id信息
     * @param string $str id字符串
     * @return array array(编号, 类别);
     */
    private static function getTestIdInfo($str){
        if(strpos($str, \Test\Model\TestQueryModel::DIVISION) !== false){
            return array(str_replace(\Test\Model\TestQueryModel::DIVISION,'', $str), 1);
        }
        $str = preg_replace('/[^\d|,]/s', '', trim($str, ',')); //容错处理
        return array($str, 0);
    }

    /**
     * 每次查询必须获取的字段
     * @author demo
     * @date 
     */
    private function appendRequireField($field){
        if(is_string($field)){

        }
    }
}


/******************************************************************
 * 试卷下载查询model，需要用到的组合试卷的相关数据。同时可用于共有试题和私有试题的试题量验证
 *      该类将在查询前进行试题量验证;
 *      该类setParams()仅支持where试题id字符串查询："111,222,333"
 * @author demo
 * @date 2015-4-20
 */
class TestDownloadQuery extends BaseQuery{
    /**
     * 重写方法, 在题量验证有误的时候，该方法可能会返回字符串。
     * @return string|array
     */
    public function getResult($division = false){
        //查询下载试题前，进行题量验证
        $verify = new TestNumVerifyQuery();
        $verify->setParams(array(), array_merge($this->id[0], $this->id[1]));
        $verify = $verify->getResult();
        if($verify !== ''){
            return $verify;
        }
        $prvData = $this->getPrvList();
        if(is_string($prvData)){
            return $prvData;
        }
        if(!$prvData){
            $prvData = array();
        }
        $pubData = $this->getPubList();
        if(is_string($pubData)){
            return $pubData;
        }
        if(!$pubData){
            $pubData = array();
        }
        return $prvData + $pubData;
    }

    /**
     * 重写方法
     */
    public function getPrvList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[1])){
            return array();
        }
        $id = implode($this->id[1], ',');
        $customTest = new \Custom\Model\CustomTestModel();
        $result = $customTest->getDownloadData($id);
        return $result;
    }

    /**
     * 重写方法
     */
    public function getPubList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[0])){
            return array();
        }
        $id = implode($this->id[0], ',');
        $buffer = $this->db->selectData(
            'TestDoc',
            '*',
            'TestID in (' . $id . ')');
        
        $bufferTestDoc = array();
        if($buffer){
            foreach ($buffer as $iBuffer) {
                $bufferTestDoc[$iBuffer['TestID']] = $iBuffer;
            }
        }else{
            return array();
        } 
        unset($buffer);
        $buffer2 = $this->db->selectData(
            'TestAttrReal',
            'TestID,TestNum,OptionWidth,IfChoose,OptionNum',
            'TestID in (' . $id . ')');
        if($buffer2){
            foreach ($buffer2 as $iBuffer2) {
                $bufferTestDoc[$iBuffer2['TestID']] = array_merge($bufferTestDoc[$iBuffer2['TestID']],$iBuffer2);
            }
        }
        return $bufferTestDoc;
    }
}


/******************************************************************
 * 试卷试题量是否超出验证
 * @author demo
 * @date 2015-7-31
 */
class TestNumVerifyQuery extends BaseQuery{
    /**
     * 该方法仅返回验证结果
     * @return string 无错返回空字符串，或者返回错误信息
     */
    public function getResult($division = false){
        $prvData = $this->getPrvList();
        if(!empty($prvData)){
            return $prvData;
        }
        $pubData = $this->getPubList();
        if(!empty($pubData)){
            return $pubData;
        }
        return '';
    }

    public function getPrvList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[1])){
            return '';
        }
        $customTestAttr = new \Custom\Model\CustomTestAttrModel();
        $result = $customTestAttr->isOverLimit(implode($this->id[1], ','));
        if($result !== ''){
            return $result;
        }
        return '';
    }

    public function getPubList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[0])){
            return '';
        }
        $test = new \Test\Model\TestModel();
        $result = $test->checkTypeOver(implode($this->id[0], ','));
        if($result !== ''){
            return $result;
        }
        return '';
    }
}

/******************************************************************
 * 试卷存档混合查询model
 *      仅支持where试题id字符串查询："111,222,333"
 * @author demo
 * @date 2015-7-13
 */
class ArchiveQuery extends BaseQuery{
     /**
     * 重写方法
     */
    public function getPrvList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[1])){
            return array();
        }
        $field =  '`test`.TestID, `test`.Test, `test`.Answer, `test`.Analytic, `test`.Remark';
        $result = $this->db->customTestSelectById(implode(',', $this->id[1]), $field);
        $customTest = new \Custom\Model\CustomTestModel();
        foreach($result as $key=>$value){
            if($value['Test']){
                $result[$key]['Test'] = R('Common/TestLayer/replaceText',array($value['Test']));
            }
        }
        return $result;
    }

    /**
     * 重写方法
     */
    public function getPubList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[0])){
            return array();
        }
        return $this->db->testSelectById(implode(',', $this->id[0]));
    }
}


/******************************************************************
 * 混合试题小题查询model
 *      where:试题id字符串查询："111,222,333"
 *      filed:zj_test_judge与zj_custom_test_judge表中的有效字段，校本题库查询时将在id前添加'c'
 * @author demo
 * @date 2015-4-20
 */
class TestJudgeQuery extends BaseQuery{

    /**
     * 该类将直接返回BaseQuery->getResult() array(data, 0, 111)中的data;
     * @author demo
     * @date 
     */
    public function getResult($division = false){
        $result = parent::getResult($division);
        return $result[0];
    }

    public function getPrvList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[1])){
            return array();
        }
        $params = $this->params;
        $field = $params['field'];
        //验证是否给定TestID字段，并且在该字段前加'c'，以区分，
        $requireField = 'CONCAT("c",TestID) as TestID';
        if(empty($field) || $field === '*'){
            $field = 'JudgeID, OrderID, IfChoose, '.$requireField;
        }else if(strpos($field, 'TestID') === false){
            $field .= ','.$requireField;
        }else{
            $field = str_replace('TestID', $requireField, $field);
        }
        $result = $this->db->selectData(
            'CustomTestJudge',
            $field,
            'TestID IN('.implode($this->id[1], ',').')',
            'OrderID ASC'
        );
        if(!$result)
            return array();
        
        return $result;
    }

    public function getPubList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[0])){
            return array();
        }
        $params = $this->params;
        $field = $params['field'];
        if(empty($field)){
            $field = 'JudgeID, OrderID, IfChoose, TestID';
        }
        $result = $this->db->selectData(
            'TestJudge',
            $field,
            'TestID in (' .implode($this->id[0], ','). ')'
        );
        if(!$result)
            return array();
        return $result;
    }
}


/******************************************************************
 * 试题属性查询model
 *      仅支持where试题id字符串查询："111,222,333"
 *      filed:zj_custom_test_attr与zj_test_attr_real表中的有效字段，校本题库查询时将在id前添加'c'
 * @author demo
 * @date 2015-4-20
 */
class TestAttrQuery extends TestJudgeQuery{

    public function getPrvList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[1])){
            return array();
        }
        $field = $this->params['field'];
        $requireField = 'CONCAT("c",TestID) as TestID';
        if(empty($field) || $field === '*'){
            $field = 'TypesID, IfChoose, '.$requireField;
        }else if(strpos($field, 'TestID') === false){
            $field .= ','.$requireField;
        }else{
            $field = str_replace('TestID', $requireField, $field);
        }
        $result = $this->db->selectData(
                    'CustomTestAttr',
                    $field,
                    'TestID IN('.implode($this->id[1], ',').')'
                );
        if(!$result){
            return array();
        }
        return $result;
    }

    public function getPubList($params=array(), $ids=''){
        if(!empty($params) || !empty($ids))
            $this->setParams($params, $ids);
        if(empty($this->id[0])){
            return array();
        }
        $field = $this->params['field'];
        if(empty($field) || '*' == $field){
            $field = 'TypesID, IfChoose, TestID';
        }
        $result = $this->db->selectData(
            'TestAttrReal',
            $field,
            'TestID in (' .implode($this->id[0], ','). ')'
        );
        if(!$result){
            return array();
        }
        return $result;
    }
}
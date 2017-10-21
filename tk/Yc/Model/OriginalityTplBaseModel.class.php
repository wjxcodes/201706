<?php
/**
 * 原创模板基类
 * @author demo 2015-9-7
 */
namespace Yc\Model;
use Common\Model\BaseModel;
abstract class OriginalityTplBaseModel extends BaseModel{

    private $id = 0;  //主键id
    /*
        错误信息，后期错误可通过setErrorMessage()来模拟栈
    */
    private $err = array();

    /*
        分页信息：
        array(
            'page' => 'xx',
            'recordsNum' => 'xx'
        );
    */
    protected $pagtion = array(
        'page' => '1',
        'recordsNum' => '1'
    );

    /**
     * 当前查询的总记录数
     * @author demo 
     */
    protected $count = 0;

    /**
     * @param int $id 指定的主键id
     * @author demo 
     */
    public function __construct($id=0){
        parent::__construct();
        $this->id = (int)$id;
    }

    /**
     * 设置分页信息，使用model进行多个分页查询时，可能需要修改该分页信息
     * @param int|boolean $record 每页显示的记录数，当record=false时，不在进行分页操作
     * @param int $page 当前页数
     * @return void
     * @author demo 
     */
    public function setPagtion($record, $page=1){
        if($record === false){
            $this->pagtion = $record;
        }else{
            $this->pagtion['recordsNum'] = $record;
            $this->pagtion['page'] = $page; 
        }
    }

    /**
     * 清除分页信息
     * @return void
     * @author demo 
     */
    public function clearPagtion(){
        $this->pagtion = array();
    }

    /**
     * 返回当前model的主键id
     * @return int
     * @author demo 
     */
    public function getId(){
        return $this->id;
    }

    /**
     * 返回错误信息，始终返回第一个结果
     * @return string
     * @author demo 
     */
    public function getErrorMessage(){
        if(isset($this->err[0]))
            return $this->err[0];
        return '';
    }

    /**
     * 设置错误信息
     * @parmar string $error
     * @author demo 
     */
    public function setErrorMessage($error){
        if(!empty($error))
            array_unshift($this->err, $error);
    }

    /**
     * 数据插入
     * @param array $data
     * @return boolean 成功返回true
     * @author demo 
     */
    public function insert($data){
        $result = parent::insertData($data);
        if($result !== false){
            $this->id= $result;
            return true;
        }
        return $result;
    }

    /**
     * 数据更新
     * @return boolean 成功返回true
     * @author demo 
     */
    public function update($data){
        $result = parent::updateData($data, $this->getCondition());
        if($result === false){
            return false;
        }
        return true;
    }

    /**
     * 数据删除
     * @return boolean 成功返回true
     * @author demo 
     */
    public function delete(){
        $result = parent::deleteData($this->getCondition());
        if($result === false){
            return false;
        }
        return true;
    }

    /**
     * 单表数据查询，分页使用该类
     * @return array 如果需要分页返回的结果 array('data'=>array,'count'=>xx)
     * @author demo 
     */
    public function select($field, $where, $order='', $limit=''){
        $count = $this->dbConn->selectCount($field, $where);
        $rn = $this->pagtion['recordsNum'];
        $page = page((int)$count, $this->pagtion['page'], $rn);
        $limit = ($rn * ($page - 1)).','.$rn;
        return array(
            'data' => $this->dbConn->selectData($this->modelName, $field, $where, $order, $limit),
            'count' => $count
        );
    }

    /**
     * 返回当前的一条记录，用于单表操作
     * @return array
     * @author demo 
     */
    public function getOneRecord(){
        return $this->dbConn->findData($this->modelName, '*', $this->getCondition());
    }


    /**
     * 返回相关操作的条件，子类必须进行处理并且返回合法的条件语句
     * @param $type 操作的类型
     * @return array
     * @author demo 
     */
    protected abstract function getCondition($type='');
}
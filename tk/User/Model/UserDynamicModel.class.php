<?php
/**
 * 用户动态model，用于前台展示
 * @author demo 2015-8-11
 */
namespace User\Model;
use Common\Model\BaseModel;
class UserDynamicModel extends BaseModel{

    public static $dynamicType = array(
            'doc' => '试卷文档',
            'work' => '作业文档',
            'case' => '导学案文档',
        );
    //数据集
    private $data = array();

    //总记录数
    private $count = 0;

    //操作的错误信息
    private $error = '';

    //在此数组之内的值，允许外部赋值
    private $setter = array(
        'Title', 'Classification','UserName','SubjectID','IfShare'
    );

    public function __set($name, $val){
        if(in_array($name, $this->setter))
            $this->data[$name] = $val;
    }

    /**
     * 根据下载试题id关联
     * @parma int $id 外部数据编号
     * @return boolean|array 失败返回false，成功返回结果集
     */
    public function add($id){
        $this->data['AssociateID'] = $id;
        if(!isset($this->data['Title']) || empty($this->data['Title'])){
            $this->error = '描述不能为空！';
            return false;
        }
        if(!isset($this->data['Classification']) || empty($this->data['Classification'])){
            $this->error = '分类不能为空！';
            return false;
        }
        $this->data['AddTime'] = time();
        $result = $this->insertData(
            $this->data
        );
        if($result){
            $this->data['id'] = $result;
            return true;
        }
        $this->error = '数据保存失败！';
        return $this->data;
    }

    /**
     * 返回首页数据
     * @param $params 相关参数
     * @return array
     * @author demo 2015-12-31
     */
    public function getHomePageList($param=array()){
        if(empty($param['SubjectID'])){
            $param['SubjectID'] = 12;
        }
        if(empty($param['current'])){
            $param['currentPage'] = 1;
        }
        if(empty($param['prepage'])){
            $param['prepage'] = 6;
        }
        $userDynamicResult = $this->selectDataOfPagtion(
            '*',
            array(
                'CheckStatus'=>1,
                'IfShare'=>1,
                'SubjectID'=>$param['SubjectID']
            ),
            $param['currentPage'],
            $param['prepage']
        );
        $count = 0;
        foreach ($userDynamicResult as $i=>$val){
            $userDynamicResult[$i]['AddTime'] = date('Y/m/d' , $val['AddTime']);
            $userDynamicResult[$i]['UserName'] = formatString('hiddenUserName',$val['UserName']);
            $count++;
        }
        //记录不满足预定的数量是，将内容填充
        if($count < $param['prepage']){
            for(; $count<$param['prepage']; $count++){
                $userDynamicResult[] = array();
            }
        }
        return $userDynamicResult;
    }

    /**
     * 查询数据
     * @param string $field 指定的字段
     * @param array $where 指定的条件，键值数组
     * @param int $currentPage 当前页数
     * @param int $prepage 分页数
     * @return array
     */
    public function selectDataOfPagtion($field='*', $where=array(), $currentPage=1, $prepage=10){
        if(empty($where)){
            $where = '1=1';
        }else{
            $temp = array();
            while(list($k, $v) = each($where)){
                $temp[] = "{$k} = '{$v}'";
            }
            $where = implode(' AND ', $temp);
            unset($temp);
        }
        //该操作将直接查询数据库
        $limit = '';
        $order = 'AddTime DESC';
        if(!empty($this->data['id'])){
            $where .= ' AND UDID IN('.$this->data['id'].')';
        }else{ //执行分页操作
            if((int)$currentPage < 1){
                $currentPage = 1;
            }
            $count = $this->selectData(
                'count(UDID) as c',
                $where
            );
            $this->count = $count = (int)$count[0]['c'];
            $page = page($count, $currentPage, $prepage);
            $limit = (($page-1) * $prepage).','.$prepage;
        }
        $result = $this->selectData(
            $field,
            $where,
            $order,
            $limit
        );
        $this->data = array();
        foreach($result as $key=>$value){
            $this->data[$key] = $this->produce($value);
        }
        return $this->data;
    }
    /**
     * 返回产生的错误信息
     * @return string 
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 返回总页数
     * @author demo 
     */
    public function getCount(){
        return $this->count;
    }

    /**
     * 查出关联数据
     * @return array
     */
    private function produce($value){
        return $value;
    }
}
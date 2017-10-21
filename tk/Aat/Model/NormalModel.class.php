<?php
/**
 * @author demo
 * @date 2014年8月7日
 */
/**
 * 正态分布表的转换从v到p以及从p到v即能力值[0,1]和[-3,3]之间的转换
 */
namespace Aat\Model;

use Common\Model\BaseModel;
class NormalModel extends BaseModel{
    private $normalArray = array();

    /**
     * val转为pro即[-3,3]区间转为[0,1]区间
     * @param float $val Val值区间[-3,3]
     * @return float [0,1]区间的对应值
     * @author demo
     */
    public function val2Pro($val){
        if (!$this->normalArray) {
            $this->normalArray = $this->selectData(
                'val,pro',
                '1=1',
                'pro DESC'
            );
        }
        $arrayVal = $this->i_array_column($this->normalArray,'val');//待搜索的数组
        $arrayPro = $this->i_array_column($this->normalArray,'pro');//pro值组成的数组用于获取最后结果
        //查表得p值
        if ($val < 0) {
            $val = - $val;
            $i = $this->binarySearchKey($arrayVal,$val);//数组的键
            return 1 - $arrayPro[$i];
        } else{
            $i = $this->binarySearchKey($arrayVal,$val);//数组的键
            return $arrayPro[$i];
        }
    }

    /**
     * pro转为val即[0,1]区间转为[-3,3]区间
     * 待完成
     * @param float $pro Pro值区间[0,1]
     * @return float [-3,3]区间的对应值
     * @author demo
     */
    public function pro2Val($pro){

    }



    /**
     * 在数组中折半查找，如果找到则返回，如果找不到，返回最接近的值
     * @param array $array 待查找的数组
     * @param mixed $target 查找的目标，int或float
     * @return int 返回$array中目标值或相近的值对应的在数组中的位置（数组的键）
     */
    private function binarySearchKey($array, $target)
    {
        $high = count($array) - 1; //high指针
        $middle = 0; //middle指针
        $low = 0; //low指针
        $found = 0; //是否找到标志
        $i = 0; //计数器，进行了多少轮循环
        $result = 0;//找到的值在数组中的键

        //没有找到且low指针小于等于high指针时循环
        while (($low <= $high) && ($found == 0)) {
            $middle = floor(($low + $high) / 2);
            if ($target < $array[$middle])
                $high = $middle - 1;
            elseif ($target > $array[$middle])
                $low = $middle + 1;
            else {
                $found = 1;
                $result = $middle;
            }
            $i++;
        }
        if ($found == 0) {
            $result = abs($array[$middle] - $target) < abs($array[$middle + 1] - $target) ? $middle : $middle + 1;
        }
        return $result;//结果在数组中的键
    }
    
    /**
     * 兼容PHP5.5的array_column()函数
     * @param array $input 数组
     * @param  string $columnKey 数组键
     * @param null $indexKey
     * @return array
     */
    function i_array_column($input, $columnKey, $indexKey=null){
        if(!function_exists('array_column')){
            $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
            $indexKeyIsNull            = (is_null($indexKey))?true :false;
            $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
            $result                         = array();
            foreach((array)$input as $key=>$row){
                if($columnKeyIsNumber){
                    $tmp= array_slice($row, $columnKey, 1);
                    $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
                }else{
                    $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
                }
                if(!$indexKeyIsNull){
                    if($indexKeyIsNumber){
                        $key = array_slice($row, $indexKey, 1);
                        $key = (is_array($key) && !empty($key))?current($key):null;
                        $key = is_null($key)?0:$key;
                    }else{
                        $key = isset($row[$indexKey])?$row[$indexKey]:0;
                    }
                }
                $result[$key] = $tmp;
            }
            return $result;
        }else{
            return array_column($input, $columnKey, $indexKey);
        }
    }

}
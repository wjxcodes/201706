<?php
/**
 * 试题难度接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class DiffApi extends CommonApi{

    /**
     * 获取难度数据集
     * @return 难度数据集
     * @author demo
     */
    public function getDiffStringList(){
        $buffer = $this->getDiff();
        $output=array();
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                $output[$i][0]=$iBuffer[0];
            }
        }
        return $output;
    }

    /**
     * 返回难度配置文件数据
     * @author demo 16-4-11
     */
    public function getDiff(){
        return C('WLN_TEST_DIFF');
    }
}
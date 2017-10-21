<?php
/**
 * @author demo
 * @date 2014年8月12日
 */
/**
 * 章节知识点模型类，用于处理章节知识点相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class ChapterKlModel extends BaseModel{
    /**
     * 根据知识点查找对应章节；
     * @param array $kl_array 知识点id数组
     * @return array 
     * @author demo
     */
    public function getChapterByKl($kl_array){
        $buffer=$this->selectData(
            '*',
            'KID in ('.implode(',',$kl_array).')');
        if($buffer){
            $output='';
            foreach($buffer as $iBuffer){
                $output[]=$iBuffer['CID'];
            }
            return array_unique(array_filter($output));
        }
        return '';
    }
}
?>
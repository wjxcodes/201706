<?php
/**
 * @author demo 
 * @date 2014年8月4日
 * @update 2015年9月29日
 */
/**
 * 学校模型类，用于处理学校相关操作
 */
namespace Manage\Model;
use Common\Model\BaseModel;
class SchoolModel extends BaseModel{

    /**
     * 学校数组处理 仅用作地区联动显示学校
     * @param $areaId 地区ID
     * @return bool or array
     * @author demo
     */
    public function areaSchool($areaId,$all=0){
        $field='SchoolID,SchoolName';
        if($all==1) $field='*';
        $array=$this->selectData(
            $field,
            'AreaID='.$areaId,
            'SchoolID asc');
        if($array){
            $output=array();
            foreach($array as $i=>$iBuffer){
                if($all==1) $output[$i]=$iBuffer;
                $output[$i]['AreaID']=$iBuffer['SchoolID'];
                $output[$i]['AreaName']=$iBuffer['SchoolName'];
                $output[$i]['end']=1;
            }
            return $output;
        }else{
            return '';
        }
    }
    /**
     * 根据地区id获取地区下的学校数据； 待优化到前端
     * @param int $areaID 地区id
     * @param int $schoolID 学校id
     * @return string
     * @author demo
     */
    public function schoolSelectByID($areaID,$schoolID){
        $buffer2=$this->selectData(
            '*',
            'AreaID='.$areaID.' and Status=2 and Type<3');
        $schoolInit='(高中)';
        if($buffer2){
            $schoolInit='<select name="SchoolID" id="SchoolID">';
            $jibie='';
            foreach($buffer2 as $iBuffer2){
                $select='';
                if($iBuffer2['Type']==1) $jibie='(高中)';
                else if($iBuffer2['Type']==2) $jibie='(初中)';
                else if($iBuffer2['Type']==3) $jibie='(职高)';
                if($iBuffer2['SchoolID']==$schoolID) $select=' selected="selected" ';
                $schoolInit.='<option value="'.$iBuffer2['SchoolID'].'"'.$select.'>'.$iBuffer2['SchoolName'].$jibie.'</option>';
            }
            $schoolInit.='</select>';
        }
        return $schoolInit;
    }
    /**
     * 根据id获取学校
     * @param int $id 学校id
     * @return array 学校数据集
     * @author demo
     */
    public function getSchoolById($id){
        $buffer=$this->selectData(
            '*',
            'SchoolID='.$id);
        return $buffer[0];
    }
    
    /**
     * 查询学校信息
     * @param string $field 查询字段
     * @param string $where 查询条件
     * @param string $order 排序
     * @author demo
     */
    public function getSchoolFieldByAreaID($field='*',$where='Status=2 and Type<3',$order='SchoolID ASC'){
        return $this->selectData(
                $field,
                $where,
                $order
        );
    }
}
?>
<?php
/**
 * @author demo
 * @date 2015年2月2日
 */
/**
 * 智能组卷默认题型类，用于处理智能组卷默认题型相关操作
 */
namespace Ga\Model;
use Common\Model\BaseModel;
class TypesDefaultModel extends BaseModel{

    /**
     * 更新缓存；
     * @author demo
     */
    public function setCache(){
        $typesDefault=array();
        $typeArray = $this->selectData(
            '*',
            '1=1',
            'DefaultID asc');
        if($typeArray){
            foreach($typeArray as $i=>$iTypeArray){
                $areaList = explode(',',$iTypeArray['AreaID']);
                foreach($areaList as $j=>$jAreaList){
                    $typesDefault[$iTypeArray['SubjectID']][$jAreaList] = $iTypeArray['AreaID'];
                    $typesDefault[$iTypeArray['SubjectID']][$iTypeArray['AreaID']] =$iTypeArray;
                }
            }
        }
        S('typesDefaultSubject',$typesDefault);  //分学科地区默认智能组卷缓存
    }

    /**
     * 获取缓存
     * @param string $str 缓存名称
     * @param int $num 防止死循环
     * @return array
     * @author demo
     */
    public function getCache($str='typesDefaultSubject',$num=0){
        switch($str){
            case 'typesDefaultSubject':
                $buffer=S('typesDefaultSubject');  //分学科地区默认智能组卷缓存
                break;
            default:
                return false;
                break;
        }
        return $buffer;
    }
    /**
     * 获取默认题型信息
     * @param string $subjectID 学科ID
     * @param string $area 地区信息
     * @param string $area 地区信息
     * @param string $area 地区信息
     * @return string
     * @author demo
     */
    public function getTypeDefault($subjectID,$area,$areaChildList,$types){
        $areaID = '';
        $defaultArray = SS('typesDefaultSubject');
        $allArray=$defaultArray[$subjectID][0];

        if($allArray){
            $userTypes = $allArray;
            $typesArray = array();
            $typesArray['TypesID'] = explode('|',$userTypes['TypesID']);
            $typesArray['Num'] = explode('|',$userTypes['Num']);
            $typesArray['Score'] = explode('|',$userTypes['Score']);
            $typesArray['ChooseNum'] = explode('|',$userTypes['ChooseNum']);
            foreach($typesArray['TypesID'] as $i=>$iTypesArray){
                $result[$i] = $types[$iTypesArray];
                $result[$i]['Num'] = $typesArray['Num'][$i];
                $result[$i]['Score'] = $typesArray['Score'][$i];
                $result[$i]['IfDo'] = $typesArray['ChooseNum'][$i];
            }
            $output[4] = $typesArray;
        }else{
            foreach($areaChildList[0] as $i=>$iAreaChildList){
                if($area==''){
                    $area = '河南省郑州市';
                }
                if(strpos($area,str_replace(array('省','市'),'',$iAreaChildList['AreaName']))!==false){
                    $areaID = $iAreaChildList['AreaID'];
                    break;
                }
            }
            $areaIDAllStr=$defaultArray[$subjectID][$areaID];

            if(is_array($areaIDAllStr)){
                $userTypes=$areaIDAllStr;
            }else{
                $userTypes = $defaultArray[$subjectID][$areaIDAllStr];
            }

            if($userTypes){
                $typesArray = array();
                $result = array();
                $typesArray['TypesID'] = explode('|',$userTypes['TypesID']);
                $typesArray['Num'] = explode('|',$userTypes['Num']);
                $typesArray['Score'] = explode('|',$userTypes['Score']);
                $typesArray['ChooseNum'] = explode('|',$userTypes['ChooseNum']);
                $typesArray['IntelNum'] = explode('|',$userTypes['IntelNum']);
                foreach($typesArray['TypesID'] as $i=>$iTypesArray){
                    $result[$i] = $types[$iTypesArray];
                    $result[$i]['IntelNum'] = $typesArray['IntelNum'][$i];
                    $result[$i]['selectNum'] = $typesArray['Num'][$i];
                    $result[$i]['Score'] = $typesArray['Score'][$i];
                    $result[$i]['TypesID'] = $typesArray['TypesID'][$i];
                    $result[$i]['ChooseNum'] = $typesArray['ChooseNum'][$i];
                }
                $output[4] = $result;
            }
        }
        return $output[4];
    }
}
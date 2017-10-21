<?php
/**
 * 章节接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class ChapterApi extends CommonApi{
    /**
     * 获取章节子类id缓存
     * @return array $return 以章节id为键 值为子类id以逗号间隔
     * @author demo
     */
    public function chapterIDList(){
        return SS('chapterIDList');
    }
    /**
     * 获取章节缓存
     * @param array $param
     * ##参数格式:
     * $param = array(
     *      'style'=>'chapter',
     *      'pID'=>章节ID,//可选
     *      'haveLayer'=> 包含的层级数
     *      'subjectID'=> 学科ID
     *       //参数关系说明:如果有pID就不用传subjectID,否则必需传学科ID,当传pID时,如果不传入haveLayer或者haveLayer为假,就直接返回该章节的一层子类数据(不区分学科),除此,haveLayer的可选范围为1,2,3(此时区分学科)分别对应返回几层数据,当传subjectID时,不用传入haveLayer参数和pID此时直接返回该学科对应的章节数据
     * )
     * @return array $return
     * ##数据返回格式:
     * 当传入pID时 假设pID=2如果haveLayer=false
     * $return = array(
     *       [0] => Array//只获取一层,不获取子层
     *           (
     *              [ChapterID] => 3 //章节ID
     *              [ChapterName] => 第一章 集合 //章节名称
     *              [SubjectID] => 13 //学科ID
     *              [Last] => 0 //是否是最后一级 1是0否
     *          )
     *        ...
     * )
     * 当传入pID时 假设pID=111 如果haveLayer=1,只获取一层
     * $return = array(
     *      [0] => Array
     *           (
     *              [ChapterID] => 112 //章节ID
     *              [ChapterName] => 必修1 //章节名称
     *              [SubjectID] => 13 //学科ID
     *              [Last] => 0  //是否是最后一级 1是0否
     *           ),
     *          ...
     * )
     * 当传入pID时 假设pID=111 如果haveLayer=2,获取两层
     * $return = array(
    [0] => Array
    (
    [ChapterID] => 112
    [ChapterName] => 必修1
    [SubjectID] => 13
    [Last] => 0
    [sub] => Array //二级子类
    (
    [0] => Array
    (
    [ChapterID] => 113
    [ChapterName] => 第一章 集合与函数概念
    [SubjectID] => 13
    [Last] => 0
    ),
    [1] => Array
    (
    [ChapterID] => 117
    [ChapterName] => 第二章 基本初等函数（Ⅰ）
    [SubjectID] => 13
    [Last] => 0
    ),
    [2] => Array
    (
    [ChapterID] => 121
    [ChapterName] => 第三章 函数的应用
    [SubjectID] => 13
    [Last] => 0
    )
    )
     *              ),
     *              ...
     * )
     * 当传入pID时 假设pID=111 如果haveLayer=3,获取三层
     * $return = array(
    [0] => Array
    (
    [ChapterID] => 112
    [ChapterName] => 必修1
    [SubjectID] => 13
    [Last] => 0
    [sub] => Array//二级子类
    (
    [0] => Array
    (
    [ChapterID] => 113
    [ChapterName] => 第一章 集合与函数概念
    [SubjectID] => 13
    [Last] => 0
    [sub] => Array//三级子类
    (
    [0] => Array
    (
    [ChapterID] => 114
    [ChapterName] => 1.1 集合
    [SubjectID] => 13
    [Last] => 1
    ),
    [1] => Array
    (
    [ChapterID] => 115
    [ChapterName] => 1.2 函数及其表示
    [SubjectID] => 13
    [Last] => 1
    ),
    [2] => Array
    (
    [ChapterID] => 116
    [ChapterName] => 1.3 函数的基本性质
    [SubjectID] => 13
    [Last] => 1
    )
    )
    ),
     *                      ...
     *              )
     *          ),
     *          ...
     * )
     * ##
     * @author demo
     */
    public function chapterCache($param){
        extract($param);
        if($pID){
            //不指定层级 仅显示一级
            if(!$haveLayer){
                $buffer= SS('chapterChildList'); //根据父类ID获取对应子类 仅一层
                return $buffer[$pID];
            }
            //获取章节列表 显示层级数据
            $buffer=SS('chapterBySubjectN');

            $tmpBuffer=array();
            foreach($buffer as $iBuffer){
                foreach($iBuffer as $jBuffer){
                    if($jBuffer['ChapterID']==$pID){
                        $tmpBuffer=$jBuffer['sub'];
                    }
                }
            }

            if(empty($tmpBuffer)) return ;
            switch($haveLayer){ //需要获取知识点层级数，默认只获取1层
                case 1:
                    foreach($tmpBuffer as $i=>$iTmpBuffer){
                        unset($tmpBuffer[$i]['sub']);
                    }
                    return $tmpBuffer;
                    break;
                case 2:
                    foreach($tmpBuffer as $i=>$iTmpBuffer){
                        if($tmpBuffer[$i]['sub']){
                            foreach($tmpBuffer[$i]['sub'] as $j=>$jTmpBuffer){
                                unset($tmpBuffer[$i]['sub'][$j]['sub']);
                            }
                        }
                    }
                    return $tmpBuffer;
                    break;
                case 3:
                    return $tmpBuffer;
                    break;
            }
        }else{
            //获取版本
            $buffer=SS('chapterBySubjectN');
            foreach($buffer[$subjectID] as $i=>$iBuffer){
                unset($buffer[$subjectID][$i]['sub']);
            }
            return $buffer[$subjectID];
        }
    }

    /**
     * 返回章节缓存父类路径path数据
     * @author demo 16-4-11
     */
    public function chapterParentPath(){
        return SS('chapterParentPath');
    }

    /**
     * 返回缓存子类list数据
     * @author demo 16-4-11
     */
    public function chapterList(){
        return SS('chapterList');
    }
}
<?php
/**
 * 知识点接口
 * @author demo 2015-1-5
 */
namespace Common\Api;
class KnowledgeApi extends CommonApi{
    
    /**
     * 返回包含子知识点id的数组
     * @return array array(1=>'2,3,4,5','5'=>'7,8','9'=>'10,11,12,13',...)
     * @author demo 16-4-8
     */
    public function klList(){
        return SS('klList');
    }

    /**
     * 获取知识点列表以知识点id为键值
     * @return 以知识点id为键值数据集
     * @author demo
     */
    public function knowledge(){
        return SS('knowledge');
    }

    /**
     * 获取知识点
     * @param array $param
     * ##参数说明:
     * $param = array(
     *      'style'=>'knowledge',
     *      'pID'=>知识点ID//可选项,
     *      'haveLayer'=> 获取层级 //可选项
     *      'subjectID'=>学科ID //可选项
     *       //参数关系说明 如果有pID则 则不必传haveLayer和subjectID,否则必需传学科ID,haveLayer可以不传但是默认为1
     * )
     * ##
     * @return array $return
     * ##返回数据格式
     * 传pID时(程序里会判断pID变量的真假),获取此知识点ID的子类 如传入238
     * $return = Array
     *     (
     *           [0] => Array
     *           (
     *               [KlID] => 239  //知识点ID
     *               [KlName] => 字音 //知识点名称
     *               [SubjectID] => 12 //知识点所属学科
     *               [PID] => 238  //知识父类ID
     *               [OrderID] => 239 //排序
     *               [Frequency] => 1 //考频
     *               [IfTest] => 1 //是否加入测试 0否 1是
     *               [Style] => 2 //类型 1理科 2通用（默认） 3文科
     *               [IfInChoose] => 0 //该知识点是否是选考范围（0否，1是）
     *               [IfChoose] => 0 //使用范围 todo不同值的说明
     *               [Last] => 1 //是否是最后一级 1是0否
     *           ),
     *          ...
     *    )
     * pID为假时 必需传subjectID,haveLayer表示获取知识的层级,如参数处所说为空则取默认值1 haveLayer的可选值为1,2,3 故这里列举haveLayer等于1,2,3的情况,字段说明和传pID时是一样的,下面不再标注
     * 当haveLayer = 1(再次说明,也是haveLayer为空的情形)
     * $return = Array//子类是一维数组
     *       (
     *           [0] => Array
     *           (
     *                   [KlID] => 237
     *                   [KlName] => 语言知识基础
     *                   [SubjectID] => 12
     *                   [PID] => 0
     *                   [OrderID] => 237
     *                   [Frequency] => 1
     *                   [IfTest] => 1
     *                   [Style] => 2
     *                   [IfInChoose] => 0
     *                   [IfChoose] => 0
     *                   [Last] => 0
     *           ),
     *          ...
     *      )
     * 当haveLayer = 2时
     * $return = array(
     *      [0] => Array
     *              (
     *                  [KlID] => 237
     *                  [KlName] => 语言知识基础
     *                  [SubjectID] => 12
     *                  [PID] => 0
     *                  [OrderID] => 237
     *                  [Frequency] => 1
     *                  [IfTest] => 1
     *                  [Style] => 2
     *                  [IfInChoose] => 0
     *                  [IfChoose] => 0
     *                  [Last] => 0
     *                  [sub] => Array//多了sub键,表示二级子类
     *                  (
     *                      [0] => Array
     *                      (
     *                          [KlID] => 238
     *                          [KlName] => 语言知识运用
     *                          [SubjectID] => 12
     *                          [PID] => 237
     *                          [OrderID] => 238
     *                          [Frequency] => 1
     *                          [IfTest] => 1
     *                          [Style] => 2
     *                          [IfInChoose] => 0
     *                          [IfChoose] => 0
     *                           [Last] => 0
     *                       ),
     *                      ...//更多的二级子类
     *                   ),
     *                  ...//更多的一级子类
     *               )
     * )
     * 当haveLayer = 3时
     * $return = Array
     *  (
     *      [0] => Array
     *      (
     *              [KlID] => 237
     *              [KlName] => 语言知识基础
     *              [SubjectID] => 12
     *              [PID] => 0
     *              [OrderID] => 237
     *              [Frequency] => 1
     *              [IfTest] => 1
     *              [Style] => 2
     *              [IfInChoose] => 0
     *              [IfChoose] => 0
     *              [Last] => 0
     *              [sub] => Array//二级子类
     *              (
     *                  [0] => Array
     *                  (
     *                      [KlID] => 238
     *                      [KlName] => 语言知识运用
     *                      [SubjectID] => 12
     *                      [PID] => 237
     *                      [OrderID] => 238
     *                      [Frequency] => 1
     *                      [IfTest] => 1
     *                      [Style] => 2
     *                      [IfInChoose] => 0
     *                      [IfChoose] => 0
     *                      [Last] => 0
     *                      [sub] => Array//三级子类
     *                      (
     *                          [0] => Array
     *                          (
     *                             [KlID] => 239
     *                              [KlName] => 字音
     *                             [SubjectID] => 12
     *                             [PID] => 238
     *                             [OrderID] => 239
     *                             [Frequency] => 1
     *                             [IfTest] => 1
     *                             [Style] => 2
     *                             [IfInChoose] => 0
     *                             [IfChoose] => 0
     *                             [Last] => 1
     *                          ),
     *                         ...//可能更多的三级子类
     *                      )
     *                  ),
     *                 ...//可能更多的二级子类
     *      ),
     *       ...//可能更多的一级子类
     * )
     * ##
     * @author demo
     */
    public function knowledgeCache($param){
        extract($param);
        if($pID){
            $buffer=SS('knowledgeNext');
            return $buffer[$pID];
        }else{
            //兼容之前的，设置默认层级1层
            if(empty($haveLayer)){
                $haveLayer=1;
            }

            //根据学科返回对应层级数据集
            $buffer=SS('klBySubject3');
            if($buffer[$subjectID]){
                switch($haveLayer){ //需要获取知识点层级数，默认只获取1层
                    case 1:
                        foreach($buffer[$subjectID] as $i=>$iBuffer){
                            unset($buffer[$subjectID][$i]['sub']);
                        }
                        return $buffer[$subjectID];
                        break;
                    case 2:
                        foreach($buffer[$subjectID] as $i=>$iBuffer){
                            if($buffer[$subjectID][$i]['sub']){
                                foreach($buffer[$subjectID][$i]['sub'] as $j=>$jBuffer){
                                    unset($buffer[$subjectID][$i]['sub'][$j]['sub']);
                                }
                            }
                        }
                        return $buffer[$subjectID];
                        break;
                    case 3:
                        return $buffer[$subjectID];
                        break;
                }
            }
        }
    }

    /**
     * 所有知识点对应父类路径数据集缓存
     * @author demo 16-4-11
     */
    public function knowledgeParent(){
        return SS('knowledgeParent');
    }

    public function klBySubject3(){
        return SS('klBySubject3');
    }
}
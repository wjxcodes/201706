<?php
/**
 * @author demo
 * @date 2015年1月9日
 */
/**
 * 缓存调用Model类，用于处理缓存调用相关操作
 */
namespace Common\Model;
class ApiCacheModel{

    //获取所有数据验证
    public function checkPowerAll($code){
        $code1=md5(date('YmdH',time()).C('API_KEY').'Manage');
        $code2=md5(date('YmdH',(time()+3600)).C('API_KEY').'Manage');
        if($code!=$code1 && $code!=$code2){
            return 'error|非法数据';
        }
    }

    /**
     * 按学科ID获取对应能力值
     * @param array $param
     * ##参数格式:
     * $param = array(
     *     'style'=>'ability',
     *     'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *      [0] => Array
     *           (
     *              [AbID] => 8 //能力ID
     *              [AbilitName] => 基础知识题 //能力名称
     *              [OrderID] => 1 //排序ID
     *              [SubjectID] => 12 //学科ID
     *           ),
     *      ...//表示更多同结构数据,下同
     * )
     * ##
     * @author demo
     */
    public function abilityCache($param){
        extract($param);
        $buffer=SS('abilitySubject'); //根据学科ID获取对应能力属性
        return $buffer[$subjectID];
    }
    /**
     * 根据区域ID获取其子类地区
     * @param array $param
     * ##参数格式:
     * $param = array(
     *     'style'=>'area',
     *     'pID'=>区域ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *     [0] => Array
     *         (
     *            [AreaID] => 2 //区域ID
     *            [AreaName] => 东城区 //地区名称
     *            [Last] => 1   //是否是最后一级(是否包含子地区),1表示是最后一级,0表示包含不是最后一级
     *         ),
     *      ...
     *
     * );
     * ##
     * @author demo
     */
    public function areaCache($param){
        extract($param);
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Area'))->getAllData();
        }

        $buffer=SS('areaChildList'); //获取地区子类
        return $buffer[$pID];
    }
    /**
     * 根据地区ID获取学校
     * @param array $param
     * ##参数格式:
     * $param = array(
     *     'style'=>'areaToSchool',
     *     'pID'=> 区域ID, //此处做兼容处理,平时使用只传入areaID即可
     *     'areaID'=> 区域ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *       [0]=>'school',//只是做标志符使用
     *       [1]=>array(
     *          [0]=>array(
                        [AreaID] => 1 //学校主键ID
                        [AreaName] => 北京二十四中 //学校名称
                        [end] => 1 //固定值,1表示最后一级
     *               ),
     *          ...
     *       )
     * )
     * ##
     * @author demo
     */
    public function areaToSchoolCache($param){
        extract($param);
        //载入学校
        if($pID){
            $areaID=$pID;
        }
        $school=D(getMap('School'));

        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return $school->areaSchool($areaID,1);
        }

        $output=$school->areaSchool($areaID);
        return array('school',$output);
    }

    /**
     * 获取学科的栏目缓存
     * @param array $param 数组 带$subjectID 学科ID $forumID 栏目ID
     * ##参数格式:
     * $param = array(
     *      'style'=>'caseMenu',
     *      'subjectID'=>学科ID,
     *      'forumID'=>栏目ID //forumID为可选键,forum为空时,表示获取学科下所有的栏目缓存,否则表示获取学科下具体模块的栏目缓存
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *          [105] => Array //如你所见,键名对应MenuID
     *           (
     *              [MenuID] => 105  //栏目ID
     *              [SubjectID] => 12 //学科ID
     *              [ForumID] => 1    //模块ID
     *              [MenuName] => 学习目标 //栏目名称
     *              [OrderID] => 1 //排序
     *              [IfTest] => 0  //是否含有小题 0不含,1含
     *              [IfAnswer] => 0 //是否含有答案 0无,1有
     *              [NumStyle] => 0 //序号是否显示汉字 1显示汉字,0默认
     *           ),
     *           ...
     * )
     * 有无forumID,返回数据格式是一致的
     * ##
     * @author demo
     */
    public function caseMenuCache($param){
        extract($param);
        $buffer=SS('menuSubject'); //根据学科ID获取对应能力属性
        $menuForumSubject=SS('subjectForumMenu');
        if(!empty($forumID)){
            return $menuForumSubject[$subjectID][$forumID]; //返回学科对应大模块下的导学案栏目
        }
        return $buffer[$subjectID];
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
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Chapter'))->getAllData();
        }

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
     * 获取已选中章节路径数组或路径列表
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'chapterList',
     *    'ID'=>章节ID(多个章节ID以逗号间隔如:1,2)
     *    'parent'=> 章节缓存(父类缓存) //可选项,为空时,程序会自动获取
     *    'self'=>章节当前缓存 //可选项,为空时,程序会自动获取
     *    'buffer'=> 布尔值 bool 是否加入父类路径 //可选项,为空或者false时候,加入父类路径,为真时,只有当前章节路径
     *    'ReturnString'=> 是否返回字符串,并且返回数组转换为ReturnString作为间隔的字符串,如果为空,返回数组格式,此处,需要说明是否为空是使用empty函数进行判断的
     * )
     * ##
     * @return mixed $return
     * ##返回数据格式:
     * ①当ReturnString为空,返回数组
     * 对单章节ID 例如传入ID=5
     * $return = array(
     *         [0] => Array
     *           (
     *               [ChapterID] => 5 //章节ID,也是你传入的ID
     *               [ChapterName] => >>北师大版>>必修1>>第一章 集合>>1.2 集合的基本关系 //已选中章节的路径数组
     *           )
     * )
     * 多个章节 例如传入ID='5,10'
     * $return = array(
     *           [0] => Array
     *            (
     *              [ChapterID] => 5 //章节ID,把传入的参数ID='5,10'分割了
     *              [ChapterName] => >>北师大版>>必修1>>第一章 集合>>1.2 集合的基本关系//已选中的章节的路径数组
     *           ),
     *           [1] => Array
     *           (
     *              [ChapterID] => 10 //章节ID
     *              [ChapterName] => >>北师大版>>必修1>>第二章 函数>>2.3 函数的单调性//已选中章节的路径数组
     *           )
     * )
     * ②当ReturnString非空,我们这里假定ReturnString='Manage'
     * 对单章节ID 例如传入ID=5
     * $return = '(1)>>北师大版>>必修1>>第一章 集合>>1.2 集合的基本关系Manage';
     * 对多章节ID 例如
     * $return = '(1)>>北师大版>>必修1>>第一章 集合>>1.2 集合的基本关系Manage(2)>>北师大版>>必修1>>第二章 函数>>2.3 函数的单调性Manage'
     * 如上,当ReturnString非空时,只是把上面数组的[ChapterName]项进行了字符串连接处理,并且加入了ReturnString字符串
     * ##
     * @author demo
     */
    public function chapterListCache($param){
        if(!$param['ID']) return '';
        if(empty($param['parent'])){
            $param['parent']=SS('chapterParentPath');// 获取已选中的章节路径
        }
        if(empty($param['self'])){
            $param['self']=SS('chapterList');;
        }
        $output=$this->getDataClass('Chapter','getChapterPath',$param);
        return $output;
    }
    /**
     * 根据最后一个章节id获取章节所有列表数据 用于修改
     * @param array $param
     * ##参数说明:
     * $param = array(
     *     'style'=>'chapterParentList',
     *     'id'=>章节ID,//需注意这里是小写id
     *     'subjectID'=>学科ID
     * )
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *   [0] => Array //当前章节ID还有父类个数
     *         (
     *           [0] => 1
     *           [1] => 2
     *           [2] => 3
     *        ),
     *   [1] => Array
     *        (
     *           [0] => Array
     *               (
     *                   [ChapterID] => 954  //章节ID
     *                   [ChapterName] => 人教版 //章节名称
     *                   [SubjectID] => 12 //学科ID
     *                   [Last] => 0 //是否时章节最后一级,1是,0非
     *               ),
     *           [1] => Array
     *               (
     *                   [ChapterID] => 1045
     *                   [ChapterName] => 鲁教版
     *                   [SubjectID] => 12
     *                   [Last] => 0
     *               ),
     *            ...
     *   ),
     * ...
     * )
     * ##
     * @author demo
     */
    public function chapterParentListCache($param){
        extract($param);
        $output=array();
        if($id){
            $buffer=SS('chapterParentPath');  // 缓存父类路径数据
            $bufferChild= SS('chapterChildList'); //根据父类ID获取对应子类

            //反向排序
            $parentList=array();
            foreach($buffer[$id] as $i=>$iBuffer){
                $parentList[count($buffer[$id])-$i-1]=$iBuffer['ChapterID'];
            }
            $output[0]=$parentList;
            $output[1]=$this->chapterCache(array('subjectID'=>$subjectID));
            foreach($parentList as $i=>$iParentList){
                if($i!=count($buffer)-1) $output[]=$bufferChild[$iParentList];
            }
        }
        return $output;
    }
    /**
     * 获取文档类型缓存
     * @param array $param
     * ##参数说明:
     * $param = array(
     *     'style'='docType',
     *     'ID'=>文档类型ID //可选项,有ID时,表示该ID对应的文档类型缓存,否则获取全部缓存
     *     'field'=> //可选项,指定获取文档类型的字段,有ID时,默认值为'DefaultTest',无ID时,默认值为'TypeID,TypeName,GradeList'
     * )
     * ##
     * @return mixed $return
     * ##返回数据格式:
     * 有ID时,没有给field传值则获取默认值'DefaultTest'字段的值,否则获取所传字段的值,这里以默认字段举例
     * $return = 2;
     * 无ID时,没有给field传值则获取默认值'TypeID,TypeName,GradeList'字段的值,否则获取所传字段的值,这里也用默认字段举例
     * $return = array(
     *      [0] => Array
     *           (
     *               [TypeID] => 8 //文档类型ID
     *               [TypeName] => 月考试卷 //文档类型名称
     *               [GradeList] => 2,3,4,6,7,8 //所属年级
     *           ),
     *      [1] => Array
     *           (
     *               [TypeID] => 9
     *               [TypeName] => 期中试卷
     *               [GradeList] => 2,3,4,6,7,8
     *           )
     *     ...
     * )
     * ##
     * @author demo
     */
    public function docTypeCache($param){
        extract($param);
        $typeArray=SS('docType');

        //返回指定id的指定字段
        if($ID){
            if(empty($field)) $field='DefaultTest';
            return $typeArray[$ID][$field];
        }

        if($ifDel){
            unset($typeArray[12]); //去掉自助招生
        }

        //返回以默认数字为键的数组
        if($typeArray){
            $k=0;
            if(empty($field)) $field='TypeID,TypeName,GradeList';
            $fieldArray=explode(',',$field);
            foreach($typeArray as $i=>$iTypeArray){
                foreach($fieldArray as $j=>$jFieldArray){
                    $output[$k][$jFieldArray]=$iTypeArray[$jFieldArray];
                }
                $k++;
            }
        }
        return $output;
    }

    /**
     * 获取难度属性
     * $param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'diff'
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *      [0] => Array
     *           (
     *               [DiffID] => 1 //难度id
     *               [DiffName] => 容易 //难度名称
     *               [DiffArea] => 0.801-0.999 //难度系数区间
     *           )
     *
     *       [1] => Array
     *           (
     *               [DiffID] => 2
     *               [DiffName] => 较易
     *               [DiffArea] => 0.601-0.800
     *           )
     *      ...
     *
     * )
     * ##
     * @author demo
     */
    public function diffCache(){
        $buffer = C('WLN_TEST_DIFF');

        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return $buffer;
        }

        $bufferX = array ();
        foreach ($buffer as $i => $iBuffer) {
            $bufferX[] = array (
                'DiffID' => $i,
                'DiffName' => $iBuffer[0],
                'DiffArea' => $iBuffer[3].'-'.$iBuffer[4]
            );
        }
        return $bufferX;
    }

    /**
     * 获取固定数据函数
     * @notice 此函数为内部函数,不做外部文档 @
     * @param string $modelName 模型名称
     * @param string $functionName 处理方法名称
     * @param array $paramArray 参数数组
     * @return mixed
     * @author demo
     **/
    public function getDataClass($modelName,$functionName,$paramArray){
        $cacheMsg=D(getMap($modelName))->$functionName($paramArray);
        return $cacheMsg;
    }
    /**
     * 获取导学案栏目
     * @param array $param
     * ##参数说明:
     * $param = array(
     *     'style'=>'getForumMenu',
     *     'subjectID'=>学科ID,
     *     'forumID'=>模块ID
     * )
     * ##
     * @return mixed $return
     * ##返回数据格式:
     * $return = array(
     *       [0] => Array
     *           (
     *               [MenuID] => 105 //栏目ID
     *               [SubjectID] => 12 //学科ID
     *               [ForumID] => 1 //模块ID
     *               [MenuName] => 学习目标 //栏目名称
     *               [OrderID] => 1 //排序
     *               [IfTest] => 0 //是否有试题,0无,1有
     *               [IfAnswer] => 0 //是否有答案,0无,1有
     *               [NumStyle] => 0 //序号是否显示汉字,1汉字 0默认
     *           ),
     *      ...
     *
     * )
     * 需要注意,$return 可能为空
     * ##
     * @author demo
     */
    public function getForumMenuCache($param){
        extract($param);
        $menuSubject=SS('menuSubject');
        $subjectMenu=$menuSubject[$subjectID];
        foreach($subjectMenu as $i=>$iSubjectMenu){
            if($subjectMenu[$i]['ForumID']==$forumID){
                $resultMenu[]=$subjectMenu[$i];
            }
        }
        return $resultMenu;
    }
    /**
     * 获取年级对应学科信息
     * @param $param array
     * ##参数说明:
     * $param = array(
     *     'style'=>'getGradeSubject',
     *     'gradeID'=>年级ID
     * )
     * ##
     * @return array $return
     * ##返回数组格式
     * $return = array(
     *     [0] => Array
     *           (
     *               [SubjectID] => 12 //学科ID
     *               [SubjectName] => 语文 //学科名称
     *           ),
     *      [1] => Array
     *           (
     *               [SubjectID] => 13
     *               [SubjectName] => 数学
     *           ),
     *     ...
     * )
     * ##
     * @author demo
     */
    public function getGradeSubjectCache($param){
        $gradeID   = $param['gradeID'];
        $buffer    = SS('grade');;
        $subjectID = $buffer[$gradeID]['SubjectID'];
        $buffer    = SS('subject');
        $output    = array();
        $j=0;

        foreach($buffer as $i=>$iBuffer){
            if($subjectID==$iBuffer['PID']){
                $output[$j]['SubjectID']=$i;
                $output[$j]['SubjectName']=$iBuffer['SubjectName'];
                $j++;
            }
        }

        return $output;
    }

    /**
     * 根据学科id获取多个关于学科的数据 能力、知识点、章节等等
     * @param array $param 数组 带参数subjectID list多个模型以逗号间隔
     * ##参数说明:
     * $param = array(
     *      'style'=>'getMoreData',
     *      'subjectID'=>学科ID,
     *      'list'=>学科的数据列表 //如 'chapter,caseMenu' 表示学科的章节信息和导学案菜单
     *      'idList'=>学科数据列表对应的ID,这里键值和数据列表字符串是都应的
     *      //如list="knowledgeList,chapterList,special,types,knowledge,chapter" 则idList=>array(
    "knowledgeList":知识ID,"chapterList":章节ID,"special":专题ID...) 需要说明,idList并非和list一一对应的,需要看所调用的缓存是否需要ID参数,例如 假如调用special缓存不需要专题ID则idList中就不用写此键了
     *      //你也可以全局搜索'getMoreData'看之前的例子
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * 本函数返回格式,根据list而定,所以详细格式要结合其他缓存,但是都是以list为键的,如传入了'special,chapterList',以此类推
     * $return = array(
     *      'special'=>array(),//专题对应的缓存,具体格式请查看专题缓存的说明
     *      'chapterList'=>array()//章节列表对应的缓存,具体格式请查看章节列表缓存的说明
     * )
     * ##
     * @author demo
     */
    public function getMoreDataCache($param){
        extract($param);
        $list=explode(',',$list);
        $output=array();
        foreach($list as $i=>$iList){
            $iLists=$iList.'Cache';
            $output[$iList]=$this->$iLists(array('gradeID'=>$gradeID,'subjectID'=>$subjectID,'ID'=>$idList[$iList]));
        }
        return $output;
    }

    /**
     * 获取单个缓存
     * @notice 内部函数 不做外部文档 @
     * @param array $param 所含键值'cacheName' 缓存名称
     * @return array
     * @author demo
     */
    public function getSingleCache($param){
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
        }
        extract($param);
        $output=S($cacheName);
        if($output)  return $output;
        $modelName='';
        $list['ability']='Ability';
        $list['abilitySubject']='Ability';
        $list['subject']='Subject';
        $list['subjectParent']='Subject';
        $list['subjectParentId']='Subject';
        $list['system']='System';
        $list['areaParentPath']='Area';
        $list['areaChildList']='Area';
        $list['areaList']='Area';
        $list['areaIDList']='Area';
        $list['chapterParentPath']='Chapter';
        $list['chapterChildList']='Chapter';
        $list['chapterList']='Chapter';
        $list['chapterIDList']='Chapter';
        $list['chapterBySubjectN']='Chapter';
        $list['grade']='ClassGrade';
        $list['gradeListSubject']='ClassGrade';
        $list['examType']='DirExamType';
        $list['docType']='DocType';
        $list['docChapterOrder']='DocType';
        $list['klList']='Knowledge';
        $list['knowledge']='Knowledge';
        $list['knowledgeParent']='Knowledge';
        $list['knowledgeNext']='Knowledge';
        $list['klBySubject3']='Knowledge';
        $list['SkillList']='Skill';
        $list['Skill']='Skill';
        $list['SkillParent']='Skill';
        $list['SkillNext']='Skill';
        $list['SkillBySubject3']='Skill';
        $list['CapacityList']='Capacity';
        $list['Capacity']='Capacity';
        $list['CapacityParent']='Capacity';
        $list['CapacityNext']='Capacity';
        $list['CapacityBySubject3']='Capacity';
        $list['menu']='Menu';
        $list['menuList']='Menu';
        $list['powerAdminList']='PowerAdminList';
        $list['powerAdminListTag']='PowerAdminList';
        $list['powerAdmin']='PowerAdmin';
        $list['powerUserByID']='PowerUserList';
        $list['powerUserList']='PowerUserList';
        $list['powerUserTag']='PowerUserList';
        $list['powerUserGroup']='PowerUser';
        $list['powerUserId']='PowerUser';
        $list['special']='Special';
        $list['specialParent']='Special';
        $list['specialTree']='Special';
        $list['testMark']='TestMark';
        $list['testMarkSubject']='TestMark';
        $list['types']='Types';
        $list['typesSubject']='Types';
        $list['typesDefaultSubject']='TypesDefault';
        $list['pregError']='ProcessErrorPreg';
        $list['menuSubject']='CaseMenu';
        $list['caseMenu']='CaseMenu';
        $list['subjectForumMenu']='CaseMenu';
        $list['expList']='UserExp';
        $list['levelList']='UserLevel';
        $list['powerByLevel']='UserLevelValue';
        $list['powerByID']='UserLevelValue';
        $list['docSource']='DocSource';
        $list['Skill']='Skill';
        $list['Capacity']='Capacity';
        if(!$list[$cacheName]) return false;


        D(getMap($list[$cacheName]))->setCache();
        $output=S($cacheName);
        return $output;
    }

    /**
     * 获取学科缓存
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'getSubject',
     *    'subjectID'=>学科ID //可选项,为空时,返回所有学科缓存,否则返回该学科下的缓存
     * )
     * @return array $return
     * ##返回数据格式:
     * 无学科ID
     * $return = array(
     *        [2] => Array //键值为学科ID
     *           (
     *               [SubjectID] => 2 //学科ID
     *               [SubjectName] => 高中 //学科名称
     *               [ParentName] =>  //该学科父类名称
     *               [PID] => 0 //父类ID
     *               [Style] => 1 //文理科属性 0无 1文 2理
     *               [TotalScore] => 150 //总分
     *               [TestTime] => 120 //测试时间
     *               [ChapterSet] => 0 //章节对应关系 0对应知识点 1对应知识点和关键字
     *               [FormatDoc] => 0 //针对图片和公式格式化doc文档（0检测公式，本行自适应 1公式自适应 2图片垂直居中）
     *               [MoneyStyle] => 0 //学科支付方法   0按题 1按套卷
     *               [PayMoney] => 0 //支付金额
     *          ),
     *      [12] => Array
     *          (
     *              [SubjectID] => 12
     *              [SubjectName] => 语文
     *              [ParentName] => 高中
     *              [PID] => 2
     *              [Style] => 3
     *              [TotalScore] => 150
     *              [TestTime] => 120
     *              [ChapterSet] => 1
     *              [FormatDoc] => 0
     *              [MoneyStyle] => 0
     *              PayMoney] => 1.5
     *          )
     *      ...//其他学科
     * )
     * 有学科ID 如学科ID为12
     * $return = Array
     *      (
     *          [SubjectID] => 12
     *          [SubjectName] => 语文
     *          [ParentName] => 高中
     *          [PID] => 2
     *          [Style] => 3
     *          [TotalScore] => 150
     *          [TestTime] => 120
     *          [ChapterSet] => 1
     *          [FormatDoc] => 0
     *          [MoneyStyle] => 0
     *          [PayMoney] => 1.5
     *      )
     * ##
     * @author demo
     */
    public function getSubjectCache($param){
        extract($param);
        $cache = SS('subject');
        if($subjectID != ''){
            if(array_key_exists($subjectID,$cache)) {
                return $cache[$subjectID];
            }
            return array();
        }
        return $cache;
    }

    /**
     * 获取指学科ID的获取对应阶段的年级
     * @param array $param
     * ##参数格式:
     * $param = array(
     *     'style'=>'grade',
     *     'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式
     * $return =Array
     *    (
     *       [0] => Array
     *       (
     *           [GradeID] => 2  //年级ID
     *           [GradeName] => 高一 //年级名称
     *           [SubjectID] => 2 //指定学科的父类ID
     *           [OrderID] => 99 //排序
     *       ),
     *       [1] => Array
     *       (
     *           [GradeID] => 3
     *           [GradeName] => 高二
     *           [SubjectID] => 2
     *           [OrderID] => 99
     *       ),
     *       [2] => Array
     *       (
     *           [GradeID] => 4
     *           [GradeName] => 高三
     *           [SubjectID] => 2
     *           [OrderID] => 99
     *       )
     *   )
     * ##
     * @author demo
     */
    public function gradeCache($param){
        extract($param);
        $classGrade=SS('gradeListSubject');

        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('ClassGrade'))->getAllData();
        }

        if($getAllGrade==1) return SS('gradeList');

        $subjectArr=SS('subject');
        return $classGrade[$subjectArr[$subjectID]['PID']]['sub'];

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
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Knowledge'))->getAllData();
        }

        if($pID){
            $buffer=SS('knowledgeNext');
            if(empty($buffer[$pID])) return array();
            return $buffer[$pID];
        }else{
            //兼容之前的，设置默认层级1层
            if(empty($haveLayer)){
                $haveLayer=1;
            }

            //根据学科返回对应层级级数据集
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
            return array();
        }
    }
    //同knowledgeCache
    public function capacityCache($param){
        extract($param);
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Capacity'))->getAllData();
        }

        if($pID){
            $buffer=SS('CapacityNext');
            if(empty($buffer[$pID])) return array();
            return $buffer[$pID];
        }else{
            //兼容之前的，设置默认层级1层
            if(empty($haveLayer)){
                $haveLayer=1;
            }

            //根据学科返回对应层级级数据集
            $buffer=SS('CapacityBySubject3');
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
            return array();
        }
    }
    //同knowledgeListCache
    public function skillListCache($param){
        if(!$param['ID']) return '';
        if(empty($param['self'])){
            $param['self']=SS('Skill');
        }
        if(empty($param['parent'])){
            $param['parent']=SS('SkillParent');
        }
        $output=$this->getDataClass('Skill','getPath',$param);
        return $output;
    }

    //同knowledgeCache
    public function skillCache($param){
        extract($param);
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Skill'))->getAllData();
        }

        if($pID){
            $buffer=SS('SkillNext');
            if(empty($buffer[$pID])) return array();
            return $buffer[$pID];
        }else{
            //兼容之前的，设置默认层级1层
            if(empty($haveLayer)){
                $haveLayer=1;
            }

            //根据学科返回对应层级级数据集
            $buffer=SS('SkillBySubject3');
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
            return array();
        }
    }
    //同knowledgeListCache
    public function capacityListCache($param){
        if(!$param['ID']) return '';
        if(empty($param['self'])){
            $param['self']=SS('Capacity');
        }
        if(empty($param['parent'])){
            $param['parent']=SS('CapacityParent');
        }
        $output=$this->getDataClass('Capacity','getPath',$param);
        return $output;
    }
    /**
     * 根据已选中的知识点ID，获取对应知识点路径列表
     * @param array $param 数组 $ID(单个知识点ID，或以逗号间隔的多个知识点ID)
     * ##参数说明:
     * $param = array(
     *     'style'=>'knowledgeList',
     *     'ID'=>知识点ID,多个知识点ID用逗号间隔
     *     'self'=>知识点当前缓存 //可选项,为空时,程序会自动获取
     *     'parent'=>知识点缓存(父类缓存) //可选项,为空时,程序会自动获取
     *     'ReturnString'=> 是否返回字符串,并且返回数组转换为ReturnString作为间隔的字符串,如果为空,返回数组格式,此处,需要说明是否为空是使用empty函数进行判断的
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * ①当ReturnString为空,返回数组
     * 对单知识点ID 例如传入ID=1792
     * $return = array(
     *       [0] => Array
     *           (
     *              [KlID] => 1792//知识点ID
     *              [KlName] => >>集合与常用逻辑用语>>集合>>集合的含义与表示//知识点路径列表
     *          )
     * )
     * 对多个知识点ID='1792,1793'
     * $return = array(
     *           [0] => Array
                    (
                        [KlID] => 1792
                        [KlName] => >>集合与常用逻辑用语>>集合>>集合的含义与表示
                    ),
                 [1] => Array
                    (
                        [KlID] => 1793
                        [KlName] => >>集合与常用逻辑用语>>集合>>集合间的基本关系
                    )
     * )
     * ②当ReturnString非空,我们这里假定ReturnString='Manage'
     * 对单知识点ID 例如传入ID=1792
     * $returan = '(1)>>集合与常用逻辑用语>>集合>>集合的含义与表示Manage';
     * 对多个知识点ID 例如传入ID='1792,1793'
     * $returan = '(1)>>集合与常用逻辑用语>>集合>>集合的含义与表示Manage(2)>>集合与常用逻辑用语>>集合>>集合间的基本关系Manage';
     * ##
     * @author demo
     */
    public function knowledgeListCache($param){
        if(!$param['ID']) return '';
        if(empty($param['self'])){
            $param['self']=SS('knowledge');
        }
        if(empty($param['parent'])){
            $param['parent']=SS('knowledgeParent');
        }
        $output=$this->getDataClass('Knowledge','getKnowledgePath',$param);
        return $output;
    }

    /**
     * 根据所在年级段ID，获取该阶段对应学科
     * @param array $param
     * ##参数格式:
     * $param = array(
     *     'style'=>'subject',
     *     'pID'=>年级段阶段ID(暂时有高中(主要)和初中)
     * )
     * ##
     * @return array $return
     * ##返回格式说明
     * $return = array(
     *     [0] => Array
                (
                    [SubjectID] => 12 //学科ID
                    [SubjectName] => 高中语文 //学科名称
                    [PID] => 2  //学科父类
                    [OrderID] => 99 //排序
                    [Style] => 3 //属性 0无 1文科 2理科 3文理通用
                    [TestTime] => 120 //测试时间
                    [TotalScore] => 150 //总分
                    [FontSize] => 0 //生成word字体大小 0默认
                    [ChapterSet] => 1 //章节对应关系 0对应知识点 1对应知识点和关键字
                    [FormatDoc] => 0 //针对图片和公式格式化doc文档（0检测公式，本行自适应 1公式自适应 2图片垂直居中）
                    [MoneyStyle] => 0 //学科支付方法   0按题 1按套卷
                    [PayMoney] => 1.5 //支付金额
                ),
     *      ...//更多学科
     * )
     * ##
     * @author demo
     */
    public function subjectCache($param){
        extract($param);

        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Subject'))->getAllData();
        }

        if($gradeID){
            $gradeBuffer=SS('grade');
            if($gradeBuffer[$gradeID]['SubjectID']) $pID=$gradeBuffer[$gradeID]['SubjectID'];
        }

        $buffer=SS('subjectParentId'); //获取学科树状图
        if(empty($pID)) return SS('subjectParent'); //返回学段
        return $buffer[$pID]['sub'];
    }
    /**
     * 根据学科ID获取对应专题
     * @param array $param
     * ##参数格式说明:
     * $param = array(
     *     'style'=>'special',
     *     'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *       [0] => Array
     *          (
     *          [SpecialID] => 7 //主题ID
     *          [SpecialName] => 新定义问题 //专题名称
     *          [SubjectID] => 13 //学科ID
     *          [PID] => 0  //专题父ID
     *          [OrderID] => 0 //排序
     *          ),
     *       [1] => Array
     *          (
     *              [SpecialID] => 40
     *              [SpecialName] => 选择题
     *              [SubjectID] => 13
     *              [PID] => 0
     *              [OrderID] => 1
     *              [sub] => Array//子类
     *               (
     *                  [43] => Array
     *                  (
     *                      [SpecialID] => 43
     *                      [SpecialName] => 直接法
     *                      [SubjectID] => 13
     *                      [PID] => 40
     *                      [OrderID] => 11
     *                  ),
     *                  [44] => Array
     *                  (
     *                      [SpecialID] => 44
     *                      [SpecialName] => 特例法
     *                      [SubjectID] => 13
     *                      [PID] => 40
     *                      [OrderID] => 12
     *                  ),
     *                  [45] => Array
     *                  (
     *                      [SpecialID] => 45
     *                      [SpecialName] => 排除法
     *                      [SubjectID] => 13
     *                      [PID] => 40
     *                      [OrderID] => 13
     *                  ),
     *                  [46] => Array
     *                  (
     *                      [SpecialID] => 46
     *                      [SpecialName] => 图像法
     *                      [SubjectID] => 13
     *                      [PID] => 40
     *                      [OrderID] => 14
     *                  ),
     *                  [47] => Array
     *                  (
     *                      [SpecialID] => 47
     *                      [SpecialName] => 估算法
     *                      [SubjectID] => 13
     *                      [PID] => 40
     *                      [OrderID] => 15
     *                  )
     *              )
     *           ),
     *          ...
     * )
     * 需要说明:返回数据格式可能时键值为0的结构,也可能键值为1的结构,也可能上面两种结构一起
     * ##
     * @author demo
     */
    public function specialCache($param){
        extract($param);
        $buffer=SS('specialTree'); //根据学科ID获取对应的专题
        return $buffer[$subjectID];
    }
    /**
     * 根据学科ID获取题型
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'types',
     *    'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array(
     *        [0] => Array
     *               (
     *                  [TypesID] => 79 //体型ID
     *                  [TypesName] => 选择题 //体型名称
     *                  [SubjectID] => 13 //学科ID
     *                  [OrderID] => 1 //排序
     *                  [Num] => 30 //最大体型数量
     *                  [Volume] => 1 //所在试卷 1卷1 2卷2
     *                  [DScore] => 5 //默认分值 默认1
     *                  [MaxScore] => 10 //最大分值 默认1
     *                  [TypesScore] => 2//试题分值类型；按小题计分1 按大题计分2
     *                  [IfSingle] => 0//题型是否对应单选（0是，1否）
     *                  [IfSearch] => 0//是否设置默认在本题型搜题参数（0是，1否）
     *                  [IfDo] => 0 //题型是否有选做题（0是，1否）
     *                  [IfChooseType] => 1 //题型是否有选择类型（0是，1否）
     *                  [IfChooseNum] => 1 //题型是否选择小题（0是，1否）
     *                  [TypesStyle] => 1 //默认题型类型（默认3，1选择题，2选择非选择混合，3非选择题）
     *                  [IfPoint] => 0 //题型是否需要0.5分（1是，0否 默认）
     *                  [SelectType] => 1 //选题方式 默认0忽略小题数量 1按小题数量 关联intelNum
     *                  [ScoreNormal] => 0 //试题任务加分值 默认 0
     *                  [ScoreIntro] => 0 //试题入库加分值 默认0
     *                  [ScoreMiss] => 0 //放弃标引扣分值 默认0
     *                  [ScorePic] => 0 //图片版加分值 默认0
     *                  [IntelName] => 题 //取单位（用于智能组卷题型选取，值可为个，篇等,默认值为题）
     *                  [IntelNum] => Array //
     *                      (
     *                         [0] => 0
     *                      )
     *                      //IntelNum说明:选取数量（默认0）用于智能组卷,选题方式为'是'时在智能组卷选题方式显示每'题'(选取单位)多少小题,逗号间隔的数字为数量可选项,例如：5,15,25 （英文逗号）关联SelectType
     *              ),
     *              ...
     * )
     * ##
     * @author demo
     */
    public function typesCache($param){
        extract($param);

        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('Types'))->getAllData();
        }

        $buffer=SS('typesSubject'); //根据学科ID获取对应的题型
        return $buffer[$subjectID];
    }

    /**
     * 获取题型选取单位
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'typesIntel',
     *    'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式:
     * $return = array
     *        (
     *           [79] => 题 //题型选取单位
     *           [80] => 题 //题型选取单位
     *           [81] => 题 //题型选取单位
     *        )
     * ##
     * @author demo
     */
    public function typesIntelCache($param){
        $subjectTypes=$this->typesCache($param);
        $intelNameArray=array();
        foreach($subjectTypes as $i=>$iSubjectTypes){
            $intelNameArray[$iSubjectTypes['TypesID']]=$iSubjectTypes['IntelName'];
        }
        return $intelNameArray;
    }

    /**
     * 获取指定学科ID的自定义打分属性
     * @param array $param
     * ##参数说明:
     * $param = array(
     *    'style'=>'testMark',
     *    'subjectID'=>学科ID
     * )
     * ##
     * @return array $return
     * ##返回数据格式
     * $return = array(
     *      [0] => Array
     *      (
     *          [MarkID] => 20 //标记ID
     *          [MarkName] => 知识点数 //标记名称
     *          [SubjectID] => 13 //学科ID
     *          [MarkList] => #1|1-2个知识点#2|3个知识点#3|>3个知识点#//标记内容#分值|描述#分值|描述#
     *          [OrderID] => 99 //排序
     *          [Style] => 0 //类型 0主要难度 1辅助难度
     *      ),
     *      ...
     * )
     * ##
     * @author demo
     */
    public function testMarkCache($param){
        extract($param);
        if(!empty($code)){
            $result=$this->checkPowerAll($code);
            if(strstr($result,'error')!=false){
                return $result;
            }
            return D(getMap('TestMark'))->getAllData();
        }

        $testMark=SS('testMarkSubject');
        return $testMark[$subjectID];
    }


    /**
     * 获取指定提取码的试卷信息、答题卡图片、答题卡坐标
     * @param string $savecode 提取码
     * @return array 数据信息
     * array('Paper'=>试卷信息,'Img'=>答题卡图片,'Cut'=>答题卡切割数据)
     *  试卷信息['Test'=>[
     *              [0]=>试题信息
     *              [1]=>试题信息
     *              [2]=>试题信息
     *              ]
     *          'Cookie'=>'数据cookie数据 可以判断ab卷',
     *          'SubjectID'=>学科id]
     *      ]
     *  答题卡图片 [
     *      0=>[0=>http://xxx/aaa.jpg,1=>http://xxx/aaa1.jpg] //a卷
     *      1=>[0=>http://xxx/aaa.jpg,1=>http://xxx/aaa1.jpg] //b卷
     * ]
     *  答题卡坐标 [array(
                ['OrderID'=>0,
                    'Style'=>4,默认0考号 1缺考 2客观题 3主观题 4页面标示
                    'TestOrderStat'=>0,
                    'TestOrderEnd'=>0,
                    'TestSmallID'=>0,
                    'IfChooseTest'=>0,
                    'Coordinate'=>[
                            0=>[ //a卷
                                0=>[
                                    'x'=>'11,11',
                                    'y'=>'22,22',
                                    'sheet'=>1
                                ]
                                1=>[
                                    'x'=>'11,11',
                                    'y'=>'22,22',
                                    'sheet'=>1
                                ]
                            ]
                            1=>[ //b卷
                                0=>[
                                    'x'=>'11,11',
                                    'y'=>'22,22',
                                    'sheet'=>1
                                ]
                                1=>[
                                    'x'=>'11,11',
                                    'y'=>'22,22',
                                    'sheet'=>2
                                ]
                            ]
                        ]
                    ],
     * @author demo
     */
    public function paperContentCache($param){
        extract($param);
        $result=$this->checkPowerAll($code);
        if(strstr($result,'error')!=false){
            return $result;
        }

        $output =array(); //返回数据

        $docSave=D(getMap('DocSave'));
        $buffer=$docSave->getDataByCode($savecode);

        //提取试卷
        $result=$docSave->paperContentByCode($buffer['TestList']);
        if($result[0]==0){
            return 'error|'.$result[1];
        }
        $output['Paper']=[
            'Test'=>$result[1],
            'Cookie'=>$buffer['CookieStr'],
            'SubjectID'=>$buffer['SubjectID']
        ];

        //判断试卷是不是ab卷
        $cookieArray=R('Common/Doc/formatPaperCookie',array($buffer['CookieStr']));
        $ifAb=0; //不是ab卷
        if($cookieArray['attr'][2]==2){
            $ifAb=1;
        }

        //提取答题卡
        $doc=D(getMap('Doc'));
        $result=$doc->answerPdfImg($buffer['SaveID'],$ifAb);
        if($result[0]==0){
            return 'error|'.$result[1];
        }
        $output['Img']=$result[1];

        //答题卡坐标
        $result=$doc->answerCoordinate($buffer['SaveID'],$ifAb);
        if($result[0]==0){
            return 'error|'.$result[1];
        }
        $output['Cut']=$result[1];
        return $output;
    }

    /**
     * 获取指定答题卡坐标信息
     * @param string $code 提取码
     * @return array
     * @author demo
     */
    public function answerContentCache($param){
        extract($param);
        $result=$this->checkPowerAll($code);
        if(strstr($result,'error')!=false){
            return $result;
        }
    }
}
?>
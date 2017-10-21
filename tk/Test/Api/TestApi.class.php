<?php
/**
 * 试题接口
 * @author demo 2015-1-5
 */
namespace Test\Api;;
use Common\Api\CommonApi;
class TestApi extends CommonApi{

    /**
     * @改变数组的键
     * @param array $arr 试题数据集
     * @param string $demo 字段对应关系例如：
     * array(
     *      'testid'=>'LoreID',
     *      'testold'=>'Lore',
     *      'answerold'=>'Answer'
     * )
     * @return array 试题数据集
     * @author demo
     */
    public function changeArrayKey($arr,$demo=array()){

    }
    /**
     * 分割分值到小题上
     * @param $score int 总分值
     * @param $testNum int 小题数量
     * @return string
     * @author demo
     */
    public function gaCutScore($score,$testNum){
        //判断使用整数还是小数表达分值；
        $ifPoint=0; //默认不用小数标示
        if($score < $testNum)
            $ifPoint=1;
        $tmpScore=$score / $testNum; //平均分
        $tmpScore=number_format($tmpScore,$ifPoint); //保留一位小数

        //计算分值到小题
        $tmpScore2=array();
        for($k=0;$k < $testNum - 1;$k++){
            $tmpScore2[]=$tmpScore;
        }
        $tmpScore2[]=$score - $tmpScore * ($testNum - 1);

        return implode(',',$tmpScore2);
    }

    /**
     * 根据参数获取索引数据 参数同查询索引
     * @param string $field 字段  如judge表数据请传递judge字段 返回数据为与TestID并列的字段['Judge'][]
     * @param array $where 查询条件
     * @param array $order 排序信息
     * @param array $page 分页信息
     * @param int $auto 0查询全部数据  1查询系统 2查询个人
     * @param int $convert 是否转换结构数组(转化为试题id为索引的数组)  -1仅合并 0不转换不合并 , 1 转换不合并 , 2 转换并合并
     * @return array
     * 返回共有题库及私有题库的试题
     * @author demo 16-5-24
     */
    public function getAllIndexTest($field,$where,$order,$page,$auto=0,$convert=0){
        return $this->getModel('TestRealQuery')->getIndexTest($field,$where,$order,$page,$auto,$convert);
    }

    /**
     * 试题索引
     * @param array $field 返回字段
     * @param array $where 条件 array('DocID'=>文档id（支持逗号间隔）,
     *                                         'TestID'=>试题id（支持逗号间隔）
     *                                          'maxtestid' => 不能超过试题id
     *                                          'testfilter' => 为1时则排除试题id
     *                                         'Diff'=>难度（数据类型1-5五段）
     *                                         'DocTypeID'=>文档类型
     *                                         'TestNum'=>小题数
     *                                         'TestStyle'=>试题类型
     *                                         'TypesID'=>题型id（支持逗号间隔）
     *                                         'TypeFilter'=>题型id排除，为1时排除TypesID的题型
     *                                         'SubjectID'=>学科id（支持逗号间隔）
     *                                         'SpecialID'=>专题id（支持逗号间隔）
     *                                         'GradeID'=>年级id（支持逗号间隔）
     *                                         'KlID'=>知识点id（支持逗号间隔）
     *                                         'ChapterID'=>章节id（支持逗号间隔）
     *                                         'LastTime'=>按时间查询（正数 时间--当前；负数 0-时间）
     *                                         'key'=>按字符串进行查询
     *                                         'width'=>试题的内容选项宽度
     *                                         'field'=>试题需要查询的字段 必须有key
     *                                         'searchStyle'=>查询类型 如果有key 默认any任何关键字匹配 normal全部关键字匹配
     *                                         'Duplicate'=>重复字段 0不重复
     *                                         'ShowWhere'=>0组卷端 1通用 2提分端 3前台禁用
     *                                         );
     * @param array $order 排序 array('字段1 DESC','字段2 ASC',...)
     * @param array $page 分页信息 array('page'=>当前页,'perpage'=>'每页几个');
     * @return array 返回数组    array(0=>试题数据集,1=>总数量,2=>每页数量);
     *         可用于返回数据数组
     *         array（
     *             'testid'=>试题id
     *             'docid'=>所属文档id
     *             'typeid'=>所属文档类型id
     *             'docname'=>所属文档名称
     *             'docyear'=>所属文档年份
     *             'numbid'=>所属文档试题编号
     *             'typesid'=>所属题型id
     *             'typesname'=>所属题型名称
     *             'subjectid'=>学科id
     *             'subjectname'=>学科名字
     *             ‘specialid'=>专题id
     *             ‘specialname'=>专题名字 //未做
     *             'test'=>题文被table分割选项后的字符串并且序号化
     *             'testold'=>题文被table分割选项后的字符串并且标签化
     *             'testnormal'=>题文未被处理
     *             'answer'=>答案序号化
     *             'answerold'=>答案标签化
     *             'answernormal'=>答案未被处理
     *             'analytic'=>解析序号化
     *             'analyticold'=>解析标签化
     *             'analyticnormal'=>解析未被处理
     *             'remark'=>解析序号化
     *             'remarkold'=>解析标签化
     *             'remarknormal'=>解析未被处理
     *             'firstloadtime'=>第一次入库时间（格式 ：年/月/日）
     *             'firstloadtimeint'=>第一次unix入库时间
     *             'loadtime'=>最近一次入库时间（格式 ：年/月/日）
     *             'loadtimeint'=>最近一次unix入库时间
     *             'testnum'=>小题数量
     *             'diff'=>难度值（3为小数）
     *             'diffid'=>难度id（1-5共五档）
     *             'diffstar'=>难度数据段标示（例如：0.001-0.300）
     *             'diffname'=>难度名称
     *             'diffxing'=>难度html星星标示（需要css）
     *             'mark'=>打分细则
     *             'kllist'=>知识点列表带知识点视频
     *             'klnameall'=>知识点名称路径
     *             'klnameonly'=>知识点名称
     *             'ifchoose'=>试题类型（0非选择题 1复合体 2多选 3单选）
     *             'times'=>试题下载次数
     *             'admin'=>管理员
     *             'specialname'=>专题名称
     *             'chapternameall'=>章节名称路径
     *             'gradename'=>年级
     *             'TestStyle'=>试题类型
     *             'OptionWidth'=>选项宽度
     *             'OptionNum'=>选项数量
     *         ）；
     * @author demo
     **/
    public function getTestIndex($field,$where,$order,$page,$openBackIndex=0){
        return $this->getModel('TestReal')->getTestIndex($field,$where,$order,$page,$openBackIndex);
    }

    /**
     * 将数据转换为以testid为key的数组
     * @param array $arr 试题数据集
     * @param string $field 字段
     * @param string $pre 前缀
     * @param int $ifArray 是否数组 0适合单个testid 1适用于多个相同的testid
     * @return array 试题数据集
     * @author demo
     */
    public function reloadTestArr($arr,$field='testid',$pre='',$ifArray=0){
        $output=array();
        if($arr && is_array($arr)){
            foreach($arr as $iArr){
                if($ifArray == 1){
                    $output[$pre.$iArr[$field]][]=$iArr;
                }
                else{
                    $output[$pre.$iArr[$field]]=$iArr;
                }
            }
        }

        return $output;
    }

    /**
     * 获取制定个数不重复的随机整数数据数组
     * @param array $arr 返回的数组
     * @param int $length 指定的长度
     * @param int $max 随机数最大值
     * @return array
     * @author demo
     */
    public function getRandArr($arr,$length,$max){
        //取长度为0的数组
        if($length <= 0)
            return array();

        //取长度大于总长度
        if($length > $max){
            for($i=0;$i < $max;$i++){
                $arr[]=$i;
            }

            return $arr;
        }

        //随机去指定长度数组
        $num=rand(0,$max - 1);
        if(!in_array($num,$arr))
            $arr[]=$num;
        if(count($arr) == $length){
            return $arr;
        }
        else{
            return $this->getRandArr($arr,$length,$max);
        }
    }
    /**
     * 获取小题数量
     * @param string $str 字符串
     * @param string $style 默认1获取小题题号数量   2获取小题数量  3分割小题  4分割题号
     * @return string|int 返回值 0无小题或题号
     * @author demo
     */
    public function xtnum($str,$style=1){
        return  $this->getModel('Test')->xtnum($str,$style);
    }

    /**
     * 通过索引和数据库查询获取试题测试信息
     * @param int $recordId 测试记录ID
     * @param int $ifShowID 是否返回id号
     * @return array 整个测试的试题信息和作答信息
     * @author demo
     */
    public function getTestAnswerByIndex($recordId,$ifShowID=0) {
        return  $this->getModel('TestReal')->getTestAnswerByIndex($recordId,$ifShowID);
    }


    /**
     * 通过索引和数据库查询获取试题测试信息
     * @param int $sendID 作业测试记录ID
     * @return array 整个作业测试的试题信息和作答信息
     * @author demo
     */
    public function getHomeworkByIndex($sendID){
        return  $this->getModel('TestReal')->getHomeworkByIndex($sendID);
    }

    /**
     * 通过索引获取试题内容byid
     * @param int $testID 试题ID
     * @param int $recordID 答题记录id
     * @param int $isHomeWork 是否是作业
     * @param int $ifAnswer 是否返回答案解析
     * @return array 单个试题的内容
     * @author demo
     */
    public function getTestByID($testID,$recordID=0,$isHomeWork=0,$ifAnswer=0){
        return  $this->getModel('TestReal')->getTestByID($testID,$recordID,$isHomeWork,$ifAnswer);
    }
    /**
     * 描述：根据IDs获取知识和试题数据
     * @param string $IDStr 正常试题 不加字母；校本题库 用字母c开头；后台加的 知识用字母l开头；用户加的 知识用字母u开头
     * @param array $testField  索引需要查询的字段
     * @param int $sendID 作业ID，存在如果，则需要格式化试题部分（home false；aat true）
     * @return array 知识和试题的数组，键为IDs
     * @author demo
     */
    public function getCaseContentData($IDStr,$testField,$sendID=0) {
        return  $this->getModel('TestReal')->getCaseContentData($IDStr,$testField,$sendID);
    }

    public function getJudgeByIDs($arr){
        return $this->getModel('TestReal')->getJudgeByIDs($arr);
    }
}
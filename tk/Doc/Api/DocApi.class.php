<?php
/**
 * 文档接口
 * @author demo 2015-1-5
 */
namespace Doc\Api;
use Common\Api\CommonApi;
class DocApi extends CommonApi{
    /**
     * 根据cookie数据下载word文档
     * @param array @param 参数
     * @param['subjectID']=$subjectID; //学科id
     * @param['cookieStr']=$cookieStr; //内容
     * @param['isSaveRecord']=$isSaveRecord; //是否存档
     * @param['docVersion']=$docVersion; //文档类型
     * @param['paperSize']=$paperSize; //纸张大小
     * @param['paperType']=$paperType; //试卷类型
     * @param['backType']=0; //是否仅返回路径 0不返回 1返回
     * @param['testList']=$testList; //试题id字符串以英文逗号间隔
     * @param['docName']=$docName; //文档名称
     * @param['downStyle']=$downStyle; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
     * @return array
     * @author demo
     */
    public function getDownUrlByCookie($param,$userName){
        extract($param);

        //验证试题数量限制
        $testRealQuery=$this->getModel('TestRealQuery');
        $limitTest=$testRealQuery->checkAndLogTypeLimit($testList);
        if($limitTest[0]==='false'){
            return $limitTest; //您选择的试题超出最大限制！
        }

        //获取试题内容
        $data=$testRealQuery->getTestArrayByID($testList);
        if($data[0]==='false'){
            return $data; //您选择的试题已不存在！请选择试题后组卷！
        }

        //加入参数
        $paperStyleArray=array(
            'normal'=>'普通用卷',
            'teacher'=>'教师用卷',
            'student'=>'学生用卷',
            'week'=>'周练专用',
        );
        $paperStyle='普通用卷';
        if($paperType){
            $paperStyle=$paperStyleArray[$paperType];
        }

        $doc=$this->getModel('Doc');
        $cookieParam = $doc->getTestContent($cookieStr, $data);

        $cookieParam['issaverecord']=$isSaveRecord;
        $cookieParam['docversion']=$docVersion;
        $cookieParam['docstyle']=$paperSize;
        $cookieParam['doctype']=$paperType;
        $cookieParam['ifShare']=$ifShare;
        $cookieParam['ifAnswer']=$ifAnswer;
        $cookieParam['subjectID']=$subjectID; //增加学科判断英语 做特殊处理

        if(!$docName){
            $docName=$cookieParam['title_main'][1].'('.$paperStyle.')';
        }

        //组合试卷
        $str = $doc->setDocCon($cookieParam);

        //$str=preg_replace('/o:gfxdata="[^"]*"/im','',$str);

        //除去单个<v:shape />
        preg_match_all('/<v:shape[^\/>]*\/>/im',$str,$tmpArr);
        if(!empty($tmpArr[0])){
            foreach($tmpArr[0] as $iTmpArr){
                if(strstr($iTmpArr,'position:absolute')) $str=str_replace($iTmpArr,'',$str);
            }
        }

        //去除<v:shape中的position:absolute
        $tmpArr=explode('</v:shape>',$str);
        $newStr='';
        $thisArr=array();
        foreach($tmpArr as $i=>$iTmpArr){
        $tmpArr2=explode('<v:shape',$iTmpArr);
        $len=count($tmpArr2);
        if($len>1 && strstr($tmpArr2[$len-1],'position:absolute')){
            unset($tmpArr2[$len-1]);
            $newStr.=implode('<v:shape',$tmpArr2);
        }else{
            $newStr.=$iTmpArr;
            if($i!=count($tmpArr)-1) $newStr.='</v:shape>';
        }
        $str = $newStr;
        //exit(print_r($thisArr));
}
        //记录组卷次数
        $this->getModel('User')->conAddData(
            'Times=Times+1',
            'UserName="'.$userName.'"'
        );

        //系统数据统计使用数量
        $idList=R('Common/TestLayer/cutIDStrByChar',array($testList,1)); //切割字母开头的字符串id为数组
        if($idList[0]){
            $testDown = $this->getModel('TestDown');
            $testDown->setDownTime($idList[0]);
            unset($testDown);
        }

        $param=array();
        $param['downStyle']=$downStyle; // 区分下载作业还是试卷 答题卡4 导学案3 作业2，试卷1
        $param['str']=$str;
        $param['docName']=$docName;
        $param['docVersion']=$docVersion;
        $param['docStyle']=$paperSize;
        $param['docType']=$paperType;
        $param['isSaveRecord']=$isSaveRecord;
        $param['backType']=$backType;
        $param['subjectID']=$subjectID;
        $param['cookieStr']=$cookieStr;
        $param['ifShare']=$ifShare;
        //生成试卷
        return $doc->createDocCon($param,$userName);
    }

    /**
     * 获取doc索引
     * @param $field array 获取字段数组 例如 array(0=>'docid',1=>'docname')
     * @param $where array 查询条件数组 例如 array('DocID'=>'1,2,3','DocYear'=>2013)
     * @param $order array 排序数组 例如 array(0=>'@docid DESC',1=>'IntroTime DESC')
     * @param $page array 分页信息数组        perpage 每页几个 page当前页数 limit 返回总数最大数量
     * @return array 0:doc数据 1:数据总数 2:每页几个
     */
    public function getDocIndex($field,$where,$order,$page){
        return $this->getModel('Doc')->getDocIndex($field,$where,$order,$page);
    }
    /**
     * 试卷被测试的次数自增
     * 之后可以增加参数，自增其它字段
     * @param int $docID 试卷ID
     * @author demo
     */
    public function addTestTimes($docID) {
        return $this->getModel('DocTestData')->addTestTimes($docID);
    }
}
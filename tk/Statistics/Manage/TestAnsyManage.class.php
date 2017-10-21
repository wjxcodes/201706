<?php
/**
 * @author demo
 * @date 2014-11-12
 */
/**
 * 试题分析类，用于处理试题分析相关操作
 */
namespace Statistics\Manage;
class TestAnsyManage extends BaseController  {
    var $moduleName = '试题分析'; //模块名称

    /**
     * 浏览试题分析导航界面；
     * @author demo 
     * @last update date 2014-10-27
     */
    public function index() {
        $pageName = '试题分析';
        /*载入模板标签*/
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 知识点对应试题数量报表
     * @author demo
     */
    public function zsdtest() {
        $pageName = '知识点试题报表';

        //获取年份
        $yearArray=array();
        $maxYear=date('Y',time())+1;
        for($i=$maxYear;$i>2000;$i--){
            $yearArray[]=$i;
        }
        //获取试卷类型
        $doctypeCache=SS('docType');//文档属性
        $subjectArray=SS('subjectParentId');
        //获取地区
        $areaBuffer=SS('areaChildList');
        $areaArray=$areaBuffer[0];
        $tiaoJian='无';
        if(IS_POST){
            $AreaID=$_POST['AreaID'];
            $TypeID=$_POST['TypeID'];
            $Year1=$_POST['Year1'];
            $Year2=$_POST['Year2'];
            $subjectID=$_POST['SubjectID'];

            $areaCache=SS('areaList');//地区列表
            $subject=SS('subject');


            //需要显示的项
            $style=array();
            $style['kaopin']=$_POST['kaopin']; //考频
            $style['mangdian']=$_POST['mangdian']; //盲点
            $style['changkao']=$_POST['changkao']; //常考
            $style['nandu']=$_POST['nandu']; //难度
            $style['renzhi']=$_POST['renzhi']; //认知


            $tiaoJian='';
            if($subjectID)
                $tiaoJian.=' 学科： '.$subject[$subjectID]['SubjectName'].' ';
            if($AreaID)
                $tiaoJian.=' 地区： '.$areaCache[$AreaID]['AreaName'].' ';
            if($TypeID)
            $tiaoJian.=' 类型： '.$doctypeCache[$TypeID]['TypeName'].' ';
            if($Year1 || $Year2)
            $tiaoJian.=' 年份：'.$Year1.' '.$Year2;
            if($tiaoJian=='') $tiaoJian='无';

            $output=array();
            $knowledgeSubject=SS('klBySubject3');
            //统计
            $arr=array();
            $arr['AreaID']=$AreaID;
            $arr['TypeID']=$TypeID;
            $arr['Year1']=$Year1;
            $arr['Year2']=$Year2;
            $buffer=$knowledgeSubject[$subjectID];
            if($buffer){
                $output[$subjectID]['SubjectID']=$subjectID;
                $output[$subjectID]['SubjectName']=$subject[$subjectID]['SubjectName'];

                //获取知识点树形结构
                foreach($buffer as $i=>$iBuffer){
                    if($iBuffer['sub']){
                        foreach($iBuffer['sub'] as $j=>$jBuffer){
                            if($jBuffer['sub']){
                                foreach($jBuffer['sub'] as $l=>$lBuffer){
                                    if($lBuffer['sub']){
                                        //已经到第三层
                                    }else{
                                        $arr['KlID']=$lBuffer['KlID'];
                                        $lBuffer['TestCount']=$this->searchCount($arr,$style);
                                    }
                                    $jBuffer['sub'][$l]=$lBuffer;
                                }
                            }else{
                                $arr['KlID']=$jBuffer['KlID'];
                                $jBuffer['TestCount']=$this->searchCount($arr,$style);
                            }
                            $iBuffer['sub'][$j]=$jBuffer;
                        }
                        $buffer[$i]=$iBuffer;
                    }else{
                        $arr['KlID']=$iBuffer['KlID'];
                        $buffer[$i]['TestCount']=$this->searchCount($arr,$style);
                    }
                }
                $output[$subjectID]['sub']=$buffer;
            }
            //输出表格
            $table='<table border="1" style="border-collapse:collapse;">';
            $str='<tr><td colspan="'.(count($style)+3).'">';
            foreach($output as $iOutput){
                $str.='<a href="#titl'.$iOutput['SubjectID'].'">'.$iOutput['SubjectName'].'</a>　';
            }
            $str.='</td></tr>';
            $table.=$str;

            foreach($output as $iOutput){
                //学科
                $tablerows=0;//学科所占行数
                $table.='<tr>';
                $table.='<th colspan="'.(count($style)+3).'"><a name="titl'.$iOutput['SubjectID'].'">'.$iOutput['SubjectName'].'</a></th>';
                $table.='</tr>';
                $table.='<tr>';
                $table.='<th>分类1</th>';
                $table.='<th>分类2</th>';
                $table.='<th>分类3</th>';
                if($style['kaopin']) $table.='<th>试题数</th>';
                if($style['mangdian']) $table.='<th>模拟/比值</th>';
                if($style['changkao']) $table.='<th>题型</th>';
                if($style['nandu']) $table.='<th>难度</th>';
                if($style['renzhi']) $table.='<th>打分</th>';
                $table.='</tr>';

                $zsd1='';
                foreach($iOutput['sub'] as $j=>$jOutput){
                    $zsd1.='<tr>';
                    if(is_array($jOutput['sub'])){
                        $zsd1.='<td rowspan="{#num1#}">'.$jOutput['KlName'].'</td>';
                        $num1=0;
                        foreach($jOutput['sub'] as $k=>$kOutput){
                            if($k) $zsd1.='<tr>';
                            if(is_array($kOutput['sub'])){
                                $zsd1.='<td rowspan="{#num2#}">'.$kOutput['KlName'].'</td>';
                                $num2=0;
                                foreach($kOutput['sub'] as $l=>$lOutput){
                                    if($l) $zsd1.='<tr>';
                                    if(is_array($lOutput['sub'])){
                                    }else{
                                        $num2+=1;
                                        $zsd1.='<td>'.$lOutput['KlName'].'</td>';
                                        if($style['kaopin']) $zsd1.='<td>'.$lOutput['TestCount']['kaopin'].'</td>';
                                        if($style['mangdian']) $zsd1.='<td>'.$lOutput['TestCount']['moni'].'/'.$lOutput['TestCount']['mangdian'].'</td>';
                                        if($style['changkao']) $zsd1.='<td>'.$lOutput['TestCount']['changkao'].'</td>';
                                        if($style['nandu']) $zsd1.='<td>'.$lOutput['TestCount']['nandu'].'</td>';
                                        if($style['renzhi']) $zsd1.='<td>'.$lOutput['TestCount']['renzhi'].'</td>';
                                        $zsd1.='</tr>';
                                    }
                                }
                                $zsd1=str_replace('{#num2#}',$num2,$zsd1);
                                $num1+=$num2;
                            }else{
                                $num1+=1;
                                $zsd1.='<td>'.$kOutput['KlName'].'</td>';
                                $zsd1.='<td>&nbsp;</td>';
                                if($style['kaopin']) $zsd1.='<td>'.$kOutput['TestCount']['kaopin'].'</td>';
                                if($style['mangdian']) $zsd1.='<td>'.$kOutput['TestCount']['moni'].'/'.$kOutput['TestCount']['mangdian'].'</td>';
                                if($style['changkao']) $zsd1.='<td>'.$kOutput['TestCount']['changkao'].'</td>';
                                if($style['nandu']) $zsd1.='<td>'.$kOutput['TestCount']['nandu'].'</td>';
                                if($style['renzhi']) $zsd1.='<td>'.$kOutput['TestCount']['renzhi'].'</td>';
                                $zsd1.='</tr>';
                            }
                        }
                        $zsd1=str_replace('{#num1#}',$num1,$zsd1);
                    }else{
                        $zsd1.='<td>'.$jOutput['KlName'].'</td>';
                        $zsd1.='<td>&nbsp;</td>';
                        $zsd1.='<td>&nbsp;</td>';
                        if($style['kaopin']) $zsd1.='<td>'.$jOutput['TestCount']['kaopin'].'</td>';
                        if($style['mangdian']) $zsd1.='<td>'.$jOutput['TestCount']['moni'].'/'.$jOutput['TestCount']['mangdian'].'</td>';
                        if($style['changkao']) $zsd1.='<td>'.$jOutput['TestCount']['changkao'].'</td>';
                        if($style['nandu']) $zsd1.='<td>'.$jOutput['TestCount']['nandu'].'</td>';
                        if($style['renzhi']) $zsd1.='<td>'.$jOutput['TestCount']['renzhi'].'</td>';
                        $zsd1.='</tr>';
                    }
                }
                $table.=$zsd1;
            }
            $table.='</table>';
        }

        /*载入模板标签*/
        $this->assign('table', $table); //页面标题
        $this->assign('areaArray', $areaArray); //页面标题
        $this->assign('yearArray', $yearArray); //页面标题
        $this->assign('doctypeArray', $doctypeCache); //页面标题
        $this->assign('subjectArray', $subjectArray); //页面标题
        $this->assign('tiaoJian', $tiaoJian); //页面标题
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 根据条件查询试题数据量
     * @author demo
     */
    protected function searchCount($arr,$style){
        $testKl=$this->getModel('TestKl');
        $testKlReal=$this->getModel('TestKlReal');
        $testAttr=$this->getModel('TestAttr');
        $testAttrReal=$this->getModel('TestAttrReal');
        $arrayCount=array();

        $moniTypeID=2; //模拟试题对应文档类型id

        //根据 地区  年份   类型 查找DocID
        $arrayCount['kaopin']=0;
        $wherekaopin=$this->getzsdWhere($arr);


        if($style['mangdian']){
            $arrayCount['moni']=0;
            $arrayCount['mangdian']=0;
            $tmpArr=$arr;
            $tmpArr['TypeID']=$moniTypeID;
            $wheremangdian=$this->getzsdWhere($tmpArr);
        }

        if($style['changkao']){
            $typeArray=SS('types');
            $arrayCount['changkao']=0;
        }

        if($style['nandu']){
            $arrayCount['nandu']=0;
        }

        if($style['renzhi']){
            $arrayCount['renzhi']=0;
        }

        if($arr){
            $unionSelect='testAttrKlSelectCount';
            $unionSelectReal='testAttrKlRealSelectCount';
            $unionSelectCK='testAttrKlSelectGroup';
            $unionSelectRealCK='testAttrKlRealSelectGroup';
            $unionSelectRZ='testAttrKlSelectMark';
            $unionSelectRealRZ='testAttrKlRealSelectMark';
            if($arr['AreaID']){
                $unionSelect='testAttrSelectCount';
                $unionSelectReal='testAttrRealSelectCount';
                $unionSelectCK='testAttrSelectGroup';
                $unionSelectRealCK='testAttrRealSelectGroup';
                $unionSelectRZ='testAttrSelectMark';
                $unionSelectRealRZ='testAttrRealSelectMark';
            }

            $arrayCount['kaopin']=$testAttr->unionSelect($unionSelect,$wherekaopin);
            $arrayCount['kaopin']+=$testAttr->unionSelect($unionSelectReal,$wherekaopin);
            if($style['mangdian']){
                $arrayCount['moni']=$testAttr->unionSelect($unionSelect,$wheremangdian);
                $arrayCount['moni']+=$testAttr->unionSelect($unionSelectReal,$wheremangdian);

                if($arrayCount['kaopin']>0) $arrayCount['mangdian']=round($arrayCount['moni']/$arrayCount['kaopin'],2);
            }

            if($style['changkao']){
                $tmpCk1=$testAttr->unionSelect($unionSelectCK,$wherekaopin,'a.TypesID');
                $tmpCk2=$testAttr->unionSelect($unionSelectRealCK,$wherekaopin,'a.TypesID');

                $tmpArr=array();
                if($tmpCk1){
                    foreach($tmpCk1 as $iTmpCk1){
                        if(empty($tmpArr[$iTmpCk1['TypesID']])) $tmpArr[$iTmpCk1['TypesID']]=0;
                        $tmpArr[$iTmpCk1['TypesID']]+=$iTmpCk1['TestNum'];
                    }
                }
                if($tmpCk2){
                    foreach($tmpCk2 as $iTmpCk1){
                        if(empty($tmpArr[$iTmpCk1['TypesID']])) $tmpArr[$iTmpCk1['TypesID']]=0;
                        $tmpArr[$iTmpCk1['TypesID']]+=$iTmpCk1['TestNum'];
                    }
                }
                $tmpArr2=array();
                foreach($tmpArr as $i=>$iTmpArr){
                    $tmpArr2[]=$typeArray[$i]['TypesName'].':'.$iTmpArr;
                }

                $arrayCount['changkao']=implode('<br/>',$tmpArr2);
            }

            if($style['nandu']){
                $tmpCk1=$testAttr->unionSelect($unionSelectCK,$wherekaopin,'a.Diff');
                $tmpCk2=$testAttr->unionSelect($unionSelectRealCK,$wherekaopin,'a.Diff');

                $tmpArr=array();
                if($tmpCk1){
                    foreach($tmpCk1 as $iTmpCk1){
                        $tmpDiffid=0;
                        if($iTmpCk1['Diff']>0.6) $tmpDiffid=1;
                        else if($iTmpCk1['Diff']<0.5) $tmpDiffid=2;
                        else $tmpDiffid=3;
                        if(empty($tmpArr[$tmpDiffid])) $tmpArr[$tmpDiffid]=0;
                        $tmpArr[$tmpDiffid]+=$iTmpCk1['TestNum'];
                    }
                }
                if($tmpCk2){
                    foreach($tmpCk2 as $iTmpCk1){
                        $tmpDiffid=0;
                        if($iTmpCk1['Diff']>0.6) $tmpDiffid=1;
                        else if($iTmpCk1['Diff']<0.5) $tmpDiffid=2;
                        else $tmpDiffid=3;
                        if(empty($tmpArr[$tmpDiffid])) $tmpArr[$tmpDiffid]=0;
                        $tmpArr[$tmpDiffid]+=$iTmpCk1['TestNum'];
                    }
                }
                $tmpArr2=array();
                $diff=array(1=>'简单',2=>'困难',3=>'一般');
                foreach($tmpArr as $i=>$iTmpArr){
                    $tmpArr2[]=$diff[$i].':'.$iTmpArr;
                }

                $arrayCount['nandu']=implode('<br/>',$tmpArr2);
            }

            if($style['renzhi']){
                $tmpRz1=$testAttr->unionSelect($unionSelectRZ,$wherekaopin,'tam.MarkID,tam.Score');
                $tmpRz2=$testAttr->unionSelect($unionSelectRealRZ,$wherekaopin,'tam.MarkID,tam.Score');
                $tmpArr=array();
                if($tmpRz1){
                    //markid和score主键的数据
                    foreach($tmpRz1 as $iTmpCk1){
                        $key=(string)round($iTmpCk1['Score'],2);
                        if(empty($tmpArr[$iTmpCk1['MarkID']][$key])) $tmpArr[$iTmpCk1['MarkID']][$key]=0;
                        $tmpArr[$iTmpCk1['MarkID']][$key]+=$iTmpCk1['TestNum'];
                    }
                }
                if($tmpRz2){
                    //markid和score主键的数据
                    foreach($tmpRz2 as $iTmpCk1){
                        $key=(string)round($iTmpCk1['Score'],2);
                        if(empty($tmpArr[$iTmpCk1['MarkID']][$key])) $tmpArr[$iTmpCk1['MarkID']][$key]=0;
                        $tmpArr[$iTmpCk1['MarkID']][$key]+=$iTmpCk1['TestNum'];
                    }
                }

                $tmpArr2=array();
                $markArray=SS('testMark');
                foreach($tmpArr as $i=>$iTmpArr){
                    $tmpArr2[]=$markArray[$i]['MarkName'];
                    foreach($iTmpArr as $j=>$jTmpArr){
                        $tmpArr2[]='　　'.$markArray[$i]['MarkListArray'][$j].'：'.$jTmpArr;
                    }
                }
                $arrayCount['renzhi']=implode('<br/>',$tmpArr2);
            }
        }else{
            $arrayCount['kaopin']=$testKl->selectCount(
                $wherekaopin,
                'TestID');
            $arrayCount['kaopin']+=$testKlReal->selectCount(
                $wherekaopin,
                'TestID');
        }
        return $arrayCount;
    }

    /**
     * 获取知识点试题对应条件
     * @author demo
     */
    private function getzsdWhere($arr) {
        $where=' 1=1 ';
        if($arr['TypeID']) $where.=' and TypeID='.$arr['TypeID'].' ';
        if($arr['Year1'] and $arr['Year2']) $where.=' and DocYear between '.$arr['Year1'].' and '.$arr['Year2'].' ';
        else if($arr['Year1']) $where.=' and DocYear >= '.$arr['Year1'].' ';
        else if($arr['Year2']) $where.=' and DocYear <= '.$arr['Year2'].' ';

        $where.=' and KlID='.$arr['KlID'];
        if($arr['AreaID']){
            $where.=' and r.AreaID ='.$arr['AreaID'];
        }

        return $where;
    }


    /**
     * 章节对应知识点关系报表
     * @author demo
     */
    public function zj2zsd() {
        $pageName = '章节知识点报表';

        $subjectArray=SS('subjectParentId');
        if(IS_POST){
            $chapterID=$_POST['ChapterID'];
            $subjectID=$_POST['SubjectID'];
            if($this->ifSubject && $this->mySubject){
                if(!in_array($subjectID,explode(',',$this->mySubject))){
                    $this->setError('30712'); //您不能查看非所属学科知识点报表！
                }
            }
            //获取章节
            $param=array();
            $param['style']='chapter';
            $param['pID']=$chapterID;
            $param['haveLayer']=3;
            $buffer=$this->getData($param);


            $sSubject=SS('subject');
            //是否显示关键字
            $chapertSet=$sSubject[$subjectID]['ChapterSet'];
            $sChapter=SS('chapterList');
            $idListChapter=SS('chapterIDList');
            $knowledgeParent=SS('knowledgeParent');
            $knowledge=SS('knowledge');
            $table='<h1>所属学科：'.$sSubject[$subjectID]['SubjectName'].', 所属版本：'.$sChapter[$chapterID]['ChapterName'].'</h1>';
            //获取章节对应知识点
            $tmpChanpterKl=$this->getModel('ChapterKl')->selectData(
                '*',
                'CID in ('.$idListChapter[$chapterID].')');
            $chapterArray=array();
            if($tmpChanpterKl){
                foreach($tmpChanpterKl as $iTmpChanpterKl){
                    $chapterArray[$iTmpChanpterKl['CID']][]=$iTmpChanpterKl['KID'];
                }
            }
            //获取章节对应关键字
            if($chapertSet){
                $tmpChapterKey=$this->getModel('ChapterKey')->selectData(
                    '*',
                    'ChapterID in ('.$idListChapter[$chapterID].')');
                $keyArray=array();
                if($tmpChapterKey){
                    foreach($tmpChapterKey as $iTmpChapterKey){
                        $keyArray[$iTmpChapterKey['ChapterID']][]=$iTmpChapterKey['Keyword'];
                    }
                }
            }
            //输出表格
            $table.='<table border="1" style="border-collapse:collapse;">';
            $str='<tr><td colspan="4">';
            foreach($buffer as $iBuffer){
                $str.='<a href="#titl'.$iBuffer['ChapterID'].'">'.$iBuffer['ChapterName'].'</a>　';
            }
            $str.='</td></tr>';
            $table.=$str;

                //学科
                $tableRows=0;//学科所占行数
                $table.='<tr>';
                $table.='<th>分类1</th>';
                $table.='<th>分类2</th>';
                $table.='<th>分类3</th>';
                $table.='<th>对应知识点</th>';
                $table.='</tr>';

                $zsd1='';
                foreach($buffer as $i=>$iOutput){
                    $zsd1.='<tr>';
                    if(is_array($iOutput['sub'])){
                        $zsd1.='<td rowspan="{#num1#}"><a name="titl'.$iOutput['ChapterID'].'"></a>'.$iOutput['ChapterName'].'</td>';
                        $num1=0;
                        foreach($iOutput['sub'] as $j=>$jOutput){
                            if($j) $zsd1.='<tr>';
                            if(is_array($jOutput['sub'])){
                                $zsd1.='<td rowspan="{#num2#}">'.$jOutput['ChapterName'].'</td>';
                                $num2=0;
                                foreach($jOutput['sub'] as $k=>$kOutput){
                                    if($k) $zsd1.='<tr>';
                                    if(is_array($kOutput['sub'])){
                                    }else{
                                        $num2+=1;
                                        $zsd1.='<td>'.$kOutput['ChapterName'].'</td>';
                                        $keyWord='';
                                        if($chapertSet){
                                            $keyWord='<br/>关键字：'.implode(';',$keyArray[$kOutput['ChapterID']]);
                                        }
                                        $zsd1.='<td>'.$this->getZsdPath($chapterArray[$kOutput['ChapterID']],$knowledgeParent,$knowledge).$keyWord.'</td>';
                                        $zsd1.='</tr>';
                                    }
                                }
                                $zsd1=str_replace('{#num2#}',$num2,$zsd1);
                                $num1+=$num2;
                            }else{
                                $num1+=1;
                                $zsd1.='<td>'.$jOutput['ChapterName'].'</td>';
                                $zsd1.='<td>&nbsp;</td>';
                                $keyWord='';
                                if($chapertSet){
                                    $keyWord='<br/>关键字：'.implode(';',$keyArray[$jOutput['ChapterID']]);
                                }
                                $zsd1.='<td>'.$this->getZsdPath($chapterArray[$jOutput['ChapterID']],$knowledgeParent,$knowledge).$keyWord.'</td>';
                                $zsd1.='</tr>';
                            }
                        }
                        $zsd1=str_replace('{#num1#}',$num1,$zsd1);
                    }else{
                        $keyWord='';
                        if($chapertSet){
                            $keyWord='<br/>关键字：'.implode(';',$keyArray[$iOutput['ChapterID']]);
                        }
                        $zsd1.='<td><a name="titl'.$iOutput['ChapterID'].'"></a>'.$iOutput['ChapterName'].'</td>';
                        $zsd1.='<td>&nbsp;</td>';
                        $zsd1.='<td>&nbsp;</td>';
                        $zsd1.='<td>'.$this->getZsdPath($chapterArray[$iOutput['ChapterID']],$knowledgeParent,$knowledge).$keyWord.'</td>';
                        $zsd1.='</tr>';
                    }
                }
                $table.=$zsd1;
            $table.='</table>';
            $this->assign('table', $table); //页面标题
        }

        /*载入模板标签*/
        $this->assign('subjectArray', $subjectArray); //页面标题
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }
    /**
     * 根据知识点id获取知识点路径
     * @param mixed $id 知识点id 可是是数字id或者数组id
     * @param array $buffer 知识点父级缓存数组
     * @param array $bufferX 知识点缓存数组
     * @return string
     * @author demo 
     */
    public function getZsdPath($id,$buffer,$bufferX){
        if(empty($id)) return;
        if(!is_array($id)) $tmpArr=array($id);
        else $tmpArr=$id;
        $output=array();
        foreach($tmpArr as $iTmpArr){
            if($buffer[$iTmpArr]){
                $tmpStr=array();
                foreach($buffer[$iTmpArr] as $jBuffer){
                    $tmpStr[]=$jBuffer['KlName'];
                }
                $tmpStr[]=$bufferX[$iTmpArr]['KlName'];
                $output[]=implode('>>',$tmpStr);
            }else{
                $output[]=$bufferX[$iTmpArr]['KlName'];
            }
        }
        return implode('<br/>',$output);
    }
    /**
     * 分学科查询试题数量；
     * @author demo
     */
    public function xk2test(){
        $testStyle=$_POST['teststyle']; //查询类型
        $pageName='学科对应试题统计';
        $condition='无'; //查询类型对应标题
        $subjectArray=SS('subject'); //载入学科缓存
        $gradeArr=SS('grade');
        foreach($subjectArray as $i=>$iSubjectArray){
            if($subjectArray[$i]['ParentName']==null){
                unset($subjectArray[$i]);
            }
        }
        $table=''; //输出表格数据
        $output=array(); //存储查询数据库

        switch($testStyle){
            case 3:
                $condition='全部试题';
                $testAttr=$this->getModel('TestAttr');
                $i=0;
                foreach($subjectArray as $j=>$jSubjectArray){
                    foreach($gradeArr as $k=>$jGradeArr){
                        $testAttrBuffer=$testAttr->getAttrNum($k,$j);
                        $testTotal[$i]['total']=$testAttrBuffer[0]['TotalNum'];
                        $testTotal[$i]['GradeName']=$gradeArr[$k]['GradeName'];
                        $testTotal[$i]['SubjectName']=$subjectArray[$j]['SubjectName'];
                        $testTotal[$i]['GradeSubject']=$subjectArray[$j]['ParentName'];
                        $testTotal[$i]['SubjectID']=$gradeArr[$k]['SubjectID'];
                        $testTotal[$i]['PID']=$subjectArray[$j]['PID'];
                        $i++;
                    }
                }
                foreach($testTotal as $i=>$iTestTotal){//去除年级与高初不符的学科
                    if($testTotal[$i]['SubjectID']!=$testTotal[$i]['PID']){
                        unset($testTotal[$i]);
                    }
                }
                $ar=array();
                $result=array();
                foreach($testTotal as $i=>$iTestTotal){//按年级，分科分组
                    $ar[$iTestTotal['GradeName']]['items'][]=array_slice($iTestTotal,0,3);
                }
                foreach($ar as $i=>$iAr){
                    $result[]=array('GradeName'=>$i,'items'=>$iAr['items']);
                }
                $testAttrReal=$this->getModel('TestAttrReal');

                $j=0;
                foreach($subjectArray as $i=>$iSubjectArray){
                    foreach($gradeArr as $k=>$kGrade){
                        $testAttrBuffer2=$testAttrReal->getAttrRealNum($k,$i);
                        $testTotal2[$j]['total']=$testAttrBuffer2[0]['TotalNum'];
                        $testTotal2[$j]['GradeName']=$gradeArr[$k]['GradeName'];
                        $testTotal2[$j]['SubjectName']=$subjectArray[$i]['SubjectName'];
                        $testTotal2[$j]['GradeSubject']=$subjectArray[$i]['ParentName'];
                        $testTotal2[$j]['SubjectID']=$gradeArr[$k]['SubjectID'];
                        $testTotal2[$j]['PID']=$subjectArray[$i]['PID'];
                        $j++;

                    }
                }
                foreach($testTotal2 as $i=>$iTestTotal2){//去除年级与高初不符的学科
                    if($testTotal2[$i]['SubjectID']!=$testTotal2[$i]['PID']){
                        unset($testTotal2[$i]);
                    }
                }
                $ar2=array();
                $result2=array();
                foreach($testTotal2 as $i=>$iTestTotal2){
                    $ar2[$iTestTotal2['GradeName']]['items'][]=array_slice($iTestTotal2,0,3);
                }
                foreach($ar2 as $i=>$iAr2){
                    $result2[]=array('GradeName'=>$i,'items'=>$iAr2['items']);
                }
                foreach($result2 as $i=>$iResult2){
                    foreach($result2[$i]['items'] as $j=>$jResult2){
                        $result[$i]['items'][$j]['total']=$result[$i]['items'][$j]['total']+$result2[$i]['items'][$j]['total'];
                    }
                }
                unset($testAttrBuffer);
                unset($testAttrBuffer2);
                unset($testAttr);
                unset($testAttrReal);
            break;
            case 1:
                $condition='入库试题';
                $testAttrReal=$this->getModel('TestAttrReal');
                $j=0;
                foreach($subjectArray as $i=>$iSubjectArray){
                    foreach($gradeArr as $k=>$kGrade){
                        $testAttrBuffer2=$testAttrReal->getAttrRealNum($k,$i);;
                        $testTotal2[$j]['total']=$testAttrBuffer2[0]['TotalNum'];
                        $testTotal2[$j]['GradeName']=$gradeArr[$k]['GradeName'];
                        $testTotal2[$j]['SubjectName']=$subjectArray[$i]['SubjectName'];
                        $testTotal2[$j]['GradeSubject']=$subjectArray[$i]['ParentName'];
                        $testTotal2[$j]['SubjectID']=$gradeArr[$k]['SubjectID'];
                        $testTotal2[$j]['PID']=$subjectArray[$i]['PID'];
                        $j++;

                    }
                }
                foreach($testTotal2 as $i=>$iTestTotal2){//去除年级与高初不符的学科
                    if($testTotal2[$i]['SubjectID']!=$testTotal2[$i]['PID']){
                        unset($testTotal2[$i]);
                    }
                }
                $ar2=array();
                $result=array();
                foreach($testTotal2 as $i=>$iTestTotal2){
                    $ar2[$iTestTotal2['GradeName']]['items'][]=array_slice($iTestTotal2,0,3);
                }
                foreach($ar2 as $i=>$iAr2){
                    $result[]=array('GradeName'=>$i,'items'=>$iAr2['items']);
                }
                unset($testAttrBuffer2);
                unset($testAttrReal);
            break;
            case 2:
                $condition='未入库试题';
                $testAttr=$this->getModel('TestAttr');
                $i=0;
                foreach($subjectArray as $j=>$iSubjectArray){
                    foreach($gradeArr as $k=>$kGrade){
                        $testAttrBuffer=$testAttr->getAttrNum($k,$j);;
                        $testTotal[$i]['total']=$testAttrBuffer[0]['TotalNum'];
                        $testTotal[$i]['GradeName']=$gradeArr[$k]['GradeName'];
                        $testTotal[$i]['SubjectName']=$subjectArray[$j]['SubjectName'];
                        $testTotal[$i]['GradeSubject']=$subjectArray[$j]['ParentName'];
                        $testTotal[$i]['SubjectID']=$gradeArr[$k]['SubjectID'];
                        $testTotal[$i]['PID']=$subjectArray[$j]['PID'];
                        $i++;
                    }
                }
                foreach($testTotal as $i=>$iTestTotal){//去除年级与高初不符的学科
                    if($testTotal[$i]['SubjectID']!=$testTotal[$i]['PID']){
                        unset($testTotal[$i]);
                    }
                }
                $ar=array();
                $result=array();
                foreach($testTotal as $i=>$iTestTotal){
                    $ar[$iTestTotal['GradeName']]['items'][]=array_slice($iTestTotal,0,3);
                }
                foreach($ar as $i=>$iAr){
                    $result[]=array('GradeName'=>$i,'items'=>$iAr['items']);
                }
                unset($testAttrBuffer);
                unset($testAttr);
            break;
        }

        $total=0;
        foreach($result as $i=>$iResult){//按年级统计试题总数
            foreach($result[$i]['items'] as $j=>$jResult){
                $result[$i]['alltotal']+=$result[$i]['items'][$j]['total'];
            }
        }
        if($result){
            $table='<table border="0" cellpadding="5" cellapsing="0">' .
                    '<tr><th>年级\学科</th>';
                    foreach($subjectArray as $i=>$iSubjectArray){//遍历学科
                        if($subjectArray[$i]['PID']=='21'){
            $table.='           <th>'.$subjectArray[$i]['SubjectName'].'</th>';
                        }
                    }
            $table.='<th>合计</th></tr>';
            foreach($result as $i=>$iResult){//遍历年级及各科试题统计数量
                $table.='<tr><td align="center">'.$result[$i]['GradeName'].'</td>';
                foreach($result[$i]['items'] as $j=>$jResult){
                    $table.='<td align="center">'.$result[$i]['items'][$j]['total'].'</td>';
                }
                $table.='<td align="center">'.$result[$i]['alltotal'].'</td></tr>';//统计该年级各科总数
            }
            $table.='</table>';
        }
        /*载入模板标签*/
        $this->assign('table', $table); //表格数据
        $this->assign('condition', $condition); //条件
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }

    /**
     * 试卷信息统计
     * @author demo
     * @date 2014-10-27
     */
    public function stat(){
        $pageName = '试卷信息统计';
        $params = false;
        $param = array();
        $params = $_REQUEST;
        $start = $_REQUEST['date'];
        $end = $_REQUEST['dateEnd'];
        if(strstr($start,'-')){
            $start=strtotime($start);
        }
        if(strstr($end,'-')){
            $end=strtotime($end);
        }
        if ($start){
            $param['date'] = $start;
            $_REQUEST['date']=date('Y-m-d',$start);
        }
        if ($end){
            $param['dateEnd'] = $end;
            $_REQUEST['dateEnd']=date('Y-m-d',$end);
        }
        if(!empty($_POST['search']) || !empty($_GET['search'])){
            $params = isset($_GET['search']) ? $_GET : $_POST;
            $param['SubjectID'] = $params['SubjectID'];
        }
        $param['p'] = $params['p'];
        $subjectArray=SS('subjectParentId');
        $result = $this->getModel('Doc')->stat($param);
        $this->assign('datas',$result['data']);
        $this->assign('page',$result['page']);
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('subjectArray', $subjectArray); //页面标题
        $this->display();
    }



    /**
     * 试题概率统计
     * @author demo
     */
    public function testAttrPre(){
        //获取试卷类型
        $doctypeCache=SS('docType');//文档属性
        $subjectArray=SS('subjectParentId');

        //设置顶级省份
        $bufferz=SS('areaChildList');  // 缓存子类list数据
        $areaArray = $bufferz[0]; //省份数据集

        //获取年份
        $yearArray=array();
        $maxYear=date('Y',time())+1;
        for($i=$maxYear;$i>2000;$i--){
            $yearArray[]=$i;
        }

        //分析条件
        $where=' 1=1 ';
        if($_REQUEST['DocID']){
            $where.=' AND d.DocID='.$_REQUEST['DocID'];
        }else{
            if($_REQUEST['SubjectID']){
                $where.=' AND tar.SubjectID='.$_REQUEST['SubjectID'];
            }
            if($_REQUEST['AreaID']){
                $where.=' AND da.AreaID='.$_REQUEST['AreaID'];
            }
            if(is_numeric($_REQUEST['Year1'])){
                $year2=$_REQUEST['Year2'];
                if(empty($year2) || !is_numeric($year2)) $year2=date('Y',time());
                $where.=' AND d.DocYear between '.$_REQUEST['Year1'].' AND '.$year2;
            }
            if(is_numeric($_REQUEST['TypeID'])){
                $where.=' AND d.TypeID='.$_REQUEST['TypeID'];
            }
            if(is_numeric($_REQUEST['TypesID'])){
                $where.=' AND tar.TypesID='.$_REQUEST['TypesID'];
            }
        }

        if($where && IS_POST){
            //统计每个选项的概率
            //按照选项字母排序
            if($_REQUEST['AreaID']){
                $letterBuffer=$this->getModel('Base')->dbConn->field('tac.Letter,count(tac.Letter) as num')
                    ->table('zj_test_attr_choose tac')
                    ->join('left join zj_test_attr_real tar on tar.TestID=tac.TestID')
                    ->join('left join zj_doc d on d.DocID=tac.DocID')
                    ->join('left join zj_doc_area da on d.DocID=da.DocID')
                    ->where($where.' AND tac.IfAnswer=1')
                    ->group('tac.Letter')
                    ->select();
            }else{
                $letterBuffer=$this->getModel('Base')->dbConn->field('tac.Letter,count(tac.Letter) as num')
                    ->table('zj_test_attr_choose tac')
                    ->join('left join zj_test_attr_real tar on tar.TestID=tac.TestID')
                    ->join('left join zj_doc d on d.DocID=tar.DocID')
                    ->where($where.' AND tac.IfAnswer=1')
                    ->group('tac.Letter')
                    ->select();
            }

            $this->assign('letterBuffer', $letterBuffer); //字母概率

            //按照试题序号选项字母排序
            //文档id按照序号进行统计
            $numLetterBuffer=$this->getModel('Base')->dbConn->field('tac.RealNumbID,tac.Letter,count(tac.Letter) as num')
                ->table('zj_test_attr_choose tac')
                ->join('left join zj_test_attr_real tar on tar.TestID=tac.TestID')
                ->join('left join zj_doc d on d.DocID=tar.DocID')
                ->join('left join zj_doc_area da on d.DocID=da.DocID')
                ->where($where.' AND tac.IfAnswer=1')
                ->group('tac.RealNumbID,tac.Letter')
                ->select();
            $this->assign('numLetterBuffer', $numLetterBuffer); //字母概率


            //统计选项字数最长和最短对应正确答案的概率
            $lenContentBuffer=$this->getModel('Base')->dbConn->field('tac.ContentRank,count(tac.ContentRank) as num')
                ->table('zj_test_attr_choose tac')
                ->join('left join zj_test_attr_real tar on tar.TestID=tac.TestID')
                ->join('left join zj_doc d on d.DocID=tar.DocID')
                ->join('left join zj_doc_area da on d.DocID=da.DocID')
                ->where($where.' AND tac.IfAnswer=1')
                ->group('tac.ContentRank')
                ->select();

            $strDes=array(
                0=>'所有选项长度一致',
                1=>'选项最短',
                2=>'选项第二短',
                3=>'选项第二长',
                4=>'选项最长'
            );
            foreach($lenContentBuffer as $j=>$jLenContentBuffer){
                $lenContentBuffer[$j]['ContentRank']=$strDes[$jLenContentBuffer['ContentRank']];
            }

            $this->assign('lenContentBuffer', $lenContentBuffer); //字母概率
        }

        $pageName='试题概率统计';

        $this->assign('yearArray', $yearArray); //年份
        $this->assign('doctypeArray', $doctypeCache); //文档类型
        $this->assign('subjectArray', $subjectArray); //学科
        $this->assign('areaArray', $areaArray); //地区
        $this->assign('pageName', $pageName); //页面标题
        $this->display();
    }


    /**
     * 试题概率统计 为试题统计做前期数据处理
     * @author demo
     */
    public function testAttrPreData(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $check=$_GET['check'];
        if($check==1){

            $prepage=100;
            $page=is_numeric($_GET['page'])? $_GET['page'] :1;
            $page=$page<1?1:$page;
            $limit=($page-1)*$prepage.','.$prepage;

            $status=$_GET['status'];
            if(empty($status)){
                $status=1;
            }

            $testAttrReal=$this->getModel('TestAttrReal');
            if($status==1){
                $count=$testAttrReal->selectCount(
                    'IfChoose>1',
                    'TestID'
                );

                if($count<=($page-1)*$prepage) exit('下面转入带小题处理。<script>location.href="'.__CONTROLLER__.'/testAttrPreData/check/1/status/2";</script>');

                $buffer=$testAttrReal->selectData(
                    'TestID,NumbID,DocID',
                    'IfChoose>1',
                    'TestID ASC',
                    $limit
                );
            }elseif($status==2){
                $testJudge=$this->getModel('TestJudge');
                $count=$testJudge->selectCount(
                    'IfChoose>1 AND OrderID=1',
                    'TestID'
                );
                if($count<=($page-1)*$prepage) exit('下面转入试题编号处理。<script>location.href="'.__CONTROLLER__.'/testAttrPreData/check/1/status/3";</script>');

                $buffer=$testJudge->groupData(
                    'TestID',
                    'IfChoose>1',
                    'TestID',
                    'TestID ASC',
                    $limit
                );
                $testIDArray=array();
                foreach($buffer as $iBuffer){
                    $testIDArray[]=$iBuffer['TestID'];
                }
                $buffer=$testAttrReal->selectData(
                    'TestID,NumbID,DocID',
                    'TestID in ('.implode(',',$testIDArray).')'
                );
            }else{
                //处理realNumbID
                $testAttrChoose=$this->getModel('TestAttrChoose');
                $count=$testAttrChoose->selectCount(
                    '1=1',
                    'DISTINCT DocID'
                );

                if($count<=($page-1)*$prepage) exit('处理完毕。');

                $testAttrChooseCountDoc=$testAttrChoose->distinctData(
                    'DocID',
                    '1=1',
                    'DocID ASC',
                    $limit
                );

                $docIDArray=array();
                foreach($testAttrChooseCountDoc as $iBuffer){
                    $docIDArray[]=$iBuffer['DocID'];
                }

                $buffer=$testAttrChoose->selectData(
                    'ChooseID,TestID,DocID,OrderID,NumbID',
                    'DocID in ('.implode(',',$docIDArray).')',
                    'DocID ASC,NumbID ASC,OrderID ASC'
                );

                //处理文档id对应试题序号到realNumbID
                $currentDocID=0;
                $num=0;
                $currentTestID=0;
                foreach($buffer as $iBuffer){
                    if($currentTestID!=$iBuffer['TestID'].'-'.$iBuffer['OrderID']){
                        $num++;
                        $currentTestID=$iBuffer['TestID'].'-'.$iBuffer['OrderID'];
                    }

                    if($currentDocID!=$iBuffer['DocID']){
                        $num=1;
                        $currentDocID=$iBuffer['DocID'];
                    }

                    //更新当前数据的真实编号
                    $testAttrChoose->updateData(array('RealNumbID'=>$num),'ChooseID='.$iBuffer['ChooseID']);
                }


                //转入下一页
                exit('当前第'.$page.'页，跳转中。。<script>location.href="'.__CONTROLLER__.'/testAttrPreData/check/'.$check.'/status/'.$status.'/page/'.($page+1).'";</script>');
            }

            //获取试题id
            $testIDArray=array();
            $testIDBuffer=array();
            foreach($buffer as $iBuffer){
                $testIDArray[]=$iBuffer['TestID'];
                $testIDBuffer[$iBuffer['TestID']]=$iBuffer;
            }

            if(empty($testIDArray))
                exit('当前第'.$page.'页，跳转中。。<script>location.href="'.__CONTROLLER__.'/testAttrPreData/check/'.$check.'/status/'.$status.'/page/'.($page+1).'";</script>');

            //获取试题内容
            $testReal=$this->getModel('TestReal');
            $test=$this->getModel('Test');

            $testBuffer=$testReal->selectData('TestID,Test,Answer','TestID in ('.implode(',',$testIDArray).')');


            //处理选择题，仅处理单选和多选，复杂选择题不做统计例如七选五
            foreach($testBuffer as $iTestBuffer){
                $xtArray=$test->xtnum($iTestBuffer['Test'],3);
                $xtAnswerArray=$test->xtnum($iTestBuffer['Answer'],3);
                if(count($xtAnswerArray)!=count($xtArray)) continue;

                //numbid
                $numbID=explode('-',$testIDBuffer[$iTestBuffer['TestID']]['NumbID']);
                $numbID=(substr($numbID[1],0,1)==0?substr($numbID[1],1):$numbID[1]);
                $docID=$testIDBuffer[$iTestBuffer['TestID']]['DocID'];

                if($xtArray==0){
                    $this->testAttrPreSendData($iTestBuffer['TestID'],$iTestBuffer['Test'],$iTestBuffer['Answer'],0,$numbID,$docID);
                }else{
                    for($i=1;$i<count($xtArray);$i++){
                        $this->testAttrPreSendData($iTestBuffer['TestID'],$xtArray[$i],$xtAnswerArray[$i],$i,$numbID,$docID);
                    }
                }
            }
            //转入下一页
            exit('当前第'.$page.'页，跳转中。。<script>location.href="'.__CONTROLLER__.'/testAttrPreData/check/'.$check.'/status/'.$status.'/page/'.($page+1).'";</script>');
        }
    }

    //插入数据
    private function testAttrPreSendData($testID,$testStr,$answerStr,$order,$numbID,$docID){
        //只有一道题
        $test=$this->getModel('Test');
        $str=R('Common/TestLayer/delMoreTag',array($testStr)); //去除多余标记
        $keywords=$test->formatStrToArr($str); //提取选项

        if(count($keywords)<3) return false;

        $answer=R('Common/TestLayer/delMoreTag',array($answerStr)); //去除多余标记

        //如果存在则删除后插入
        $testAttrChoose=$this->getModel('TestAttrChoose');
        $testAttrChoose->deleteData('TestID='.$testID);

        $dataArray=array();//批量插入数据

        $contentLen=array();
        for($i=1;$i<count($keywords);$i++){
            $contentLen[$i]=mb_strlen(preg_replace('/<[^>]*>/','',R('Common/TestLayer/delMoreTag',array($keywords[$i]))),'UTF-8')-2;
        }

        for($i=1;$i<count($keywords);$i++){
            $ifAnswer=0;
            if(strstr($answer,chr(64+$i))){
                $ifAnswer=1;
            }
            $contentRank=0;
            $maxlen=0;
            $minlen=0;
            foreach($contentLen as $j=>$jContentLen){
                if($i==$j) continue;
                if($contentLen[$i]>=$jContentLen){
                    $maxlen++;
                }
                if($contentLen[$i]<=$jContentLen){
                    $minlen++;
                }
            }
            if($maxlen==$minlen && $maxlen!=2){
                //四个选项一样长
                $contentRank=0;
            }elseif($minlen==0){
                //最长
                $contentRank=4;
            }elseif($maxlen==0){
                //最短
                $contentRank=1;
            }elseif($maxlen==3){
                //并列最长
                $contentRank=4;
            }elseif($minlen==3){
                //并列最短
                $contentRank=1;
            }elseif($maxlen==2){
                //第二大
                $contentRank=3;
            }elseif($minlen==2){
                //第二小
                $contentRank=2;
            }else{
                $contentRank=3;
            }

            $dataArray[]=array(
                'TestID'=>$testID,
                'Letter'=>chr(64+$i),
                'IfAnswer'=>$ifAnswer,
                'Content'=>$keywords[$i],
                'ContentLen'=>$contentLen[$i],
                'OrderID'=>$order,
                'NumbID'=>$numbID,
                'DocID'=>$docID,
                'ContentRank'=>$contentRank
            );
        }
        $testAttrChoose->addAllData($dataArray);
    }
}
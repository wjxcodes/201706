<?php
/**
 * 试题类，用于处理标引试题相关操作
 */
namespace Teacher\Controller;
class TestController extends BaseController {
    var $moduleName = '试题标引管理'; //模块名称
    /**
     * 按照文档id浏览试题；
     * @author demo
     */
    public function index() {
        $pageName = '试题管理';
        $UserName=$this->getCookieUserName();
        $did=$_GET['did'];
        $wid=$_GET['wid'];
        if(!is_numeric($did) || !is_numeric($wid)){
            $this->setError('30502');
        }
        //判断用户权限
        $buffer=$this->getModel('TeacherWork')->selectData(
            '*',
            ' WorkID='.$wid
        );
        
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        if($buffer[0]['UserName']!=$UserName){
            $this->setError('40113',NORMAL_ERROR);
        }
        $checkTimes=$buffer[0]['CheckTimes'];

        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'DocID='.$did.' and WorkID='.$wid
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        
        //文档验证码
        $docsavecode=md5($UserName.$did.$wid.C('TEST_KEY'));
        
        //查询试题数据
        $Test = $this->getModel('Test');
        $list = $Test->unionSelect('testSelectByDocId', $did);
        //载入知识点缓存
        $Knowledge = SS('knowledge');
        //载入章节缓存
        $Chapter = SS('chapterList');
        //载入专题缓存
        $special = SS('special');
        //载入章节父类路径缓存
        $ChapterParent=SS('chapterParentPath');
        //处理试题列表
        if($list){
            $host=C('WLN_DOC_HOST');
            foreach($list as $nn=>$listn){
                //图片外部链接
                if($host){
                    $list[$nn]['Test']= R('Common/TestLayer/strFormat',array($list[$nn]['Test']));
                }
                //试题验证码
                $list[$nn]['savecode']=md5($UserName.$list[$nn]['TestID'].C('TEST_KEY'));
                //获取knowledge
                $buffer = $this->getModel('TestKl')->selectData(
                    '*',
                    'TestID='.$listn['TestID']
                );
                if($buffer){
                    $arr_temp=array();
                    foreach($buffer as $buffern){
                        $arr_temp[]=$Knowledge[$buffern['KlID']]['KlName'];
                    }
                    $list[$nn]['KlName']=implode('<br/>',$arr_temp);
                }
                //获取Chapter
                $buffer = $this->getModel('TestChapter')->selectData(
                    '*',
                    'TestID='.$listn['TestID']
                );
                if($buffer){
                    $arr_temp=array();
                    foreach($buffer as $key=>$buffern){
                        $tmp_str = '('.($key+1).')：';
                        foreach($ChapterParent[$buffern['ChapterID']] as $a){
                            $tmp_str.=$a['ChapterName'].'>>';
                        }
                        $arr_temp[] = $tmp_str.$Chapter[$buffern['ChapterID']]['ChapterName'];
                    }
                    $list[$nn]['ChapterName']=implode('<br/>',$arr_temp);
                }
                //获取Special
                if($list[$nn]['SpecialID'])
                $list[$nn]['SpecialName']=$special[$special[$list[$nn]['SpecialID']]['PID']]['SpecialName'].' >> '.$special[$list[$nn]['SpecialID']]['SpecialName'];
                else
                $list[$nn]['SpecialName']='';
            }
        }
        
        //处理审核意见
        $buffer=$this->getModel('TeacherWorkCheck')->selectData(
            '*',
            'WorkID='.$wid.' and CheckTimes='.$checkTimes);
        $testError=array();
        if($buffer){
            //获取试题审核及原因
            $buffer=$this->getModel('TeacherWorkTestAttr')->unionSelect('teacherTestError',$buffer[0]['WCID'],$checkTimes,$did);
            
            if($buffer){
                foreach($buffer as $i=>$iBuffer){
                    $testError[$iBuffer['TestID']][$iBuffer['Style']]['Content']=$iBuffer['Content'];
                    $testError[$iBuffer['TestID']][$iBuffer['Style']]['Suggestion']=$iBuffer['Suggestion'];
                }
            }
        }
        /*载入模板标签*/
        $this->assign('wid', $wid);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('testError', $testError); // 赋值数据集
        $this->assign('did', $did); // 赋值数据集
        $this->assign('wid', $wid); // 赋值数据集
        $this->assign('docsavecode', $docsavecode); // 赋值数据集
        $this->assign('pageName', $pageName); //页面标题
        $this->assign('redirect',base64_encode(__SELF__));
        $this->display();
    }

    /**
     * 试题标引
     * @author demo 
     */
    public function intro(){
        $did = $_GET['did'];
        $wid = $_GET['wid'];
        if(!is_numeric($did) || !is_numeric($wid)){
            $this->setError('30502');
        }
        //判断用户权限
        $buffer=$this->getModel('TeacherWork')->selectData(
            '*',
            ' WorkID='.$wid);

        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        $UserName=$this->getCookieUserName();
        if($buffer[0]['UserName']!=$UserName){
            $this->setError('40113',NORMAL_ERROR,__URL__);
        }
        $checkTimes=$buffer[0]['CheckTimes'];

        $buffer=$this->getModel('TeacherWorkList')->selectData(
            '*',
            'DocID='.$did.' and WorkID='.$wid
        );
        if(!$buffer){
            $this->setError('40112',NORMAL_ERROR);
        }
        
        //查询试题数据
        $Test = $this->getModel('Test');
        $testAttr = $this->getModel('TestAttr');
        $TestID = $_GET['id']; //获取数据标识
        $tids = $testAttr->selectData(
            'TestID,Duplicate',
            'DocID='.$did,
            'TestID asc'
        );//获取试题编号集
        if(empty($tids)){
            $this->setError('40601',NORMAL_ERROR);
        }
        //判断数据标识
        if(empty($TestID)){
          $TestID = $tids[0]['TestID'];  
        }
        $nextid = $this->getNextId($tids,$TestID); //下一个试题id
        $securityCode = md5($UserName.$TestID.C('TEST_KEY')); //生成验证信息
        //查询试题信息
        $edit = $Test->unionSelect('testSelectByTestId', $TestID);
        $edit = $edit[0];

        //如果当前是重题则无需标引
        if($edit['Duplicate']!=0){
            if($nextid==-1){
                $this->setError('40605',NORMAL_ERROR,U("Teacher/Test/index?did=".$did."&wid=".$wid));
            }else{
                header('Location:'.U("Teacher/Test/intro?did=".$did."&wid=".$wid."&id=".$nextid));
            }
        }

        //获取题型名称
        $typesArray=SS('types');
        $edit['TypesName']=$typesArray[$edit['TypesID']]['TypesName'];
        unset($typesArray);
        //试题对应知识点
        $TestKl=$this->getModel('TestKl');
        $buffer = $TestKl->selectData(
            '*',
            'TestID='.$TestID
        );
        unset($TestKl);
        $edit['KlID']=0;
        if($buffer){
            $arr_temp=array();
            foreach($buffer as $buffern){
                $arr_temp[]=$buffern['KlID'];
            }
            $edit['KlID']=implode(',',$arr_temp);
        }
        //试题对应章节
        $buffer = $this->getModel('TestChapter')->selectData(
            '*',
            'TestID='.$TestID
        );
        $edit['ChapterID']=0;
        if($buffer){
            $arr_temp=array();
            foreach($buffer as $buffern){
                $arr_temp[]=$buffern['ChapterID'];
            }
            sort($arr_temp);
            $edit['ChapterID']=implode(',',$arr_temp);
        }
        //试题对应难度
        if(strstr($edit['Mark'],'@')){
            $arr=explode('@',$edit['Mark']);
            foreach($arr as $ii=>$arrn){
                $edit['Markx'][$ii+1]=array_filter(explode('#',$arrn));
            }
        }else{
            $edit['Markx'][1]=array_filter(explode('#',$edit['Mark']));
        }

        //自定义打分
        $markArray=$this->getModel('TestMark')->selectData(
            '*',
            'SubjectID = '.$edit['SubjectID'],
            'Style asc,OrderID asc'
        );
        if($markArray){
            foreach($markArray as $ii=>$iMarkArray){
                $markArray[$ii]['MarkListx']=formatString('str2Arr',$markArray[$ii]['MarkList']);
                foreach($markArray[$ii]['MarkListx'] as $jj=>$marklistn){
                    $markArray[$ii]['MarkListx'][$jj][3]=$markArray[$ii]['MarkID'].'|'.$markArray[$ii]['MarkListx'][$jj][0];
                }
            }
        }
        
        //自定义打分次数
        $times=$Test->xtnum($edit['Test'],1);
        if(!$times) $times=1;
        
        //试题路径
        $host=C('WLN_DOC_HOST');
        if($host){
            $edit['Test']= R('Common/TestLayer/strFormat',array($edit['Test']));
            $edit['Answer']= R('Common/TestLayer/strFormat',array($edit['Answer']));
            $edit['Analytic']= R('Common/TestLayer/strFormat',array($edit['Analytic']));
            $edit['Remark']= R('Common/TestLayer/strFormat',array($edit['Remark']));
        }

        //处理审核意见
        $teacherWork=$this->getModel('TeacherWork');
        $checkTimes=$teacherWork->getCheckTimes($wid);
        $teacherWorkCheck=$this->getModel('TeacherWorkCheck');
        $testError=$teacherWorkCheck->getTestCheckErr($checkTimes,$wid,$did,$TestID);
        
        //查询doc信息
        $doc = $this->getModel('Doc')->findData(
            'DocID,DocName',
            'DocID='.$did
        );
        $redirect = '';
        if($_GET['redirect']){
            $redirect = base64_decode($_GET['redirect']);
        }
        /*载入模板标签*/
        $this->assign('redirect',$redirect);
        $this->assign('errorInfo',$testError);
        $this->assign('doc',$doc);
        $this->assign('docsavecode',md5($UserName.$did.$wid.C('TEST_KEY')));
        $this->assign('tids',$tids);
        $this->assign('edit', $edit);
        $this->assign('times', $times);
        $this->assign('securityCode',$securityCode);
        $this->assign('nextid',$nextid);
        $this->assign('mark_array', $markArray);
        $this->assign('wid',$wid);
        $this->assign('did',$did);
        $this->assign('tid',$TestID);
        $this->display();
    }
    /**
     * 保存试题；
     * @author demo
     */
    public function save() {
        $TestID = $_POST['TestID'];
        //判断数据标识
        if(!is_numeric($TestID)){
            $this->setError('30502');
        }
        //验证数据合法性
        $s=$_POST['s'];
        $UserName=$this->getCookieUserName();
        if($s!=md5($UserName.$TestID.C('TEST_KEY'))){
            $this->setError('40119',AJAX_ERROR);
        }
        //接收数据
        $kl=$_POST['kl'];
        $cp=$_POST['cp'];
        $SpecialID=$_POST['SpecialID'];
        $Mark=$_POST['Mark'];
        $DfStyle=$_POST['DfStyle'];
        //更改状态
        $Test=$this->getModel('Test');
        $TestKl = $this->getModel('TestKl');
        $buffer=$TestKl->selectData(
            '*',
            'TestID='.$TestID
        );
        //保存考点
        if($kl){
            $kl=explode(',',$kl);
            if($buffer){
                for($nn=0;$nn<count($buffer);$nn++){
                    if($nn>=count($kl)){
                        $TestKl->deleteData(
                            'TklID ='.$buffer[$nn]['TklID'].' '
                        );
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$TestID;
                    $data['KlID']=$kl[$nn];
                    $data['TklID']=$buffer[$nn]['TklID'];
                    $TestKl->updateData(
                        $data,
                        'TklID='.$buffer[$nn]['TklID']
                    );
                }
                if($nn<count($kl)){
                    for(;$nn<count($kl);$nn++){
                        $data=array();
                        $data['TestID']=$TestID;
                        $data['KlID']=$kl[$nn];
                        $TestKl->insertData(
                            $data
                        );
                    }
                }
            }else{
                foreach($kl as $kln){
                    $data=array();
                    $data['TestID']=$TestID;
                    $data['KlID']=$kln;
                    $TestKl->insertData(
                        $data
                    );
                }
            }
        }else if($buffer){
            $delid=array();
            foreach($buffer as $buffern){
                $delid[]=$buffern['TklID'];
            }
            $TestKl->deleteData(
                'TklID in ('.implode(',',$delid).')'
            );
        }
        $TestChapter = $this->getModel('TestChapter');
        //保存章节
        $buffer=$TestChapter->selectData(
            '*',
            'TestID='.$TestID
        );
        $cp=explode(',',$cp);

        if($cp){
            if($buffer){
                for($nn=0;$nn<count($buffer);$nn++){
                    if($nn>=count($cp)){
                        $TestChapter->deleteData(
                            'TCID ='.$buffer[$nn]['TCID'].' '
                        );
                        continue;
                    }
                    $data=array();
                    $data['TestID']=$TestID;
                    $data['ChapterID']=$cp[$nn];
                    $data['TCID']=$buffer[$nn]['TCID'];
                    $TestChapter->updateData(
                        $data,
                        'TCID ='.$buffer[$nn]['TCID'].' '
                    );
                }
                if($nn<count($cp)){
                    for(;$nn<count($cp);$nn++){
                        $data=array();
                        $data['TestID']=$TestID;
                        $data['ChapterID']=$cp[$nn];
                        $TestChapter->insertData(
                            $data
                        );
                    }
                }
            }else{
                foreach($cp as $cpn){
                    $data=array();
                    $data['TestID']=$TestID;
                    $data['ChapterID']=$cpn;
                    $TestChapter->insertData(
                        $data
                    );
                }
            }
        }else if($buffer){
            $delid=array();
            foreach($buffer as $buffern){
                $delid[]=$buffern['TCID'];
            }
            $TestChapter->deleteData(
                'TCID in ('.implode(',',$delid).')'
            );
        }
            
        //更改属性
        $TestAttr=$this->getModel('TestAttr');
        $data=array();
        $data['DfStyle']=$DfStyle;
        $data['SpecialID']=$SpecialID;
        //如果有小题难度项目按照小题分组
        $Diff=0;
        if($Mark){
            $test_data=C('WLN_TEST_DATA');//难度和分值转换数组
            $Mark=explode(',',$Mark); //所有mark分组
            $buffer=$Test->selectData(
                '*',
                'TestID='.$TestID
            );   //提取试题信息作为判断小题数的依据

            $times=$Test->xtnum($buffer[0]['Test'],1);    //小题数量
            if($times){    //如果存在分组
                $ci=count($Mark)/$times;    //每个分组的打分项数目
                $str='';
                $Diff_arr=0;
                foreach($Mark as $ii=>$markn){
                    //小题分组字符串
                    if($ii%$ci==0 && $ii!=0){
                        $Diff_arr+=$test_data[$Diff];
                        $Diff=0;
                        $str.='@#';//分组字符串标记
                    }
                    if($markn) $str.=$markn.'#';
                    //分组计算分值
                    $n=array();
                    $n=explode('|',$markn);
                    if($n[1]>=1) $Diff+=$n[1];
                }
                $Diff=$Diff_arr+$test_data[$Diff];
                $data['Mark']='#'.$str;
            }else{
                $Mark=array_filter($Mark);
                $data['Mark']='#'.implode('#',$Mark).'#';
                //计算分值
                foreach($Mark as $Markn){
                    $n=array();
                    $n=explode('|',$Markn);
                    if($n[1]>=1) $Diff+=$n[1];
                }
                $Diff=$test_data[$Diff];
            }
            //计算辅助因素
            $diff_xs=0;
            if($Mark){
                foreach($Mark as $Markn){
                    $n=array();
                    $n=explode('|',$Markn);
                    if($n[1]<1) $diff_xs+=$n[1];
                }
            }
            $Diff+=$diff_xs;
            if($times) $Diff=round($Diff/$times,4);    //求多小题的平均值
        }
        
        if($DfStyle) $Diff=$_POST['Diff'];
        if(empty($Diff)) $Diff=0;
        $data['Diff']=$Diff;

        if($TestAttr->selectData('*','TestID='.$TestID)){
            $TestAttr->updateData(
                $data,
                'TestID='.$TestID
            );
        }else{
            $data['TestID']=$TestID;
            $TestAttr->insertData(
                $data
            );
        }
        
        //计算返回数据
        //知识点
        // $knowledgeStr=array();
        // if($kl){
        //     if(!is_array($kl)){
        //         $kl=array($kl);
        //     }
        //     $klArray=$this->getModel->selectData(
        //         'Knowledge',
        //         '*',
        //         'KlID in ('.implode(',',$kl).')'
        //     );
        //     foreach($klArray as $iKlArray){
        //         $knowledgeStr[]=$iKlArray['KlName'];
        //     }
        // }else{
        //     $kl=array(0=>0);
        // }
        
        // //章节
        // $chapterStr=array();
        // if($cp){
        //     if(!is_array($cp)){
        //         $cp=array($cp);
        //     }
        //     $cpArray=$this->getModel->selectData(
        //         'Chapter',
        //         '*',
        //         'ChapterID in ('.implode(',',$cp).')'
        //     );
        //     foreach($cpArray as $iCpArray){
        //         $chapterStr[]=$iCpArray['ChapterName'];
        //     }
        // }else{
        //     $cp=array(0=>0);
        // }
        // //专题
        // if($SpecialID){
        //     $special=SS('special');
        //     $specialStr=$special[$special[$SpecialID]['PID']]['SpecialName'].' >> '.$special[$SpecialID]['SpecialName'];
        // }else{
        //     $specialStr='';
        // }
        // //难度
        // $Diffstr=$Diff;
        
        //写入日志
        $this->teacherLog($this->moduleName, '修改标引试题TestID为【' . $TestID . '】的数据');
        //更新页面数据
        $this->setBack('success');
    }

    /**
     * 错误信息
     */
    private function getErrorHtml($subject,$data){
        $error = '';
        if($data['Content'] != ''){
            $error = '【'.$subject.'初审意见】：<font color="red">'.$data['Content'].'</font><br>';
        }
        if($data['Suggestion'] != ''){
            $error .= '【'.$subject.'终审意见】：<font color="red">'.$data['Suggestion'].'</font><br>';
        }
        if($error != '')
            return $error.'<hr style="color:#ffc;">';
        return $error;
    }
}
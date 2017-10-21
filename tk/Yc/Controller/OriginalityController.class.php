<?php
/**
 * 原创平台
 * @author demo 15-12-28
 */
namespace Yc\Controller;
class OriginalityController extends IndexController{
    
    /**
     * 协同命制专题
     */
    public function originality(){
        $SubjectID = I('SubjectID','12','intval') ;//学科ID
        //获取学科缓存数据        
        $subjetCache = SS('subject');
        $subjectData = array();
        foreach($subjetCache as $value){
            if($value['PID'] != 0 && '高中' == $value['ParentName']){
                $subjectData[$value['SubjectID']] = array(
                    'SubjectID'=>$value['SubjectID'],
                    'SubjectName'=>$value['SubjectName']
                );
            }
        }
        //验证用户是否登录，同时生成相关html
        $username = $this->getCookieUserName('Home');
        /*是否登陆  是否是教师分配学科变量*/
        if($username != ''){//教师登陆
            $userModel = $this->getModel('User');
            $buffer = $userModel->getInfoByName($username,'Whois,SubjectStyle,RealName');
            //如果未设置学科，则获取缓存数据
            if(!$buffer[0]['SubjectStyle']){
                $SubjectID = $this->getCookieSubjectID();
            }else{
                $SubjectID = $buffer[0]['SubjectStyle'];
            }
            if($buffer[0]['Whois']==1){
                if($SubjectID){
                    $subjectData = array($subjectData[$SubjectID]);
                }
                $username = '<a href="javascript:;"><span class="icon1"></span>
                                    您好，'.($username).'</a>
                                  <a href="'.U('Yc/Originality/loginOut').'"><span class="icon2"></span></a>';
            }else{//不是教师直接跳转到提分系统
                header('location:/Aat');
                exit;
            }
        }else{//没有登陆 或者 不是教师
            $username = '<a id="needLogin" href="javascript:;"><span class="icon1"></span>您好，欢迎访问题库原创试卷协同命制平台</a>';
        }
        $this->assign('subject', $subjectData);
        $this->assign('username', $username);
        $this->assign('SubjectID', $SubjectID);
        //查询该时间段内的指定期数
        $originalityStage = $this->getModel('OriginalityStage');
        $stage = $originalityStage->getCurrentStage();
        if(!$stage){
            header('location:/Index');
        }
        $this->assign('endTime', $stage['EndTime']);//结束时间
        if(empty($SubjectID)){
            $SubjectID = 12;
        }
        //查询模板数据
        $tmpl = $originalityStage->selectOriginality('OriginalityTemplate t', 'TID,DocType', 't.SID = '.$stage['SID'].' and t.SubjectID = '.$SubjectID);
        //获取试卷类型
        $docType = SS('examType');
        //模板数据不存在时，输出默认内容
        if(empty($tmpl)){
            $this->assign('docType', $docType);
            $this->assign('tplInfo', array());
            $this->display();
            exit;
        }
        $docTypeName = $docType[$tmpl[0]['DocType']] ;//现在默认取原创试卷
        unset($docType);
        $this->assign('docType', array($docTypeName));

        $tt =  $originalityStage->selectOriginality('OriginalityTemplateTest tt', '*', 'tt.TID = '.$tmpl[0]['TID']);
        if(!empty($tt)){
            $list = array();
            foreach($tt as $value){
                $list[$value['TTID']] = $value;
            }
            $tt = $list;
            foreach ($tt as $i=>$t){
                $tkWhere[] = $t['TTID'];//获得当前知识点条件
            }
            $tmp = array();
            $tkWhere = 'TTID IN('.implode(',', $tkWhere).')';
            $tmp = $originalityStage->selectOriginality('OriginalityTestKnowledge', 'TTID,KlID', "{$tkWhere}");

            $tk = SS('knowledge');//获得知识点
            //生成知识点数据   2015-9-22
            foreach($tmp as $i=>$in){
                //$in 都是数组
                $ttid = $in['TTID'];
                if(!array_key_exists('klName', $tt[$ttid])){
                    $tt[$ttid]['klName'] = array(
                        'klName' => array(),
                        'klId' => array()
                    );
                }
                $tt[$ttid]['klName']['klName'][] = $tk[$in['KlID']]['KlName'];
                $tt[$ttid]['klName']['klId'][] = $in['KlID'];
            }
            unset($tk,$tmp);

            /*命题人ID*/
            //此处的UserID ASC 不能更改，后面将按照此排序计算出“原创次数”  2015-9-22
            $tmp = $originalityStage->selectOriginality('OriginalityRelateTest rt', 'TTID, UserID', 'rt.'.$tkWhere, 'UserID ASC'); 
            $list = array();
            //去除重复的用户编号  2015-9-22
            foreach ($tmp as $key => $value){
                $list[$key] = $value['UserID'];
            }
            $list = array_unique($list);
            if(!empty($list)){
                //获取用户名的数据
                $list = $originalityStage->selectOriginality('User', 'UserID, UserName', 'UserID IN('.implode(',', $list).')');
            }else{
                $list = array();
            }
            
            $temp = array();
            //用户名组合成键值关系
            foreach($list as $key=>$value){
                $temp[$value['UserID']] = $value['UserName'];
            }
            $list = $temp;
            unset($temp);
            //生成用户名信息  2015-9-22
            foreach($tmp as $value){
                $ttid = $value['TTID'];
                if(!array_key_exists('UserNumCount', $tt[$ttid])){
                    $tt[$ttid]['UserNumCount'] = 0;
                }
                if(!array_key_exists('UserID', $tt[$ttid])){
                    $tt[$ttid]['UserID'] = $tt[$ttid]['UserName'] = array();
                }
                //获取参与人数，重复不累计
                if(!in_array($value['UserID'], $tt[$ttid]['UserID'])){
                    $tt[$ttid]['UserNumCount'] = ++$tt[$ttid]['UserNumCount'];
                }
                $tt[$ttid]['UserID'][] = $value['UserID'];
                $size = count($tt[$ttid]['UserID']); //获取当前总共包含多少个用户id
                //如果包含1个以上，则总是对数组的上一条数据做比较
                if($size > 1){
                    $sub = 1;
                    //获取上一条数据
                    $prev = $tt[$ttid]['UserID'][$size-2];
                    //上一个试题的用户和本次的用户为同一人
                    if($prev == $tt[$ttid]['UserID'][$size-1]){
                        $i = $size - 1;
                        //查找之前的所有与之相匹配的用户id
                        while($i >= 1 && $tt[$ttid]['UserID'][$i-1] == $prev){
                            $i--;
                        }
                        $sub = $size - $i; //计算出改用户id出现的次数
                    }
                }else{
                    $sub = 1;
                }
                $sub = formatString('num2Chinese',$sub);
                $hiddenUserName = formatString('hiddenUserName',$list[$value['UserID']]);
                $tt[$ttid]['UserName'][] = "<li><span>原创{$sub}</span>命题人：{$hiddenUserName}</li>";
            }
            //组合模板试题命名人
            unset($list);
            /*难度  参与人数  题型*/
            $diff = C('WLN_TEST_DIFF');
            $testTypes = SS('types');
            foreach ($tt as $i=>$t){
                unset($tt[$i]['UserID']);
                $tt[$i]['klName']['klName'] = implode('<br>', $tt[$i]['klName']['klName']);
                $tt[$i]['klName']['klId'] = implode(',', $tt[$i]['klName']['klId']);
                $tt[$i]['awarenessPercent'] = $diff[$t['Diff']][3].'-'.$diff[$t['Diff']][4];//难度 正答率
                $tt[$i]['typeName'] = $testTypes[$t['TypesID']]['TypesName'];//题型名
                //$tt[$i]['UserNum'] = count($t['UserName']);//当前参与人数
                $n = (int)$t['UserNumCount'];///home-CustomTestStore-add
                if($t['LimitNum'] - $n > 0){
                    $tt[$i]['UserNum'] = '已有'.$n.'人参与，余'.($t['LimitNum']-$n).'名席位';
                    if($stage['EndTime'] >= time()){
                        $tt[$i]['UserNum'] .= '<a tid="'.$tmpl[0]['TID'].'" diff="'.$t['Diff'].'" ttid="'.$t['TTID'].'" typeid="'.$t['TypesID'].'" klid="'.$tt[$i]['klName']['klId'].'" href="#">我要原创</a>';
                    }
                }else{
                    $tt[$i]['UserNum'] = '已有'.$n.'人参与，余0名席位' ;
                }
                if($i == 0){//第一道模板试题
                    if($t['TestNum']>1){
                        $tt[$i]['tihao'] = '第1~'.($t['TestNum']+1).'题';
                        $tt[$i]['tihaoNum'] = $t['TestNum']+1;//不显示 用于程序计数
                    }else{
                        $tt[$i]['tihao'] = '第1题';
                        $tt[$i]['tihaoNum'] = 1;
                    }
                }else{
                    $currentTihaoNuM = $tt[$i-1]['tihaoNum'] + 1;
                    $addNum = $t['TestNum']-1;
                    $tt[$i]['tihao'] = '第'.$currentTihaoNuM;
                    $tt[$i]['tihao'] .= $addNum > 0 ? '~'.($currentTihaoNuM+$addNum) : '';
                    $tt[$i]['tihao'] .= '题';
                    $tt[$i]['tihaoNum'] = $currentTihaoNuM + $addNum;
                    unset($tt[$i-1]['tihaoNum']);//删除计数
                }
                $tmp1 = explode(',', $t['Score']);
                $tmp2 = array_unique($tmp1);
                if(count($tmp1) == count($tmp2)){//小题分值一样
                    $tt[$i]['ScoreSting'] .= '每小题'.$tmp1[0].'分<br />共'.array_sum($tmp1).'分';
                }else{//小题分值不一样
                    foreach ($tmp1 as $j=>$m){
                        $tt[$i]['ScoreSting'] .= '第'.($j+1).'道小题'.$m.'分<br />';
                    }
                    $tt[$i]['ScoreSting'] .= '共'.count($tmp1).'道小题，共'.array_sum($tmp1).'分';
                }
            }
            unset($diff,$testTypes);
        }
        $this->assign('tplInfo', $tt);
        $this->display();
    }

    /**
     * 检查原创模板的试题是否过期或者试题已经加入审核任务
     * @author demo 
     */
    public function originalityCheck(){
        $tid = (int)$_POST['tid'];
        if(!$tid){
            exit('failure');
        }
        $ot = new \Yc\Model\OriginalityTemplateModel($tid);
        $result = $ot->isAllow();
        $info = 'success';
        if($result === false){
            $info = 'failure';
        }
        exit($info);
    }
    
    /**
     * 复制home/index
     * 有冲突 退出成功 退出失败 
     * 退出
     */
    public function loginOut() {
        $username=$this->getCookieUserName('Home');
        if($username){
            $this->setCookieUserName(null, null, 'Home');
            $this->setCookieTime(null, null, 'Home');
            $this->setCookieCode(null, null, 'Home');
            $this->userLog('用户登录', '用户【' . $username . '】退出原创试卷协同命制平台',$username);
        }
        header("location:".U('/yc'));
    }
}
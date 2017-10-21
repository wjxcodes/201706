<?php
/**
 * @author demo
 * @date 2015年6月7日
 */
/**
 * 基础控制器类，用于处理官网基础数据相关操作
 */
namespace Common\Controller;
class SystemLayerController extends CommonController{
    /**
     * 描述：统一检测网站是否关闭
     * 12-26 增加关闭回调函数，各个分组可以设置不同处理逻辑
     * @author demo 5.7.15
     */
    public function checkWebClose(){
        //groupName取值Aat AatApi Home Teacher Exam Manage Index School Marking
        $groupName = $this->getMainModuleName();
        //配置文件关闭
        $ifClose = C('WLN_ClOSE_TRUE_'.strtoupper($groupName));
        $ifClose = ($ifClose==1&&$_GET['jumperror']!=1)?1:0;
        $reason = str_replace('{#$closeEndTime#}',C('WLN_CLOSE_END_TIME'),C('WLN_CLOSE_REASON'));
        //配置文件没有关闭时检测后台关闭
        $config = SS('system')[$groupName];
        if($ifClose!=1&&isset($config)&&array_key_exists('Switch',$config)&&$config['Switch']==1){
            $ifClose = 1;
            $reason = '管理员暂时关闭了网站，请稍后访问！';
        }
        if($ifClose==1){
            //没回调，分组默认
            if (IS_AJAX) {
                header('Content-Type:application/json; charset=utf-8');
                if ($groupName == 'Aat') {
                    exit(json_encode(['data' => null,'info'=>$reason, 'status' => 0]));
                } elseif ($groupName == 'AatApi') {
                    exit(json_encode(['data' => null,'info'=>$reason, 'status' => 0]));
                }else {
                    exit(json_encode(['data' => $reason, 'status' => 0]));
                }
            } else {
                exit('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $reason);
            }
        }
    }

    /**
     * EXCEL导出数据公共方法
     * @param $excelMsg array excel文档内容信息
     * @param $excelData array $excelData 数据内容信息
     * @param $excelName array $excelName EXCEL题目名称
     * @return string
     * @author demo
     */
    public function excelExport($excelMsg,$excelData,$excelName,$path=''){
        import("Common.Tool.PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objPHPExcel->setActiveSheetIndex(0);
        foreach($excelMsg as $i=>$iExcelMsg){
            $objPHPExcel->getActiveSheet()->getColumnDimension($iExcelMsg["keyNum"])->setWidth($iExcelMsg["width"]);
        }
        $excelObj=$objPHPExcel->setActiveSheetIndex(0);
        foreach($excelMsg as $i=>$iExcelMsg){
            $excelObj=$excelObj->setCellValue($iExcelMsg['keyNum2'], $iExcelMsg["keyName"]);
        }
        if ($excelData) {
            foreach($excelData as $i=>$iValue) {
                $j = $i +2;
                $contentObj=$objPHPExcel->setActiveSheetIndex(0);
                foreach($iValue as $k=>$kValue){
                    $tmp=array();
                    if(strstr($kValue,'{#urlTag#}')){
                        $tmp=explode('{#urlTag#}',$kValue);
                        $kValue=$tmp[0];
                    }
                    $contentObj=$contentObj->setCellValue($excelMsg[$k]['keyNum'].$j, $kValue);
                    if(!empty($tmp[1])){
                        $contentObj->getCell($excelMsg[$k]['keyNum'].$j)->getHyperlink()->setUrl($tmp[1]);
                    }
                }
            }
        } else {
            $j = 2;
            $noMsg=$objPHPExcel->setActiveSheetIndex(0);
            foreach($excelMsg as $i=>$iExcelMsg){
                $noMsg=$noMsg->setCellValue($iExcelMsg['keyNum']. $j, '暂无数据');
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle($excelName['title']);
        $objPHPExcel->setActiveSheetIndex(0);
        //header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8");
        $filename = iconv('UTF-8','GB2312//IGNORE',$excelName['excelName'] . "_" . date("Y-m-d", time()) . "_" . time() . ".xls");

        $objWriter = \PHPExcel_IOFactory :: createwriter($objPHPExcel, "Excel5");
        if($path){
            $objWriter->save($path);
            return ;
        }
        header('content-Type:application/vnd.ms-excel;charset=utf-8');
        header("Content-Disposition: attachment;filename=\"" . $filename . "\"");
        header("Cache-Control: max-age=0");
        $objWriter->save("php://output");
    }

    /**
     * 根据参数，重新配置excel内容标题
     * @param $keyName array 单元格每列名称
     * @param $keyWidth array 单元格每列宽度
     * @return array
     * @author demo
     */
    public function resetExcelMsg($keyName,$keyWidth){
        if(!empty($keyName) && !empty($keyWidth)){
            for($i=0;$i<count($keyName);$i++){
                $excelMsg[$i]['keyNum']=chr(65+$i);
                $excelMsg[$i]['keyNum2']=chr(65+$i).'1';
                $excelMsg[$i]['keyName']=$keyName[$i];
                $excelMsg[$i]['width']=$keyWidth[$i];
            }
            return $excelMsg;
        }
    }

    /**
     * 防采集
     * @return int 0正常 1警告 2禁用
     * @author demo 
     */
    public function setHttpLimit(){
        if(C('WLN_HTTP_LIMIT')===false){
            return true;
        }
        $moduleName = $this->getMainModuleName();
        $maxSession=100;//单位时间内访问次数
        $maxTimes=10;//单位时间
        if($moduleName=='Home'||$moduleName=='Teacher'){//框架页和其他分组统一
            $maxSession = 100;
            $maxTimes = 10;
        }
        $error = 0;
        $username = $this->getCookieUserName();
        //防采集
        $questTimes=session('questTimes');
        if(empty($questTimes)) $questTimes=time().'|0|0';
        $tmpArr=explode('|',$questTimes);
        //重置计数
        if(time()-$tmpArr[0]>$maxTimes){
            $tmpArr[0]=time();//当前时间
            $tmpArr[1]=0;//次数
            $tmpArr[2]=0;//是否警告
            session('questTimes',implode('|',$tmpArr));
        }elseif($tmpArr[1]>$maxSession && $tmpArr[2]>1 && $username){//禁用账户
            $tmpArr[0]=time();
            $tmpArr[1]=0;
            $tmpArr[2]=0;
            $this->getModel('User')->changeUserStatus($username,1);
            $this->userLog('用户锁定', '用户【' . $username . '】因为访问频繁锁定',$username);
            session('questTimes',implode('|',$tmpArr));
            $error = 2;
        }elseif($tmpArr[1]>$maxSession){//警告处理
            $tmpArr[1]=$maxSession/2;
            $tmpArr[2]+=1;
            session('questTimes',implode('|',$tmpArr));
            $error = 1;
        }else{//正常累加
            $tmpArr[1]++;
            session('questTimes',implode('|',$tmpArr));
        }
        if($error>0){
            $errorMsg = C('ERROR_30210');
            if($moduleName=='AatApi'){
                //外部手机端接口
                exit(json_encode(['data' => null,'info'=>$errorMsg, 'status' => 0]));
            }
            if(IS_AJAX){
                header('Content-Type:application/json; charset=utf-8');
                if ($moduleName == 'Aat') {
                    exit(json_encode(['data' => null,'info'=>$errorMsg, 'status' => 0]));
                }else {
                    $this->setError('30210',1);
                }
            }else{
                if($moduleName=='Aat'){
                    $this->redirect('Aat/Default/index');
                }else{
                    $this->setError('30210');
                }
            }
        }else{
            return true;
        }
    }
    /**
     * 错误提示
     * @param string $msgDetail 错误提示标题
     * @param string $link 跳转地址
     * @param bool $autoRedirect = true 跳转地址
     * @param int $seconds=3 等待时间
     * @param sting $displayContent 调取模板名称
     * @return bool
     * @author demo
     */
    public function showErrorMsg($msgDetail, $link='', $autoRedirect = true, $seconds = 3,$displayContent){
        if ($link) {
            $links[0]['text'] = '进入>>';
            $links[0]['href'] = $link;
            $links[0]['target'] = '_self';
        }else{
            $links[0]['text'] = '返回上一页';
            $links[0]['href'] = 'javascript:history.go(-1)';
            $links[0]['target'] = '_self';
        }
        $this->assign('msg_detail', $msgDetail);
        $this->assign('links', $links);
        $this->assign('icon', 'ico_dh');
        $this->assign('jumpUrl', $links[0]['href']);
        $this->assign('auto_redirect', $autoRedirect);
        $this->assign('waitSecond', $seconds);
        $this->display($displayContent);//'Public/error'
        exit;
    }
    /**
     * 成功提示
     * @param string $msgDetail 错误提示标题
     * @param string $link 跳转地址
     * @param int $seconds=3 等待时间
     * @param sting $displayContent 调取模板名称
     * @return bool
     * @author demo
     */
    public function showSuccessMsg($msgDetail, $link='',  $seconds = 3,$displayContent){
        if ($link) {
            $links[0]['text'] = '进入>>';
            $links[0]['href'] = $link;
            $links[0]['target'] = '_self';
        }else{
            $links[0]['text'] = '返回上一页';
            $links[0]['href'] = 'javascript:self.location=document.referrer;';
            $links[0]['target'] = '_self';
        }
        $this->assign('msg_detail', $msgDetail);
        $this->assign('links', $links);
        $this->assign('auto_redirect', true);
        $this->assign('icon', 'ico_dt');
        $this->assign('msgTitle', '操作成功');
        $this->assign('message', $msgDetail);
        $this->assign('jumpUrl', $links[0]['href']);
        $this->assign('waitSecond', $seconds);
        $this->display($displayContent);
        exit;
    }
    /**
     * 处理404页面，get请求
     * 当分隔符和系统不匹配时，添加分隔符自动纠错，匹配时不处理跳转404
     * 例如：
     * /Aat-ff-ff?param=f-f.html =》/Aat/ff/ff?param=f-f.html
     * /Aat-ff-ff =》/Aat/ff/ff
     * /Aat-ff-ff-param-zz =》/Aat/ff/ff/param/zz
     * @author demo
     */
    public function jumpError(){
        $url = substr(__CONTROLLER__,1);
        $department = C('URL_PATHINFO_DEPR');
        if(strpos($url,$department)===false){
            //可能需要转换
            $param = '';
            $url = __SELF__;
            $headerUrl = $url;
            if(strpos($url,'?')!==false){
                $urlArr = explode('?',__SELF__,2);
                $url = $urlArr[0];
                $param = '?'.$urlArr[1];
            }
            if(strpos($url,'-')!==false){
                //转换-到$department
                $headerUrl = str_replace('-',$department,$param?__CONTROLLER__:__SELF__);
            }elseif(strpos($url,'/')!==false){
                //转换/到$department
                $headerUrl = str_replace('/',$department,$param?__CONTROLLER__:__SELF__);
            }
            $headerUrl = $headerUrl.$param;
            if(IS_GET){
                header('http/1.1 301 moved permanently');
                header('Location:'.$headerUrl);
            }else{
                //兼容手机端post，web端因为没有cookie传递，无效
                echo(simpleCurl($_SERVER['HTTP_HOST'].$headerUrl,http_build_query($_POST),$header=null,$isPost=true));
            }
        }else{
            //分隔符应该没错，不需要转换
            if(!IS_AJAX){
                header("HTTP/1.0 404 Not Found");
                $base_url = C('WLN_URL_404');
                header("Location:$base_url");
            }else{
                $this->ajaxReturn(['data'=>'网络地址请求错误，请联系我们！','status'=>0]);
            }
        }
    }
    /**
     * 兼容错误码和replace内容
     * @param array $errorNum 错误码数组
     * @return string|json
     * @author demo
     */
    public function formatError($errorNum){
        if(is_string($errorNum)) return $errorNum;
        $output=array();
        foreach($errorNum as $i=>$iErrorNum){
            if(is_array($iErrorNum)){
                $output[0][]=$iErrorNum[0];
                $output[1][]=$iErrorNum[1];
                continue;
            }
            $output[0][]=$iErrorNum;
            $output[1][]='';
        }
        return $output;
    }


   /**
     * ajax 返回所有错误码
     * @param string $errorNum 错误码 多个则以逗号间隔
     * @param int $flag=0 类型 默认0返回错误页面 1返回ajax数据 2返回字符串
     * @param string $url 跳转路径
     * @param string $replace 错误码中%s替换 多个则以逗号间隔
     * @param string $diplayContent='Public/error' 默认加载模板
     * @return string|json
     * @author demo
     */
    public function ajaxSetError($errorNum,$flag=0,$url='',$replace='',$displayContent='Common/error'){
        //之前的api返回的是文字，现在是Aat下错误码
        if('AatApi' === MODULE_NAME){
            C(load_config(APP_PATH.'Aat/Conf/config'.CONF_EXT));
            $flag = 1;
        }

        if(!$errorNum) return ; //错误码为空

        //兼容多个错误码
        if(!is_array($errorNum)) $numArray=explode(',',$errorNum);
        else $numArray=$errorNum;

        $error=array();
        foreach($numArray as $iNumArray){
            $error[]=C('ERROR_'.$iNumArray);
        }

        //是否存在需要替换的数据
        if($replace){
            if(!is_array($replace)) $replaceArray=explode(',',$replace);
            else $replaceArray=$replace;

            foreach($error as $i=>$iError){
                $error[$i]=str_replace('%s',$replaceArray[$i],$iError);
            }
        }
        $error=implode(',',$error);

        if(empty($error)){
            $error=$errorNum; //错误描述为空
            $errorNum=0;
        }
        //返回类型
        switch($flag){
            case 0:
                $this->showErrorMsg($error, $url,true,3,$displayContent);
                break;
            case 1:
                if($url){
                    $data['data']=$error;
                    $data['url']=$url;
                }else{
                    $data=$error;
                }
                $newData['data']=$data;
                $newData['status']=$errorNum;
                $this->ajaxReturn($newData,'json');
                break;
            case 2:
                return $error;
                break;
        }
    }
    /**
     * 返回正确数据
     * @param string $data 需要返回的数据
     * @param string $url 跳转地址
     * @param int $second 跳转间隔时间
     * @return json
     * @author demo
     */
    public function ajaxSetBack($data,$url='',$second=3){
        if('AatApi' ==  MODULE_NAME || IS_AJAX || $data['return']==2){
            $newData['data']=$data;
            $newData['info']='success';
            $newData['status']=1;
            $this->ajaxReturn($newData,'json');
        }
        $this->showSuccessMsg($data,$url,$second);
    }

    /**
     * 数据导出分页
     * @param int $count 总数
     * @param int $prepage 每页数量
     * @param array $map=array() 参数数组 用于跳转到分页时带查询参数 格式为array('key'=>'value')
     * @return string
     * @author demo
     * */
    public function exportPageList($count,$prepage,$map=array()){
        $count=ceil($count/$prepage);
        $linkPage = "";
        $url='';
        if($map){
            foreach($map as $key=>$val) {
                $url   .=   "$key=".urlencode($val).'&';
            }
        }
        parse_str($url,$parameter);
        $parameter['p']  =   '__PAGE__';
        $replaceUrl=str_replace('downLog','export',U('',$parameter));
        $url            =   $replaceUrl;
        for($i=1;$i<=$count;$i++){
            $limitShow=(($i-1)*2000).','.$i*2000;
            $page       =   $i;
            $linkPage .="<tr align='center'><td width='150' >".$limitShow." 条</td><td width='100'><a href='".str_replace('__PAGE__',$page,$url)."'>下载</a></td></tr>";
        }
        return $linkPage; // 赋值分页输出
    }
}


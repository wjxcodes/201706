<?php
/**
 * 默认控制器
 * @author demo 15-12-24
 */
namespace Common\Controller;
class DefaultController extends CommonController{

    public function __construct(){
        parent::__construct();
        $this->initial();
    }

    public function initial(){
        $mainModule = $this->getMainModuleName();
        //关闭网站
        R('Common/SystemLayer/checkWebClose');
        //防采集
        R('Common/SystemLayer/setHttpLimit');
        //缓存控制
        $this->cacheControl();

        //获取配置
        $config=SS('system')[$mainModule];

        //是否关闭网站
        if(isset($config)&&array_key_exists('Switch',$config)&&$config['Switch']==1){
            exit('网站暂时关闭，请稍后访问！');
        }

        $this->assign('config',$config);

        //权限登录验证
        $power = R('Common/PowerLayer/checkLoginAndPower',array($mainModule));
        $this->ifDiff = $power['ifDiff'];
        $this->ifSubject = $power['ifSubject'];
        $this->mySubject = $power['mySubject'];
    }

    /**
     * 清除缓存
     * @return null
     * @author demo
     */
    private function cacheControl(){
        $mainModule = $this->getMainModuleName();
        if($mainModule=='Manage'||$mainModule=='Teacher'){
            header("Cache-Control:no-cache,must-revalidate,no-store"); //这个no-store加了之后，Firefox下有效
            header("Pragma:no-cache");
            header("Expires:-1");
        }
    }

    /**
     * 根据用户权限标记，获取权限值
     * 调用用户cookie值和权限缓存，根据权限名称（例如：limitDown）返回权限值，
     * @param string $tag 用户权限标记
     * @return int
     * @author demo
     */
    protected function getUserPowerByTag($tag){
        (new PowerLayerController())->getUserPowerByTag($tag);
    }

    /**
     * 记录班级日志
     * @param int $classID 班级id
     * @param int $userID 用户id
     * @param string $content 日志描述
     * @param string $username 用户名 默认空
     * @return null
     * @author demo
     */
    protected function classDynamic($classID,$userID,$content,$username=''){
        if(!$username){
            $username=$this->getCookieUserName();
        }
        $buffer=$this->getModel('User')->getInfoByName($username);
        $username=empty($buffer[0]['RealName']) ? $username : $buffer[0]['RealName'];

        $dynamicType=$this->getModel('Dynamic')->getDynamicType();
        $data=array();
        $data['ObjType']=$dynamicType['Class'];
        $data['ObjID']=$classID;
        $data['Content']= '<span class="u_id" uid="'.$buffer[0]['UserID'].'">@'.$username.'</span>'.$content;
        $data['UserID']=$buffer[0]['UserID'];
        $data['LoadTime']=time();
        R('Common/ClassLayer/addClassDynamic',array($data,$userID));
    }

    /**
     * 验证学科权限是否异常
     * @param int|array $subjectID 学科id
     * @param int $admin 用户名
     * @param int $ajax是否是ajax 0不是 1是
     * @author demo
     */
    protected function powerCheckSubject($subjectID,$admin,$ajax=0){
        (new PowerLayerController())->powerCheckSubject($subjectID,$admin,$ajax);
    }

    /**
     * 返回适用于ajax的分页
     * @param int $count 总数量
     * @param int $perpage 每页数量
     * @param array $map 参数
     * @return string
     * @author demo
     */
    protected function ajaxPageList($count,$perpage,$map){
        // 分页栏每页显示的页数
        $rollPage = 5;
        // 页数跳转时要带的参数
        $parameter = $map;
        // 分页URL地址
        $url     =   '';
        // 默认列表每页显示行数
        $listRows = $perpage;
        // 总行数
        $totalRows =$count;
        // 分页总页面数
        $totalPages =ceil($totalRows/$listRows);     //总页数
        // 分页的栏的总页数
        $coolPages =ceil($totalPages/$rollPage);
        // 分页显示定制
        $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
        // 默认分页变量名
        $varPage =C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(0 == $count) return '';
        $p = $varPage;
        // 当前页数
        $nowPage =!empty($_GET[$varPage])?intval($_GET[$varPage]):1;
        $nowCoolPage    =   ceil($nowPage/$rollPage);
        if($nowPage<1){
            $nowPage  =   1;
        }elseif(!empty($totalPages) && $nowPage>$totalPages) {
            $nowPage  =   $totalPages;
        }
        // 起始行数
        $firstRow     =   $listRows*($nowPage-1);

        // 分析分页参数
        if($url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($parameter && is_string($parameter)) {
                parse_str($parameter,$parameter);
            }elseif(empty($parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $nowPage-1;
        $downRow        =   $nowPage+1;
        if ($upRow>0){
            $upPage     =   "<a data='".str_replace('__PAGE__',$upRow,$url)."' href='javascript:;'>".$config['prev']."</a>";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $totalPages){
            $downPage   =   "<a data='".str_replace('__PAGE__',$downRow,$url)."' href='javascript:;'>".$config['next']."</a>";
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $nowPage-$rollPage;
            $prePage    =   "<a data='".str_replace('__PAGE__',$preRow,$url)."' href='javascript:;' >上".$rollPage."页</a>";
            $theFirst   =   "<a data='".str_replace('__PAGE__',1,$url)."' href='javascript:;' >".$config['first']."</a>";
        }
        if($nowCoolPage == $coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $nowPage+$rollPage;
            $theEndRow  =   $totalPages;
            $nextPage   =   "<a data='".str_replace('__PAGE__',$nextRow,$url)."' href='javascript:;' >下".$rollPage."页</a>";
            $theEnd     =   "<a data='".str_replace('__PAGE__',$theEndRow,$url)."' href='javascript:;' >".$config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$rollPage;$i++){
            $page       =   ($nowCoolPage-1)*$rollPage+$i;
            if($page!=$nowPage){
                if($page<=$totalPages){
                    $linkPage .= "<a data='".str_replace('__PAGE__',$page,$url)."' href='javascript:;'>".$page."</a>";
                }else{
                    break;
                }
            }else{
                if($totalPages != 1){
                    $linkPage .= "<a class='current'>".$page."</a>";
                }
            }
        }
        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($config['header'],$nowPage,$totalRows,$totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$config['theme']);
        return $pageStr;
    }

    /**
     * 分页函数 赋予模板变量page为分页字符串
     * @param int $count 总数量
     * @param int $prepage 每页数量
     * @param array $map 跳转链接参数数组
     * @return null
     * @author demo
     */
    protected function pageList($count,$prepage,$map=array()){
        $page = handlePage('init',$count,$prepage);
        if($map){
            foreach($map as $key=>$val) {
                $page->parameter   .=   "$key=".urlencode($val).'&';
            }
        }
        $show = $page->show();
        $this->assign('page', $show); // 赋值分页输出
    }

    /**
     * 描述：生成csrf安全码
     * @param string $platform 分组名称取值 aat home teacher Manage index
     * @param string $userName 当前用户名，没有为空
     * @return string
     * @author demo
     */
    protected function generateCsrfToken($platform, $userName) {
        $userName = $userName ? $userName : '';//防止其他字符串出现
        $token = md5($platform . $userName . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . date('Ymd', time()));
        return $token;
    }

    /**
     * 描述：验证csrf安全码
     * @param string $platform 分组名称取值 aat home teacher Manage index
     * @param string $userName 当前用户名，没有为空
     * @return bool
     * @author demo
     */
    protected function checkCsrfToken($platform, $userName) {
        $userToken = $_REQUEST['_csrf'];
        if (!$userToken) {
            return false;
        }
        $hash = md5($platform . $userName . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . date('Ymd', time()));
        $hashNoUsername = md5($platform . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . date('Ymd', time()));
        //部分特殊情况如用户第一次登录，本地没有用户名，但是登录接口有，所以做兼容
        if ($userToken !== $hash&&$userToken!==$hashNoUsername) {
            return false;
        }
        return true;
    }


    /**
     * 返回分页信息
     * @param array $map 分页查询参数
     * @param int $count 总记录数
     * @param int $page 当前页
     * @param int $prepage 每页显示数量
     * @param string $urlPrefix url前缀
     * @param int $showPage 显示多少个分页链接
     * @return array
     * @author demo 2015-9-17
     */
    protected function fillPagtion($map, $count, $page, $prepage, $urlPrefix, $showPage=10){
        $splitStr = C('URL_PATHINFO_DEPR');
        $totalPage = ceil($count / $prepage);
        $data = array();
        $offset = ceil($page / $showPage) * $showPage;
        $param = array();
        //生成查询参数
        foreach($map as $k=>$v){
            if(!empty($v) || is_numeric($v)){
                $param[] = $k.$splitStr.urlencode($v);
            }
        }
        if(count($param) == 0){
            $param = '';
        }else{
            $param = $splitStr.implode($splitStr, $param);
        }
        //此处生成相关选项参数的链接地址
        if(count($map) > 0){
            foreach($map as $k=>$v){
                $data[$k]['c'] = str_replace($splitStr, '', $v); //去除与url配置文件相冲突的分隔符
                $key = $splitStr.$k.$splitStr;
                $temp = str_replace('/', '\/', $key);
                if(!preg_match("/({$temp})(\d+|\w+)*/is", $param)){
                    $data[$k]['a'] = $urlPrefix.$param.$key."%s";
                    if(!empty($v)){
                        $param .= "{$key}{$v}";
                    }
                }else{
                    $temp = preg_replace("/{$temp}[\d|\w]+/is", '', $param);
                    $data[$k]['a'] = $urlPrefix.$temp.$key."%s";
                }
            }
        }
        if($page == $totalPage || $offset > $totalPage){
            $offset = $totalPage;
        }
        //以下生成分页链接 p：指定页数 a：链接 c：当前页
        $links = array();
        $mark = '|#|#';
        $u = $urlPrefix.$splitStr.'p'.$splitStr.$mark.$param;
        $i = $offset - $showPage;
        if($offset > $showPage){
            $links[] = array(
                'p' => 1,
                'a' => str_replace($mark, 1, $u),
                'n' => '首页'
            );
            $links[] = array(
                'p' => 1,
                'a' => str_replace($mark, $page-1, $u),
                'n' => '上一页'
            );
        }else{
            $i = 0;
        }
        for(; $i < $offset; $i++){
            $p = $i+1;
            $a = str_replace($mark, $p, $u);
            if($p == $page){
                $links[] = array(
                    'a' => $a,
                    'n' => $p,
                    'c' => 'c'
                );
            }else{
                $links[] = array(
                    'c' => '',
                    'a' => $a,
                    'n' => $p
                );
            }
        }
        if($i < $totalPage){
            $links[] = array(
                'p' => $i,
                'a' => str_replace($mark, $page+1, $u),
                'n' => '下一页'
            );
            $links[] = array(
                'p' => $i,
                'a' => str_replace($mark, $totalPage, $u),
                'n' => '最后一页'
            );
        }
        $data['pages'] = $links;
        return $data;
    }

    /**
     * 验证用户是否登录
     * @author demo
     * @date 2015-6-6
     */
    protected function homeIsLoginCheck(){
        (new PowerLayerController())->homeIsLoginCheck();
    }

    /**
     * 验证用户登录
     * @param string $modelName 站点名称Home\Manage\Aat\Index\Teacher\Exam
     * @param int $flag 返回类型 1返回数字 0提示并跳转
     * @return int|none    1 已登录 否则返回错误码
     * @author demo
     */
    public function checkLogin($modelName,$flag=0){
        $ifLogin = R('Common/PowerLayer/checkLogin',array($modelName,$flag));
        return $ifLogin;
    }

    /**
     * 生成二维码
     * @author demo
     */
    public function qcode(){
        $str=urldecode($_GET['str']);
        $size=$_GET['size'];
        if(empty($size)){
            $size=200;
        }
        if(empty($str)){
            exit();
        }
        import('Common.Tool.QrCode.QrCodeImageUploadModel');
        (new \Tool\QrCode\QrCodeImageUploadModel())->qrCodeByUrl($str,$size);
    }

}
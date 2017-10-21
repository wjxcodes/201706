<?php
/**
 * @author demo  
 * @date 2014年9月12日
 * @update 2015年1月14日 2015年9月28日
 */
/**
 * 题库官网标签获取
 */
namespace Index\TagLib;
use Think\Template\TagLib;
defined('THINK_PATH') or exit();
class Manage extends TagLib{
    public  $pageSize; //每页显示数量
    public  $pageCount; //总页数
   // private $dbConn; //数据链接XXX
    protected $tags = array(
        'tag' => array('attr' => 'tagname,param', 'close' => 0), // attr 属性列表close 是否闭合（0 或者1 默认为1，表示闭合）
        'focus' => array('attr' => 'target,first', 'close' => 1),
        'newList' => array('attr' => 'status,pagesize', 'close' => 1),
        'newContent' => array('attr' => 'status', 'close' => 1),
        'video' => array('attr' => 'status,types,pagesize', 'close' => 1),
        'videoContent' => array('attr' => 'status,types', 'close' => 1),
        'myPage' => array('attr'=>'type,pagenumcount','close' => 0),
        'coreSystemInfo'=>array('attr'=>'type,csid','close'=>1)
    );
    /**
     * 页面标签
     * @param string $attr 标签名
     * @return string HTML标签字符串
     * @author demo
     */
    public function _tag($attr){
        $tagName = $attr['tagname'];

        //优先使用参数模式
        if(isset($attr['param'])) $param = $attr['param'];
        if(!empty($param)){
            $tagName = $_GET[$param];
        }

        $tag = D("Index/MyTag");
        return $tag->pageTag($tagName);
    }
    /**
     * 图片轮播
     * @param string $attr 分组名
     * @return string HTML标签字符串
     * @author demo
     */
    public function _focus($attr,$content){
        $target = $attr['target'];
        $first = $attr['first'];

        if(empty($target)) return ;
        $buffer = D('Index/ImagePlay')->imagePlay(
                    'Url,Title',
                    'Target = "'.$target.'"',
                    'OrderID ASC'
                );
        $output = '';
        $host=C('WLN_DOC_HOST');
        foreach($buffer as $i=>$iBuffer){
            if($host) $iBuffer['Url'] = $host.$iBuffer['Url'];
            $output .= str_replace('[i/]',$i+1,$content);
            if($i == 0) $output = str_replace('[first/]',$first,$output);
            else  $output = str_replace('[first/]','',$output);
            $output = str_replace('[url/]',$iBuffer['Url'],$output);
            $output = str_replace('[title/]',$iBuffer['Title'],$output);
        }
        return $output;
    }
    
    /**
     * 新闻列表标签
     * @param array $attr 前台条件数组
     * @return string HTML标签字符串
     * @author demo  
     * @update 2015年9月28日
     */
    public function _newList($attr,$content){
        $deslen=$attr['deslen']; //描述长度
        $pageSize = empty($attr['pagesize']) ? 10 : $attr['pagesize'] ;  //每页数量
        $this->pageSize = $pageSize; 
        $where["Status"] = $attr['status'];  //状态
        $where["Types"] = array("in",("官网,通用"));
        $newModel = D('Manage/News');//新闻model
        $pageCount = $newModel->newsCountField($where,'NewID');
        $this->pageCount = $pageCount;
        $page = page($pageCount,$_GET['page'],$pageSize);
        $page=$page.','.$pageSize;
        $buffer = $newModel->newsDatePage(
                       '*',
                       $where,
                       'NewID Desc',
                       $page
                   );
        $output = '';
        foreach($buffer as $i => $iBuffer){
            $output .= $content;
            $output = str_replace('[NewID/]',$iBuffer['NewID'],$output);
            $output = str_replace('[NewTitle/]',$iBuffer['NewTitle'],$output);
            $output = str_replace('[time/]',date("Y-m-d",$iBuffer['LoadDate']),$output);
        
            $iBuffer['NewContent']=R('Common/TestLayer/strFormat',array($iBuffer['NewContent']));
            preg_match('/<img.*?src=["\']{1}([^\'"]*)["\']{1}[^>]*>/iU', $iBuffer['NewContent'] ,$tmp);
            $imgSrc = empty($tmp[1]) ? '__PUBLIC__/index/imgs/in-page/news/news.jpg' : $tmp[1];
            $output = str_replace('[ImgSrc/]',$imgSrc,$output);

            if(empty($deslen)) $deslen=100;
            $output = str_replace('[NewContent/]',formatString('getHtmlDescription',$iBuffer['NewContent'],$deslen),$output);
        }
        return $output;
    }
    /**
     * 新闻内容标签
     * @param array $attr 前台条件数组
     * @return string HTML标签字符串
     * @author demo 
     * @update 2015年9月28日
     */
    public function _newContent($attr,$content){
        $where["NewID"] = $_GET['id'];
        $where["Status"]= $attr['status'];
        $where["Types"]= array("in",("官网,通用"));
        $buffer = D('Manage/News')->newsDate(
                    '*',
                    $where
                );
        $output = '';
        foreach($buffer as $i => $iBuffer){
            $output .= $content;
            $output = str_replace('[NewID/]',$iBuffer['NewID'],$output);
            $output = str_replace('[NewTitle/]',$iBuffer['NewTitle'],$output);
            $output = str_replace('[Types/]',$iBuffer['Types'],$output);
            $output = str_replace('[time/]',date("Y-m-d",$iBuffer['LoadDate']),$output);
            $output = str_replace('[NewContent/]',formatString('IPReturn',$iBuffer['NewContent']),$output);
            $output = str_replace('[NewDescription/]',formatString('getHtmlDescription',$iBuffer['NewContent']),$output);
        }
        return $output;
    }
    /**
     * 视频列表标签
     * 修改 添加分页
     * @param array $attr 前台条件数组
     * @return string HTML标签字符串
     * @author demo 
     * @update 2015年9月28日
     */
    public function _video($attr,$content){
        $where["Status"] = $attr['status'];
        $where["Types"] = $attr['types'];
        $pageSize = empty($attr['pagesize']) ? 10 : $attr['pagesize'] ;
        $this->pageSize = $pageSize;
        $newModel = D('Manage/News');//新闻model
        $pageCount = $newModel->newsCountField($where,'NewID');
        $this->pageCount = $pageCount;
        $page = page($pageCount,$_GET['page'],$pageSize);
        $page=$page.','.$pageSize;

        $buffer = $newModel->newsDatePage(
                '*',
                $where,
                'LoadDate DESC',
                $page
        );
        
        $output = '';

        foreach($buffer as $i => $iBuffer){
            $output .= $content;
            $output = str_replace('[NewID/]',$iBuffer['NewID'],$output);
            $output = str_replace('[NewTitle/]',$iBuffer['NewTitle'],$output);
            $output = str_replace('[Types/]',$iBuffer['Types'],$output);
            $output = str_replace('[time/]',date("Y-m-d",$iBuffer['LoadDate']),$output);
        }
        return $output;
    }
    /**
     * 视频内容标签
     * @param array $attr 前台条件数组
     * @return string HTML标签字符串
     * @author demo 
     * @update 2015年9月28日
     */
    public function _videoContent($attr,$content){
        $where['NewID'] = (int)$_GET['id'];
        $where['Status']= $attr['status'];
        $where['Types']= $attr['types'];
        $newModel = D('Manage/News');//新闻model
        $buffer = $newModel->newsDate('*',$where);
        $next = $newModel->newsDate(
                    'NewID,NewTitle',
                    'Types="'.$attr['types'].'" and Status="'.$attr['status'].'" and NewID < "'.$where['NewID'].'"',
                    'NewID DESC',
                    1
                );//上一个
        $prev = $newModel->newsDate(
                    'NewID,NewTitle',
                    'Types="'.$attr['types'].'" and Status="'.$attr['status'].'" and NewID > "'.$where['NewID'].'"',
                    'NewID ASC',
                    1
                );//下一个
        $output = '';
        foreach($buffer as $i => $iBuffer){
            $output .= $content;
            $output = str_replace('[NewID/]',$iBuffer['NewID'],$output);
            $output = str_replace('[NewTitle/]',$iBuffer['NewTitle'],$output);
            $output = str_replace('[Types/]',$iBuffer['Types'],$output);
            $output = str_replace('[time/]',date("Y-m-d",$iBuffer['LoadDate']),$output);
            $output = str_replace('[NewContent/]',formatString('IPReturn',$iBuffer['NewContent']),$output);
            if($next[0]){
                $output = str_replace('[nextTitle/]','下一篇：'.$next[0]['NewTitle'],$output);
                $output = str_replace('[nextNewID/]',$next[0]['NewID'],$output);
            }else{
                $output = str_replace('[nextTitle/]','',$output);
                $output = str_replace('[nextNewID/]','',$output);
            }
            if($prev[0]){
                $output = str_replace('[prevTitle/]','上一篇：'.$prev[0]['NewTitle'],$output);
                $output = str_replace('[prevNewID/]',$prev[0]['NewID'],$output);
            }else{
                $output = str_replace('[prevTitle/]','',$output);
                $output = str_replace('[prevNewID/]','',$output);
            }
        }
        return $output;
    }
    /**
     * 分页标签
     * 修改  首页不显示上一页  尾页不显示下一页
     * 如果有其他参数 则修改 url 添加其他参数
     * @return string HTML标签字符串
     * @author demo  
     */
    public function _myPage($attr){
        $type = $attr['type'];
        $act = __ACTION__;
        $url = substr(urldecode($_SERVER['REQUEST_URI']), strpos($_SERVER['REQUEST_URI'] , $act) + strlen($act) +1);
        $suffix = C('URL_HTML_SUFFIX');
        if($suffix){
            $repalce =  '.'.ltrim($suffix,'.');
        }
        $url = str_replace($repalce, '', $url);
        //获得参数字符串
        $url = explode(C('URL_PATHINFO_DEPR'),$url);//转化成数组
        if(count($url) > 1){
            $arr = array();
            for($i=0;$i<count($url);$i+=2){
                $arr[$url[$i]] = $url[$i+1];
            }
            unset($arr['page']);
        }
        $pageSize = $this->pageSize;//每页显示数量
        $pageCount = $this->pageCount;
        $totalPage = ceil($pageCount/$pageSize);
        $page = page($pageCount,$_GET['page'],$pageSize);
        $prev =($page-1)>0?$page-1:1;
        $next =($page+1)<$totalPage?$page+1:$totalPage;
        $shouye = $page > 1
                        ? '<a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>1),$arr)).'">首页</a>
                          <a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>$prev),$arr)).'">上一页</a>'
                       : '' ;
        $weiye = $page < $totalPage
                        ? '<a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>$next),$arr)).'">.下一页</a>
                           <a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>$totalPage),$arr)).'">尾页</a>'
                       : '' ;
        if($type == 2){
            $pageNumCount = empty($attr['pagenumcount']) ? 9 : $attr['pagenumcount'] ;//页码数字个数
            $pageNumCount = $pageNumCount > $totalPage ? $pageNumCount : $pageNumCount;
            $pageNumMid = ceil($pageNumCount/2);//中间页码
            $i = $page - $pageNumMid + 1;//开始页码
            $i = $i > $totalPage - $pageNumCount + 1 ? $totalPage - $pageNumCount + 1 : $i ;
            $i = $i < 1 ? 1 : $i ;
            $endPage = $i + $pageNumCount -1 ;//结束页码
            $endPage = $endPage > $totalPage ? $totalPage : $endPage ;
            $yema = $i <= 1 || $totalPage <=$pageNumCount
                    ? ''
                    : '<a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>$i-1),$arr)).'">···</a>';
            while ($i <= $endPage){
                $yema .= '<a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>$i),$arr)).'"';
                $yema .= $i == $page ? ' class="current" ' : '';
                $yema .= '>'.$i.'</a>';
                $i++;
            }
            $yema .= $endPage >= $totalPage || $totalPage <= $pageNumCount
                    ? ''
                    : '<a href="'.U('Index/About/'.ACTION_NAME,array_merge(array('page'=>$endPage+1),$arr)).'">···</a>' ;
            $output = $shouye.$yema.$weiye;
        }else{
            $yema .= '当前 第<b>&nbsp;'.$page .'&nbsp;</b>页';
            $yema .= '共 '.$totalPage.' 页';
            $output = $shouye.$weiye.$yema;
        }
        //如果有下一页，显示分页
        if($totalPage>1){
            return $output;
        }
    }

    /**
     * 核心系统标签
     * @author demo
     */
    public function _coreSystemInfo($attr,$content){
        //此时属性变量还未被模板替换,故此处使用$_GET获取变量
        $type = ucfirst(strtolower($_GET['param']));
        $csid = (int)$_GET['csid'];

        $dbName = 'CoreSystem';
        if($type == 'Faq'){
            $dbName = 'CoreSystem'.$type;
        }

        $dbModel = D('Index/'.$dbName);

        $output  = '';//返回变量
        $output .= $content;
        $noData  = stripslashes_deep(str_replace('[coreSystemInfo/]','<div class="no-data-tips" style="text-align: center;">暂无记录...</div>', $output));//数据为空时候的处理

        if(!$dbModel){//未找到该模型
            return $noData;
        }
        //分类讨论
        if($type == 'Faq'){//常见问题 获取分页数据
            $pageSize = empty($attr['pagesize']) ? 10 : $attr['pagesize'] ;
            $this->pageSize = $pageSize;
            $liStr = '';//字符串中间变量
            $pageCount = $dbModel->selectCount(['CSID'=>$csid],'CSID');
            if($pageCount) {
                $this->pageCount = $pageCount;
                $page = page($pageCount, $_GET['page'], $pageSize);
                $page = $page . ',' . $pageSize;

                $buffer = $dbModel->pageData(
                    'FAQID,Question',
                    ['CSID'=>$csid],
                    'OrderID DESC,AddTime DESC',
                    $page
                );
                if ($buffer) {//存在对应的常见问题
                    $liStr .= '<div class="core-intro-q-list"><ul>';
                    foreach ($buffer as $i => $iBuffer) {
                        $liStr .= '<li><h3><em class="q-icon">Q</em><span class="q-tit">' . $iBuffer['Question'] . '</span><i class="q-icon-arrow" thisID="' . $iBuffer['FAQID'] . '">+</i></h3><div class="q-context"></div></li>';
                    }
                    $liStr .= '</ul></div>';
                    return stripslashes_deep(str_replace('[coreSystemInfo/]',$liStr,$output));
                }
            }
            return $noData;
        }else{//系统简介和流程展示 统一了数据库格式
            $field = 'Detail';
            if($type == 'Flow'){
                $field = 'Flow';
            }
            $buffer = $dbModel->selectData(
                  $field,
                  ['Status'=>1,'CSID'=>$csid],
                  '',
                  1
            );
            $buffer = $buffer[0];
            if(trim($buffer[$field])){
                return stripslashes_deep(str_replace('[coreSystemInfo/]',$buffer[$field],$output));
            }else {
                //简介如果为空
                if ($type == 'Detail') {//系统简介为空从描述中获取内容
                    $buffer = $dbModel->selectData(
                        'Description',
                        ['Status' => 1, 'CSID' => $csid],
                        '',
                        1
                    );
                    $buffer = $buffer[0];
                    if(trim($buffer['Description'])){
                        return stripslashes_deep(str_replace('[coreSystemInfo/]',$buffer['Description'],$output));
                    }
                }
            }
            return $noData;
        }
    }

}
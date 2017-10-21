<?php
/**
 * 官网试卷更新，用户动态action
 * @author demo 2015-15
 */
namespace Doc\Controller;
use Common\Controller\DefaultController;
class DocController extends DefaultController{

    private $prepage = 20;

    private $keywords = array();
    private $description = '';

    private $isWapClient = false;
    private $relModuleName = 'Index';//对应的模块名称

    //外部get请求相关参数列表，参数名称可自定义
    private $params = array(
        'grade' => 'grade',
        'area' => 'area',
        'sid' => 'sid',
        'tid' => 'tid',
        'times' => 'times',
        'year' => 'year'
    );

    public function __construct(){
        parent::__construct();
        $this->isWapClient = isMobile();
    }

    /**
     * 试题预览.
     * @author demo
     */
    public function show(){
        $subjects = $this->getSubjectes();
        $classGrade=SS('gradeListSubject')[2];
        $data['docAttr'] = $this->getDocAttr(0,1); //试卷属性 隐藏不需要显示的数据
        $data['area'] = $this->getArea(); //试卷省份
        $data['grade'] = $classGrade['sub']; //年级
        $data['subjects'] = $subjects;
        $year = ((int)date('Y', time()))+1;
        $years = array();
        for($i=0; $i<9; $i++){
            $years[] = $year--;
        }
        $data['years'] = $years;
        foreach($this->params as $key=>$value){
            $this->params[$key] = (int)(empty($_GET[$value]) ? 0 : $_GET[$value]);
        }
        if(!empty($this->params['sid'])){
            $this->keywords[] = $subjects[$this->params['sid']];
        }
        if(!empty($this->params['area'])){
            foreach($data['area'] as $value){
                if($value['AreaID'] == $this->params['area']){
                    $this->keywords[] = $value['AreaName'];
                    break;
                }
            }
        }
        if(!empty($this->params['grade'])){
            foreach($data['grade'] as $value){
                if($value['GradeID'] == $this->params['grade']){
                    $this->keywords[] = $value['GradeName'];
                    break;
                }
            }
        }
        $result = $this->getDocList();
        $page = $_GET['p'];
        if(empty($page)){
            $page = 1;
        }
        $prepage = $_GET['prepage'];
        if(!empty($perpage)){
            $this->prepage = $perpage;
        }

        $pageLinks = $this->fillPagtion($this->params, $result[1], $page, $this->prepage, U('Doc/Doc/show','', false));

        $this->assign('pages', $pageLinks['pages']);
        unset($pageLinks['pages']);
        $this->assign('pageLinks', $pageLinks);
        $this->assign('keywords', implode(',', $this->keywords));
        $this->assign('description', $this->description);
        $this->assign('result', $result[0]);
        $this->assign('params', json_encode($this->params));
        $this->assign('token', md5(get_client_ip(0,true).$this->token));
        $this->assign('data', $data);
        if($this->isWapClient){
            $this->display('DocWap/show');
            exit;
        }
        $this->display('Doc/show');
    }

    /**
     * 试卷详情页
     * @author demo
     * @date 
     */
    public function showContent(){
        $id = (int)$_GET['id'];
        $doc = $this->getModel('Doc')->getDocIndex(
            array('docid','docname','subjectname','typename','docyear','areaname','loadtime','introfirsttime','typeid','subjectid'),
            array('DocID'=>$id),
            array(),
            array('perpage'=> 1)
        );

        $doc = $doc[0][0];
        if(!$doc['areaname']){
            $doc['areaname'] = '不区分';
        }
        $query = getStaticFunction('TestQuery', 'getInstance');
        // $query = \Test\Model\TestQueryModel::getInstance();
        $test = $this->getTestByDocId($id, array('testid', 'testold','diff', 'typesid', 'testnum', 'numbid'));
        $types = SS('typesSubject');
        $types = $types[$doc['subjectid']];
        $typesMap = array();
        foreach($types as $type){
            $typesMap[$type['TypesID']] = $type;
        }
        $result = array();
        //分卷组合试题内容
        foreach($test as $key=>$value){
            $info = $typesMap[$value['typesid']];
            $order = (int)preg_replace('/\d+-[0]{0,}/', '', $value['numbid']); //获取试题的顺序
            unset($value['typesid'], $value['numbid']);
            $value['type'] = $info['TypesName'];
            $result[(int)$info['Volume']][$value['type']][$order] = $value;
        }
        unset($test, $typesMap);
        $num = 1; //试题数量
        //将试题按照卷-题型-试题的形式组合
        while(list($key, $value) = each($result)){
            foreach($value as $k=>$v){
                asort($v);
                foreach($v as $vk=>$vv){
                    $string = R('Common/TestLayer/changeTagToNum',array($vv['testold'], $num));
                    $link = C('WLN_DOWNLOAD_RANDOM_CONTENT');
                    $link[1] = '/Public/index/images/copyright-img.png';
                    $tags = array('<span %s>%s</span>', '<span %s><img src="%s"  width="48" height="66" alt="题库"/></span>');
                    //$string = \Doc\Model\RandomIdentificationModel::addRandomContent($string, $link, $tags);
                    $string = getStaticFunction('RandomIdentification','addRandomContent',$string, $link, $tags);
                    $value[$k][$vk]['testold'] = $string;
                    $num += $vv['testnum'];
                }
            }
            //将value按照题型进行组合
            $result[$key] = $value;
        }
        $doc['testCount'] = $num - 1;
        $output=array();
        $buffer = SS('typesSubject');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                foreach($iBuffer as $j=>$jBuffer){
                    $output[$i][$j]['TypesID']=$jBuffer['TypesID'];
                    $output[$i][$j]['TypesName']=$jBuffer['TypesName'];
                    $output[$i][$j]['SubjectID']=$jBuffer['SubjectID'];
                    $output[$i][$j]['TypesScore']=$jBuffer['TypesScore'];
                    $output[$i][$j]['DScore']=$jBuffer['DScore'];
                    $output[$i][$j]['Volume']=$jBuffer['Volume'];
                    $output[$i][$j]['IfDo']=$jBuffer['IfDo'];
                    $output[$i][$j]['Num']=$jBuffer['Num'];
                }
            }
        }
        $types = json_encode($buffer, JSON_UNESCAPED_UNICODE);
        $this->assign('test', $result);
        $this->assign('doc', $doc);
        $this->assign('types', $types);
        $this->assign('keywords', implode(',', $this->keywords));
        $this->assign('description', $this->description);
        if($this->isWapClient){
            $this->display('DocWap/showContent');
            exit;
        }
        $this->display('Doc/showContent');
    }

    /**
     * 用户动态默认页
     * @author demo 
     */
    public function user(){
        $page = $_REQUEST['p'];
        if(!$page){
            $page = 1;
        }
        $where = $map = array();
        $subject = $_REQUEST['s'];
        if($subject){
            $map['s'] = $where['SubjectID'] = $subject;
        }else{
            $map['s'] = '';
        }
        $type = $_REQUEST['t'];
        if($type){
            $map['t'] = $where['Classification'] = $type;
        }else{
            $map['t'] = '';
        }
        $where['IfShare'] = 1;
        $where['CheckStatus'] = 1;
        $userDynamic = $this->getModel('UserDynamic');
        $userDynamicResult = $userDynamic->selectDataOfPagtion('', $where, $page, $this->prepage);
        $dynamicType = array(
            'doc' => '试卷文档',
            'work' => '作业文档',
            'case' => '导学案文档',
        );
        $this->assign('dynamicType', $dynamicType);
        $this->assign('userDynamicResult', $userDynamicResult);
        $subjects = $this->getSubjectes();

        $links = $this->fillPagtion($map, $userDynamic->getCount(),$page, $this->prepage, U('Doc/Doc/user','', false), 8);

        $this->assign('pages', $links['pages']);
        //关键词输出

        $keywords[] = $cache['Keyword'];
        if($subject){
            $this->keywords[] = $subjects[$subject];
        }
        unset($links['pages']);
        $this->assign('keywords', implode(',', $this->keywords));
        $this->assign('description', $this->description);
        $this->assign('pageLinks', $links);
        $this->assign('subjects', $subjects);
        if($this->isWapClient){
            $this->display('DocWap/showContent');
            exit();
        }
        $this->display('Doc/user');
    }

    /**
     * 用户资源详情页
     * @author demo 
     */
    public function userContent(){
        $id = (int)$_GET['id'];

        //判断用户数据是否合适
        if(0 == $id){
            header('location:'.U('Doc/Doc/User'));
        }
        $data = $this->getModel('UserDynamic')->findData(
            'Classification', 'AssociateID='.$id
        );
        if(empty($data)){
            header('location:'.U('Doc/Doc/User'));
        }

        //获取用户资源数据字段
        $docDown = $this->getModel('DocDown');
        $result = $docDown->findData(
            "*",
            'DownID='.$id
        );
        $title = $result['DocName'];
        $subjects = $this->getSubjectes();
        $title = $title.'-'.$subjects[$result['SubjectID']];
        $this->keywords[] = $subjects[$result['SubjectID']];

        $classify = $data['Classification'];
        $dynamicType = array(
            'doc' => '试卷文档',
            'work' => '作业文档',
            'case' => '导学案文档',
        );

        $doc['docname'] = $result['DocName'];
        $doc['subjectid'] = $result['SubjectID'];
        $doc['typename'] = $dynamicType[$classify];
        $doc['username'] = formatString('hiddenUserName',$result['UserName']);
        $doc['orginalUserName'] = $result['UserName'];
        $doc['loadtime'] = date('Y-m-d', $result['LoadTime']);
        $doc['did'] = $result['DownID'];
        $types = SS('typesSubject');
        $types = $types[$doc['subjectid']];
        $typesMap = array();
        foreach($types as $type){
            $typesMap[$type['TypesID']] = $type;
        }

        //根据cookie获取试卷结构
        $cookieArray = R('Common/DocLayer/formatPaperCookie',array($result['CookieStr']));
        $testIDArray = R('Common/DocLayer/explodeCookieStr',array($result['CookieStr']));

        //获取试题内容
        $testRealQuery=$this->getModel('TestRealQuery');

        $field = array('testid','testnum','typesid','judge','ifchoose','testold');
        $where = array('TestID'=>$testIDArray);
        $order = array();
        $page = array('perpage'=>100);
        $data=$testRealQuery->getIndexTest($field,$where,$order,$page,0,2);

        if($data[0]===false){
            $content='';
            $doc['testCount'] = 0;
        }else{
            $content=$cookieArray['parthead'];

            $num=1;
            foreach($content as $i=>$iContent){
                foreach($iContent['questypehead'] as $j=>$jContent){
                    foreach($jContent[5] as $k=>$kContent){
                        $string = R('Common/TestLayer/changeTagToNum',array($data[$kContent[0]]['testold'], $num));
                        $link = C('WLN_DOWNLOAD_RANDOM_CONTENT');
                        $link[1] = '/Public/index/images/copyright-img.png';
                        $tags = array('<span %s>%s</span>', '<span %s><img src="%s"  width="48" height="66" alt="题库"/></span>');
                        //$string = \Doc\Model\RandomIdentificationModel::addRandomContent($string, $link, $tags);
                        $string = getStaticFunction('RandomIdentification','addRandomContent',$string, $link, $tags);
                        $data[$kContent[0]]['testold'] = $string;

                        $num+=$data[$kContent[0]]['testnum']==0 ? 1 : $data[$kContent[0]]['testnum'];
                    }
                }
            }
            $doc['testCount'] = $num-1;
        }

        $output=array();
        $buffer = SS('typesSubject');
        if($buffer){
            foreach($buffer as $i=>$iBuffer){
                foreach($iBuffer as $j=>$jBuffer){
                    $output[$i][$j]['TypesID']=$jBuffer['TypesID'];
                    $output[$i][$j]['TypesName']=$jBuffer['TypesName'];
                    $output[$i][$j]['SubjectID']=$jBuffer['SubjectID'];
                    $output[$i][$j]['TypesScore']=$jBuffer['TypesScore'];
                    $output[$i][$j]['DScore']=$jBuffer['DScore'];
                    $output[$i][$j]['Volume']=$jBuffer['Volume'];
                    $output[$i][$j]['IfDo']=$jBuffer['IfDo'];
                    $output[$i][$j]['Num']=$jBuffer['Num'];
                }
            }
        }

        $types = json_encode($output, JSON_UNESCAPED_UNICODE);
        $this->assign('content', $content);
        $this->assign('doc', $doc);
        $this->assign('title', $title);
        $this->assign('types', $types);
        $this->assign('keywords', implode(',', $this->keywords));
        $this->assign('description', $this->description);
        $this->assign('data', $data);
        if($this->isWapClient){
            $this->display('DocWap/userContent');
            exit;
        }
        $this->display('Doc/userContent');
    }

    /**
     * 记录分享数据
     * @author demo 
     */
    public function addRecord(){
        $did = (int)$_POST['did'];
        $docDown = $this->getModel('DocDown');

        $ifLogin=R('Common/PowerLayer/indexCheckLogin',array(1,2));
        if(is_numeric($ifLogin)){
            $this->setBack('login');
        }

        $currentUserName = $this->getCookieUserID('Home');
        if(empty($currentUserName)){
            $currentUserName = $this->getCookieUserID('Aat');
        }
        if($_POST['keys']){
            $keys = $_POST['keys'];
            $result = $docDown->findData(
                "DocPath,DocName",
                'DownID='.$did
            );
            if(md5($did.$currentUserName.$result['DocName']) == $keys){
                $url=C('WLN_DOC_HOST').R('Common/UploadLayer/getDocServerUrl',array($result['DocPath'],'down','mht',$result['DocName']));
                header('location:'.$url);
                exit;
            }
        }
        $username = $_POST['username'];
        if(0 == $did || empty($username)){
            header('location:'.U('Doc/Doc/User'));
        }
        $user = $this->getModel('User')->getInfoByName(
            $username
        );
        if(empty($user)){
            $this->setBack('创建该资源的用户不存在！');
        }
        if(empty($currentUserName)){
            $this->setBack('login');
        }
        $data['AutherID'] = $user[0]['UserID'];
        $cookieUserID = $this->getCookieUserID('Home');
        $result = $this->getModel('DocShare')->save(
            $did, $currentUserName, $data,$cookieUserID
        );
        if($result === false)
            $this->setBack('添加记录失败！');
        $result = $docDown->findData(
            "DocPath,DocName",
            'DownID='.$did
        );
        $keys = md5($did.$currentUserName.$result['DocName']);
        $this->setBack('success|'.$keys);
    }


    /**
     * ajax获取文档
     */
    public function getDocList(){
        $subjectID=$_REQUEST['sid'];
        $typeID=$_REQUEST['tid'];
        $areaID=$_REQUEST['area'];
        $order=$_REQUEST['o'];
        $page=$_REQUEST['p'];
        $perPage=$_REQUEST['perpage'];
        $gradeID=$_REQUEST['grade'];
        $year = $_REQUEST['year'];
        if(empty($perPage)) $perPage=20;
        // if(!$subjectID){
        //     $this->setBack('chooseSubject');
        // }
        $where=array();
        $where['SubjectID']=$subjectID;
        $where['ShowWhere']=array(0,1);
        $docType=SS('docType');
        if($typeID && $docType[$typeID]['IfHidden']==1) {
            $this->setBack('');//抱歉！暂时没有符合条件的试卷，请尝试更换查询条件。
        }else if($typeID){
            $where['TypeID'] = $typeID;
        }else{
            $typeID=array();
            foreach($docType as $i=>$iDocType){
                if($iDocType['IfHidden']!=1){
                    $typeID[]=$i;
                }
            }
            $where['TypeID']=implode(',',$typeID);
        }
        if($areaID){
            $where['AreaID']=$where['AreaID']=$areaID.',0';
        }
        if($gradeID){
            $where['GradeID']=$gradeID;
        }
        if($year){
            $where['DocYear'] = $year;
        }
        $orderList='';
        if($order){
            switch($order){
                case 'ydown':
                $orderList='docyear DESC, introfirsttime DESC';
                break;
                case 'yup':
                $orderList='docyear ASC, introfirsttime DESC';
                break;
                case 'tdown':
                $orderList='introfirsttime DESC, @id DESC';
                break;
                case 'tup':
                $orderList='introfirsttime ASC, @id DESC';
                break;
                case 'rdown': //IfRencon倒序、第一次入库时间倒序
                $orderList='ifrecom DESC, introfirsttime DESC';                
                break;
            }
        }
        //$order=array('docyear DESC, introtime DESC');
        if($orderList) $order=array($orderList);
        else $order=array('docyear DESC,introfirsttime Desc');
        $field=array('docid','docname','subjectname','typename','docyear','areaname','loadtime','introfirsttime','introtime');
        $page=array('page'=>$page,'perpage'=>$perPage);
        $doc=$this->getModel('Doc');
        $buffer=$doc->getDocIndex($field,$where,$order,$page);
        if(IS_AJAX){
            $this->setBack($buffer);
        }else{
            return $buffer;
        }
    }

    /**
     * ajax获取试卷属性
     * @param int $style
     * @param int $ifHidden
     * @return array
     */
    protected function getDocAttr($style=0,$ifHidden=0) {
        $output = array ();
        if($style==1) $buffer=SS('docChapterOrder');
        else $buffer = SS('docType');
        if($buffer){
            $j=0;
            foreach($buffer as $i=>$iBuffer){
                if($iBuffer['IfHidden']==1 && $ifHidden==1){
                    continue;
                }
                $output[$j]['TypeID']=$i;
                $output[$j]['TypeName']=$iBuffer['TypeName'];
                $output[$j]['GradeList']=$iBuffer['GradeList'];
                $j++;
            }
        }
        return $output;
    }

    /**
     * ajax获取省份
     * @return array
     */
    protected function getArea() {
        $output = array ();
        //$subjectID = $_GET['id'];
        $buffer = SS('areaChildList');
        if($buffer[0]){
            foreach($buffer[0] as $i=>$iBuffer){
                $output[$i]['AreaID']=$iBuffer['AreaID'];
                $output[$i]['AreaName']=$iBuffer['AreaName'];
            }
        }
        return $output;
    }

    /**
     * 根据文档id加载试卷中的试题
     * @author demo
     * @date 2015-6-6
     */
    public function loadTestByDocId(){
        $id = $_POST['docid'];
        if(empty($id)){
            $this->showError('非法的请求！');
        }
        $data = $this->getTestByDocId($id, array('testid', 'typesid', 'testnum', 'optionnum'));

        $this->setBack($data);
    }

    /**
     * 根据文档id返回试题
     * @param int $id 文档id
     * @param array $field 获取的字段
     * @return array
     * @author demo
     * @date 2015-6-10
     */
    private function getTestByDocId($id, $field){
        return getStaticFunction('TestQuery','getInstance')->getPubList(array(
            'field' => $field,
            'where' => array('DocID'=>$id),
            'page' => array('perpage'=>150),
            'order' => array('testid asc')
        ));
    }
    /**
     * 返回学科数组
     * @author demo 
     */
    private function getSubjectes(){
        $subjects = SS('subject');
        foreach($subjects as $key=>$value){
            if($value['PID'] != 0 && $value['ParentName'] == '高中')
                $subjects[$key] = $value['ParentName'].$value['SubjectName'];
            else
                unset($subjects[$key]);
        }
        return $subjects;
    }


    /**
     * ajax获取用户动态数据 （用户资源）
     * @author demo
     */
    public function ajaxGetUserResource(){
        $userDynamic = $this->getModel('UserDynamic');
        $params = array(
            'SubjectID'=>$_POST['sid'],
            'prepage' => $_POST['perpage']
        );
        $result = $userDynamic->getHomePageList($params);
        $this->setBack(
            array(
                'type'=>\User\Model\UserDynamicModel::$dynamicType,
                'rs' => $result
            )
        );
    }
}
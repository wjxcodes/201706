<?php
/**
 * 提取文档注释
 * @date 2016年10月11日
 */
error_reporting(E_ERROR & ~E_NOTICE & ~E_STRICT);
header("Content-type: text/html; charset=utf-8");

Class ReadNotes
{
    private $del = array(); //需要删除的数据
    private $ifPublic = 0; //是否只显示public数据
    private $ifClass = 0; //是否只显示第一个class数据
    private $file = ''; //文件地址

    /**
     * 读取目录中所有php文件
     * @return array 返回目录中所有php文件
     * @author 
     */
    public function getFile($dir)
    {
        if (!is_dir($dir)) {
            echo '<script>alert("目录不存在");</script>';
            exit;
        }
        //使用系统预定义类进行目录遍历
        $ite = new \RecursiveDirectoryIterator($dir);

        foreach (new \RecursiveIteratorIterator($ite) as $filename => $cur) {
            if (is_dir($filename)) continue;
            //获取文件名后缀并进行判断
            $basename = pathinfo($filename, PATHINFO_EXTENSION);
            if ($basename == 'php') {
                $filename = ltrim(strrchr($filename, "/"), "/");
                $res['file'][] = $filename;
            }
        }
        $res['dir'] = $dir;
        return $res;
    }

    /**
     * 读取文件中文档头，类，方法注释
     * @return array 返回所有文档头注释
     * @author 
     */
    public function headNotes($file)
    {
        if ($file) $this->file = $file;

        $data = array();

        $patren = file_get_contents($file);
        preg_match("/\/[*]{2}(.*?)namespace/s", $patren, $subject);
        if (empty($subject[1])) {
            $arr = preg_split("/class[\s0-9a-zA-Z]*\{/", $patren);
            $subject[1] = $arr[0];
        }
        if ($subject && $subject[1]) preg_match_all("/\*\s([^\*]*)/", $subject[1], $data);

        $fileNotes = array();
        if ($data && $data[1]) $fileNotes[] = $data[1];
        return $fileNotes;
    }

    /**
     * 读取类成员属性注释
     * @return array 返回所有文件类成员属性的注释
     * @author 
     */
    public function readClassNote($file)
    {
        if ($file) $this->file = $file;
        $res = $this->checkParams($file);
        if ($res) {
            $newpatren = file_get_contents($file);
            if (strstr($newpatren, '@public') !== false) {
                $this->ifPublic = 1;
            }
            if (strstr($newpatren, '@class') !== false) {
                $this->ifClass = 1;
            }
            preg_match_all("/(public|private|protected)\s([$].*?)\/\/([^\r\n]*)/s", $newpatren, $subject);
            foreach ($subject[2] as $v) {
                $v = str_replace(";", "", $v);
                $arr[] = $v;
            }
            $arr = isset($arr) ? $arr : '';
            $subject[2] = $arr;
        }

        return $subject;
    }

    /**
     * 读取方法注释参数param
     * @return array 返回所有param
     * @author 
     */
    public function readPnames($file)
    {
        if ($file) $this->file = $file;
        $data = $this->checkParams($file);
        foreach ($data as $value) {
            preg_match_all("/\*\s@param\s+(\w+)\s+([$]\w+|\w+|)\s+(.*?)[^@]/U", $value, $param);
            $param = array_filter($param);
            if (empty($param)) {
                unset($param);
            }
            $params[] = isset($param) ? $param : '';
        }
        return $params;
    }

    /**
     * 读取方法注释返回参数return
     * @return array 返回方法的return参数
     * @author 
     */
    public function readReturn($file)
    {
        if ($file) $this->file = $file;
        $data = $this->checkParams($file);
        foreach ($data as $value) {
            preg_match_all("/\*\s@return\s+(.*?)\s+([^@]*)/", $value, $subject);
            $subject = array_filter($subject);
            if (isset($subject[2])) {
                foreach ($subject[2] as $v) {
                    $v = str_replace("*", "", $v);
                    $subject[2][0] = $v;
                }
                $params[] = $subject;
            } else {
                $params[] = '';
            }
        }
        return $params;
    }

    /**
     * 读取方法注释作者参数
     * @return array 返回方法的author参数
     * @author 
     */
    public function readAuthor($file)
    {
        if ($file) $this->file = $file;
        $data = $this->checkParams($file);
        foreach ($data as $value) {
            preg_match_all("/\*\s+@author\s+([^\*\/]*)/", $value, $subject);
            $subject = array_filter($subject);
            if (isset($subject[1])) {
                $params[] = $subject[1];
            } else {
                $params[] = '';
            }
        }
        return $params;
    }

    /**
     * 读取方法注释描述参数
     * @return array 返回方法的描述参数
     * @author 
     */
    public function readDescript($file)
    {
        if ($file) $this->file = $file;
        $data = $this->checkParams($file);
        foreach ($data as $value) {
            preg_match("/\*\s([^@]*)/", $value, $subject);
            $subject = array_filter($subject);
            if (empty($subject)) {
                unset($subject);
            }
            if (isset($subject[1])) {
                $subject = str_replace("*", "", $subject[1]);
                $params[] = $subject;
            }
        }
        return $params;
    }

    /**
     * 读取方法名称
     * @return array 返回所有方法名称
     * @author 
     */
    public function readFname($file)
    {
        if ($file) $this->file = $file;
        $res = $this->checkParams($file);
        if ($res) {
            $patren = file_get_contents($file);
            preg_match_all("/(public|private|protected)\s+function\s+(\w+)\((.*?)\)/", $patren, $subject);
            return $subject[2];
        }
    }

    /**
     * 读取方法参数
     * @return array 返回所有方法参数
     * @author 
     */
    public function readFparams($file)
    {
        if ($file) $this->file = $file;
        $res = $this->checkParams($file);
        if ($res) {
            $patren = file_get_contents($file);
            preg_match_all("/(public|private|protected)\s+function\s+(\w+)\([^\)]*?\)\s*\{/s", $patren, $subject);

            foreach ($subject[0] as $i => $v) {
                if (strstr($v, 'public') === false) $this->del[] = $i;
                $v = str_replace("{", "", $v);
                $v = str_replace("private", "私有", $v);
                $v = str_replace("public", "公共", $v);
                $v = str_replace(" function ", "方法 ", $v);
                $arr[] = $v;
            }
            $subject[0] = $arr;
            return $subject[0];
        }
    }

    /**
     * 检测是否是类文件
     * @return bool 不是返回false是返回匹配到的中间内容
     * @author 
     */
    public function checkParams($file)
    {
        if ($file) $this->file = $file;
        $patren = file_get_contents($file);
        $res = preg_match_all("/class\s[\w\s]+(\{[\w\W]+\})/", $patren, $subject);
        if ($res) {
            preg_match_all("/\/[*]{2}(.*?)\*\//s", $subject[1][0], $data);
            return $data[1];
        } else {
            return false;
        }
    }

    /**
     * 读取function 的代码体页面出现@show 1 则显示代码题
     * @return array 返回显示的方法代码块
     * @author 
     */
    public function showContent($file)
    {
        if ($file) $this->file = $file;
        $patren = file_get_contents($file);
        $res = preg_match_all("/class\s[\w\s]+(\{[\w\W]+\})/", $patren, $subject);
        if ($res) {
            preg_match_all("/\/[*]{2}(.*?)\*\//s", $subject[1][0], $data);
            if (empty($data)) unset($data);
            foreach ($data[1] as $k => $v) {
                $str = strpos($v, "@show");
                if ($str) {
                    $arr[] = $k + 1;
                }
            }
            $ret = preg_split("/\/[*]{2}(.*?)\*\//s", $subject[1][0]);
            if (isset($arr)) {
                foreach ($arr as $v) {
                    $conts[$v - 1] = $ret[$v];
                }
                return $conts;
            } else {
                return false;
            }
        }
    }

    /**
     * 删除非公共方法数据
     * @return array
     * @author mabo
     */
    public function deleteNoPublic($array)
    {
        if ($this->ifPublic == 1) {
            $del = $this->del;
            $output = array();
            if ($array) {
                foreach ($array as $i => $iArray) {
                    if (!in_array($i, $del)) {
                        $output[] = $iArray;
                    }
                }
            }
            $array = $output;
        }

        if ($this->ifClass == 1) {
            $count = $this->getOneClassPublic($this->file);
            if ($count > 0) $array = array_slice($array, 0, $count);
        }

        return $array;
    }


    /**
     * 删除非公共变量
     * @param array $array
     * @return array
     * @author mabo
     */
    public function deleteNoPublicParam($array)
    {
        if ($this->ifPublic == 1) {
            $output = array();
            foreach ($array as $i => $iArray) {
                if (strstr($iArray, 'private')) {
                    continue;
                }
                $output[] = $iArray;
            }
            return $output;
        }
        return $array;
    }

    /**
     * 获取第一个class里面有多少public
     * @return array
     * @author mabo
     */
    public function getOneClassPublic($file)
    {
        $patren = file_get_contents($file);
        $res = preg_split("/class[\s0-9a-zA-Z]+\{/", $patren);
        if ($res) {
            preg_match_all("/(public)\s+function\s+(\w+)\((.*?)\)/", $res[1], $data);
            return count($data[2]);
        }
        return 0;
    }
}

$note = new ReadNotes();

//获取文件夹
$dir = isset($_GET['dir']) ? $_GET['dir'] : '../tk/AatApi/Controller';
// $dir = '../edu/AatApi/Controller';
// var_dump($dir);die;
//读取文件夹下的php文件
$apifile = $note->getFile('../tk/AatApi/Controller');
// $recDir = '../edu/Common/Api';
// $recfile =$note->getFile('');
// var_dump($apifile);die;
// $apifile = array('file' => array(
//     0 => 'AnalysisApi.class.php',
//     1 => 'AnalysisStudentApi.class.php',
//     2 => 'AnalysisTeacherApi.class.php',
//     3 => 'AnalysisHeadmasterApi.class.php',
//     4 => 'AnalysisRectorApi.class.php',
//     5 => 'AnalysisRegionApi.class.php',
//     6 => 'ReviewApi.class.php',
//     7 => 'ScanApi.class.php',
//     8 => '../../../../file/uploadFileApi.php'
// ),
//     'dir' => '../mark/Common/Api');

$fileName = isset($_GET['file']) ? $_GET['file'] : $apifile['file'][0];
// if (!in_array($fileName, $apifile['file'])) {
//     exit('文件不被允许！');
// }
$file = $dir . '/' . $fileName;
if (!file_exists($file)) {
    echo '<script>alert("文件不存在");</script>';
    exit;
}

//文件头注释
$headers = $note->headNotes($file)[0];
//类文件每个方法
$fparams = $note->readFparams($file);

if ($fparams) {
    //类变量注释
    $classNote = $note->readClassNote($file);
    //方法描述注释
    $descript = $note->readDescript($file);
    //方法参数param注释
    $pnames = $note->readPnames($file);
    //方法返回值注释
    $ret = $note->readReturn($file);
    //方法作者注释
    $author = $note->readAuthor($file);
    //函数名
    $fnames = $note->readFname($file);
    //显示的函数
    $conts = $note->showContent($file);
}

$fparams = $note->deleteNoPublic($fparams);
$descript = $note->deleteNoPublic($descript);
$pnames = $note->deleteNoPublic($pnames);
$ret = $note->deleteNoPublic($ret);
$author = $note->deleteNoPublic($author);
$fnames = $note->deleteNoPublic($fnames);
$conts = $note->deleteNoPublic($conts);

?>
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>接口文档</title>
    <link rel="stylesheet" href="api-doc/css/bootstrap.min.css">
    <link rel="stylesheet" href="api-doc/css/doc.css">
</head>
<body>
<div class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar"
                    aria-controls="bs-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="" class="navbar-brand">接口文档</a>
        </div>
        <div id="bs-navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="">教师端</a>
                </li>
                <li>
                    <a href="">学生端</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="http://localhost:7780/" target="_blank">首页</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="jumbotron">
    <div class="container">
        <h1>接口文档</h1>
        <p>“文档中心包含API接口文档”</p>
    </div>
</div>
<!--主体内容-->
<div class="container">
    <div class="row">
        <div class="col-md-2">
            <div class="docs-left hidden-print" id="sideMenu">
                <h3 class="menu-title">文档目录</h3>
                <ul class="menu-1">
                    <li class="">
                        <a class="J_ExpTrigger" href="javascript:void(0)" title="开发文档">API帮助文档</a>
                        <ul class="menu-2">
                            <li class="">
                                <a class="J_ExpTrigger" href="javascript:void(0)">手机端APP接口</a>
                                <ul class="menu-3">
                                    <?php
                                    foreach ($apifile['file'] as $v) {
                                        ?>
                                        <li>
                                            <a href="?file=<?php echo $v ?>&dir=<?php echo $apifile['dir'] ?>"><?php echo $v ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a class="J_ExpTrigger" href="javascript:void(0)">API调用场景</a>
                        <ul class="menu-2">
                            <!-- <li class="">
                                <a class="J_ExpTrigger" href="javascript:void(0)">Api</a>
                                <ul class="menu-3">
                                    <?php
                                    foreach ($apifile['file'] as $v) {
                                        ?>
                                        <li>
                                            <a href=<?php $_SERVER['HTTP_HOST'] ?>"logicdoc.php?file=<?php echo $v ?>&dir=<?php echo $apifile['dir'] ?>"><?php echo $v ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li> -->
                            <li class="">
                                <a class="J_ExpTrigger" href="javascript:void(0)">电子凭证</a>
                                <ul class="menu-3">
                                    <li><a href="">电子凭证签名加密和解密</a></li>
                                    <li><a href="">电子凭证发码sign验证</a></li>
                                </ul>
                            </li>
                            <li><a href="">API使用说明</a></li>
                        </ul>
                    </li>
                    <li><a href="">规则中心</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <!--文件说明-->
            <div class="bs-docs-section" id="sectionContent">
                <h3><?php echo $fileName ?> <span class="update-time">更新时间：2016/02/06</span></h3>
                <p class=""></p>
                <?php
                foreach ($headers as $v) {
                    ?>
                    <p class=""><?php echo $v ?></p>
                    <?php
                }
                if (isset($classNote[1]) && !empty($classNote[1])) {
                    ?>
                    <div class="docs-section-li section-item" id="section-item1">
                        <h4>
                            成员属性
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>修饰符</th>
                                    <th>变量</th>
                                    <th>描述</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $len = count($classNote[1]);
                                for ($i = 0; $i < $len; $i++) {

                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <code><?php echo $classNote[1][$i] ?></code>
                                        </th>
                                        <td>
                                            <?php echo $classNote[2][$i] ?>
                                        </td>
                                        <td>
                                            <?php echo $classNote[3][$i] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <!-- 方法介绍 -->
                <?php
                $len = count($fparams);
                for ($i = 0; $i < $len; $i++) {
                    if (strstr($descript[$i], '未完成')) continue; //未完成的代码不允许显示
                    ?>
                    <div class="docs-section-li section-item" id="section-item<?php echo $i + 2 ?>">
                        <h4>
                            <?php echo $fparams[$i] ?>
                        </h4>

                        <p>
                            方法描述：<?php echo $descript[$i] ?>
                        </p>
                        <?php
                        $aut = isset($author[$i][0]) ? $author[$i][0] : '';
                        if ($aut) echo '@author:' . $aut;

                        ?>
                        <h5 id="session">参数描述</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>类型</th>
                                    <th>参数名称</th>
                                    <th>描述</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (isset($pnames[$i][1])) {
                                    foreach ($pnames[$i][1] as $k => $v) {
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <code><?php echo $v ?></code>
                                            </th>
                                            <td>
                                                <?php echo $pnames[$i][2][$k] ?>
                                            </td>
                                            <td>
                                                <?php echo $pnames[$i][3][$k] ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>

                        </div>
                        <?php
                        if (isset($ret[$i][1][0])) {
                            ?>
                            <h5 id="session">返回值</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th width="15%">类型</th>
                                        <th>描述</th>
                                        <!-- <th>描述</th> -->
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <th scope="row">
                                            <code><?php echo $ret[$i][1][0] ?></code>
                                        </th>
                                        <td>
                                            <?php echo '<pre style="border:0px;background-color:white;padding:0px">' . $ret[$i][2][0] . '</pre>'; ?>
                                        </td>

                                    </tr>


                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php if (isset($conts[$i])) {

                        ?>
                        <div class="wln-code">
                            <pre>
                         <?php echo $conts[$i] ?>
                            </pre>
                        </div>

                        <?php
                    } else {

                    }
                }
                ?>

            </div>

        </div>
        <!--文件说明-END-->
        <!--侧边导航-->
        <div class="col-md-2">
            <div class="bs-docs-sidebar hidden-print hidden-xs hidden-sm affix-top">
                <ul class="nav bs-docs-sidenav" id="maoNav">

                    <li class="active">
                        <a href="#section-item1">APi调用方法</a>
                    </li>
                    <?php
                    if (isset($fnames)) {
                        $len = count($fnames);
                        for ($i = 0; $i < $len; $i++) {
                            ?>
                            <li class="">
                                <a href="#section-item<?php echo $i + 2 ?>"><?php echo $fnames[$i] . '(' . $descript[$i] . ')' ?></a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
                <a class="back-to-top" href="#top">返回顶部</a>
            </div>

        </div>
        <!--侧边导航-END-->
    </div>
</div>
<!--主体内容-END-->


</body>
<script src="http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="api-doc/js/bootstrap.min.js"></script>
<script src="api-doc/js/docs.js"></script>
<script>
    $(function () {
        $.WLN_docs.init();
    });

</script>
</html>
<?php
/**
 * 生成数据库文档
 */
header ( "Content-type: text/html; charset=utf-8" );

// 配置数据库
$dbserver = "192.168.15.33";
$dbusername = "tk";
$dbpassword = "tiku";
$database = "tk";


$file = 'sql_cache.php';//缓存文件
//更新缓存操作
$action = isset($_GET['action']) ? $_GET['action'] : '';
if($action == 'update'){
    if (file_exists($file)) {
        unlink($file);
        echo '<script>alert("更新成功");location="dbdoc.php"</script>';
    }
}

// 其他配置
$title = '数据库文档';

if (file_exists($file)) {
    function mb_unserialize($serial_str) {
        $serial_str = preg_replace_callback('!s:(\d+):"(.*?)";!s', create_function('$math',"return 's:'.strlen(\$math[2]).':\"'.\$math[2].'\";';"), $serial_str);
        return unserialize($serial_str);
    }
    $serial_str = file_get_contents($file);
    $tables = mb_unserialize($serial_str);
} else {
    $mysql_conn = @mysql_connect ( "$dbserver", "$dbusername", "$dbpassword" ) or die ( "Mysql connect is error." );
    mysql_select_db ( $database, $mysql_conn );
    mysql_query ( 'SET NAMES utf8', $mysql_conn );
    $table_result = mysql_query ( 'show tables', $mysql_conn );
    // 取得所有的表名
    while ( $row = mysql_fetch_array ( $table_result ) ) {
        $tables [] ['TABLE_NAME'] = $row [0];
    }

    // 循环取得所有表的备注及表中列消息
    foreach ( $tables as $k => $v ) {
        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.TABLES ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$database}'";

        $table_result = mysql_query ( $sql, $mysql_conn );
        while ( $t = mysql_fetch_array ( $table_result ) ) {
            $tables [$k] ['TABLE_COMMENT'] = $t ['TABLE_COMMENT'];
            $tables [$k] ['ENGINE'] = $t ['ENGINE']; //引擎
            $tables [$k] ['TABLE_COLLATION'] = $t ['TABLE_COLLATION'];//字符集
        }

        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
        $fields = array ();
        $field_result = mysql_query ( $sql, $mysql_conn );
        while ( $t = mysql_fetch_array ( $field_result ) ) {
            switch($t['COLUMN_KEY']){
                case 'PRI':
                    $t['COLUMN_KEY'] = '主键';
                    break;
                case 'UNI':
                    $t['COLUMN_KEY'] = '唯一索引';
                    break;
                case 'IND':
                    $t['COLUMN_KEY'] = '索引---';
                    break;
                case 'MUL':
                    $t['COLUMN_KEY'] = '索引';
                    break;
                case 'FUL':
                    $t['COLUMN_KEY'] = '全文索引';
                    break;
                default :
                    $t['COLUMN_KEY'] = '';
                    break;
            }
            $fields [] = $t;
        }
        $fields[0]['ENGINE'] = $tables [$k] ['ENGINE'];
        $fields[0]['TABLE_COLLATION'] = $tables [$k] ['TABLE_COLLATION'];
        $tables [$k] ['COLUMN'] = $fields;

    }
        //写入缓存
        $output = serialize($tables);

        $fp = fopen($file,"w");

        fputs($fp, $output);

        fclose($fp);
    mysql_close ( $mysql_conn );
}
/**===============================输出页面=============================================*/
$html = '';
$table = '';
// 循环所有表
foreach ( $tables as $k => $v ) {
    $k = $k+1;
    $html .= '<ul><a href="#c'.$k.'"><li class="item">' . $v ['TABLE_NAME'] . '  ' . $v ['TABLE_COMMENT'] . '</li><li  class="dasheder"></li><li class="num">'.$k.'</li></a></ul>';
    $table .= '<table  name="c'.$k.'" id="c'.$k.'" border="1" cellspacing="0" cellpadding="0" align="center" class="table2">';
    $table .= '<caption>' . $v ['TABLE_NAME'] . '  ' . $v ['TABLE_COMMENT'] . '</caption>';
    $table .= '<tbody><tr><th>字段名</th><th>数据类型</th><th>键值</th><th>字符集</th><th>默认值</th><th>字段编码</th><th>备注</th><th>引擎类型</th></tr>';

    foreach ( $v ['COLUMN'] as $f ) {
        $table .= '<tr><td class="c1">' . $f ['COLUMN_NAME'] . '</td>';
        $table .= '<td class="c2">' . $f ['COLUMN_TYPE'] . '</td>';
        $table .= '<td class="c3">&nbsp;' . $f ['COLUMN_KEY'] .($f ['EXTRA'] == 'auto_increment' ? '自增' : '&nbsp;'). '</td>';
        $table .= '<td class="c9">&nbsp;' . (isset($f ['TABLE_COLLATION']) ? $f ['TABLE_COLLATION'] : '&nbsp;') . '</td>';
        $table .= '<td class="c4">&nbsp;' . $f ['COLUMN_DEFAULT'] . '</td>';
        $table .= '<td class="c6">&nbsp;' . $f ['COLLATION_NAME'] . '</td>';
        $table .= '<td class="c7">&nbsp;' . $f ['COLUMN_COMMENT'] . '</td>';
        $table .= '<td class="c8">&nbsp;' . (isset($f ['ENGINE']) ? $f ['ENGINE'] : '&nbsp;') . '</td>';
        $table .= '</tr>';
    }
    $table .= '</tbody></table><div class="top"><a href="#top">TOP</a></div>';
}

// 输出
echo '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>' . $title . '</title>
<style>
*{
    margin:0;
    padding:0;
    font-size:14px;
}
table,.top{
    width:1000px;
    margin: 0 auto;
}
.top{text-align:right;}
body{
    background-color: #f1f1f1;
}
body,td,th {font-family:"宋体"; font-size:12px;top:20px;}
table{border-collapse:collapse;border:1px solid #CCC;background:#6089D4;position: relative;margin-top:30px;}
.list-box{
    background-color: #fff;
    margin:0 auto;
    width:943px;
    border:1px solid #ddd;
    padding:60px 30px;
}
.list-box ul{
    font-size:14px;
    overflow:hidden;
}
ul li{
    list-style-type:none;
}
a{ text-decoration:none; color:#333;}
a:hover{
    color:blue;
}
ul li.item{
    float: left;
    background-color: #fff;
    padding:0 12px 0 0;
}
ul li.num{
    float: right;
    background-color: #fff;
    margin-top:-7px;
    padding:0 0 0 12px;
}
ul li.dasheder{
    border-bottom:1px dashed #000;
    height:12px;
}
.update{
    margin-left: 770px;
    font-size:17px;
    color:blue;
}
body table{position: relative;}
body:last-child{margin-bottom:6.2em;}
body {position: relative;margin-top:35px;}
table caption{text-align:left; background-color:#fff; line-height:2em; font-size:14px; font-weight:bold;font-family:"Times New Roman"; }
table th{text-align:left; font-weight:bold;height:20px; line-height:20px; font-size:16px; border:3px solid #fff; color:#ffffff; padding:5px;}
table td{height:25px; font-size:14px; border:3px solid #fff; background-color:#f0f0f0; padding:5px;font-family:"Times New Roman";}
.c1{ width: 150px;}
.c2{ width: 130px;}
.c3{ width: 110px;}
.c4{ width: 80px;}
.c5{ width: 80px;}
.c6{ width: 100px;}
.c7{ width: 220px;}
.c8{ width: 80px;}
.c9{ width: 80px;}
</style>
</head>
<body>';

echo '<div class="list-box" name="top">';
echo '<a href="dbdoc.php?action=update" class="update">更新缓存</a>';
echo '<h1 style="text-align:center; font-size:20px;margin-bottom:2.2em;">' . $title . '</h1>';
echo '<h2 style="text-align:center;">目&nbsp;&nbsp;录</h2>';

echo $html;
echo '</div>';
echo $table;
echo '</body></html>';
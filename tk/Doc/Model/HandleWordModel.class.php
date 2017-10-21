<?php
/**
 * @author demo
 * @date 2015年7月24日
 */
/**
 * 文档下载模型类，用于处理文档下载相关操作
 */
namespace Doc\Model;
class HandleWordModel extends BaseModel{
    protected $_regAD='/([A-D](<i[^m>]*>)?(\.|．))/s'; //正则规则提取选项
    protected $_regADAll='/([A-D](<i[^m>]*>)?(\.|．).*)/s'; //正则规则提取选项内容
    protected $symbolForNum = '、';  //试题序号后的符号
    public $docStyle='<style>
<!--
 /* Font Definitions */
 @font-face
    {font-family:宋体;
    panose-1:2 1 6 0 3 1 1 1 1 1;
    mso-font-alt:SimSun;
    mso-font-charset:134;
    mso-generic-font-family:auto;
    mso-font-pitch:variable;
    mso-font-signature:3 135135232 16 0 262145 0;}
 /* Style Definitions */
 p{margin:2px 0px;line-height:125%;}
 p.MsoNormal, li.MsoNormal, div.MsoNormal
    {mso-style-unhide:no;
    mso-style-qformat:yes;
    mso-style-parent:"";
    margin:2px 0cm;
    margin-bottom:.0001pt;
    text-align:left;
    text-justify:inter-ideograph;
    mso-pagination:none;
    font-size:10.5pt;
    mso-bidi-font-size:11.0pt;
    font-family:"Times New Roman","serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoHeader, li.MsoHeader, div.MsoHeader
    {mso-style-priority:99;
    mso-style-link:"页眉 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    text-align:center;
    mso-pagination:none;
    tab-stops:center 207.65pt right 415.3pt;
    layout-grid-mode:char;
    border:none;
    mso-border-bottom-alt:solid windowtext .75pt;
    padding:0cm;
    mso-padding-alt:0cm 0cm 1.0pt 0cm;
    font-size:9.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoFooter, li.MsoFooter, div.MsoFooter
    {mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-link:"页脚 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    mso-pagination:none;
    tab-stops:center 207.65pt right 415.3pt;
    layout-grid-mode:char;
    font-size:9.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
    {mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-link:"批注框文本 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    text-align:justify;
    text-justify:inter-ideograph;
    mso-pagination:none;
    font-size:9.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoNoSpacing, li.MsoNoSpacing, div.MsoNoSpacing
    {mso-style-priority:1;
    mso-style-unhide:no;
    mso-style-qformat:yes;
    mso-style-parent:"";
    mso-style-link:"无间隔 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    mso-pagination:widow-orphan;
    font-size:9.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;}
span.Char
    {mso-style-name:"页眉 Char";
    mso-style-priority:99;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:页眉;
    mso-ansi-font-size:9.0pt;
    mso-bidi-font-size:9.0pt;}
span.Char0
    {mso-style-name:"页脚 Char";
    mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:页脚;
    mso-ansi-font-size:9.0pt;
    mso-bidi-font-size:9.0pt;}
span.Char1
    {mso-style-name:"批注框文本 Char";
    mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:批注框文本;
    mso-ansi-font-size:9.0pt;
    mso-bidi-font-size:9.0pt;}
span.Char2
    {mso-style-name:"无间隔 Char";
    mso-style-priority:1;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:无间隔;
    mso-ansi-font-size:11.0pt;
    mso-font-kerning:0pt;}
.MsoChpDefault
    {mso-style-type:export-only;
    mso-default-props:yes;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
    {mso-style-name:普通表格;
    mso-tstyle-rowband-size:0;
    mso-tstyle-colband-size:0;
    mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-parent:"";
    mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
    mso-para-margin:0cm;
    mso-para-margin-bottom:.0001pt;
    mso-pagination:widow-orphan;
    font-size:10.5pt;
    font-family:"Times New Roman","serif";}
 td{font-size:10.5pt;padding:0px;border:0px;}
 table{
     border-collapse:collapse;
     padding:0px;border:0px;margin:0px;
 }
</style>
<![endif]-->'; //word样式
    /*顶部样式 0=>绝密★启用前     {#juemi#}
            1=>2012-2013学年度???学校7月月考卷     {#maintitle#}
            2=试卷副标题 {#subtitle#}
            3=>考试范围：xxx；考试时间：100分钟；命题人：xxx{#titleinfo#}
            4=>考生输入栏 {#titlestudent#}
            5=>试卷分数栏    {#tihao#} {#defen#}
            6=>试卷分数栏    上    {#num#}
            7=>试卷分数栏    下
            8=>注意事项    {#careful#}
            9=>分卷    {#juanbiao#} {#juanzhu#}
            10=>换页
            11=>题型分数栏
            12=>题型标题     {#pingfen#} {#tixing#} {#tizhu#}
     * */
    public $topStyle=array(
        0=>"<p class=MsoNormal style='font-family:黑体'><b>{#juemi#}</b></p>",
        1=>"<p class=MsoNormal style='text-align:center;font-size:15.0pt;mso-fareast-font-family:黑体'><b>{#maintitle#}</b></p>",
        2=>"<p class=MsoNormal style='text-align:center;font-size:18.0pt;font-family:黑体'><b>{#subtitle#}</b></p>",
        3=>"<p class=MsoNormal style='text-align:center'>{#titleinfo#}</p>",
        4=>"<p class=MsoNormal style='text-align:center'>{#titlestudent#}</p>",
        5=>"<div align=center>
<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
 mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
 mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
 mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1'>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
  <td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='text-align:center'>题号</p>
  </td>{#tihao#}
  <td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal style='text-align:center'>总分</p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:1;mso-yfti-lastrow:yes;height:26.0pt'>
  <td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
  text1;border-top:none;mso-border-top-alt:solid black .5pt;mso-border-top-themecolor:
  text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
  0cm 5.4pt 0cm 5.4pt;height:26.0pt'>
  <p class=MsoNormal style='text-align:center'>得分</p>
  </td>
  {#defen#}
  <td width=67 style='width:50.0pt;border-top:none;border-left:none;border-bottom:
  solid black 1.0pt;mso-border-bottom-themecolor:text1;border-right:solid black 1.0pt;
  mso-border-right-themecolor:text1;mso-border-top-alt:solid black .5pt;
  mso-border-top-themecolor:text1;mso-border-left-alt:solid black .5pt;
  mso-border-left-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
  text1;padding:0cm 5.4pt 0cm 5.4pt;height:26.0pt'>
  <p class=MsoNormal style='text-align:center'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
  </td>
 </tr>
</table>
</div>",

        6=>"<td width=67 style='width:50.0pt;border:solid black 1.0pt;mso-border-themecolor:
                      text1;border-left:none;mso-border-left-alt:solid black .5pt;mso-border-left-themecolor:
                      text1;mso-border-alt:solid black .5pt;mso-border-themecolor:text1;padding:
                      0cm 5.4pt 0cm 5.4pt'>
                      <p class=MsoNormal style='text-align:center'>{#num#}</p>
                    </td>",
        7=>"<td width=67 style='width:50.0pt;border-top:none;border-left:none;border-bottom:
                      solid black 1.0pt;mso-border-bottom-themecolor:text1;border-right:solid black 1.0pt;
                      mso-border-right-themecolor:text1;mso-border-top-alt:solid black .5pt;
                      mso-border-top-themecolor:text1;mso-border-left-alt:solid black .5pt;
                      mso-border-left-themecolor:text1;mso-border-alt:solid black .5pt;mso-border-themecolor:
                      text1;padding:0cm 5.4pt 0cm 5.4pt;height:26.0pt'>
                      <p class=MsoNormal style='text-align:center'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
                    </td>",
        8=>"<p class=MsoNormal>注意事项：</p>
<p class=MsoNormal>{#careful#}</p>
<p class=MsoNormal></p>",
        9=>"<p class=MsoNormal style='text-align:center'><b>{#juanbiao#}</b></p>
<p class=MsoNormal>{#juanzhu#}</p>",
        10=>"<br clear=all style='page-break-before:always'>",
        11=>"<td valign=top style='padding:0cm 5.4pt 0cm 5.4pt'>
    <table border=0 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none;mso-border-insideh:none;mso-border-insidev:none'>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;page-break-inside:avoid'>
  <td>
  <table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0 width=129
   style='width:96.9pt;border-collapse:collapse;border:none;mso-border-alt:
   solid windowtext 1.5pt;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
   mso-border-insideh:1.5pt solid windowtext;mso-border-insidev:1.5pt solid windowtext'>
   <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;height:13.7pt'>
    <td width=65 valign=top style='width:48.45pt;border:solid windowtext 1.5pt;
    padding:0cm 5.4pt 0cm 5.4pt;height:13.7pt'>
    <p class=MsoNormal align=center style='text-align:center'><span
    style='font-family:宋体;mso-ascii-font-family:Times New Romance;mso-ascii-theme-font:
    minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Times New Romance;mso-hansi-theme-font:minor-latin'>评卷人</span></p>
    </td>
    <td width=65 valign=top style='width:48.45pt;border:solid windowtext 1.5pt;
    border-left:none;mso-border-left-alt:solid windowtext 1.5pt;padding:0cm 5.4pt 0cm 5.4pt;
    height:13.7pt'>
    <p class=MsoNormal align=center style='text-align:center'><span
    style='font-family:宋体;mso-ascii-font-family:Times New Romance;mso-ascii-theme-font:
    minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Times New Romance;mso-hansi-theme-font:minor-latin'>得分</span></p>
    </td>
   </tr>
   <tr style='mso-yfti-irow:1;mso-yfti-lastrow:yes;height:27.45pt'>
    <td width=65 valign=top style='width:48.45pt;border:solid windowtext 1.5pt;
    border-top:none;mso-border-top-alt:solid windowtext 1.5pt;padding:0cm 5.4pt 0cm 5.4pt;
    height:27.45pt'>
    <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
    </td>
    <td width=65 valign=top style='width:48.45pt;border-top:none;border-left:
    none;border-bottom:solid windowtext 1.5pt;border-right:solid windowtext 1.5pt;
    mso-border-top-alt:solid windowtext 1.5pt;mso-border-left-alt:solid windowtext 1.5pt;
    padding:0cm 5.4pt 0cm 5.4pt;height:27.45pt'>
    <p class=MsoNormal align=center style='text-align:center'><span lang=EN-US><o:p>&nbsp;</o:p></span></p>
    </td>
   </tr>
  </table>    </td>
   </tr>
  </table>
  <p class=MsoNormal><span lang=EN-US><o:p></o:p></span></p>
  </td>",
        12=>"<p class=MsoNormal><span lang=EN-US><o:p>&nbsp;</o:p></span></p><table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt'>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;mso-yfti-lastrow:yes'>
  {#pingfen#}
  <td style='padding:0cm 5.4pt 0cm 5.4pt'>
  <p class=MsoNormal><b style='mso-bidi-font-weight:normal'><span
  style='font-family:宋体;mso-ascii-font-family:Times New Romance;mso-ascii-theme-font:
  minor-latin;mso-fareast-font-family:宋体;mso-fareast-theme-font:minor-fareast;
  mso-hansi-font-family:Times New Romance;mso-hansi-theme-font:minor-latin'>{#tixing#}{#tizhu#}</span><span
  lang=EN-US><o:p></o:p></span></b></p>
  </td>
 </tr>
</table>",
        //空白表格行添加表格数据 例如第二页的答题卡
        13=>"<p align=center>
<table class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
 style='border-collapse:collapse;border:none;mso-border-alt:solid black .5pt;
 mso-border-themecolor:text1;mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
 mso-border-insideh:.5pt solid black;mso-border-insideh-themecolor:text1;
 mso-border-insidev:.5pt solid black;mso-border-insidev-themecolor:text1'>
 {#tihao#}
</table>
</p>"
    );
    //设置word样式
    public function setDocStyle($num,$style=0){
        $width=550;
        $zjStylePath='/zjstyle/'.$style.'/headerA4.htm';
        $this->docStyle='<style>
<!--
 /* Font Definitions */
 @font-face
    {font-family:宋体;
    panose-1:2 1 6 0 3 1 1 1 1 1;
    mso-font-alt:SimSun;
    mso-font-charset:134;
    mso-generic-font-family:auto;
    mso-font-pitch:variable;
    mso-font-signature:3 135135232 16 0 262145 0;}
@font-face
    {font-family:黑体;
    panose-1:2 1 6 0 3 1 1 1 1 1;
    mso-font-alt:SimHei;
    mso-font-charset:134;
    mso-generic-font-family:auto;
    mso-font-pitch:variable;
    mso-font-signature:1 135135232 16 0 262144 0;}
@font-face
    {font-family:"Cambria Math";
    panose-1:2 4 5 3 5 4 6 3 2 4;
    mso-font-charset:1;
    mso-generic-font-family:roman;
    mso-font-format:other;
    mso-font-pitch:variable;
    mso-font-signature:0 0 0 0 0 0;}
@font-face
    {font-family:Calibri;
    panose-1:2 15 5 2 2 2 4 3 2 4;
    mso-font-charset:0;
    mso-generic-font-family:swiss;
    mso-font-pitch:variable;
    mso-font-signature:-1610611985 1073750139 0 0 159 0;}
@font-face
    {font-family:"\@黑体";
    panose-1:2 1 6 0 3 1 1 1 1 1;
    mso-font-charset:134;
    mso-generic-font-family:auto;
    mso-font-pitch:variable;
    mso-font-signature:1 135135232 16 0 262144 0;}
@font-face
    {font-family:"\@宋体";
    panose-1:2 1 6 0 3 1 1 1 1 1;
    mso-font-charset:134;
    mso-generic-font-family:auto;
    mso-font-pitch:variable;
    mso-font-signature:3 135135232 16 0 262145 0;}
 /* Style Definitions */
 p{margin:2px 0px;line-height:125%;}
 p.MsoNormal, li.MsoNormal, div.MsoNormal
    {mso-style-unhide:no;
    mso-style-qformat:yes;
    mso-style-parent:"";
    margin:0cm;
    margin-bottom:.0001pt;
    text-align:left;
    text-justify:inter-ideograph;
    mso-pagination:none;
    font-size:10.5pt;
    mso-bidi-font-size:11.0pt;
    font-family:"Times New Roman","serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoHeader, li.MsoHeader, div.MsoHeader
    {mso-style-priority:99;
    mso-style-link:"页眉 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    text-align:center;
    mso-pagination:none;
    tab-stops:center 207.65pt right 415.3pt;
    layout-grid-mode:char;
    border:none;
    mso-border-bottom-alt:solid windowtext .75pt;
    padding:0cm;
    mso-padding-alt:0cm 0cm 1.0pt 0cm;
    font-size:9.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoFooter, li.MsoFooter, div.MsoFooter
    {mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-link:"页脚 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    mso-pagination:none;
    tab-stops:center 207.65pt right 415.3pt;
    layout-grid-mode:char;
    font-size:9.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
    {mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-link:"批注框文本 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    text-align:justify;
    text-justify:inter-ideograph;
    mso-pagination:none;
    font-size:10.5pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;
    mso-font-kerning:1.0pt;}
p.MsoNoSpacing, li.MsoNoSpacing, div.MsoNoSpacing
    {mso-style-priority:1;
    mso-style-unhide:no;
    mso-style-qformat:yes;
    mso-style-parent:"";
    mso-style-link:"无间隔 Char";
    margin:0cm;
    margin-bottom:.0001pt;
    mso-pagination:widow-orphan;
    font-size:11.0pt;
    font-family:"Calibri","sans-serif";
    mso-ascii-font-family:Calibri;
    mso-ascii-theme-font:minor-latin;
    mso-fareast-font-family:宋体;
    mso-fareast-theme-font:minor-fareast;
    mso-hansi-font-family:Calibri;
    mso-hansi-theme-font:minor-latin;
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;}
span.Char
    {mso-style-name:"页眉 Char";
    mso-style-priority:99;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:页眉;
    mso-ansi-font-size:9.0pt;
    mso-bidi-font-size:9.0pt;}
span.Char0
    {mso-style-name:"页脚 Char";
    mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:页脚;
    mso-ansi-font-size:9.0pt;
    mso-bidi-font-size:9.0pt;}
span.Char1
    {mso-style-name:"批注框文本 Char";
    mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:批注框文本;
    mso-ansi-font-size:9.0pt;
    mso-bidi-font-size:9.0pt;}
span.Char2
    {mso-style-name:"无间隔 Char";
    mso-style-priority:1;
    mso-style-unhide:no;
    mso-style-locked:yes;
    mso-style-link:无间隔;
    mso-ansi-font-size:11.0pt;
    mso-font-kerning:0pt;}
.MsoChpDefault
    {mso-style-type:export-only;
    mso-default-props:yes;
    mso-ascii-font-family:"Times New Roman";
    mso-hansi-font-family:"Times New Roman";
    mso-bidi-font-family:"Times New Roman";
    mso-bidi-theme-font:minor-bidi;}
 /* Page Definitions */
 @page
    {mso-mirror-margins:yes;
    mso-page-border-surround-header:no;
    mso-page-border-surround-footer:no;
    mso-footnote-separator:url("#zjstyle#") fs;
    mso-footnote-continuation-separator:url("#zjstyle#") fcs;
    mso-endnote-separator:url("#zjstyle#") es;
    mso-endnote-continuation-separator:url("#zjstyle#") ecs;
    mso-facing-pages:yes;}
 @page Section1
    {';
        $zjStyleName='';
        $zjStyleSize='size:21.0cm 841.95pt;
    margin:45.0pt 99.85pt 45.0pt 99.85pt;';
        switch($num){
            case 'A4':
                $width=550;
                $zjStyleName='headerA4.htm';
                $zjStyleSize='size:21.0cm 841.95pt;
    margin:45.0pt 99.85pt 45.0pt 99.85pt;';
                break;
            case 'A4H':
                $width=450;
                $zjStyleName='headerA4H.htm';
                if($style) $zjStyleSize='size:841.9pt 595.3pt;
    mso-page-orientation:landscape;
    margin:42.5pt 28.0pt 42.5pt 99.85pt;
    mso-header-margin:27.55pt;
    mso-footer-margin:29.6pt;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                else  $zjStyleSize='size:841.9pt 595.3pt;
    mso-page-orientation:landscape;
    margin:42.5pt 63.0pt 42.5pt 64.85pt;
    mso-header-margin:27.55pt;
    mso-footer-margin:29.6pt;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                break;
            case 'B4H':
                $width=510;
                $zjStyleName='headerB4H.htm';
                if($style) $zjStyleSize='size:1031.95pt 728.6pt;
    mso-page-orientation:landscape;
    margin:50.0pt 89.85pt 50.0pt 139.85pt;
    mso-header-margin:42.55pt;
    mso-footer-margin:29.6pt;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                else $zjStyleSize='size:1031.95pt 728.6pt;
    mso-page-orientation:landscape;
    margin:50.0pt 114.85pt 50.0pt 114.85pt;
    mso-header-margin:42.55pt;
    mso-footer-margin:29.6pt;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                break;
            case '16K':
                $width=500;
                $zjStyleName='header16K.htm';
                if($style) $zjStyleSize='size:498.9pt 708.65pt;
    margin:21.25pt 28.05pt 21.25pt 90.7pt;';
                else $zjStyleSize='size:498.9pt 708.65pt;
    margin:21.25pt 64.05pt 21.25pt 64.7pt;';
                break;
            case 'B5':
                $width=410;
                $zjStyleName='headerB5.htm';
                if($style) $zjStyleSize='size:521.65pt 26.0cm;
    margin:45.0pt 99.85pt 45.0pt 99.85pt;';
                else $zjStyleSize='size:515.95pt 728.55pt;
    margin:45.0pt 99.85pt 45.0pt 99.85pt;';
                break;
            case 'A3H':
                $width=585;
                $zjStyleName='headerA3H.htm';
                if($style) $zjStyleSize='size:1190.55pt 841.9pt;
    mso-page-orientation:landscape;
    margin:42.55pt 28.05pt 42.55pt 99.8pt;
    mso-header-margin:49.6pt;
    mso-footer-margin:49.6pt;
    mso-page-numbers:1;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                else  $zjStyleSize='size:1190.55pt 841.9pt;
    mso-page-orientation:landscape;
    margin:42.55pt 63.05pt 42.55pt 64.8pt;
    mso-header-margin:49.6pt;
    mso-footer-margin:49.6pt;
    mso-page-numbers:1;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                break;
            case '8KH':
                $width=560;
                $zjStyleName='header8KH.htm';
                if($style) $zjStyleSize='size:1000.65pt 708.65pt;
    mso-page-orientation:landscape;
    margin:49.6pt 28.05pt 49.6pt 94.95pt;
    mso-header-margin:49.6pt;
    mso-footer-margin:49.6pt;
    mso-page-numbers:1;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                else  $zjStyleSize='size:1000.65pt 708.65pt;
    mso-page-orientation:landscape;
    margin:49.6pt 61.05pt 49.6pt 61.95pt;
    mso-header-margin:49.6pt;
    mso-footer-margin:49.6pt;
    mso-page-numbers:1;
    mso-columns:2 even 21.25pt;
    mso-column-separator:solid;';
                break;

        }
        if($zjStyleSize) $this->docStyle.=$zjStyleSize;
        $this->docStyle.='mso-even-header:url("#zjstyle#") eh1;
    mso-header:url("#zjstyle#") h1;
    mso-even-footer:url("#zjstyle#") ef1;
    mso-footer:url("#zjstyle#") f1;
    mso-paper-source:0;
    layout-grid:15.6pt;}
    div.Section1{page:Section1;}
    @page Section2
    {size:595.3pt 841.9pt;
    margin:72.0pt 89.85pt 72.0pt 89.85pt;
    mso-header-margin:42.55pt;
    mso-footer-margin:49.6pt;
    mso-page-numbers:1;
    mso-even-header:url("#zjstyle#") eh2;
    mso-header:url("#zjstyle#") h2;
    mso-even-footer:url("#zjstyle#") ef2;
    mso-footer:url("#zjstyle#") f2;
    mso-paper-source:0;
    layout-grid:15.6pt;}
    div.Section2
    {page:Section2;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
    {mso-style-name:普通表格;
    mso-tstyle-rowband-size:0;
    mso-tstyle-colband-size:0;
    mso-style-noshow:yes;
    mso-style-priority:99;
    mso-style-parent:"";
    mso-padding-alt:0cm 5.4pt 0cm 5.4pt;
    mso-para-margin:0cm;
    mso-para-margin-bottom:.0001pt;
    mso-pagination:widow-orphan;
    font-size:10.5pt;
    font-family:"Times New Roman","serif";
    mso-bidi-font-family:"Times New Roman";}
 td{font-size:10.5pt;padding:0px;border:0px;}
 table{
     border-collapse:collapse;
     padding:0px;border:0px;margin:0px;
 }
</style>
<![endif]-->';
        if($zjStyleName) $zjStylePath='/zjstyle/'.$style.'/'.$zjStyleName;
        $this->docStyle=str_replace('#zjstyle#',$zjStylePath,$this->docStyle);

        return $width;
    }
    /**
     * 获取文档字段对应关系
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    public function getTestField(){
        return array('题目'=>'Test',
                     '答案'=>'Answer',
                     '解析'=>'Analytic',
                     '题型（属性）'=>'attr_types',
                     '备注'=>'Remark');
    }
    /*****************************8
     * 以下word转html方法
     *************************************/
    /**
     * word转html
     * @param string $wfilepath 文件本地路径
     * @return 文件转换后路径
     * @author demo
     */
    public function word2html($wfilepath) {
        $error='';
        $word = new COM("Word.Application") or ($error="error|30711");
        if($error) return $error;
        $word->visible = 0;
        $word->Documents->Open($wfilepath) or  ($error="error|30713");
        if($error) return $error;
        $htmlpath = substr($wfilepath, 0, strrpos($wfilepath, '.'));
        $word->ActiveDocument->SaveAs($htmlpath.'.htm', 8);
        $word->quit(0);
        return $wfilepath;
    }
    /*****************************8
     * 以下html转word方法
     *************************************/
    //html另存为word
    public function htmltoword($wfilepath,$type='docx') {
        $error='';
        $word = new COM("Word.Application") or ($error="error|30711");
        if($error) return $error;
        $word->visible = false;
        $word->Documents->Open($wfilepath) or ($error="error|30713");
        if($error) return $error;
        $style=12;
        if($type=='doc'){
            $style=0;
        }
        $htmlpath = substr($wfilepath, 0, strrpos($wfilepath, '.'));
        $word->ActiveDocument->SaveAs($htmlpath.'.'.$type, $style);
        $word->quit(0);
        return $wfilepath;
    }
    /*引入word的header*/
    function wordheader($shiTi='word文档',$strT='.doc') {
        $shiTi=iconv('utf-8','GBK//IGNORE',$shiTi);
        header("Pragma: public");
        header("Expires: 0"); // set expiration time
        header("Cache-Component: must-revalidate, post-check=0, pre-check=0");
        if($strT=='.docx'){
            header("Content-type:application/vnd.openxmlformats");
            header("Content-type:application/octet-stream");
            $strT='.docx';
        }elseif($strT=='.xls'){
            header("Content-type:application/msexcel");
        }elseif($strT=='.xlsx'){
            header("Content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-type:application/octet-stream");
        }else{
            header("Content-type:application/msword");
            $strT='.doc';
        }

        //header("Content-type:application/msword");

        //header( "Content-Length: " . strlen( $content ) );
        header('Content-Disposition: attachment; filename="' . $shiTi.$strT . '"'); //存为的名字

        header('Content-Transfer-Encoding: binary');
    }
    //html转word  $str内容  $shiti标题  $style是否需要header
    function html2Word($str,$font=10.5) {
        //添加段落垂直居中
        $tmpStr=iconv('utf-8', 'GBK//IGNORE', "说明: http://www.tk.com智慧云题库云平台");
        $str=preg_replace('/alt=\"[^\"]*\"/is',"",$str);
        $str=preg_replace('/<v:shape ([^>]*)>/is',"<v:shape alt=\"".$tmpStr."\" \\1>",$str);
        $str=preg_replace('/<img ([^>]*)>/is',"<img alt=\"".$tmpStr."\" \\1>",$str);
        //$str=preg_replace('/<p/is',"<p style='vertical-align:middle'",$str);
        //$ole_path='/Uploads/doc/20131104/52775439632ea.files/oledata.mso';
        $olePath='/zjstyle/common/oledata.mso';///zjstyle/common/oledata.mso';

        //$http=substr($path,0,strrpos($path,'/')+1);
        //$str=preg_replace('/src="([^\.]*)\.files/is','src="'.$http.'\\1.files',$str);
        //$oledatapath=$path.'/';

        $outPut = '<html xmlns:v="urn:schemas-microsoft-com:vml"
                        xmlns:o="urn:schemas-microsoft-com:office:office" ';
        //if($word=='2007'){
        $outPut .= 'xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" ';
        //}
        $outPut .= 'xmlns:w="urn:schemas-microsoft-com:office:word"
                        xmlns="http://www.w3.org/TR/REC-html40">
                        <head>
                        <title>'.iconv('UTF-8','GBK','智慧云题库云平台').'</title>
                        <meta http-equiv=Content-Type content="text/html;charset=gb2312">
                        <meta name=ProgId content=Word.Document>
                        <meta name=Generator content="Microsoft Word 12">
                        <meta name=Originator content="Microsoft Word 12">';
        $outPut .= '<link rel=OLE-Object-Data href="'.$olePath.'">';
        $outPut .= '<xml><w:WordDocument><w:View>Print</w:View></xml><!--[if gte mso 9]><xml>
                     <o:OfficeDocumentSettings>
                      <o:AllowPNG/>
                     </o:OfficeDocumentSettings>
                    </xml><![endif]-->'.$this->docStyle.'<!--[if gte mso 9]><xml>
                     <o:shapedefaults v:ext="edit" spidmax="3074"/>
                    </xml><![endif]--><!--[if gte mso 9]><xml>
                     <o:shapelayout v:ext="edit">
                      <o:idmap v:ext="edit" data="2"/>
                     </o:shapelayout></xml><![endif]-->
                    </head>
                    <body bgcolor=white lang=ZH-CN style=\'font-size:'.$font.'pt;tab-interval:21.0pt;text-justify-trim:punctuation;\'>'.$str.'</body></html>';

        /*if($word=='2007'){
            //创建并引入docx
            $tmp=realpath('./').'/Uploads/doc/'.uniqid().'.htm';
            $tmpdoc=substr($tmp,0,strrpos($tmp,'.')).'.doc';
            $tmpdocx=substr($tmp,0,strrpos($tmp,'.')).'.docx';
            file_put_contents($tmp,$out_put);

            $word = new COM("Word.Application") or die("无法打开 MS Word");
            $word->visible = 0;
            $word->Documents->Open($tmp) or die("无法打开这个文件");

            $htmlpath = substr($tmpdoc, 0, strrpos($tmpdoc, '.'));
            $word->ActiveDocument->SaveAs($htmlpath, 12);
            $word->quit(0);
            //unlink($tmp);
            echo file_get_contents($tmpdocx);
            exit;
        }*/
        return $outPut;

        /*
         $word=new COM("Word.Application") or die("无法打开 MS Word");
         $word->visible = 0 ;
         $htmlpath=substr($wfilepath,0,strrpos($wfilepath,'.'));
         file_put_contents($htmlpath.'_a.htm',$con);

         $word->Documents->Open($htmlpath.'_a.htm') or die("无法打开这个文件");

         $word->ActiveDocument->SaveAs($htmlpath,12);
         $word->quit(0);
        */
    }
    /*生成mht*/
    function getWordDocument( $content , $absolutePath = "" , $isEraseLink = true ){
        $mht = $this->getModel('MhtFileMaker');
        if ($isEraseLink)
            $content = preg_replace('/<a\s*.*?\s*>(\s*.*?\s*)<\/a>/i' , '$1' , $content);   //去掉链接
        $images = array();
        $files = array();
        $matches = array();
        //这个算法要求src后的属性值必须使用引号括起来

        if ( preg_match_all('/<[^>]*(src|href)\s*?=\s*?[\"\']([^\"\']*)[\"\'][^>]*>|url\(\s*?[\"\']([^\"\']*)[\"\']\)/i',$content ,$matches ) ){
            $arrPath = array_merge($matches[2],$matches[3]);
            $arrPath = array_filter(array_unique($arrPath));

            rsort($arrPath);
            for ( $i=0;$i<count($arrPath);$i++){
                $path = $arrPath[$i];
                $imgPath = trim( $path );
                if ( $imgPath != "" ){
                    $files[] = $imgPath;
                    if( substr($imgPath,0,7) == 'http://'){
                        //绝对链接，不加前缀
                    }else{
                        $imgPath = $absolutePath.$imgPath;
                    }
                    $images[] = $imgPath;
                }
            }
        }
        $mht->AddContents("tmp.html",$mht->GetMimeType("tmp.html"),$content);
        for ( $i=0;$i<count($images);$i++)
        {
            $image = $images[$i];
            if ( @fopen($image , 'r') )
            {
                $imgcontent = @file_get_contents( $image );
                if ( $content )
                    $mht->AddContents($files[$i],$mht->GetMimeType($image),$imgcontent);
            }
            else
            {
                echo "file:".$image." not exist!<br />";
            }
        }

        return $mht->GetFile();
    }
    //去除多余标签  用于doc 保留公式
    function legoClean($text) {
        // normalize white space
        $text = preg_replace("/[[:space:]]+/is", " ", $text);
        $text = str_replace("> <", ">\r\r<", $text);
        $text = preg_replace("/<\!--\[if supportFields\]>[^<]*<\!\[endif\]-->/is", "", $text);
        $text = preg_replace("/<\!\[if \!supportAnnotations\]>[^<]*<\!\[endif\]>/is", "", $text);

        //去除特殊标签
        $text = preg_replace("/<\/?st1:chsdate[^>]*>/is", '', $text);

        // remove everything before <body>
        if(strstr($text,'<body')) $text = strstr($text, "<body");

        // keep tags, strip attributes
        $text = preg_replace("/<p [^>]*align=center[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "{#aligncstart#}\\1{#aligncend#}", $text);
        $text = preg_replace("/<p [^>]*align=right[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "{#alignrstart#}\\1{#alignrend#}", $text);

        $text = preg_replace("/<p [^>]*BodyTextIndent[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "<p>\\1</p>", $text);
        $text = preg_replace("/<p [^>]*margin-left[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "<p>\\1</p>", $text);

        $text = str_replace("<o:p></o:p>", "", $text);
        $text = preg_replace("/<\/?a[^>]*>/is", "", $text);

        //clean up whatever is left inside <p> and <li>
        $text = preg_replace("/<p [^>]*>/is", "<p>", $text);
        $text = preg_replace("/<\/?font[^>]*>/is", "", $text);
        $text = preg_replace("/<li [^>]*>/is", "<li>", $text);
        $text = preg_replace("/<pre[^>]*>/is", "<p>", $text);
        $text = preg_replace("/<\/pre>/is", "</p>", $text);
        // kill style and on mouse* tags
        //$text = preg_replace("/([ \f\r\t\n\'\"])style=[^>]+/is", "\\1", $text);
        $text = preg_replace("/([ \f\r\t\n\'\"])on[a-z]+=[^>]+/is", "\\1", $text);


        // kill unwanted tags
        $text = preg_replace("/<\/?span[^>]*>/is", "", $text);
        $text = preg_replace("/<\/?body[^>]*>/is", "", $text);
        $text = preg_replace("/<\/?div[^>]*>/is", "", $text);
        //$text = preg_replace("/<\\![^>]*>/is", "", $text);
        //$text = preg_replace("/<\/?[a-z]\:[^>]*>/is", "", $text);


        //remove empty paragraphs
        $text = str_replace("<p></p>", "", $text);

        //remove closing </html>
        $text = str_replace("</html>", "", $text);

        //clean up white space again
        $text = preg_replace("/[[:space:]]+/is", " ", $text);
        $text = str_replace("> <", ">\r\r<", $text);
        $text = str_replace("<br>", "<br>\r", $text);
        //$text = str_replace("file:///".dirname(__FILE__),dirname($_SERVER['PHP_SELF']),$text);
        //$text = preg_replace("/src=\"([^\\\\]*)\\\\([^\\\\]*)\\\\([^\\\\]*)\\\\/is","src=\"\\1/\\2/\\3/",$text);
        //$text = preg_replace("/v\:shapes=\"[^\"]*\"/is", '', $text);
        $text = preg_replace("/<\/a[^>]*>/is", "", $text);
        $text = preg_replace("/<b([^>]*)><\/b>/is", "", $text);
        $text = preg_replace("/<b([^>]*)>([^<]*)<\/b><b([^>]*)>([^<]*)<\/b>/is", "<b>\\2\\4</b>", $text);
        $text = preg_replace("/<b([^>]*)>([^<]*)<\/b><b([^>]*)>([^<]*)<\/b>/is", "<b>\\2\\4</b>", $text);
        $text = preg_replace("/<b([^>]*)>([^<]*)<\/b><b([^>]*)>([^<]*)<\/b>/is", "<b>\\2\\4</b>", $text);
        //$text = preg_replace("/<img width=[0-9]+ height=[0-9]+ src=\"([^\"]*)\" ([^>]*)>/is", "<img src=\"\\1\" style=\"vertical-align:middle;\" />", $text);
        $text = preg_replace('/(<br([^>]*)>([\s\r\n]*))*$/is','',$text);
        $text = preg_replace('/[\r\n]{1,}/is',"\r\n",$text);
        $text = preg_replace("/<u >/is", "<u>", $text);
        $text = preg_replace("/alt=\"[^\"]*\"/is", "", $text);
        $text = preg_replace("/title=\"[^\"]*\"/is", "", $text);
        $text = preg_replace("/href=\"[^\"]*\"/is", "", $text);

        $text = str_replace('{#aligncstart#}',"<p align=\"center\">",$text);
        $text = str_replace('{#aligncend#}',"</p>",$text);
        $text = str_replace('{#alignrstart#}',"<p align=\"right\">",$text);
        $text = str_replace('{#alignrend#}',"</p>",$text);
        $text = preg_replace('/<p([^>]*)>(\s|　|&nbsp;){1,}/is',"<p\\1>",$text);
        $text = preg_replace('/(&nbsp;){100}/is',"&nbsp;",$text);
        $text = preg_replace('/(\s|　|&nbsp;){1,}<\/p>/is',"</p>",$text);
        $text = preg_replace('/<\/?script[^>]*>|<\/?iframe[^>]*>/is',"",$text);
        $text = preg_replace("/<[\/]?strong[^>]*>/is", "", $text);

        //居中居右
        $arr=explode('【居中】',$text);
        if(count($arr)>1){
            for($i=0;$i<count($arr)-1;$i++){
                $arrx=explode('<p',$arr[$i]);
                $arrx[count($arrx)-1]=' align=center '.$arrx[count($arrx)-1];
                $arr[$i]=implode('<p',$arrx);
            }
        }
        $text=implode('',$arr);

        $text = preg_replace('/align=center[\s]*align=[\'\"]?center[\'\"]?/is',"align=center",$text);

        $arr=explode('【居右】',$text);
        if(count($arr)>1){
            for($i=0;$i<count($arr)-1;$i++){
                $arrx=explode('<p',$arr[$i]);
                $arrx[count($arrx)-1]=' align=right '.$arrx[count($arrx)-1];
                $arr[$i]=implode('<p',$arrx);
            }
        }
        $text=implode('',$arr);

        $text = preg_replace('/align=right[\s]*[\'\"]?align=right[\'\"]?/is',"align=right",$text);

        $arr=explode('【居左】',$text);
        if(count($arr)>1){
            for($i=0;$i<count($arr)-1;$i++){
                $arrx=explode('<p',$arr[$i]);
                $arrx[count($arrx)-1]=' align=left '.$arrx[count($arrx)-1];
                $arr[$i]=implode('<p',$arrx);
            }
        }
        $text=implode('',$arr);

        return $text;

    }
    //简单去除多余标签 用于html 保留img,b,p,align,u  去除段前段后空白 最后一个</p>之后数据清除  完善table标签border
    function simpleClean($text) {
        //text = implode("\r",$text);
        while(1){
            if(strstr($text,'<!--')){
                $text1=substr($text,0,strpos($text,'<!--'));
                $text2=substr($text,strpos($text,'-->')+3,strlen($text));
                $text=$text1.$text2;
            }else{
                break;
            }
        }
        //待完善 仅适用于一层
        if(strstr($text,'<![if !supportAnnotations]>')){
            $text1=explode('<![if !supportAnnotations]>',$text);
            $count=count($text1);
            for($i=1;$i<$count;$i++){
                $text2=explode('<![endif]>',$text1[$i]);
                unset($text2[0]);
                $text1[$i]=implode('<![endif]>',$text2);
            }
            $text=implode('',$text1);
        }
        $text = preg_replace("/<\!\-\-\[if([^\[]*)\[endif\]\-\->/is", '', $text);

        //去除特殊标签
        $text = preg_replace("/<\/?st1:chsdate[^>]*>/is", '', $text);

        // normalize white space
        $text = preg_replace("/[[:space:]]+/is", " ", $text);
        $text = str_replace("> <", ">\r\r<", $text);
        $text = str_replace("<o:p></o:p>", "", $text);
        $text = preg_replace("/<\/?a[^>]*>/is", "", $text);


        // remove everything before <body>
        if(strstr($text,'<body')) $text = strstr($text, "<body");

        //为没有加border的table 判断是否加border
        $tmpArr=explode('<table',$text);
        if(count($tmpArr)>1){
            foreach($tmpArr as $i=>$iTmpArr){
                if($i==0) continue;
                $tmpArrTwo=explode('</table>',$iTmpArr);
                if(count($tmpArrTwo)==2){
                    //判断是否存在table边框
                    //如果没有border=0则继续下一个循环
                    $tmpStrOne=preg_replace('/^[^>]*border=0/i','',$tmpArrTwo[0]);
                    if($tmpStrOne==$tmpArrTwo[0]) continue;
                    $tmpStrOne=preg_replace('/border:[^;]*[1-9].[0-9]/i','',$tmpArrTwo[0]);

                    if($tmpStrOne==$tmpArrTwo[0]) continue;
                    $tmpArrTwo[0]=preg_replace('/^([^>]*)border=0/i','\1border=1',$tmpArrTwo[0]);
                    $tmpArr[$i]=implode('</table>',$tmpArrTwo);
                }
            }
        }
        $text=implode('<table',$tmpArr);


        // keep tags, strip attributes
        $text = preg_replace("/<p [^>]*align=center[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "{#aligncstart#}\\1{#aligncend#}", $text);
        $text = preg_replace("/<p [^>]*align=right[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "{#alignrstart#}\\1{#alignrend#}", $text);

        $text = preg_replace("/<p [^>]*BodyTextIndent[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "<p>\\1</p>", $text);
        $text = preg_replace("/<p [^>]*margin-left[^>]*>([^\n|\n\015|\015\n]*)<\/p>/is", "<p>\\1</p>", $text);
        //$text = str_replace(" ","",$text);

        //clean up whatever is left inside <p> and <li>
        $text = preg_replace("/<p [^>]*>/is", "<p>", $text);
        $text = preg_replace("/<\/p>/is", "</p>", $text);
        $text = preg_replace("/<\/?font[^>]*>/is", "", $text);
        $text = preg_replace("/<li [^>]*>/is", "<li>", $text);
        $text = preg_replace("/<pre[^>]*>/is", "<p>", $text);
        $text = preg_replace("/<\/pre>/is", "</p>", $text);
        // kill style and on mouse* tags
        $text = preg_replace("/([ \f\r\t\n\'\"])style=[^>]+/is", "\\1", $text);
        $text = preg_replace("/([ \f\r\t\n\'\"])on[a-z]+=[^>]+/is", "\\1", $text);

        //checkimg
        $text = preg_replace("/<img(border=[0-9]+|width=[0-9]+|height=[0-9]+|\s)*([^>]*)>/is", "<img \\2 />", $text);
        if(!strstr($text,'<img')) $text = preg_replace("/<v:imagedata src=\"([^\"]*)\"([^\/]*)\/>/is", "<img src=\"\\1\" style=\"vertical-align:middle;\"/>", $text);
        $text = preg_replace("/<img src=\"([^\"]*)\" ([^>]*)>/is", "<img src=\"\\1\" style=\"vertical-align:middle;\" />", $text);
        // kill unwanted tags
        $text = preg_replace("/<\/?span[^>]*>/is", "", $text);
        $text = preg_replace("/<\/?body[^>]*>/is", "", $text);
        $text = preg_replace("/<\/?div[^>]*>/is", "", $text);
        $text = preg_replace("/<\/?em[^>]*>/is", "", $text);
        $text = preg_replace("/<\\![^>]*>/is", "", $text);
        $text = preg_replace("/<\/?[a-z]\:[^>]*>/is", "", $text);
        $text = preg_replace("/alt=\"[^\"]*\"/is", "", $text);
        $text = preg_replace("/<[\/]?strong[^>]*>/is", "", $text);


        //remove empty paragraphs
        $text = str_replace("<p></p>", "", $text);

        //remove closing </html>
        $text = str_replace("</html>", "", $text);

        //clean up white space again
        $text = preg_replace("/[[:space:]]+/is", " ", $text);
        $text = str_replace("> <", ">\r\r<", $text);
        $text = str_replace("<br>", "<br>\r", $text);
        //$text = str_replace("file:///".dirname(__FILE__),dirname($_SERVER['PHP_SELF']),$text);
        //$text = preg_replace("/src=\"([^\\\\]*)\\\\([^\\\\]*)\\\\([^\\\\]*)\\\\/is","src=\"\\1/\\2/\\3/",$text);


        $text = preg_replace("/v\:shapes=\"[^\"]*\"/is", '', $text);
        $text = preg_replace("/<a([^>]*)><\/a>/is", "", $text);
        $text = preg_replace("/<\/a>/is", "", $text);
        $text = preg_replace("/<a [^>]*>/is", "", $text);
        $text = preg_replace("/<b([^>]*)><\/b>/is", "", $text);
        $text = preg_replace("/<b([^>]*)>([^<]*)<\/b><b([^>]*)>([^<]*)<\/b>/is", "<b>\\2\\4</b>", $text);
        $text = preg_replace("/<b([^>]*)>([^<]*)<\/b><b([^>]*)>([^<]*)<\/b>/is", "<b>\\2\\4</b>", $text);
        $text = preg_replace("/<b([^>]*)>([^<]*)<\/b><b([^>]*)>([^<]*)<\/b>/is", "<b>\\2\\4</b>", $text);
        $text = preg_replace('/(<br([^>]*)>([\s\r\n]*))*$/is','',$text);
        $text = preg_replace('/[\r\n]{1,}/is',"\r\n",$text);
        $text = preg_replace("/<u >/is", "<u>", $text);

        $text = str_replace('{#aligncstart#}',"<p align=\"center\">",$text);
        $text = str_replace('{#aligncend#}',"</p>",$text);
        $text = str_replace('{#alignrstart#}',"<p align=\"right\">",$text);
        $text = str_replace('{#alignrend#}',"</p>",$text);
        $text = preg_replace('/<p([^>]*)>(\s|　|&nbsp;){1,}/is',"<p\\1>",$text);
        $text = preg_replace('/(&nbsp;){100}/is',"&nbsp;",$text);
        $text = preg_replace('/(\s|　|&nbsp;){1,}<\/p>/is',"</p>",$text);
        $text = preg_replace('/<\/?script[^>]*>|<\/?iframe[^>]*>/is',"",$text);

        $text = preg_replace('/【[<\/a-z=\'\":\s\->]*小[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->0-9]*】/is',"【小题】",$text);
        $text = preg_replace('/【[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->]*号[<\/a-z=\'\":\s\->0-9]*】/is',"【题号】",$text);


        //居中居右
        $arr=explode('【居中】',$text);
        if(count($arr)>1){
            for($i=0;$i<count($arr)-1;$i++){
                $arrx=explode('<p',$arr[$i]);
                $arrx[count($arrx)-1]=' align=center '.$arrx[count($arrx)-1];
                $arr[$i]=implode('<p',$arrx);
            }
        }
        $text=implode('',$arr);

        $text = preg_replace('/align=center[\s]*align=[\'\"]?center[\'\"]?/is',"align=center",$text);

        $arr=explode('【居右】',$text);
        if(count($arr)>1){
            for($i=0;$i<count($arr)-1;$i++){
                $arrx=explode('<p',$arr[$i]);
                $arrx[count($arrx)-1]=' align=right '.$arrx[count($arrx)-1];
                $arr[$i]=implode('<p',$arrx);
            }
        }
        $text=implode('',$arr);

        $text = preg_replace('/align=right[\s]*align=[\'\"]?right[\'\"]?/is',"align=right",$text);

        $arr=explode('【居左】',$text);
        if(count($arr)>1){
            for($i=0;$i<count($arr)-1;$i++){
                $arrx=explode('<p',$arr[$i]);
                $arrx[count($arrx)-1]=' align=left '.$arrx[count($arrx)-1];
                $arr[$i]=implode('<p',$arrx);
            }
        }
        $text=implode('',$arr);

        return $text;
    }
    //分割后 除去多余标签
    function lastClean($text) {
        //截取到最后一个</p>
        $tmpStr=preg_replace('/(<\/?[a-hj-z][^>]*>|\n|\r|\s|　)*/is','',$text);
        if($tmpStr==='') return '';

        //判断第一个<p>前面是否只有</p>  如果有去掉
        $tmpArr=explode('<p>',$text);
        $tmpStr=preg_replace('/(<\/[a-hj-z][^>]*>|\n|\r|\s|　)*/is','',$tmpArr[0]);
        if($tmpStr==='') unset($tmpArr[0]);

        $tmpStr=preg_replace('/(<\/[a-hj-z][^>]*>|\n|\r|\s|　)*/is','',$tmpArr[count($tmpArr)-1]);
        if($tmpStr==="") unset($tmpArr[count($tmpArr)-1]);

        $tmpStr=implode('<p>',$tmpArr);

        return $tmpStr;
    }


    //为doc进行过滤
    public function changeArrFormatDoc($arr) {
        if ($arr) {
            foreach ($arr as $i => $iArr) {
                foreach ($iArr as $j => $jArr) {
                    if($j===0 && strstr($arr[$i][$j],'【小题')){
                        $arr[$i][$j]=$this->addSj($arr[$i][$j]);
                    }
                }
            }
        }
        return $arr;
    }
    //为HTML进行过滤 并提取上下标为图片
    public function changeArrFormat($arr){
        if($arr){
            foreach($arr as $i=>$iArr){
                foreach($iArr as $j=>$jArr){
                    if($j===0 && strstr($arr[$i][$j],'【小题')){
                        $arr[$i][$j]=$this->addSj($arr[$i][$j]);
                    }
                }
            }
            //if($ifReplace) $arr=$this->subp2img($arr,$path);
        }
        return $arr;
    }
    //添加缩进
    public function addSj($str){
        $tmpArr=preg_split('/【小题】/i',$str);
        $tmpArr2=preg_split('/<p\s*>/i',$tmpArr[0]);
        if(count($tmpArr2)>0){
            if(preg_replace('/[\s　\r\n]*/is','',$tmpArr2[count($tmpArr2)-1])=='') unset($tmpArr2[count($tmpArr2)-1]);
            if(!strstr($tmpArr2[count($tmpArr2)-1],'</p>')){
                $tmpArr[0]=$tmpArr2[count($tmpArr2)-1];
                unset($tmpArr2[count($tmpArr2)-1]);
                $tmpArr[0]=implode('<p>　　',$tmpArr2).$tmpArr[0];
            }
        }
        return implode('【小题】',$tmpArr);
    }
    //获取文件路径
    public function getDocContent($url){
        $host=C('WLN_DOC_HOST_IN');
        $content='';
        if($host){
            $urlOut=$host.R('Common/UploadLayer/getDocServerUrl',array($url,'getWordFile','',''));
            $str=file_get_contents($urlOut);

            $content=mb_convert_encoding($str,'utf-8','gbk');
            if(empty($content)) //解决方正排版符号导致下载空白
                $content=iconv('gbk', 'utf-8//IGNORE', $str);
        }else{
            $str=file_get_contents(realpath('./') .$url );
            $content=mb_convert_encoding($str,'utf-8','gbk');
            if(empty($content)) //解决方正排版符号导致下载空白
                $content=iconv('gbk', 'utf-8//IGNORE', $str);
        }

        //对图片中的路径进行转换
        $fileUrl=substr($url,0,strrpos($url,'.')+1).'files';
        $content=$this->changeImgPath($fileUrl,$content);

        return $content;
    }
    //更改图片路径为 /Uploads/doc/xxxxxx/xxxxxx.flies/imagexxx.xxx
    public function changeImgPath($path,$html){
        return str_replace(substr($path,strrpos($path,'/')+1,strlen($path)),$path,$html);
    }
    //修改htm内绝对路径为相对路径
    public function changeAllPath($DocHtmlPath) {
        $host=C('WLN_DOC_HOST');
        if(!$host){
            $f=substr($DocHtmlPath,strrpos($DocHtmlPath,'/')+1,strrpos($DocHtmlPath,'.')-strrpos($DocHtmlPath,'/')).'files';
            $realp='file:///'.str_replace('/','\\',realpath('./').substr($DocHtmlPath,0,strrpos($DocHtmlPath,'/')+1));
            $html=str_replace($realp,'',file_get_contents(realpath('./').$DocHtmlPath));
            $realp='file:///'.str_replace('\\','/',realpath('./').substr($DocHtmlPath,0,strrpos($DocHtmlPath,'/')+1));
            $html=str_replace($realp,'',$html);
            $html=str_replace($f.'\\',$f.'/',$html);
            $html=preg_replace('/<base ([^>]*)>/is','',$html);
            file_put_contents(realpath('./').$DocHtmlPath,$html);
        }
    }
    /**
     * 格式化doc文档输出的试题内容字符串
     * @param string $output 需要格式化的内容 请仅传递试题部分的内容
     * @return string
     * @author demo
     */
    protected function formatDocOutput($output,$subjectID,$style='.docx'){
        //对表格下载进行过滤table标签border为1则素有td加上对应样式
        $output=$this->changeDocTableBorder($output);

        //去掉居中 使生成以后的公式垂直对齐方式自适应
        $oneReplace='vertical-align:middle;font-family:Times New Romance';
        $twoReplace='<p style="vertical-align:middle;font-family:Times New Romance;">';
        if($style=='.doc'){
            $subject=SS('subject');
            $formatDoc=$subject[$subjectID]['FormatDoc'];
            if($formatDoc==1){
                $oneReplace='font-family:Times New Romance';
                $twoReplace='<p style="font-family:Times New Romance;">';
            }else if($formatDoc==0){
                //对试卷逐行分析
                $tmpArr=preg_split('/<p>|<p\sstyle=["|\']vertical-align:middle["|\']>/i',$output);

                $return=''; //输出数据
                foreach($tmpArr as $i=>$iTmpArr){
                    if($i==0){
                        $return=$iTmpArr;
                        continue;
                    }
                    //<!--[if supportFields]> 公式标记
                    if(strstr($iTmpArr,'msEquation')){
                        $return.='<p style="font-family:Times New Romance">'.$iTmpArr;
                    }else{
                        $return.='<p style="vertical-align:middle;font-family:Times New Romance">'.$iTmpArr;
                    }
                }
                $output=str_replace("<td",'<td style="margin:2px 0px;" ',$return);

                return $output;
            }
        }
        $output=str_replace("vertical-align:middle",$oneReplace,$output);
        $output=str_replace("<p>",$twoReplace,$output);
        $output=str_replace("<td",'<td style="margin:2px 0px;" ',$output);

        return $output;
    }

    //用标记分割字符串 $style=1 word  $style=2 html
    public function division($html, $start ,$style=1) {
        if($style==1) $html=$this->legoClean($html);
        else if($style==2) $html=$this->simpleClean($html);

        $startCount = count($start); //分割标记个数
        $arr = explode(end($start), $html); //以最后一个标签的结尾分割
        unset ($arr[count($arr) - 1]);
        $newArr = array ();
        if ($arr) {
            foreach ($arr as $i => $iArr) {
                $surPlus = $iArr; //存储分割后剩余数据
                //倒叙分割数据
                for ($j = $startCount -2; $j >= 0; $j--) {
                    if (strstr($surPlus, $start[$j])) {
                        $lsArr = array ();
                        $lsArr = explode($start[$j], $surPlus);
                        $surPlus = $lsArr[count($lsArr)-2];
                        $newArr[$i][$j] = $this->lastClean($lsArr[count($lsArr)-1]);
                    }else{
                        $newArr[$i][$j] = '';
                    }
                }
                ksort($newArr[$i]);
            }
        }
        return $newArr;
    }
    /**
     * 根据str生成试卷及更新相关属性
     * $downStyle: 下载类型
     * $str：work字符串
     * $docname 文档名称
     * $docversion 文档类型 .doc .docx
     * $issaverecord 是否存档 0不显示存档  1显示存档
     * $backtype 0返回js 1返回地址
     * $subejctID 学科id
     * $cookieStr cookie数据
     * @return array|string
     * @author demo
     * */
    public function createDocCon($param,$userName){
        extract($param);
        if(empty($backType)) $backType=0;
        if(empty($subjectID)) $subjectID=0;
        if(empty($ifShare)) $ifShare=0;
        $str=mb_convert_encoding($str,'gbk','utf-8');
        $docDown = $this->getModel('DocDown');
        $buffer = $docDown->isExistTheCookie($cookieStr.$docVersion.$docStyle.$docType);
        $urlPath=$buffer['DocPath'];
        $downID=$buffer['DownID'];
        $downUserName=$buffer['UserName'];
        $url = '';
        $host=C('WLN_DOC_HOST');

        if($urlPath !== ''){
            $url = $host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down','mht',$docName));
        }else{
            $str = $this->html2word($str);
            if($host){
                //$str=str_replace('10.5','15.5',$str);
                $urlPath=R('Common/UploadLayer/setWordDocument',array($str ,substr($docVersion,1)));
                //验证下载word是否正常
                if(strstr($urlPath,'error') || !R('Common/UploadLayer/checkDownUrl',array($urlPath))){
                    //错误记录
                    $data=array();
                    $data['description'] = '文档生成失败，下载失败';
                    $data['msg'] = $urlPath.'###@###'.$cookieStr;
                    $this->addErrorLog($data);
                    $data=array();
                    $data[0]='false';
                    $data['msg']='30501';
                    return $data;
                }
                $url=$host.R('Common/UploadLayer/getDocServerUrl',array($urlPath,'down','mht',$docName));
            }else{
                $hostIn="http://" . $_SERVER['HTTP_HOST'] . "/";
                $urlPath = '/Uploads/mht/' . date('Y/md', time());
                $path = realpath('./') . $urlPath;
                if (!file_exists($path))
                    $this->createpath($path);
                $content=$this->getWordDocument( $str ,$hostIn);
                $urlPath = $urlPath . '/' . uniqid(mt_rand()) . rand(100, 999) . '.mht';
                $docPath = realpath('./') . $urlPath;
                $sizeLenght=file_put_contents(iconv('UTF-8', 'GBK//IGNORE', $docPath), $content);
                $newPath=$this->htmltoword(iconv('UTF-8', 'GBK//IGNORE', $docPath),substr($docVersion,1));
                unlink($docPath);
                $urlPath = str_replace('.mht',$docVersion,$urlPath);
                if($sizeLenght==0){
                    $data=array();
                    $data['description'] = '文档写入失败，下载失败';
                    $data['msg'] = $sizeLenght;
                    $this->addErrorLog($data);
                    $data=array();
                    $data[0]='false';
                    $data['msg']='30731'; //文档写入失败
                    return $data;
                }
            }
        }

        if($downUserName!=$userName){
            //保存试卷
            $data=array();
            $data['UserName']=$userName;
            $data['LoadTime']=time();
            $data['DocPath']=$urlPath;
            if(empty($subjectID)) $subjectID=cookie('SubjectId');
            if(!is_numeric($subjectID)) $subjectID=0;
            $data['SubjectID']=$subjectID;
            $data['DocName']=$docName;
            $data['Point']=0;
            $data['IP']=get_client_ip(0,true);
            $data['DownStyle']=$downStyle;
            if($isSaveRecord==1){
                $data['IfShow']=1;
            }else{
                $data['IfShow']=0;
            }
            $data['CookieStr'] = $cookieStr;

            $downID=$docDown->insertData(
                $data);

            if($downID===false){
                $logData=array();
                $logData['description'] ='历史下载写入错误';
                $logData['msg'] = $data;
                $this->addErrorLog($logData);
                $data=array();
                $data[0]='false';
                $data['msg']='30731'; //文档写入失败
                return $data;
            }

            if($downUserName) $ifShare=0; //如果是重复数据默认不分享
            $ud = $this->getModel('UserDynamic');
            switch($downStyle){ //答题卡4 导学案3 作业2，试卷1
                case 1:
                    $ud->Classification = 'doc';
                    break;
                case 2:
                    $ud->Classification = 'work';
                    break;
                case 3:
                    $ud->Classification = 'case';
                    break;
                case 4:
                    $ud->Classification = 'answer';
                    break;
            }
            $ud->Title = $docName;
            $ud->UserName = $userName;
            $ud->SubjectID = $subjectID;
            $ud->IfShare = $ifShare;
            $ud->add($downID);
        }


        if($backType==1) return $urlPath;

        //输出doc路径
        $outPut='';
        if($isSaveRecord==1)
            $outPut='<br/>您也可以到“<a href=\\\'' .U('User/Home/down').'\\\'>历史下载</a>”中重新下载。';

        $id=md5($downID.'(*&!@#%^&#@$)(@!^^#!%@#&*@!)');
        if(!$host) $url=U('Home/Index/wordDownLoad',array('DownID'=>$downID,'id'=>$id));
        $data[0]='success';
        $data['msg']="<form id='thisform' method='post' action='" . $url ."'></form>
                <script>
                $.myCommon.verifyImg();
                $.workDown.alert('word文档已经生成，请<a href=\'".$url."\'>点击这里</a>将文档保存到本地。".$outPut."<span class=downFeedback><a>【下载反馈】</a></span>');
                document.getElementById('thisform').submit();
                </script>";
        return $data;
    }
    /**
     * 仅用于答案解析交叉 替换doc中的【小题x】
     * @param string $str 字符串1
     * @param string $str1 字符串2
     * @param int $key 序号
     * @return string 字符串
     * @author demo
     */
    public function changeHZJX($str,$str1,$key) {
        //按小题标签切割
        $arr=$this->xtnum($str,3);
        $arr1=$this->xtnum($str1,3);

        if($arr){
            foreach($arr as $i=>$iArr){
                if($i!=0){
                    $str=R('Common/TestLayer/delMoreTag',array($iArr));
                    $str=$this->filterP($str);
                    $str=$this->ifEmptyStr($str)==='' ? '无' : $str; //判断数据是否为空
                    $str=$this->addNum($key).$str;//添加序号;
                    $arr[$i]='<p>'.$str.'</p>';
                    $key++;
                }
            }
        }
        if($arr1){
            foreach($arr1 as $i=>$iArr1){
                if($i!=0){
                    $str=R('Common/TestLayer/delMoreTag',array($iArr1));
                    $str=$this->filterP($str);
                    $str=$this->ifEmptyStr($str)==='' ? '无' : $str; //判断数据是否为空
                    $arr1[$i]='<p>'.$str.'</p>';
                }
            }
        }
        if(count($arr)==count($arr1)){
            foreach($arr as $i=>$iArr){
                if($i!=0)
                    $arr[$i]=$arr[$i].'<p>【解析】'.R('Common/TestLayer/removeLeftTag',array($arr1[$i],'<p>'));
            }
        }
        //$tmp=$arr[0];
        unset($arr[0]);
        $str=implode('<p>&nbsp;</p>',$arr);
        //$str=$tmp.$str;
        $str=$this->myFilter($str); //替换特殊格式
        return $str;
    }
    /**
     * 仅处理包含小题的数据 替换doc中的【小题x】 包括格式化选项
     * @param string $str 需要格式化的字符串
     * @param int $key 试题序号
     * @param int $width 文档宽度
     * @param int $score 多题分值
     * @param int $ifFormat 是否格式化选项 默认0不排版 只有题文需要
     * @param int $optionWidth 选项宽度
     * @param int $optionNum 选项数量
     * @param int $ifAddNum 0加序号 1当key为1且没有小题时不加序号
     * @param int $ifDoc 0不导出word 1导出word  导出word做特殊处理
     * @return string 组合后的试题字符串
     * @author demo
     */
    public function changeHZ($param) {
        $str=$param['Test'];
        $key=$param['Key'];
        $width=$param['Width'];
        $score=$param['Score'];
        $ifFormat=$param['IfFormat'];
        $optionWidth=$param['OptionWidth'];
        $optionNum=$param['OptionNum'];
        $ifAddNum=$param['IfAddNum'];
        $ifDoc=$param['IfDoc'];
        if(empty($width)) $width=550;
        if(empty($score)) $score=0;
        if(empty($ifFormat)) $ifFormat=0;
        if(empty($optionWidth)) $optionWidth=0;
        if(empty($optionNum)) $optionNum=0;
        if(empty($ifAddNum)) $ifAddNum=0;
        if(empty($ifDoc)) $ifDoc=0;

        $oldKey=$key; //存储旧序号
        if($key==0) $key=-1; //如果序号为0那么改为-1 不加序号
        $arr=$this->xtnum($str,3); //按小题标签切割

        if($arr){
            $optionWidthList=array();
            $optionNumList=array();
            if($optionWidth){
                $optionWidthList=explode(',',$optionWidth);
            }
            if($optionNum){
                $optionNumList=explode(',',$optionNum);
            }
            foreach($arr as $i=>$iArr){
                if($i!=0){
                    if($optionWidthList[$i-1]<10){
                        $param=array();
                        $param['Test']=$arr[$i];
                        $param['Key']=$key;
                        $param['Width']=0;
                        $param['Score']=$score[$i-1];
                        $param['IfFormat']=0;
                        $param['OptionWidth']=0;
                        $param['OptionNum']=0;
                        $param['IfAddNum']=$ifAddNum;
                        $arr[$i]=$this->changeGS($param);
                    }else{
                        $param=array();
                        $param['Test']=$arr[$i];
                        $param['Key']=$key;
                        $param['Width']=$width;
                        $param['Score']=$score[$i-1];
                        $param['IfFormat']=$ifFormat;
                        $param['OptionWidth']=$optionWidthList[$i-1];
                        $param['OptionNum']=$optionNumList[$i-1];
                        $param['IfAddNum']=$ifAddNum;
                        $arr[$i]=$this->changeGS($param);
                    }
                    if($key>0) $key++;
                }
            }
            $str=implode('',$arr);
        }else{
            $str=R('Common/TestLayer/delMoreTag',array($str));
        }

        //添加题号
        if($key>0){
            $arr=$this->xtnum($str,4);
            if($arr){
                foreach($arr as $i=>$iArr){
                    if($i!=0){
                        $arr[$i]='<u>　'.$oldKey.'　</u>'.$iArr;
                        $oldKey++;
                    }
                }
                $str=implode(' ',$arr);
            }
        }
        $str=$this->filterP($str);
        $str=$this->myFilter($str); //替换特殊格式

        $str='<p>'.$str.'</p>';
        return $str;
    }
    /**
     * 仅处理不包含小题的数据 改变格式包括格式化选项和添加序号
     * @param string $str 字符串
     * @param int $key 试题序号 -1返回小题 0返回题文
     * @param int $width 文档宽度
     * @param int $score 试题分值
     * @param int $ifFormat 是否格式化选项 默认0不排版  只有题文需要
     * @param int $optionWidth 选项宽度
     * @param int $optionNum 选项数量
     * @param int $ifAddNum 0加序号 1当key为1且没有小题时不加序号
     * @return string 组合后的试题字符串
     * @author demo
     */
    public function changeGS($param){
        $str=$param['Test'];
        $key=$param['Key'];
        $width=$param['Width'];
        $score=$param['Score'];
        $ifFormat=$param['IfFormat'];
        $optionWidth=$param['OptionWidth'];
        $optionNum=$param['OptionNum'];
        $ifAddNum=$param['IfAddNum'];
        $ifDoc=$param['IfDoc'];
        if(empty($width)) $width=550;
        if(empty($score)) $score=0;
        if(empty($ifFormat)) $ifFormat=0;
        if(empty($optionWidth)) $optionWidth=0;
        if(empty($optionNum)) $optionNum=0;
        if(empty($ifAddNum)) $ifAddNum=0;
        if(empty($ifDoc)) $ifDoc=0;

        if($ifFormat!=0){
            //计算选项行数
            $bl=$this->calcLine($optionWidth,$width,$optionNum);
            $str=$this->formatOneTest($str,$bl,$optionNum);
        }else{
            $str=R('Common/TestLayer/delMoreTag',array($str)); //去除多余标签
            $str=$this->ifEmptyStr($str)==='' ? '无' : $str; //判断数据是否为空
        }
        $str=$this->filterP($str);

        //添加序号 或者返回标签
        $output='';
        if($key>0 and $ifAddNum==0) $output=$this->addNum($key);//添加序号;
        else{
            if($key==-1) $output='【小题】';
        }
        //添加分值
        if($score) $output=$output.'<span>(本题'.$score.'分)</span>';
        //添加序号到试题
        if(strstr($str,'【题文】')) $str=str_replace('【题文】',$output,$str);
        else $str=$output.$str;
        $str='<p>'.$str.'</p>';
        return $str;
    }
    /**
     * 为文档数据过滤特殊格式
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    public function myFilter($str){
        //图片圆形和三角附加 path || <v:formulas>x
        $str=str_replace('path="al10800,10800@8@8@4@6,10800,10800,10800,10800@9@7l@30@31@17@18@24@25@15@16@32@33xe"','path="m@4@5l@4@11@9@11@9@5xe"',$str);
        $str=$this->filterStr($str,'<v:formulas>','</v:formulas>',' <v:formulas>
                  <v:f eqn="if lineDrawn pixelLineWidth 0"/>
                  <v:f eqn="sum @0 1 0"/>
                  <v:f eqn="sum 0 0 @1"/>
                  <v:f eqn="prod @2 1 2"/>
                  <v:f eqn="prod @3 21600 pixelWidth"/>
                  <v:f eqn="prod @3 21600 pixelHeight"/>
                  <v:f eqn="sum @0 0 1"/>
                  <v:f eqn="prod @6 1 2"/>
                  <v:f eqn="prod @7 21600 pixelWidth"/>
                  <v:f eqn="sum @8 21600 0"/>
                  <v:f eqn="prod @7 21600 pixelHeight"/>
                  <v:f eqn="sum @10 21600 0"/>
                 </v:formulas>');

        return $str;
    }
    /**
     * 过滤标签
     * @param string $str 需要过滤的字符串
     * @return string
     * @author demo
     */
    public function filterP($str){
        $output=preg_replace('/<pre[^>]*>/is','<p>',$str); //pre标记替换成p
        $output=preg_replace('/<\/pre>/is','</p>',$output);
        $output=preg_replace('/<[\/]?strong[^>]*>/is','',$output); //去掉加粗标记
        $output=preg_replace('/<br([^>]*)>([\r\n\s]*)<br([^>]*)>/is','<br/>',$output);
        $output=preg_replace('/(<p\s*>[\/\s　<:>]*<\/p>)|(<p\s?>|\s|　)*$/is','',$output);
        $output=preg_replace('/<td([^>]*)>[\r\n]*<\/td>/is','<td\1>&nbsp;</td>',$output);
        $output=preg_replace('/^(<\/p[^>]*>|\n|\r|\s|　)*/is','',$output);
        $output=preg_replace("/<\!--\[if supportFields\]>[^<]*<\!\[endif\]-->/is", "", $output);
        return $output;
    }
    /**
     * 判断字符串是否为空
     * @param string $str 字符串
     * @return string
     * @author demo
     */
    public function ifEmptyStr($str){
        if(strstr($str,'<img')) return $str;
        $tmpStr=preg_replace('/<[^>]*>|[　\s\n\r]*/is','',$str);
        if($tmpStr==='') return '';
        return $str;
    }
    /**
     * 过滤字符串中的字符串
     * @param string $str  原字符串
     * @param string $start 开始部分
     * @param string $end 结束部分
     * @param string $replace 替换字符串
     * @return string 处理后的字符串
     * @author demo
     */
    public function filterStr($str,$start,$end,$replace=''){
        $output='';
        $arr=array();
        $brr=array();
        $arr=explode($start,$str);
        if(count($arr)<2) return $str;
        foreach($arr as $iArr){
            if(strstr($iArr,$end)){
                $brr=explode($end,$iArr);
                $output.=$replace.$brr[1];
                continue;
            }
            $output.=$iArr;
        }
        return $output;
    }
    /**
     * 计算选项每行排列个数
     * @param int $optionWidth 选项长度
     * @param int $maxWidth 最大长度
     * @param int $num 选项数量
     * @return string
     */
    public function calcLine($optionWidth,$maxWidth,$num){
        $bl=1;//默认每行排一个
        if($num==4){
            if($optionWidth*4+20<$maxWidth) $bl=4;
            else if($optionWidth*2+10<$maxWidth) $bl=2;
        }else if($num==3){
            if($optionWidth*3+15<$maxWidth) $bl=3;
        }
        return $bl;
    }
    /**
     * 为试题添加序号
     * @param int $key 试题序号
     * @return string
     * @author demo
     */
    public function addNum($key){
        return "<span lang=EN-US style='font-size:10.0pt;mso-bidi-font-size:12.0pt'>".$key."．</span>";
    }
    /**
     * 获取字符串中英文和中文长度；
     * @param string $str 字符串
     * @return array
     * @author demo
     */
    public function strLength($str)
    {
        $kb=explode('&nbsp;',$str);
        $str = preg_replace('/\&nbsp\;/','',$str);
        $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));
        $arr['en'] = strlen($str) - $length+count($kb)-1;
        $arr['cn'] = intval($length / 3);
        return $arr;
    }
    /**
     * 获取小题数量
     * @param string $str 字符串
     * @param string $style 默认1获取小题题号数量   2获取小题数量  3分割小题  4分割题号
     * @return string|int 返回值 0无小题或题号
     * @author demo
     */
    public function xtnum($str,$style=1){
        $output='';
        $arr=preg_split("/【[<\/a-z=\'\":\s\->]*小[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->0-9]*】/i",$str);
        switch($style){
            case 1:
                if(count($arr)>1){
                    return count($arr)-1;
                }
                $arr1=preg_split("/(<u[^>]*>)?(\s|　)*【[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->]*号[<\/a-z=\'\":\s\->0-9]*】(\s|　)*(<\/u>)?/i",$str);
                if(count($arr1)>1){
                    return count($arr1)-1;
                }
                return 0;
                break;
            case 2:
                if(count($arr)>1){
                    return count($arr)-1;
                }
                return 0;
                break;
            case 3:
                if(count($arr)>1){
                    return $arr;
                }
                break;
            case 4:
                //对多个空格U进行合并
                $str=preg_replace("/<u[^>]*>(\s| |　|&nbsp;)*<\/u>/i",'',$str);
                $arr1=preg_split("/(<u[^>]*>)?(\s| |　|&nbsp;)*【[<\/a-z=\'\":\s\->]*题[<\/a-z=\'\":\s\->]*号[<\/a-z=\'\":\s\->0-9]*】(\s| |　|&nbsp;)*(<\/u>)?/i",$str);
                if(count($arr1)>1){
                    return $arr1;
                }
                break;
        }
        return 0;
    }
    /**
     * 格式化一道题
     * @param string $str 需要分割的字符串
     * @param string $bl 选项行数
     * @param string $optionNum 选项数量
     * @return string 返回格式化后的字符串
     * @author demo
     */
    public function formatOneTest($str,$bl,$optionNum) {

        $str=R('Common/TestLayer/delMoreTag',array($str)); //去除多余标记

        preg_match_all($this->_regAD,$str,$arr); //切割选项
        $arr1=array_unique($arr[1]);
        //选项小于三 或者 选项有重复选项
        if(count($arr1)<3 || count($arr1)!=count($arr[1])){
            return $str;
        }
        $keywords=$this->formatStrToArr($str); //按选项切割为数组

        //选项数量和已知的是否匹配 不匹配则返回
        if(($optionNum+1)!=count($keywords)) return $str;
        $tmpArr=$this->optionLine($keywords,$bl); //对选项数量进行排版
        //仅有选项 例如：听力类型
        if(!$tmpArr[0]){
            $tmpArr[1]='<table width="100%" class="MsoNormalTable"><tr><td width="10" valign="top">【题文】</td><td valign="top">'.$tmpArr[1].'</td></tr></table>';
        }
        return $tmpArr[1];
    }
    /**
     * 根据选项分割字符串
     * @param string $str 要分割的字符串
     * @return array|string 可以分割返回数组不能分割返回字符串
     * @author demo
     */
    public function formatStrToArr($str){
        $oldstr=$str;
        $arr=array();
        //提取选项
        $tmpArrNow=preg_match_all('/(A(<i[^m>]*>)?(\.|．).*)/s',$str,$arr);
        //如果没有选项返回原数据
        if(!$tmpArrNow) return $oldstr;
        $str=$arr[1][0]; //从选项开始的数据
        $tmpTime=preg_match_all($this->_regADAll,$str,$arr); //切割选项
        //如果切割失败则返回原始数据
        if(!$tmpTime) return $oldstr;

        $arr[1][0]=preg_replace('/<\/?span[^>]*>|<\/?o[^>]*>/i','',$arr[1][0]);
        //提取第二个字符
        $pos=strpos($arr[1][0],'.',0);
        $pos2=strpos($arr[1][0],'．',0);
        if((is_numeric($pos2) && $pos>$pos2) || !(is_numeric($pos))) $pos=$pos2;
        if(!is_numeric($pos) && !is_numeric($pos2)) return $oldstr;
        $bz=mb_substr($arr[1][0],$pos,1,'UTF-8'); //选项字母后的标志
        $tbz=mb_substr($arr[1][0],0,$pos+1,'UTF-8'); //选项字母后的标志
        $reg[0]="/([A-H](<i[^m>]*>)?(\.|．))/s";
        //提取题文
        $title=R('Common/TestLayer/delMoreTag',array(mb_substr($oldstr,0,mb_strpos($oldstr,$tbz,0,'UTF-8'),'UTF-8')));
        $tmpStr=preg_replace('/(<\/?p[^>]*>|\n|\r|\s|　)*/is','',$title);
        if(empty($tmpStr)) $title='';
        //保存选项
        $keywords = preg_split ($reg[0], $arr[1][0]);
        unset($keywords[0]);
        //不足三个选项则不是选择题
        if(count($keywords)<3) return $oldstr;
        if($keywords){
            $chr=64;
            //$tmpI=0;
            foreach($keywords as $i=>$iKeywords){
                $keywords[$i]=chr($chr+$i).'.'.R('Common/TestLayer/delMoreTag',array($iKeywords));//添加选项abcd 处理多余标记
            }
            $keywords[0]=$title;
            ksort($keywords);
            return $keywords;
        }else
            return $oldstr;
    }
    /**
     * 对选项数据进行排版
     * @param array $keywords 要格式化的选项数组
     * @param array $bl 几列一行 可以为4或2或1
     * @return string
     * @author demo
     */
    public function optionLine($keywords,$bl){
        $len=count($keywords);
        if($bl>2 && $bl==$len-1){
            //一行
            $td=floor(1/($len-1)*100).'%';
            for($i=1;$i<$len;$i++){
                if($i==1){
                    $keywords[$i]="<table width='100%'><tr><td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td>";
                    continue;
                }
                if($i==$len-1){
                    $keywords[$i]="<td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td></tr></table>";
                    continue;
                }
                $keywords[$i]="<td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td>";
            }
        }else if($bl==2){
            //2列
            $td=floor(2/($len-1)*100).'%';
            for($i=1;$i<$len;$i++){
                if($i==1){
                    $keywords[$i]="<table width='100%'><tr><td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td>";
                    continue;
                }
                if($i==$len-1){
                    $keywords[$i]="<td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td></tr></table>";
                    continue;
                }
                if($i%2==0){
                    $keywords[$i]="<td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td></tr>";
                    continue;
                }
                if($i%2==1)
                    $keywords[$i]="<tr><td width='".$td."' valign='middle'><p style='vertical-align:middle'>".$keywords[$i]."</p></td>";

            }
        }else if($bl==1){
            //1行1个
            for($i=1;$i<$len;$i++){
                $keywords[$i]="<p style='vertical-align:middle'>".$keywords[$i]."</p>";
            }
        }
        $output=array();
        $checkTitle=preg_replace('/(\s|　| |\r|\n|&nbsp;)*/i','',$keywords[0]);

        if($checkTitle===''){
            $keywords[0]='';
            $output[0]=0; //选项前没有数据
            $output[1]=implode('',$keywords);
        }else{
            $output[0]=1; //选项前有数据
            $output[1]=implode('',$keywords);
        }
        return $output;
    }


    /**
     * 对下载word中的边框进行替换
     * @author demo
     */
    protected function changeDocTableBorder($str){
        $arr=preg_split('/<table/i',$str);

        if(count($arr)<2) return $str;

        foreach($arr as $i=>$iArr){
            if($i==0) continue;
            $tmp=explode('>',$iArr);
            if(strstr($tmp[0],'border=1')){
                $tmp=preg_split('/<\/table>/is',$iArr);
                $tmp[0]=preg_replace_callback('/(<td[^>]*>)/is',function($matches){return R('Common/TestLayer/addStyleBorder',array($matches));},$tmp[0]);
                $arr[$i]=implode('</table>',$tmp);
            }
        }

        return implode('<table',$arr);
    }
    /**
     * 对答案和解析中带小题的数据进行交叉 内容必须带<span class="quesindex">标记
     * @author demo
     */
    protected function crossAnswerAnalytic($answer,$analytic){
        $tmpAnswer=explode('<span class="quesindex">',$answer);
        $tmpAnalytic=explode('<span class="quesindex">',$analytic);

        $answerShow='';
        if(count($tmpAnswer)<=2){
            $answerShow.=$answer;
            if($analytic) $answerShow.='<p>【解析】'.R('Common/TestLayer/removeLeftTag',array($analytic,'<p>'));
            return $answerShow;
        }
        foreach($tmpAnswer as $i=>$iTmpAnswer){
            if($i==0) continue;
            if($tmpAnalytic[$i]) $tmpAnswer[$i].='【解析】<span class="quesindex">'.$tmpAnalytic[$i];
        }
        $answerShow.=implode('<span class="quesindex">',$tmpAnswer);
        return $answerShow;
    }

    //记录要删除的文件
    public function deleteAllFile($buffer) {
        $data=array();
        $data['FilePath']=$buffer['DocPath'];
        $data['DelTimes']=0;
        $data['AddTime']=time();
        $data['Style']='Doc';
        $this->getModel('DelFile')->insertData(
            $data);
    }
    /**
     * 检查文件word是否可下载
     * @param string $str 处理的字符串
     * @return string 错误返回'error'
     */
    public function checkHtmlPath($docPath,$docID){
        //判断文档是否需要提取
        $output=file_get_contents(C('WLN_DOC_HOST_IN').R('Common/UploadLayer/getDocServerUrl',array($docPath,'wordToHtml','word')));

        if(strstr($output,'error')){
            $data=array();
            $data['description']='文档转换htm失败';
            $data['msg']=$output.'###@###文档路径：'.$docPath;
            $this->addErrorLog($data);
            return '30715'; //文档有误，请检查文档是否正确上传！
        }
        $edit[0]['DocHtmlPath']=$output;

        //修改doc表对应DocHtmlPath DocFilePath
        $data=array();
        $data['DocHtmlPath']=$output;

        $strLs = substr($data['DocHtmlPath'], 0, strrpos($data['DocHtmlPath'], '.'));
        $data['DocFilePath'] = $strLs . '.files';

        //修改htm内绝对路径为相对路径
        $this->changeAllPath($data['DocHtmlPath']);

        if($this->updateData($data,'DocID='.$docID)===false){
            return '30716'; //转换信息保存失败!请重试.
        }
    }

    /**
     * 递归创建路径
     * @param string $path 路径
     * @return Null
     * @author demo
     */
    public function createPath($path){
        mkdir($path,0755,true);
    }


    /**
     * 关于文档下载部分关键字小写转大写
     * @param array $array 数据集
     * @return array
     * @author demo
     */
    public function changeDownFieldUpper($array){
        //设置转换规则
        $field=array(
            'testid'=>'TestID',
            'doctest'=>'DocTest',
            'docanswer'=>'DocAnswer',
            'docanalytic'=>'DocAnalytic',
            'docremark'=>'DocRemark',
            'optionnum'=>'OptionNum',
            'optionwidth'=>'OptionWidth',
            'ifchoose'=>'IfChoose',
            'testnum'=>'TestNum',
            'judge'=>'Judge'
        );
        $newArray=array();
        foreach($array as $i=>$iArray){
            if($field[$i]){
                $newArray[$field[$i]]=$iArray;
            }
        }
        return $newArray;
    }
}
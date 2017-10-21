<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pagename); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/customTest.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/tree.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <script>
    var local='<?php echo U('Index/zsd');?>';
    </script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/unionSearch.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/artTemplate-3.0.3.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/template.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/customMicroList.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ckplayer/ckplayer.js" charset="utf-8"></script>
</head>
<body>
<div id="main">
    <div id="leftdiv" class="ui-resizable">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
            <tbody>
                <tr>
                    <td valign="top" style="border:0;">
                    <div id="category">
                        <div id="categorytop" class='target' val="0" style='cursor: pointer;'>
                        <div class="tit">知识点目录</div>
                        </div>
                        <div id="categorytreebox">
                            <div id="treeContainer">
                            <ul id="treecon"><p class="list_ts" style="color:#fff">数据加载中请稍候...</p>
                            </ul>
                            </div>
                        </div>
                    </div>
                    </td>
                    <td id="bar"/>
                </tr>
            </tbody>
        </table>
        <div class="ui-resizable-handle ui-resizable-e"/>
    </div>
    <div id="rightdiv" style="overflow-y: auto; width: 290px; height: 291px;">
        <div id="righttop">
            <div id="categorylocation"><span class="nowPath">当前位置：</span>> <span id="loca_text"></span></div>
        </div>
        <div id="filterbox"></div>
        <div id="list_px" p='order'>
            <div id="pagediv">共<a id="quescount">？</a>记录
                <a class="prev_page pageTarget" title="上一页" p='page' val='1'></a><span id="pagebox" class="tspan">
                <a id="curpage">？</a>
                <a id="selectpageicon" style="display: inline-block;"></a>
                /<a id="pagecount">？</a></span>
                <a class="next_page pageTarget" title="下一页" p='page' val='1'></a></div>
        </div>
        <div id="queslistbox">
        </div>
        <div id="pagelistbox" p='page'></div>
    </div>
</div>
<script type='text/html' id='template'>
    <% 
        if(typeof(datas) === 'string'){ 
    %>
            <p class="list_ts"><%= datas %></p>
    <%
        }else{
            for(var k in datas[0]){
                var data = datas[0][k];
    %>
                <div id="quesbox<%= data["MID"] %>" class="quesbox">
                    <div class="quesbox_inner">
                        <div class="quesinfobox">
                            <%
                                if(!data['MName']){
                                    data['MName'] = '暂未填写';
                                }
                            %>
                            <div class="quesinfo_tit">微课名称：<span class="questitle"><%= data['MName'] %></span></div>
                            <div>
                                <table cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr>
                                            <td>编号：<%= data['MID'] %>，学科：<%= data['SubjectName'] %>，年级：<%= data['GradeName'] %></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="quesmenu">&nbsp;&nbsp;日期：<%= data['AddTime'] %>&nbsp;&nbsp;</div>
                        </div>
                        <div style="-moz-user-select:none;" oncopy="return false;" onselectstart="return false;" id="quesdiv<%= data['MID'] %>" class="quesdiv">
                            <div class="quesinfo">
                                <p><%=data['Remark']%></p>
                            </div>
                            <div class="quesanswer_tit">知识点</div>
                            <div><%
                                    for(var h in data['KlIDArr']){
                                        %>
                                        <p><%= data['KlIDArr'][h] %></p>
                                        <%
                                    }
                                    %>
                            </div>
                            <div class="quesanswer_tit">听课</div>
                            <div><%
                                    for(var h in data['UrlArr']){
                                        %>
                                        <span class="showMicro pointer" mid="<%= data['MID'] %>" startid="<%= h %>"><%= data['UrlArr'][h] %></span>
                                        <%
                                    }
                                    %>
                            </div>
                        </div>
                        <div class="quesinfobox">
                            <div class="quesother">
                                <a title="修改" class="customTestEdit" mid='<%= data['MID'] %>'></a>
                                <a title="删除" class="deletion"  mid='<%= data['MID'] %>' ></a>
                            </div>
                        </div>
                    </div>
                </div>
    <% 
            }
        }
     %>
</script>
<script type='text/html' id='selectionTemplate'>
    <div class="filterbox_li" p="types">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td width="45">年级</td>
                    <td>
                        <span id="quesgradeselect">
                            <a href="javascript:void(0);" class="button target button_current" val="0">全部</a>
                            <%
                                var data = datas['data'][1];  
                                for(var i in data){ 
                            %>
                                    <a href="javascript:void(0);" class="button target" val="<%=data[i]['GradeID'] %>"><%=data[i]['GradeName'] %></a>
                            <%  } %>
                        </span>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</script>
<script type='text/html' id='treeTemplate'>
    <% 
        for(var i=0;i<datas['data'][3].length;i++){
            var data = datas['data'][3][i];
    %>
        <li><span></span><a href="#" class="zsd treeTarget" p="knowledge" val="<%=data['KlID']%>"><%=data['KlName']%></a>
        <% if(data['sub']){  %>
            <ul>
                <% 
                    for(var j=0;j<data['sub'].length;j++){
                %>
                    <li><span></span><a href="#" class="zsd treeTarget" p="knowledge" val="<%=data['sub'][j]['KlID']%>"><%=data['sub'][j]['KlName']%></a>
                        <% if(data['sub'][j]['sub']){ %>
                            <ul>
                                <% 
                                    for(var k=0;k<data['sub'][j]['sub'].length;k++){
                                %>
                                        <li><span></span><a href="#" class="zsd treeTarget" p="knowledge" val="<%=data['sub'][j]['sub'][k]['KlID']%>"><%=data['sub'][j]['sub'][k]['KlName']%></a></li>
                                <% } %>
                            </ul>
                        <% } %>
                    </li>
                <% } %>
            </ul>
        <% } %>
        </li>
    <% } %>
</script>
<script type="text/javascript">
$.CustomMicroList.init();
</script>

</body>
</html>
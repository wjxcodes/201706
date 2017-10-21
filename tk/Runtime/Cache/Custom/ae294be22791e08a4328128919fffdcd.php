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
    <script type="text/javascript" src="/Public/plugin/unionSearch.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/artTemplate-3.0.3.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/template.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/customTestList.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
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
        <div id="list_px" p='order'><span>排序：</span><a href="#" class="button target button_current" val="def">默认</a><!-- <a href="#" val="hotDesc" class="button target">人气<b></b></a> --><a href="#" val="diffAsc" class="button target">难易度<b></b></a><a href="#" val="saveTimeAsc" class="button target">上传时间<b></b></a>
            <div id="pagediv">共<a id="quescount">？</a>道题
                <a class="prev_page pageTarget" title="上一页" p='page' val='1'></a><span id="pagebox" class="tspan">
                <a id="curpage">？</a>
                <a id="selectpageicon" style="display: inline-block;"></a>
                /<a id="pagecount">？</a></span>
                <a class="next_page pageTarget" title="下一页" p='page' val='1'></a></div>
        </div>
        <div style="font-size:12px;color:#555;padding-left:20px;line-height:22px;"> * 新添加试题将在1分钟后显示，可点击<a href="javascript:location.reload();" style="font-size:12px;color:#0000FF;">刷新</a>查看</div>
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
                <div id="quesbox<%= data["testid"] %>" class="quesbox">
                    <div class="quesbox_inner">
                        <div class="quesinfobox">
                            <%
                                if(!data['docname']){
                                    data['docname'] = '暂未填写';
                                }
                            %>
                            <div class="quesinfo_tit">标题/来源：<span class="questitle"><%= data['docname'] %></span></div>
                            <div>
                                <table cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr>
                                            <td>题号：<%= data['testid'].replace(/^c/, '') %>，题型：<%= data['typesname'] %>，难度：</td>
                                            <td><%= data['diffxing'] %></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="quesmenu">&nbsp;&nbsp;日期：<%= data['firstloadtime'] %>&nbsp;&nbsp;</div>
                        </div>
                        <div style="-moz-user-select:none;" oncopy="return false;" onselectstart="return false;" id="quesdiv<%= data['testid'] %>" class="quesdiv">
                            <div class="quesinfo">
                                <p><%=data['test']%></p>
                            </div>
                            <div show="0" tid="<%= data['testid'] %>" class="quesanswer">
                                <p class="list_ts">
                                    <span class="ico_dd">载入数据请稍候...</span>
                                </p>
                            </div>
                        </div>
                        <div class="quesinfobox">
                            <div class="quesother">
                                <a title="修改试题" class="customTestEdit" testid='<%= data['testid'] %>'></a>
                                <a title="删除试题" class="deletion" childnum='<%= (data['testnum'] == 0 ? 1 : data['testnum']) %>' testid='<%= data['testid'] %>' verify='<%= data['verify'] %>'></a>
                            </div>
                            <% 
                                var css='',classname='addquessel';
                                if(editData.ifhavetest(data['testid'])){
                                    classname='delques';
                                    css='style="display:none;"';
                                }
                                if(data['status'] == 1){
                            %>
                                    <div class="quesmenu">
                                        <a qdname="<%= data['diffname'] %>" qdid="<%= data['diff'] %>" qyisselect="<%= data['typesisselect']%>" qyname="<%= data['typesname'] %>" qyid="<%= data['typesid'] %>" childnum="<%= (data['testnum'] == 0 ? 1 : data['testnum']) %>" quesid="<%= data['testid'] %>" classify="1" class="<%= classname %>" id="quesselect<%= data['testid'] %>"></a><span testid="<%= data['testid'] %>" id="selmore<%= data['testid'] %>" qyid="<%= data['typesid'] %>" childnum="<%= (data['testnum'] == 0 ? 1 : data['testnum']) %>" class="selmore"<%= css %>></span><span id="selpicleft<%= data['testid'] %>" class="selpicleft"<%= css %>></span>
                                    </div>
                            <% }else if(data['status'] == -1){ %>
                                    <div class="quesmenu"><div class="stateoptimizfailure" title='<%= data['ErrorMsg'] %>'>试题优化失败</div></div>
                            <% }else if(data['schedule'] == 0 && data['iflock'] == 0){ %>
                                    <div class="testState">
                                        <div class="stateProgress"></div>
                                        <div class="stateItem">
                                            <span class="stateCurt">试题优化中</span>
                                            <span class="">优化标引中</span>
                                            <span class="">审核中…</span>
                                        </div>
                                    </div>
                            <% }else if(data['schedule'] == 0){ %>
                                <div class="testState">
                                    <div class="stateProgress state2"></div>
                                    <div class="stateItem">
                                        <span class="stateCurt">试题优化中</span>
                                        <span class="stateCurt">优化标引中</span>
                                        <span class="">审核中…</span>
                                    </div>
                                </div>
                            <% }else if(data['schedule'] == 1){ %>
                                <div class="testState">
                                    <div class="stateProgress state3"></div>
                                    <div class="stateItem">
                                        <span class="stateCurt">试题优化中</span>
                                        <span class="stateCurt">优化标引中</span>
                                        <span class="stateCurt">&nbsp;&nbsp;审核中…</span>
                                    </div>
                                </div>
                            <% } %>
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
                    <td width="45">题型</td>
                    <td>
                        <span id="questypeselect">
                            <a href="javascript:void(0);" class="button target button_current" val="0">全部</a>
                            <%
                                var data = datas['data'][1];  
                                for(var i in data){ 
                            %>
                                    <a href="javascript:void(0);" class="button target" val="<%=data[i]['TypesID'] %>"><%=data[i]['TypesName'] %></a>
                            <%  } %>
                        </span>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="filterbox_li" p="diff"  style="background:none">
        <table  border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <td width="65">难度系数</td>
                    <td>
                        <span id="quesdiffselect">
                            <a href="javascript:void(0);" class="button target button_current" val="0">全部</a>
                            <% var data = datas['data'][2]; %>
                            <% for(var i in data){ %>
                                <a href="javascript:void(0);" class="button target" val="<%=data[i]['DiffID']%>" title="<%=data[i]['DiffArea']%>"><%=data[i]['DiffName']%></a>
                            <% } %>
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
<script type='text/html' id='originalityTemplate'>
    <div class="submission-content">
        <table class="g-table g-table-bordered submission-table">
            <thead>
            <tr>
                <th width="30">题号</th>
                <th width="80">题型</th>
                <th>主干知识点</th>
                <th width="60">分值</th>
                <th width="75">正答率预估</th>
                <th width="90">加入原创</th>
            </tr>
            </thead>
            <tbody>
            {{each testTemplate as value i}}
            <tr>
                <td><span class="blue">{{#value.testNum}}</span></td>
                <td>{{#value.typeName}}</td>
                <td>{{#value.knowledge.klName}}</td>
                <td>{{#value.score}}</td>
                <td>{{#value.rightPercent}}</td>
                <td class="join">
                    <p class="f-yahei join-num">
                        已有
                        <span class="blue">{{value.userNum.have}}</span>
                        人参与<br />
                        余
                        <span class="red">{{value.userNum.leave}}</span>
                        名席位
                    </p>
                    <p class="f-yahei join-check">
                        <label class="g-radio" for="ttID{{value.ttID}}">
                            <input type="radio" {{if value.userNum.leave==0}}disabled{{/if}}
                                  value="{{value.ttID}}" data-testID="{{testID}}" class="ttID" name="ttID" id="ttID{{value.ttID}}" />
                            加入投稿
                        </label>
                    </p>
                </td>
            </tr>
            {{/each}}
            </tbody>
        </table>
    </div>
</script>
<script type="text/javascript">
$.CustomTestList.init();
</script>

</body>
</html>
<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title><?php echo ($pageName); ?> - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <link type="text/css" href="/Public/default/css/teachPlan.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/template.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.config.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/ueditor/ueditor.all.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/plugin/editor.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
</head>
<body>
    <div id="caseDiv">
        <div class="PublicBox" id='loreBox'>
            <div class="righttop">
                <div id="categorylocation">
                    <span class="newPath">当前位置：</span>
                    >
                    <span id="loca_text">
                        <span></span>
                        > 高效同步课堂 > 知识管理
                    </span>
                </div>
            </div>

            <div class="PublicBoxnr">
                <div class="Publictitle">
                    <h3 class="addTit">知识管理</h3>
                    <div class="addDes">
                        <a class="selectright" id='addLore'>添加知识</a>
                    </div>
                </div>

                <div class="mylorebox">
                    <form id='queryForm'>
                        <table class="mylore_table hasTable">
                            <thead>
                                <div class="tabletitbox loreSearch">
                                    所在板块：
                                    <label for="select"></label>
                                    <select name="module" id='moduleSelectTag'>
                                        <option value=''>请选择</option>
                                    </select>
                                    筛选栏目：
                                    <label for="select"></label>
                                    <select name="menu" id='menuSelectTag'>
                                        <option value=''>请先选择板块</option>
                                    </select>
                                    筛选章节：
                                    <select name="chapter" class="chapterSelectTag" id='chapterRootSelect'>
                                        <option value=''>请选择</option>
                                    </select>
                                    <a class="td_btn" id='searchLore'>查找</a>
                                </div>
                                <tr class="tr">
                                    <th class="th loreItem1">内容</th>
                                    <th class="th loreItem2">答案</th>
                                    <th class="th loreItem3">学科</th>
                                    <th class="th loreItem4">课时</th>
                                    <th class="th loreItem5">栏目</th>
                                    <th class="th loreItem6">存档时间</th>
                                    <th class="th loreItem7">操作</th>
                                </tr>
                            </thead>
                            <tbody id='list'>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div id="pagelistbox">
                </div>
            </div>
        </div>
    </div>
    <!-- 知识列表模板 -->
    <script type='text/html' id='loreList'>
        <% 
            if(num > 0){
              for(var data in datas){ 
                data = datas[data];
        %>
                <tr class="tr">
                    <td class="loreItem1 td loreConParent"><div class="loreCon"><%= data.Lore %></div></td>
                    <td class="loreItem2 textleft td loreConParent"><div class="loreCon"><%= data.Answer %></div></td>
                    <td class="loreItem3 td"><%= data.SubjectName %></td>
                    <td align="left" class="loreItem4 textleft td"><%= data.ChapterName %></td>
                    <% 
                        var menuName = '暂未分栏目';
                        if($.MyLoreManager.menus[data.ForumID]){
                            var forum = $.MyLoreManager.menus[data.ForumID];
                            if(forum['content']){
                                var content = forum['content'];
                                for(var i=0; i<content.length; i++){
                                    if(content[i]['MenuID'] == data.MenuID){
                                        menuName = content[i]['MenuName'];
                                    }
                                }
                            }
                        } 
                    %>
                    <td class="loreItem5 td"><%= menuName %></td>
                    <td class="loreItem6 td"><%= data.AddTime %></td>
                    <td class="loreItem7 lastOperate td" loreid='<%= data.LoreID %>'>
                        <span class="showDetail">查看详细</span>
                        <span class="editContent">修改</span>
                        <span class="deletion" >删除</span>
                    </td>
                </tr>
        <% 
                }
            }else{
         %>
                <tr class="tr"><td class="td" style="text-align:center" colspan='7'>暂无相关知识！</td></tr>
         <% } %>
    </script>
    <!-- 查看内容模板 -->
    <script type='text/html' id='loreContentShow'>
        <%
            //控制table相关元素的长度
            //var tableWidth = $.MyLoreManager.widthProperties.dialog - 100;
            //var leftTd = 80;
            //var tdWidth = tableWidth - leftTd;
        %>
        <div class="loreContentShowCon">
                <table width="100%" border="0" cellspacing="0" class="loreContentTable">
                    <tbody>
                        <tr class="tr">
                            <td align="center" class="td1"> <b>内容</b>
                            </td>
                            <td class="td2">
                            <div style="overflow:auto;"><%= datas['Lore'] %></div>
                            </td>
                        </tr>
                        <tr class="tr">
                            <td align="center" class="td1">
                                <b>答案</b>
                            </td>
                            <td class="td2"><div style="overflow:auto;"><%= datas['Answer'] %></div></td>
                        </tr>
                        <tr class="tr">
                            <td align="center" class="td1">
                                <b>学科</b>
                            </td>
                            <td class="td2">
                                <%= datas['SubjectName'] %>
                                <br>
                            </td>
                        </tr>
                        <tr class="tr">
                            <td align="center" class="td1">
                                <b>章节</b>
                            </td>
                            <td class="td2"><%= datas['ChapterName'] %></td>
                        </tr>
                        <tr class="tr">
                            <td align="center" class="td1">
                                <b>栏目</b>
                            </td>
                            <% 
                                var menuName = '暂未分栏目';
                                if($.MyLoreManager.menus[datas['ForumID']]){
                                    var content = $.MyLoreManager.menus[datas['ForumID']]['content'];
                                    for(var i=0; i<content.length; i++){
                                        if(content[i]['MenuID'] == datas.MenuID){
                                            menuName = content[i]['MenuName'];
                                        }
                                    }
                                }
                             %>
                            <td class="td2"><%= menuName %></td>
                        </tr>
                    </tbody>
                </table>
        </div>
        <div class="normal_btn"><span class="normal_yes bgbt an01" only="1"><span class="an_left"></span><a>修改</a><span class="an_right"></span></span><span class="normal_no bgbt an02" idname="addworkdiv"><span class="an_left"></span><a>取消</a><span class="an_right"></span></span></div>
    </script>
    <!-- 编辑内容模板 -->
    <script type='text/html' id='editContent'>
        <div class="loreMsgCon">
        <form action='<?php echo U("Case/saveLore");?>' method='post' id='saveForm'>
            <input type='hidden' name='LoreID' value='<%= datas["LoreID"] %>'/>
            <div class="popupbox">
                    <div class="popuptit">所在板块</div>
                    <div class="popupnr">
                        <label for="select"></label>
                        <select id='showModuleMenu'>
                            <option value=''>请选择</option>
                        </select>
                    </div>
                    <div class="popuptit">选择栏目</div>
                    <div class="popupnr">
                        <label for="select"></label>
                        <select name="MenuID" id='showContentMenu'>
                            <option value=''>请先选择板块</option>
                        </select>   
                    </div>
                    <div class="popuptit">章节选择</div>
                    <div class="popupnr">
                        <label for="select2"></label>
                        <select class='chapterSelectTag' id="loadContentSelect">
                            <option value=''>请选择</option>
                        </select>
                        <div id='showChapterZone' style="clear:both;margin-top:10px;"><%= datas['showChapterZone'] %></div>
                        <input type='hidden' id='chapterRealValue' name='ChapterID' value='<%= datas['ChapterID'] %>'>
                    </div>
                    <div class="popuptit">知识内容</div>
                    <div class="popupnr loreEditor"></div>
                    <div class="popuptit">知识答案</div>
                    <div class="popupnr anwserEditor"></div>
                </div>
        </form>
        </div>
        <div class="normal_btn"><span id="saveLore" class="normal_yes bgbt an01" only="1"><span class="an_left"></span><a>保存</a><span class="an_right"></span></span><span id="cancelSave" class="normal_no bgbt an02" idname="addworkdiv"><span  class="an_left"></span><a>取消</a><span class="an_right"></span></span></div>
    </script>
    <script type="text/javascript" src="/Public/default/js/myLore.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type='text/javascript'>
        $.MyLoreManager.init('/Guide/Case', <?php echo ($chapter); ?>, <?php echo ($menusJSON); ?>, <?php echo ($modulesJSON); ?>);
    </script>
</body>
</html>
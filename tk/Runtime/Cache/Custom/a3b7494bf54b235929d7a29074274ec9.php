<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
        <title> - 组卷系统 - 智慧云题库云平台</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="keywords" content="组卷,题库">
        <meta name="description" content="组卷,题库">
        <link type="text/css" href="/Public/default/css/common1.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet">
        <link type="text/css" href="/Public/default/css/customTest.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" rel="stylesheet">
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/Public/plugin/tips.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/common.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/customTest.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript">
        $.customeNav = {
            init:function(){
                this.initPage();
                this.showTip();
            },
            showTip:function(){
                $.CustomTestStore.showTip('您当前正在编辑协同命制试题,请选择原创试题上传方式！');
            },
            initPage:function(){
                var height = $(window).height() - 50;
                $("#divbox").css({ 'height': height-5 ,'overflow-y':'auto','overflow-x':'hidden'});
                //上传方式-鼠标滑过效果
                $(".upload-box").on({
                    mouseenter: function () {
                        var $this = $(this);
                        $this.find(".upload-btn").show();
                        $this.find(".ua-context").hide();
                    },
                    mouseleave: function () {
                        var $this = $(this);
                        $this.find(".upload-btn").hide();
                        $this.find(".ua-context").show();
                    }
                })
            }
        };
        $(document).ready(function(){
            $.customeNav.init();
        });
    </script>
    </head>
    <body>
        <div id="righttop">
            <div id="categorylocation">
                <span class="nowPath">当前位置：</span>&gt; <span id="loca_text"> <a href="<?php echo U('Custom/CustomTestStore/index');?>">校本题库</a></span>
            </div>
            </div>
            <div class="custom-content" id="divbox">
                <table class="upload-table-layout">
                    <tbody>
                    <tr>
                        <td class="upload-area-warp">
                            <div class="upload-box">
                                <div class="icon icon-xie"></div>
                                <h3 class="upload-title">单题上传</h3>
                                <div
                                        class="upload-btn"><a class="nor-btn" href="<?php echo U('Custom/CustomTestStore/add');?>">立即上传</a></div>
                                <div class="ua-context">不拼数量拼质量，好的试题要收藏</div>
                            </div></td>
                        <td class="upload-area-warp"> <div class="upload-box">
                            <div class="icon icon-paizhao"></div>
                            <h3 class="upload-title">图片拍照上传</h3>
                            <div class="upload-btn"><a class="nor-btn" href="<?php echo U('Custom/CustomTestStore/photograph');?>">立即上传</a></div>
                            <div class="ua-context">懒得打字不要紧，轻松拍照快速传</div>
                        </div></td>
                        <td class="upload-area-warp"><div class="upload-box">
                            <div class="icon icon-doc"></div>
                            <h3 class="upload-title">文档上传</h3>
                            <div
                                    class="upload-btn"><a class="nor-btn" href="<?php echo U('Custom/CustomTestStore/docList');?>">立即上传</a></div>
                            <div class="ua-context">单题入库太麻烦？一键上传最省心</div>
                        </div></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </body></html>
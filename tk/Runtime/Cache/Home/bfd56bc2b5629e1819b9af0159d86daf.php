<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="cn">
<head>
    <title>配置答题卡 - <?php echo ($config["IndexName"]); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?php echo ($config["Keyword"]); ?>" />
    <meta name="description" content="<?php echo ($config["Description"]); ?>" />
<!--/*    <script src="/static/plugin/jquery-1.11.1.min.js"></script>-->
    <!--<link rel="stylesheet" href="../../css/card.css">-->
    <!--<link rel="stylesheet" href="/static/plugin/layer/skin/layer.css">-->
    <!--<script src="/static/plugin/layer/layer.js"></script>-->
    <!--<script src="/static/plugin/vue/vue.js"></script>-->
    <!--<script src="/static/plugin/sea.js"></script>*/-->
    <link rel="stylesheet" href="/Public/default/css/card.css<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>" />
    <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
    <link rel="stylesheet" href="/Public/plugin/layer/skin/layer.css" />
    <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/plugin/sea.js"></script>
    <script type="text/javascript" src="/Public/plugin/vue/vue.js"></script>
    <script src="https://cdn.bootcss.com/lodash.js/4.17.4/lodash.js"></script>
    <!--[if lte IE 6]>
    <script type="text/javascript" src="/Public/plugin/png.js"></script>
    <script>DD_belatedPNG.fix('a,img,div,span');</script>
    <![endif]-->
    <!--<script type="text/javascript" src="/Public/plugin/ajax_uploadimage/jquery.wallform.js"></script>-->
    <link rel="stylesheet" type="text/css" href="/Public/plugin/webuploader/webuploader.css">
    <script type="text/javascript" src="/Public/default/js/common<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>
    <script type="text/javascript" src="/Public/default/js/paper<?php echo (C("WLN_PICK_JS")); ?>.js<?php echo (C("WLN_UPDATE_FILE_DATE")); ?>"></script>


    <script>
        var local='<?php echo U('Index/answer');?>';
        var school='<?php echo ($school); ?>';
        
        Vue.component('modal', {
            template: '#modal-template'
        });
    </script>
</head>

<body>
<!--root模版-->
<div class="is-old-ie" style="display: none;">
    提示：您可以使用以下浏览器：<a href="https://www.baidu.com/s?ie=UTF-8&wd=%E8%B0%B7%E6%AD%8C%E6%B5%8F%E8%A7%88%E5%99%A8" title="下载谷歌浏览器">谷歌浏览器</a>
    <a href="http://www.firefox.com.cn/" title="下载火狐浏览器">火狐浏览器</a>
    <a href="http://chrome.360.cn/" title="下载360极速浏览器">360极速浏览器</a>
</div>
<div id="app">
</div>

<div id="tpl-source" style="display:none">
    <!--弹出框-公共组件-->
     <script type="text/x-template" id="modal-template">
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container" style="max-height:700px">

                        <div class="modal-header">
                            <slot name="header">
                                信息提示
                            </slot>
                        </div>

                        <div class="modal-body">
                            <slot name="body">
                                default body
                            </slot>
                        </div>

                        <div class="modal-footer">
                            <slot name="footer">
                                footer
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
</script>
<script>
    Vue.component('modal', {
        template: '#modal-template'
    });
</script>

    <!--分卷-->
    <!--alert-part-info-->
    <script type="text/html" id="tpl-alert-part-info">
    <modal v-show="modal.state_alertPartInfo">
        <h3 slot="header">
            编辑分卷信息
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form">
                <div class="item-content" id="partInfoSet">
                    <div class="itm-item partInfoItem" v-for="(partTitleItem,partTitleItemIndex) in examPart">
                        <div>
                            <span class="item-name">{{String.fromCharCode(8544+partTitleItemIndex)}} 卷信息：</span>
                            <input class="w-full" type="text" v-bind:name="'part-title-'+partTitleItemIndex" v-bind:value="partTitleItem.title">
                        </div>
                        <div style="margin-top:6px">
                            <span class="item-name">{{String.fromCharCode(8544+partTitleItemIndex)}} 卷描述：</span>
                            <input class="w-full" type="text" v-bind:name="'part-desc-'+partTitleItemIndex" v-bind:value="partTitleItem.desc">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>



    <!--left-con-->
    <!--<link rel="import" href="../include/paper-style.tpl?__inline"/>-->
    <script type="text/html" id="tpl-paper-style">
    <div class="tablayout" id="tablayout-1">
        <div class="box-warp" v-bind:class="{'box-a4':layout.style==null || layout.style=='A4','box-a3':layout.style=='A3'}">
            <!--=====================切割标记=====================-->
            <ul class="black-box">
                <li class="black-mark mark1">
                    <span class="triangle"></span>
                    <span class="square"></span>
                </li>
                <li class="black-mark mark2">
                    <span class="square"></span>
                    <span class="triangle"></span>
                </li>
                <li class="black-mark mark3">
                    <span class="triangle"></span>
                    <span class="square"></span>
                </li>
                <li class="black-mark mark4">
                    <span class="square"></span>
                    <span class="triangle"></span>
                </li>
            </ul>
            <!--=====================切割标记=====================-->

            <div class="con-height">
                <!--=====================头部信息=====================-->
                <main-top :title="title"></main-top>
                <!--=====================头部信息=====================-->

                <!--=====================考试信息=====================-->
                <exam-info-a4
                        :data="data"
                        :care="care"
                        :type="type"
                        v-if="layout.style=='A4'"></exam-info-a4>
                <exam-info-a3
                        :data="data"
                        :care="care"
                        :type="type"
                        v-if="layout.style=='A3'"></exam-info-a3>
                <!--=====================考试信息=====================-->

                <!--====================================================================================================
                ================================================分卷====================================================
                ====================================================================================================-->
                <div class="part-wrap">

                    <template v-for="(partItem,partItemIndex) in examPart" v-if="examPart != null">

                        <!--<template v-if="partItem && partItemIndex==0">-->
                            <!--<p class="text-muted center part-box" style="padding:100px 0;margin-top:8px;">尚未添加试题</p>-->
                        <!--</template>-->

                        <template v-if="partItem.list.length>0">
                            <!--分卷标题设置============-->
                            <div class="part-title center red hover">
                                <div class="part-menu">
                                    <a href="javascript:;" class="btn"
                                       @click="showAlertEditPartTitle(partItemIndex)">
                                        <i class="iconfont">&#xe601;</i>编辑
                                    </a>
                                </div>
                                <p class="partname1">{{partItem.title}}&nbsp;<small>{{partItem.desc}}</small></p>
                            </div>
                            <!--分卷标题设置-END============-->

                            <!--class fillin-->  <!--height-max a3高度  height-max-->

                            <div class="part-box">

                                <div v-for="(typeItem,typeItemIndex) in partItem.list">


                                    <!--=====================客观题=====================-->
                                    <!--typeItem.type==0-->
                                    <div class="choice hover ques-type ques-type0"
                                         v-bind:id="'ques-anchor'+partItemIndex+'-'+typeItemIndex"
                                         v-if="typeItem.type==0 && typeItem.content.length>0">
                                        <div class="part-menu">
                                            <a href="javascript:;" class="btn"
                                               @click="showAlertXuanzeti(partItemIndex,typeItemIndex)">
                                                <i class="iconfont">&#xe602;</i>添加
                                            </a>
                                            <a href="javascript:;" class="btn"
                                               @click="changeTypeItemStyle(partItemIndex,typeItemIndex)">
                                                <i class="iconfont">&#xe60b;</i>切换版式
                                            </a>
                                            <a href="javascript:;"
   class="btn"
   v-on:click="delThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe600;</i>删除
</a>
<a v-show="typeItemIndex != partItem.list.length-1"
   href="javascript:;"
   class="btn"
   @click="moveDownThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe605;</i>下移
</a>
<a v-show="typeItemIndex !=0"
   href="javascript:;"
   class="btn"
   @click="moveUpThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe604;</i>上移
</a>
                                        </div>
                                        <div class="q-choice clearfix">

                                            <!--选择题块模版-->
                                            <!--分组显示 每组不超过5个-->
                                            <div class="choice-item"
                                                 v-for="(itemBlock,itemBlockIndex) in Math.ceil(typeItem.content.length/5)"
                                                 v-bind:class="(typeItem.style==1?'vertical-choice':'')">

                                                <!--只显示当前分组-->
                                                <div v-for="(itemTr,itemTrIndex) in typeItem.content"
                                                     class="order-item" v-if="(itemTrIndex+1)>(itemBlockIndex*5) && (itemTrIndex+1) <= ((itemBlockIndex+1)*5)"
                                                     v-bind:card-ques-id="itemTr.order+'.'+itemTr.small">
                                                    <span class="item-number">{{itemTr.order}}</span>

                                                    <!--大写字母-->
                                                    <template v-if="itemTr.style==1">
                                                        <span v-for="(itemTd,indexId) in itemTr.num-0"
                                                              class="item-option">[<b class="pad4">{{quesTemp.choice[indexId]}}</b>]</span>
                                                    </template>

                                                    <!--小写字母-->
                                                    <template v-if="itemTr.style==2">
                                                        <span  v-for="(itemTd,indexId) in itemTr.num-0" class="item-option">[<b class="pad4">{{quesTemp.choice[indexId].toLowerCase()}}</b>]</span>
                                                    </template>

                                                    <!--判断题-->
                                                    <template v-if="itemTr.style==3" >
                                                        <span v-for="(itemTd,indexId) in 2"
                                                              class="item-option">[<b class="pad4">{{quesTemp.trueFalse[indexId]}}</b>]</span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--=====================客观题-END=====================-->



                                    <!--=========================解答题模版==========================-->
                                    <div class="ques-type ques-type1"
                                         v-bind:id="'ques-anchor'+partItemIndex+'-'+typeItemIndex"
                                         v-if="typeItem.type==1 && typeItem.content.length>0">
                                        <div class="question explain hover">
                                            <div class="part-menu">
    <a href="javascript:"
       class="btn"
       v-on:click="showAlertEditQuesTitle(partItemIndex,typeItemIndex)">
        <i class="iconfont">&#xe601;</i>编辑
    </a>
    <a href="javascript:"
       class="btn"
       v-on:click="showAlertJiedati(partItemIndex,typeItemIndex)">
        <i class="iconfont">&#xe602;</i>添加
    </a>
    <a href="javascript:"
       class="btn"
       v-on:click="delThisQues(partItemIndex,typeItemIndex)">
        <i class="iconfont">&#xe600;</i>删除
    </a>
    <a v-show="typeItemIndex != partItem.list.length-1"
       href="javascript:"
       class="btn"
       v-on:click="moveDownThisQues(partItemIndex,typeItemIndex)">
        <i class="iconfont">&#xe605;</i>下移
    </a>
    <a v-show="typeItemIndex !=0"
       href="javascript:"
       class="btn"
       v-on:click="moveUpThisQues(partItemIndex,typeItemIndex)">
        <i class="iconfont">&#xe604;</i>上移
    </a>
</div>


                                            <div class="q-title">
                                                <ques-title-info v-bind:item="typeItem"></ques-title-info>
                                            </div>
                                            <div class="q-content">
                                                <div class="jd-kong-list clearfix">
                                                    <jd-kong-template
                                                            v-for="(contentItem , subOrderID) in typeItem.content"
                                                            v-bind:itemLength="typeItem.content.length"
                                                            v-bind:score="score"
                                                            v-bind:contentItem="contentItem"
                                                            v-bind:partItemIndex="partItemIndex"
                                                            v-bind:typeItemIndex="typeItemIndex"
                                                            v-bind:subOrderID="subOrderID"
                                                    ></jd-kong-template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!--解答题模版-END-->


                                    <!--选答题模版-->
                                    <template v-if="typeItem.type==2 && typeItem.content.length>0">
                                        <xuandati-template
                                                v-bind:dataState="dataState"
                                                v-bind:score="score"
                                                v-bind:partItem="partItem"
                                                v-bind:partItemIndex="partItemIndex"
                                                v-bind:typeItemIndex="typeItemIndex"
                                                v-bind:typeItem="typeItem">
                                        </xuandati-template>
                                    </template>
                                    <!--选答题模版-END-->


                                    <!-- 语文作文 -->
                                    <template v-if="typeItem.type==3 && typeItem.content.length>0">
                                        <div class="question ques-type composition hover"
                                             v-bind:id="'ques-anchor'+partItemIndex+'-'+typeItemIndex">
                                            <div class="part-menu">
                                                <a href="javascript:;" class="btn" @click="showAlertEditCnZuowen(partItemIndex,typeItemIndex)">
                                                    <i class="iconfont">&#xe601;</i>编辑
                                                </a>
                                                <a href="javascript:;"
   class="btn"
   v-on:click="delThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe600;</i>删除
</a>
<a v-show="typeItemIndex != partItem.list.length-1"
   href="javascript:;"
   class="btn"
   @click="moveDownThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe605;</i>下移
</a>
<a v-show="typeItemIndex !=0"
   href="javascript:;"
   class="btn"
   @click="moveUpThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe604;</i>上移
</a>
                                            </div>
                                            <div class="q-title">
                                                <ques-title-info v-bind:item="typeItem"></ques-title-info>
                                            </div>
                                            <div class="q-content">

                                                <div class="content-list">
                                                    <template v-for="(contentItem , subOrderID) in typeItem.content">
                                                        <span class="number" v-bind:card-ques-id="contentItem.order">{{contentItem.order}}.
                                                            <em class="score" v-if="score.display == 1">({{contentItem.score}}分)</em>
                                                        </span>

                                                        <!--if desc-->
                                                        <div class="en-zuowen-desc" style="word-wrap: break-word" v-if="contentItem.desc != null && contentItem.desc.length>0">{{contentItem.desc}}</div>

                                                        <!--if img-->
                                                        <div class="en-zuowen-img" v-if="contentItem.img != null && contentItem.img.length>0">
                                                            <span class="jd-img-list big-block" style="display:block;">
                                                                <span class="img-item-cell" style="display:block;text-align: right; margin-top:10px;"
                                                                      v-for="(imgItem,imgItemIndex) in contentItem.img">
                                                                    <span class="img-item">
                                                                        <img v-bind:src="imgItem[0]" v-bind:alt="contentItem.order">
                                                                    </span>
                                                                </span>
                                                            </span>
                                                        </div>

                                                        <div class="com-title center">题目：________________________________________</div>
                                                        <div class="com-grid clearfix">
                                                            <span v-for="(charItem,charItemIndex) in Math.floor((contentItem.char-0)*(1.3))">
                                                                <em class="tips"
                                                                    v-if="((charItemIndex+1)==contentItem.char ||
                                                                    (charItemIndex+1)==contentItem.char-100 ||
                                                                    (charItemIndex+1)==contentItem.char+100)">{{charItemIndex+1}}</em>
                                                            </span>
                                                            <!--A4版格数补整行-->
                                                            <template v-if="(Math.floor((contentItem.char-0)*(1.3)))%24 !== 0 && layout.style=='A4'">
                                                                <span v-for="charItem1 in (24-(Math.floor((contentItem.char-0)*(1.3)))%24)"></span>
                                                            </template>
                                                            <!--A3版格数补整行-->
                                                            <template v-if="(Math.floor((contentItem.char-0)*(1.3)))%16 !== 0 && layout.style=='A3'">
                                                                <span v-for="charItem1 in (16-(Math.floor((contentItem.char-0)*(1.3)))%16)"></span>
                                                            </template>
                                                        </div>

                                                    </template>
                                                </div>
                                                <!-- 禁止作答区 -->
                                                <!--<div class="no-writing" v-if="noWritable.display">禁止作答区</div>-->
                                            </div>
                                        </div>
                                    </template>
                                    <!--语文作文-END-->


                                    <!--英语作文-->
                                    <template v-if="typeItem.type==4 && typeItem.content.length>0">
                                        <div class="question writing-En ques-type hover"
                                             v-bind:id="'ques-anchor'+partItemIndex+'-'+typeItemIndex">
                                            <div class="part-menu">
                                                <!--<a href="javascript:;" class="btn" @click="showAlertEnZuowen">-->
                                                <!--<i class="iconfont">&#xe602;</i>添加-->
                                                <!--</a>-->
                                                <a href="javascript:;" class="btn" @click="showAlertEditEnZuowen(partItemIndex,typeItemIndex)">
                                                    <i class="iconfont">&#xe601;</i>编辑
                                                </a>
                                                <a href="javascript:;"
   class="btn"
   v-on:click="delThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe600;</i>删除
</a>
<a v-show="typeItemIndex != partItem.list.length-1"
   href="javascript:;"
   class="btn"
   @click="moveDownThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe605;</i>下移
</a>
<a v-show="typeItemIndex !=0"
   href="javascript:;"
   class="btn"
   @click="moveUpThisQues(partItemIndex,typeItemIndex)">
    <i class="iconfont">&#xe604;</i>上移
</a>
                                            </div>
                                            <div class="q-title">
                                                <ques-title-info v-bind:item="typeItem"></ques-title-info>
                                            </div>
                                            <div class="q-content">
                                                <div class="content-list clearfix">
                                                    <template v-for="(contentItem , contentItemIndex) in typeItem.content">

                                                        <span class="number" v-bind:card-ques-id="contentItem.order">
                                                            {{contentItem.order}}.
                                                            <em class="score" v-if="score.display==1">({{contentItem.score}}分)</em>
                                                        </span>

                                                        <!--if desc-->
                                                        <div class="en-zuowen-desc" style="word-wrap: break-word" v-if="contentItem.desc != null && contentItem.desc.length>0">{{contentItem.desc}}</div>

                                                        <!--if img-->
                                                        <div class="en-zuowen-img" v-if="contentItem.img != null && contentItem.img.length>0">
                                                            <span class="jd-img-list big-block" style="display:block;">
                                                                <span class="img-item-cell" style="display:block;text-align: right; margin-top:10px;"
                                                                      v-for="(imgItem,imgItemIndex) in contentItem.img">
                                                                    <span class="img-item">
                                                                        <img v-bind:src="imgItem[0]" v-bind:alt="contentItem.order">
                                                                    </span>
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <span class="line widthF left"
                                                              v-bind:style="{'borderBottom':(contentItem.uline==1?'1px solid #333':'none')}"
                                                              v-for="lineItem in (contentItem.hline-0)">
                                                        </span>
                                                    </template>

                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!--英语作文-END-->
                                </div>
                            </div>


                        </template>
                    </template>




                    <div class="part-tips center red">请在各题目答题区域作答，超出黑色矩形边框限定区域答题无效</div>
                </div>
                <!--====================================================================================================
                ================================================分卷====================================================
                ====================================================================================================-->
                <!--<div class="page red center"></div>-->
            </div>
        </div>
    </div>
</script>

<!--解答题-->
<script type="text/html" id="tpl-jd-kong-template">

    <div class="jd-kong-list-item">
        <template v-if="!isENError">
            <div class="jd-hang-item clearfix"
                 v-on:mouseenter="hoverThisQues(1)"
                 v-on:mouseleave="hoverThisQues(0)"
                 v-bind:class="{'jd-hang-p10':isQuesBlock,'jd-hang-npb':isContentLastQuesBlock}"
                 v-bind:card-ques-id="cardQuesId">
                
<div class="J_ques-handle-group" v-if="hoverThis===1">
    <div class="J_layer-box"></div>
    <div class="ques-handle-group">
        <a href="javascript:;"
           class="btn"
           v-on:click="showAlertEditJiedati(partItemIndex,typeItemIndex,subOrderID)">
            <i class="iconfont">&#xe601;</i>编辑
        </a>
        <a href="javascript:;"
           class="btn"
           v-on:click="delThisQues(partItemIndex,typeItemIndex,subOrderID)">
            <i class="iconfont">&#xe600;</i>删除
        </a>
        <a v-show="subOrderID != itemLength-1"
        href="javascript:;"
        class="btn"
        @click="moveDownThisQues(partItemIndex,typeItemIndex,subOrderID)">
        <i class="iconfont">&#xe605;</i>下移
        </a>
        <a v-show="subOrderID !=0"
        href="javascript:;"
        class="btn"
        @click="moveUpThisQues(partItemIndex,typeItemIndex,subOrderID)">
        <i class="iconfont">&#xe604;</i>上移
        </a>
    </div>
</div>

                <!--if no img-->
                <span class="jd-kong-item"
                      v-for="kongItem in kongItemLen"
                      v-if="!isImg"
                      v-bind:class="isNoImgClass">
                    <em class="jd-order" v-bind:class="{'jd-jdt':isQuesBlock}">
                        <span v-if="kongItem==1 && contentItem.small<=1">{{contentItem.order}}.
                            <span v-if="score.display==1"> ({{contentItem.score}}分)</span>
                        </span>
                    </em>

                    <em class="jd-sub" v-if="kongItem==1 && isSmall">({{contentItem.small}})</em>

                    <ques-desc v-bind:contentItem="contentItem" v-if="kongItem==1"></ques-desc>

                    <span class="jdt-block-line" v-if="isShowQuesBlockLine && kongItem==1"></span>
                </span>

                <!--if img-->
                <span class="jd-kong-item hang-has-img no-line"
                      v-bind:style="hangHasImgStyle"
                      v-bind:class="isImgClass"
                      v-if="isImg">
                    <em class="jd-order" v-bind:class="{'jd-jdt':isQuesBlock}">
                        <span>{{contentItem.order}}.<span v-if="score.display==1"> ({{contentItem.score}}分)</span></span>
                    </em>
                    <em class="jd-sub" v-if="isSmall">({{contentItem.small}})</em>

                    <ques-desc v-bind:contentItem="contentItem"></ques-desc>

                    <span class="jd-img-list big-block">
    <span class="img-item-cell"
          v-for="(imgItem,imgItemIndex) in contentItem.img">
        <span class="img-item">
            <!--<a href="javascript:;" class="del-img" v-on:click="delThisQuesImg(partItemIndex,typeItemIndex,subOrderID,imgItemIndex)">删除</a>-->
            <img v-bind:src="imgItem[0]" v-bind:alt="contentItem.order">
            <!--<br>-->
            <!--<span class="img-item-desc" v-if="imgItem[1]">{{imgItem[1]}}</span>-->
        </span>
    </span>
</span>

                    <span class="jdt-block-line" v-if="isShowQuesBlockLine"></span>
                </span>
            </div>
        </template>

        <!--短文改错-->
        <template v-if="isENError">
            <div class="jd-hang-item clearfix"
                 v-bind:card-ques-id="cardQuesId"
                 v-on:mouseenter="hoverThisQues(1)"
                 v-on:mouseleave="hoverThisQues(0)">
                
<div class="J_ques-handle-group" v-if="hoverThis===1">
    <div class="J_layer-box"></div>
    <div class="ques-handle-group">
        <a href="javascript:;"
           class="btn"
           v-on:click="showAlertEditJiedati(partItemIndex,typeItemIndex,subOrderID)">
            <i class="iconfont">&#xe601;</i>编辑
        </a>
        <a href="javascript:;"
           class="btn"
           v-on:click="delThisQues(partItemIndex,typeItemIndex,subOrderID)">
            <i class="iconfont">&#xe600;</i>删除
        </a>
        <a v-show="subOrderID != itemLength-1"
        href="javascript:;"
        class="btn"
        @click="moveDownThisQues(partItemIndex,typeItemIndex,subOrderID)">
        <i class="iconfont">&#xe605;</i>下移
        </a>
        <a v-show="subOrderID !=0"
        href="javascript:;"
        class="btn"
        @click="moveUpThisQues(partItemIndex,typeItemIndex,subOrderID)">
        <i class="iconfont">&#xe604;</i>上移
        </a>
    </div>
</div>
                <span style="height:auto;">
                <em class="jd-order">
                    <span v-if="contentItem.small==1 || contentItem.small==0">{{contentItem.order}}.
                    <span v-if="score.display==1"> ({{contentItem.score}}分)</span></span>
                </em>
                <em class="jd-sub" v-if="contentItem.small!=0">({{contentItem.small}})</em>
                <div style="line-height:48px;padding-top:15px;" v-html="contentItem.desc"></div>
            </span>
            </div>
        </template>

        <!--短文改错-END-->
    </div>

</script>
<!--解答题-END-->


<!--选答题-->
<sctipt type="text/html" id="tpl-xuandati-template">
    <div class="ques-type ques-type2"
         v-bind:id="'ques-anchor'+partItemIndex+'-'+typeItemIndex">
        <div class="question explain choice-more hover">
            <div class="part-menu">
                <!--<a href="javascript:;" class="btn" @click="showAlertXuandati(partItemIndex,typeItemIndex)">-->
                <!--<i class="iconfont">&#xe602;</i>添加-->
                <!--</a>-->
                <a href="javascript:;" class="btn" @click="showAlertEditXuandati(partItemIndex,typeItemIndex)">
                <i class="iconfont">&#xe601;</i>编辑
                </a>
                <a href="javascript:;"
                   class="btn"
                   v-on:click="delThisQues(partItemIndex,typeItemIndex)">
                    <i class="iconfont">&#xe600;</i>删除
                </a>
                <a v-show="typeItemIndex != partItem.list.length-1"
                   href="javascript:;"
                   class="btn"
                   @click="moveDownThisQues(partItemIndex,typeItemIndex)">
                    <i class="iconfont">&#xe605;</i>下移
                </a>
                <a v-show="typeItemIndex !=0"
                   href="javascript:;"
                   class="btn"
                   @click="moveUpThisQues(partItemIndex,typeItemIndex)">
                    <i class="iconfont">&#xe604;</i>上移
                </a>
                <!--<link rel="import" href="/app/card/view/include/ques-handle-btn-group.tpl?__inline">-->
            </div>
            <div class="q-title">
                <ques-title-info v-bind:item="newType2"></ques-title-info>
                <!--选作题标题-->
                <div class="content-list">
                    <div class="choice-more-detail">
                        <p class="info">请考生在 {{newType2.content.order.join(', ')}} 题中任选 {{newType2.do}} 题作答。
                            请把你所选题目的题号用2B铅笔涂黑。如果多做，则按所做的第一题计分。在作答过程中请写清每问的小标号。
                        </p>
                        <p class="num" v-if="newType2.content.order && newType2.content.order.length>0">
                            我选做的题号是：
                            <span v-for="(orderItem, subOrderID) in newType2.content.order"
                                    class="title-num"
                                    v-bind:card-ques-id="orderItem">[<b>{{orderItem}}</b>]</span>
                        </p>
                    </div>
                </div>
                <!--选作题标题-END-->
            </div>
            <div class="q-content">

                <div class="jd-kong-list clearfix">
                    <!--新模板-->
                    <template>
                        <div class="jd-hang-item clearfix">

                            <!--if desc-->
                            <div style="word-wrap: break-word;padding-top:10px;font-size:13px;" v-if="newType2.content.desc != null && newType2.content.desc.length>0">
                                <p v-for="descItem in newType2.content.desc">{{descItem}}</p>
                            </div>

                            <!--no img-->
                            <span class="jd-kong-item hang-1-1"
                                  v-if="!isHasImg"
                                  v-for="kongItem in computedKong"
                                  v-bind:class="{'no-line':newType2.content.uline==0}">
                            </span>

                            <!--img-->
                            <span class="jd-kong-item hang-1-1 hang-has-img no-line"
                                  v-if="isHasImg"
                                  v-bind:style="{minHeight:hlineHeight*computedKong+'px'}">
                                <span class="jd-img-list big-block">
                                    <span class="img-item clearfix"
                                          v-for="imgItem in newType2.content.img">
                                        <span class="clearfix" v-for="imgItm in imgItem" style="display:block;padding-top:15px;">
                                            <img v-bind:src="imgItm[0]" alt="试题图片">
                                            <br>
                                            <span class="img-item-desc" style="display: block;text-align: center;" v-if="imgItm[1] != null">{{imgItm[1]}}</span>
                                        </span>
                                    </span>
                                </span>
                            <!--img-list-END-->
                            </span>
                        </div>
                    </template>
                </div>
                <!--新模板-END-->
            </div>
        </div>
    </div>
</sctipt>
    <script type="text/html" id="tpl-main-top">
    <div class="main-top hover">
        <div class="part-menu">
            <a href="javascript:;" class="btn" @click="showTitleAlert">
                <i class="iconfont">&#xe601;</i>编辑
            </a>
        </div>
        <div class="name center" id="paper-name" v-show="title.top.display">
            {{title.top.content}}
        </div>
        <div class="paper-title mintitle red center" v-show="title.title.display">
            {{title.title.content}}
        </div>
        <div class="paper-title subtitle center" v-show="title.sub.display">
            {{title.sub.content}}
        </div>
    </div>
</script>
    <!-- 姓名及标题 -->
<script type="text/html" id="tpl-alert-title">
    <modal v-show="modal.state_alertTitle">
        <h3 slot="header">
            修改头部信息
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>

        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <form class="lay-set form" id="form-title-info">
                <div class="item-content">
                    <div class="itm-item" id="alert-paper-name">
                        <span class="item-name">姓名：</span>
                        <input class="w80" type="text" name="paperName"
                               v-bind:value="paperName.content"
                               v-bind:disabled="!paperName.display">
                        <span class="ml8 view-title unselect" data-state="paperName" v-html="paperName.html" @click="toggleShowState"></span>
                    </div>
                    <div class="itm-item" id="alert-paper-title">
                        <span class="item-name">主标题：</span>
                        <input class="w80" type="text" name="mainTitle"
                               v-bind:value="mainTitle.content"
                               v-bind:disabled="!mainTitle.display">
                        <span class="ml8 view-title unselect" data-state="mainTitle" v-html="mainTitle.html" @click="toggleShowState"></span>
                    </div>
                    <div class="itm-item" id="alert-paper-subtitle">
                        <span class="item-name">副标题：</span>
                        <input class="w80" type="text" name="subTitle"
                               v-bind:value="subTitle.content"
                               v-bind:disabled="!subTitle.display">
                        <span class="ml8 view-title unselect" data-state="subTitle" v-html="subTitle.html" @click="toggleShowState"></span>
                    </div>
                </div>
            </form>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>

</script>


    <!--编辑答题卡-->
    <script type="text/html" id="tpl-exam-info-a4">
    <div class="exam-intro hover">
        <div class="part-menu">
            <a href="javascript:;" class="btn" v-on:click="showAlertExamInfo">
                <i class="iconfont">&#xe601;</i>编辑
            </a>
        </div>
        <!-- A4高考仿真  -->
        <div class="exam-cont layer1 examtype-gk" v-if="type.display=='1'">
            <div class="intro-l left">
                <div class="exam-number red" v-if="data.num.display == 1">
    <em class="numwidth">准考证号</em><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
</div>
                <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
            </div>
            <div class="intro-r right">
                <div class="barcode center red" v-if="data.code.display == 1">
    <h3>贴条形码区</h3>
    <p>（正面朝上，切勿贴出虚线方框）</p>
</div>
                <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
            </div>
        </div>
        <!-- A4期中期末  -->
        <div class="exam-cont layer1 examtype-qz" v-if="type.display=='2'">
            <div class="intro-l left">
                <!-- 准考证号 -->
<div class="tab-card" v-if="data.num.display == 1">
    <table class="card">
        <tbody>
            <tr>
                <td colspan="13" class="title"><em>准考证号</em></td>
            </tr>
            <tr class="pad8">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
            </tr>
        </tbody>
    </table>
</div>
            </div>
            <div class="intro-r right">
                <div class="barcode center red" v-if="data.code.display == 1">
    <h3>贴条形码区</h3>
    <p>（正面朝上，切勿贴出虚线方框）</p>
</div>
                <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
                <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
            </div>
        </div>
        <!-- A4单元测试 -->
        <div class="exam-cont layer1 examcard-no-a4 examtype-dy" v-if="type.display=='3'">
            <div class="intro-l left">
                <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
            </div>
            <div class="intro-r right">
                <div class="barcode center red" v-if="data.code.display == 1">
    <h3>贴条形码区</h3>
    <p>（正面朝上，切勿贴出虚线方框）</p>
</div>
                <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
            </div>
        </div>
        <!-- A4课时训练 -->
        <div class="exam-cont layer1 barcode-no-a4 examtype-ks" v-if="type.display=='4'">
            <div class="intro-l left">
                <!-- 准考证号 -->
<div class="tab-card" v-if="data.num.display == 1">
    <table class="card">
        <tbody>
            <tr>
                <td colspan="13" class="title"><em>准考证号</em></td>
            </tr>
            <tr class="pad8">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
            </tr>
        </tbody>
    </table>
</div>
            </div>
            <div class="intro-r right">
                <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
                <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
            </div>
        </div>
    </div>
</script>
    <script type="text/html" id="tpl-exam-info-a3">
    <div class="exam-intro hover">
        <div class="part-menu">
            <a href="javascript:;" class="btn" v-on:click="showAlertExamInfo">
                <i class="iconfont">&#xe601;</i>编辑
            </a>
        </div>
        <!-- A3高考仿真  -->
        <div class="exam-cont simpleness examtype-gk" v-if="type.display=='1'">
            <div class="exam-number red" v-if="data.num.display == 1">
    <em class="numwidth">准考证号</em><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
</div>
            <div class="clearfix">
                <div class="intro-l left">
                    <div class="barcode center red" v-if="data.code.display == 1">
    <h3>贴条形码区</h3>
    <p>（正面朝上，切勿贴出虚线方框）</p>
</div>
                    <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
                </div>
                <div class="intro-r right">
                    <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
                </div>
            </div>
        </div>
        <!-- A3期中期末  -->
        <div class="exam-cont complex examtype-qz" v-if="type.display=='2'">
            <!-- 准考证号 -->
<div class="tab-card" v-if="data.num.display == 1">
    <table class="card">
        <tbody>
            <tr>
                <td colspan="13" class="title"><em>准考证号</em></td>
            </tr>
            <tr class="pad8">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
                <td>
                    [<span class="pad4">0</span>]<br>
                    [<span class="pad4">1</span>]<br>
                    [<span class="pad4">2</span>]<br>
                    [<span class="pad4">3</span>]<br>
                    [<span class="pad4">4</span>]<br>
                    [<span class="pad4">5</span>]<br>
                    [<span class="pad4">6</span>]<br>
                    [<span class="pad4">7</span>]<br>
                    [<span class="pad4">8</span>]<br>
                    [<span class="pad4">9</span>]<br>
                </td>
            </tr>
        </tbody>
    </table>
</div>
            <div class="intro-l left">
                <div class="barcode center red" v-if="data.code.display == 1">
    <h3>贴条形码区</h3>
    <p>（正面朝上，切勿贴出虚线方框）</p>
</div>
                <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
            </div>
            <div class="intro-r right">
                <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
            </div>
        </div>
        <!-- A3单元测试 -->
        <div class="exam-cont examtype-dy" v-if="type.display=='3'">
            <div class="intro-l left">
                <div class="barcode center red" v-if="data.code.display == 1">
    <h3>贴条形码区</h3>
    <p>（正面朝上，切勿贴出虚线方框）</p>
</div>
                <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
            </div>
            <div class="intro-r right">
                <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
            </div>
        </div>
        <!-- A3课时训练 -->
        <div class="exam-cont examtype-ks" v-if="type.display=='4'">
            <div class="exam-number red" v-if="data.num.display == 1">
    <em class="numwidth">准考证号</em><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
</div>
            <!--<div class="notice">-->
    <!--<h3>注意事项</h3>-->
    <!--<p class="text">1．答题前，请将姓名、班级、考场、座位号、准考证号填写清楚。<br />-->
        <!--2．客观题需用（2B）铅笔按<span></span>图示规范填涂，修改时用橡皮擦干净。<br />-->
        <!--3．主观题使用黑色签字笔书写。<br />-->
        <!--4．必须在题号所对应的答题区域内作答，超出答题区域书写无效。</p>-->
<!--</div>-->
<div class="notice" v-if="data.care.display">
    <h3>注意事项</h3>
    <p class="text" v-html="data.care.content.replace(/\n/g,'<br>')"></p>
</div>
            <div class="miss red" v-if="data.miss.display == 1">
    <span class="miss-sign">缺考标记<em></em></span>
    <span>考生禁填</span>
</div>
        </div>
    </div>
</script>
    <script type="text/html" id="tpl-alert-exam-info">
    <!-- 编辑准考证区域 -->
    <modal v-show="modal.state_alertExamInfo">
        <h3 slot="header">
            编辑答题卡
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form">
                <div class="item-content">
                    <div class="itm-item">
                        <span class="item-name">准考证号：</span>
                        <input class="w-full" type="text" value="准考证号" name="examcard" disabled>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">注意事项：</span>
                        <textarea class="large-text" style="height:8em;" id="examInfoNotice" name="notice"
                                  v-bind:disabled="careDisplay !=1 "
                                  v-bind:value="care.content" rows="5"></textarea>
                        <div class="prompt"><strong>提示：</strong>段落换行请使用 <em>"回车键"</em></div>
                    </div>
                    <!-- v-if="data.type.display == 1 || data.type.display == 2"-->
                    <div class="itm-item">
                        <span class="item-name">显示区块：</span>
                        <div class="ctrl-block-show unselect">
                            <label><input type="checkbox"
                                          v-bind:true-value="1"
                                          v-bind:false-value="0"
                                          v-bind:disabled="data.type.display == 3"
                                          v-model="numDisplay" id="J_numDisplay">准考证号</label>
                            <label><input type="checkbox"
                                          v-bind:true-value="1"
                                          v-bind:false-value="0"
                                          v-bind:disabled="data.type.display == 4"
                                          v-model="codeDisplay" id="J_codeDisplay">贴条形码区</label>
                            <br>
                            <label><input type="checkbox"
                                          v-bind:true-value="1"
                                          v-bind:false-value="0"
                                          v-on:change="careDisplayChange"
                                          v-model="careDisplay" id="J_careDisplay">注意事项</label>
                            <label><input type="checkbox"
                                          v-bind:true-value="1"
                                          v-bind:false-value="0"
                                          v-model="missDisplay" id="J_missDisplay">缺考标记</label>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>

    <script type="text/html" id="tpl-add-img-comp">
    <!--添加图片-->
    <div class="itm-item">
        <span class="item-name">图片：</span>
        <div class="uploadImage">
            <div class="j_imgList" v-if="computedEditData.img && computedEditData.img.length>0">
                <span class="list-item-cell" v-for="(imgItem,imgItemIndex) in computedEditData.img">
                    <span class="list-item">
                        <img v-bind:src="imgItem[0]" v-bind:alt="imgItem[1]?imgItem[1]:('试题图片-'+imgItemIndex)">
                        <a href="javascript:;" class="del-img" v-on:click="delThisQuesImg(imgItemIndex)" title="删除图片">×</a>
                    </span>
                </span>
            </div>
            <span class="type-select">
                <label class="unselect">
                    <input type="checkbox" v-model="isAddQuesImg">添加图片</label>
                <label v-if="isAddQuesImg != 0">
                    <input type="radio" name="addQuesImgBy" value="0" v-on:click="selectServerImage">选用试题图片</label>
                <label v-if="isAddQuesImg != 0">
                    <input type="radio" name="addQuesImgBy" value="1" v-on:click="uploadThisImage">上传</label>
            </span>
        </div>
    </div>
</script>
<script type="text/html" id="tpl-add-desc-comp">
    <!--添加描述-->
    <div class="itm-item">
        <span class="item-name">描述：</span>
        <textarea class="w-full" role="desc" placeholder="描述~" v-model="computedEditData.desc"></textarea>
        <p><strong>提示：</strong>添加换行请使用<span class="text-primary">"回车键"</span></p>
    </div>
</script>

    <!--添加试题-alert-->
    <script type="text/html" id="tpl-alert-tiankongti">
    <modal v-show="modal.state_alertTiankongti">
        <h3 slot="header">
            添加填空题
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form">
                <div class="item-content" id="lay_tiankongti">
                    <select-part-to v-if="addFrom==='bar'" v-bind:addPartTo="1"></select-part-to>
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" role="title" v-bind:value="defValue.title">
                    </div>
                    <div class="itm-item">
                        <span class="item-name">题号：</span>
                        <div class="test-order-set">
                            <span class="text-primary order-str">{{computedOrderRange}}</span>
                            <set-ques-order v-bind:selectedAddedTestNum="selectedAddedTestNum"></set-ques-order>
                        </div>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">添加题数：</span>
                        <span>
                            <select class="w8em" v-model="selectedAddedTestNum">
                                <option v-bind:value="optItem" v-for="optItem in 20">{{optItem}} 题</option>
                            </select>
                        </span>
                    </div>
                    <!--编辑小题属性-->
                    <div class="itm-item" id="add_tiankongti_form">
                        <span class="item-name">试题编号：</span>
                        <div class="fillin-list">
                            <tiankongti-sub
                                    v-bind:selectedAddedTestNum="selectedAddedTestNum"
                                    v-bind:itemIndex="itemIndex"
                                    v-bind:computedOrderID="computedOrderID"
                                    v-for="(subItem,itemIndex) in (selectedAddedTestNum-0)"></tiankongti-sub>
                        </div>
                        <!--v-bind:toggleSetOrderID="toggleSetOrderID"
                                    v-bind:getOrderID="getOrderID"
                                    v-bind:itemIndex="itemIndex"-->

                    </div>

                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>

<script type="text/html" id="tpl-tiankongti-sub">
    <div class="fillin-list-nub">
        <span class="item-number" v-bind:data-role-order="computedOrderID + itemIndex">第 {{computedOrderID + itemIndex}} 题</span>
        <p class="item-detail">
            <select class="w8em" role="kong" v-model="changeTestItemKong">
                <option v-bind:value="kongItem" v-for="kongItem in 10">{{kongItem}} 空</option>
            </select>
        </p>
        <p class="item-detail">
            每空
            <select class="w8em" role="hline">
                <option value="1">1 行</option>
                <option value="0.5">1/2 行</option>
                <option value="0.3">1/3 行</option>
                <option v-bind:value="optItem+1" v-for="optItem in 1">{{optItem+1}} 行</option>
            </select>
        </p>
        <p class="item-detail" v-if="changeTestItemKong!=1">
            <label><input type="checkbox" v-bind:disabled="changeTestItemKong==1" class="checktype" role="hasSubOrder">添加小题号</label>
        </p>
    </div>
</script>

    <script type="text/html" id="tpl-alert-xuanzeti">
    <modal v-show="modal.state_alertXuanzeti">
        <h3 slot="header">
            添加选择题
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form" id="add-xuanZeTi-form">
                <div class="item-content">
                    <select-part-to v-if="addFrom==='bar'" v-bind:addPartTo="0"></select-part-to>
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" value="选择题" disabled>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">题号：</span>
                        <div class="test-order-set">
                            <span class="text-primary order-str">{{computedOrderRange}}</span>
                            <set-ques-order v-bind:selectedAddedTestNum="selectedAddedTestNum"></set-ques-order>
                        </div>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">添加题数：</span>
                        <span>
                            <select class="w8em" name="choice-num" id="choice-num" v-model="selectedAddedTestNum">
                                <option v-for="optNum in 50" v-bind:value="optNum">{{optNum}} 题</option>
                            </select>
                        </span>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">选项类型：</span>
                        <span class="type-select">
                    <label class="unselect"><input type="radio" name="choice-style" v-model="choiceStyle" value="1">选择题</label>
                    <label class="unselect"><input type="radio" name="choice-style" v-model="choiceStyle" value="3">判断题</label>
                </span>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">选项数量：</span>
                        <select class="w8em" name="choice-num" id="option-num" v-bind:disabled="choiceStyle==3">
                            <option value="2" v-bind:selected="choiceStyle==3">2</option>
                            <option value="3">3</option>
                            <option value="4" v-bind:selected="choiceStyle!=3">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn()">确定</a>
        </template>
    </modal>
</script>

    <script type="text/html" id="tpl-alert-jiedati">
    <modal v-show="modal.state_alertJiedati">
        <h3 slot="header">
            添加解答题
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info" style="width:800px">
            <div class="lay-set form">
                <div class="item-content" id="lay_jiedati">
                    <select-part-to v-if="addFrom==='bar'" v-bind:addPartTo="1"></select-part-to>
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" role="title" v-bind:value="defValue.title">
                    </div>
                    <div class="itm-item">
                        <span class="item-name">题号：</span>
                        <div class="test-order-set">
                            <span class="text-primary order-str">{{computedOrderRange}}</span>
                            <set-ques-order v-bind:selectedAddedTestNum="selectedAddedTestNum"></set-ques-order>
                        </div>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">添加题数：</span>
                        <select class="w8em" v-model="selectedAddedTestNum">
                            <option v-bind:value="addNumItem" v-for="addNumItem in maxAddNum">{{addNumItem}} 题</option>
                        </select>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">设置：</span>
                        <div class="explain-info">
                            <ul class="tab-item">
                                <li v-bind:class="{on:(currentItemIndex==addItemIndex)}"
                                    v-for="(addItem,addItemIndex) in selectedAddedTestNum-0"
                                    v-on:click="tabCurrentItem(addItemIndex)">第{{computedOrderID+addItemIndex}}题</li>
                            </ul>
                            <div class="con-item" id="jiedatiAddedQuesContent">
                                <jiedati-add-test-item
                                    v-for="(addItemContent,addItemContentIndex) in selectedAddedTestNum-0"
                                    v-show="currentItemIndex==addItemContentIndex"
                                    v-bind:computedOrderID="computedOrderID+addItemContentIndex"
                                    v-bind:addItemContentIndex="addItemContentIndex"></jiedati-add-test-item>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>

<!--解答题各题属性设置-->
<script type="text/html" id="tpl-jiedati-add-test-item">
<div class="con-item-list" v-bind:jiedati-ques-id="computedOrderID">
    <div class="itm-item" style="padding-left:65px;">
        <span class="item-name" style="margin-left:-65px;width:65px;">小题：</span>
        <span class="type-select">
        <label class="unselect">
            <input type="radio" role="has-sub-test"
                   v-bind:name="'has-sub-test-'+addItemContentIndex"
                   v-model="isAddSubTest" value="0">无小题</label>
        <label class="unselect">
            <input type="radio" role="has-sub-test"
                   v-bind:name="'has-sub-test-'+addItemContentIndex"
                   v-model="isAddSubTest" value="1">有小题</label>
        <div style="clear:both"></div>
    </span>
    </div>
    <div class="itm-item" style="padding-left:65px;">
        <span class="item-name" style="margin-left:-65px;width:65px;">属性：</span>

        <!--有小题-->
        <div v-if="isAddSubTest-0">
            共
            <select class="w5em" v-model="selectedAddSubNum">
                <option v-for="(optItem,optItemIndex) in (maxAddSubNum-1)"
                        v-bind:value="optItem+1">{{optItem+1}}</option>
            </select>
            <span class="ml8">小题</span>
            <div class="sub-tab-item-wrap" role="hasSmallQues">
                <ul class="sub-tab-item clearfix">
                    <li v-for="(addSubItem,addSubItemIndex) in selectedAddSubNum"
                        v-bind:class="{on:(currentSubItemIndex==addSubItemIndex)}"
                        v-on:click="tabCurrentSubItem(addSubItemIndex)">({{addSubItem}})小题</li>
                </ul>
                <div class="sub-tab-con">
                    <jiedati-add-sub-test-item
                        v-for="(addSubItemContent,addSubItemContentIndex) in selectedAddSubNum"
                        v-show="currentSubItemIndex==addSubItemContentIndex"
                        v-bind:computedOrderID="computedOrderID"
                        v-bind:addItemContentIndex="addItemContentIndex"
                        v-bind:addSubItemContentIndex="addSubItemContentIndex"></jiedati-add-sub-test-item>
                </div>
            </div>
        </div>
        <!--没有小题-->
        <div v-if="!(isAddSubTest-0)" role="noSmallQues">
            <jiedati-add-sub-test-item
                    v-bind:computedOrderID="computedOrderID"
                    v-bind:addItemContentIndex="addItemContentIndex"
                    v-bind:addSubItemContentIndex="0"></jiedati-add-sub-test-item>
        </div>
    </div>
</div>
</script>

<!--解答题小题属性设置-->
<script type="text/html" id="tpl-jiedati-add-sub-test-item">
    <div class="item-detail" v-bind:ques-id="computedOrderID" v-bind:sub-test-id="addSubItemContentIndex">
        <!--样式-->
        <div style="margin-bottom:6px;">
            <span class="type-select" style="margin:0;">
                <label class="unselect radio-img no-line" style="margin:3px 15px 3px 0">
                    <input type="radio" role="uline" checked v-bind:name="'has-underline-'+addItemContentIndex+'-'+addSubItemContentIndex" value="0">无横线</label>
                <label class="unselect radio-img on-line" style="margin:3px 0">
                    <input type="radio" role="uline" v-bind:name="'has-underline-'+addItemContentIndex+'-'+addSubItemContentIndex" value="1">有横线</label>

            </span>
        </div>
        <!--分数-->
        <div style="margin-bottom:6px;">
            <span>
                <select class="w8em" role="score">
                    <option v-bind:value="optScore"
                            v-for="optScore in 30">{{optScore}} 分</option>
                </select>
            </span>
        </div>
        <!--空行属性-->
        <div style="margin-bottom:6px;">

            <!--空-->
            <select class="w8em" role="kong">
                <option v-bind:value="addKongNumItem" v-for="addKongNumItem in 20">{{addKongNumItem}} 空</option>
            </select>

            <!--行-->
            每空
            <select class="w8em" role="hline">
                <option value="1">1 行</option>
                <option value="0.5">1/2 行</option>
                <option value="0.3">1/3 行</option>
                <option v-for="(optItem,optItemIndex) in 29" v-bind:value="optItemIndex+2">{{optItemIndex+2}} 行</option>
            </select>
        </div>
    </div>
</script>

    <script type="text/html" id="tpl-alert-edit-jiedati">
    <modal v-show="modal.state_alertEditJiedati">
        <h3 slot="header">
            编辑试题：第{{computedEditData.order}}题
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info" style="width:560px">
            <div class="lay-set form" id="j_edit-this-ques">
                <div class="item-content">
                    <!--样式-->
                    <div class="itm-item" v-if="!isENError">
                        <span class="item-name">样式：</span>
                        <span class="type-select">
                            <label class="unselect radio-img no-line">
                                <input type="radio" role="uline" v-model="computedEditData.uline" name="has-underline" value="0">无横线
                            </label>
                            <label class="unselect radio-img on-line">
                                <input type="radio" role="uline" v-model="computedEditData.uline" name="has-underline" value="1">有横线
                            </label>
                        </span>
                        <span class="text-primary">（提示：‘有图片’或‘有描述’时横线将不显示）</span>
                    </div>

                    <!--分数-->
                    <div class="itm-item">
                        <span class="item-name">分数：</span>
                        <select class="w8em" role="score" v-model="computedEditData.score">
                            <option v-bind:value="optScore" v-for="optScore in 30">{{optScore}} 分</option>
                        </select>
                    </div>

                    <!--空行属性-->
                    <div class="itm-item" v-if="!isENError">
                        <span class="item-name">空行：</span>

                        <!--空-->
                        <select class="w8em" role="kong" v-model="computedEditData.kong">
                            <option v-bind:value="addKongNumItemIndex" v-for="(addKongNumItem,addKongNumItemIndex) in 11">{{addKongNumItemIndex}} 空</option>
                        </select>

                        <!--行-->
                        每空
                        <select class="w8em" role="hline" v-model="computedEditData.hline">
                            <option value="1">1 行</option>
                            <option value="0.5">1/2 行</option>
                            <option value="0.3">1/3 行</option>
                            <option v-for="(optItem,optItemIndex) in 30" v-bind:value="optItemIndex+2">{{optItemIndex+2}} 行
                            </option>
                        </select>
                    </div>

                    <!--添加图片-->
                    <add-img-comp v-if="!isENError" v-bind:computedEditData="computedEditData"></add-img-comp>

                    <!--添加描述-->
                    <div class="itm-item" v-if="!isENError">
                        <span class="item-name">描述：</span>
                        <textarea class="w-full" role="desc" placeholder="描述~" v-model="computedEditData.desc"></textarea>
                        <p><strong>提示：</strong>添加换行请使用<span class="text-primary">"回车键"</span></p>
                    </div>



                    <div class="itm-item" v-if="isENError">
                        <span class="item-name">描述：</span>
                        <div class="J_content-edit" v-html="computedEditData.desc" contenteditable="true">

                        </div>
                        <!--<p><strong>提示：</strong>添加换行请使用<span class="text-primary">"回车键"</span></p>-->
                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>
    <!--<link rel="import" href="../include/alert/alert-add-img.tpl?__inline"/>-->
    <!--<link rel="import" href="../include/alert/alert-add-desc.tpl?__inline"/>-->
    <script type="text/html" id="tpl-alert-xuandati">
    <modal v-show="modal.state_alertXuandati">
        <h3 slot="header">
            添加选答题
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info" style="width:550px">
            <div class="lay-set form">
                <div class="item-content" id="lay_xuandati">
                    <select-part-to v-if="addFrom==='bar'" v-bind:addPartTo="1"></select-part-to>
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" role="title" v-bind:value="defValue.title">
                    </div>
                    <div class="itm-item">
                        <span class="item-name">题号：</span>
                        <div class="test-order-set">
                            <span class="text-primary order-str">{{computedOrderRange}}</span>
                            <set-ques-order v-bind:selectedAddedTestNum="selectedAddedTestNum"></set-ques-order>
                        </div>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">添加题数：</span>
                        <select class="w8em" v-model="selectedAddedTestNum">
                            <option v-bind:value="addNumItem+1" v-for="addNumItem in 5">{{addNumItem+1}} 题</option>
                        </select>
                        <select class="w8em" role="ques-do">
                            <option v-bind:value="addNumItem" v-for="addNumItem in selectedAddedTestNum-1">选做 {{addNumItem}} 题</option>
                        </select>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">分数：</span>
                        <select class="w8em" role="score">
                            <option v-bind:value="optScore" v-for="optScore in 30">{{optScore}} 分</option>
                        </select>
                    </div>

                    <div class="itm-item">
                        <span class="item-name">行数：</span>
                        <select class="w8em" role="hline">
                            <option v-for="optItem in 30" v-bind:value="optItem">{{optItem}} 行</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>



    <script type="text/html" id="tpl-alert-edit-xuandati">
    <modal v-show="modal.state_alertEditXuandati">
        <h3 slot="header">
            编辑选答题
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info" style="width:650px">
            <div class="lay-set form" id="j_edit-this-ques">
                <div class="item-content" id="lay_edit_xuandati">
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" role="title" v-bind:value="defValue.title">
                    </div>
                    <div class="itm-item">
                        <span class="item-name">题号：</span>
                        <select class="w8em" role="order" v-model="editOrderID" v-on:change="changeQuesOrderID">
                            <option value="0">-请选择-</option>
                            <option v-bind:value="item"  v-bind:data-index="index" v-for="(item,index) in editOrderArray">第 {{item}} 题</option>
                        </select>
                    </div>

                    <!--选择题号后显示-->
                    <template v-if="editOrderID>0">
                        <div class="itm-item">
                            <span class="item-name">分数：</span>
                            <select class="w8em" role="score" v-model="computedEditData.score">
                                <option v-bind:value="optScore" v-for="optScore in 30">{{optScore}} 分</option>
                            </select>
                        </div>

                        <div class="itm-item">
                            <span class="item-name">行数：</span>
                            <select class="w8em" role="hline" v-model="computedEditData.hline">
                                <option v-for="optItem in 30" v-bind:value="optItem">{{optItem}} 行</option>
                            </select>
                        </div>
                        <add-img-comp v-bind:computedEditData="computedEditData"></add-img-comp>
                        <add-desc-comp v-bind:computedEditData="computedEditData"></add-desc-comp>
                    </template>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>



    <script type="text/html" id="tpl-alert-en-zuowen">
    <modal v-show="modal.state_alertEnZuowen">
        <h3 slot="header">
            英语作文
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form" id="j_edit-this-ques">
                <div class="item-content" id="EN_zuowenForm">
                    <select-part-to v-if="isEdit==0" v-bind:addPartTo="1"></select-part-to>
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" name="en-zuowen-title" v-bind:value="defValue.title">
                    </div>
                    <div class="itm-item" v-if="isEdit==0">
                        <span class="item-name">题号：</span>
                        <div class="test-order-set">
                            <span class="text-primary order-str">{{computedOrderRange}}</span>
                            <set-ques-order v-bind:selectedAddedTestNum="selectedAddedTestNum"></set-ques-order>
                        </div>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">添加行数：</span>
                        <select class="w8em" name="en-zuowen-hline" v-bind:value=defValue.hline>
                            <option v-bind:value="hangOpt" v-for="hangOpt in 40">{{hangOpt}} 行</option>
                        </select>
                    </div>

                    <!--添加图片-->
                    <add-img-comp v-if="isEdit==1" v-bind:computedEditData="computedEditData"></add-img-comp>

                    <!--添加描述-->
                    <add-desc-comp v-if="isEdit==1" v-bind:computedEditData="computedEditData"></add-desc-comp>
                    <!--<div class="itm-item">-->
                        <!--<span class="item-name">开头描述：</span>-->
                        <!--<textarea class="w-full">This example cannot be edited because our editor uses a textarea for input,and your browser does not allow a textarea inside a textarea.</textarea>-->
                    <!--</div>-->
                    <!--<div class="itm-item">-->
                        <!--<span class="item-name">结尾描述：</span>-->
                        <!--<textarea>This example cannot be edited because our editor uses a textarea for input,and your browser does not allow a textarea inside a textarea.</textarea>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn">确定</a>
        </template>
    </modal>
</script>

    <script type="text/html" id="tpl-alert-cn-zuowen">
    <modal v-show="modal.state_alertCnZuowen">
        <h3 slot="header">
            语文作文
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form" id="j_edit-this-ques">
                <div class="item-content" id="CN_zuowenForm">
                    <select-part-to v-if="isEdit==0" v-bind:addPartTo="1"></select-part-to>
                    <div class="itm-item">
                        <span class="item-name">标题：</span>
                        <input class="w-full" type="text" name="cn-zuowen-title" v-bind:value="defValue.title">
                    </div>
                    <div class="itm-item" v-if="isEdit==0">
                        <span class="item-name">题号：</span>
                        <div class="test-order-set">
                            <span class="text-primary order-str">{{computedOrderRange}}</span>
                            <set-ques-order v-bind:selectedAddedTestNum="selectedAddedTestNum"></set-ques-order>
                        </div>
                    </div>
                    <div class="itm-item">
                        <span class="item-name">格数：</span>
                        <span>
                            <select class="w8em" name="cn-zuowen-char" v-bind:value="computedEditData.char">
                                <option v-bind:value="charOpt*50" v-for="charOpt in 20">{{charOpt*50}} 格</option>
                            </select>
                        </span>
                    </div>

                    <!--添加图片-->
                    <add-img-comp v-if="isEdit==1" v-bind:computedEditData="computedEditData"></add-img-comp>

                    <!--添加描述-->
                    <add-desc-comp v-if="isEdit==1" v-bind:computedEditData="computedEditData"></add-desc-comp>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn()">确定</a>
        </template>
    </modal>
</script>

    <script type="text/html" id="tpl-set-ques-order">
    <span v-show="selectedAddedTestNum==1" class="unselect" style="vertical-align:-1px">
        <label>
            <input type="checkbox"
               style="vertical-align:-2px"
               v-model="toggleSetOrderID"
               v-bind:disabled="selectedAddedTestNum>1">设置题号
        </label>
        <span class="f12 text-muted" v-if="toggleSetOrderID">
            <input id="userSetOrderID" class="w5em center" type="number"
                   v-bind:value="computedOrderID"
               v-on:change="checkSetterOrderID">
            <a href="javascript:;" class="text-primary"
               v-on:mouseleave="hideSuggestOrderID"
               v-on:mouseenter="showSuggestOrderID"> 提示
            </a>
        </span>
    </span>
</script>


    <!--编辑试题title-->
    <script type="text/html" id="tpl-alert-edit-ques-title">
    <modal v-show="modal.state_alertEditQuesTitle">
        <h3 slot="header">
            编辑头信息
            <a href="javascript:" class="modal-close" v-on:click="closeThisModal">×</a>
        </h3>
        <div slot="body" class="lay-normal-box lay-edit-paper-info">
            <div class="lay-set form">
                <div v-for="(partItem,partItemIndex) in examPart"
                     v-if="partItemIndex == currentPartID">
                    <div class="item-content"
                         v-for="(quesListItem,quesListItemIndex) in partItem.list"
                         v-if="quesListItemIndex == currentTitleID">
                        <div class="itm-item">
                            <span class="item-name">名称：</span>
                            <input class="w-full" type="text" v-bind:name="('ques-title-'+partItemIndex+'-'+currentTitleID)" v-bind:value="quesListItem.title">
                        </div>
                        <div class="itm-item">
                            <span class="item-name"></span>
                            <label><input type="radio" v-model="titleShowModal" value="1" v-bind:name="('ques-title-display-'+partItemIndex+'-'+currentTitleID)" >显示</label>
                            <label><input type="radio" v-model="titleShowModal" value="0" v-bind:name="('ques-title-display-'+partItemIndex+'-'+currentTitleID)" >隐藏</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <a class="modal-btn" href="javascript:;" @click="closeThisModal">取消</a>
            <a class="modal-btn modal-btn-primary" href="javascript:;" @click="yesBtn(currentPartID,currentTitleID)">确定</a>
        </template>
    </modal>
</script>


    <!--right-con-->
    <script type="text/html" id="tpl-type-style">
    <div class="panel-order format">
        <ul>
            <li id="tabA4" v-on:click="chooseTypeA4">
                <span v-bind:class="['format-img',{'choice':layout.style=='A4' || layout.style==null}]"><img v-bind:src="typeA4.src" alt="A4尺寸"></span>
                <p>一栏（A4尺寸）</p>
            </li>
            <li id="tabA3" v-on:click="chooseTypeA3">
                <span v-bind:class="['format-img',{'choice':layout.style=='A3'}]"><img v-bind:src="typeA3.src" alt="A3尺寸"></span>
                <p>三栏（A3尺寸）</p>
            </li>
        </ul>
    </div>
</script>
    <script type="text/html" id="tpl-exam-type">
    <div class="panel-order examtype" id="examtype">
        <a href="javascript:;"
           data-examtype="1"
           v-bind:class="{choice:type.display=='1'}"
           v-on:click="toggleExamType" id="examtype1">高考仿真</a>
        <a href="javascript:;"
           data-examtype="2"
           v-bind:class="{choice:type.display=='2'}"
           v-on:click="toggleExamType" id="examtype2">期中期末</a>
        <a href="javascript:;"
           data-examtype="3"
           v-bind:class="{choice:type.display=='3'}"
           v-on:click="toggleExamType" id="examtype3">单元测试</a>
        <a href="javascript:;"
           data-examtype="4"
           v-bind:class="{choice:type.display=='4'}"
           v-on:click="toggleExamType" id="examtype4">课时训练</a>
    </div>
</script>
<script>

</script>

    <script type="text/html" id="tpl-add-test">
    <div class="panel-order addtest">
        <a href="javascript:;" v-on:click="showAlertXuanzeti(comeFrom)"><span>选</span>选择题</a>
        <a href="javascript:;" v-on:click="showAlertTiankongti(comeFrom)"><span>填</span>填空题</a>
        <a href="javascript:;" v-on:click="showAlertJiedati(comeFrom)"><span>解</span>解答题</a>
        <a href="javascript:;" v-on:click="showAlertXuandati(comeFrom)"><span>选</span>选答题</a>
        <a href="javascript:;" v-on:click="showAlertEnZuowen(comeFrom)"><span>英</span>英语作文</a>
        <a href="javascript:;" v-on:click="showAlertCnZuowen(comeFrom)"><span>文</span>语文作文</a>
    </div>
</script>

    <script type="text/html" id="tpl-score-view">
    <div class="panel-order score-view">
        <span class="type-select">
            <label class="unselect" v-bind:class="{'text-primary':score.display==1}">
                <input type="radio" name="scoreShow" value="1" v-model="score.display">显示
            </label>
            <label class="unselect" v-bind:class="{'text-primary':score.display==0}">
                <input type="radio" name="scoreShow" value="0" v-model="score.display">隐藏
            </label>
        </span>
    </div>
</script>

    <script type="text/html" id="tpl-forbid-view">
    <div class="panel-order forbid-view">
        <span class="type-select">
            <label><input type="radio" name="noWritable" value="1" v-model="display">启用</label>
            <label><input type="radio" name="noWritable" value="0" v-model="display">禁用</label>
        </span>
    </div>
</script>

    <script type="text/html" id="tpl-test-list">
    <div class="panel-order testlist">
        <ul v-for="(partItem,partItemIndex) in examPart" v-if="examPart.length>0">
            <li class="text-primary">
                <span class="left">{{partItem.title}}</span>
                <a href="javascript:;"
                   class="del-all-part"
                   v-if="partItem.list.length>0"
                   v-on:click="delAllQues(partItemIndex)">清空</a></li>
            <li v-for="(typeItem,typeItemIndex) in partItem.list">
                <a class="test-title" v-on:click="scrollThisPlace" v-bind:data-href="'#ques-anchor'+partItemIndex+'-'+typeItemIndex">{{typeItem.title}}</a>
                <span class="test-number">{{typeItem.content.length}} 题</span>
                <a href="javascript:;" v-on:click="barDelThisQuesType(partItemIndex,typeItemIndex)" class="icon" title="删除该题"></a>
            </li>
        </ul>
    </div>
</script>
<!--<i class="iconfont"></i>-->
    <script type="text/html" id="tpl-side-btm-handle">
    <div class="panel-item btn-item">
        <a href="javascript:;" class="btn btn-defult" v-on:click="saveAnswerJson">保存</a>
        <!--<a href="javascript:;" class="btn btn-defult">预览</a>-->
        <a href="javascript:;" class="btn btn-primary" v-on:click="answerDown">下载</a>
        <div class="page-total" v-html="page"></div>
    </div>
</script>

</div>



<script type="text/html" id="tpl-main">
    <div class="content clearfix" id="answer-content">
        <tpl-alert-title
                v-bind:title="title"
                v-bind:modal="alert">
        </tpl-alert-title>

        <!--编辑答题卡-->
        <alert-exam-info
                v-bind:data="data"
                v-bind:care="data.care"
                v-bind:modal="alert">
        </alert-exam-info>

        <!--编辑分卷信息-->
        <alert-part-info
                v-bind:examPart="data.paper"
                v-bind:modal="alert">
        </alert-part-info>

        <!--添加填空题-->
        <alert-tiankongti
                v-bind:modal="alert"
                v-bind:examPart="data.paper"
                v-bind:examOrderIDArray="examOrderIDArray">
        </alert-tiankongti>

        <!--选择题-->
        <alert-xuanzeti
                v-bind:modal="alert"
                v-bind:examOrderIDArray="examOrderIDArray">
        </alert-xuanzeti>

        <!--解答题-->
        <alert-jiedati
                v-bind:modal="alert"
                v-bind:examOrderIDArray="examOrderIDArray">
        </alert-jiedati>

        <!--编辑解答题-->
        <alert-edit-jiedati
                v-bind:modal="alert">
        </alert-edit-jiedati>

        <!--选答题-->
        <alert-xuandati
                v-bind:modal="alert"
                v-bind:examOrderIDArray="examOrderIDArray">
        </alert-xuandati>

        <!--编辑选答题-->
        <alert-edit-xuandati
                v-bind:modal="alert">
        </alert-edit-xuandati>

        <!--英语作文-->
        <alert-en-zuowen
                v-bind:modal="alert"
                v-bind:examOrderIDArray="examOrderIDArray">
        </alert-en-zuowen>

        <!--语文作文-->
        <alert-cn-zuowen
                v-bind:modal="alert"
                v-bind:examOrderIDArray="examOrderIDArray">
        </alert-cn-zuowen>

        <!--编辑试题title-->
        <alert-edit-ques-title
                v-bind:examPart="data.paper"
                v-bind:modal="alert">
        </alert-edit-ques-title>

        <!--添加试题弹出框-END-->
        <!--答题卡显示区-->
        <div class="left-con" id="left-con">
            <div class="content-box" id="content-box">
                <paper-style
                        v-bind:dataState="dataState"
                        v-bind:data="data"
                        v-bind:examPart="data.paper"
                        v-bind:type="data.type"
                        v-bind:care="data.care"
                        v-bind:modal="alert"
                        v-bind:title="title"
                        v-bind:score="data.score"
                        v-bind:layout="data.layout">
                </paper-style>
            </div>
        </div>
        <!--答题卡显示区-END-->
        <!-- 右侧操作 -->
        <div class="right-con" id="right-con">
            <div class="panel-item" v-if="answerType != ''">
                <div class="title">答题卡格式</div>
                <div class="answer-type">
                    <p v-if="answerType == '1'">统一答题卡</p>
                    <p v-if="answerType == '2'">AB卷</p>
                </div>
            </div>
            <div class="panel-item">
                <div class="title">选择版式</div>
                <type-style
                v-bind:layout="data.layout">

                </type-style>
            </div>
            <div class="panel-item">
                <div class="title">考试类型</div>
                <exam-type
                        v-bind:type="data.type">
                </exam-type>
            </div>
            <div class="panel-item">
                <div class="title">添加试题</div>
                <add-test
                        v-bind:modal="alert"
                ></add-test>
            </div>
            <div class="panel-item">
                <div class="title">显示分数</div>
                <score-view
                        v-bind:score="data.score">
                </score-view>
            </div>
            <!--<div class="panel-item">-->
                <!--<div class="title">设置禁止作答区</div>-->
                <!--<forbid-view>-->
                <!--</forbid-view>-->
            <!--</div>-->
            <div class="panel-item">
                <div class="title">试题列表</div>
                <test-list
                        v-bind:modal="alert"
                        v-bind:examPart="data.paper">

                </test-list>
            </div>
        </div>
        <side-btm-handle v-bind:page="computedPage"></side-btm-handle>
    </div>
</script>
<script type="text/javascript" src="/Public/plugin/webuploader/webuploader.js"></script>
<script type="text/javascript">
    seajs.use("/Public/default/js/answer");
</script>
</body>
</html>
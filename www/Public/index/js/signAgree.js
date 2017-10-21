// 在线协议
jQuery.signAgree = {
    init: function() {
        e = this;
        e.formCheck();
        e.submitForm();
    },
    // 表单验证
    formCheck: function() {
        $('.inspectionItem').blur(function() {
            var e = $(this);
            if (e.attr("data-validate")) {
                e.closest('.g-control-group').find(".g-form-msg-in").hide();
                var checkdata = e.attr("data-validate").split(',');
                var checkvalue = e.val();
                var checkstate = true;
                var checktext = "";


                for (var i = 0; i < checkdata.length; i++) {
                    var checktype = checkdata[i].split(':');
                    if (!checkFunc(e, checktype[0], checkvalue)) {
                        checkstate = false;
                        if (checkvalue == "" && e.attr("data-validate").indexOf("required") >= 0) {
                            checktext = e.attr("data-empty");
                        }
                        if (checkvalue != "" && e.attr("data-validate").indexOf("required") >= 0) {
                            checktext = checktype[1];
                        }

                    }
                }
                // 选项未填写事件处理
                if (checkstate) {
                    e.closest(".g-control-group").find(".g-form-msg-in").html("<i class='iconfont true'>&#xe631;</i>").show();
                } else if (e.hasClass("choosable") && !checkvalue) {
                    e.closest(".g-control-group").find(".g-form-msg-in").html("");
                } else {
                    e.closest(".g-control-group").find(".g-form-msg-in").html("<i class='iconfont err'>&#xe634;</i>" + checktext).show();
                }
            }
        });
        // 选择区域
        $(".selectArea").on("change", function() {
            var e = $(this);
            var selectOpt = e.parent(".selectAreaItem").find(".selectArea").last().find("option:selected");
            if (selectOpt.attr('last') != 1) {
                e.closest(".g-control-group").find(".g-form-msg-in").html("<i class='iconfont err'>&#xe634;</i>请选至地区").show();
            } else {
                e.closest(".g-control-group").find(".g-form-msg-in").html("<i class='iconfont true'>&#xe631;</i>").show();
            }
        })


        //匹配方法
        checkFunc = function(element, type, value) {
            checkVal = value.replace(/(^\s*)|(\s*$)/g, "");
            switch (type) {
                case "required":
                    return /[^(^\s*)|(\s*$)]/.test(checkVal);
                    break;
                case "chinese":
                    return /^[\u0391-\uFFE5]+$/.test(checkVal);
                    break;
                case "number":
                    return /^([+-]?)\d*\.?\d+$/.test(checkVal);
                    break;
                case "integer":
                    return /^-?[1-9]\d*$/.test(checkVal);
                    break;
                case "plusinteger":
                    return /^[1-9]\d*$/.test(checkVal);
                    break;
                case "unplusinteger":
                    return /^-[1-9]\d*$/.test(checkVal);
                    break;
                case "znumber":
                    return /^[1-9]\d*|0$/.test(checkVal);
                    break;
                case "fnumber":
                    return /^-[1-9]\d*|0$/.test(checkVal);
                    break;
                case "double":
                    return /^[-\+]?\d+(\.\d+)?$/.test(checkVal);
                    break;
                case "plusdouble":
                    return /^[+]?\d+(\.\d+)?$/.test(checkVal);
                    break;
                case "unplusdouble":
                    return /^-[1-9]\d*\.\d*|-0\.\d*[1-9]\d*$/.test(checkVal);
                    break;
                case "english":
                    return /^[A-Za-z]+$/.test(checkVal);
                    break;
                case "username":
                    return /^[a-z]\w{3,}$/i.test(checkVal);
                    break;
                case "mobile":
                    return /^\s*(15\d{9}|13\d{9}|14\d{9}|17\d{9}|18\d{9})\s*$/.test(checkVal);
                    break;
                case "phone":
                    return /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/.test(checkVal);
                    break;
                case "tel":
                    return /^((\(\d{3}\))|(\d{3}\-))?13[0-9]\d{8}?$|15[89]\d{8}?$|170\d{8}?$|147\d{8}?$/.test(checkVal) || /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/.test(checkVal);
                    break;
                case "email":
                    return /^[^@]+@[^@]+\.[^@]+$/.test(checkVal);
                    break;
                case "url":
                    return /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(checkVal);
                    break;
                case "ip":
                    return /^[\d\.]{7,15}$/.test(checkVal);
                    break;
                case "qq":
                    return /^[1-9]\d{4,10}$/.test(checkVal);
                    break;
                case "currency":
                    return /^\d+(\.\d+)?$/.test(checkVal);
                    break;
                case "zipcode":
                    return /^[1-9]\d{5}$/.test(checkVal);
                    break;
                case "chinesename":
                    return /^[\u0391-\uFFE5]{2,15}$/.test(checkVal);
                    break;
                case "englishname":
                    return /^[A-Za-z]{1,161}$/.test(checkVal);
                    break;
                case "age":
                    return /^[1-99]?\d*$/.test(checkVal);
                    break;
                case "date":
                    return /^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/.test(checkVal);
                    break;
                case "datetime":
                    return /^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-)) (20|21|22|23|[0-1]?\d):[0-5]?\d:[0-5]?\d$/.test(checkVal);
                    break;
                case "idcard":
                    return /^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/.test(checkVal);
                    break;
                case "bigenglish":
                    return /^[A-Z]+$/.test(checkVal);
                    break;
                case "smallenglish":
                    return /^[a-z]+$/.test(checkVal);
                    break;
                case "color":
                    return /^#[0-9a-fA-F]{6}$/.test(checkVal);
                    break;
                case "ascii":
                    return /^[\x00-\xFF]+$/.test(checkVal);
                    break;
                case "md5":
                    return /^([a-fA-F0-9]{32})$/.test(checkVal);
                    break;
                case "zip":
                    return /(.*)\.(rar|zip|7zip|tgz)$/.test(checkVal);
                    break;
                case "img":
                    return /(.*)\.(jpg|gif|ico|jpeg|png)$/.test(checkVal);
                    break;
                case "doc":
                    return /(.*)\.(doc|xls|docx|xlsx|pdf)$/.test(checkVal);
                    break;
                case "mp3":
                    return /(.*)\.(mp3)$/.test(checkVal);
                    break;
                case "video":
                    return /(.*)\.(rm|rmvb|wmv|avi|mp4|3gp|mkv)$/.test(checkVal);
                    break;
                case "flash":
                    return /(.*)\.(swf|fla|flv)$/.test(checkVal);
                    break;
                case "radio":
                    var radio = element.closest('form').find('input[name="' + element.attr("name") + '"]:checked').length;
                    return eval(radio == 1);
                    break;
                default:
                    var $test = type.split('#');
                    if ($test.length > 1) {
                        switch ($test[0]) {
                            case "compare":
                                return eval(Number(checkVal) + $test[1]);
                                break;
                            case "regexp":
                                return new RegExp($test[1], "gi").test(checkVal);
                                break;
                            case "length":
                                var $length;
                                if (element.attr("type") == "checkbox") {
                                    $length = element.closest('form').find('input[name="' + element.attr("name") + '"]:checked').length;
                                } else {
                                    $length = checkVal.replace(/[\u4e00-\u9fa5]/g, "***").length;
                                }
                                return eval($length + $test[1]);
                                break;
                            case "ajax":
                                var $getdata;
                                var $url = $test[1] + checkVal;
                                $.ajaxSetup({
                                    async: false
                                });
                                $.getJSON($url, function(data) {
                                    $getdata = data.getdata;
                                });
                                if ($getdata == "true") {
                                    return true;
                                }
                                break;
                            case "repeat":
                                return checkVal == jQuery('input[name="' + $test[1] + '"]').eq(0).val();
                                break;
                            default:
                                return true;
                                break;
                        }
                        break;
                    } else {
                        return true;
                    }
            }
        };

    },
    submitForm: function() {
        var submitMyData = $("#submitMyData");
        submitMyData.on("click", function(event) {

            var realName = $("#realName").val();
            var qqID = $("#qqID").val();
            var phonecode = $("#phonecode").val();
            var telcode = $("#telcode").val();
            var idcardID = $("#idcardID").val();
            var idcardFile = 'idcardFile';
            var addressID1 = $("#addressID1").val();
            var areaID1 = $("#areaID1").val();
            var address1 = $("#address1").val();
            var zipcode1 = $("#zipcode1").val();
            var addressID2 = $("#addressID2").val();
            var areaID2 = $("#areaID2").val();
            var address2 = $("#address2").val();
            var zipcode2 = $("#zipcode2").val();
            var openingBank = $("#openingBank").val();
            var accountName = $("#accountName").val();
            var bankAccount = $("#bankAccount").val();

            var data = {
                'RealName': realName,
                'QQ': qqID,
                'Phonecode': phonecode,
                'Telcode': telcode,
                'IdcardID': idcardID,
                'IdcardFile': idcardFile,
                'AddressID1': addressID1,
                'AreaID1': areaID1,
                'Address1': address1,
                'Zipcode1': zipcode1,
                'AddressID2': addressID2,
                'AreaID2': areaID2,
                'Address2': address2,
                'Zipcode2': zipcode2,
                'OpeningBank': openingBank,
                'AccountName': accountName,
                'BankAccount': bankAccount
            }
            var e = $(this);
            event.stopPropagation();
            $(".inspectionItem").blur();
            console.log(data);
        })


    },
    selectFile: function() {


    }
};
$(function() {
    var UploadIDStr = $("#shoujiErweimaUploadID").html();
    $("#byErweima").on("click", function() {
        layer.open({
            type: 1,
            shift: 5,
            title: "扫描二维码",
            shade: 0.6,
            shadeClose: true,
            content: UploadIDStr
        })
    });
    // 点击上传按钮事件
    var uploadIDcard = $("#uploadIDcard");
    var uploadIDcardDef = $("#uploadIDcardDef");
    var delThisPic = $(".delThisPic");
    var viewPicTips = $(".viewPicTips");
    var showBigPic = $(".showBigPic");
    var bigPicDemo = $("#bigPicDemo");
    var uploadBtn = $(".uploadBtn");
    uploadIDcard.click(function() {
        uploadIDcardDef.click();
    });
    // 删除已上传图片
    delThisPic.on("click", function() {
        var e = $(this);
        e.parent(".view-left").find("img").removeAttr("src");
        e.hide();
        uploadBtn.hide();
        viewPicTips.show();

    });

    /**
     * 从 file 域获取 本地图片 url
     */
    function getFileUrl(sourceId) {
        var url;
        if (navigator.userAgent.indexOf("MSIE") >= 1) {
            url = document.getElementById(sourceId).value;
        } else if (navigator.userAgent.indexOf("Firefox") > 0) {
            url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0));
        } else if (navigator.userAgent.indexOf("Chrome") > 0) {
            url = window.URL.createObjectURL(document.getElementById(sourceId).files.item(0));
        }
        return url;
    }

    /**
     * 将本地图片 显示到浏览器上
     */
    function preImg(sourceId, targetId) {
        var url = getFileUrl(sourceId);
        var imgPre = document.getElementById(targetId);
        imgPre.src = url;
    }

    // 选中图片上传
    uploadIDcardDef.on("change", function() {
        preImg(this.id,'imgPre')
        // var src = "../../imgs/agree/idcard.jpg";
        // $("<img src=" + src + ">").appendTo("#viewPicSite");
        if(uploadIDcardDef.val()){
            viewPicTips.hide();
            delThisPic.show();
            uploadBtn.show();
        }else{
            viewPicTips.show();
            uploadBtn.hide();
            delThisPic.hide();
        }
    });
    // 示例详情
    showBigPic.on("click", function() {
        layer.open({
            type: 1 //不自动关闭
                ,
            area: ['auto', 'auto'],
            shift: 5,
            title: false,
            closeBtn: 2,
            shade: 0.6,
            shadeClose: true,
            content: "<img src=" + bigPicDemo.attr('src') + " height='600' width='474'>"
        })
    });
    //        签名输入事件
    var signErweima = $("#signErweima");
    var clientSignSite = $("#clientSignSite");
    var signWatermark = $("#signWatermark");
    var submitSign = $("#submitSign");
    signErweima.on("click", function() {
        clientSignSite.hide();
        signWatermark.show();
        submitSign.removeClass("btn-disabled");
    })
});

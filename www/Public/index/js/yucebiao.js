var htmlCodes = [
'{{each DotList as value i}}',
'{{if DotList[i].DotTit }}',
'    <li>',
'        <h3 class="row"><span>{{DotList[i].DotTit}}</span></h3>',
'        <p class="row analyze"><span>考向分析：{{DotList[i].DotAnalyze}}</span></p>',
'        <div class="row content">',
'            <div class="ques-attr">',
'                <cite class="round',
'                {{if (DotList[i].Frequency >= 5 && (Subject=="zhengzhi" || Subject=="wuli" || Subject=="huaxue"))}}',
'                 bg-red',
    '                {{else if (DotList[i].Frequency >= 4 && Subject=="lishi")}}',
    '                 bg-red',
    '                {{else if (DotList[i].Frequency >= 6 && (Subject=="dili" || Subject=="yuwen" || Subject=="shengwu"))}}',
    '                 bg-red',
    '                {{else if (DotList[i].Frequency >= 8 && (Subject=="shuli" || Subject=="shuwen"))}}',
    '                 bg-red',
'                {{else}}',
'                 bg-grey',
'                {{/if}}',
'                ">{{DotList[i].Frequency}}</cite><br>',
'                <span>必考点提示</span>',
'            </div>',
'            <div class="ques-attr">',
'                <cite class="round',
'                {{if (DotList[i].BlindSpot <= 3 && (Subject=="zhengzhi" || Subject=="dili" || Subject=="shuli" || Subject=="shuwen" || Subject=="shengwu"))}}',
'                 bg-yellow',
    '                {{else if (DotList[i].BlindSpot <= 2 && (Subject=="yuwen"))}}',
    '                 bg-yellow',
    '                {{else if (DotList[i].BlindSpot <= 2.33 && (Subject=="wuli"))}}',
    '                 bg-yellow',
    '                {{else if (DotList[i].BlindSpot <= 1.5 && (Subject=="huaxue"))}}',
    '                 bg-yellow',
    '                {{else if (DotList[i].BlindSpot <= 6 && (Subject=="lishi"))}}',
    '                 bg-yellow',
'                {{else}}',
'                 bg-grey',
'                {{/if}}',
'                ">{{DotList[i].BlindSpot}}</cite><br>',
'                <span>易失分提示</span>',
'            </div>',
'        </div>',
'        <div class="row ques-attr ques-type">',
'            <cite>常考题型：</cite>',
'            <span>{{ DotList[i].QuesType }}</span>',
'        </div>',
'        <div class="row ques-attr ques-num">',
'            <cite>密卷考题索引：</cite>',
'            <span>{{DotList[i].QuesNum}}</span>',
'        </div>',
'        <div class="row ques-attr ques-diff">',
'            <cite>难易程度：</cite>',
'            {{if DotList[i].Diff == 1}}',
'                <span class="i-star-s">',
'                    <a class="star-a"></a>',
'                    <a class="star-c"></a>',
'                    <a class="star-c"></a>',
'                </span>',
'            {{else if DotList[i].Diff == 2}}',
'                <span class="i-star-s">',
'                    <a class="star-a"></a>',
'                    <a class="star-a"></a>',
'                    <a class="star-c"></a>',
'                </span>',
'            {{else if DotList[i].Diff == 3}}',
'                <span class="i-star-s">',
'                    <a class="star-a"></a>',
'                    <a class="star-a"></a>',
'                    <a class="star-a"></a>',
'                </span>',
'            {{else}}',
'                <span class="i-star-s">',
'                    <a class="star-c"></a>',
'                    <a class="star-c"></a>',
'                    <a class="star-c"></a>',
'                </span>',
'            {{/if}}',
'        </div>',
// '        <div class="row down-btn">',
// '            <a href="javascript:;" class="btn">下载app,巧练考点专项</a>',
// '        </div>',
'    </li>',
'{{/if}}',
'{{/each}}'
].join("");

// 使用 Mock
// 模拟数据
// var Ran = Mock.Random;
// var data = Mock.mock({
//     "Subject":"wenkeshuxue",
//     "DotList": []
// });

// 添加一条数据
// function addArr() {
//     // 数据条数
//     var count = 40;

//     for (var i = 0; i < count; i++) {
//         var dataArr = Mock.mock([{
//             "DotTit": Mock.Random.ctitle(3, 30),
//             "DotAnalyze": Mock.Random.csentence(1, 40),
//             "Frequency": Mock.Random.float(1, 9, 0, 2),
//             "BlindSpot": Mock.Random.float(1, 9, 0, 2),
//             // "Score": Mock.Random.natural(1, 20),
//             // "QuesCount": Mock.Random.natural(1, 20),
//             "QuesType": Mock.Random.ctitle(3, 10),
//             "QuesNum": Mock.Random.natural(1, 20),
//             "Diff":Mock.Random.natural(1, 3)
//         }]);
//         data.DotList = data.DotList.concat(dataArr);
//     }
//     // console.log(data);

//     data.ratio = {"Freq":1,"Blin":1};
//     // 设置学科比值转换关系

//     var checkSubject = function(sub){
//         switch(sub){
//             case 'yuwen':
//             return data.ratio = {"Freq":2,"Blin":1};
//             break;
//             case 'wenkeshuxue':
//             return data.ratio = {"Freq":3,"Blin":1};
//             break;
//             case 'likeshuxue':
//             return data.ratio = {"Freq":4,"Blin":1};
//             break;
//             case 'yingyu':
//             return data.ratio = {"Freq":5,"Blin":1};
//             break;
//             case 'wuli':
//             return data.ratio = {"Freq":6,"Blin":1};
//             break;
//             case 'huaxue':
//             return data.ratio = {"Freq":7,"Blin":1};
//             break;
//             case 'shengwu':
//             return data.ratio = {"Freq":1,"Blin":1};
//             break;
//             case 'zhengzhi':
//             return data.ratio = {"Freq":1,"Blin":1};
//             break;
//             case 'lishi':
//             return data.ratio = {"Freq":1,"Blin":1};
//             break;
//             case 'dili':
//             return data.ratio = {"Freq":1,"Blin":1};
//             break;
//         }
//     };
//     checkSubject(data["Subject"]);
// }
// addArr();

// 输出结果
// artTemplate

if(window.data && data.Subject){
    var render = template.compile(htmlCodes);
    var html = render(data);
    $("#subjectContent").append(html);
}

// 指定显示条数
var content = $("#subjectContent");
var listNum = content.find("li").length;
var count = 0;
// 显示条数
var showNum = 18;
var listNumVi = 0;

// 渲染数据
function htmlRander() {
    if (count < listNum && listNumVi < listNum) {
        for (var i = 0; i < showNum && i < listNum; i++) {
            content.find("li").eq(count + i).fadeIn();
        }
    } else {
        $(".no-more").show();
    }
    count += 12;
    listNumVi = content.find("li:visible").length;

}
htmlRander();
// 加载更多
// $("#load").click(function() {
//     htmlRander();
// });


// 修改下载链接地址
var ua = navigator.userAgent.toLowerCase();
var IOS = {"href":"http://www.tk.com/Aat/App/index.html"};
var android = {"href":"http://www.tk.com/Aat/App/index.html"};
function checkDevice() {
    var downloadBtn = $("#subjectContent").find(".btn");
    btnUrl = downloadBtn.attr("href");
    if (/iphone|ipad|ipod/.test(ua) && btnUrl == 'javascript:;') {
        downloadBtn.attr(IOS);
    } else {
        downloadBtn.attr(android);
    }
};
checkDevice();

// 滚动加载更多
var scrHeight;
var wHeight = $(window).height();
var docHeight;
var offHeight = 400;

$(document).scroll(function() {
    scrHeight = $(document).scrollTop();
    docHeight = $(document).height();
    if (scrHeight > docHeight - wHeight - offHeight) {
        htmlRander();
        // checkDevice();
    }
});

// 设置导航当前页面样式
function checkNavBar(subject) {
    var item = $("#navBar").find("a");
    item.each(function() {
        var e = $(this);
        mark = e.attr("data-mark");
        if (mark == subject) {
            e.closest("li").addClass("on").siblings("li").removeClass("on");
        }
    })
}

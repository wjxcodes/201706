function getPagtionHtml(count, prepage){
    var prevPage = page - 1;
    var totalPage = Math.ceil(count / prepage);
    var nextPage = ((page >= totalPage) ? totalPage : page) + 1;
    return '查询到&nbsp;&nbsp;'+count+'&nbsp;&nbsp;道试题&nbsp;&nbsp;共&nbsp;'+totalPage+'&nbsp;页&nbsp;&nbsp;当前第&nbsp;'+page+'&nbsp;页<a href="#" title="上一页" style="margin-left:5px;" page="'+prevPage+'" aspect="u">&lt;</a>&nbsp;<a href="#" title="下一页" aspect="d" page="'+nextPage+'" totalPage="'+totalPage+'">&gt;</a>';
}


function setConfig(cfg){
    if(cfg['formId'] && cfg['formId'] != config.formId){
        hideForm();
    }
    config = $.extend(config,cfg);
}

 //表相关配置项
var config = {
    'formId' : '#doSomeThing', //表单id
    'appendToAfterZone' : '#result', //在此区域之前显示添加表单
    'editForm' : 'editForm', //在此区域内显示编辑表单
    //生成编辑表单的父类tr
    'editZone' : '<tr class="editForm"><td colspan="9"></td></tr>',
    'primaryKey' : 'SID'
}
//填充数据到表单
function fillData(data){
    var form = $(config.formId);
    for(var d in data){
        form.find('[name="'+d+'"]').val(data[d]);
    }
    form.find('#primaryKeyId').val(data[config.primaryKey]);
}
//隐藏表单
function hideForm(){
    var form = $(config.formId);
    var _parent = hasEditForm();
    if(_parent !== false){
        $(config.appendToAfterZone).before(form);
        _parent.remove();
    }
    form.hide();
}
//显示表单
function showForm(obj){
    var form = $(config.formId);
    hideForm();
    if(!obj){
        if(hasEditForm() !== false || form.css('display').toLowerCase() == 'none'){
            clearForm(form);
        }
        form.show();
        return;
    }
    //判断当前表单是否是已编辑状态
    var _parent = obj.parents('tr');
    if(_parent.next().hasClass(config.editForm)){
        return false;
    }
    var tr = $(config.editZone);
    form.show().appendTo(tr.find('td'));
    _parent.after(tr);
    return false;
}

//表单是否存在编辑状态
function hasEditForm(){
    var form = $(config.formId);
    var _parent = form.parents('.'+config.editForm);
    if(_parent.length > 0){
        return _parent; 
    }
    return false;
}

//清理值，待完善
function clearForm(form){
    form.find('input').each(function(){
        var _that = $(this);
        //如果该值不是固定的值，将初始话为空
        if(!_that.hasClass('fixedVal')){
            _that.val('');
        }
    });
}
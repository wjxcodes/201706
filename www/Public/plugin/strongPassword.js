/**
 * @author demo
 * @date 2015年10月22日
 */
(function() {
    /**
     * 密码强度插件
     * 使用示例：
     * $('input:password#password').strongPassword();
     * @param options 插件参数
     * @author demo
     */
    $.fn.strongPassword = function (options) {
        var config = $.extend({}, $.fn.strongPassword.defaults, options);
        var self = $(this);
        //满分30分
        var validationRules = {
            'passwordLength': function (word) {
                var wordLen = word.length;
                if(wordLen<6){
                    return -30;
                }
                if(wordLen<8) return 1;
                if(wordLen<10) return 3;
                else return 4;
            },
            'lowercase': function (word) {
                return word.match(/[a-z]/) && 1;
            },
            'uppercase': function (word) {
                return word.match(/[A-Z]/) && 3;
            },
            'one_number': function (word) {
                return word.match(/\d+/) && 3;
            },
            'one_special_char': function (word) {
                return word.match(/.[!,@,#,$,%,\^,&,*,?,_,~]/) && 3;
            },
            'two_special_char': function (word) {
                return word.match(/(.*[!,@,#,$,%,\^,&,*,?,_,~].*[!,@,#,$,%,\^,&,*,?,_,~])/) && 5;
            },
            'upper_lower_combo': function (word) {
                return word.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/) && 3;
            },
            'letter_number_combo': function (word) {
                return word.match(/([a-zA-Z])/) && word.match(/([0-9])/) && 4;
            },
            'letter_number_char_combo' : function (word) {
                return word.match(/([a-zA-Z0-9].*[!,@,#,$,%,\^,&,*,?,_,~])|([!,@,#,$,%,\^,&,*,?,_,~].*[a-zA-Z0-9])/) && 4;
            }
        };
        //密码改变时
        function trigger(){
            var passwordLevel = getPasswordStrength(self.val());
            toggleImg(passwordLevel);
        }
        //改变强度css
        function toggleImg(level){
            var levelDom = $('.'+config.levelClass),domIndex = 0;
            levelDom.find('cite').removeClass();
            if(level<34){
                levelDom.find('cite:lt(1)').addClass('on-weak');
            }else if(level<67){
                levelDom.find('cite:lt(2)').addClass('on-medium');
            }else{
                levelDom.find('cite:lt(3)').addClass('on-high');
            }

        }
        //计算密码强度，返回0-100值
        function getPasswordStrength(password){
            var passwordLevel = 0;
            $.each(validationRules,function(i,k){
                passwordLevel += k(password);
            });
            return parseInt(passwordLevel/30*100);
        }
        self.on('keyup', trigger).on('blur', trigger);
    };
    /**
     * 图片放大插件默认参数
     * levelClass 等级dom的class名
     * @author demo
     */
    $.fn.strongPassword.defaults = {
        levelClass:'level'
    };
})();

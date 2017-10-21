(function (window, document) {
	
    var layout   = document.getElementById('layout'),
        menu     = document.getElementById('menu'),
        menuLink = document.getElementById('menuLink');

    function toggleClass(element, className) {
        var classes = element.className.split(/\s+/),
            length = classes.length,
            i = 0;

        for(; i < length; i++) {
          if (classes[i] === className) {
            classes.splice(i, 1);
            break;
          }
        }
        // The className is not found
        if (length === classes.length) {
            classes.push(className);
        }

        element.className = classes.join(' ');
    }

    menuLink.onclick = function (e) {
        var active = 'active';

        e.preventDefault();
        toggleClass(layout, active);
        toggleClass(menu, active);
        toggleClass(menuLink, active);
    };

}(this, this.document));

$("#goTop").click(function(){
    // $(document.body||document.documentElement).scrollTop=0;
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
});
window.onscroll = function () {
    var x = document.body.scrollTop || document.documentElement.scrollTop;
    if (x < 1)$("#goTop").css("display","none");
    else $("#goTop").css("display","block");
};


(function(a){a.fn.centerImage=function(g,f){f=f||function(){};var c=this;var e=a(this).length;g=g=="inside";var d=function(i){var j=a(i);var h=j.parent();
h.css({overflow:"hidden",position:h.css("position")=="absolute"?"absolute":"relative"});j.css({position:"static",width:"auto",height:"auto","max-width":"100%","max-height":"100%"});
var k={w:h.width(),h:h.height(),r:h.width()/h.height()};var i={w:j.width(),h:j.height(),r:j.width()/j.height()};j.css({"max-width":"none","max-height":"none",width:Math.round((k.r>i.r)^g?"100%":k.h/i.h*i.w),height:Math.round((k.r<i.r)^g?"100%":k.w/i.w*i.h)});
var k={w:h.width(),h:h.height()};var i={w:j.width(),h:j.height()};j.css({position:"absolute",left:Math.round((k.w-i.w)/2),top:Math.round((k.h-i.h)/3)});
b(i);};var b=function(h){e--;f.apply(c,[h,e]);};return c.each(function(h){if(this.complete||this.readyState==="complete"){(function(i){setTimeout(function(){d(i);
},1);})(this);}else{(function(i){a(i).one("load",function(){setTimeout(function(){d(i);},1);}).one("error",function(){b(i);}).end();if(navigator.userAgent.indexOf("Trident/5")>=0||navigator.userAgent.indexOf("Trident/6")){i.src=i.src;
}})(this);}});};a.fn.imageCenterResize=function(b){return a(this).centerImage("inside",b);};a.fn.imageCropFill=function(b){return a(this).centerImage("outside",b);
};})(jQuery);

jQuery.fn.exist = function(){ return jQuery(this).length > 0; }
jQuery(function($){
	if ($('.bxslider').exist()) {
	    $('.bxslider').bxSlider({
	        auto: true,
	        pause: 3000,
	        pager: false
	    });
	}
	if ($('.input-qty').exist()) {
		$('.input-qty').TouchSpin();
	}
    if ($(window).width() > 750) {
    	$('.link-p img').centerImage();
    }
    $(window).resize(function(){
    	var width = $(this).width();
    	if (width > 750) {
    		$('.link-p img').centerImage();
    		$('.link-p img').removeClass('def-img');
    	} else {
    		$('.link-p img').addClass('def-img');
    	}
    })
    $(window).scroll(function(){
		if ($(this).scrollTop()>70) {
			$('.back-top').fadeIn();
		} else {
			$('.back-top').fadeOut();
		}
	});

	$('#ship_to_billing').bind('change', function(){        
        var checked = this.checked;

        if(checked) {
        	$('.checkout-fields-shipping .form-group input, .checkout-fields-shipping .form-group select').attr('disabled', 'disabled').parent('.form-group').hide();
        } else {
        	$('.checkout-fields-shipping .form-group input, .checkout-fields-shipping .form-group select').attr('disabled', false).parent('.form-group').show();
        }
	});
})
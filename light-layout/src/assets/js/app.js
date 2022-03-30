$(function() {

    var $speedMenuAnimation = 125;

    //controle do sidebar
	if ($.cookie('sidebar_closed') == 1) {
	 	$('body').addClass('page-sidebar-closed');
		$('.sidebar-toggler-userinfo').hide();
	} else {
		$('.sidebar-toggler-userinfo').show();
	}

    $('.sidebar-toggler').click(function(event) {  	
	 	if ($('body').hasClass('page-sidebar-closed')) {
    	 	$('body').removeClass('page-sidebar-closed');
            $('.sidebar-toggler-userinfo').show();
            $.cookie('sidebar_closed', '0', {path : '/'});
        } else {
    	 	$('body').addClass('page-sidebar-closed');
            $('.sidebar-toggler-userinfo').hide();
            $.cookie('sidebar_closed', '1', {path : '/'});
    	 }
    });

    // funcionamento do menu
    // jakjr: nao é utilizado cookie para verificar qual menu esta ativo: see Menu.php:69
    /*
	var cookie_sidebar_menu = function() {
	    openItems = new Array();
        $('.page-sidebar-menu li').each(function(index, item) {
            if ($(item).hasClass('active')) {
                openItems.push(index);
            }
        });
        $.cookie('sidebar_menu', openItems.join(','), {path : '/'});
	}*/



	if( $.cookie('sidebar_menu') && $.cookie('sidebar_menu').length > 0 && !$('.page-sidebar-menu').children('li').hasClass('active')) {
	    previouslyOpenItems = $.cookie('sidebar_menu');
	    openItemIndexes = previouslyOpenItems.split(',');
	    $(openItemIndexes).each(function(index, item) {
	    	$('.page-sidebar-menu li').eq(item).addClass('active');
			$('.page-sidebar-menu li').eq(item).addClass('open');				 
	    	$('.page-sidebar-menu li').eq(item).children('a').children('span').addClass('open');
	    });
	}

    $('.page-sidebar-menu li a').click(function(){ 

    	var li = $(this).parent();

    	if (li.children('.sub-menu').length > 0) {

			li.children('a').attr('href','#'); // tirar o link se for item pai

    		if (li.parent().hasClass('page-sidebar-menu')) {
		    	$('.page-sidebar-menu li').each(function(index) {
		    		if($(this).get(0) !== li.get(0)) {
		    			$(this).children('a').children('span').removeClass('open');
		    			$(this).children('.sub-menu').slideUp($speedMenuAnimation);
						$(this).removeClass('open');
   					}
			    });
		   	}
	   		if (li.hasClass('open')) {
	    		$(this).children('span').removeClass('open');
	    		li.children('.sub-menu').slideUp($speedMenuAnimation);
	    		li.removeClass('open');
	    	} else {
    			$(this).children('span').addClass('open');
   				li.children('.sub-menu').slideDown($speedMenuAnimation);
				li.addClass('open');
	    	}
		} else {
			$('.page-sidebar-menu li').each(function() {
				$(this).removeClass('open');
			});
			$(this).parents('li').addClass('open');
			li.addClass('open');
		}

        //cookie_sidebar_menu();
    });

    // ir para o topo
    $('.go-top').click(function() {
            $('html,body').animate({scrollTop: $('.page-header-fixed').offset().top}, $speedMenuAnimation);
    });
});

/*
$(document).ready(function()
{
    try {
        var test = $('input[type=checkbox]:not(.toggle), input[type=radio]:not(.toggle, .star)');
        if (test.size() > 0) {
            test.each(function() {
                if ($(this).parents('.checker').size() == 0) {
                    $(this).uniform();
                }
            });
        }
    }
    catch (e){}
});
*/
$(document).ready(function () {

    $('.rgumymoodle_outertabs').easyResponsiveTabs({
        type: 'default',                        // Types: default, vertical, accordion
        width: 'auto',                          // auto or any width like 600px
        fit: true,                              // 100% fit in a container
        tabidentify: 'hor_1',                   // The tab groups identifier
    });

    $('.rgumymoodle_innertabs').easyResponsiveTabs({
        type: 'vertical',
        width: 'auto',
        fit: true,
        tabidentify: 'ver_1',                   // The tab groups identifier
        activetab_bg: '#fff',                   // background color for active tabs in this group
        inactive_bg: '#F5F5F5',                 // background color for inactive tabs in this group
        active_border_color: '#c1c1c1',         // border color for active tabs heads in this group
        active_content_border_color: '#5AB1D0'  // border color for active tabs contect in this group so that it matches the tab head border
    });
    
    
    $('.rgu_course_overview_page_flip').click(function(e) {
    	e.preventDefault();
     	$(this).parent('div').children("a").removeClass('rgu_course_overview_paginator_selected');
        $(this).addClass('rgu_course_overview_paginator_selected'); 
    	var parentid = ($(this).parent().parent().attr('id'));
    	var showthis = parentid+($(e.target).text());
    	$('#'+parentid+' .rgucoursepanel').hide();
    	$('#'+parentid+' .rgu_courseoverview_pagination_furniture').show();
    	$('#'+showthis).show();
    	return false;
    });
    
    $('.rgu_courseoverview_showall').click(function(e) {
    	e.preventDefault();
     	$(this).parent('div').children("a").removeClass('rgu_course_overview_paginator_selected');
        $(this).addClass('rgu_course_overview_paginator_selected'); 
    	var parentid = ($(this).parent().parent().attr('id'));
    	$('#'+parentid+' .rgucoursepanel').show();
    	$('#'+parentid+' .rgu_courseoverview_pagination_furniture').hide();
    });
    
    
});

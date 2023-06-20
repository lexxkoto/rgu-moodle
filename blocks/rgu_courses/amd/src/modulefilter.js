define(['jquery'], function($) {
    return {
        init: function() {
            $('.block_rgu_courses .yearTitle a').on("click", function(e) {
                $(this).children("i").toggleClass("fa-caret-right fa-caret-down");
                e.stopPropagation();
            });
            $('#rgu_course_search').on("keyup", function() {
                var searchQuery = $(this).val().toLowerCase();
                $("#courses_tab_all .rgu-course-list li").filter(function() {
                  $(this).toggle($(this).text().toLowerCase().indexOf(searchQuery) > -1);
                });
            });
        }
    };
});

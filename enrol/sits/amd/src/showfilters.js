define(['jquery'], function($) {
    return {
        init: function() {
            $('#showRuleFilterBox').click(function() {
                $('#ruleFilterEnabler').slideUp();
                $('#ruleFilterBox').slideDown();
            });
        }
    };
});

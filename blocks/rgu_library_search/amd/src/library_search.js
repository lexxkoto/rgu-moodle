define(['jquery'], function($) {
    return {
        init: function() {
            $('#rguls-go').click(function() {
                document.getElementById("primoQuery").value = "any,contains," + document.getElementById("primoQueryTemp").value.replace(/[,]/g, " ");
                $('#rguls-form').submit();
            });
        }
    };
});
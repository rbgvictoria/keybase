$(function(){
    $('form#load_image').on("submit", function(event) {
        return false;
        $.ajax({
            type: "POST",
            url: "http://www.rbg.vic.gov.au/dbpages/dev/4fde7c4fa5200/index.php/key/st_load_image",
            success: function(response) {
                $('#loaded-images').append('<div>' + response + '</div>');
                $.colorbox({open: true});
            }
        })
    });
});
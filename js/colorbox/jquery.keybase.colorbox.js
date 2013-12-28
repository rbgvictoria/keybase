$(function(){
    $(".colorbox_ajax").colorbox({
        opacity: 0.40, 
        transition: 'none', 
        speed: 0,
        innerWidth: 506,
        maxHeight: '80%',
        onLoad: function() {
            $('#cboxClose').hide();
        },
        onComplete: function() {
            $('#colorbox button[name="cancel"]').click(function(e) {
                e.preventDefault();
                $.colorbox.close();
            });
        }
    });
    
    $(".colorbox_load_image").colorbox({
        iframe: true,
        width: 800,
        height: 800
    });
    
    /*$("#edit-key a").click(function () {
        var cbox_href = $(this).attr('href');
        $(this).attr('href', cbox_href + '/cbox');
        $(this).colorbox({
            opacity: 0.40, 
            transition: 'elastic', 
            speed: 100,
            innerWidth: 860,
            innerHeight: "80%",
            close: 'close',
            onLoad: function() {
                $('#cboxClose').hide();
            },
            onComplete: function() {
                $('#colorbox').addClass('edit-project');
                $('#colorbox input[name="cancel"]').click(function(e) {
                    e.preventDefault();
                    $.colorbox.close();
                });
                $('input[type="submit"]').button();
            }
        });
    });*/
    
    
});



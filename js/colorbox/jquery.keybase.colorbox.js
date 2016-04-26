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
    
});



$(function(){
        $("#linkedkeys li, #orphanedkeys li").attr('data', 'expand:true');

        $("#linkedkeys, #orphanedkeys").dynatree({
            //autoCollapse: true,
            onActivate: function(node) {
                    if (node.data.href) {
                            window.location.href=node.data.href;
                    }
            }
        });

        $('#linkedkeys li, #orphanedkeys li').each(function() {
                $(this).addClass('key');
        });
});

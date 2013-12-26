$(function () {
    $('input[name=delimiter]').change(function () {
        var href = location.href;
        href = href.replace('/cbox', '');
        href = href.replace('/editkey/', '/getinputkey/');
        var tempfilename = $('input[name=tempfilename]').val();
        var keyid = $('input[name=keyid]').val();
        var delimiter = $(this).val();
        var url = href + '/' + tempfilename + '/' + delimiter;
        
        $('#input_key').load(url);
        
    });
});
(function($){
    $('#myform').submit(function(e){
        var val = $('#in').val();
        $('ul.list').append('<li>' + val + '</li>');
        e.preventDefault();
    });
})(jQuery);

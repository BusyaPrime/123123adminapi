$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    // autosize($('textarea'));
    $('#open_filter').on('click', function () {
        $(this).hide();
        $('#filter_block').show();
        $('#close_filter').show();
    });
    $('#close_filter').on('click', function () {
        $(this).hide();
        $('#filter_block').hide();
        $('#open_filter').show();
    });
    $('.redirect-show-page').on('click', function () {
        var url = $(this).data('url');
        window.location.href = url;
    });

});

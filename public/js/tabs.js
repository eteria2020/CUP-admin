$(document).ready(function () {

    var idTab = '#tab-' + tab;
    $('#menu-' + tab).addClass('active');
    $(idTab).addClass('active');

    var href = $(document).find('#js-tabs .active a').attr("href");

    $(idTab).load(href,function(e){
      $('#js-tabs .active a').tab('show');
    });

    $('#js-tabs a').click(function(e) {
        var _this = $(this);
        var loadurl = _this.attr('href');
        var targ = _this.attr('data-target');

        $.get(loadurl, function(data) {
            $(targ).html(data);
        });

        _this.tab('show');
        return false;
    });
});

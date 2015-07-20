$(function()
{
    $("#js-value").keyup(function(event)
    {
        if (event.keyCode == 13) {
            $("#js-search").click();
        }
    });
});

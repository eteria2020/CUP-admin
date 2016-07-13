function loginRedirect(data, textStatus, jqXHR)
{
    /*
     * This is triggered when the session expires since the returned data is
     * an html page and not a proper response for the ajax call. Probably not
     * the best solution as other errors may cause this to be triggered.
     * Consider this a workaround.
     */
    if (textStatus == 'parsererror') {
        window.location.replace('/user/login');
    }
}

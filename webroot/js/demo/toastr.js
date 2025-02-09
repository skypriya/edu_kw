$(function() {
    toastr.options = {
        closeButton: !1,
        debug: $("#debugInfo").prop("checked"),
        progressBar: !0,
        preventDuplicates: $("#preventDuplicates").prop("checked"),
        positionClass: "toast-top-right",
        onclick: null,
        bodyOutputType: 'trustedHtml'
    }, $("#succmsg").html() && toastr.success("", $("#succmsg").html()), $("#errmsg").html() && toastr.error("", $("#errmsg").html())
});
function viewAkcessDatatables(type) {
    var vid = $("#ViewSendEformData #vieweid").val();
    var vt = $("#ViewSendEformData #viewType").val();
    $.ajax({
        type: 'POST',
        url: burl + "/i-d-card/getResponseData?type=" + type + "&vid=" + vid + "&vt=" + vt,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        cache: false,
        success: function(result)
        {
            if (type == 'viewemail') {
                $('#viewSentEmail').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Email"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]],
                });
                $("#viewSentEmail").css('width','100%');
            } else if (type == 'viewphone') {
                $('#viewSentPhone').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Phone No"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentPhone").css('width','100%');
            } else if (type == 'viewackess') {
                $('#viewSentAkcess').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "AkcessID"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentAkcess").css('width','100%');
            }
        }
    });
}

function viewReceivedAkcessIDCardDatatables() {
    var vid = $("#viewReceivedIDCardtData #viewReceivedid").val();
    var vt = $("#viewReceivedIDCardtData #viewType").val();
    $.ajax({
        type: 'POST',
        url: burl + "/i-d-card/getReceivedResponseData?vid=" + vid + "&vt=" + vt,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        cache: false,
        success: function(result)
        {
            $('#viewReceivedIDCardAkcess').DataTable({
                aaData: result, //here we get the array data from the ajax call.
                bDestroy: true,
                aoColumns: [
                    {sTitle: "AkcessID"},
                    {sTitle: "FullName"},
                    {sTitle: "Phone no"},
                    {sTitle: "Email"},
                    {sTitle: "Status"},
                    {sTitle: "Date"}
                ],
                order: [[ 5, "desc" ]]
            });
            $("#viewReceivedIDCardAkcess").css('width','100%');
        }
    });
}

function viewReceivedAkcessDocumentDatatables() {
    var vid = $("#viewReceivedDocumentData #viewReceivedid").val();
    var vt = $("#viewReceivedDocumentData #viewType").val();
    $.ajax({
        type: 'POST',
        url: burl + "/i-d-card/getReceivedResponseData?vid=" + vid + "&vt=" + vt,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        cache: false,
        success: function(result)
        {
            $('#viewReceivedAkcess').DataTable({
                aaData: result, //here we get the array data from the ajax call.
                bDestroy: true,
                aoColumns: [
                    {sTitle: "AkcessID"},
                    {sTitle: "FullName"},
                    {sTitle: "Phone no"},
                    {sTitle: "Email"},
                    {sTitle: "Status"},
                    {sTitle: "Date"}
                ],
                order: [[ 5, "desc" ]]
            });
            $("#viewReceivedAkcess").css('width','100%');
        }
    });
}

function viewAkcessDocumentDatatables(type) {
    var vid = $("#ViewSendDocumentData #vieweid").val();
    var vt = $("#ViewSendDocumentData #viewType").val();
    $.ajax({
        type: 'POST',
        url: burl + "/i-d-card/getResponseData?type=" + type + "&vid=" + vid + "&vt=" + vt,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        cache: false,
        success: function(result)
        {
            if (type == 'viewemail') {
                $('#viewSentEmail').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Email"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentEmail").css('width','100%');
            } else if (type == 'viewphone') {
                $('#viewSentPhone').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Phone No"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentPhone").css('width','100%');
            } else if (type == 'viewackess') {
                $('#viewSentAkcess').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "AkcessID"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentAkcess").css('width','100%');
            }
        }
    });
}

function viewAkcessIDCardDatatables(type) {
    var vid = $("#ViewSendIDCardData #vieweid").val();
    var vt = $("#ViewSendIDCardData #viewType").val();
    $.ajax({
        type: 'POST',
        url: burl + "/i-d-card/getResponseData?type=" + type + "&vid=" + vid + "&vt=" + vt,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        cache: false,
        success: function(result)
        {
            if (type == 'viewemail') {
                $('#viewSentIDCardEmail').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Email"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentIDCardEmail").css('width','100%');
            } else if (type == 'viewphone') {
                $('#viewSentIDCardPhone').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Phone No"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentIDCardPhone").css('width','100%');
            } else if (type == 'viewackess') {
                $('#viewSentIDCardAkcess').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "AkcessID"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentIDCardAkcess").css('width','100%');
                //$("#viewSentIDCardAkcess").css('width','433px');
            }
        }
    });
}

function viewAkcessGuestPassDatatables(type) {
    var vid = $("#ViewSendGuestPassData #vieweid").val();
    var vt = $("#ViewSendGuestPassData #viewType").val();
    $.ajax({
        type: 'POST',
        url: burl + "/i-d-card/getResponseData?type=" + type + "&vid=" + vid + "&vt=" + vt,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        cache: false,
        success: function(result)
        {
            if (type == 'viewemail') {
                $('#viewSentGuestPassEmail').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Email"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    language: { search: "", searchPlaceholder: "Search" },
                    "sDom": 'Rfrtlip',
                    drawCallback: function() {
                        $(".dataTables_filter label i").remove();
                        $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
                    },
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentGuestPassEmail").css('width','100%');
            } else if (type == 'viewphone') {
                $('#viewSentGuestPassPhone').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "Phone No"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    language: { search: "", searchPlaceholder: "Search" },
                    "sDom": 'Rfrtlip',
                    drawCallback: function() {
                        $(".dataTables_filter label i").remove();
                        $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
                    },
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentGuestPassPhone").css('width','100%');
            } else if (type == 'viewackess') {
                $('#viewSentGuestPassAkcess').DataTable({
                    aaData: result, //here we get the array data from the ajax call.
                    bDestroy: true,
                    aoColumns: [
                        {sTitle: "AkcessID"},
                        {sTitle: "Status"},
                        {sTitle: "Date"}
                    ],
                    language: { search: "", searchPlaceholder: "Search" },
                    "sDom": 'Rfrtlip',
                    drawCallback: function() {
                        $(".dataTables_filter label i").remove();
                        $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
                    },
                    order: [[ 2, "desc" ]]
                });
                $("#viewSentGuestPassAkcess").css('width','100%');
                //$("#viewSentIDCardAkcess").css('width','433px');
            }
        }
    });
}

function viewSendDocumentModalModule(data_title){
    if (typeof (document.getElementById("viewSendDocumentModalModule")) != 'undefined' && document.getElementById("viewSendDocumentModalModule") != null) {
        var data_value = data_title;
        $('#ViewSendDocumentData #vieweid').val(data_value);
        $('#viewSendDocumentModalModule').modal('show');
        $("#ViewSendDocumentData #viewSendDocumentinlineRadio3").trigger('click');
    }
}

function viewSendIDCardModalModule(data_title){
    if (typeof (document.getElementById("viewSendIDCardModalModule")) != 'undefined' && document.getElementById("viewSendIDCardModalModule") != null) {
        var data_value = data_title;
        $('#ViewSendIDCardData #vieweid').val(data_value);
        $('#viewSendIDCardModalModule').modal('show');
        $("#ViewSendIDCardData #viewSendIDCardinlineRadio3").trigger('click');
    }
}

function viewSendGuestPassModalModule(data_title){
    if (typeof (document.getElementById("viewSendGuestPassModalModule")) != 'undefined' && document.getElementById("viewSendGuestPassModalModule") != null) {
        var data_value = data_title;
        $('#ViewSendGuestPassData #vieweid').val(data_value);
        $('#viewSendGuestPassModalModule').modal('show');
        $("#ViewSendGuestPassData #viewSendGuestPassinlineRadio3").trigger('click');
    }
}

function viewReceivedIDCardModalModule(data_title, viewType){
    if (typeof (document.getElementById("viewReceivedIDCardModalModule")) != 'undefined' && document.getElementById("viewReceivedIDCardModalModule") != null) {
        $('#viewReceivedIDCardModalModule').modal('show');
        $('#viewReceivedIDCardModalModule .form-group').show();
        var data_value = data_title;
        $('#viewReceivedIDCardtData #viewReceivedid').val(data_value);
        $('#viewReceivedIDCardtData #viewType').val(viewType);
        viewReceivedAkcessIDCardDatatables();
    }
}

function viewSendEformModalModule(data_value, viewType){
    if (typeof (document.getElementById("viewSendEformModalModule")) != 'undefined' && document.getElementById("viewSendEformModalModule") != null) {
        $('#vieweid').val(data_value);
        $('#viewSendEformModalModule').modal('show');
        $("#viewSendEformModalModule #viewSendEforminlineRadio3").trigger('click');
    }
}

function sendEformModalModule(data_value, viewType){
    if (typeof (document.getElementById("sendEformModalModule")) != 'undefined' && document.getElementById("sendEformModalModule") != null) {
        $('#eid').val(data_value);
        $('#sendEformModalModule').modal('show');
        $('input[type=text]').val();
        $("#sendEformModalModule #inlineRadio3").trigger('click');
    }
}

function viewReceivedDocumentModalModule(data_title, viewType){
    if (typeof (document.getElementById("viewReceivedDocumentModalModule")) != 'undefined' && document.getElementById("viewReceivedDocumentModalModule") != null) {

        $('#viewReceivedDocumentModalModule').modal('show');
        $('#viewReceivedDocumentModalModule .form-group').show();
        var data_value = data_title;
        $('#viewReceivedDocumentData #viewReceivedid').val(data_value);
        $('#viewReceivedDocumentData #viewType').val(viewType);
        viewReceivedAkcessDocumentDatatables();

    }
}

function sendIDCardModalModule(data_title){
    var data_value = data_title;
    $('#SendDocIDCard #idcardid').val(data_value);
    $('#SendDocIDCard').trigger('reset');
    $('#sendIDCardModalModule').modal('show');
    $('input[type=text]').val();
    $("#SendDocIDCard #viewReceivedInlineRadio3").trigger('click');
}

function sendModalModule(data_title){
    var data_value = data_title;
    $('#SendDoc #idcardid').val(data_value);
    $('#SendDoc').trigger('reset');
    $('#sendModalModule').modal('show');
    $('input[type=text]').val();
    $('#sendModalModule .form-group').hide();
    $("#sendModalModule #inlineRadio3").trigger('click');
}

function sendPortalModalModule(data_title){
    var data_value = data_title;
    $('#SendPortalData #eid').val(data_value);
    $('#SendPortalData').trigger('reset');
    $('#sendPortalModalModule').modal('show');
    $('input[type=text]').val();
}

function sendInvitationModalModule(data_title) {
    var data_value = data_title;
    $('#SendInvitationData #eid').val(data_value);
    $('#SendInvitationData').trigger('reset');
    $('#sendInvitationModalModule').modal('show');
    $('input[type=text]').val();
}

function getDataFROM(){
    $("#from_akcess_id").removeClass('has-error');
    $('#getDataFROMData').trigger('reset');
    $('#getDataFROMModalModule').modal('show');
    $('input[type=text]').val();
}

$(document).ready(function () {
    if (typeof (document.getElementById("repeat_time")) != 'undefined' && document.getElementById("repeat_time") != null) {
        $("#repeat_time").change("click", function () {
            if (this.checked)
            {
                alert('checked');
                var openepeattimefrom = $("#openepeattimefrom").val();
                var openepeattimeto = $("#openepeattimeto").val();
                $(".openfrom-picktime").val(openepeattimefrom);
                $(".opento-picktime").val(openepeattimeto);
            }
            if(!this.checked){
                alert('Unchecked');
            }
        });
    }
});
$(document).ready(function() {

    if (typeof (document.getElementById("SendMessage")) != 'undefined' && document.getElementById("SendMessage") != null) {

        var e = 1000;
        $(".global-tokenize").tokenize({
            placeholder: "Select AKcess ID",
            displayDropdownOnFocus: !0,
            searchMaxLength: 20,
            nbDropdownElements: e,
            allowDuplicates: false,
            onAddToken: function(e, a, t) {
            }
        });
    }

    if (typeof (document.getElementById("send-notification-form")) != 'undefined' && document.getElementById("send-notification-form") != null) {

        var e = 10;
        $("#SendNotifications .global-tokenize").tokenize({
            placeholder: "Select AKcess ID",
            displayDropdownOnFocus: !0,
            searchMaxLength: 20,
            nbDropdownElements: e,
            allowDuplicates: false,
            onAddToken: function(e, a, t) {
            }
        });
    }

    if (typeof (document.getElementById("sendPortalModalModule")) != 'undefined' && document.getElementById("sendPortalModalModule") != null) {

        // $('#multiple_portal').multiselect({
        //     columns: 1,
        //     placeholder: 'Select to portal',
        //     search: true,
        //     selectAll: true
        // });
    }

    if (typeof (document.getElementById("SendInvitationData")) != 'undefined' && document.getElementById("SendInvitationData") != null) {
        $('#sendInvitationModalModule .form-group').hide();
        $('input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#sendInvitationModalModule').find('input[type="text"]').val('');
            $('#sendInvitationModalModule .form-group').hide();
            $('#sendInvitationModalModule .' + $(this).val()).show();
            $('#sendInvitationModalModule .btn_text').text($(this).attr('placeholder'));
            $(".TokensContainer .Token a.Close").trigger('click');
            $(".remove_all").remove();
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        });
        $(".sendInvitationModalModule").click(function() {
            var data_value = $(this).attr('data-title');
            $('#eid').val(data_value);
            $('#sendInvitationModalModule').modal('show');
            $('input[type=text]').val();
        });

        $('#sendInvitationModalModule').on('hidden.bs.modal', function() {
            $('#eid').val('');
            $(this).find('form').trigger('reset');
            $('#sendInvitationModalModule .form-group').hide();
        });

        var e = 10;
        $(".global-tokenize").tokenize({
            placeholder: "Select AKcess ID",
            displayDropdownOnFocus: !0,
            searchMaxLength: 20,
            nbDropdownElements: e,
            allowDuplicates: false,
            onAddToken: function(e, a, t) {
            }
        });

        $("#email_search").tokenize({
            placeholder: "Enter Email",
            searchMaxLength: 50
        });
    }

    if (typeof (document.getElementById("viewSendIDCardModalModule")) != 'undefined' && document.getElementById("viewSendIDCardModalModule") != null) {

        $('#viewSendIDCardModalModule .form-group').hide();
        $('#viewSendIDCardModalModule input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#viewSendIDCardModalModule .form-group').hide();
            $('#viewSendIDCardModalModule .' + $(this).val()).show();
            viewAkcessIDCardDatatables($(this).val());
        });

        $('#viewSendIDCardModalModule').on('hidden.bs.modal', function() {
            $('#vieweid').val();
            $(this).find('form').trigger('reset');
            $('#SendDocIDCard').trigger('reset');
            $('#viewSendIDCardModalModule .form-group').hide();
        });
    }

    if (typeof (document.getElementById("viewSendGuestPassModalModule")) != 'undefined' && document.getElementById("viewSendGuestPassModalModule") != null) {

        $('#viewSendGuestPassModalModule .form-group').hide();
        $('#viewSendGuestPassModalModule input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#viewSendGuestPassModalModule .form-group').hide();
            $('#viewSendGuestPassModalModule .' + $(this).val()).show();
            viewAkcessGuestPassDatatables($(this).val());
        });

        $('#viewSendGuestPassModalModule').on('hidden.bs.modal', function() {
            $('#vieweid').val();
            $(this).find('form').trigger('reset');
            $('#SendDocGuestPass').trigger('reset');
            $('#viewSendGuestPassModalModule .form-group').hide();
        });
    }

    if (typeof (document.getElementById("viewSendDocumentModalModule")) != 'undefined' && document.getElementById("viewSendDocumentModalModule") != null) {

        $('#viewSendDocumentModalModule .form-group').hide();
        $('#viewSendDocumentModalModule input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#viewSendDocumentModalModule .form-group').hide();
            $('#viewSendDocumentModalModule .' + $(this).val()).show();
            viewAkcessDocumentDatatables($(this).val());
        });

        $('#viewSendDocumentModalModule').on('hidden.bs.modal', function() {
            $('#vieweid').val();
            $(this).find('form').trigger('reset');
            $('#viewSendDocumentModalModule .form-group').hide();
        });
    }

    if (typeof (document.getElementById("viewReceivedIDCardModalModule")) != 'undefined' && document.getElementById("viewReceivedIDCardModalModule") != null) {

        $('#viewReceivedIDCardModalModule').on('hidden.bs.modal', function() {
            $('#viewReceivedIDCardModalModule').modal('hide');
        });

    }

    if (typeof (document.getElementById("viewReceivedDocumentModalModule")) != 'undefined' && document.getElementById("viewReceivedDocumentModalModule") != null) {

        $('#viewReceivedDocumentModalModule').on('hidden.bs.modal', function() {
            $('#viewReceivedDocumentModalModule').modal('hide');
        });

    }



    $('#flash_success').addClass('animated fadeInDown');
    $( ".close" ).click(function() {
        jQuery('#flash_success').removeClass('fadeInDown');
        jQuery('#flash_success').addClass('fadeOutUp');
    });
    setTimeout(function() {
        jQuery('#flash_success').removeClass('fadeInDown');
        jQuery('#flash_success').addClass('fadeOutUp');
    }, 8000);

    if (typeof (document.getElementById("eformTable")) != 'undefined' && document.getElementById("eformTable") != null) {
        $('#eformTable').DataTable({
            "sDom": 'Rfrtlip',
            order: [2, 'desc'],
            language: { search: "", searchPlaceholder: "Search" },
            responsive:false,
            stateSave:true,
            columnDefs: [
                {
                    targets: [3], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("formInner")) != 'undefined' && document.getElementById("formInner") != null) {
        $('#formInner').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            columnDefs: [ {
                targets: [1,2], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }],
            drawCallback: function() {
            $(".dataTables_filter label i").remove();
            $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
        }
        });
    }

    if (typeof (document.getElementById("manage_students_table")) != 'undefined' && document.getElementById("manage_students_table") != null) {
        $('#manage_students_table').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            columnDefs: [ {
                targets: [4], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("user_access_data_table")) != 'undefined' && document.getElementById("user_access_data_table") != null) {
        $('#user_access_data_table').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            columnDefs: [ {
                targets: [4], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            },"searching": false
        });
    }

    if (typeof (document.getElementById("student-attends")) != 'undefined' && document.getElementById("student-attends") != null) {
        $('#student-attends').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            columnDefs: [ {
                targets: [2], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }]
        });
    }

    if (typeof (document.getElementById("users-type")) != 'undefined' && document.getElementById("users-type") != null) {
        $('#users-type').DataTable({
            "sDom": 'Rfrtlip',
            order: [[4, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [5], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("class-example1")) != 'undefined' && document.getElementById("class-example1") != null) {
        $('#class-example1').DataTable({
            "sDom": 'Rfrtlip',
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: -1 },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("student-example1")) != 'undefined' && document.getElementById("student-example1") != null) {
        $('#student-example1').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [4], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }


    if (typeof (document.getElementById("eform_response")) != 'undefined' && document.getElementById("eform_response") != null) {
        $('#eform_response').DataTable({

            order: [[5, 'desc']],
            "serverSide": true,
            "ajax": {
                "url":  burl + '/eform-response/getList',
                "type": "POST",
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                data : function(res)
                {
                }
            },
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [6], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
                { responsivePriority: 2, targets: 2 },
                { responsivePriority: 1, targets: -1 },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
                $('#eform_response_info').prepend($('#eform_response_length'));
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: "Export To Excel",
                    className: "btn waves-effect waves-light btn-info"
                }
            ]
        });
    }

    if (typeof (document.getElementById("student_recyle")) != 'undefined' && document.getElementById("student_recyle") != null) {
        $('#student_recyle').DataTable({
            "sDom": 'Rfrtlip',
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: -1 },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("admin_recyle")) != 'undefined' && document.getElementById("admin_recyle") != null) {
        $('#admin_recyle').DataTable({
            "sDom": 'Rfrtlip',
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: -1 },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("teacher_recyle")) != 'undefined' && document.getElementById("teacher_recyle") != null) {
        $('#teacher_recyle').DataTable({
            "sDom": 'Rfrtlip',
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: -1 },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("staff_recyle")) != 'undefined' && document.getElementById("staff_recyle") != null) {
        $('#staff_recyle').DataTable({
            "sDom": 'Rfrtlip',
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: -1 },
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("example1")) != 'undefined' && document.getElementById("example1") != null) {
        $('#example1').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            //scrollX: true,
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [5], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },

            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("locationTable")) != 'undefined' && document.getElementById("locationTable") != null) {
        $('#locationTable').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            //scrollX: true,
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [5], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 2, targets: -1 },

            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("attendance-report-tbl")) != 'undefined' && document.getElementById("attendance-report-tbl") != null) {
        $('#attendance-report-tbl').DataTable({
            "sDom": 'Rfrtlip',
            "searching": false,
            order: [[0, 'asc']],
            //scrollX: true,
            responsive: true,
            columnDefs: [ {
                targets: [5], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }]
        });
    }

    if (typeof (document.getElementById("class-attendance-report-tbl")) != 'undefined' && document.getElementById("class-attendance-report-tbl") != null) {
        $('#class-attendance-report-tbl').DataTable({
            //"sDom": 'Rfrtlip',
            order: [[0, 'asc']],
            language: { search: "", searchPlaceholder: "Search" },
            //scrollX: true,
            responsive: true,
            columnDefs: [ {
                targets: [5], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
                $('#class-attendance-report-tbl_info').prepend($('#class-attendance-report-tbl_length'));
            },
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: "Export To Excel",
                    className: "btn waves-effect waves-light btn-info excel-btn",
                    insertCells: [
                        {
                            cells: 'sh',
                            content: ['','','','','',''],
                            pushRow: true
                        },
                        {
                            cells: 'sh',
                            content: ['Attendance date : ',$('.attendance-report-date').attr('data-val'),'','','',''],
                            pushRow: true
                        },
                        {
                            cells: 'sh',
                            content: ['Location : ',$('#location-name').text(),'','Teacher name :',$('#teacher-name').text(),''],
                            pushRow: true
                        },
                        {
                            cells: 'sh',
                            content: ['Class name : ',$('#class-name').text(),'','Number of students :',$('#no-of-student').text(),''],
                            pushRow: true
                        },

                    ],
                    excelStyles: [
                        {
                            cells: ["2:3:4","sf"],
                            template: 'header_blue',

                        },
                        {
                            cells: "2",
                            style: {
                                font: {
                                    b: true,
                                },
                            },
                        },
                        {
                            cells: "6:n,2",
                            template: 'stripes_blue',
                        },
                        {
                            cells: 'sh:f',
                            template: 'rowlines_blue',
                        },
//                                {
//                                    template: 'outline_blue',
//                                },
                    ],

                },
                {
                    extend: 'print',
                    text: "Print",
                    className: "btn waves-effect waves-light btn-info tbl-print-btn",
                    customize: function ( win ) {
                        $(win.document.body)
                            .css( 'font-size', '10pt' )
                            .prepend(
                            '<div class="row"><div class="col-md-6"><div class="form-group"><label class="font-400">Class name : '+$('#class-name').text()+'</label></div></div><div class="col-md-6"><div class="form-group"><label class="font-400">No of students : '+$('#no-of-student').text()+'</label></div></div><div class="col-md-6"><div class="form-group"><label class="font-400">Location : '+$('#location-name').text()+'</label></div></div><div class="col-md-6"><div class="form-group"><label class="font-400">Teachers name : '+$('#teacher-name').text()+'</label></div></div><div class="col-md-6"><div class="form-group"><label class="font-400">Date : '+$('.attendance-report-date').attr('data-val')+'</label></div></div></div>'
                        );

                        $(win.document.body).find( 'table' )
                            .addClass( 'compact' )
                            .css( 'font-size', 'inherit' );
            }
                }
            ]
        });
    }

    if (typeof (document.getElementById("eformResponseTable")) != 'undefined' && document.getElementById("eformResponseTable") != null) {
        $('#eformResponseTable').DataTable({
            "sDom": 'Rfrtlip',
            order: [[5, 'desc']],
            columnDefs: [ {
                targets: [6], // column index (start from 0)
                orderable: false, // set orderable false for selected columns
            }]
        });
    }

    if (typeof (document.getElementById("idcard_table")) != 'undefined' && document.getElementById("idcard_table") != null) {
        $('#idcard_table').DataTable({
            "sDom": 'Rfrtlip',
            order: [[5, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [3], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                }
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("idcard_recycle_table")) != 'undefined' && document.getElementById("idcard_recycle_table") != null) {
        $('#idcard_recycle_table').DataTable({
            "sDom": 'Rfrtlip',
            aaSorting: [[3, "desc"]],
            columnDefs: [{
                targets: [3],
                visible: !1
            }]
        });
    }

    if (typeof (document.getElementById("auditTrail")) != 'undefined' && document.getElementById("auditTrail") != null) {
        $('#auditTrail').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs:[
                { responsivePriority: 1, targets: 1 }
            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("guestPassTable")) != 'undefined' && document.getElementById("guestPassTable") != null) {
        $('#guestPassTable').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            //scrollX: true,
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            columnDefs: [
                {
                    targets: [8], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 3, targets: 2 },
                { responsivePriority: 2, targets: -1 },

            ],
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            }
        });
    }

    if (typeof (document.getElementById("invitation_list")) != 'undefined' && document.getElementById("invitation_list") != null) {
        $('#invitation_list').DataTable({
            "sDom": 'Rfrtlip',
            order: [[0, 'desc']],
            language: { search: "", searchPlaceholder: "Search" },
            responsive: false,
            drawCallback: function() {
                $(".dataTables_filter label i").remove();
                $(".dataTables_filter label").append('<i class="fal fa-search text-primary"></i>');
            },
            'columnDefs' : [
                //hide the second & fourth column
                { 'visible': false, 'targets': [0] }
            ]
        });
    }

    $sidebar = $('.sidebar');

    $sidebar_img_container = $sidebar.find('.sidebar-background');

    $full_page = $('.full-page');

    $sidebar_responsive = $('body > .navbar-collapse');

    window_width = $(window).width();

    fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

    if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
        if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
        }

    }

    $('.fixed-plugin a').click(function(event) {
        // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
        if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else if (window.event) {
                window.event.cancelBubble = true;
            }
        }
    });

    $('.fixed-plugin .active-color span').click(function() {
        $full_page_background = $('.full-page-background');

        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        var new_color = $(this).data('color');

        if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
        }

        if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
        }

        if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
        }
    });

    $('.fixed-plugin .background-color .badge').click(function() {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        var new_color = $(this).data('background-color');

        if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
        }
    });

    $('.fixed-plugin .img-holder').click(function() {
        $full_page_background = $('.full-page-background');

        $(this).parent('li').siblings().removeClass('active');
        $(this).parent('li').addClass('active');


        var new_image = $(this).find("img").attr('src');

        if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
                $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
                $sidebar_img_container.fadeIn('fast');
            });
        }

        if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
                $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
                $full_page_background.fadeIn('fast');
            });
        }

        if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
        }

        if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
        }
    });

    $('.switch-sidebar-image input').change(function() {
        $full_page_background = $('.full-page-background');

        $input = $(this);

        if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
                $sidebar_img_container.fadeIn('fast');
                $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
                $full_page_background.fadeIn('fast');
                $full_page.attr('data-image', '#');
            }

            background_image = true;
        } else {
            if ($sidebar_img_container.length != 0) {
                $sidebar.removeAttr('data-image');
                $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
                $full_page.removeAttr('data-image', '#');
                $full_page_background.fadeOut('fast');
            }

            background_image = false;
        }
    });

    $('.switch-sidebar-mini input').change(function() {
        $body = $('body');

        $input = $(this);

        if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

        } else {

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

            setTimeout(function() {
                $('body').addClass('sidebar-mini');

                md.misc.sidebar_mini_active = true;
            }, 300);
        }
    });

    // we simulate the window Resize so the charts will get updated in realtime.
    var simulateWindowResize = setInterval(function() {
        window.dispatchEvent(new Event('resize'));
    }, 180);

    // we stop the simulation of Window Resize after the animations are completed
    setTimeout(function() {
        clearInterval(simulateWindowResize);
    }, 1000);

    $('#main-wrapper').on('click', 'a.not_delete_btn', function(e) {
        e.preventDefault();
        $("#not_delete_modal").modal('show');

        $("#ok_btn").click(function(e) {
            $("#not_delete_modal").modal('hide');
            return false;
        });

    });

    /*$('#main-wrapper').on('click', 'a.approve_btn', function(e) {
     e.preventDefault();
     //alert(1)
     $('.textname').html('');
     $("#approve_modal").modal('show');
     var hl = $(this).attr('data-link');
     var hname = $(this).attr('data-attr');
     //alert(hl);
     $('.textname').html('are you sure you want to approve '+hname+'?');
     $("#yes_approve_btn").click(function() {
     //$('#remove_btn_form').attr('action', hl);
     $('#' + hl).click();
     });
     $("#no_approve_btn").click(function(e) {
     $("#approve_modal").modal('hide');
     return false;

     });

     });*/

    $('#main-wrapper').on('click', 'a.delete_btn', function(e) {
        e.preventDefault();
        //alert(1)
        $("#delete_modal").modal('show');
        var hl = $(this).attr('data-link');
        //alert(hl);
        $("#yes_btn").click(function() {
            //$('#remove_btn_form').attr('action', hl);
            $('#' + hl).click();
        });
        $("#no_btn").click(function(e) {
            $("#delete_modal").modal('hide');
            return false;

        });

    });

    $('#main-wrapper').on('click', 'a.copy_btn', function(e) {
        e.preventDefault();
        //alert(1)
        $("#copy_modal").modal('show');
        var hl = $(this).attr('data-link');
        //alert(hl);
        $("#copy_yes_btn").click(function() {
            //$('#remove_btn_form').attr('action', hl);
            $('#' + hl).click();
        });
        $("#no_btn").click(function(e) {
            $("#copy_modal").modal('hide');
            return false;

        });

    });

    $("#checkidcard").click(function(e) {

        var error = checkerrorIDCardError();
        if (error == 1) {
            toastr.error("Profile Picture, FirstName, LastName, Email, Date Of Birth, AKcess ID fields is required for Create ID Card.");
            return false;
        }

    });

//    if ($('#date_of_birth').attr('id')) {
//
//        $('#date_of_birth').datetimepicker({
//            format: 'YYYY-MM-DD',
//        });
//    }
//
//    if ($('.student_dob').attr('id')) {
//
//        $('.student_dob').datetimepicker({
//            format: 'YYYY-MM-DD',
//        });
//    }
//    
//    if ($('.adminssion_date').attr('id')) {
//
//        $('.adminssion_date').datetimepicker({
//            format: 'YYYY-MM-DD',
//        });
//    }
//    
//    if ($('#expiry_date').attr('id')) {
//
//        $('#expiry_date').datetimepicker({
//            format: 'YYYY-MM-DD',
//        });
//    }
//
//    if ($('#expiry_date_popup').attr('id')) {
//
//        $('#expiry_date_popup').datetimepicker({
//            format: 'YYYY-MM-DD',
//        });
//    }
//
//    if ($('#expiry_date_popup_doc').attr('id')) {
//
//        $('#expiry_date_popup_doc').datetimepicker({
//            minDate: 0,
//        });
//    }

    if (typeof (document.getElementById("message-field")) != 'undefined' && document.getElementById("message-field") != null) {
        function checkChars() {
            var numChars = $('#message-field').val().length;
            var maxChars = 160;
            var remChars = maxChars - numChars;
            if (remChars < 0) {
                $('#message-field').val($('#message-field').val().substring(0, maxChars));
                remChars = 0;
            }
            $('#character-remaining').text(remChars);
        }

        $('#message-field').bind('input propertychange', function() {
            checkChars();
        });

        checkChars();
    }

    if (typeof (document.getElementById("message-field_notification")) != 'undefined' && document.getElementById("message-field_notification") != null) {
        function checkChars() {
            var numChars = $('#message-field_notification').val().length;
            var maxChars = 160;
            var remChars = maxChars - numChars;
            if (remChars < 0) {
                $('#message-field_notification').val($('#message-field_notification').val().substring(0, maxChars));
                remChars = 0;
            }
            $('#character-remaining_notification').text(remChars);
        }

        $('#message-field_notification').bind('input propertychange', function() {
            checkChars();
        });

        checkChars();
    }

    if (typeof (document.getElementById("sendIDCardModalModule")) != 'undefined' && document.getElementById("sendIDCardModalModule") != null) {

        $('#sendIDCardModalModule .form-group').hide();
        $('input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#sendIDCardModalModule').find('input[type="text"]').val('');
            $('#sendIDCardModalModule .form-group').hide();
            $('#sendIDCardModalModule .' + $(this).val()).show();
            $('#sendIDCardModalModule .btn_text').text($(this).attr('placeholder'));
            $(".TokensContainer .Token a.Close").trigger('click');
            $(".remove_all").remove();
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        });

        $('#sendIDCardModalModule').on('hidden.bs.modal', function() {
            $('#idcardid').val('');
            $(this).find('form').trigger('reset');
            $('#sendIDCardModalModule .form-group').hide();
        });
    }

    if (typeof (document.getElementById("sendModalModule")) != 'undefined' && document.getElementById("sendModalModule") != null) {

        $('#sendModalModule .form-group').hide();

        $('input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#sendModalModule').find('input[type="text"]').val('');
            $('#sendModalModule .form-group').hide();
            $('#sendModalModule .' + $(this).val()).show();
            $('#sendModalModule .btn_text').text($(this).attr('placeholder'));
            $(".TokensContainer .Token a.Close").trigger('click');
            $(".remove_all").remove();
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        });

        $('#sendModalModule').on('hidden.bs.modal', function() {
            $('#idcardid').val('');
            $(this).find('form').trigger('reset');
            $('#SendDoc').trigger('reset');
            $('#sendModalModule .form-group').hide();
        });
    }

    if (typeof (document.getElementById("SendDoc")) != 'undefined' && document.getElementById("SendDoc") != null) {
        var e = 10;
        $(".global-tokenize").tokenize({
            placeholder: "Select AKcess ID",
            displayDropdownOnFocus: !0,
            searchMaxLength: 20,
            nbDropdownElements: e,
            allowDuplicates: false,
            onAddToken: function(e, a, t) {
            }
        });
        $("#email_search").tokenize({
            placeholder: "Enter Email",
            searchMaxLength: 50
        });

        $("#email_search_id").tokenize({
            placeholder: "Enter Email",
            searchMaxLength: 50
        });
    }

    if (typeof (document.getElementById("SendDocIDCard")) != 'undefined' && document.getElementById("SendDocIDCard") != null) {
        var e = 10;
        $(".global-tokenize").tokenize({
            placeholder: "Select AKcess ID",
            displayDropdownOnFocus: !0,
            searchMaxLength: 20,
            nbDropdownElements: e,
            allowDuplicates: false,
            onAddToken: function(e, a, t) {
            }
        });
        $("#email_search").tokenize({
            placeholder: "Enter Email",
            searchMaxLength: 50
        });
        $("#email_search_id").tokenize({
            placeholder: "Enter Email",
            searchMaxLength: 50
        });
    }

    if (typeof (document.getElementById("sendEformModalModule")) != 'undefined' && document.getElementById("sendEformModalModule") != null) {

        $('#sendEformModalModule .form-group').hide();
        $('input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#sendEformModalModule').find('input[type="text"]').val('');
            $('#sendEformModalModule .form-group').hide();
            $('#sendEformModalModule .' + $(this).val()).show();
            $('#sendEformModalModule .btn_text').text($(this).attr('placeholder'));
            $(".TokensContainer .Token a.Close").trigger('click');
            $(".remove_all").remove();
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        });
        $(".sendEformModalModule").click(function() {
            var data_value = $(this).attr('data-title');
            $('#eid').val(data_value);
            $('#sendEformModalModule').modal('show');
            $('input[type=text]').val();
        });

        $('#sendEformModalModule').on('hidden.bs.modal', function() {
            $('#eid').val('');
            $(this).find('form').trigger('reset');
            $('#sendEformModalModule .form-group').hide();
        });
        var e = 10;
        $(".global-tokenize").tokenize({
            placeholder: "Select any option",
            displayDropdownOnFocus: !0,
            nbDropdownElements: e,
            allowDuplicates: false,
            onAddToken: function(e, a, t) {
            }
        });

        $("#email_search").tokenize({
            placeholder: "Enter Email",
        });

    }

    if (typeof (document.getElementById("viewSendEformModalModule")) != 'undefined' && document.getElementById("viewSendEformModalModule") != null) {

        $('#viewSendEformModalModule .form-group').hide();
        $('input[type=radio]').click(function() {
            $(this).find('form').trigger('reset');
            $('#viewSendEformModalModule .form-group').hide();
            $('#viewSendEformModalModule .' + $(this).val()).show();
            viewAkcessDatatables($(this).val());
        });

        $(".viewSendEformModalModule").click(function() {
            var data_value = $(this).attr('data-title');
            $('#vieweid').val(data_value);
            $('#viewSendEformModalModule').modal('show');
        });

        $('#viewSendEformModalModule').on('hidden.bs.modal', function() {
            $('#vieweid').val();
            $(this).find('form').trigger('reset');
            $('#viewSendEformModalModule .form-group').hide();
        });
    }

    if (typeof (document.getElementById("eFormModalModule")) != 'undefined' && document.getElementById("eFormModalModule") != null) {

        $('#eFormModalModule .form-group').hide();

        $(".eFormModalModule").click(function() {
            $('#eFormModalModule').modal('show');
        });

        $(".eFormAddModule").click(function() {
            $('#eFormModalModule').modal('hide');
            $('#eFormAddModule').modal('show');
            $(this).find('form').trigger('reset');
        });
    }



    $('#firstname').on('focus blur keydown keyup focusout keypress', function() {
        $('.firstname_label').text($(this).val());
    });

    $('#lastname').on('focus blur keydown keyup focusout keypress', function() {
        $('.lastname_label').text($(this).val());
    });

    $('#idno').on('focus blur keydown keyup focusout keypress', function() {
        $('.idno_label').text($(this).val());
    });

    $('#date_of_birth').on('focus blur keydown keyup focusout keypress', function() {
        $('.date_of_birth_label').text($(this).val());
    });

    $('#expiry_date').on('focus blur keydown keyup focusout keypress', function() {
        $('.expiry_date_label').text($(this).val());
    });

    $('.picktime').datetimepicker({
        format: 'LT',
        //inline: true,
    });

    $('#photo').change(function() {
        $('#fileInput').submit();
    });

    $(".adddoc").click(function(e) {
        e.preventDefault();
        //console.log(this.href)
        var data_doc = $(this).attr('data-doc');
        $.ajax({url: this.href, action: 'GET', success: function(result) {
            if (data_doc) {
                $("#myModalaplusDocument #labelh4").text('Edit Document');
            }
            $("#myModalaplusDocument #b").val(data_doc);
            var name_doc = $("#namedoc-" + data_doc).text();
            var dtype_doc = $("#namedoc-" + data_doc).attr('data-dtype');
            var date_doc = $("#namedoc-" + data_doc).attr('data-date');

            $("#myModalaplusDocument #name").val(name_doc);
            $("#myModalaplusDocument #fk_documenttype_id").val(dtype_doc);
            $("#myModalaplusDocument #expiry_date_popup_doc").val(date_doc);
            $("#myModalaplusDocument").modal('show');
            $('#adddoc').submit(function(event) {
                event.preventDefault();
                var formEl = $(this);
                var submitButton = $('input[type=submit]', formEl);

                $.ajax({
                    type: 'POST',
                    url: formEl.prop('action'),
                    dataType: 'json',
                    accept: {
                        javascript: 'application/javascript'
                    },
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                        submitButton.prop('disabled', 'disabled');
                    }
                }).done(function(data) {
                    $('#load').hide();
                    document.getElementById('load').style.visibility = "hidden";
                    if (data.result && data.result == 'error') {
                        toastr.error(data.msg);
                    } else {
                        location.reload();
                    }
                    submitButton.prop('disabled', false);
                }).fail(function(data) {
                    $('#load').hide();
                    document.getElementById('load').style.visibility = "hidden";
                    var res = data.responseText;
                    if (res.indexOf('success') > -1) {
                        location.reload();
                    } else {
                        toastr.error('Something went wrong.');
                    }

                    submitButton.prop('disabled', false);
                });
            });

        }});
    });

    $(".addidcard").click(function(e) {
        e.preventDefault();
        var error = checkerrorIDCardError();
        if (error == 1) {
            $(".idcard_message").removeClass('hidden');
            $(".idcard_message").text("Profile Picture, Faculty, FirstName, LastName, Email, Date Of Birth fields is required for Create ID Card.");
            window.setTimeout(function() {
                $('.idcard_message').addClass('hidden');
            }, 5000);
            return false;
        } else {
            $.ajax({url: this.href, action: 'GET', success: function(result) {
                $("#myModalaplusIDCard").modal('show');
                $('#addidcard').submit(function(event) {
                    event.preventDefault();
                    var formEl = $(this);
                    var submitButton = $('input[type=submit]', formEl);

                    $.ajax({
                        type: 'POST',
                        url: formEl.prop('action'),
                        dataType: 'json',
                        accept: {
                            javascript: 'application/javascript'
                        },
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#load').show();
                            document.getElementById('load').style.visibility = "visible";
                            submitButton.prop('disabled', 'disabled');
                        }
                    }).done(function(data) {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                        if (data.result && data.result == 'error') {
                        } else {
                            location.reload();
                        }
                        submitButton.prop('disabled', false);
                    }).fail(function(data) {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                        var res = data.responseText;
                        if (res.indexOf('success') > -1) {
                            location.reload();
                        } else {
                            toastr.error('Something went wrong.');
                        }
                        submitButton.prop('disabled', false);
                    });
                });

            }});
        }
    });

    $("#addStudents").click(function(e) {
        e.preventDefault();
        //console.log(this.href)
        $.ajax({url: this.href, action: 'GET', success: function(result) {
            //alert("success"+result);
            $("#myModalaplus #contentBody").html(result);

            $("#myModalaplus #contentBody select option").each(function(index) {
                if ($('#' + $(this).val()).length)
                {
                    $(this).remove();
                }
            });
            $("#myModalaplus").modal('show');

            $('#add_stu').submit(function(event) {
                event.preventDefault();
                var formEl = $(this);
                var submitButton = $('input[type=submit]', formEl);

                $.ajax({
                    type: 'POST',
                    url: formEl.prop('action'),
                    dataType: 'json',
                    accept: {
                        javascript: 'application/javascript'
                    },
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        submitButton.prop('disabled', 'disabled');
                    }
                }).done(function(data) {
                    if (data.result && data.result == 'error') {
                        toastr.error(data.msg);
                    } else {
                        location.reload();
                    }
                    submitButton.prop('disabled', false);
                }).fail(function(data) {
                    var res = data.responseText;
                    if (res.indexOf('success') > -1) {
                        location.reload();
                    } else {
                        toastr.error('Number of students exceeded the Maximum number of students allowed.');
                    }

                    submitButton.prop('disabled', false);
                });
            });

        }});
    });

    window.setTimeout(function() {
        $('.message').addClass('hidden');
    }, 5000);

});

document.onreadystatechange = function() {
    if (typeof (document.getElementById("load")) != 'undefined' && document.getElementById("load") != null) {
        var state = document.readyState
        if (state == 'complete') {
            document.getElementById('interactive');
            document.getElementById('load').style.visibility = "hidden";
        }
    }
}

function isANumber(str) {
    return $.isNumeric(str);
}

function checkerrorIDCardError() {

    var error = 0;

    var check_fields_id = document.getElementById('check_fields_id').value;
    if (check_fields_id == 1) {
        error = 1;
    }

    return error;
}

function checkerrorGetDataFROM() {

    var error = 0;

    var from_akcess_id = document.getElementById('from_akcess_id').value;
    if (from_akcess_id == "") {
        error = 1;
        $("#from_akcess_id").addClass('has-error');
        //toastr.error("AKcess ID is required field!");
        error = 1;
    } else {
        $("#from_akcess_id").removeClass('has-error');
    }

    return error;
}


function checkerrorInvitation() {

    var error = 0;

    var type = $("input[name='inlineRadioOptions']:checked").val();

    if (type == 'phone') {
        var phone = document.getElementById('phone').value;
        if (phone == "") {
            error = 1;
            $("#phone").addClass('has-error');
        } else if (isANumber(phone) == false) {
            error = 1;
            $("#phone").addClass('has-error');
        } else {
            $("#phone").removeClass('has-error');
        }

        var country_code = document.getElementById('country_code').value;
        if (country_code == "") {
            error = 1;
            $("#country_code").addClass('has-error');
        } else {
            $("#country_code").removeClass('has-error');
        }

        //var messagep = document.getElementById('messagep').value;
        //
        //if (messagep == "") {
        //    error = 1;
        //    $("#messagep").addClass('has-error');
        //} else {
        //    $("#messagep").removeClass('has-error');
        //}
    }
    else if (type == 'email') {

        if (typeof (document.getElementsByClassName("Token")) != 'undefined' && document.getElementsByClassName("Token") != null) {
            var errorval = 1;
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                if(field_items == ''){
                    errorval = 1;
                }else  if (isEmail(field_items) == false) {
                    errorval = 1;
                } else {
                    errorval = 0;
                }
            });
            error = errorval;

        } else {
            error = 1;
        }

        if(error == 1) {
            $('.TokensContainer').attr("style","border:1px solid #fd0505")
        } else {
            $('.TokensContainer').attr("style","border:1px solid #ccc")
        }

        var messagee = document.getElementById('messagee').text;

        if (messagee == "") {
            error = 1;
            $("#messagee").addClass('has-error');
        } else {
            $("#messagee").removeClass('has-error');
        }
    }
    else if (type == 'ackess') {

        if (typeof (document.getElementsByClassName("Token")) != 'undefined' && document.getElementsByClassName("Token") != null) {
            var errorval = 1;
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                if(field_items == ''){
                    errorval = 1;
                } else {
                    errorval = 0;
                }
            });
            error = errorval;

        } else {
            error = 1;
        }

        if(error == 1) {
            $('.TokensContainer').attr("style","border:1px solid #fd0505");
        } else {
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        }
    } else {
        //toastr.error("Please select any one options!");
        error = 1;
    }

    return error;
}


function checkerrorPortal() {

    var error = 0;

    var portal = $('#portal :selected').val();
    if (portal == "") {
        error = 1;
        $("#portal").addClass('has-error');
    } else {
        $("#portal").removeClass('has-error');
    }

    if( $('#multiple_portal').val() == "" ) {
        error = 1;
        $("#multiple_portal").addClass('has-error');
    } else {
        $("#multiple_portal").removeClass('has-error');
    }

    return error;
}

function checkerror() {

    var error = 0;

    var type = $("input[name='inlineRadioOptions']:checked").val();

    if (type == 'phone') {
        var phone = document.getElementById('phone').value;
        if (phone == "") {
            error = 1;
            $("#phone").addClass('has-error');
        } else if (isANumber(phone) == false) {
            error = 1;
            $("#phone").addClass('has-error');
        } else {
            $("#phone").removeClass('has-error');
        }

        var country_code = document.getElementById('country_code').value;
        if (country_code == "") {
            error = 1;
            $("#country_code").addClass('has-error');
        } else {
            $("#country_code").removeClass('has-error');
        }
    }
    else if (type == 'email') {

        if (typeof (document.getElementsByClassName("Token")) != 'undefined' && document.getElementsByClassName("Token") != null) {
            var errorval = 1;
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                if(field_items == ''){
                    errorval = 1;
                }else  if (isEmail(field_items) == false) {
                    errorval = 1;
                } else {
                    errorval = 0;
                }
            });
            error = errorval;

        } else {
            error = 1;
        }

        if(error == 1) {
            $('.TokensContainer').attr("style","border:1px solid #fd0505")
        } else {
            $('.TokensContainer').attr("style","border:1px solid #ccc")
        }
    }
    else if (type == 'ackess') {

        if (typeof (document.getElementsByClassName("Token")) != 'undefined' && document.getElementsByClassName("Token") != null) {
            var errorval = 1;
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                if(field_items == ''){
                    errorval = 1;
                } else {
                    errorval = 0;
                }
            });
            error = errorval;

        } else {
            error = 1;
        }

        if(error == 1) {
            $('.TokensContainer').attr("style","border:1px solid #fd0505");
        } else {
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        }

    } else {
        //toastr.error("Please select any one options!");
        error = 1;
    }

    return error;
}

function checkerrorIDCArd() {

    var error = 0;

    var type = $("input[name='inlineRadioOptions']:checked").val();

    if (type == 'phone') {
        var phone = document.getElementById('phone_id').value;
        if (phone == "") {
            error = 1;
            $("#phone_id").parent("div").addClass('has-error');
        } else if (isANumber(phone) == false) {
            error = 1;
            $("#phone_id").parent("div").addClass('has-error');
        } else {
            $("#phone_id").parent("div").removeClass('has-error');
        }

        var country_code = document.getElementById('country_code_id').value;
        if (country_code == "") {
            error = 1;
            $("#country_code_id").parent("div").addClass('has-error');
        } else {
            $("#country_code_id").parent("div").removeClass('has-error');
        }
    }
    else if (type == 'email') {

        if (typeof (document.getElementsByClassName("Token")) != 'undefined' && document.getElementsByClassName("Token") != null) {
            var errorval = 1;
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                if(field_items == ''){
                    errorval = 1;
                }else  if (isEmail(field_items) == false) {
                    errorval = 1;
                } else {
                    errorval = 0;
                }
            });
            error = errorval;

        } else {
            error = 1;
        }

        if(error == 1) {
            $('.TokensContainer').attr("style","border:1px solid #fd0505")
        } else {
            $('.TokensContainer').attr("style","border:1px solid #ccc")
        }
    }
    else if (type == 'ackess') {

        if (typeof (document.getElementsByClassName("Token")) != 'undefined' && document.getElementsByClassName("Token") != null) {
            var errorval = 1;
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                if(field_items == ''){
                    errorval = 1;
                } else {
                    errorval = 0;
                }
            });
            error = errorval;

        } else {
            error = 1;
        }

        if(error == 1) {
            $('.TokensContainer').attr("style","border:1px solid #fd0505");
        } else {
            $('.TokensContainer').attr("style","border:1px solid #ccc");
        }
    } else {
        //toastr.error("Please select any one options!");
        error = 1;
    }

    return error;
}

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function getDataFROMData(label) {
    var ajaxLoading = false;

    $('form#getDataFROMData').on('submit', function(e) {
        toastr.remove();
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var x = [];
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                x.push(field_items);
            });
            var formData = new FormData(document.getElementById("getDataFROMData"));
            var error = checkerrorGetDataFROM();
            if (error == 1) {
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: burl + '/i-d-card/getDataFROMData?label='+label,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {
                        toastr.remove();
                        if (response.status == 'success') {
                            ajaxLoading = false;
                            $('#getDataFROMData').trigger('reset');
                            $('#getDataFROMDataSubmit').trigger('click');
                            // $("#add-global-form #akcessid").val(response.akcessId);
                            // $("#add-global-form #email").val(response.email);
                            // $("#add-global-form #firstname").val(response.firstName);
                            // $("#add-global-form #lastname").val(response.lastName);
                            // $("#add-global-form #mobilenumber").val(response.phone);
                            $('#getDataFROMModalModule').modal('hide');
                            toastr.success(response.message);
                            return false;
                        } else {
                            ajaxLoading = false;
                            $('#getDataFROMData').trigger('reset');
                            toastr.error(response.message);
                            return false;
                        }

                    },
                });
                return false;
            }
        }
    });
}

function sendPortalData() {
    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var formData = new FormData(document.getElementById("SendPortalData"));
            var error = checkerrorPortal();
            if (error == 1) {
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: burl + '/messaging/sendPortalData',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                            ajaxLoading = false;
                            var eid = $('#eid').val();
                            $('#SendPortalData').trigger('reset');
                            $('#sendPortalDatasubmit').trigger('click');
                            $("#portal").removeClass('has-error');
                            $("#multiple_portal").removeClass('has-error');
                            $('#eid').val(eid);
                            toastr.success(response.data);
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function sendInvitationData() {
    //var ajaxLoading = false;
    //$('form').on('submit', function(e) {
    //    e.preventDefault();
    //    if (!ajaxLoading) {
    //        ajaxLoading = true;
    //        var x = [];
    //        $('.Token').each(function(index, obj)
    //        {
    //            var field_items = $(this).attr('data-value');
    //            x.push(field_items);
    //        });
    //        $('#email').val(x);
    //        var formData = new FormData(document.getElementById("SendInvitationData"));
    //        var error = checkerrorInvitation();
    //        if (error == 1) {
    //            return false;
    //        } else {
    //            $.ajax({
    //                type: 'POST',
    //                url: burl + '/messaging/SendInvitationData',
    //                data: formData,
    //                dataType: 'json',
    //                headers: {
    //                    'X-CSRF-Token': csrfToken
    //                },
    //                processData: false,
    //                contentType: false,
    //                beforeSend: function() {
    //                    $('#load').show();
    //                    document.getElementById('load').style.visibility = "visible";
    //                },
    //                complete: function() {
    //                    $('#load').hide();
    //                    document.getElementById('load').style.visibility = "hidden";
    //                },
    //                success: function(response) {
    //
    //                    if (response.message == 'success') {
    //                        ajaxLoading = false;
    //                        var eid = $('#eid').val();
    //                        $('#SendInvitationData').trigger('reset');
    //                        $('#sendInvitationDatasubmit').trigger('click');
    //                        $('#SendInvitationData .form-group').hide();
    //                        $('#eid').val(eid);
    //                        toastr.success(response.data);
    //
    //                    }
    //                    return false;
    //                },
    //            });
    //            return false;
    //        }
    //    }
    //});

    var x = [];
    $('.Token').each(function(index, obj)
    {
        var field_items = $(this).attr('data-value');
        x.push(field_items);
    });
    $('#email').val(x);
    var formData = new FormData(document.getElementById("SendInvitationData"));
    var error = checkerrorInvitation();
    if (error == 1) {
        return false;
    } else {
        $.ajax({
            type: 'POST',
            url: burl + '/messaging/SendInvitationData',
            data: formData,
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";
            },
            success: function(response) {

                if (response.message == 'success') {
                    //ajaxLoading = false;
                    var eid = $('#eid').val();
                    $('#SendInvitationData').trigger('reset');
                    //$('#sendInvitationDatasubmit').trigger('click');
                    $('#SendInvitationData .form-group').hide();
                    $('#eid').val(eid);
                    toastr.success(response.data);

                }
                return false;
            },
        });
        return false;
    }

}


function sendEformData() {
    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var x = [];
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                x.push(field_items);
            });
            $('#email').val(x);
            var type_checked = $("input[name='inlineRadioOptions']:checked").val();
            var formData = new FormData(document.getElementById("SendEformData"));
            var error = checkerror();
            if (error == 1) {
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: baseurl + '/SendEformData',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                            ajaxLoading = false;
                            var eid = $('#eid').val();
                            $('#SendEformData').trigger('reset');
                            $('#send').trigger('click');
                            if (type_checked == 'email') {
                                $("#SendEformData #inlineRadio1").trigger('click');
                            } else if (type_checked == 'phone') {
                                $("#SendEformData #inlineRadio2").trigger('click');
                            } else if (type_checked == 'ackess') {
                                $("#SendEformData #inlineRadio3").trigger('click');
                            }
                            $('#eid').val(eid);
                            $(".TokensContainer .Token a.Close").trigger('click');
                            $(".remove_all").remove();

                            //$('#sendEformModalModule .form-group').hide();

                            toastr.success(response.data);
                        } else {
                            ajaxLoading = false;
                            toastr.error(response.data);
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function sendData(type) {
    //var ajaxLoading = false;
    //$('form').on('submit', function(e) {
    //    e.preventDefault();
    //    if (!ajaxLoading) {
    //        ajaxLoading = true;
    //        var x = [];
    //        $('.Token').each(function(index, obj)
    //        {
    //            var field_items = $(this).attr('data-value');
    //            x.push(field_items);
    //        });
    //        $('#email').val(x);
    //        var formData = new FormData(document.getElementById("SendDoc"));
    //        //var error = checkerror();
    //        var error = 0;
    //        var type_checked = $("input[name='inlineRadioOptions']:checked").val();
    //        if (error == 1) {
    //            return false;
    //        } else {
    //            $.ajax({
    //                type: 'POST',
    //                url: burl + '/i-d-card/sendData?type='+type,
    //                data: formData,
    //                dataType: 'json',
    //                headers: {
    //                    'X-CSRF-Token': csrfToken
    //                },
    //                processData: false,
    //                contentType: false,
    //                beforeSend: function() {
    //                    $('#load').show();
    //                    document.getElementById('load').style.visibility = "visible";
    //                },
    //                complete: function() {
    //                    $('#load').hide();
    //                    document.getElementById('load').style.visibility = "hidden";
    //                },
    //                success: function(response) {
    //
    //                    if (response.message == 'success') {
    //                        ajaxLoading = false;
    //                        var idcardid = $('#idcardid').val();
    //                        $('#SendDoc').trigger('reset');
    //
    //                        //$('#send').trigger('click');
    //
    //                        // if (type_checked == 'email') {
    //                        //     $("#sendModalModule #inlineRadio1").trigger('click');
    //                        // } else if (type_checked == 'phone') {
    //                        //     $("#sendModalModule #inlineRadio2").trigger('click');
    //                        // } else if (type_checked == 'ackess') {
    //                        //     $("#sendModalModule #inlineRadio3").trigger('click');
    //                        // }
    //
    //                        $('#sendModalModule').modal('hide');
    //
    //                        $('#idcardid').val(idcardid);
    //                        $(".TokensContainer .Token a.Close").trigger('click');
    //                        $(".remove_all").remove();
    //
    //                        toastr.success(response.data);
    //                        return false;
    //                    }
    //                    return false;
    //                },
    //            });
    //            return false;
    //        }
    //    }
    //
    //});

            var x = [];
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                x.push(field_items);
            });
            $('#email').val(x);
            var formData = new FormData(document.getElementById("SendDoc"));
            //var error = checkerror();
            var error = 0;
            var type_checked = $("input[name='inlineRadioOptions']:checked").val();
            if (error == 1) {
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: burl + '/i-d-card/sendData?type='+type,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                    //ajaxLoading = false;
                            var idcardid = $('#idcardid').val();
                            $('#SendDoc').trigger('reset');

                    //$('#send').trigger('click');

                            // if (type_checked == 'email') {
                            //     $("#sendModalModule #inlineRadio1").trigger('click');
                            // } else if (type_checked == 'phone') {
                            //     $("#sendModalModule #inlineRadio2").trigger('click');
                            // } else if (type_checked == 'ackess') {
                            //     $("#sendModalModule #inlineRadio3").trigger('click');
                            // }          

                            $('#sendModalModule').modal('hide');

                            $('#idcardid').val(idcardid);
                            $(".TokensContainer .Token a.Close").trigger('click');
                            $(".remove_all").remove();

                            toastr.success(response.data);
                            return false;
                        }
                        return false;
                    },
                });
                return false;
            }
        }

function sendDataIDCard(type) {
    //var ajaxLoading = false;
    //$('form').on('submit', function(e) {
    //    e.preventDefault();
    //    if (!ajaxLoading) {
    //        ajaxLoading = true;
    //        var x = [];
    //        $('.Token').each(function(index, obj)
    //        {
    //            var field_items = $(this).attr('data-value');
    //            x.push(field_items);
    //        });
    //        $('#email_id').val(x);
    //        var formData = new FormData(document.getElementById("SendDocIDCard"));
    //        //var error = checkerrorIDCArd();
    //        var error = 0;
    //        var type_checked = $("input[name='inlineRadioOptions']:checked").val();
    //        if (error == 1) {
    //            return false;
    //        } else {
    //            $.ajax({
    //                type: 'POST',
    //                url: burl + '/i-d-card/sendData?type='+type,
    //                data: formData,
    //                dataType: 'json',
    //                headers: {
    //                    'X-CSRF-Token': csrfToken
    //                },
    //                processData: false,
    //                contentType: false,
    //                beforeSend: function() {
    //                    $('#load').show();
    //                    document.getElementById('load').style.visibility = "visible";
    //                },
    //                complete: function() {
    //                    $('#load').hide();
    //                    document.getElementById('load').style.visibility = "hidden";
    //                },
    //                success: function(response) {
    //
    //                    if (response.message == 'success') {
    //                        ajaxLoading = false;
    //                        var idcardid = $('#idcardid').val();
    //                        $('#SendDocIDCard').trigger('reset');
    //
    //                        //$('#send').trigger('click');
    //
    //                        // if (type_checked == 'email') {
    //                        //     $("#SendDocIDCard #viewReceivedInlineRadio1").trigger('click');
    //                        // } else if (type_checked == 'phone') {
    //                        //     $("#SendDocIDCard #viewReceivedInlineRadio2").trigger('click');
    //                        // } else if (type_checked == 'ackess') {
    //                        //     $("#SendDocIDCard #viewReceivedInlineRadio3").trigger('click');
    //                        // }
    //
    //                        $('#sendIDCardModalModule').modal('hide');
    //
    //                        $('#idcardid').val(idcardid);
    //                        $(".TokensContainer .Token a.Close").trigger('click');
    //                        $(".remove_all").remove();
    //                        toastr.success(response.data);
    //                    }
    //                    return false;
    //                },
    //            });
    //            return false;
    //        }
    //    }
    //});


            var x = [];
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                x.push(field_items);
            });
            $('#email_id').val(x);
            var formData = new FormData(document.getElementById("SendDocIDCard"));
            //var error = checkerrorIDCArd();   
            var error = 0;
            var type_checked = $("input[name='inlineRadioOptions']:checked").val();
            if (error == 1) {
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: burl + '/i-d-card/sendData?type='+type,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                    //ajaxLoading = false;
                            var idcardid = $('#idcardid').val();
                            $('#SendDocIDCard').trigger('reset');

                            //$('#send').trigger('click');

                            // if (type_checked == 'email') {
                            //     $("#SendDocIDCard #viewReceivedInlineRadio1").trigger('click');
                            // } else if (type_checked == 'phone') {
                            //     $("#SendDocIDCard #viewReceivedInlineRadio2").trigger('click');
                            // } else if (type_checked == 'ackess') {
                            //     $("#SendDocIDCard #viewReceivedInlineRadio3").trigger('click');
                            // }    

                            $('#sendIDCardModalModule').modal('hide');

                            $('#idcardid').val(idcardid);
                            $(".TokensContainer .Token a.Close").trigger('click');
                            $(".remove_all").remove();
                            toastr.success(response.data);
                        }
                        return false;
                    },
                });
                return false;
            }
        }

function sendNotification() {
    var x = [];
    $('#SendNotifications .Token').each(function(index, obj)
    {
        var field_items = $(this).attr('data-value');
        x.push(field_items);
    });
    $('#ackess_id_notification').val(x);
    var error = checkNotificationerror();
    if (error == 1) {
        return false;
    } else {
        $('form').submit();
    }
}

function checkNotificationerror() {

    var error = 0;

    var ackess = document.getElementById('ackess_notification').value;

    if (ackess == "" && $('#send_notification_to_all_user').is(':checked') == false) {
        error = 1;
        $("#SendNotifications .global-tokenize .TokensContainer").addClass('has-error');
    } else {
        $("#SendNotifications .global-tokenize .TokensContainer").removeClass('has-error');
    }

    var subj = document.getElementById('subj_notification').value;
    if (subj == "") {
        error = 1;
        $("#subj_notification").addClass('has-error');
    } else {
        $("#subj_notification").removeClass('has-error');
    }

    var message_field = document.getElementById('message-field_notification').value;
    if (message_field == "") {
        error = 1;
        $("#message-field_notification").addClass('has-error');
    } else {
        $("#message-field_notification").removeClass('has-error');
    }

    return error;
}


function checkSenderror() {

    var error = 0;

    var ackess = document.getElementById('ackess').value;

    if (ackess == "" && $('#send_msg_to_all_user').is(':checked') == false) {
        error = 1;
        $("#ackess").addClass('has-error');
    } else {
        $("#ackess").removeClass('has-error');
    }

    var message_field = document.getElementById('message-field').value;
    if (message_field == "") {
        error = 1;
        $("#message-field").addClass('has-error');
    } else {
        $("#message-field").removeClass('has-error');
    }

    return error;
}

$(function() {

    if (typeof (document.getElementById("messaging-page")) != 'undefined' && document.getElementById("messaging-page") != null) {
        $('#ackess').on('focus blur keydown keyup focusout keypress', function() {
            checkSenderror()
        });

        $('#message-field').on('focus blur keydown keyup focusout keypress', function() {
            checkSenderror()
        });
    }

    if (typeof (document.getElementById("send-notification-form")) != 'undefined' && document.getElementById("send-notification-form") != null) {
        $('#ackess_notification').on('focus blur keydown keyup focusout keypress', function() {
            checkNotificationerror()
        });

        $('#subj_notification').on('focus blur keydown keyup focusout keypress', function() {
            checkNotificationerror()
        });

        $('#message-field_notification').on('focus blur keydown keyup focusout keypress', function() {
            checkNotificationerror()
        });
    }
});

function sendMessage() {
    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var x = [];
            $('.Token').each(function(index, obj)
            {
                var field_items = $(this).attr('data-value');
                x.push(field_items);
            });
            $('#ackess_id').val(x);
            var formData = new FormData(document.getElementById("SendMessage"));
            var error = checkSenderror();
            if (error == 1) {

                var ackess = document.getElementById('ackess').value;
                if (ackess == "" && $('#send_msg_to_all_user').is(':checked') == false)
                {
                    toastr.error('Please select AKcess ID or check select all option');
                }
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: baseurl + '/sendMessage',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {
                        if (response.message == 'success') {
                            ajaxLoading = false;
                            location.reload();
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function saveForm() {

    $('#submit_btn').click();
}

$(document).ready(function() {

    $(document).on('click','.user-form-submit-btn',function(e)
    {
        var validation_check = $("#add-user").valid();
        if(validation_check)
        {
            var ut = $('#ut').val();
            var akcessId = $('#akcessid').val();
            if(akcessId && $('#force_add_user').val() == 0)
            {
                if(ut == 'Teacher' || ut == 'Admin' || ut == 'Staff' || ut == 'Student')
                {
                    e.preventDefault();
                    $.ajax({
                        url: burl + '/users/checkAkcessid/'+akcessId+'/'+ut ,
                        type: 'GET',
                        dataType: 'json',
                        async: false,
                        success: function(res) {
                            if (res.status == 1)
                            {
                                $("#role_confirm_modal").modal('show');
                                $("#yes_role_confirm_btn").click(function()
                                {
                                    $('#force_add_user').val(1);
                                    $("#role_confirm_modal").modal('hide');
                                    $("#add-user").submit();
                                });
                                $("#role_confirm_btn").click(function(e)
                                {
                                    $('#force_add_user').val(0);
                                    $("#role_confirm_modal").modal('hide');
                                    return false;
                                });
                            }
                            else{
                                $("#add-user").submit();
                            }
                        },
                        error: function() {
                        }
                    });
                }
                else
                {
                    $("#add-user").submit();
                }
            }
            else
            {
                $("#add-user").submit();
            }
        }
    });



    ///api/verifierSignup/addVerifier

    function getToken() {

        var data;

        $.ajax({
            url: baseurl + '/getToken',
            type: 'POST',
            dataType: 'json',
            data: {api: API_KEY, orurl: BLOCKCHAIN_ORIGIN_URL, apiurl: AK_API_BASE_URL},
            async: false,
            headers: {
                'X-CSRF-Token': csrfToken
            },
            success: function(res) {
                if (res.status) {
                    data = res.token;
                }
            },
            error: function() {
            }
        }); // ajax asynchronus request 
        //the following line wouldn't work, since the function returns immediately
        return data;
    }

    $(".send").click(function(e) {
        e.preventDefault();
        var token = getToken();
    });
});

function checkEformerror() {

    var error = 0;

    var formName = document.getElementById('formName').value;
    if (formName == "") {
        error = 1;
        $("#formName").addClass('has-error');
    } else {
        $("#formName").removeClass('has-error');
    }

    var description = document.getElementById('description').value;
    if (description == "") {
        error = 1;
        $("#description").addClass('has-error');
    } else {
        $("#description").removeClass('has-error');
    }

    //var instruction = document.getElementById('instruction').value;
    //if (instruction == "") {
    //    error = 1;
    //    $("#instruction").addClass('has-error');
    //} else {
    //    $("#instruction").removeClass('has-error');
    //}

    return error;
}

function checkSaveEformerror() {

    var error = 0;

    var field_check = $(".field_check").val();
    if (field_check == 0) {
        error = 1;
    }

    return error;
}

if (typeof (document.getElementById("eformEdit")) != 'undefined' && document.getElementById("eformEdit") != null) {

//    $(function() {
//
//
//        var $tabs = $('#tab-container').tabs();
//
//        $(".tab-pane").each(function(i) {
//
//            var totalSize = $(".tab-pane").size() - 1;
//
//            if (i != totalSize) {
//                next = i + 2;
//                $(this).append("<a href='#' class='next-tab round mover' rel='" + next + "'>Next Page &raquo;</a>");
//            }
//
//            if (i == 1) {
//                $(this).append("<button type='submit' onclick='saveEform();' class='save-tab round mover' rel='save'>Save</button>");
//                $(this).append("<button type='button' onclick='removeAllField();' class='reset-tab round mover'  rel='reset'>Reset</button>");
//            }
//
//
//
//            if (i != 0) {
//
//                prev = i;
//                $(this).append("<a href='#' class='prev-tab round mover' rel='" + prev + "'>&laquo; Prev Page</a>");
//
//            }
//
//            if (i == 3) {
//
//                $(this).append("<button type='submit' onclick='editEform();' class='submit-tab round mover' rel='submit'>Submit</button>");
//
//            }
//
//        });
//
//        $('.next-tab, .prev-tab').click(function() {
//            $tabs.tabs('select', $(this).attr("rel"));
//            var getHtml = $("#notification_display").html();
//            $("#notification_get").html(getHtml);
//            $("#notification_get .fielda").remove();
//            return false;
//        });
//
//
//    });
}

function previewForm() {
    var getHtml = $("#notification_display").html();
    $("#notification_get").html(getHtml);
    $("#notification_get .fielda").remove();
    $("#notification_get .card-footer").remove();
    return false;
}

function submitEform() {
    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var formData = new FormData(document.getElementById("eFormAddPopup"));
            var error = checkEformerror();
            if (error == 1) {
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: baseurl + '/eFormData',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                            ajaxLoading = false;
                            window.location.href = baseurl + "/edit/" + response.id;
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function editEform() {
    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var formData = new FormData(document.getElementById("eformEdit"));
            var error = checkSaveEformerror();
            if (error == 1) {
                //toastr.error("Please atleast on field added.");
                return false;
            } else {
                var pulldata = 0;
                var facematch = 0;
                if ($('input[name="pulldata"]:checked').val() == 'yes' && $('input[name="facematch"]:checked').val() == 'yes') {
                    $('#addfield .custom-file-input').each(function() {
                        var check = $(this).attr('data-type');
                        if (check == 'file') {
                            pulldata = 1;
                            facematch=1;
                        }

                    });
                    if (pulldata == 0 && facematch==0) {
                        toastr.error('This eForm should include a file for face matching feature and pull data from document feature ');
                        return false;
                    }
                }
                if ($('input[name="facematch"]:checked').val() == 'yes') {

                    $('#addfield .custom-file-input').each(function() {
                        var check_facematch = $(this).attr('data-type');
                        if (check_facematch == 'file') {
                            facematch = 1;
                        }

                    });
                    if (facematch == 0) {

                        toastr.error('This eForm should include a file for face matching feature');
                        return false;
                    }
                }
                if ($('input[name="pulldata"]:checked').val() == 'yes') {

                    $('#addfield .custom-file-input').each(function() {
                        var check_pulldata = $(this).attr('data-type');
                        if (check_pulldata == 'file') {
                            pulldata = 1;
                        }

                    });
                    if (pulldata == 0) {
                        toastr.error('This eForm should include a file for pull data from document feature ');
                        return false;
                    }
                }


                var eid = $('#eid').val();
                $.ajax({
                    type: 'POST',
                    url: burl + '/eform/submitEform?eid=' + eid,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                            ajaxLoading = false;
                            window.location.href = burl + '/eform/';
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function saveEform() {
    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var formData = new FormData(document.getElementById("eformEdit"));
            var error = checkSaveEformerror();
            if (error == 1) {
                //toastr.error("Please atleast on field added.");
                return false;
            } else {
                var pulldata = 0;
                var facematch = 0;
                if ($('input[name="pulldata"]:checked').val() == 'yes' && $('input[name="facematch"]:checked').val() == 'yes') {
                    $('#addfield .custom-file-input').each(function() {
                        var check = $(this).attr('data-type');
                        if (check == 'file') {
                            pulldata = 1;
                            facematch=1;
                        }

                    });
                    if (pulldata == 0 && facematch==0) {
                        toastr.error('This eForm should include a file for face matching feature and pull data from document feature ');
                        return false;
                    }
                }
                if ($('input[name="facematch"]:checked').val() == 'yes') {

                    $('#addfield .custom-file-input').each(function() {
                        var check_facematch = $(this).attr('data-type');
                        if (check_facematch == 'file') {
                            facematch = 1;
                        }

                    });
                    if (facematch == 0) {

                        toastr.error('This eForm should include a file for face matching feature');
                        return false;
                    }
                }
                if ($('input[name="pulldata"]:checked').val() == 'yes') {

                    $('#addfield .custom-file-input').each(function() {
                        var check_pulldata = $(this).attr('data-type');
                        if (check_pulldata == 'file') {
                            pulldata = 1;
                        }

                    });
                    if (pulldata == 0) {
                        toastr.error('This eForm should include a file for pull data from document feature ');
                        return false;
                    }
                }
                var eid = $('#eid').val();
                $.ajax({
                    type: 'POST',
                    url: burl + '/eform/editField?eid=' + eid,
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        //$('#load').show();
                        //document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        //$('#load').hide();
                        //document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {

                        if (response.message == 'success') {
                            ajaxLoading = false;
                            $('#idcardid').val('');
                            $('#SendDoc').trigger('reset');
                            //$('.close').trigger('click');
                            toastr.success(response.data);
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function addeForm() {

    var field_name = $('#field_name').attr('data-key');
    var field_type = $('#field_type').attr('data-type');
    var field_isVisible = $('#field_isVisible').attr('data-isVisible');
    var field_section = $('#field_section').attr('data-section');
    var field_ids = $('#field_ids').attr('data-ids');
    var field_items = $('#field_items').attr('data-items');
    var field_label = $('#field_label').val();
    var field_label_instructions = $('textarea#field_label_instructions').val();
    var field_mandate = $('input[name="field_mandate[' + field_name + ']"]:checked').val();
    var field_verified = $('input[name="field_verified[' + field_name + ']"]:checked').val();
    var signature_required = $('input[name="signature_required[' + field_name + ']"]:checked').val();


    var verification_grade = $('#field_verification_grade').val();
    var verification_grade_text = '';
    if (verification_grade == 'G') {
        verification_grade_text = 'Verification grade: Government';
    } else if (verification_grade == 'F') {
        verification_grade_text = 'Verification grade: Financial';
    } else if (verification_grade == 'T') {
        verification_grade_text = 'Verification grade: Telecom';
    } else if (verification_grade == 'A') {
        verification_grade_text = 'Verification grade: Akcess';
    } else if (verification_grade == 'O') {
        verification_grade_text = 'Verification grade: Other';
    }
    var label_required = '';
    if(field_mandate == 'yes')
    {
        label_required = 'required';
    }
    var html = "";

    var field_check = '0';
    if (field_type == 'file') {

        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<div class="custom-file">';
        html += '<input type="file" class="custom-file-input" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" data-items="' + field_items + '" disabled="disabled">';
        html += '<label class="custom-file-label" for="field_' + field_name + '">Choose file</label>';
        html += '</div>';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][signature_required]" value="' + signature_required + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (signature_required == 'yes') {
            html += '<label class="d-block">Signature required: ' + signature_required + '</label>';
        } else if (signature_required == 'no') {
            html += '<label class="d-block">Signature required: ' + signature_required + '</label>';
        }
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'string') {
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="text" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" data-items="' + field_items + '" placeholder="' + field_label + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'address') {
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<textarea data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + field_items + '" class="form-control"></textarea>';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'list') {
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<select data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + field_items + '" class="form-control">';
        var split_items = field_items.split(',');
        html += '<option value="">Select ' + field_label + '</option>';
        $.each(split_items, function(key1, val1) {
            html += '<option value="' + val1 + '">' + val1 + '</option>';
        });
        html += '</select>'
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'phone') {
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="tel" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + field_items + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'number') {
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="number" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + field_items + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'date') {
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="date" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '( YYYY-MM-DD )" data-items="' + field_items + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    $("#addfield").append(html);

    $(".field_check").val(field_check);

    $('.date_field').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $('#addFieldForm').trigger('reset');

    $('#eFormFieldModalModule').modal('hide');

}

function createForm() {

    var field_items = $('#field_items').attr('data-items');
    var field_id = $('#id_create').attr('data-id');
    var random = field_id;
    var field_ids = 'ids_' + field_id;
    var field_label = $('#field_label_create').val();
    var field_label_instructions = $('textarea#field_label_instructions_create').val();
    var field_type = $('#field_type_create').val();
    var field_name_full = field_label + '_' + random;
    var field_name = field_name_full.toLowerCase();
    var field_mandate = $('input[name="field_mandate_create[' + field_id + ']"]:checked').val();
    var field_verified = $('input[name="field_verified_create[' + field_id + ']"]:checked').val();

    var verification_grade = $('#field_verification_grade_create').val();

    var verification_grade_text = '';
    if (verification_grade == 'G') {
        verification_grade_text = 'Verification grade: Government';
    } else if (verification_grade == 'F') {
        verification_grade_text = 'Verification grade: Financial';
    } else if (verification_grade == 'T') {
        verification_grade_text = 'Verification grade: Telecom';
    } else if (verification_grade == 'A') {
        verification_grade_text = 'Verification grade: Akcess';
    } else if (verification_grade == 'O') {
        verification_grade_text = 'Verification grade: Other';
    }

    var html = "";

    var field_check = '0';

    if (field_type == 'radio') {
        var data_type = 'radio';
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label>' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '</div>';
        html += '<div class="d-flex align-items-center">';
        var x = [];
        $('.Token').each(function(index, obj)
        {
            var field_items = $(this).attr('data-value');


            html += '<div class="form-check form-check-radio">';
            html += '<label class="form-check-label '+lable_required+'">';
            html += '<input class="form-check-input" type="radio" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + data_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" data-items="' + field_items + '" placeholder="' + field_label + '">' + field_items;
            html += '<span class="circle">';
            html += '<span class="check"></span>';
            html += '</span>';
            html += '</label>';
            html += '</div>';
            x.push(field_items);
        });

        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + x + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'checkbox') {
        var data_type = 'checkbox';
        lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';

        /*html += '<div class="d-flex align-items-center">';*/
        var x = [];
        $('.Token').each(function(index, obj)
        {
            var field_items = $(this).attr('data-value');
            html += '<div class="form-check">';
            html += '<label class="form-check-label">';
            html += '<input class="form-check-input" type="checkbox" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + data_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + index + '][]" data-ids="' + field_ids + '" data-items="' + field_items + '" placeholder="' + field_label + '">' + field_items;
            html += '<span class="form-check-sign">';
            html += '<span class="check"></span>';
            html += '</span>';
            html += '</label>';
            html += '</div>';
            x.push(field_items);
        });

        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + x + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        /*html += '</div>';*/

        html += '<div class="form-group col-12">';
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'select') {
        var data_type = 'string';
        lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        var x = [];
        $('.Token').each(function(index, obj)
        {
            var field_items = $(this).attr('data-value');
            x.push(field_items);
        });
        html += '<select data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + x + '" class="form-control">';
        html += '<option value="">Select ' + field_label + '</option>';
        $('.Token').each(function(index, obj)
        {
            var field_items = $(this).attr('data-value');
            html += '<option value="' + field_items + '">' + field_items + '</option>';
        });
        html += '</select>'
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + x + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'text') {
        data_type = 'string';
        var lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="text" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + data_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" data-items="" placeholder="' + field_label + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'textarea') {
        lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<textarea data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + field_items + '" class="form-control"></textarea>';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'number') {
        lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="number" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '" data-items="' + field_items + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'password') {
        lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="password" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" data-items="' + field_items + '" placeholder="' + field_label + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    if (field_type == 'date') {
        lable_required = '';
        if(field_mandate == 'yes')
        {
            lable_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+lable_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<input type="date" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="" data-section="" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" placeholder="' + field_label + '( YYYY-MM-DD )" data-items="' + field_items + '" class="form-control">';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';


        html += '</div>';

        html += '<div class="form-group col-12">';

        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }

    $("#addfield").append(html);

    $(".field_check").val(field_check);

    $('.date_field').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $(".TokensContainer .Token").remove();

    $("#newTag").hide();

    $('#addFieldForm_create').trigger('reset');

    $('#eFormCreateFieldModalModule').modal('hide');
}

function createfieldModalpopup() {

    $('#addFieldForm_create').trigger('reset');

    $('#eFormCreateFieldModalModule').modal('show');

    var id = Math.floor(Math.random() * 1000000000);

    var data_title = '';
    var data_key = id;
    var data_type = '';
    var data_isVisible = '';
    var data_section = '';
    var data_ids = id;
    var data_items = '';

    $('#field_name_create').attr("data-key", data_key);
    $('#field_type_create').attr("data-type", data_type);
    $('#field_isVisible_create').attr("data-isVisible", data_isVisible);
    $('#field_section_create').attr("data-section", data_section);
    $('#field_ids_create').attr("data-ids", data_ids);
    $('#field_items_create').attr("data-items", data_items);
    $('#id_create').attr("data-id", id);

    $('#field_label_create').attr("placeholder", data_title);
    $('#field_label_create').attr("name", "field_label[" + data_key + "]");
    $('#field_label_create').val(data_title);

    $('#field_label_instructions_create').attr("name", "field_label_instructions_create[" + data_key + "]");

    $('#field_mandate_yes_create').attr("name", "field_mandate_create[" + data_key + "]");
    $('#field_mandate_no_create').attr("name", "field_mandate_create[" + data_key + "]");

    $('#fieldver_yes_create').attr("name", "field_verified_create[" + data_key + "]");
    $('#fieldver_no_create').attr("name", "field_verified_create[" + data_key + "]");

    $('#verification_grade_create').attr("name", "verification_grade_create[" + data_key + "]");

    $("#fieldverified_yes_no_create").hide();

}

function fieldModalpopup(id) {

    $('#addFieldForm').trigger('reset');

    $('#eFormFieldModalModule').modal('show');

    var data_title = $('#' + id).attr('data-title');
    var data_key = $('#' + id).attr('data-key');
    var data_type = $('#' + id).attr('data-type');
    var data_isVisible = $('#' + id).attr('data-isVisible');
    var data_section = $('#' + id).attr('data-section');
    var data_ids = $('#' + id).attr('data-ids');
    var data_items = $('#' + id).attr('data-items');

    $('#field_name').attr("data-key", data_key);
    $('#field_type').attr("data-type", data_type);
    $('#field_isVisible').attr("data-isVisible", data_isVisible);
    $('#field_section').attr("data-section", data_section);
    $('#field_ids').attr("data-ids", data_ids);
    $('#field_items').attr("data-items", data_items);

    $('#field_label').attr("placeholder", data_title);
    $('#field_label').attr("name", "field_label[" + data_key + "]");
    $('#field_label').val(data_title);

    $('#field_label_instructions').attr("name", "field_label_instructions[" + data_key + "]");

    $('#field_mandate_yes').attr("name", "field_mandate[" + data_key + "]");
    $('#field_mandate_no').attr("name", "field_mandate[" + data_key + "]");

    $('#fieldver_yes').attr("name", "field_verified[" + data_key + "]");
    $('#fieldver_no').attr("name", "field_verified[" + data_key + "]");

    $('#field_signature_yes').attr("name", "signature_required[" + data_key + "]");
    $('#field_signature_no').attr("name", "signature_required[" + data_key + "]");

    $('#verification_grade').attr("name", "verification_grade[" + data_key + "]");

    $("#fieldverified_yes_no").hide();

    if (data_type == 'file') {
        $(".signature_file").show();
    } else {
        $(".signature_file").hide();
    }
}


function viewEformModalModule(datatitle,name) {
    var data_title = datatitle;
    var name = name;
    $('.headname').text(name);
    $.ajax({
        url: burl + '/eform/view/' + data_title,
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        beforeSend: function() {
            $('#load').show();
            document.getElementById('load').style.visibility = "visible";
        },
        complete: function() {
            $('#load').hide();
            document.getElementById('load').style.visibility = "hidden";
        },
        success: function(result) {
            $("#viewModal").modal('show');

            $("#viewModal #qrCodeview").html("");
            $("#viewModal #contentBody").html(result.html);
            $("#viewModal #contentTitle").html(result.title);
            $("#viewModal #qrCodeviewtext").html(result.qrdata);
            $("#viewModal #qrcode_url").val(result.qrcode_url);

            var qrcode = new QRCode(document.getElementById("qrCodeview"), {
                width: 200,
                height: 200
            });
            qrcode.makeCode($("#qrCodeviewtext").text());

        },
        error: function() {
        }
    });
    return false;
}

$(document).ready(function() {


    $(".viewEform").click(function(e) {
        e.preventDefault();
        var data_title = $(this).attr('data-title');
        $.ajax({
            url: burl + '/eform/view/' + data_title,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";
            },
            success: function(result) {
                $("#viewModal").modal('show');
                $("#viewModal #qrCodeview").html("");
                $("#viewModal #contentBody").html(result.html);
                $("#viewModal #contentTitle").html(result.title);
                $("#viewModal #qrCodeviewtext").html(result.qrdata);
                $("#viewModal #qrcode_url").val(result.qrcode_url);

                var qrcode = new QRCode(document.getElementById("qrCodeview"), {
                    width: 200,
                    height: 200
                });
                qrcode.makeCode($("#qrCodeviewtext").text());

            },
            error: function() {
            }
        });
        return false;
    });

    if (typeof (document.getElementById("eformEdit")) != 'undefined' && document.getElementById("eformEdit") != null) {
        $("#fieldverified_yes_no").hide();
        $("input[type='radio'].fieldver").click(function() {
            var fieldver = $(this).val();
            if (fieldver == 'yes') {
                $("#fieldverified_yes_no").show();
            } else if (fieldver == 'no') {
                $("#fieldverified_yes_no").hide();
            }
        });

        $("#fieldverified_yes_no_create").hide();
        $("input[type='radio'].fieldver_create").click(function() {
            var fieldver_create = $(this).val();
            if (fieldver_create == 'yes') {
                $("#fieldverified_yes_no_create").show();
            } else if (fieldver_create == 'no') {
                $("#fieldverified_yes_no_create").hide();
            }
        });

        $("#form_multiple_akcessID").hide();

        var additional_notification_selected = $("input[type=radio].additional_notification:checked").val();
        $("#form_multiple_akcessID").hide();
        if (additional_notification_selected == 'yes') {
            $("#form_multiple_akcessID").show();
        }

        $("input[type='radio'].additional_notification").click(function() {
            var additional_notification = $(this).val();
            if (additional_notification == 'yes') {
                $("#form_multiple_akcessID").show();
            } else if (additional_notification == 'no') {
                $("#form_multiple_akcessID").hide();
            }
        });

        $('#multiple_akcessID').multiselect({
            columns: 1,
            placeholder: 'Select User',
            search: true,
            selectAll: true
        });

        $('#verification_grade').change(function() {
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            var value = $option.val(); //returns the value of the selected option.
            $("#field_verification_grade").val(value);
        });

        $('#verification_grade_create').change(function() {
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            var value = $option.val(); //returns the value of the selected option.
            $("#field_verification_grade_create").val(value);
        });
        $("#newTag").hide();
        $('#field_type_create').change(function() {
            var $option = $(this).find('option:selected');

            //Added with the EDIT
            var value = $option.val(); //returns the value of the selected option.
            if (value == 'radio' || value == 'select' || value == 'checkbox') {
                $("#newTag").show();
            } else {
                $(".TokensContainer .Token").remove();
                $("#newTag").hide();
            }
        });


        $(function() {
            if (toastr.options = {
                    closeButton: !1,
                    debug: $("#debugInfo").prop("checked"),
                    progressBar: !0,
                    preventDuplicates: $("#preventDuplicates").prop("checked"),
                    positionClass: "toast-top-right",
                    onclick: null,
                    showDuration: 1000,
                    bodyOutputType: 'trustedHtml'
                });

            $("#tokenize").tokenize({
                placeholder: "Field Options (A-Z,a-z,0-9,_)",
                //searchMaxLength: 20
            });
        });
    }

    getFields('');
});

function getFields(item) {

    var country = '';
    if (item) {
        country = item.value;
    }

    $.ajax({
        url: burl + '/eform/getFields?c=' + country,
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        beforeSend: function() {
            $('#load').show();
            document.getElementById('load').style.visibility = "visible";
        },
        complete: function() {
            $('#load').hide();
            document.getElementById('load').style.visibility = "hidden";
        },
        success: function(res) {
            var html_input = '';
            var html_select = '';

            var html_select_search = '';

            var html_select_button = '';

            html_select_button += '<div class="row">';
            html_select_button += '<div class="col-md-12">';
            html_select_button += '<div class="form-group">';
            html_select_button += '<button type="button" class="mt-2 mr-3 btn btn-sm waves-effect waves-light btn-info" onclick="createfieldModalpopup();">Create New Field</button>';
            html_select_button += '<button type="button" class="mt-2 btn btn-sm waves-effect waves-light btn-info">Create Section</button>';
            //html_select_button += '<div class="clearfix" style="margin-top: 8px;"></div>';
            html_select_button += '<button type="button" class="mr-3 mt-2 btn btn-sm waves-effect waves-light btn-info" onclick="createfilefieldModalpopup();">Create File Field</button>';
            html_select_button += '<button type="button" class="mt-2 btn btn-sm waves-effect waves-light btn-info">Create ID Document Field</button><div class="clearfix"></div>';
            html_select_button += '</div>';
            html_select_button += '</div>';
            html_select_button += '</div>';

            html_select_search += '<div class="row">';
            html_select_search += '<div class="col-md-12">';
            html_select_search += '<div class="form-group">';
            html_select_search += '<label class="form-label">Search Fields:</label>';
            html_select_search += '<input type="text" class="form-control" id="Search" onkeyup="searchFunction()" placeholder="Search Fields" title="Search Fields">';
            html_select_search += '</div>';
            html_select_search += '</div>';
            html_select_search += '</div>';

            $.each(res, function(index) {
                var labelname = res[index].labelname;
                var key = res[index].key;
                var items = res[index].items;
                var section = res[index].section;
                var type = res[index].type;
                var _id = res[index]._id;
                var isVisible = res[index].isVisible;



                if (key === 'countryofresidence') {
                    html_select += '<div class="row">';
                    html_select += '<div class="col-md-12">';
                    html_select += '<div class="form-group">';
                    html_select += '<label>Filter by Country:</label>';
                    html_select += '<select class="form-control custom-select" id="verification_grade" onchange="getFields(this);">';
                    html_select += '<option value="">Global</option>';
                    $.each(items, function(key1, val1) {
                        var select = '';
                        if (country == val1) {
                            select = "selected";
                        }
                        html_select += '<option value="' + val1 + '" ' + select + '>' + val1 + '</option>';
                    });
                    html_select += '</select>';
                    html_select += '</div>';
                    html_select += '</div>';
                    html_select += '</div>';
                }

                var random = Math.floor(Math.random() * 1000000000);

                html_input += '<div class="target mb-2"><button type="button" onclick="fieldModalpopup(\'' + key + '_' + random + '\');" class="btn btn-sm btn-outline-info" data-title="' + labelname + '" data-ids="' + _id + '" id="' + key + '_' + random + '" name="' + key + '_' + random + '" data-key="' + key + '_' + random + '" data-id="' + _id + '" data-type="' + type + '" data-section="' + section + '" data-items="' + items + '" data-isVisible="' + isVisible + '" value="' + labelname + '">' + labelname + '</button></div>';
            });
            $("#getfield").html(html_select_button + html_select + html_select_search + '<div class="field_scroll">' + html_input + '</div>');

        },
        error: function() {
        }
    }); // ajax asynchronus request 
    //the following line wouldn't work, since the function returns immediately       
}

function searchFunction() {
    var input = document.getElementById("Search");
    var filter = input.value.toLowerCase();
    var nodes = document.getElementsByClassName('target');

    for (i = 0; i < nodes.length; i++) {
        if (nodes[i].innerText.toLowerCase().includes(filter)) {
            nodes[i].style.display = "block";
        } else {
            nodes[i].style.display = "none";
        }
    }
}

$(document).on('click','.reset-filed-eform-btn',function(){
    $("#reset_eform_confirm_modal").modal();
    $("#yes_reset_eform_btn").click(function()
    {
        $("#reset_eform_confirm_modal").modal('hide');
        removeAllField();
    });
    $("#no_reset_eform_btn").click(function(e)
    {
        $("#reset_eform_confirm_modal").modal('hide');
        return false;
    });
});

function removeAllField() {
    $("#addfield").html('');
}

function removeField(ids) {
    $("." + ids).remove();
}

function getEformResponse(id) {

    $.ajax({
        url: burl + '/eform-response/getFields?c=' + id,
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        beforeSend: function() {
            $('#load').show();
            document.getElementById('load').style.visibility = "visible";
        },
        complete: function() {
            $('#load').hide();
            document.getElementById('load').style.visibility = "hidden";
        },
        success: function(res) {
            $('#EformVerifyResponseData').trigger('reset');

            $('#EformVerifyResponseModalModule').modal({
                backdrop: 'static',
                keyboard: false
            });

            $("#getData").html(res.html);
            $("#myModalLabelTitle").html(res.label);
            $("#erid").val(res.erid);
            $("#view_as_pdf").attr("href",res.eformasfile_url);
            $("#customVerify").val(res.customVerify);


            return false;
        },
        error: function() {
        }
    }); // ajax asynchronus request 
    //the following line wouldn't work, since the function returns immediately       
}

function getEformResponseExpiryDate(value) {
    $('#EformResponseExpiryDateModalModule').attr("style","display:block");
    $("#expiry_date").attr("style","");
    $("#key_expiry_date").val(value);
    $("#expiry_date").val('');
}

function getEformResponseExpiryDateClose(id) {
    $('#EformResponseExpiryDateModalModule').attr("style","display:none");
}

function getEformResponseExpiryDateAdd(id) {
    var expiry_date = $("#expiry_date").val();
    if(expiry_date == "") {
        $("#expiry_date").attr("style","border-color:#ff0000");
        return false;
    } else if(expiry_date != "") {
        var key_expiry_date = $("#key_expiry_date").val();
        $("#expiry_date_label_"+key_expiry_date).text(expiry_date);
        $("#expiry_date_"+key_expiry_date).val(expiry_date);
        $('#EformResponseExpiryDateModalModule').attr("style","display:none");
    }

}

function getEformResponseVerify(fid, eid) {

    $.ajax({
        url: burl + '/eform-response/postEformResponseVerify',
        type: 'POST',
        data: 'f='+fid+'&e='+eid,
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        beforeSend: function() {
            $('#load').show();
            document.getElementById('load').style.visibility = "visible";
        },
        complete: function() {
            $('#load').hide();
            document.getElementById('load').style.visibility = "hidden";
        },
        success: function(res) {
            $('#EformVerifyResponseData').trigger('reset');

            $('#EformVerifyResponseModalModule').modal({
                backdrop: 'static',
                keyboard: false
            });

            $("#getData").html(res.html);
            $("#myModalLabelTitle").html(res.label);
            $("#erid").val(res.erid);

            $('#viewEformResponseModalModule').attr("style","display:block");
            $('#viewEformResponse').DataTable({
                aaData: res, //here we get the array data from the ajax call.
                bDestroy: true,
                aoColumns: [
                    {sTitle: "AKcess ID"},
                    {sTitle: "Verifier Name"},
                    {sTitle: "Verifier Grade"},
                    {sTitle: "Expiry Date"}
                ],
                order: [[ 1, "desc" ]]
            });
            return false;
        },
        error: function() {
        }
    }); // ajax asynchronus request 
    //the following line wouldn't work, since the function returns immediately       
}

function getEformResponseClose(id) {
    $('#viewEformResponseModalModule').attr("style","display:none");
}


function getInvitationResponse(data_title,status) {
    $.ajax({
        url: burl + '/invitationlist/view/' + data_title + '?s='+status,
        type: 'POST',
        dataType: 'json',
        headers: {
            'X-CSRF-Token': csrfToken
        },
        beforeSend: function() {
            $('#load').show();
            document.getElementById('load').style.visibility = "visible";
        },
        complete: function() {
            $('#load').hide();
            document.getElementById('load').style.visibility = "hidden";
        },
        success: function(result) {

            $("#viewInvitation").modal('show');

            $("#viewInvitation #contentBody").html(result.html);
            $("#viewInvitation #contentTitle").html(result.title);
            $("#viewInvitation #submit_invitation").html(result.button);
            $("#viewInvitation #customInvitation").html(result.select);
            $("#viewInvitation #sd").val(result.sd);

        },
        error: function() {
        }
    });
    return false;
}

function submitInvitationEformResponse() {
    var ajaxLoading = false;
    $('#load').hide();
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var customInvitation = $('#customInvitation').val();
            if(customInvitation == ""){
                $("#customInvitation").attr("style","border-color:#ff0000;");
                return false;
            } else {
                var formData = new FormData(document.getElementById("EformInvitationResponseData"));
                $.ajax({
                    type: 'POST',
                    url: burl + '/invitationlist/postInvitationData',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#load').show();
                        document.getElementById('load').style.visibility = "visible";
                    },
                    complete: function() {
                        $('#load').hide();
                        document.getElementById('load').style.visibility = "hidden";
                    },
                    success: function(response) {
                        if (response.status == 0) {
                            ajaxLoading = false;
                            $("#viewInvitation a.Close").trigger('click');
                            //toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                        return false;
                    },
                });
                return false;
            }
        }
    });
}

function submitVerifyEformResponse() {
    //var ajaxLoading = false;
    //$('form').on('submit', function(e) {
    //    e.preventDefault();
    //    if (!ajaxLoading) {
    //        ajaxLoading = true;
    //        var customVerify = $('#customVerify').val();
    //        if(customVerify == ""){
    //            $("#customVerify").attr("style","border-color:#ff0000;");
    //            return false;
    //        } else {
    //            var formData = new FormData(document.getElementById("EformVerifyResponseData"));
    //            $.ajax({
    //                type: 'POST',
    //                url: burl + '/eform-response/postVerifyData',
    //                data: formData,
    //                dataType: 'json',
    //                headers: {
    //                    'X-CSRF-Token': csrfToken
    //                },
    //                processData: false,
    //                contentType: false,
    //                beforeSend: function() {
    //                    $('#load').show();
    //                    document.getElementById('load').style.visibility = "visible";
    //                },
    //                complete: function() {
    //                    $('#load').hide();
    //                    document.getElementById('load').style.visibility = "hidden";
    //                },
    //                success: function(response) {
    //                    if (response.status == true) {
    //                        ajaxLoading = false;
    //                        $("#EformVerifyResponseModalModule a.Close").trigger('click');
    //                        toastr.success(response.message);
    //                    } else {
    //                        toastr.error(response.message);
    //                    }
    //                    return false;
    //                },
    //            });
    //            return false;
    //        }
    //    }
    //});

    var ajaxLoading = false;
    $('form').on('submit', function(e) {
        e.preventDefault();
        if (!ajaxLoading) {
            ajaxLoading = true;
            var customVerify = $('#customVerify').val();
            if(customVerify == ""){
                $("#customVerify").attr("style","border-color:#ff0000;");
                return false;
            } else {
                var formData = new FormData(document.getElementById("EformVerifyResponseData"));
                var akcessId = $('#akcessId').val();

                if(customVerify == 'create admin' || customVerify == 'create staff' || customVerify == 'create student' || customVerify == 'create teacher')
                {
                    $.ajax({
                        url: burl + '/users/checkAkcessid/'+akcessId+'/'+customVerify ,
                        type: 'GET',
                        dataType: 'json',
                        async: false,
                        success: function(res) {
                            if (res.status == 1)
                            {
                                $('#EformVerifyResponseModalModule').css('z-index',0);
                                //$('#role_confirm_modal').css('z-index',9999);
                                $("#role_confirm_modal").modal('show');
                                $("#yes_role_confirm_btn").click(function()
                                {
                                    $.ajax({
                                        type: 'POST',
                                        url: burl + '/eform-response/postVerifyData',
                                        data: formData,
                                        dataType: 'json',
                                        headers: {
                                            'X-CSRF-Token': csrfToken
                                        },
                                        processData: false,
                                        contentType: false,
                                        beforeSend: function() {
                                            $('#load').show();
                                            document.getElementById('load').style.visibility = "visible";
                                        },
                                        complete: function() {
                                            $('#load').hide();
                                            document.getElementById('load').style.visibility = "hidden";
                                        },
                                        success: function(response) {
                                            if (response.status == true) {
                                                ajaxLoading = false;
                                                //$("#EformVerifyResponseModalModule a.Close").trigger('click');
                                                toastr.success(response.message);
                                                $("#role_confirm_modal").modal('hide');
                                                $("#EformVerifyResponseModalModule").modal('hide');

                                                $('#EformVerifyResponseModalModule').css('z-index',1050);

                                                //$('#EformVerifyResponseModalModule').css('z-index',1050);
                                                //$('#role_confirm_modal').css('z-index',1050);


                                            } else {
                                                toastr.error(response.message);
                                            }
                                            return false;
                                        },
                                    });
                                    return false;
                                });
                                $("#no_role_confirm_btn").click(function(e)
                                {
                                    $('#EformVerifyResponseModalModule').css('z-index',1050);
                                    //$('#EformVerifyResponseModalModule').css('z-index',1050);
                                    //$('#role_confirm_modal').css('z-index',1050);
                                    $("#role_confirm_modal").modal('hide');
                                    return false;
                                });
                            }
                            else
                            {
                                $.ajax({
                                    type: 'POST',
                                    url: burl + '/eform-response/postVerifyData',
                                    data: formData,
                                    dataType: 'json',
                                    headers: {
                                        'X-CSRF-Token': csrfToken
                                    },
                                    processData: false,
                                    contentType: false,
                                    beforeSend: function() {
                                        $('#load').show();
                                        document.getElementById('load').style.visibility = "visible";
                                    },
                                    complete: function() {
                                        $('#load').hide();
                                        document.getElementById('load').style.visibility = "hidden";
                                    },
                                    success: function(response) {
                                        if (response.status == true) {
                                            ajaxLoading = false;
                                            $("#EformVerifyResponseModalModule").modal('hide');
                                            //$("#EformVerifyResponseModalModule a.Close").trigger('click');
                                            toastr.success(response.message);

                                            //$('#EformVerifyResponseModalModule').css('z-index',1050);
                                            //$('#role_confirm_modal').css('z-index',1050);
                                            //$("#role_confirm_modal").modal('hide');
                                        } else {
                                            toastr.error(response.message);
                                        }
                                        return false;
                                    },
                                });
                                return false;
                            }
                        },
                        error: function() {
                        }
                    });

                }
                else
                {
                    $.ajax({
                        type: 'POST',
                        url: burl + '/eform-response/postVerifyData',
                        data: formData,
                        dataType: 'json',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#load').show();
                            document.getElementById('load').style.visibility = "visible";
                        },
                        complete: function() {
                            $('#load').hide();
                            document.getElementById('load').style.visibility = "hidden";
                        },
                        success: function(response) {
                            if (response.status == true) {
                                ajaxLoading = false;
                                //$("#EformVerifyResponseModalModule a.Close").trigger('click');
                                toastr.success(response.message);
                                $("#role_confirm_modal").modal('hide');
                                $("#EformVerifyResponseModalModule").modal('hide');

                                $('#EformVerifyResponseModalModule').css('z-index',1050);

                                //$('#EformVerifyResponseModalModule').css('z-index',1050);
                                //$('#role_confirm_modal').css('z-index',1050);


                            } else {
                                toastr.error(response.message);
                            }
                            return false;
                        },
                    });
                    return false;
                }

            }
        }
    });

}

var invitation_selected = $("input[type=radio].isclientInvitationEform:checked").val();
$("#form_send_invite").hide();
if (invitation_selected == 'yes') {
    $("#form_send_invite").show();
}
$("input[type='radio'].isclientInvitationEform").click(function() {
    var invitation_selected = $(this).val();

    if (invitation_selected == 'yes') {
        $("#form_send_invite").show();
    } else if (invitation_selected == 'no') {
        $("#form_send_invite").hide();
    }
});

var process_selected = $("input[type=radio].process:checked").val();
$(".div_process_wrapper").hide();
if (process_selected == 'yes') {
    $(".div_process_wrapper").show();
}
$("input[type='radio'].process").click(function() {
    var process_selected = $(this).val();

    if (process_selected == 'yes') {
        $(".div_process_wrapper").show();
    } else if (process_selected == 'no') {
        $(".div_process_wrapper").hide();
    }
});

var process_selected = $("input[type=radio].processpdf:checked").val();
$(".div_process_wrapper_pdf").hide();
if (process_selected == 'yes') {
    $(".div_process_wrapper_pdf").show();
}
$("input[type='radio'].processpdf").click(function() {
    var process_selected = $(this).val();

    if (process_selected == 'yes') {
        $(".div_process_wrapper_pdf").show();
    } else if (process_selected == 'no') {
        $(".div_process_wrapper_pdf").hide();
    }
});

function createfilefieldModalpopup() {

    $('#addfileFieldForm').trigger('reset');

    $('#eFormfileFieldModalModule').modal('show');

    var id = Math.floor(Math.random() * 1000000000);

    var data_title = '';
    var data_key = id;
    var data_type = '';
    var data_isVisible = '';
    var data_section = '';
    var data_ids = id;
    var data_items = '';

    $('#filefield_name').attr("data-key", data_key);
    $('#filefield_type').attr("data-type", data_type);
    $('#filefield_isVisible').attr("data-isVisible", data_isVisible);
    $('#filefield_section').attr("data-section", data_section);
    $('#filefield_ids').attr("data-ids", data_ids);
    $('#filefield_items').attr("data-items", data_items);
    $('#fileid').attr("data-id", id);

    $('#filefield_label').attr("placeholder", data_title);
    $('#filefield_label').attr("name", "field_label[" + data_key + "]");
    $('#filefield_label').val(data_title);

    $('#filefield_label_instructions').attr("name", "field_label_instructions[" + data_key + "]");

    $('#filefield_mandate_yes').attr("name", "field_mandate[" + data_key + "]");
    $('#filefield_mandate_no').attr("name", "field_mandate[" + data_key + "]");

    $('#filefieldver_yes').attr("name", "field_verified[" + data_key + "]");
    $('#filefieldver_no').attr("name", "field_verified[" + data_key + "]");

    $('#filefield_signature_yes').attr("name", "field_signature[" + data_key + "]");
    $('#filefield_signature_no').attr("name", "field_signature[" + data_key + "]");


    $("#fieldverified_yes_no").hide();

}

function addFileFieldeForm() {
    var field_name = $('#filefield_name').attr('data-key');

    var field_type = $('#filefield_type').attr('data-type');
    var field_type = 'file';
    var field_isVisible = $('#field_isVisible').attr('data-isVisible');
    var field_section = $('#field_section').attr('data-section');
    var field_ids = $('#field_ids').attr('data-ids');
    var field_items = $('#field_items').attr('data-items');
    var field_label = $('#filefield_label').val();
    var field_label_instructions = $('textarea#field_label_instructions').val();
    var field_mandate = $('input[name="field_mandate[' + field_name + ']"]:checked').val();
    var field_verified = $('input[name="field_verified[' + field_name + ']"]:checked').val();
    var signature_required = $('input[name="signature_required[' + field_name + ']"]:checked').val();


    var verification_grade = $('#field_verification_grade').val();
    var verification_grade_text = '';
    if (verification_grade == 'G') {
        verification_grade_text = 'Verification grade: Government';
    } else if (verification_grade == 'F') {
        verification_grade_text = 'Verification grade: Financial';
    } else if (verification_grade == 'T') {
        verification_grade_text = 'Verification grade: Telecom';
    } else if (verification_grade == 'A') {
        verification_grade_text = 'Verification grade: Akcess';
    } else if (verification_grade == 'O') {
        verification_grade_text = 'Verification grade: Other';
    }

    var html = "";

    var field_check = '0';
    if (field_type == 'file') {

        var label_required = '';
        if(field_mandate == 'yes')
        {
            label_required = 'required';
        }
        html += '<div class="col-12 col-lg-6 ' + field_ids + '">';
        html += '<div class="row">';
        html += '<div class="form-group col-12">';
        html += '<label class="'+label_required+'">' + field_label + '</label>';
        html += '<a href="javascript:void(0);" class="btn-delete fielda" onclick="removeField(\'' + field_ids + '\')"><i class="fa fa-trash"></i></a>';
        html += '<div class="custom-file">';
        html += '<input type="file" class="custom-file-input" data-instructions="' + field_label_instructions + '" data-name="' + field_name + '" data-type="' + field_type + '" data-isVisible="' + field_isVisible + '" data-section="' + field_section + '" data-verification-grade="' + verification_grade + '" data-fieldver="' + field_verified + '" data-field_mandate="' + field_mandate + '" id="field_' + field_name + '" name="field_name[' + field_name + '][]" data-ids="' + field_ids + '" data-items="' + field_items + '" disabled="disabled">';
        html += '<label class="custom-file-label" for="field_' + field_name + '">Choose file</label>';
        html += '</div>';
        html += '<input type="hidden" name="field_name[' + field_name + '][instructions]" value="' + field_label_instructions + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][type]" value="' + field_type + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][isVisible]" value="' + field_isVisible + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][section]" value="' + field_section + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][verification_grade]" value="' + verification_grade + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][fieldver]" value="' + field_verified + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][field_mandate]" value="' + field_mandate + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][ids]" value="' + field_ids + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][items]" value="' + field_items + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][keyfields]" value="' + field_name + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][name]" value="' + field_label + '">';
        html += '<input type="hidden" name="field_name[' + field_name + '][signature_required]" value="' + signature_required + '">';

        html += '</div>';

        html += '<div class="form-group col-12">';
        if (signature_required == 'yes') {
            html += '<label class="d-block">Signature required: ' + signature_required + '</label>';
        } else if (signature_required == 'no') {
            html += '<label class="d-block">Signature required: ' + signature_required + '</label>';
        }
        if (field_verified == 'yes') {
            html += '<label class="d-block">File needs to be verified: ' + field_verified + '</label>';
            html += '<label class="d-block">' + verification_grade_text + '</label>';
        }

        html += '</div>';

        html += '</div>';
        html += '</div>';
        field_check = '1';
    }
    $("#addfield").append(html);
    $('#addfileFieldForm').trigger('reset');

    $('#eFormfileFieldModalModule').modal('hide');
}

function getDataFromDocument(data_key,data_value){
    if (typeof (document.getElementById("EformVerifyResponseModalModule")) != 'undefined' && document.getElementById("EformVerifyResponseModalModule") != null) {
        var erid = $("#erid").val();
        $.ajax({
            type: 'POST',
            url: burl + '/eform-response/getDataFromDocument',
            data: {
                'c' : erid,
                'data_key' : data_key, 
                'data_value' : data_value
            },
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";

                $('#EformVerifyResponseModalModule').modal('hide');
            },
            success: function(response) {

                if (response.message == 'success') {
                    ajaxLoading = false;
                    toastr.success(response.data);
                }
                return false;
            },
        });
    }
}

$(document).ready(function()
{
    $(document).on("click",".report-data-style-btn",function()
    {
        var dataFor = $(this).attr('data-for');
        var dataStyle = $(this).attr('data-style');
        if(dataFor == 'all')
        {
            if(dataStyle == 'list')
            {
                $('.all-list-view').addClass('btn-info');
                $('.all-list-view').removeClass('btn-secondary');
                $('.all-grid-view').addClass('btn-secondary');
                $('.all-grid-view').removeClass('btn-info');

                $('#all-grid-section').css('display','none');
                $('#all-list-section').css('display','block');
            }
            else
            {
                $('.all-grid-view').addClass('btn-info');
                $('.all-grid-view').removeClass('btn-secondary');
                $('.all-list-view').addClass('btn-secondary');
                $('.all-list-view').removeClass('btn-info');

                $('#all-grid-section').css('display','flex');
                $('#all-list-section').css('display','none');
            }
        }
        else
        {
            if(dataStyle == 'list')
            {
                $('.list-view').addClass('btn-info');
                $('.list-view').removeClass('btn-secondary');
                $('.grid-view').addClass('btn-secondary');
                $('.grid-view').removeClass('btn-info');

                $('#grid-section').css('display','none');
                $('#list-section').css('display','block');
            }
            else
            {
                $('.grid-view').addClass('btn-info');
                $('.grid-view').removeClass('btn-secondary');
                $('.list-view').addClass('btn-secondary');
                $('.list-view').removeClass('btn-info');

                $('#grid-section').css('display','flex');
                $('#list-section').css('display','none');
            }
        }



    });

    $(document).on("click",".change-notification-status",function()
    {
        var row = $(this);
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            url: burl + '/notifications/changeNotification/'+id,
            dataType: 'json',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";
            },
            success: function(response) {
                if (response.code == 1)
                {
                    row.text('SEEN');
                    row.css('background-color','green');
                    row.removeClass('change-notification-status');
                    row.removeAttr('data-id');

                    row.parent().parent().find('.badge').remove();

                    toastr.success(response.msg);
                } else {
                    toastr.error(response.msg);
                }
                return false;
            },
        });
        return false;

    });

    $(document).on("click","#add-guest-pass",function()
    {
        $('#guest-pass-form').attr('action',burl + '/accessControl/add-guest-pass');

        $('#addGuestPassModal').find('#myModalLabel').text('Generate Guest Pass');
        $('#akcessid').val("");
        $('#first-name').val("");
        $('#last-name').val("");
        $('#invitee-name').val("");
        $('#institution-name').val("");
        $('#guest-pass-date').val("");
        $('#guest-pass-time').val("");
        $('#mobile').val("");
        $('#country').val(1);
        $('#email').val("");
        $('#location').val("");
        $('#purpose').val("");
        $('#note').val("");

        $('#addGuestPassModal').modal('show');
    });

    $(function () {
        $('.datetimepicker').datetimepicker({
            //format: 'DD/MM/YYYY hh:mm A',
            minDate:new Date()
        });


        if(typeof (document.getElementById("class-attendance-report-tbl")) != 'undefined' && document.getElementById("class-attendance-report-tbl") != null)
        {
            $('.attendance-report-date').datetimepicker({
                format: 'DD/MM/YYYY',
                maxDate:new Date()
            });

            //$('.attendance-report-date').val($('.attendance-report-date').attr('data-val'));


            $('.attendance-report-date').on('dp.change', function(e){
                var date = $('.attendance-report-date').val();
                date = date.split("/");
                var newDate = new Date( date[2]+'-'+date[1]+'-'+date[0]);
                var dateUnixTimestamp = Math.floor(newDate.getTime() / 1000);
                window.location = burl + '/attendance-report/class-attendance/'+$('#idEncode').val()+'/'+dateUnixTimestamp;
            });
        }
    });

    $("#guest-pass-form").validate({
        rules: {
            first_name: "required",
            last_name: "required",
            invitee_name: "required",
            guestPassDate: "required",
            guestPassTime: "required",
            mobile: "required",
            email: "required",
            location: "required"
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    $('#main-wrapper').on('click', 'a.delete_guest_pass_btn', function(e) {
        e.preventDefault();
        //alert(1)
        $("#guest_pass_delete_modal").modal('show');
        var hl = $(this).attr('data-link');
        //alert(hl);
        $("#g_yes_btn").click(function() {
            //$('#remove_btn_form').attr('action', hl);
            $('#' + hl).click();
        });
        $("#g_no_btn").click(function(e) {
            $("#guest_pass_delete_modal").modal('hide');
            return false;

        });

    });

    $(document).on("click",".edit-guest-pass",function()
    {
        var id = $(this).attr('data-id');
        $('#addGuestPassModal').find('#myModalLabel').text('Edit Guest Pass');
        $.ajax({
            type: 'GET',
            url: burl + '/accessControl/getGuestPassData/'+id,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";
            },
            success: function(response)
            {
                if (response.code == 1)
                {
                    $('#guest-pass-form').attr('action',burl + '/accessControl/editGuestPass/'+id);
                    var data = response.data;
                    $('#akcessid').val(data.akcessId);
                    $('#first-name').val(data.first_name);
                    $('#last-name').val(data.last_name);
                    $('#invitee-name').val(data.invitee_name);
                    //$('#institution-name').val(data.institution_name);
                    $('#guest-pass-date').val(data.guest_pass_date);
                    $('#guest-pass-time').val(data.guest_pass_time);
                    $('#country').val(data.country_code);
                    $('#mobile').val(data.mobile);
                    $('#email').val(data.email);
                    $('#location').val(data.location);
                    $('#purpose').val(data.purpose);
                    $('#note').val(data.note);

                    $('#addGuestPassModal').modal('show');
                } else {
                    toastr.error(response.msg);
                }
                return false;
            },
        });
        return false;
    });


    $(document).on("click",".sendGuestPassModalModuleBtn",function()
    {
        var id = $(this).attr('data-id');
        $('#guestPassId').val(id);

        if($(this).attr('data-akcessId'))
        {
            $('.akcessId-option').css('display','block');
        }
        else
        {
            $('.akcessId-option').css('display','none');
        }

        var x = [];
        var field_items = $(this).attr('data-email');
        x.push(field_items);
        $('#email_id').val(x);
        $('#sendGuestPassModalModule').modal('show');
    });

    $(document).on("click",'#export-to-excel-btn',function(){
        $(".excel-btn").trigger("click");
    });

    $(document).on("click",'#print-btn',function(){
        $(".tbl-print-btn").trigger("click");
    });

    $(document).on("click",".sendGuestPassBtn",function(e)
    {
        var formData = new FormData(document.getElementById("SendGuestPassForm"));
        //var error = checkerrorIDCArd();
        var error = 0;
        var type_checked = $("input[name='inlineRadioOptions']:checked").val();
        if (error == 1) {
            return false;
        } else {
            $.ajax({
                type: 'POST',
                url: burl + '/i-d-card/sendData?type=guestpass',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#load').show();
                    document.getElementById('load').style.visibility = "visible";
                },
                complete: function() {
                    $('#load').hide();
                    document.getElementById('load').style.visibility = "hidden";
                },
                success: function(response) {

                    if (response.message == 'success') {
                        //ajaxLoading = false;
                        //var idcardid = $('#idcardid').val();
                        $('#SendGuestPassForm').trigger('reset');

                        //$('#send').trigger('click');

                        // if (type_checked == 'email') {
                        //     $("#SendDocIDCard #viewReceivedInlineRadio1").trigger('click');
                        // } else if (type_checked == 'phone') {
                        //     $("#SendDocIDCard #viewReceivedInlineRadio2").trigger('click');
                        // } else if (type_checked == 'ackess') {
                        //     $("#SendDocIDCard #viewReceivedInlineRadio3").trigger('click');
                        // }

                        $('#sendGuestPassModalModule').modal('hide');
                        //
                        //$('#idcardid').val(idcardid);
                        //$(".TokensContainer .Token a.Close").trigger('click');
                        //$(".remove_all").remove();
                        toastr.success(response.data);
                    }
                    return false;
                },
            });
            return false;
        }
    });
    function sendDataGuestPass() {
        var ajaxLoading = false;
        $('form').on('submit', function(e) {
            e.preventDefault();
            if (!ajaxLoading) {
                ajaxLoading = true;
                var formData = new FormData(document.getElementById("SendGuestPassForm"));
                //var error = checkerrorIDCArd();
                var error = 0;
                var type_checked = $("input[name='inlineRadioOptions']:checked").val();
                if (error == 1) {
                    return false;
                } else {
                    $.ajax({
                        type: 'POST',
                        url: burl + '/i-d-card/sendData?type=guestpass',
                        data: formData,
                        dataType: 'json',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#load').show();
                            document.getElementById('load').style.visibility = "visible";
                        },
                        complete: function() {
                            $('#load').hide();
                            document.getElementById('load').style.visibility = "hidden";
                        },
                        success: function(response) {

                            if (response.message == 'success') {
                                ajaxLoading = false;
                                //var idcardid = $('#idcardid').val();
                                $('#SendGuestPassForm').trigger('reset');

                                //$('#send').trigger('click');

                                // if (type_checked == 'email') {
                                //     $("#SendDocIDCard #viewReceivedInlineRadio1").trigger('click');
                                // } else if (type_checked == 'phone') {
                                //     $("#SendDocIDCard #viewReceivedInlineRadio2").trigger('click');
                                // } else if (type_checked == 'ackess') {
                                //     $("#SendDocIDCard #viewReceivedInlineRadio3").trigger('click');
                                // }

                                $('#sendGuestPassModalModule').modal('hide');
                                //
                                //$('#idcardid').val(idcardid);
                                //$(".TokensContainer .Token a.Close").trigger('click');
                                //$(".remove_all").remove();
                                toastr.success(response.data);
                            }
                            return false;
                        },
                    });
                    return false;
                }
            }
        });
    }


    $(document).on('change','#to_filter',function()
    {
        $.ajax({
            type: 'GET',
            url: burl + '/messaging/getMessageUsers/'+$(this).val(),
            dataType: 'html',
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";
            },
            success: function(opt)
            {
                $('#ackess').html(opt);
            }
        });
    });

    $(document).on('change','#notification_to_fiter',function()
    {
        $.ajax({
            type: 'GET',
            url: burl + '/messaging/getNotificationUsers/'+$(this).val(),
            dataType: 'html',
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#load').show();
                document.getElementById('load').style.visibility = "visible";
            },
            complete: function() {
                $('#load').hide();
                document.getElementById('load').style.visibility = "hidden";
            },
            success: function(opt)
            {
                $('#ackess_notification').html(opt);
            }
        });
    });

});

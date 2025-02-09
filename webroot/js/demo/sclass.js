$("#postAddClassForm").validate({
    rules: {
        name: {
            required: true,
            minlength: 3
        },
        userallow: {
            required: true,
            digits: true
        },
        location: {
            required: true
        },
        fk_user_id: {
            required: true
        }
    },  
    highlight: function(element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function(form) {
        $("#postAddClassForm").submit();
    }        
});
//$(document).ready(function () {
//    $( "#repeat_time" ).click(function() {
//        if(this.checked){
//            alert('checked');
//        }
//        if(!this.checked){
//            alert('Unchecked');
//        }
//    });
//});
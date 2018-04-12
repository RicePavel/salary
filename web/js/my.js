
$(document).ready(function() {
    var dateInputs = $('.dateInput');
    dateInputs.datepicker({dateFormat: 'dd.mm.yy'});
});

function getFormData(jqueryForm) {
    var data = {};
    var params = jqueryForm.serializeArray();
    for (var i = 0; i < params.length; i++) {
        var paramObj = params[i];
        data[paramObj.name] = paramObj.value;
    }
    return data;
}



$(document).ready(function() {
    
    var containerClass = 'employmentTypeContainer';
    var controllerName = 'employment-type';
    var primaryKeyName = 'employment_type_id';
    
    $('.' + containerClass + ' .addLink').click(function() {
        var url = '?r=' + controllerName + '/add&type=ajax';
        $.ajax({
            url: url,
            success: function(response) {
                var result = $.parseJSON(response);
                $('#myModal .modal-body').html(result.html);  
                $('#myModal').modal('show');  
            }
        });
        return false;
    });
    
    $('body').on('submit', '.' + containerClass + ' .addForm', function() {
        var form = $(this);
        var s = form.serialize();
        var url = '?r=' + controllerName + '/add&type=ajax&submit=1&' + s;
        $.ajax({
            url: url,
            success: function(response) {
                var result = $.parseJSON(response);
                if (result.ok) {
                    window.location.search = '?r=' + controllerName + '/list';
                } else {
                    $('#myModal .modal-body').html(result.html);
                }
            },
            error: function(response) {
                alert('error');
            }
        });
        return false;
    });
    
    $('.' + containerClass + ' .changeLink').click(function() {
        var id = $(this).attr('data-id');
        var url = '?r=' + controllerName + '/change&type=ajax&' + primaryKeyName + '=' + id;
        $.ajax({
            url: url,
            success: function(response) {
                var result = $.parseJSON(response);
                $('#myModal .modal-body').html(result.html);
                $('#myModal').modal('show');
            }
        });
        return false;
    });
    
    $('body').on('submit', '.' + containerClass + ' .changeForm', function() {
        var form = $(this);
        var s = form.serialize();
        var url = '?r=' + controllerName + '/change&type=ajax&submit=1&' + s;
        $.ajax({
            url: url,
            success: function(response) {
                var result = $.parseJSON(response);
                if (result.ok) {
                    window.location.search = '?r=' + controllerName + '/list';
                } else {
                    $('#myModal .modal-body').html(result.html);
                }
            }
        });
    });
    
});



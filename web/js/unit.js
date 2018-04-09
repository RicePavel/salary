
$(document).ready(function() {
    $('.unitContainer .addLink').click(function() {
        var url = '?r=unit/add&type=ajax';
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
    
    $('body').on('submit', '.unitContainer .addForm', function() {
        var form = $(this);
        var s = form.serialize();
        var url = '?r=unit/add&type=ajax&submit=1&' + s;
        $.ajax({
            url: url,
            success: function(response) {
                var result = $.parseJSON(response);
                if (result.ok) {
                    window.location.search = '?r=unit/list';
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
    
    $('.unitContainer .changeLink').click(function() {
        var id = $(this).attr('data-id');
        var url = '?r=unit/change&type=ajax&unit_id=' + id;
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
    
    $('body').on('submit', '.unitContainer .changeForm', function() {
        var form = $(this);
        var s = form.serialize();
        var url = '?r=unit/change&type=ajax&submit=1&' + s;
        $.ajax({
            url: url,
            success: function(response) {
                var result = $.parseJSON(response);
                if (result.ok) {
                    window.location.search = '?r=unit/list';
                } else {
                    $('#myModal .modal-body').html(result.html);
                }
            }
        });
    });
    
});



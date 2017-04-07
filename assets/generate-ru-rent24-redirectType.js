
    $(document).ready(function() {
        var BASE_URL = '';

        $('#obj_type, #ap_type').live('change', function() {
            $('#update_overlay').show();
            $('#is_update').val(1);
            $('#Apartment-form').submit();
            return false;
        });
	});
	
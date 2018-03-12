<script>
$(document).ready(function(){
	$('#btnSearch').click(function() {
		$('#msg').html('Please wait.....');
		$('#msg').css('color', 'black');
		$('#btnSearch').attr('disabled', 'disabled');
		
		var form_data = $('#frmTest').serialize();
		// console.log('form_data: ', form_data);
		$.ajax({
			type: 'post',
			dataType: 'json',
			url: 'ajax-helper-test',
			data: {'form_data': form_data, 'op': 'purchase_domain'},
			success: function(data) {
				console.log('data upon success: ', data);
				if(data['errors'] != undefined)
				{
					$('#msg').html(data['errors']);
					$('#msg').css('color', 'red');
				}
				else if(data['success'] != undefined)
				{
					$('#msg').html(data['success'].join('. '));
					$('#msg').css('color', 'blue');
				}
				$('#btnSearch').removeAttr('disabled');
			},
			error: function(data) {
				console.log('data upon error: ', data);
				$('#btnSearch').removeAttr('disabled');
			}
		});
	});
});
</script>

<div>
	<b><span id="msg"></span></b>
	<form method="post" id="frmTest" name="frmTest">
		<input name="domain" placeholder="Search Domain" />
		<input name="registrantFirstName" placeholder="registrantFirstName" />
		<input name="registrantLastName" placeholder="registrantLastName" />
		<input name="registrantAddress1" placeholder="registrantAddress1" />
		<input name="registrantCity" placeholder="registrantCity" />
		<input name="registrantPostalCode" placeholder="registrantPostalCode" />
		<input name="registrantEmail" placeholder="registrantEmail" />
		<input name="registrantStateProvince" placeholder="registrantStateProvince" />
		<input name="registrantPhone" placeholder="+001.1234567890" />		
		<input type="button" id="btnSearch" value="Search & Purchase" />
	</form>
</div>

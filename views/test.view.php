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
			url: '/helpers/ajax-helper-test',
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
	
	callAjaxHelper('GET', 'ajax-helper-list-transactions', {}, 'text', null, null, 'div-list-transactions');
	$('#div-list-transactions').on("click", "#btn_first_page, #btn_previous_page, #btn_next_page, #btn_last_page",  function() {
		var button_id = $(this).attr('id');
		var form_data = $('#frm_search').serialize();
		var data_in = {'form_data': form_data};
		data_in[button_id] = 1;
		
		callAjaxHelper('POST', 'ajax-helper-list-transactions', data_in, 'text', null, null, 'div-list-transactions');
	});
});
</script>

<div>
	<b><span id="msg"></span></b>
	<form method="post" id="frmTest" name="frmTest">
		<input name="domain" placeholder="Search Domain" />
		<input name="registrantFirstName" placeholder="FirstName" />
		<input name="registrantLastName" placeholder="LastName" />
		<input name="registrantAddress1" placeholder="Address1" />
		<input name="registrantCity" placeholder="City" />
		<input name="registrantPostalCode" placeholder="Zip" />
		<input name="registrantEmail" placeholder="Email" />
		<input name="registrantStateProvince" placeholder="State" />
		<input name="registrantPhone" placeholder="+1.4165550123x1902" />
		<input name="registrantOrgName" placeholder="Organization Name" />
		<input type="button" id="btnSearch" value="Search & Purchase" />
	</form>
	<hr />
	<a href='javascript:;' id='lnk_refresh_transactions' data-ajax-helper='ajax-helper-list-transactions' data-ajax-refresh-content='div-list-transactions' data-ajax-type='GET' data-ajax-form='frm_search'>Refresh List of Transactions</a>
	<div id='div-list-transactions'></div>
</div>

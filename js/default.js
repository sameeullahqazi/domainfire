$(document).ready(function() {
	/*
	$( "#dialog" ).dialog({
      autoOpen: false,
      modal: true,
      minWidth: 800,
      show: {
        effect: "blind",
        duration: 100
      },
      hide: {
        effect: "blind",
        duration: 100
      }
    });
 */
   
/*
	$("input[type='submit']").submit(function() {
		$(this).attr('disabled', 'disabled');
		$(this).val("Loading.....");
		alert("clicked / submitted");
		return true;
	});
	*/
	
	
	$('body').on('click', '.clickable', function() {
		console.log("this when clicked: ");
		onEvent($(this));
	});
	
	$('body').on('change', '.changeable', function() {
		onEvent($(this));
	});
	
	

	$('#lnk-refresh-captcha').click(function() {
		$('#img-captcha').attr('src', '/helpers/ajax-captcha.php?r=' + Math.random());
	});
});

/*
data-ajax-helper:			Name of helper to be called
data-ajax-refresh-content: 	ID of Div/Span in which Ajax content is to be displayed
data-ajax-callback:			callback function name to be executed in case the AJAX call is successful. This function must be defined explicitly
data-ajax-form:				ID / Name of any form from which data is to be collected before the ajax call.
data-ajax-data-type:		AJAX data type, e.g. json, text/html
*/

function onEvent(obj)
{
	var data = obj.data();
	// console.log("obj:", obj);
	if(obj.val() != undefined)
		data['obj_value'] = obj.val();
		
	var revealModalDialog = data['revealModalDialog'] != undefined ? data['revealModalDialog'] : undefined;
	// console.log('data: ', data);

	if(data['callbackFunc'] != undefined)
	{
		window[data['callbackFunc']](data);
	}
	
	
	
	if(data['ajaxHelper'] != undefined)
	{
		var type			= 'POST';
		var data_type		= 'text';
		var post_url 		= data['ajaxHelper']; // 'ajax-' + data['ajaxHelper']; // + '.php';
		var callback		= null;
		var refreshContent	= null;
		var hideContent 	= null;
		var showContent 	= null;
		var data_in 		= {};	
		
		for(data_field in data)
		{
			data_value = data[data_field];
			switch(data_field)
			{
				// AJAX Data Type (json, text, ....)
				case 'ajaxType':
					type = data_value;
					break;
					
				case 'ajaxForm':
					data_in['form_data'] = $('#' + data['ajaxForm']).serialize();
					break;
				// AJAX Data Type (json, text, ....)
				case 'ajaxDataType':
					data_type = data_value;
					break;
				// Function called upon ajax success
				case 'ajaxCallback':
					callback = data_value;
					break;
				// Id of HTML element (div, span) to be filled with the content returned
				case 'ajaxRefreshContent':
					refreshContent = data_value;
					break;
				
				case 'ajaxHideContent':
					hideContent = data_value;
					break;
				
				case 'ajaxShowContent':
					showContent = data_value;
					break;
				
				default:
					data_in[data_field] = data_value;
					break;
				
			}
			
		}
		// console.log("data_in: " , data_in);
		callAjaxHelper(type, post_url, data_in, data_type, undefined, undefined, refreshContent, undefined, undefined, undefined);
	}
}

function callAjaxHelper(type, post_url, data_in, data_type, callback, obj, refreshContent, revealModalDialog, hideContent, showContent){
	// console.log("callAjaxHelper called!");
	if(type == undefined || type == null)
		type = "POST";
		
	if(data_type == undefined)
		data_type = 'json';
	if(obj != undefined)
		obj.attr('disabled', 'disabled');
	
	$('.error').html('');
	$('.error').hide();
	$.ajax({
		type: type,
		url: '/helpers/' + post_url,
		dataType: data_type,
		data: data_in,
		success: function(data)
		{
			// console.log('data upon success', data);
			if(obj != undefined)
				obj.removeAttr('disabled');
			
			var error_token1 = '{"errors":';
			var error_token2 = "{'errors':";
			
			var edit_obj_token1 = '{"edit_obj":';
			var edit_obj_token2 = "{'edit_obj':";
			
			if(data.indexOf(error_token1) >= 0 || data.indexOf(error_token2) >= 0)
			{
				eval("data = " + data);
			// if(data['errors'] != undefined)
			//{
				for(col_name in data['errors'])
				{
					$('#error_' + col_name).html(data['errors'][col_name]);
					$('#error_' + col_name).show();
				}
				window.scrollTo(0, 0);
			}
			else
			{
				if(data.indexOf(edit_obj_token1) >= 0 || data.indexOf(edit_obj_token2) >= 0)
				{
					eval("data = " + data);
					for(col_name in data['edit_obj'])
					{
						var form_field = $("[name='" + col_name + "']");
						var data_value = data['edit_obj'][col_name];
					
						if(form_field != undefined)
						{
							if(form_field.is(":checkbox"))
							{
								if(data_value == '1')
									form_field.attr('checked', 'checked');
								else
									form_field.removeAttr('checked');
							}
							/*
							else if(form_field.is("select"))
							{
								form_field.val(data_value);
							}
							else if(form_field.is("textarea"))
							{
								form_field.val(data_value);
							}*/
							else
							{
								form_field.val(data_value);
							}
						
						}
					
					}
					$("[name='hdn_edit_id']").val(data_in['editId']);
				}
			
				if(callback != undefined)
					window[callback](data, obj);
				// console.log("refreshContent in callAjaxHelper(): ", refreshContent);
				
				if(hideContent != undefined)
					$('#' + hideContent).hide();
				
				if(showContent != undefined)
					$('#' + showContent).show();
				
				if(refreshContent != undefined)
				{
					$('#' + refreshContent).html(data);
					$('#' + refreshContent).show();
				
					if(revealModalDialog != undefined)
						$('#' + refreshContent).dialog('open');
				}
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			alert(thrownError + ": " + xhr.status);
			if(obj != undefined)
				obj.removeAttr('disabled');
		}
	});
}

/*
function onProductViewChanged(data, obj)
{
	// console.log('onProductViewChanged called with args: ', data, obj);
	var views = ['list_view', 'grid_view'];
	var val = obj.data('op');
	var other_obj_id = val == 'list_view' ? 'btnGridView' : 'btnListView';
	// console.log('val: ', val, ', other_obj_id: ', other_obj_id);
	
	$("#hdn_view").val(val);
	obj.css('font-weight', 'bold');
	$('#' + other_obj_id).css('font-weight', 'normal');
}
*/

function onLoaded()
{	
	var flash_message = $("#flash-message").text().trim();
	if(flash_message != '')
	{
		$("#flash-message").slideDown();
		window.setTimeout('hideFlash()',5000);
	}
}

function hideFlash () {
	$("#flash-message").slideUp();
}

// bugtrack.js
//
// Ron Patterson

var URL = 'bugtrack_ctlr.php';

function bt_buglist ( event )
{
	var params = "action=list";
	$.post(
		URL,
		params,
		function (response)
		{
			$('#content_div').html(response);
			bt_buglist2(event);
		}
	);
	return false;
}

function bt_buglist2 ( event, id, link )
{
	var params = "action=list2&id="+id;
	$.post(
		URL,
		params,
		function (response)
		{
			$('#results').html(response);
			$('#bt_tbl').dataTable({
				"aaSorting": [[ 0, "desc" ]],
				//"bJQueryUI": true,
				"sPaginationType": "full_numbers"
			});
		}
	);
	return false;
}

function bt_bugadd ( event )
{
	var params = "action=add";
	$.post(
		URL,
		params,
		function (response)
		{
			$('#content_div').html(response);
			$('#errors').html('');
// 			$('#bdate').datepicker(
// 			{
// 				yearRange: '-80:+1',
// 				changeMonth: true,
// 				changeYear: true
// 			});
			$('#bt_form1').submit(bt_bughandler);
			$('#cancel1').click(bt_buglist);
		}
	);
	return false;
}

function bt_bugedit ( event, id )
{
	//alert('bt_bugedit '+id);
	var params = "action=edit&id="+id;
	$.post(
		URL,
		params,
		function (response)
		{
			$('#content_div').html(response);
			$('#errors').html('');
// 			$('#bdate').datepicker(
// 			{
// 				yearRange: '-80:+1',
// 				changeMonth: true,
// 				changeYear: true
// 			});
			$('#bt_form1').submit(bt_bughandler);
			$('#cancel1').click(bt_buglist);
		}
	);
	return false;
}

function bt_bugshow ( event, id )
{
	var params = "action=show&id="+id;
	$.post(
		URL,
		params,
		function (response)
		{
			$('#content_div').html(response);
		}
	);
	return false;
}

function bt_bughelp ( event )
{
	var params = "action=help";
	$.post(
		URL,
		params,
		function (response)
		{
			$('#content_div').html(response);
		}
	);
	return false;
}

function bt_bughandler( event ) {
	//alert('bt_bughandler');
	var err = bt_validate();
	if (err != '')
	{
		$('#errors').html('Errors encountered:<br>'+err);
		return false;
	}
	var params = '&'+$('#bt_form1').serialize()+'&action=add_update';
	alert('bt_bughandler '+params);
	$.post(
		URL,
		params,
		function (response)
		{
			if (/^SUCCESS/.test(response))
			{
				var id = response.replace(/^SUCCESS /,'');
				bt_bugshow(event,id);
			}
			else
 				$('#content_div').html(response);
		}
	);
	return false;
}

function bt_bugdel ( event, id )
{
	if (!confirm("Really delete this entry?")) return false;
	var params = "action=delete&id="+id;
	$.post(
		URL,
		params,
		function (response)
		{
			if (/^SUCCESS/.test(response))
			{
				bt_buglist(event);
			}
			else
 				$('#content_div').html(response);
		}
	);
	return false;
}

function bt_add_worklog ( event, id ) {
	var params = "action=add_worklog&id="+id;
	$.post(
		URL,
		params,
		function (response)
		{
			debugger;
			$('#content_div').html(response);
			$('#bt_form2').submit(bt_workloghandler);
			$('#cancel2').click(bt_buglist);
		}
	);
	return false;
}

function bt_workloghandler( event ) {
	//alert('bt_workloghandler');
	//var err = bt_validate();
	var err = '';
	if (err != '')
	{
		$('#message').html('Errors encountered:<br>'+err);
		return false;
	}
	var params = '&'+$('#bt_form2').serialize()+'&action=worklog_add';
	//alert('bt_workloghandler '+params);
	$.post(
		URL,
		params,
		function (response)
		{
			if (/^SUCCESS/.test(response))
			{
				//var id = response.replace(/^SUCCESS /,'');
				var id = $('#id').val();
				bt_bugshow(event,id);
			}
			else
 				$('#content_div').html(response);
		}
	);
	return false;
}

function bt_get_worklog (id) {
	$('#worklogDiv').html("Loading...");
	//alert("search_list called");
	$('#worklogDiv').load('bugworklogAjax.php', { id: id });
	return false;
}

function bt_validate ( )
{
	var datere = /^[01][0-9]\/[0-3][0-9]\/(19|20)[0-9]{2}$/;
	var err = '';
	if ($.trim($('#descr').val()) == '')
		err += ' - Description must not be blank<br>';
	if ($.trim($('#product').val()) == '')
		err += ' - Product or Application must not be blank<br>';
	if ($.trim($('#comments').val()) == '')
		err += ' - Comments must not be blank<br>';
// 	if (!datere.test($('#bdate').val()))
// 		err += ' - Birth date is not valid (mm/dd/yyyy)<br>';
	return err;
}

function bt_bugadmin ( event ) {
	$('#content_div').html('admin stuff');
	return false;
}

$(function ()
{
	$('#bt_refresh_btn').button();
	$('#bt_refresh_btn').click(bt_buglist);
	$('#bt_add_btn').button();
	$('#bt_add_btn').click(bt_bugadd);
	$('#bt_admin_btn').button();
	$('#bt_admin_btn').click(bt_bugadmin);
	$('#bt_help_btn').button();
	$('#bt_help_btn').click(bt_bughelp);
	//bt_buglist();
});

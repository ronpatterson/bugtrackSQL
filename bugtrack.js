// bugtrack.js
//
// Ron Patterson

var URL = 'bugtrack_ctlr.php';

var bt =
{

	buglist: function ( event, type )
	{
		var params = "action=list&type="+type;
		$.post(
			URL,
			params,
			function (response)
			{
				var sel_val = '';
				if (type == 'bytype')
				{
					sel_val = $('#bug_type').val();
				}
				if (type == 'bystatus')
				{
					sel_val = $('#status').val();
				}
				$('#content_div').html(response);
				bt.buglist2(event, type, sel_val);
			}
		);
		return false;
	},

	buglist2: function ( event, type, sel_arg )
	{
		var params = 'action=list2';
		params += '&type='+type;
		params += '&sel_arg='+sel_arg;
		$.post(
			URL,
			params,
			function (response)
			{
				$('#results').html(response);
				$('#bt_tbl').dataTable({
					"aaSorting": [[ 0, "desc" ]]
					//"bJQueryUI": true,
					//"sPaginationType": "full_numbers"
				});
			}
		);
		return false;
	},

	bugadd: function ( event )
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
				$('#bt_form1').submit(bt.bughandler);
				$('#cancel1').click(bt.buglist);
			}
		);
		return false;
	},

	bugedit: function ( event, id )
	{
		//alert('bugedit '+id);
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
				$('#bt_form1').submit(bt.bughandler);
				$('#cancel1').click(bt.buglist);
			}
		);
		return false;
	},

	bugshow: function ( event, id )
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
	},

	bughelp: function ( event )
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
	},

	bughandler: function( event ) {
		//alert('bughandler');
		var err = bt.validate();
		if (err != '')
		{
			$('#errors').html('Errors encountered:<br>'+err);
			return false;
		}
		var params = '&'+$('#bt_form1').serialize()+'&action=add_update';
		alert('bughandler '+params);
		$.post(
			URL,
			params,
			function (response)
			{
				if (/^SUCCESS/.test(response))
				{
					var id = response.replace(/^SUCCESS /,'');
					bt.bugshow(event,id);
				}
				else
					$('#content_div').html(response);
			}
		);
		return false;
	},

	bugdel: function ( event, id )
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
					bt.buglist(event);
				}
				else
					$('#content_div').html(response);
			}
		);
		return false;
	},

	add_worklog: function ( event, id ) {
		var params = "action=add_worklog&id="+id;
		$.post(
			URL,
			params,
			function (response)
			{
				debugger;
				$('#content_div').html(response);
				$('#bt_form2').submit(bt.workloghandler);
				$('#cancel2').click(bt.buglist);
			}
		);
		return false;
	},

	workloghandler: function( event ) {
		//alert('workloghandler');
		//var err = bt.validate();
		var err = '';
		if (err != '')
		{
			$('#message').html('Errors encountered:<br>'+err);
			return false;
		}
		var params = '&'+$('#bt_form2').serialize()+'&action=worklog_add';
		//alert('workloghandler '+params);
		$.post(
			URL,
			params,
			function (response)
			{
				if (/^SUCCESS/.test(response))
				{
					//var id = response.replace(/^SUCCESS /,'');
					var id = $('#id').val();
					bt.bugshow(event,id);
				}
				else
					$('#content_div').html(response);
			}
		);
		return false;
	},

	get_worklog: function (id) {
		$('#worklogDiv').html("Loading...");
		//alert("search_list called");
		$('#worklogDiv').load('bugworklogAjax.php', { id: id });
		return false;
	},

	validate: function ( )
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
	},

	bugadmin: function ( event ) {
		$('#content_div').html('admin stuff');
		return false;
	}

}

$(function ()
{
	$('#bt_refresh_btn').button();
	$('#bt_refresh_btn').click(bt.buglist);
	$('#bt_add_btn').button();
	$('#bt_add_btn').click(bt.bugadd);
	$('#bt_admin_btn').button();
	$('#bt_admin_btn').click(bt.bugadmin);
	$('#bt_help_btn').button();
	$('#bt_help_btn').click(bt.bughelp);
	//bt.buglist();
});

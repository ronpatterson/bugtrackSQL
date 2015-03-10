// bugtrack.js
//
// Ron Patterson

var bt = // setup the bt namespace
{

	URL: 'bugtrack2_ctlr.php',
	login_content: { 'uid': 'rlpatter' },
	stimer: 0,
	group_def: 'WDD',

	check_session: function (event)
	{
		var params = "action=bt_check_session";
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{
				if (response == 0)
				{
					if (bt.stimer != 0) window.clearInterval(stimer);
					bt.stimer = 0;
					bt.login_form();
				}
				else
				{
					$('#bt_user_heading').show();
				}
			}
		});
		return false;
	},

	login_form: function (event)
	{
		if (bt.stimer != 0) window.clearInterval(stimer);
		bt.stimer = 0;
		$('#bt_user_heading').hide();
		$('#bt_login_form input[type="password"]').val('');
		$('#dialog-login').dialog({
		  width: 400,
		  maxHeight: 700,
		  modal: true,
		  title: 'BugTrack Login',
		  show: 'fade',
		  hide: 'fade',
		  draggable: false,
		  resizeable: false,
		  closeOnEscape: false,
		  dialogClass: "no-close"
		  //beforeClose: function( event, ui ) {return false;}
		});
		$('#login_errors').html('');
		$('#bt_login_form').submit(bt.login_handler);
		$('input[name="uid"]').focus();
		return false;
	},

	login_handler: function (event)
	{
		var params = "action=bt_login_handler";
		params += '&'+$('#bt_login_form').serialize();
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{
				if (/FAIL/.test(response))
				{
					$('#login_errors').html(response);
					return false;
				}
				else
				{
					var row = $.parseJSON(response);
					$('#dialog-login').dialog('close');
// 					var user = $('<div></div>')
// 						.css('position','absolute')
// 						.css('width','30em')
// 						.css('top','15px')
// 						.css('right','1em')
// 						.css('text-align','right')
// 						.css('font-size','9pt')
// 						.html('Welcome '+row.fname+' '+'<a href="#" onclick="return bt.logout_handler();">Logout</a>');
// 					$('body').append(user);
					$('#bt_user_name_top').html(row.fname+' '+row.lname);
					$('#bt_user_heading').show();
					bt.stimer = window.setInterval(bt.check_session,300000);
				}
			}
		});
		return false;
	},

	logout_handler: function (event)
	{
		var params = "action=bt_logout_handler";
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{}
		});
		window.setTimeout(bt.check_session,1000); // a bit of a delay
		return false;
	},

	buglist: function ( event, type )
	{
		var params = "action=list&type="+type;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
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
				//$('#content_div').html(response);
				$('#content_div').show();
				bt.buglist2(event, type, sel_val);
			}
		});
		return false;
	},

	buglist2: function ( event, type, sel_arg )
	{
		var params = {
			'action': 'list2',
			'type': type,
			'sel_arg': sel_arg
		};
		$('#bt_tbl tbody').off( 'click', 'button');
		var table = $('#bt_tbl').DataTable({
			'ajax': {
				'url': bt.URL,
				'type': 'post',
				'data': params
			},
			'destroy': true,
			'order': [[ 0, "asc" ]],
			'columnDefs': [ {
				'targets': -1,
				'data': null,
				'defaultContent': '<button>Show</button>'
			} ]
		});
		$('#bt_tbl tbody').on( 'click', 'button', function () {
			var data = table.row( $(this).parents('tr') ).data();
			//alert( 'user='+data[0]);
			bt.bugshow(event,data[0]);
		} );
		$('#bt_bugs_list').show();
		return false;
	},

	bugadd: function ( event )
	{
		bt.showDialogDiv('BugTrack Add','bugedit_div');
		$('#bugedit_errors').html('');
		$('#bugedit_form1 input[type="text"]').val('');
		$('#bugedit_form1 textarea').val('');
		$('#bugedit_id').html('TBD');
		$('#euser').html(bt.login_content.uid);
		$('input[name="bid"]').val('');
		$('select[name="bt_group"]').val(bt.group_def);
		$('select[name="bug_type"]').val('');
		$('select[name="status"]').val('o');
		$('select[name="priority"]').val('3');
		$('#filesDiv,#bfiles,#assignedDiv').html('');
		$('.bt_date').html('');
		return false;
	},

	edit_bug: function ( event, id )
	{
		var id2 = $('#bugshow_id').val();
		if (id) id2 = id;
		//alert('edit_bug '+id2);
		var grp = id2.replace(/\d+$/,'');
		var params = "action=edit&id="+id2;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				//$('#content_div').html(response);
				$('#bugshow_div').dialog('close');
				bt.showDialogDiv('BugTrack Edit '+id2,'bugedit_div');
				$('#bugedit_errors').html('');
				$('#bugedit_id').html(id2);
				$('#bid').val(data.id);
				$('#bug_id').html(data.bug_id);
				$('select[name="bt_group"]').val(grp);
				$('input[name="descr"]').val(data.descr);
				$('input[name="product"]').val(data.product);
				$('select[name="bug_type"]').val(data.bug_type);
				$('select[name="status"]').val(data.status);
				$('select[name="priority"]').val(data.priority);
				$('textarea[name="comments"]').val(data.comments);
				$('textarea[name="solution"]').val(data.solution);
				$('#edtm').html(data.edtm);
				$('#udtm').html(data.udtm);
				$('#cdtm').html(data.cdtm);
	// 			$('#bdate').datepicker(
	// 			{
	// 				yearRange: '-80:+1',
	// 				changeMonth: true,
	// 				changeYear: true
	// 			});
			}
		});
		return false;
	},

	bugshow: function ( event, id )
	{
		var params = "action=show&id="+id;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				//$('#content_div').html(response);
				bt.showDialogDiv('BugTrack Entry '+id,'bugshow_div');
				console.log(data);
				$('#bt_admin_errors').html('');
				$('#bug_id_v').html(id);
				$('#bugshow_id').val(id);
				$('#descr_v').html(data.descr);
				$('#product_v').html(data.product);
				$('#bt_v').html(data.t_descr);
				$('#status_v').html(data.status_descr);
				$('#priority_v').html(data.priority_descr);
				$('#comments_v').html(data.comments);
				$('#solution_v').html(data.solution);
				$('#edtm_v').html(data.edtm);
				$('#udtm_v').html(data.udtm);
				$('#cdtm_v').html(data.cdtm);
			}
		});
		return false;
	},

	bughelp: function ( event )
	{
		bt.showDialogDiv('BugTrack Help','bughelp_div');
		return false;
	},

	bughandler: function( event ) {
		//alert('bughandler '+$('#bugedit_form1').serialize()); return false;
		var err = bt.validate();
		if (err != '')
		{
			$('#bugedit_errors').html('Errors encountered:<br>'+err);
			return false;
		}
		$('#bugedit_errors').html('');
		var params = 'action=add_update&'+$('#bugedit_form1').serialize();
		//alert('bughandler '+params);
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				$('#bugedit_errors').html(response);
				bt.buglist(event);
			}
		});
		return false;
	},

	bugdel: function ( event, id )
	{
		if (!confirm("Really delete this entry?")) return false;
		var params = "action=delete&id="+id;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{
				if (/^SUCCESS/.test(response))
				{
					bt.cancelDialog(event);
				}
				else
					$('#content_div').html(response);
			}
		});
		return false;
	},

	add_worklog: function ( event, id ) {
		var params = "action=add_worklog&id="+id;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{
				bt.showDialog('BugTrack Worklog',response);
				$('#bt_form2').submit(bt.workloghandler);
				$('#cancel2').click(function(event)
				{
					bt.bugshow(event,id);
				});
			}
		});
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
		var id = $('#bt_form2 input[name="id"]').val();
		var params = '&'+$('#bt_form2').serialize()+'&action=worklog_add';
		//alert('workloghandler '+params);
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{
				if (/^SUCCESS/.test(response))
				{
					//var id = response.replace(/^SUCCESS /,'');
					//var id = $('#id').val();
					bt.bugshow(event,id);
				}
				else
					$('#message').html(response);
			}
		});
		return false;
	},

	get_worklog: function (id) {
		$('#worklogDiv').html("Loading...");
		//alert("search_list called");
		$('#worklogDiv').load('bugworklogAjax.php', { id: id });
		return false;
	},

	email_bug: function (id) {
		var params = "action=email_bug&id="+id;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (data)
			{
				$('#content_div').html(response);
				$('#bt_form3').submit(bt.workloghandler);
				$('#cancel3').click(bt.cancelDialog);
			}
		});
		return false;
	},

	validate: function ( )
	{
		var datere = /^[01][0-9]\/[0-3][0-9]\/(19|20)[0-9]{2}$/;
		var err = '';
		var f = document.bt_form1;
		if ($.trim(f.descr.value) == '')
			err += ' - Description must not be blank<br>';
		if ($.trim(f.product.value) == '')
			err += ' - Product or Application must not be blank<br>';
		if ($.trim(f.bug_type.value) == '')
			err += ' - Bug Type must be selected<br>';
		if ($.trim(f.comments.value) == '')
			err += ' - Comments must not be blank<br>';
	// 	if (!datere.test($('#bdate').val()))
	// 		err += ' - Birth date is not valid (mm/dd/yyyy)<br>';
		return err;
	},

	bugadmin: function ( event )
	{
		bt.showDialogDiv('BugTrack Admin','bt_users_list',700);
		$('#bt_admin_users_add').click(bt.user_add);
		bt.bugadmin_users();
		return false;
	},

	bugadmin_users: function ( event )
	{
		$('#bt_user_tbl tbody').off( 'click', 'button');
		var params = "action=admin_users";
		var table = $('#bt_user_tbl').DataTable({
			'ajax': {
				'url': bt.URL,
				'type': 'post',
				'data': { 'action': 'admin_users' }
			},
			'destroy': true,
			'order': [[ 0, "asc" ]],
			'columnDefs': [ {
				'targets': -1,
				'data': null,
				'defaultContent': '<button>Edit</button>'
			} ]
		});
		$('#bt_user_tbl tbody').on( 'click', 'button', function () {
			var data = table.row( $(this).parents('tr') ).data();
			//alert( 'user='+data[0]);
			bt.user_show(event,data[0]);
		} );
		return false;
	},

	user_add: function ( event )
	{
		bt.showDialogDiv('User Add','bt_users_form');
		$('#bt_admin_errors').html('');
		$('#bt_users_form input[type="text"]').val('');
		$('#uid1').html('');
		$('input[name="uid"]').val('');
		$('input[name="active"]').removeAttr('checked');
		$('input[name="active"][value="y"]').prop('checked',true);
		$('input[name="roles"]').removeAttr('checked');
		$('input[name="roles"][value="user"]').prop('checked',true);
		$('select[name="bt_group"]').val('');
	},

	user_show: function ( event, uid )
	{
		uid2 = !uid ? '' : uid;
		var params = "action=bt_user_show";
		params += '&uid='+uid2;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				data = data[0];
				console.log(data);
				bt.showDialogDiv('User Edit','bt_users_form');
				$('#bt_user_form_id').submit(bt.userhandler);
				$('#bt_admin_errors').html('');
				$('input[name="uid"]').val(uid);
				$('#uid1').html(uid);
				$('input[name="lname"]').val(data.lname);
				$('input[name="fname"]').val(data.fname);
				$('input[name="email"]').val(data.email);
				$('input[name="active"]').removeAttr('checked');
				if (data.active == 'y') $('input[name="active"][value="y"]').prop('checked',true);
				else $('input[name="active"][value="n"]').prop('checked',true);
				$('input[name="roles"]').removeAttr('checked');
				if (data.roles == 'admin') $('input[name="roles"][value="admin"]').prop('checked',true);
				else if (data.roles == 'ro') $('input[name="roles"][value="ro"]').prop('checked',true);
				else $('input[name="roles"][value="user"]').prop('checked',true);
				$('input[name="pw"]').val(data.pw);
				$('input[name="pw2"]').val(data.pw);
				$('select[name="bt_group"]').val(data.bt_group);
			}
		});
		return false;
	},

	userhandler: function( event ) {
		//alert('userhandler');
		var emailre = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
// 		var err = bt.validate();
		var err = '';
		if (emailre.test($('#email').val()))
			err += ' - Email is not valid<br>';
		if (err != '')
		{
			$('#errors').html('Errors encountered:<br>'+err);
			return false;
		}
		var params = 'action=user_add_update';
		params += '&'+$('#bt_user_form_id').serialize();
		//alert('userhandler '+params); return false;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				$('#bt_admin_errors').html(response);
				bt.bugadmin_users(event);
			}
		});
		return false;
	},

	assign_locate: function ( file )
	{
		$.get(
			file,
			function (response)
			{
				bt.showDialog('BugTrack Maintenance',response);
			}
		);
		return false;
	},

	showDialog: function ( title, content )
	{
		//if ($('#dialog-modal').dialog) $('#dialog-modal').dialog('destroy');
		$('#dialog-content').html(content);
		$('#dialog-modal').dialog({
		  width: 600,
		  maxHeight: 700,
		  modal: true,
		  title: title,
		  show: 'fade',
		  hide: 'fade',
		  close: function (e,ui)
		  {
			$(this).dialog('destroy');
		  }
		});
	},

	showDialogDiv: function ( title, div, width )
	{
		var w = width ? width : 600;
		$('#'+div).dialog({
		  width: w,
		  maxHeight: 700,
		  modal: true,
		  title: title,
		  show: 'fade',
		  hide: 'fade',
		  close: function (e,ui)
		  {
			$(this).dialog('destroy');
		  }
		});
	},

	cancelDialog: function ( event )
	{
		$('#bugedit_div').dialog('close');
		bt.buglist();
	},

	/**
	 * @param name string
	 * @param data array({val,text},...);
	 */
	build_selection: function ( name, data )
	{
		var obj = $('<select></select>').attr('name',name);
		var opt = $('<option></option>').attr('value','').html('--Select One--');
		obj.append(opt);
		for (i in data)
		{
			if (typeof(data.length) == 'undefined')
				rec = { 'cd': i, 'descr': data[i] };
			else
				rec = data[i];
			var opt = $('<option></option>').attr('value',rec.cd).html(rec.descr);
			obj.append(opt);
		}
		console.log(obj);
		return obj;
	},

	init: function ( )
	{
		var params = 'action=bt_init';
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				console.log(data);
				var sel = bt.build_selection('bt_group',data.bt_groups);
				$('#bt_groups').empty().append(sel);
				var sel = bt.build_selection('bt_group',data.bt_groups);
				$('#bt_grp').empty().append(sel);
				var sel = bt.build_selection('bug_type',data.bt_types);
				$('#btypes_s').empty().append(sel);
				var sel = bt.build_selection('status',data.bt_status);
				$('#status_s').empty().append(sel);
				var sel = bt.build_selection('priority',data.bt_priority);
				$('#priority_s').empty().append(sel);
				var sel = bt.build_selection('bug_type2',data.bt_types);
				$('#btc_types').empty().append(sel);
				var sel = bt.build_selection('status2',data.bt_status);
				$('#btc_status').empty().append(sel);
			}
		});
	}

} // end of bt namespace

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
	$('#bugedit_form1').submit(bt.bughandler);
	$('#cancel1').click(bt.cancelDialog);
	$('#bt_user_form_id').submit(bt.userhandler);
	$( document ).ajaxError(function(event, jqxhr, settings, thrownError) {
		bt.showDialog( "ERROR!", "A error occurred during server call.<br>" + thrownError );
	});
	bt.init();
	//login_content = $('#login_content').html();
	//$('#login_content').html('');
	//bt.check_session();
	//bt.buglist();
});

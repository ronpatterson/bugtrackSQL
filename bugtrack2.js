// bugtrack.js
//
// Ron Patterson, WildDog Design

var bt = // setup the bt namespace
{

	URL: 'bugtrack2_ctlr.php',
	login_content: { 'uid': 'rlpatter' },
	stimer: 0,
	group_def: 'WDD',
	group_data: {},

	check_session: function (event)
	{
		var params = "action=bt_check_session";
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				if (response == 0)
				{
					if (bt.stimer != 0) window.clearInterval(bt.stimer);
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
		if (bt.stimer != 0) window.clearInterval(bt.stimer);
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
			success: function (response)
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
					$('#bt_admin_btn').show();
					if (!/admin/.test(row.roles)) $('#bt_admin_btn').hide();
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
			success: function (response)
			{}
		});
		window.setTimeout(bt.check_session,1000); // a bit of a delay
		return false;
	},

	buglist: function ( event, type )
	{
		//console.log(event,type);
		var type2 = type ? type : '';
		var sel_val = '';
		if (type2 == 'bytype')
		{
			sel_val = " and bug_type = '"+$('select[name="bug_type2"]').val()+"'";
		}
		if (type2 == 'bystatus')
		{
			sel_val = " and b.status = '"+$('select[name="status2"]').val()+"'";
		}
		//$('#content_div').html(response);
		$('#content_div').show();
		bt.buglist2(event, type2, sel_val);
		return false;
	},

	buglist2: function ( event, type, sel_arg )
	{
		//console.log(event,type,sel_arg);
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
			'columns': [
				{'data': 'bug_id'},
				{'data': 'descr'},
				{'data': 'edtm'},
				{'data': 'status'},
				null
			],
			'columnDefs': [ {
				'targets': -1,
				'data': null,
				'defaultContent': '<button>Show</button>'
			} ]
		});
		$('#bt_tbl tbody').on( 'click', 'button', function () {
			var data = table.row( $(this).parents('tr') ).data();
			//alert( 'user='+data[0]);
			bt.bugshow(event,data.id);
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
		$('#euser').html(bt.login_content.uid);
		$('input[name="bid"]').val('');
		$('select[name="bt_group"]').val(bt.group_def);
		$('select[name="bug_type"]').val('');
		$('select[name="status"]').val('o');
		$('#assignedDiv2').html('');
		$('#bt_assign_btn2').hide();
		$('select[name="priority"]').val('3');
		$('#filesDiv,#bfiles,#assignedDiv').html('');
		$('.bt_date').html('');
		return false;
	},

	edit_bug: function ( event, id )
	{
		var id2 = $('#bug_id').val();
		if (id) id2 = id;
		//alert('edit_bug '+id2);
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
				$('#bugedit_id').html(data.bug_id);
				$('#oldstatus').val(data.status);
				var grp = data.bug_id.replace(/\d+$/,'');
				$('select[name="bt_group"]').val(grp);
				$('input[name="descr"]').val(data.descr);
				$('input[name="product"]').val(data.product);
				$('select[name="bug_type"]').val(data.bug_type);
				$('select[name="status"]').val(data.status);
				$('select[name="priority"]').val(data.priority);
				$('#assignedDiv2').html(data.aname);
				$('#bt_assign_btn2').show();
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
		//alert(id);
		//var id2 = parseInt(id.replace(/[^\d]/g,''));
		var params = "action=show&id="+id;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				//$('#content_div').html(response);
				bt.showDialogDiv('BugTrack Entry '+data.bug_id,'bugshow_div');
				//console.log(data);
				$('#bt_admin_errors').html('');
				$('#bug_id').val(data.bug_id);
				$('#bug_id2_v').html(data.bug_id);
				$('#bid').val(data.id);
				$('#descr_v').html(data.descr);
				$('#product_v').html(data.product);
				$('#bt_v').html(data.t_descr);
				$('#status_v').html(data.status_descr);
				$('#priority_v').html(data.priority_descr);
				$('#assignedDiv1').html(data.aname);
				$('#comments_v').html(data.comments);
				$('#solution_v').html(data.solution);
				$('#ename_v').html(data.ename);
				$('#edtm_v').html(data.edtm);
				$('#udtm_v').html(data.udtm);
				$('#cdtm_v').html(data.cdtm);
				bt.get_files(event);
				bt.worklog_show();
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
		params += '&id='+$('#bid').val();
		params += '&bug_id='+$('#bug_id').val();
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
				//window.setTimeout(function(e) {$('#bugedit_div').dialog('close');},3000);
			}
		});
		return false;
	},

	delete_bug: function ( event )
	{
		if (!confirm("Really delete this entry?")) return false;
		var params = 'action=delete';
		params += '&id='+$('#bid').val();
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				if (/^SUCCESS/.test(response))
				{
					$('#bugshow_div').dialog('close');
					bt.buglist(event);
				}
				else
					alert(response);
			}
		});
		return false;
	},

	assign_search: function ( event )
	{
		//alert('assign_search');
		bt.showDialogDiv('BugTrack Assign','bt_users_search', 700);
		return false;
	},

	handle_search: function ( event )
	{
		$('#bt_user_assign_tbl tbody').off( 'click', 'button');
		var f = document.bt_form9;
		var table = $('#bt_user_assign_tbl').DataTable({
			'ajax': {
				'url': bt.URL,
				'type': 'post',
				'data': { 
					'action': 'getUsersSearch',
					'lname': f.lname.value,
					'fname': f.fname.value
				}
			},
			'destroy': true,
			'order': [[ 0, "asc" ]],
			'columns': [
				{'data': 'uid'},
				{'data': 'name'},
				{'data': 'email'},
				{'data': 'roles'},
				{'data': 'active'},
				null
			],
			'columnDefs': [ {
				'targets': -1,
				'data': null,
				'defaultContent': '<button>Select</button>'
			} ]
		});
		$('#bt_user_assign_tbl tbody').on( 'click', 'button', function () {
			var data = table.row( $(this).parents('tr') ).data();
			//alert( 'user='+data[0]);
			bt.assign_user(event,data.uid);
		} );
		return false;
	},
	
	assign_user: function ( event, user )
	{
		var id = $('#bug_id').val();
		var params = 'action=assign_user';
		params += '&id='+id;
		params += '&uid='+user;
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				$('#bt_users_search').dialog('close');
				bt.bugshow(event,id);
			}
		});
		return false;
	},

	add_worklog: function ( event ) {
		bt.showDialogDiv('BugTrack Worklog','bt_worklog_form');
		$('#bt_wl_bug_id').html($('#bug_id').val());
		$('#bt_wl_descr').html($('#descr_v').html());
		$('#bt_bug_comments').html($('#comments_v').html());
		$('input[name="wl_public"][value="n"]').prop('checked',true);
		$('textarea[name="wl_comments"]').val('');
		$('#bt_wl_ename').html($('#usernm').html());
		$('#bt_wl_entry_dtm').html($('#edtm_v').html());
		$('#wl_errors').html('');
		$('textarea[name="wl_comments"]').focus();
		return true;
	},

	workloghandler: function( event ) {
		//alert('workloghandler '+$('#bt_form2').serialize()); return false;
		//var err = bt.validate();
		var err = '';
		if ($.trim($('textarea[name="wl_comments"]').val()) == '')
			err += ' - Worklog Comments must not be blank<br>';
		if (err != '')
		{
			$('#wl_errors').html('Errors encountered:<br>'+err);
			return false;
		}
		var id = $('#bid').val();
		var params = 'action=worklog_add&'+$('#bt_form2').serialize();
		params += '&usernm='+$('#usernm').val();
		params += '&id='+id;
		params += '&bug_id='+$('#bug_id').val();
		//alert('workloghandler '+params);
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				if (/^SUCCESS/.test(response))
				{
					bt.worklog_show();
					$('#bt_worklog_form').dialog('close');
				}
				else
					$('#wl_errors').html(response);
			}
		});
		return false;
	},
	
	worklog_show: function ( )
	{
		$('#bt_worklog_div').empty();
		var params = "action=get_worklog_entries";
		params += '&id='+$('#bid').val();
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				var div = $('#bt_worklog_div');
				if (data.length == 0)
					div.html('No worklog records');
				else
				{
					var tbl = $('<table></table>');
					div.append(tbl);
					for (var x=0; x<data.length; ++x)
					{
						var tr = $('<tr><th>Date/Time: '+data[x].entry_dtm+'</th></tr>');
						div.append(tr);
						tr = $('<tr><td>'+bt.nl2br(data[x].comments)+'<hr></td></tr>');
						div.append(tr);
					}
				}
			}
		});
	},

	get_worklog: function (id) {
		$('#worklogDiv').html("Loading...");
		//alert("search_list called");
		$('#worklogDiv').load('bugworklogAjax.php', { id: id });
		return false;
	},

	show_email: function ( event ) {
		bt.showDialogDiv('BugTrack Email','bt_email_div');
		$('#bug_id_email').html($('#bug_id').val());
		$('#descr_email').html($('#descr_v').html());
		$('input[name="subject"]').val($('#bug_id').val()+' - '+$('#descr_v').html());
		$('#email_errors').html('');
		return true;
	},
	
	get_files: function ( event )
	{
		$('#filesDiv').empty();
		var params = 'action=get_files';
		params += '&id='+$('#bid').val();
		params += '&bug_id='+$('#bug_id').val();
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				var out = '';
				if (data.length == 0)
					out = 'No attachments';
				else
				{
					$.each(data,function (i)
					{
						out += '<a href="get_file.php?id='+data[i].id+'" target="_blank">'+data[i].file_name+'</a> ('+data[i].file_size+') <span onclick="return remove_file('+data[i].id+');">Remove</span><br>';
					});
				}
				$('#filesDiv').html(out);
			}
		});
	},

	attach_file: function ( event )
	{
		//$('errors').update();
		$('#update_list').val("0");
		//alert("add_file called");
		w = window.open('add_file.php?id='+$('#bid').val()+'&bug_id='+$('#bug_id').val(), 'Add_file', 'width=620,height=280,resizable,menubar,scrollbars');
		//setTimeout("watch_add(w)",2000);
		bt.get_files(event);
		return false;
	},

	remove_file: function ( id )
	{
		if (!confirm('Really remove this attachment file?')) return false;
		var params = 'action=remove_file';
		params += '&fid='+$('#fid').val();
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				$('#email_errors').html(response);
			}
		});
		return false;
	},

	email_bug: function (e) {
		var err = '';
		if ($.trim($('input[name="sendto"]').val()) == '')
			err += ' - Send To must not be blank<br>';
		if ($.trim($('input[name="subject"]').val()) == '')
			err += ' - Subject must not be blank<br>';
		if (err != '')
		{
			$('#email_errors').html('Errors encountered:<br>'+err);
			return false;
		}
		var params = 'action=email_bug';
		params += '&id='+$('#bid').val();
		params += '&bug_id='+$('#bug_id').val();
		params += '&'+$('#bug_email_form').serialize();
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'html',
			success: function (response)
			{
				$('#email_errors').html(response);
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
		var table = $('#bt_user_tbl').DataTable({
			'ajax': {
				'url': bt.URL,
				'type': 'post',
				'data': { 'action': 'admin_users' }
			},
			'destroy': true,
			'order': [[ 0, "asc" ]],
			'columns': [
				{'data': 'uid'},
				{'data': 'name'},
				{'data': 'email'},
				{'data': 'roles'},
				{'data': 'active'},
				null
			],
			'columnDefs': [ {
				'targets': -1,
				'data': null,
				'defaultContent': '<button>Edit</button>'
			} ]
		});
		$('#bt_user_tbl tbody').on( 'click', 'button', function () {
			var data = table.row( $(this).parents('tr') ).data();
			//alert( 'user='+data[0]);
			bt.user_show(event,data.uid);
		} );
		return false;
	},

	user_add: function ( event )
	{
		bt.showDialogDiv('User Add','bt_users_form');
		$('#bt_admin_errors').html('');
		$('#bt_users_form input[type="text"]').val('');
		$('input[name="pw"]').val('');
		$('input[name="pw2"]').val('');
		$('input[name="uid1"]').val('');
		$('input[name="uid1"]').removeAttr('readonly');
		$('input[name="uid"]').val('');
		$('input[name="id"]').val('');
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
				//console.log(data);
				bt.showDialogDiv('User Edit','bt_users_form');
				$('#bt_user_form_id').submit(bt.userhandler);
				$('#bt_admin_errors').html('');
				$('input[name="uid"]').val(uid);
				$('input[name="id"]').val(data.id);
				$('input[name="uid1"]').val(uid);
				$('input[name="uid1"]').attr('readonly',true);
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
		//alert('userhandler '+$('#bt_user_form_id').serialize());
		var emailre = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
// 		var err = bt.validate();
		var f = document.bt_user_form;
		var err = '';
		if ($.trim(f.uid1.value) == '')
			err += " - UID must not be blank<br>";
		if ($.trim(f.lname.value) == "")
			err += " - Last Name must not be blank<br>";
		if (!emailre.test(f.email.value))
			err += ' - Email is not valid<br>';
		if ($.trim(f.bt_group.value) == "")
			err += " - Group must be selected<br>";
		if (err != '')
		{
			$('#bt_admin_errors').html('Errors encountered:<br>'+err);
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
		//alert('showDialogDiv');
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
	
	cancelDialog2: function ( event )
	{
		$('#bt_worklog_form').dialog('close');
		bt.buglist();
	},
	
	nl2br: function ( val )
	{
		return val.replace(/\r?\n/g,'<br>');
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
		//console.log(obj);
		return obj;
	},
	
	get_lookup: function ( group, cd )
	{
		//debugger;
		var descr = '';
		for (var i=0; i<group.length; ++i)
		{
			var item = group[i];
			if (cd == item.cd)
			{
				return item.descr;
			}
		}
		return 'n/a';
	},

	init: function ( )
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
		$('#bt_form2').submit(bt.workloghandler);
		$('#bt_form9').submit(bt.handle_search);
		$('#bug_email_form').submit(bt.email_bug);
		$('#cancel1').click(bt.cancelDialog);
		$('#cancel2').click(bt.cancelDialog2);
		$('#bt_user_form_id').submit(bt.userhandler);
		$('#bt_show_buttons span').button();
		$('#bt_admin_btn').show();
		var params = 'action=bt_init';
		$.ajax({
			url: bt.URL,
			type: 'post',
			data: params,
			dataType: 'json',
			success: function (data)
			{
				//console.log(data);
				bt.group_data = data;
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
				if (!/admin/.test(bt.group_data.roles)) $('#bt_admin_btn').hide();
			}
		});
	}

} // end of bt namespace

$(function ()
{
	$( document ).ajaxError(function(event, jqxhr, settings, thrownError) {
		bt.showDialog( "ERROR!", "A error occurred during server call.<br>" + thrownError );
	});
	bt.init();
	//login_content = $('#login_content').html();
	//$('#login_content').html('');
	bt.check_session();
});

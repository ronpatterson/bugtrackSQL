// fieldedits.js

// Ron Patterson, WildDog Design

// depends on the jQuery library

var w = "";

// email validator expression
var emailre = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,6})+$/;
var numre = /^[0-9]+(\.[0-9]+)*$/;
var phonere = /^([(]*[0-9]{3}[)]*[ \/-]*)*[0-9]{3}[\/-]?[0-9]{4}$/;
var datere = /^[01]?[0-9][\/-][0123]?[0-9][\/-](19|20)[0-9]{2}$/;
var timere = /^[01]?[0-9]:[0-5][0-9] (am|pm)$/i;
var linkre = /^https?:\/\//;

function checkemail (obj) {
	obj.style.backgroundColor = "#ffffff";
	//alert(str);
	if (emailre.test(obj.value)) return false;
	alert("ERROR: Invalid email address entered");
	obj.style.backgroundColor = "#ffd0d0";
	//obj.focus();
	return false;
}

function checknum (obj) {
	obj.style.backgroundColor = "#ffffff";
	var str = obj.value;
	if (str.length == 0) return true;
	if (numre.test(str)) return true;
	alert("ERROR: Invalid number entered");
	obj.style.backgroundColor = "#ffd0d0";
	//obj.focus();
	return false;
}

function checkphone (obj) {
	obj.style.backgroundColor = "#ffffff";
	var str = obj.value;
	//alert(obj.value);
	if (str.length == 0) return true;
	if (phonere.test(str)) return true;
	alert("ERROR: Invalid phone number entered");
	//document.getElementById(obj.id).focus();
	obj.style.backgroundColor = "#ffd0d0";
	//obj.focus();
	return false;
}

function validate_bugtrack () {
	// do field checks
	$('#descr').css("backgroundColor","#ffffff");
	$('#product').css("backgroundColor","#ffffff");
	$('#bug_type').css("backgroundColor","#ffffff");
	$('#priority').css("backgroundColor","#ffffff");
	$('#comments').css("backgroundColor","#ffffff");
	//$('school_id').style.backgroundColor = "#ffffff";
	var err = "";
	if ($.trim($('#descr').val()) == '') {
		err += " - Description must not be blank\n";
		$('#descr').css("backgroundColor","#ffd0d0");
	}
	var found = false;
	var sid = document.form1.bug_type;
	//alert(sid.length);
	//alert("idx="+sid.selectedIndex+", text="+sid[sid.selectedIndex].value);
	for (var i=0; i<sid.length; i++) {
		if (sid[i].selected && sid[i].value != " ") {
			found = true;
			break;
		}
	}
	if (!found) {
		err += " - Bug Type must be selected\n";
		$('#bug_type').css("backgroundColor","#ffd0d0");
	}
	found = false;
	var sid = document.form1.priority;
	//alert(sid.length);
	//alert("idx="+sid.selectedIndex+", text="+sid[sid.selectedIndex].value);
	for (var i=0; i<sid.length; i++) {
		if (sid[i].selected && sid[i].value != " ") {
			found = true;
			break;
		}
	}
	if (!found) {
		err += " - Priority must be selected\n";
		$('#priority').css("backgroundColor","#ffd0d0");
	}
	if ($.trim($('#product').val()) == '') {
		err += " - Product or Application must not be blank\n";
		$('#product').css("backgroundColor","#ffd0d0");
	}
	if ($.trim($('#comments').val()) == '') {
		err += " - Comments must not be blank\n";
		$('#comments').css("backgroundColor","#ffd0d0");
	}
	if (err != "") {
		alert("ERRORS found:\n" + err + "Please correct and submit again.");
		return false;
	}
	return confirm("Really perform this action?");
}

function validate_worklog () {
	// do field checks
	$('#comments').css("backgroundColor","#ffffff");
	//$('school_id').style.backgroundColor = "#ffffff";
	var err = "";
	if ($.trim($('#comments').val()) == '') {
		err += " - Worklog Comments must not be blank\n";
		$('#comments').css("backgroundColor","#ffd0d0");
	}
	if (err != "") {
		alert("ERRORS found:\n" + err + "Please correct and submit again.");
		return false;
	}
	if (confirm("Really add this entry?")) {
		$('#message').html('Working...');
		return true;
	}
	return false;
}

function get_files (id) {
	$('#filesDiv').html("Loading...");
	$('#filesDiv').load('get_filesAjax.php', { id: id });
}

function watch_add (w) {
	if ($('#update_list').val() == "1") {
		$('#update_list').val("0");
		w.close();
		get_files($('#id').val());
		return false;
	}
	setTimeout(function () {
		watch_add(w);
		}, 2000);
	return false;
}

function add_file () {
	//$('errors').update();
	$('#update_list').val("0");
	//alert("add_file called");
	w = window.open('add_file.php?id='+$('#id').val(), 'Add_file', 'width=620,height=280,resizable,menubar,scrollbars');
	setTimeout("watch_add(w)",2000);
	return false;
}

function remove_file (id) {
	if (!confirm('Really remove this attachment file?')) return false;
	$.post('remove_fileAjax.php', { id: id }, function (msg) {
		get_files($('#id').val());
	});
	return false;
}

function delete_entry (id) {
	if (!confirm('Really delete this bug entry?')) return false;
	$.post('bugedit2Ajax.php', { id: id }, function (msg) {
		if (!msg.match('SUCCESS')) {
			alert(reply);
		}
		else {
			window.location = "buglist.php";
		}
	});
	return false;
}


function assign_locate (id) {
	//$('#errors').html('');
	w = window.open('bugassign.php?id='+id, 'Assign', 'width=620,height=500,resizable,menubar,scrollbars');
	//setTimeout("watch_add2(w)",2000);
	return false;
}

function search_list (event) {
	$('#results').html("Working...");
	//alert("search_list called");
	$('#results').load('bugassignAjax.php', $('#form9').serializeArray());
	return false;
}

function do_assign (bid, uname) {
	$('#assignedDiv').html("Working...");
	$('#assignedDiv').load('bugassign2Ajax.php', { bid: bid, uname: uname });
	w.close();
	return false;
}

function email_bug (id) {
	//$('#errors').html('');
	w = window.open('bugsend1.php?id='+id, 'Bug_send', 'width=620,height=500,resizable,menubar,scrollbars');
	//setTimeout("watch_add2(w)",2000);
	return false;
}

function watch_add2 (w,id) {
	if ($('#update_log').val() == "1") {
		$('#update_log').val("0");
		w.close();
		get_worklog(id);
		return false;
	}
	setTimeout(function () {
		watch_add2(w,id);
		}, 2000);
	return false;
}

function add_worklog (id) {
	//$('#errors').html('');
	w = window.open('bugedit3.php?id='+id, 'Worklog_send', 'width=620,height=550,resizable,menubar,scrollbars');
	setTimeout("watch_add2(w,"+id+")",1000);
	return false;
}

function get_worklog (id) {
	$('#worklogDiv').html("Loading...");
	//alert("search_list called");
	$('#worklogDiv').load('bugworklogAjax.php', { id: id });
	return false;
}

function close_win (w) {
	clearTimeout(w);
	window.close();
}

$(function() {
	$('#form9').submit(search_list);
	if ($('#form9').length > 0) $('#lname').focus();
});

//alert(checkemail("test-foo@ron.com"));
//alert(checkphone("(719) 111-3333"));

// add_file.js
// Ron Patterson, WildDog Design

// depends on the jQuery library

var w = "";

function upload_file () {
	$('#errors').html('Uploading...');
	return true;
}

function fini_upload () {
	//opener.document.form1.update_list.value = "1";
	//alert('Uploaded');
	//opener.get_files($F('id'));
	window.setTimeout(3000,function(){window.close();});
}

function close_win (w) {
	clearTimeout(w);
	window.close();
}

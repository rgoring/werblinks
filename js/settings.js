function server_action(attrs, response, errfunc)
{
	if (!errfunc) {
		errfunc = server_err;
	}
	jQuery.ajax({url: "/", data: attrs, type: "POST", success: response, dataType: "json", error: errfunc});
}

function save_pw()
{
	$("#pwresponse").html("");
	server_action({r:"user/changepw",password:$("#password").val(),npassword:$("#npassword").val(),cnpassword:$("#cnpassword").val()}, pwresult, pwresult);
}

function pwresult(jdata, status, jqXHR)
{
	if (jdata.err) {
		$("#pwresponse").html(jdata.err);
		return;
	}
	$("#pwresponse").html(jdata.success);
	$("#password").val("");
	$("#npassword").val("");
	$("#cnpassword").val("");
}
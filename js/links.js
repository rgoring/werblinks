$(document).ready(function() {
	$('#bucket').sortable({items: '.linkobj', connectWith: '.grpobj', stop:group_linkmove, helper:'clone', opacity:0.75});
   	$('#bucket').data('dbid', 0);
   
	$("#linkdiag").dialog({autoOpen: false, buttons:{"Ok":linkdiagok}, title:"Link Details", resizable: false, draggable: false, width: 350, modal: true});
	$("#groupdiag").dialog({autoOpen: false, buttons:{"Ok":groupdiagok}, title:"Group Details", resizable: false, draggable: false, width: 300, modal: true});
	$("#wsdiag").dialog({autoOpen: false, buttons:{"Ok":workspacediagok}, title:"Workspace Details", resizable: false, draggable: false, width: 300, modal: true});
	$("#wsrenamediag").dialog({autoOpen: false, buttons:{"Ok":workspacerenamediagok}, title:"Rename Workspace", resizable: false, draggable: false, width: 300, modal: true});
	$("#groupmvdiag").dialog({autoOpen: false, buttons:{"Ok":group_wsmove_diagok}, title:"Move Group", resizable: false, draggable: false, width: 300, modal: true});
   
	//query server for workspace objects
	server_action({r:"workspace/init"}, server_parse_data);
});

function server_err(jqXHR, textStatus, errorThrown)
{
	alert("Ajax: "+textStatus+" "+errorThrown+" "+jqXHR.responseText);
}

function server_action(attrs, response)
{
	jQuery.ajax({url: "/", data: attrs, type: "POST", success: response, dataType: "json", error: server_err});
}

function server_check_err(jdata, status, jqXHR)
{
	if (jdata.err) {
		alert(jdata.err);
		window.location = "https://links.werbweb.com";
	}
	return true;
}

function server_parse_data(jdata, status, jqXHR)
{
	if (jdata.err) {
		alert(jdata.err);
		return false;
	}

	if (jdata.ws) {
		$.each(jdata.ws, function(i, ws) {
			workspace_new(ws);	   
		});
	}

	if (jdata.groups) {
		$.each(jdata.groups, function(i, grp) {
			group_new(grp);
		});
	}

	if (jdata.links) {
		$.each(jdata.links, function(i, lnk) {
			link_new(lnk);
		});
	}
	
	if (jdata.htmls) {
		$.each(jdata.htmls, function(i, hobj) {
			htmlobj_new(hobj);
		});
	}

	if (jdata.wsrename) {
		$.each(jdata.wsrename, function(i, ws) {
			workspace_rename(ws.oldname, ws.newname);
		});
	}

	$("#loadimg").hide();
}

function server_get_update(jdata, status, jqXHR)
{
	if (jdata.err) {
		alert(jdata.err);
		return false;
	}

	if (jdata.groups) {
		$.each(jdata.groups, function(i, grp) {
			var grptitle = $("#grp"+grp.groupid+" .grptitle");
			$(grptitle).html(grp.name);
		});
	}

	if (jdata.links) {
		$.each(jdata.links, function(i, lnk) {
			var url = $("#lnk"+lnk.linkid+" .linkurl");
			$(url).html(lnk.name);
			$(url).attr("href", lnk.url);
		});
	}
	
	if (jdata.htmls) {
		$.each(jdata.htmls, function(i, hobj) {
			var htitle = $("#hobj"+hobj.htmlid+" .htmlobj_title");
			var hcode = $("#hobj"+hobj.htmlid+" .htmlobj_content");
			$(htitle).html(hobj.name);
			$(hcode).html(hobj.html);
		});
	}

	if (jdata.groupmv) {
		$.each(jdata.groupmv, function(i, grp) {
			group_move(grp);
		});
	}
}

//adds workspace from json object
function workspace_new(ws)
{
	var wsmenuitem = document.createElement("li");
	var url = document.createElement("a");
	url.setAttribute("href", "/ws/"+ws.name);
	url.className = "ws_link";
	url.id = "ws_"+ws.name;
	url.innerHTML = ws.name;
	wsmenuitem.appendChild(url);

	//base toolbar element
	var toolbar = document.createElement("span");
	toolbar.className = "toolbar ws_toolbar";
	toolbar.style.float = "right";
	//edit icon
	var editico = document.createElement("a");
	editico.id = "ws_editico";
	editico.className = "ws_edit"
	editico.setAttribute("href", "javascript:void(0)");
	editico.innerHTML = '<img src="/images/edit_ico.png" border="0" height="18px" title="Rename Workspace">';
	$(editico).click(workspace_rename_evt);
	toolbar.appendChild(editico);

	wsmenuitem.appendChild(toolbar);

	$("#wsmenu").append(wsmenuitem);
}

function workspace_add()
{
	$("#wsdiag").find("#wsname").val("");
	$("#wsdiag").dialog("open");
}

function workspace_rename(oldname, newname)
{
	$("#ws_"+oldname).id = "ws_"+newname;
	$("#ws_"+oldname).attr("href", "/ws/"+newname);
	$("#ws_"+oldname).text(newname);
}

function workspace_rename_evt(evtobj)
{
	var oldname = $(this).closest("li").find("a").text();
	$("#wsrenamediag").find("#wsnewname").val(oldname);
	$("#wsrenamediag").find("#wsoldname").val(oldname);
	$("#wsrenamediag").dialog("open");
}

function workspacediagok()
{
	var name = $(this).find("#wsname").val();
	//alert("adding workspace "+name);
	server_action({r:"workspace/add",name:name}, server_parse_data);
	$(this).dialog("close");
}

function workspacerenamediagok()
{
	var newname = $(this).find("#wsnewname").val();
	var oldname = $(this).find("#wsoldname").val();;
	server_action({r:"workspace/rename",oldname:oldname,newname:newname}, server_parse_data);
	$(this).dialog("close");
}

function group_add(grp)
{
	var gname = null;
	var titlebox = null;
	if (grp) {
		titlebox = $(grp).find('.grptitle').first();
		gname = titlebox.text();;
	}

	$("#groupdiag").find("#groupname").val(gname);
	$("#groupdiag").data("group", grp);
	$("#groupdiag").dialog("open");
}

function groupdiagok()
{
	var name = $(this).find("#groupname").val();
	var grp = $(this).data("group");
	
	if (name == "") {
		alert("Name cannot be blank");
		return;
	}

	if (grp) {
		//var titlebox = $(grp).find('.grptitle').first();
		//titlebox.text(name);
		//alert("Updating group "+name+" id="+$(grp).data("dbid"));
		server_action({r:"group/update",name:name, id:$(grp).data("dbid")}, server_get_update);
	} else {
		//alert("Adding new group "+name);
		server_action({r:"group/add",name:name}, server_parse_data);
	}

	$(this).dialog("close");
}

function group_startwsmove(grp)
{
	$("#groupmvdiag").data("group", grp);

	// clear out the select options before repopulating it
	$('#groupwssel').find('option').remove().end()
	
	$("#wsmenu > li > a").each(function() {
		if ($(this).text() != "New Workspace") {
			$('#groupwssel').append($("<option></option>")
							.attr("value", $(this).text())
							.text($(this).text()));
		}
	});

	$("#groupmvdiag").dialog("open");
}

function group_wsmove_diagok()
{
	var grp = $(this).data("group");
	var wsname = $(this).find("#groupwssel").val();
	server_action({r:"group/movews",wsname:wsname,id:$(grp).data("dbid")}, server_get_update);
	$(this).dialog("close");
}


function obj_hover(eventObject)
{
	$(this).find(".toolbar").show();
}

function obj_out(eventObject)
{
	$(this).find(".toolbar").hide();
}

function objexpand_out(eventObject)
{
	$(this).find(".toolbar").hide();
	$(this).find(".toolbar > #more").show();
	$(this).find(".toolbar > a").not("#more").not("#launch").hide();
}

function toolbar_more(evtobj)
{
	$(this).siblings("a").css("display", "inline");
	$(this).hide();
}

function group_edit(evtobj)
{
	group_add($(this).closest('.grpobj'));
}

function group_addlink(evtobj)
{
	link_add(null, $(this).closest('.grpobj'));
}

function group_stkytog(evtobj)
{
	var grp = $(this).closest('.grpobj');
	server_action({r:"group/sticky",id:grp.data("dbid")}, server_check_err);
}

function group_wsmove(evtobj)
{
	group_startwsmove($(this).closest('.grpobj'));
}

function group_launch(evtobj)
{
	$(this).closest(".grpobj").find(".linkurl").each(function(index) {
		window.open($(this).attr("href"), "_blank");
	});
}

function group_del(evtobj)
{
	var grp = $(this).closest('.grpobj');
	var cont = confirm("Delete Group?");
	if (cont == false) {
		return;
	}
	server_action({r:"group/del",id:grp.data("dbid")}, server_check_err);
	grp.remove();
}

function group_move(grpdata)
{
	if (grpdata.nomove == 1) {
		return;
	}
	$("#grp"+grpdata.id).remove();
}

function group_get_toolbar()
{
	//base element
	var toolbar = document.createElement("span");
	toolbar.className = "toolbar grp_toolbar";
	toolbar.style.zIndex = 15;

	//delete icon
	var delico = document.createElement("a");
	delico.id = "delico";
	delico.setAttribute("href", "javascript:void(0)");
	delico.innerHTML = '<img src="/images/del_ico.png" border="0" title="Delete Group">';
	$(delico).click(group_del);
	$(delico).hide();
	toolbar.appendChild(delico);
	
	//sticky icon
	var stickyico = document.createElement("a");
	stickyico.id = "sticky";
	stickyico.setAttribute("href", "javascript:void(0)");
	stickyico.innerHTML = '<img src="/images/sticky_ico.png" border="0" title="Toggle Sticky">';
	$(stickyico).click(group_stkytog);
	$(stickyico).hide();
	toolbar.appendChild(stickyico);

	//workspace move icon
	var wsmoveico = document.createElement("a");
	wsmoveico.id = "wsmove";
	wsmoveico.setAttribute("href", "javascript:void(0)");
	wsmoveico.innerHTML = '<img src="/images/transfer_ico.png" border="0" title="Move Workspace">';
	$(wsmoveico).click(group_wsmove);
	$(wsmoveico).hide();
	toolbar.appendChild(wsmoveico);

	//edit icon
	var editico = document.createElement("a");
	editico.id = "edit";
	editico.setAttribute("href", "javascript:void(0)");
	editico.innerHTML = '<img src="/images/edit_ico.png" border="0" title="Edit Group">';
	$(editico).click(group_edit);
	$(editico).hide();
	toolbar.appendChild(editico);
	
	//new link icon
	var lnkico = document.createElement("a");
	lnkico.id = "link";
	lnkico.setAttribute("href", "javascript:void(0)");
	lnkico.innerHTML = '<img src="/images/link_ico.png" border="0" title="Add Link">';
	$(lnkico).click(group_addlink);
	$(lnkico).hide();
	toolbar.appendChild(lnkico);
	
	//more icon
	var moreico = document.createElement("a");
	moreico.style.display = "inline";
	moreico.id = "more";
	moreico.setAttribute("href", "javascript:void(0)");
	moreico.innerHTML = '<img src="/images/more_ico.png" border="0" title="More">';
	$(moreico).click(toolbar_more);
	toolbar.appendChild(moreico);
	
	//launch
	var launchico = document.createElement("a");
	launchico.id = "launch";
	launchico.style.display = "inline";
	launchico.setAttribute("href", "javascript:void(0)");
	launchico.innerHTML = '<img src="/images/launch_ico.png" border="0" title="Launch Group">';
	$(launchico).click(group_launch);
	toolbar.appendChild(launchico);

	return toolbar;
}

//adds group from json object
function group_new(grp)
{
	var grpbox = document.createElement("div");
	grpbox.style.position = "absolute";
	grpbox.style.top = grp.ypos+"px";
	grpbox.style.left = grp.xpos+"px";
	grpbox.style.width = grp.width+"px";
	grpbox.style.height = "auto";//TODO: grp.height+"px";
	grpbox.className = "grpobj";
	$(grpbox).data("dbid", grp.groupid);
	grpbox.id = "grp"+grp.groupid;

	var grphandle = document.createElement("div");
	grphandle.className = ".grphandle";
	grpbox.appendChild(grphandle);

	var titlebox = document.createElement("div");
	titlebox.innerHTML = grp.name;
	titlebox.className = "grptitle";
	titlebox.style.zIndex = 10;
	grphandle.appendChild(titlebox);

	grphandle.appendChild(group_get_toolbar());

	$("body").append(grpbox);

	$(grpbox).draggable({opacity: 0.33, handle: ".grptitle", stop:group_dragstop});
	$(grpbox).resizable({handles:'e', stop:group_resize, maxwidth:1024});
	$(grpbox).sortable({items: '.linkobj', connectWith: '.grpobj', stop:group_linkmove, opacity:0.80, distance:5});
	$(grphandle).hover(obj_hover, objexpand_out);
}

function group_dragstop(event, ui)
{
	server_action({r:"group/move", x:ui.offset.left, y:ui.offset.top, id:$(this).data("dbid")}, server_check_err);
}

function group_resize(event, ui)
{
	$(this).height("auto");
	server_action({r:"group/resize", w:ui.size.width, h:ui.size.height, id:$(this).data("dbid")}, server_check_err);
}

function group_linkmove(event, ui)
{
	var parent = ui.item.parent();
	var parid = parent.data("dbid");
	var pos = ui.item.index();
	if (parid != 0) {
		//correct for title & toolbar, but bucket doesn't have those
		pos = pos - 2;
	}
	parent.css("height", "auto");
	//alert("id="+ui.item.data("dbid")+" offset="+ui.item.index()+", parent="+parid);

	server_action({r:"link/move", id:ui.item.data("dbid"), pos:pos, grp:parid}, server_check_err);
}

function link_add(lnk, grp)
{
	var lname = null;
	var lurl = null;
	var urlbox = null;
	if (lnk) {
		urlbox = $(lnk).children(".linkurl").first();
		lname = urlbox.html();
		lurl = urlbox.attr("href");
	}

	$("#linkdiag").find("#linkname").val(lname);
	$("#linkdiag").find("#linkurl").val(lurl);
	$("#linkdiag").dialog("open");
	$("#linkdiag").data("link", lnk);
	$("#linkdiag").data("group", grp);
}

function linkdiagok()
{
	var lnk = $(this).data("link");
	var grp = $(this).data("group");
	
	var name = $(this).find("#linkname").val();
	var url = $(this).find("#linkurl").val();
	
	if (url == "") {
		alert("Url cannot be blank");
		return;
	}

	if (name == "") {
		name = url;
	}
	
	if (lnk) {
		//var urlbox = $(lnk).children(".linkurl").first();
		//urlbox.html(name);
		//urlbox.attr("href", url);
 		//alert("editing link " + $(lnk).data("dbid"));
		server_action({r:"link/update",id:$(lnk).data("dbid"), name:name,url:url}, server_get_update);
	} else {
		if (grp) {
 			//alert("adding link to group " + $(grp).data("dbid"));
			server_action({r:"link/add",name:name,url:url,gid:$(grp).data("dbid")}, server_parse_data);	
		} else {
	 		//alert("adding new link");
			server_action({r:"link/add",name:name,url:url}, server_parse_data);	
		}
	}
	
	$(this).dialog("close");
}

function link_get_toolbar()
{
	//base element
	var toolbar = document.createElement("span");
	toolbar.className = "toolbar lnk_toolbar";

	//delete icon
	var delico = document.createElement("a");
	delico.id = "delico";
	delico.setAttribute("href", "javascript:void(0)");
	delico.innerHTML = '<img src="/images/del_ico.png" border="0" height="16px" title="Delete Link">';
	$(delico).click(link_del);
	$(delico).hide();
	toolbar.appendChild(delico);

	//bucket
	var bucketico = document.createElement("a");
	bucketico.id = "bucketico";
	bucketico.setAttribute("href", "javascript:void(0)");
	bucketico.innerHTML = '<img src="/images/bucket_ico.png" border="0" height="16px" title="Move To Bucket">';
	$(bucketico).click(link_to_bucket);
	$(bucketico).hide();
	toolbar.appendChild(bucketico);

	//edit icon
	var editico = document.createElement("a");
	editico.id = "edit";
	editico.setAttribute("href", "javascript:void(0)");
	editico.innerHTML = '<img src="/images/edit_ico.png" border="0" height="16px" title="Edit Link">';
	$(editico).click(link_edit);
	$(editico).hide();
	toolbar.appendChild(editico);

	//more icon
	var moreico = document.createElement("a");
	moreico.style.display = "inline";
	moreico.id = "more";
	moreico.setAttribute("href", "javascript:void(0)");
	moreico.innerHTML = '<img src="/images/more_ico.png" border="0" height="16px" title = "More">';
	$(moreico).click(toolbar_more);
	toolbar.appendChild(moreico);
	
	return toolbar;
}

function link_edit(evtobj)
{
	link_add($(this).closest('.linkobj'), null);
}

function link_to_bucket(evtobj)
{
	$(this).closest('.toolbar').hide();
	$('#bucket').append($(this).closest('.linkobj'));
	server_action({r:"link/move_to_bucket",id:$(this).closest('.linkobj').data("dbid")}, server_check_err);
}

function link_del(evtobj)
{
	var cont = confirm("Delete link?");
	if (cont == false) {
		return;
	}
	server_action({r:"link/del", id:$(this).closest('.linkobj').data("dbid")}, server_check_err);
	$(this).closest('.linkobj').remove();
}

//adds link from json object
function link_new(lnk)
{
	var linkbox = document.createElement("div");
	linkbox.className = "linkobj";
	linkbox.id = "lnk"+lnk.linkid;
	$(linkbox).data("dbid", lnk.linkid);

	//url
	var urlbox = document.createElement("a");
	urlbox.setAttribute("href", lnk.url);
	urlbox.setAttribute("target", "_blank");
	urlbox.innerHTML = lnk.name;
	urlbox.className = "linkurl";
	linkbox.appendChild(urlbox);
	
	linkbox.appendChild(link_get_toolbar());
	$(linkbox).hover(obj_hover, objexpand_out);

	//add to its group
	if (lnk.workspaceid == 0 && lnk.groupid == 0) {
		$("#bucket").append(linkbox);
	} else {
		$("#grp"+lnk.groupid).append(linkbox);
	}
}

function htmlobj_new(hobj)
{
	var htmlbox = document.createElement("div");
	htmlbox.className = "htmlobj";
	htmlbox.style.position = "absolute";
	htmlbox.style.top = hobj.ypos+"px";
	htmlbox.style.left = hobj.xpos+"px";
	htmlbox.style.width = hobj.width+"px";
	htmlbox.style.height = hobj.height+"px";
	htmlbox.id = "hobj"+hobj.htmlid;
	$(htmlbox).data("dbid", hobj.htmlid);
	
	var htmlcontent = document.createElement("div");
	htmlcontent.className = "htmlobj_content";
//	if (hobj.script == 1) {
		//var  junk = $.getScript(hobj.html, function(data, status, jxh) {
		//	console.log("\"" + data + "\"");
		//	console.log(status);
		//});
//		var ifrm = document.createElement("iframe");
//		ifrm.setAttribute("src", hobj.html);
//		ifrm.style.width = "100%";
//		ifrm.style.height = "100%";
//		htmlcontent.appendChild(ifrm);
//	} else {
		htmlcontent.innerHTML = hobj.html;
//	}
	htmlbox.appendChild(htmlcontent);
	
	var titlebox = document.createElement("div");
	titlebox.innerHTML = hobj.name;
	titlebox.className = "htmlobj_title";
	htmlbox.appendChild(titlebox);
	
	htmlbox.appendChild(hobj_get_toolbar());
	
	$("body").append(htmlbox);

	$(htmlbox).draggable({opacity: 0.33, handle: ".htmlobj_title", stop:hobj_dragstop});
	$(htmlbox).resizable({stop:hobj_resize, maxwidth:1024});
	$(htmlbox).hover(obj_hover, obj_out);
}

function hobj_dragstop(event, ui)
{
	server_action({r:"hobject/move", x:ui.offset.left, y:ui.offset.top, id:$(this).data("dbid")}, server_check_err);
}

function hobj_resize(event, ui)
{
	server_action({r:"hobject/resize", w:ui.size.width, h:ui.size.height, id:$(this).data("dbid")}, server_check_err);
}

function hobj_get_toolbar()
{
	//base element
	var toolbar = document.createElement("span");
	toolbar.className = "toolbar hobj_toolbar";
	
	//delete icon
	var delico = document.createElement("a");
	delico.id = "delico";
	delico.setAttribute("href", "javascript:void(0)");
	delico.innerHTML = '<img src="/images/del_ico.png" border="0" height="18px" title="Delete Object">';
	$(delico).click(hobj_del);
	toolbar.appendChild(delico);
	
	//sticky icon
	var stickyico = document.createElement("a");
	stickyico.id = "sticky";
	stickyico.setAttribute("href", "javascript:void(0)");
	stickyico.innerHTML = '<img src="/images/sticky_ico.png" border="0" height="18px" title="Toggle Sticky">';
	$(stickyico).click(hobj_stkytog);
	toolbar.appendChild(stickyico);

	//edit icon
	var editico = document.createElement("a");
	editico.id = "edit";
	editico.setAttribute("href", "javascript:void(0)");
	editico.innerHTML = '<img src="/images/edit_ico.png" border="0" height="18px" title="Edit Object">';
	$(editico).click(hobj_edit);
	toolbar.appendChild(editico);
	
	return toolbar;
}

function get_object(name, file)
{
	jQuery.get(file, function(data) {
		$("#hobjname").val(name);
		$("#hobjcode").val(data);
	});
}

function hobj_edit(evtobj)
{
	hobj_add($(this).closest('.htmlobj'));
}

function hobj_del(evtobj)
{
	var cont = confirm("Delete object?");
	if (cont == false) {
		return;
	}
	server_action({r:"hobject/del", id:$(this).closest('.htmlobj').data("dbid")}, server_check_err);
	$(this).closest('.htmlobj').remove();
}

function hobj_stkytog(evtobj)
{
	var hobj = $(this).closest('.htmlobj');
	server_action({r:"hobject/sticky",id:hobj.data("dbid")}, server_check_err);
}

function hobj_add(obj)
{
	var objname = null;
	var objcode = null;
	if (obj) {
		objname = $(obj).children(".htmlobj_title").first().text();
		objcode = $(obj).children(".htmlobj_content").first().html();
	}

	$("#hobjdiag").find("#hobjname").val(objname);
	$("#hobjdiag").find("#hobjcode").val(objcode);
	$("#hobjdiag").data("hobj", obj);
	$("#hobjdiag").dialog("open");
}

function hobjdiagok()
{
	var name = $(this).find("#hobjname").val();
	var code = $(this).find("#hobjcode").val();

	if (name == "" || code == "") {
		alert("Name and code segment cannot be blank");
		return;
	}

	var obj = $(this).data("hobj");
	
	if (obj) {
		//$(obj).children(".htmlobj_title").first().text(name);
		//$(obj).children(".htmlobj_content").first().html(code);
		//alert("editing object "+$(obj).data("dbid")+" name="+name+" code=\n"+code);
		server_action({r:"hobject/update",id:$(obj).data("dbid"), name:name,code:code}, server_get_update);
	} else {
		//alert("adding new object; name="+name+" code=\n"+code);
		server_action({r:"hobject/add",name:name,code:code}, server_parse_data);	
	}
	
	$(this).dialog("close");
}
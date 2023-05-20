<body class="workspacebody">
<div class="title">
	<div style="float:left;">
	<ul class="menu">
	<li><a href="javascript:void(0)">Links</a>
        <ul>
            <li><a href="javascript:void(0);" onClick="link_add(null, null);">New Link</a></li>
            <li><a href="javascript:void(0);" onClick="group_add(null);">New Group</a></li>
            <li><a href="javascript:void(0);" onClick="hobj_add(null);">New HTML</a></li>
        </ul>
    </li>
	<li><a href="javascript:void(0)">Workspace</a>
        <ul id="wsmenu">
        	<li style="border-bottom:1px solid #444;"><a href="javascript:void(0);" onClick="workspace_add();">New Workspace</a></li>
        </ul>
	</li>
    <li><a href="javascript:void(0)">Link Bucket</a>
    	<ul>
        <div id="bucket" class="bucket">
        </div>
        </ul>
    </li>
    <li><a href="javascript:void(0)">Account</a>
        <ul>
            <li><a href="/?r=user/logout">Sign Out</a></li>
            <li><a href="/?r=home/help">Help</a></li>
            <li><a href="/?r=user/settings">Settings</a></li>
        </ul>
	</li>
	</ul>
    </div>
    <div class="util">
    <a href="javascript:void(function(){var v='1.8.3';if(window.jQuery===undefined||window.jQuery.fn.jquery<v){var done=false;var script=document.createElement('script');script.src='https://ajax.googleapis.com/ajax/libs/jquery/'+v+'/jquery.min.js';script.onload=script.onreadystatechange=function(){if(!done&&(!this.readyState||this.readyState=='loaded'||this.readyState=='complete')){done=true;add_link();}};document.getElementsByTagName('head')[0].appendChild(script);}else{add_link();}function add_link(){(window.myBookmarklet=function(){var name=encodeURIComponent(document.title||'');var url=encodeURIComponent(window.location.href);jQuery.ajax({url:'https://links.werbweb.com',data:{r:'link/bookmark',name:name,url:url},async:false,type:'GET',success:chk_err,dataType:'jsonp'});})();}function chk_err(jdata,status,jqXHR){if(jdata.err){window.alert('Error adding link: '+jdata.err);}else{window.alert('Link added');}return true;}})();">Werblink</a>
    </div>
</div>
<div style="clear:both;" />

<div id="loadimg" style="width:100%;" align="center">
<img src="/images/loading.gif">
</div>

<div id="linkdiag" align="center">
<div style="width:60px; text-align:right;float:left;">Name:</div><div style="float:left;"><input id="linkname" type="text" size="20"></div><div style="clear:both;" />
<div style="width:60px; text-align:right;float:left;">Url:</div><div style="float:left;"><input id="linkurl" type="text" size="30"></div><div style="clear:both;" />
</div>

<div id="groupdiag" align="center">
<div style="width:60px; text-align:right;float:left;">Name:</div><div style="float:left;"><input id="groupname" type="text" size="20"></div><div style="clear:both;" />
</div>

<div id="groupmvdiag" align="center">
<div style="width:80px; text-align:right;float:left;">Workspace:</div><div style="float:left;"><select name="wsname" id="groupwssel"></select></div><div style="clear:both;" />
</div>

<div id="wsdiag" align="center">
<div style="width:60px; text-align:right;float:left;">Name:</div><div style="float:left;"><input id="wsname" type="text" size="20"></div><div style="clear:both;" />
</div>

<div id="wsrenamediag" align="center">
<div style="width:60px; text-align:right;float:left;">Name:</div><div style="float:left;"><input id="wsnewname" type="text" size="20"></div><input id="wsoldname" type="hidden"></div><div style="clear:both;" />
</div>

<div id="hobjdiag" align="center">
<div style="width:60px; text-align:right;float:left;">Name:</div><div style="float:left;"><input id="hobjname" type="text" size="20"></div><div style="clear:both;" />
<div style="width:60px; text-align:right;float:left;">Code:</div><div style="float:left;"><textarea id="hobjcode" cols="45" rows="10"></textarea></div><div style="clear:both;" />
<br>
<div style="width:100%; text-align:left;">Premade: <a href="javascript:void(0);" onClick="get_object('Google', '/widgets/google.txt');">Google</a></div>
</div>

</body>
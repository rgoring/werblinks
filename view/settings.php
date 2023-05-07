<body>
<div class="title">
Account Settings For <? echo $email; ?>
</div>
<div class="main" align="center">
	<div class="content" align="left">
        <div class="subtitle">Username</div>
    	<div class="setting_lcol">email:</div><div class="setting_rcol"><? echo $email; ?></div><div class="clear"></div>
        <div class="subtitle" style="margin-top:10px;">Password</div>
        <div class="setting_lcol">&nbsp;</div><div class="setting_rcol errmsg" id="pwresponse"></div><div class="clear"></div>
		<div class="setting_lcol">current password:</div><div class="setting_rcol"><input type="password" name="password" id="password" class="form_input"/></div><div class="clear"></div>
		<div class="setting_lcol">new password:</div><div class="setting_rcol"><input type="password" name="npassword" id="npassword" class="form_input"/></div><div class="clear"></div>
		<div class="setting_lcol">confirm new password:</div><div class="setting_rcol"><input type="password" name="cnpassword" id="cnpassword" class="form_input"/></div><div class="clear"></div>
        <br>
        <div class="setting_lcol">&nbsp;</div><div class="setting_rcol"><a class="button" href="javascript:void(0);" onClick="save_pw();">Change Password</a></div><div class="clear"></div>
        <br>
        <div class="subtitle">Stats</div>
        <div class="setting_lcol">Links:</div><div class="setting_rcol"><? echo $numlinks; ?></div><div class="clear"></div>
        <div class="setting_lcol">Groups:</div><div class="setting_rcol"><? echo $numgroups; ?></div><div class="clear"></div>
        <div class="setting_lcol">Workspaces:</div><div class="setting_rcol"><? echo $numworkspaces; ?></div><div class="clear"></div>
        <div class="setting_lcol">HTML Objects:</div><div class="setting_rcol"><? echo $numhobjs; ?></div><div class="clear"></div>
        <br><br>
        <div align="right" style="width:100%;">
        <a class="button" href="javascript:void(0);" onClick="parent.location='/';" style="margin:10px;">Return to Workspace</a>
        </div>
        <br>
    </div>
</div>
<div class="footer">
    Copyright &copy; <? echo date("Y"); ?> Werbweb.com
</div><br />
</body>

<body>
<div class="title">
Links <span style="font-size:15px; font-style:italic; font-weight:normal;">[few pics...pretty colors]</span>
</div>
<div class="main">
    <div class="leftwrap">
        <div class="leftpane">
			<div class="content">
            	<div class="subtitle">Features</div>
            	<div>Use Links as your start page to have all your favorite websites available with you wherever you go.</div><br>
                <div class="column"><div class="colcontent"><div align="center"><img src="/images/sync.png"></div><br>No more syncing bookmarks between computers and devices, or even between browsers.</div></div>
                <div class="column"><div class="colcontent"><div align="center"><img src="/images/group.png"></div><br>Groups and workspaces help you organize your links making them easy to find. Create workspaces for home and work!</div></div>
                <div class="column"><div class="colcontent"><div align="center"><img src="/images/launch.png"></div><br>The Group Launch feature allows you to open multiple sites at once making it convenient to get the latest updates from multiple sites.</div></div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="rightpane" align="center">
        <div align="left" class="signinbox">
            <div align="center" class="subtitle">
            Sign In
            </div>
            <div align="center" class="signin_body">
                <div align="left" style="width:80%;">
                <br />
            	<? if (isset($loginerr)) { ?>
					<div class="errmsg" align="center"><? echo $loginerr; ?></div>
				<? } ?>
                <form id="signin_form" name="signin_form" method="post" action="/">
                    <div align="left">
                    <strong>Username</strong><br />
                    <input type="text" name="username" id="username" class="form_input"/>
                    </div>
                    <br />
                    <div align="left">
                    <strong>Password</strong><br />
                    <input type="password" name="password" id="password" class="form_input"/>
                    </div>
                    <br />
                    <div align="right">
                      <label>
                        <input type="checkbox" name="remember" id="remember" value="true"/>
                        <span style="font-size:12px; margin-right:10px;">Remember Me</span></label>
                    <input name="r" type="hidden" id="loginrt" value="user/login" />
                    <? if (isset($_GET['ws'])) { ?>
                    <input name="ws" type="hidden" id="loginws" value="<? echo $_GET['ws']; ?>" />
					<? } ?>
                    <input type="submit" name="login_button" id="login_button" value="Sign In" />
                    </div>
                </form>
                <br />
                </div>
            </div>
        </div>

        <div class="regbox">
            <div align="center" class="subtitle">
            New? Create an Account
            </div>
            <div align="center" class="reg_body" id="reg_body">
                <div align="left" style="width:80%;">
                <br />
            	<? if (isset($regerr)) { ?>
					<div class="errmsg" align="center"><? echo $regerr; ?></div>
				<? } ?>
                <form id="regform" name="regform" method="post" action="/">
                    <div align="left">
                    <strong>Email</strong><br />
                    <input type="text" name="reg_email" id="reg_email" class="form_input"/>
                    </div>
                    <br />
                    <div align="left">
                    <strong>Password</strong><br />
                    <input type="password" name="reg_pass" id="reg_pass" class="form_input"/>
                    <br />
                    <strong>Confirm Password</strong><br />
                    <input type="password" name="reg_pass_confirm" id="reg_pass_confirm" class="form_input"/>
                    </div>
                    <br />
                    <div align="right">
                    <input name="r" type="hidden" id="regrt" value="user/register" />
                    <input type="submit" name="reg_button" id="reg_button" value="Register" />
                    </div>
                </form>
                <br />
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="footer">
    Copyright &copy; <? echo date("Y"); ?> Werbweb.com
    </div><br />
</body>

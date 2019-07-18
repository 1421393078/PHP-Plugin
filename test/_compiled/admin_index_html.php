<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang='zh-tw'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php
echo $_obj['SITE_TITLE'];
?>
::網站管理系統 - </title>
<style>

body {
	width: 350px;
	margin: 150px auto;
	background: #EFEFEF;
	font-size: small;
}
img {
	border: none;
}
#loginBox {
	width: 330px;
	height: 160px;
	padding: 8px 9px;
	border-top: solid 1px #D3D3D3;
	border-right: solid 1px #FFFFFF;
	border-bottom: solid 1px #FFFFFF;
	border-left: solid 1px #D3D3D3;
	background: #F5F5F5 url( '../images/admin/login_bg.gif' ) no-repeat;
}
#logo {
	float: left;
	width: 123px;
	margin: 0px 0px 20px 0px;
	padding: 0px;
}
#version {
	float: left;
	width: 207px;
	height: 40px;
	margin: 0px;
	padding: 0px;
	text-align: right;
}
#formBox {
	clear: both;
	padding: 10px 0px 0px 0px;
	text-align: center;
}
form {
	width: 250px;
	margin: 0px auto;
}
dl {
	margin: 0px;
	padding: 0px 0px 0px 0px;
}
dt {
	clear: both;
	float: left;
	width: 80px;
	margin: 0px 5px 0px 0px;
	padding: 0px;
	text-align: right;
}
dd {
	float:left;
	margin: 0px;
	padding: 0px;
	text-align: left;
}
#submit {
	margin: 4px;
}

</style>
<script type="text/javascript">

function check_login()
{
	if ( document.form1.account.value.replace( /\s+/, "" ) == '' || document.form1.account.value == '請輸入帳號') { alert('請輸入帳號');document.form1.account.focus(); return false; }
	if ( document.form1.password.value.replace( /\s+/, "" ) == '' || document.form1.password.value == '請輸入密碼') { alert('請輸入密碼');document.form1.password.focus(); return false; }
	return true;
}

</script><noscript>您的瀏覽器不支援JavaScript語法,建議您使用 IE 6.0 以上瀏覽器</noscript>
</head>

<body onload="document.form1.account.focus();">
<div id="loginBox">
	<h1 id="logo"><img src="../images/admin/logo.gif" border="0"></h1>
	<p id="version"></p>
	<div id="formBox">
		<form name="form1" method="post" action="index.php?fn=login" onsubmit="return check_login()">
			<dl>
				<dt>帳號:</dt>
				<dd><input name="account" type="text" id="account" size="18"></dd>
				<dt>密碼:</dt>
				<dd><input name="password" type="password" id="password" size="12"><br><input id="submit" type="submit" value="登入系統"></dd>
			</dl>
		</form>
	</div>
</div>
</body>
</html>
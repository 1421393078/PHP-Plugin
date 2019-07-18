<?php
error_reporting( E_ALL ^ E_NOTICE );

ini_set( "session.cache_expire", "120" );			// 設定 Session 存續時間 (分鐘)
ini_set( "session.gc_maxlifetime", "7200" );	// 設定 Session 存續時間 (秒)
session_start();
if ( isset( $_COOKIE["PHPSESSID"] ) ) {
	setcookie( "PHPSESSID", $_COOKIE["PHPSESSID"], time()+7200 );
}

$ADMIN = true;
require('../lib/config.inc.php');
require('../lib/const.inc.php');
require('../lib/db.inc.php');
require('../lib/function.inc.php');
require('../lib/tran.inc.php');

$function_exists = false;
$MODE = "";
$SUB = "";

$rs = new ResultSet();
$rs2 = new ResultSet();
$rs3 = new ResultSet();

if (!isset($_SESSION['WMSHide']))
	$_SESSION['WMSHide'] = false;
// Function Settings
if (!isset($_GET['fn']))
{
	if (isset($_SESSION['admin']))
		GoTo('fn=main');
	$FUNC = 'index';
	$tpl_file = "admin/index.html";
	$inc_file = 'admin/index.inc.php';
}
else
{
	// WMSPlate switch
	$FUNC = $_GET['fn'];
	switch ($FUNC)
	{
	case 'blog':
		header( "Location: ../wordpress/wp-login.php" );  exit;
		break;
	case 'calendar':
		header( "Location: ../php-calendar/admin.php?action=login" );  exit;
		break;
	case 'back':
		GoBack();
		break;
	case 'wmsplate': // WMSPlate switch
		$_SESSION['WMSHide'] = !$_SESSION['WMSHide'];
		echo '<script language="javascript">parent.nothing();</script>';
	case 'blank':
		exit;
	}
	$inc_file = "admin/$FUNC.inc.php";
	if (isset($_GET['mo']))
	{
		$MODE = $_GET['mo'];
		$tpl_file = "admin/{$FUNC}_$MODE.html";
		if (!file_exists($TEMP_PATH . $tpl_file))
			$tpl_file = "admin/$FUNC.html";
		if (isset($_GET['sub']))
			$SUB = $_GET['sub'];
	}
	else
	{
		unset($_SESSION['apostdata']);
		$MODE = '';
		$tpl_file = "admin/$FUNC.html";
	}
}
if (file_exists($TEMP_PATH . $tpl_file))
	$page = new SmartTemplate($tpl_file);
if (file_exists($WEB_PATH . $inc_file))
	$function_exists = true;
elseif (!isset($page))
	$page = new SmartTemplate('not_found.html');

//$CUR_URL = $_SERVER['REQUEST_URI'];
$CUR_URL = 'index.php?fn=' . $FUNC;
$CUR_PAGE = $_SERVER['QUERY_STRING'];
$MODE_LIST = array('new', 'add', 'edit', 'modify', 'update', 'delete', 'view');
$uu = explode('&', $CUR_URL);
for ($i = 1, $k = count($uu), $m = 0; $i < count($uu); $i++)
{
	if (substr($uu[$i], 0, 2) == 'mo')
		$k = $i;
	else if (substr($uu[$i], 0, 2) == 'id')
		$m = $i;
}
$uu[$k] = '';
if ($m != 0)
	unset($uu[$m]);
foreach ($MODE_LIST as $v)
{
	$n = strtoupper($v) . '_URL';
	$uu[$k] = 'mo=' . $v;
	$VARS[$n] = implode('&', $uu);
}
$VARS['CUR_URL'] = $CUR_URL;
$VARS['MAIN_URL'] = $MAIN_URL = 'index.php?fn=' . $FUNC;

// Main
if (!in_array($FUNC, array('index', 'login', 'go')))
	require($WEB_PATH . 'admin/auth.inc.php');
if ($function_exists)
{
	if (file_exists($WEB_PATH . 'admin/global.inc.php'))
		require($WEB_PATH . 'admin/global.inc.php');
	unset($uu, $i, $k, $m, $n, $v);
	require($WEB_PATH . $inc_file);
}
require($WEB_PATH . 'admin/post.inc.php');

if (isset($page))
{
	$VARS['FUNC'] = $FUNC;
	$VARS['MODE'] = $MODE;
	$VARS['SUB'] = $SUB;
	$VARS['WMSdisplay'] = $_SESSION['WMSHide'] ? 'none' : 'normal';
	$VARS['WMSdisplay2'] = $_SESSION['WMSHide'] ? 'normal' : 'none';
	// Variables
	if (isset($VARS))
		foreach($VARS as $k => $v)
			$page->assign($k, $v);
	// Others
	$page->assign('SITE_TITLE', $SITE_TITLE);
	$page->reuse_code = false;
	$page->output();
	//$page->debug();
}
// Page Stack
if ($MODE == '')
	unset($_SESSION['alpage']);
if ( isset( $_SESSION['alpage'] ) && is_array($_SESSION['alpage']) && $_SESSION['alpage'][count($_SESSION['alpage']) - 1] != $CUR_PAGE)
	$_SESSION['alpage'][] = $CUR_PAGE;

if (isset($_SESSION['amsg']))
{
	echo "<script language=\"javascript\">\n  alert('{$_SESSION['amsg']}');\n";
	echo "</script>\n";
	unset($_SESSION['amsg']);
}

if ( isset( $SCRIPTS ) && is_array($SCRIPTS))
{
	$s = implode("\n", $SCRIPTS);
	echo <<<EOT
<script language="javascript">
$s
</script>
EOT;
}
//echo '<pre>';
//print_r($_SESSION['alpage']);
?>

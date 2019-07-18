<?php
// 去除斜線問題 \\ No Global
//foreach ($_POST as $k => $v)
//{
//	if (is_array($v))
//	{
//		foreach ($v as $kk => $vv)
//		{
//			if (substr($vv, -1) == '\\')
//			{
//				$vv = substr($vv, 0, -1);
//				$v[$kk] = $vv;
//				$trans = true;
//			}
//		}
//		if (isset($trans))
//		{
//			$_POST[$k] = $v;
////			$$k = $v;
//			unset($trans);
//		}
//	}
//	elseif (substr($v, -1) == '\\')
//	{
//		$v = substr($v, 0, -1);
//		$_POST[$k] = $v;
////		$$k = $v;
//	}
//}
//foreach ($_GET as $k => $v)
//{
//	if (substr($v, -1) == '\\')
//	{
//		$v = substr($v, 0, -1);
//		$_GET[$k] = $v;
////		$$k = $v;
//	}
//}


// 強迫建立目錄if目錄不存在
function MakeDir($path)
{
 	while (substr($path, -1) == '/')
		$path = substr($path, 0, -1);
	if(file_exists($path))
		return;
	MakeDir(dirname($path));
	mkdir($path, 0777);
}

// 將'改成\'讓SQL語法可以運作
function SQLString($str)
{
	return str_replace("'", "\\'", $str);
}

// 將return(\n)改成文字\n, (\r)刪去, 常用於JavaScript
function RemoveReturn($str)
{
	return str_replace("\n", "\\n", str_replace("\r", '', $str));
}

// 將\改成\\後轉為JavaScript可接受的語法
function ValueString($str)
{
	return SQLString(RemoveReturn(str_replace('\\', '\\\\', $str)));
}

// 用於中文(unicode)的substr
// $str = 傳入字串
// $n = 要擷取字數
// $tail = 尾巴字串
function SubString($str, $n, $tail = '...')
{
	if (mb_strwidth($str) <= $n) // 字串過短就跳回
		return $str;
	$n -= mb_strwidth($tail); // 要先剪掉尾巴長度
	$i = $z = 0;
	do
	{
		$x = mb_strwidth(mb_substr($str, $i, 1));
		if ($z + $x > $n)
			break;
		$z += $x;
		$i++;
	}
	while ($z != $n);
	return mb_substr($str, 0, $i) . $tail;
}

// 將result set資料(一筆) assign 到 $VARS
// $rs = ResultSet
// $callback = 回call函式
// $prefix = 每個變數的前置字串, 會自動加底線
function AssignValue($rs, $callback = null, $prefix = '')
{
	global $VARS;

	if ($rs->count == 0)
		return;
	$row = $rs->fetch();
	if (!is_null($callback))
		call_user_func($callback, &$row);
	if (!empty($prefix))
		$prefix = strtolower($prefix) . '_';
	foreach ($row as $field => $val)
		$VARS[$prefix . strtolower($field)] = $val;
}

// 將result set資料製作成陣列回傳
// $rs = ResultSet
// $callback = 回call函式
// $i = index起始編號
// $cnt = 處理筆數, -1為全處理
function AssignResult($rs, $callback = null, $i = 1, $cnt = -1)
{
	if ($rs->count == 0)
		return null;
	$c = 0;
	while (($row = $rs->fetch()) && $c != $cnt)
	{
		$row['ii'] = $i - 1;
		$row['i'] = $i++;
		if (!is_null($callback))
			call_user_func($callback, &$row);
		foreach ($row as $field => $value) {
			/*
			if ( $field == "cat_name" ) {
				$v[strtolower($field)] = mb_substr( $value, 0, 20,"UTF-8" );
			} else {
				$v[strtolower($field)] = $value;
			}
			*/
			$v[strtolower($field)] = $value;
		}
		$vv[] = $v;
		unset($v);
		$c++;
	}
	$vv[count($vv) - 1]['LAST'] = true;
	return $vv;
}

// 將result set資料製成朗兩層陣列回傳
// $rs = ResultSet
// $callback = 回call函式
// $i = index起始編號
// $cnt = 處理筆數, -1為全處理
// $div = 有幾個column
function AssignResult2($rs, $callback = null, $i = 1, $cnt = -1, $div = 2)
{
	if ($rs->count == 0)
		return null;
	$c = 0;
	while (($row = $rs->fetch()) && $c != $cnt)
	{
		$k = $i % $div;
		if ($k == 0)
			$k = 5;
		$row['i'] = $i++;
		if (!is_null($callback))
			call_user_func($callback, &$row);
		foreach ($row as $field => $value)
			$v[strtolower($field) . $k] = $value;
		if ($k == $div)
		{
			$vv[] = $v;
			unset($v);
		}
		$c++;
	}
	if (isset($v))
		$vv[] = $v;
	return $vv;
}

// 將result set資料製成陣列 assign 到 $VARS
// $rs = ResultSet
// $var = 陣列名稱, 不傳值預設list, 有值會自動加入_list
// $callback = 回call函式
// $i = index起始編號
// $cnt = 處理筆數, -1為全處理
function AssignValues($rs, $var = null, $callback = null, $i = 1, $cnt = -1)
{
	global $VARS;

	if (is_null($var))
	{
		$assign = 'list';
		$nodata = 'nodata';
	}
	else
	{
		$assign = $var . '_list';
		$nodata = $var . '_nodata';
	}
	if ($rs->count == 0)
	{
		$VARS[$nodata] = true;
		return;
	}
	$VARS[$assign] = AssignResult($rs, $callback, $i, $cnt);
}

// 將result set資料製成朗兩層陣列 assign 到 $VARS
// $rs = ResultSet
// $var = 陣列名稱, 不傳值預設list, 有值會自動加入_list
// $callback = 回call函式
// $i = index起始編號
// $cnt = 處理筆數, -1為全處理
// $div = 有幾個column
function AssignValues2($rs, $var = null,  $callback = null, $i = 1, $cnt = -1, $div = 2)
{
	global $VARS;

	if (is_null($var))
	{
		$assign = 'list';
		$nodata = 'nodata';
	}
	else
	{
		$assign = $var . '_list';
		$nodata = $var . '_nodata';
	}
	if ($rs->count == 0)
	{
		$VARS[$nodata] = true;
		return;
	}
	$VARS[$assign] = AssignResult2($rs, $callback, $i, $cnt, $div);
}

function AssignValues3($rs, $var = null,  $callback = null, $div = 2, $i = 1)
{
	global $VARS;

	if (is_null($var))
	{
		$assign = 'list';
		$nodata = 'nodata';
	}
	else
	{
		$assign = $var . '_list';
		$nodata = $var . '_nodata';
	}
	if ($rs->count == 0)
	{
		$VARS[$nodata] = true;
		return;
	}
	$v = AssignResult($rs, $callback, $i, -1);
	if (count($v) < $div)
		while (count($v) != $div)
			array_push($v, '');
	$VARS[$assign] = $v;
}


// 將URL後的參數刪掉某key=value
// 如fn=abc&mo=zzz&yy=555, RemoveKey('yy')則回傳fn=abc&mo=zzz
// $str = URL
// $k = Key
function RemoveKey($str, $k)
{
	$n = strlen($k) + 1;
	$z = $k . '=';
	$x = explode('&', $str);
	if (is_array($x))
	{
		foreach ($x as $k => $v)
		{
			if (substr($v, 0, $n) == $z)
				unset($x[$k]);
		}
		return implode('&', $x);
	}
	else
		return $str;
}

// 頁面控制, 頁數是跳動的, 1 11 21 31...
// $sql = 取得資料的SQL
// $cnt = 計算資料筆數的SQL, 通常是 SELECT COUNT(*) FROM ...
// $icount = 每頁筆數
// $link = 跳頁連結URL, 通常是$CUR_URL
// $callback = 回call函式
function PageControl($sql, $cnt, $icount = null, $link = null, $callback = null)
{
	global $VARS, $rs, $NO_TOTAL, $CUR_URL, $ADMIN, $ADMIN_COUNT, $PAGE_COUNT;

	// Get Count
	$r = mysql_query($cnt);
	$r = @mysql_fetch_row($r);
	$count = $r[0];
	if ($count == 0)
	{
		$VARS['nodata'] = true;
		$VARS['total_item'] = 0;
		$VARS['total_page'] = 0;
		$VARS['cur_page'] = 0;
		$VARS['page_control'] = '<font color="#FF9900;">1</font>';
	}
	else
	{
		if (empty($link))
			$link = $CUR_URL;
		if (empty($icount))
			$icount = $ADMIN ? $ADMIN_COUNT : $PAGE_COUNT;
		$link = RemoveKey($link, 'p');
		$pp = strchr($link, '?') != -1 ? '&p=' : '?p=';
		// Go to
		$to = $count;
		$VARS['total_item'] = $to;
		$total = intval(($to + $icount - 1) / $icount);
		$VARS['total_page'] = $total;
		$cur = isset($_GET['p']) ? $_GET['p'] : 1;
		if ($cur > $total)
			$cur = $total;
		elseif ($cur < 1)
			$cur = 1;
		// Get Data
		$cc = ($cur - 1) * $icount;
		$rs->query($sql . " LIMIT $cc, $icount");
		$VARS['page_item'] = $rs->count;
		// Set data
		AssignValues($rs, null, $callback, $cc + 1, $icount);
		// Set Page
		$s = $cur == 1 ? '' : '<a href="' . $link . $pp . ($cur - 1) . "\"><img src=\"../images/admin/arrow_prior.gif\" border=0 alt=\"Prior Page\" align=\"texttop\"></a> ";
		$ff = intval(($cur - 1) / 10) * 10;
		$tt = $ff + 10;
		for ($i = 1; $i <= $total; $i++)
		{
			if ($i > $ff && $i <= $tt)
				$s .= $i != $cur ? "<a href=\"$link$pp$i\" style=\"font-size: 9pt;\">$i</a> " : "<font color=\"#FF9900;\" style=\"font-size: 9pt;\">$i</font> ";
			else
			{
				if ($i % 10 == 1)
					$s .= "<a href=\"$link$pp$i\">$i</a> ";
			}
		}
		if ($total % 10 != 1)
		{
			$s = substr($s, 0, strlen($s) - 1) . ' ';
			if ($tt != $total && $cur + 9 < $total)
				$s .= "<a href=\"$link$pp$total\"><font color=\"#CCCCCC\">$total</font></a> ";
		}
		if ($cur != $total)
		{
			$s .= '<a href="' . $link . $pp . ($cur + 1) . "\"><img src=\"../images/admin/arrow_next.gif\" border=0 alt=\"Next Page\" align=\"texttop\"></a> ";
			$VARS['next_page'] = $cur + 1;
			$VARS['last_page'] = $total;
		}
		$_SESSION['current_page'] = $VARS['cur_page'] = $cur;
		if ($cur != 1)
			$VARS['prior_page'] = $cur - 1;
		$VARS['page_control'] = trim($s);
	}
}

function PageControl2($sql, $cnt, $icount = 25, $link = null, $callback = null)
{
	global $VARS, $rs;

	// Get Count
	$r = mysql_query($cnt);
	$r = mysql_fetch_row($r);
	$count = $r[0];
	if ($count == 0)
	{
		$VARS['nodata'] = true;
		$VARS['total'] = 0;
		$VARS['total_page'] = 0;
		$VARS['cur_page'] = 0;
	}
	else
	{
		$link = RemoveKey($link, 'p');
		$pp = strchr($link, '?') != -1 ? '&p=' : '?p=';
		// Go to
		$to = $count;
		$VARS['total'] = $to;
		$total = intval(($to + $icount - 1) / $icount);
		$VARS['total_page'] = $total;
		$cur = isset($_GET['p']) ? $_GET['p'] : 1;
		if ($cur > $total)
			$cur = $total;
		elseif ($cur < 1)
			$cur = 1;
		// Get Data
		$cc = ($cur - 1) * $icount;
		$rs->query($sql . " LIMIT $cc, $icount");
		// Set data
		AssignValues($rs, null, $callback, $cc + 1, $icount);
		// Set Page
		for ($i = 1; $i <= $total; $i++)
			$s .= $i != $cur ? "<a class='style17' href=\"$link$pp$i\">$i</a>  " : "<span class='style17'><b>$i</b></span>  ";
		$VARS['page_control'] = "<span class='style17'>" . substr($s, 0, strlen($s) - 3) . '</span>';
		$VARS['cur_page'] = $cur;
	}
}

// 同PageContrl, 但是二為陣列的$row x $col
// 必須用巢狀<!-- BEGIN list -->
function PageControl3($sql, $cnt, $row, $col, $link = null, $callback = null)
{
	global $VARS, $rs;

	$icount = $row * $col;
	// Get Count
	$r = mysql_query($cnt);
	$r = mysql_fetch_row($r);
	$count = $r[0];
	if ($count == 0)
	{
		$VARS['nodata'] = true;
		$VARS['total_item'] = 0;
		$VARS['total_page'] = 0;
		$VARS['cur_page'] = 0;
	}
	else
	{
		$link = RemoveKey($link, 'p');
		$pp = strchr($link, '?') != -1 ? '&p=' : '?p=';
		// Go to
		$to = $count;
		$VARS['total_item'] = $to;
		$total = intval(($to + $icount - 1) / $icount);
		$VARS['total_page'] = $total;
		$cur = isset($_GET['p']) ? $_GET['p'] : 1;
		if ($cur > $total)
			$cur = $total;
		elseif ($cur < 1)
			$cur = 1;
		// Get Data
		$cc = ($cur - 1) * $icount;
/*
		$rs->query($sql . " LIMIT $cc, $icount");
		// Set data
		AssignValues2($rs, null, $callback, $cc + 1, $icount);
*/
		unset($v, $val);
		$pi = 0;
		for ($i = 0; $i < $row; $i++)
		{
			$ccc = $cc + $i * $col;
			$rs->query($sql . " LIMIT $ccc, $col");
			if ($rs->count == 0)
				break;
			$pi += $rs->count;
			$v['list'] = AssignResult($rs, $callback, $i * $col + 1, $col);
			while (count($v['list']) != $col)
				array_push($v['list'], '');
			$val[] = $v;
		}
		$VARS['page_item'] = $pi;
		$VARS['list'] = $val;
		// Set Page
		$s = $cur == 1 ? '' : '<a href="' . $link . $pp . ($cur - 1) . "\">上一頁</a> | ";
		$ff = intval(($cur - 1) / 10) * 10;
		$tt = $ff + 10;
		for ($i = 1; $i <= $total; $i++)
		{
			if ($i > $ff && $i <= $tt)
				$s .= $i != $cur ? "<a href=\"$link$pp$i\">$i</a> . " : "<font color=\"#666666;\"><b>$i</b></font> . ";
			else
			{
				if ($i % 10 == 1)
					$s .= "<a href=\"$link$pp$i\">$i</a> | ";
			}
		}
		if ($total % 10 != 1)
		{
			$s = substr($s, 0, strlen($s) - 3) . ' | ';
			if ($tt != $total && $cur + 9 < $total)
				$s .= "<a href=\"$link$pp$total\">$total</a> | ";
		}
		if ($cur != $total)
			$s .= '<a href="' . $link . $pp . ($cur + 1) . "\">下一頁</a>...";
//		$v = "共<font color=\"#0080C0\">$to</font>筆";
//		$VARS['page_control'] = "<span style=\"font-family:arial;\">[$v][ " . substr($s, 0, strlen($s) - 3) . ' ]</span>&nbsp;';
		$VARS['page_control'] = "<span style=\"font-family:arial;\">" . substr($s, 0, strlen($s) - 3) . '</span>&nbsp;';
		$VARS['cur_page'] = $cur;
	}
	if ($cur != $total)
	{
		$VARS['next_page'] = $cur + 1;
		$VARS['last_page'] = $total;
	}
	$_SESSION['current_page'] = $VARS['cur_page'] = $cur;
	if ($cur != 1)
		$VARS['prior_page'] = $cur - 1;
}

// 取得$_POST或$_GET的參數
function GetParam($par, $ret = null)
{
	return isset($_GET[$par]) || isset($_POST[$par]) ? isset($_GET[$par]) ? $_GET[$par] : $_POST[$par] : $ret;
}

function GoTo($str)
{
	header('Location: index.php?' . $str);
	exit;
}

function GoMain($tail = '')
{
	global $FUNC;

	if ($tail != '')
		GoTo('fn=' . $FUNC . '&' . $tail);
	else
		GoTo('fn=' . $FUNC);
}

// 回到上二個page, 如沒記錄就用JS
function GoBack()
{
	global $ADMIN;

	$var = isset($ADMIN) ? 'alpage' : 'lpage';
	if (isset($_SESSION[$var]) && count($_SESSION[$var]) >= 2)
	{
		array_pop($_SESSION[$var]);
		$a = array_pop($_SESSION[$var]);
		header('Location: index.php?' . $a);
	}
	else
	{
		echo <<<EOT
<script language="javascript">
	history.go(-2);
</script>
EOT;
	}
	exit;
}

// 回到上一個page, 如沒記錄就用JS
function GoLast()
{
	global $ADMIN;

	$var = isset($ADMIN) ? 'alpage' : 'lpage';
	if (isset($_SESSION[$var]) && count($_SESSION[$var]) >= 1)
	{
		$a = array_pop($_SESSION[$var]);
		header('Location: index.php?' . $a);
	}
	else
	{
		echo <<<EOT
<script language="javascript">
	history.go(-1);
</script>
EOT;
	}
	exit;
}

// 顯示JS訊息
function Message($msg, $goback = false)
{
	global $ADMIN;

	$var = isset($ADMIN) ? 'amsg' : 'msg';
	if (!empty($_SESSION[$var]))
		$_SESSION[$var] .= "\n$msg";
	else
		$_SESSION[$var] = $msg;
	if ($goback)
		GoLast();
}

function Either($a, $b)
{
	return is_null($a) ? $b : $a;
}

// 將$_POST的資料全部變成global變數
function PostGlobal()
{
	global $VARS;

	if (!is_array($_POST))
		return;
	foreach($_POST as $k => $v)
	{
		global $$k;
		$$k = $v;
		$VARS[$k] = $v;
	}
}

// 將$_POST資料全部製成一個list, 名為post_list
function PostVars()
{
	global $VARS;

	if (!is_array($_POST))
		return;
	foreach($_POST as $k => $v)
	{
		$VARS[$k] = $v;
		unset($vv);
		$vv['name'] = $k;
		$vv['value'] = $v;
		$val[] = $vv;
	}
	$VARS['post_list'] = $val;
}

// 將$_POST儲存至session
function PostSave()
{
	if (isset($_POST))
		$_SESSION['postdata'] = serialize($_POST);
}

// 將上次儲存的POST資料都變成全域變數
function PostLoad()
{
	global $VARS;

	if (isset($_SESSION['postdata']))
	{
		$data = unserialize($_SESSION['postdata']);
		foreach($data as $k => $v)
		{
			global $$k;
			$$k = $v;
			$VARS[$k] = $v;
		}
		unset($_SESSION['postdata']);
	}
}

// 判斷是否有POST data
function HasPost()
{
	return isset($_SESSION['postdata']);
}

// 取得 TABLE configs裡的設定
// $sep = separator, 有設定的話會自動將資料以$sep分開為array
function GetConfig($id, $sep = '')
{
	$sql = 'SELECT content FROM configs WHERE id = ' . $id;
	$rs = mysql_query($sql);
	if (mysql_num_rows($rs) == 0)
	{
		$sql = "INSERT INTO configs SET id = $id, content = ''";
		$rs = mysql_query($sql);
		$data = '';
	}
	else
	{
		list ($data) = mysql_fetch_row($rs);
		mysql_free_result($rs);
	}
	return $sep == '' || strstr($data, $sep) == '' ? $data : explode($sep, $data);
}

// 將設定資料寫入取得 TABLE configs
// $sep = separator, 有設定的話會自動將資料(array)以$sep合併
function SetConfig($id, $data, $sep = ',')
{
	if (is_array($data))
		$data = implode($sep, $data);
	$sql = "UPDATE configs SET content = '$data' WHERE id = $id";
	mysql_query($sql);
}

// 將HTML editor用的圖片移到正確位置
function MoveImages($dest, $val, $prefix = '')
{
	global $DOC_ROOT, $TEMP_IMAGE, $IMAGE_PATH;

	$rs = new ResultSet();
	if (file_exists($IMAGE_PATH))
	{
		$dpath = $DOC_ROOT . $dest;
		if (!file_exists($dpath))
			MakeDir($dpath);
		$sql = "SELECT * FROM editors WHERE sessid = '{$_REQUEST['PHPSESSID']}'";
		$rs->query($sql);
		while (($r = $rs->fetch()))
		{
			$f = $r['tmpfile'];
			if (filetype($IMAGE_PATH . $f) != 'file')
				continue;
			if (empty($prefix))
				$df = $dpath . $r['filename'];
			else
				$df = $dpath . $prefix . '_' . $r['filename'];
			if (file_exists($df))
				unlink($df);
			rename($IMAGE_PATH . $f, $df);
			chmod($df, 0666);
//			if (file_exists($IMAGE_PATH . $f))
//				unlink($IMAGE_PATH . $f);
			$sql = "DELETE FROM editors WHERE sessid = '{$_REQUEST['PHPSESSID']}' AND cdate = '{$r['cdate']}'";
			$rs->execute($sql);
		}
//		ClearTempDir();
	}
	$tmp = $TEMP_IMAGE . $_REQUEST['PHPSESSID'] . '_';
	if ($prefix == '')
		return str_replace($tmp, $dest, $val);
	else
		return str_replace($tmp, $dest . $prefix . '_', $val);
}

// 清除HTML editor暫存圖片
function ClearTempDir()
{
	global $DOC_ROOT, $IMAGE_PATH;

	if (file_exists($IMAGE_PATH))
	{
		$rs = new ResultSet();
		$date = date('Y-m-d H:i:s', time() - 86400);
		$sql = "DELETE FROM editors WHERE cdate <= '$date'";
		$rs->execute($sql);
//		$sql = "DELETE FROM editors WHERE sessid = '{$_REQUEST['PHPSESSID']}'";
//		$rs->execute($sql);
	}
}

// 建立HTML editor
function NewEditor($instance, $value = '', $height = '360', $width = '100%', $variable = 'editor')
{
	global $VARS, $DOC_ROOT, $EDITABLE;

	$value = ValueString($value);
	ClearTempDir();
//ed.Config['ImageBrowserURL'] = ed.BasePath + 'editor/filemanager/browser/delta/browser.html?Type=Image&Connector=connectors/php/connector.php&ServerPath=$TEMP_IMAGE&WebRoot=$DOC_ROOT' ;
//ed.Config['FlashBrowserURL'] = ed.BasePath + 'editor/filemanager/browser/delta/browser.html?Type=Flash&Connector=connectors/php/connector.php&ServerPath=$TEMP_IMAGE&WebRoot=$DOC_ROOT' ;
	$VARS[$variable] = <<<EOT
<script type="text/javascript" src="../FCKeditor/fckeditor.js"></script>
<script language"JavaScript">
var ed = new FCKeditor('$instance');
ed.BasePath = '../FCKeditor/';
ed.ToolbarSet = 'Compact4';
ed.Config['ImageUploadURL'] = ed.BasePath + 'editor/filemanager/upload/delta/upload.php?Type=Image&WebRoot=$DOC_ROOT&SessId={$_REQUEST['PHPSESSID']}' ;
ed.Config['FlashUploadURL'] = ed.BasePath + 'editor/filemanager/upload/delta/upload.php?Type=Flash&WebRoot=$DOC_ROOT&SessId={$_REQUEST['PHPSESSID']}' ;
ed.Width = '$width';
ed.Height = '$height';
ed.Value = '$value';
ed.Create();
</script>
<font color=blue>* 請確定游標在編輯器內閃動再開始輸入資料!</font><br><br>提示：表格可按下滑鼠右鍵，<br>選「欄」 --> 「插入欄」來增加欄位，<br>選「列」 --> 「插入列」來增加資料列。<br>
EOT;
}

// 建立一個TextArea (不使用HTML editor時以此取代)
function NewTextArea($instance, $value = '', $rows = '20', $cols = '100%', $variable = 'editor')
{
	global $VARS, $EDITABLE;

	$dis = $EDITABLE ? '' : 'disabled';
	$VARS[$variable] = <<<EOT
<textarea name="$instance" rows="$rows" style="width: $cols;" $dis>$value</textarea>
EOT;
}

// 將Form File移到指定位置, 配合database data
function MoveUpload($field, $dest, $id, $postfix = '')
{
	// 改為保有原檔名 2007/8/3
	global $DOC_ROOT;

	if (is_uploaded_file($_FILES[$field]['tmp_name']))
	{
		$file = $_FILES[$field]['name'];
		$fn = $DOC_ROOT . "$dest/$file";
		if (file_exists($fn))
			unlink($fn);
		if (!is_dir($DOC_ROOT . $dest))
			MakeDir($DOC_ROOT . $dest);
		move_uploaded_file($_FILES[$field]['tmp_name'], $fn);
		chmod($fn, 0666);
		return str_replace('//', '/', "/$dest/$file");
	}
	else
		return '';
}
/* old
function MoveUpload($field, $dest, $id, $postfix = '')
{
	global $DOC_ROOT;

	if (is_uploaded_file($_FILES[$field]['tmp_name']))
	{
		$ext = strrchr($_FILES[$field]['name'], '.');
		if ($postfix == '')
			$file = $id . $ext;
		else
			$file = $id . '_' . $postfix . $ext;
		$fn = $DOC_ROOT . "$dest/$file";
		if (file_exists($fn))
			unlink($fn);
		if (!is_dir($DOC_ROOT . $dest))
			MakeDir($DOC_ROOT . $dest);
		move_uploaded_file($_FILES[$field]['tmp_name'], $fn);
		chmod($fn, 0666);
		return str_replace('//', '/', "/$dest/$file");
	}
	else
		return '';
}
*/

// 僅將Form File移到指定位置, 與database無關
function MoveUpload2($field, $dest)
{
	global $DOC_ROOT;

	if (is_uploaded_file($_FILES[$field]['tmp_name']))
	{
		$ext = strrchr($_FILES[$field]['name'], '.');
		$fn = $DOC_ROOT . $dest . $ext;
		if (file_exists($fn))
			unlink($fn);
		move_uploaded_file($_FILES[$field]['tmp_name'], $fn);
		return str_replace('//', '/', $dest . $ext);
	}
	else
		return '';
}

// 生成image file
function MakePicture($field, $varname, $dir, $filename = '', $div = null)
{
	global $DOC_ROOT, $IMAGE_WIDTH, $IMAGE_HEIGHT;

	if (!is_uploaded_file($_FILES[$field]['tmp_name']))
		return '';
	$name = $_FILES[$field]['name'];
	$src = $_FILES[$field]['tmp_name'];
	$ext = strrchr($name, '.');
	$file = strtolower(basename($name, $ext));
	$ext = strtolower($ext);
	if (empty($filename))
		$pic = $file . $ext;
	else
		$pic = $filename . $ext;
	$dest = $DOC_ROOT .  iconv( "UTF-8", "Big5", "$dir/$pic" );
	//$dest = $DOC_ROOT . "$dir/$pic";
	if (file_exists($dest))
		unlink($dest);
	if ($ext == '.swf')
	{ // Flash
		move_uploaded_file($src, $dest);
	}
	else
	{
		if (is_array($div))
		{
			$div_width = $div[0];
			$div_height = $div[1];
		}
		else
		{
			$div_width = $IMAGE_WIDTH;
			$div_height = $IMAGE_HEIGHT;
		}
		$size = getimagesize($src);
		if ($div_width >= $size[0] && $div_height >= $size[1])
			copy($src, $dest);
		else
		{
			$div = $div_width / $size[0];
			if (floor($size[1] * $div) > $div_height)
				$div = $div_height / $size[1];
			if ($div > 1)
			{
				$width = $size[0];
				$height = $size[1];
			}
			else
			{
				$width = floor($size[0] * $div);
				$height = floor($size[1] * $div);
			}
			switch ($ext)
			{
			case '.jpg':
			case '.jpeg':
				$is = imagecreatefromjpeg($src);
				break;
			case '.gif':
				$is = imagecreatefromgif($src);
				break;
			case '.png':
				$is = imagecreatefrompng($src);
				break;
			}
			if ($ext == '.gif')
			{
				$tp = imagecolorat($is, 0, 0);
				$id = imagecreate($width, $height);
				imagepalettecopy($id, $is);
	//			$bg = imagecolorallocate($id, 255, 255, 255);
	//			imagefilledrectangle($id, 0, 0, $width, $height, $bg);
				imagefilledrectangle($id, 0, 0, $width, $height, $tp);
				imagecopyresized($id, $is, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
				imagecolortransparent($id, $tp);
			}
			else
			{
				$id = imagecreatetruecolor($width, $height);
				imagecopyresampled($id, $is, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}
			switch ($ext)
			{
			case '.jpg':
			case '.jpeg':
				imagejpeg($id, $dest, 100);
				break;
			case '.gif':
				imagegif($id, $dest);
				break;
			case '.png':
				imagepng($id, $dest);
				break;
			}
		}
	}
	chmod($dest, 0666);
	$_POST[$varname] = str_replace('//', '/', "/$dir/$pic");
	return $_POST[$varname];
}

// 生成縮圖檔, 配合MakePicture
function MakeThumb($dir, $src, $varname = 'thumb', $postfix = 's', $div = null)
{
	global $DOC_ROOT, $THUMB_WIDTH, $THUMB_HEIGHT;

	$src = $DOC_ROOT . $src;
	if (!file_exists($src))
		return '';
	$ext = strrchr($src, '.');
	$file = strtolower(basename($src, $ext));
	$ext = strtolower($ext);
	$thumb = $file . $postfix . $ext;
	$dest = $DOC_ROOT . "$dir/$thumb";
	if (file_exists($dest))
		unlink($dest);
	if ($ext == '.swf')
	{ // Flash
		move_uploaded_file($src, $dest);
	}
	else
	{
		if (is_array($div))
		{
			$div_width = $div[0];
			$div_height = $div[1];
		}
		else
		{
			$div_width = $THUMB_WIDTH;
			$div_height = $THUMB_HEIGHT;
		}
		$size = getimagesize($src);
		if ($div_width >= $size[0] && $div_height >= $size[1])
			copy($src, $dest);
		else
		{
			$div = $div_width / $size[0];
			if (floor($size[1] * $div) > $div_height)
				$div = $div_height / $size[1];
			if ($div > 1)
			{
				$width = $size[0];
				$height = $size[1];
			}
			else
			{
				$width = floor($size[0] * $div);
				$height = floor($size[1] * $div);
			}
			switch ($ext)
			{
			case '.jpg':
			case '.jpeg':
				$is = imagecreatefromjpeg($src);
				break;
			case '.gif':
				$is = imagecreatefromgif($src);
				break;
			case '.png':
				$is = imagecreatefrompng($src);
				break;
			}
			if ($ext == '.gif')
			{
				$tp = imagecolorat($is, 0, 0);
				$id = imagecreate($width, $height);
				imagepalettecopy($id, $is);
		//		$bg = imagecolorallocate($id, 255, 255, 255);
		//		imagefilledrectangle($id, 0, 0, $width, $height, $bg);
				imagefilledrectangle($id, 0, 0, $width, $height, $tp);
				imagecopyresized($id, $is, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
				imagecolortransparent($id, $tp);
			}
			else
			{
				$id = imagecreatetruecolor($width, $height);
				imagecopyresampled($id, $is, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}
			switch ($ext)
			{
			case '.jpg':
			case '.jpeg':
				imagejpeg($id, $dest, 100);
				break;
			case '.gif':
				imagegif($id, $dest);
				break;
			case '.png':
				imagepng($id, $dest);
				break;
			}
		}
	}
	chmod($dest, 0666);
	$_POST[$varname] = str_replace('//', '/', "/$dir/$thumb");
	return $_POST[$varname];
}

function NullDateTime($s)
{
	return substr($s, 0 , 4) == '0000';
}

// 寄送信件, 可依語系修改(UTF-8)
function SendMail($from, $to, $subject, $content)
{
	require_once('htmlMimeMail.php');
	$mail = new htmlMimeMail();
	$mail->setHeadCharset('UTF-8');
	$mail->setTextCharset('UTF-8');
	$mail->setHtmlCharset('UTF-8');
//	$mail->setReturnPath($from);
	$mail->setFrom($from);
	$mail->setSubject($subject);
	$mail->setHtml($content);
	if (strstr($_SERVER['HTTP_HOST'], 'howto'))
	{
		$mail->setSMTPParams('192.168.0.1');
		$mail->send(array($to), 'smtp');
	}
	else
		$mail->send(array($to));
	unset($mail);
}

// 寄送信件, 信件內容由檔案讀入, 可使用SmartTemplate, 變數由$var傳入
function SendMail3($from, $to, $subject, $file, $var = null)
{
	$tt = new SmartTemplate($file);
	foreach($var as $k => $v)
		$tt->assign($k, $v);
	$tt->assign('web_url', $_SERVER['HTTP_HOST']);
	$content = $tt->result();
	unset($tt);
	require_once('htmlMimeMail.php');
	$mail = new htmlMimeMail();
	$mail->setHeadCharset('Big5');
	$mail->setTextCharset('Big5');
	$mail->setHtmlCharset('Big5');
	$mail->setFrom($from);
	$mail->setSubject(mb_convert_encoding($subject, 'big5', 'utf-8'));
//	$mail->setSubject($subject);
//	$mail->setHtml($content);
	$mail->setHtml(mb_convert_encoding($content, 'big5', 'utf-8'));
	if (strstr($_SERVER['HTTP_HOST'], 'howto'))
	{
		$mail->setSMTPParams('192.168.0.1');
		$mail->send(array($to), 'smtp');
	}
	else
		$mail->send(array($to));
	unset($mail);
}

// 寄送郵件, 以SMTP方式寄出, 少用
function SendMail2($from, $to, $subject, $content, $var = null)
{
	require_once('htmlMimeMail.php');
	if (!is_null($var))
		foreach($var as $k => $v)
			$content = str_replace('%' . $k . '%', $v, $content);
	$content = str_replace('%web_url%', $_SERVER['HTTP_HOST'], $content);
	$mail = new htmlMimeMail();
	$mail->setSMTPParams(GetConfig(4));
	$mail->setHeadCharset('Big5');
	$mail->setTextCharset('Big5');
	$mail->setHtmlCharset('Big5');
//	$mail->setReturnPath($from);
	$mail->setFrom($from);
	$mail->setSubject($subject);
	$mail->setHtml($content);
	if (count($to) > 1)
	{
		$too = array_shift($to);
		$mail->setBcc(implode(';', $to));
	}
	else
		$too = $to[0];
	$mail->send(array($too));
	unset($mail);
}

// 幾乎不用
function MailTo($from, $to, $id, $var)
{
	global $EMAIL_SUBJECT;
	require_once('htmlMimeMail.php');

	$content = GetConfig($id);
	foreach($var as $k => $v)
		$content = str_replace('%' . $k . '%', $v, $content);
	$content = str_replace('%web_url%', $_SERVER['HTTP_HOST'], $content);
	$mail = new htmlMimeMail();
	$mail->setSMTPParams(GetConfig(4));
	$mail->setHeadCharset('Big5');
	$mail->setTextCharset('Big5');
	$mail->setHtmlCharset('Big5');
//	$mail->setReturnPath($from);
	$mail->setFrom($from);
	$mail->setSubject($EMAIL_SUBJECT[$id]);
	$mail->setHtml($content);
	$mail->send(array($to));
	unset($mail);
}

function FillSpace($a)
{
	global $VARS;

	if (!is_array($a))
		$a = array($a);
	foreach ($a as $v)
		if ($VARS[$v] == '')
			$VARS[$v] = '&nbsp;';
}

function CarriageReturn($a)
{
	global $VARS;

	if (!is_array($a))
		$a = array($a);
	foreach ($a as $v)
	{
		if (trim($VARS[$v]) != '')
			$VARS[$v] = nl2br($VARS[$v]);
		else
			$VARS[$v] = '&nbsp;';
	}
}

// 顯示圖片, $s為檔名, 傳入後會判斷屬於哪種檔案回傳正確格式, 如img or flash
// $default: 如果找不到檔案則以$default為預設圖片
function ShowPicture($s, $default = '')
{
	global $DOC_ROOT, $ADMIN;

	if ($s == '' || !file_exists($DOC_ROOT . $s))
		$s = $default;
	if ($s == '')
	{
		if ($ADMIN)
			return '';
		else
			return '&nbsp;';
	}
	$ext = strrchr($s, '.');
	if ($ext != '.swf')
	{
		if ($ADMIN)
			return "<img src=\"img.php?f=$s&rand=" . rand( 1000,9999 ) . "\" border=\"0\">";
		else
			return "<img src=\"$s\" border=\"0\">";
	}
	else
	{
		$r = getimagesize($DOC_ROOT . $s);
		return <<<EOT
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{$r[0]}" height="{$r[1]}" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0">
	<param name="movie" value="$s">
	<param name="quality" value="high">
	<embed src="$s" quality="high" width="{$r[0]}" height="{$r[1]}" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
</object>
EOT;
	}
}

// 不用
function TransPicture(&$var, $field)
{
	if (!is_array($var))
		return;
	if (!is_array($field))
		$field = array($field);
	foreach ($var as $k => $v)
	{
		if (is_array($v))
		{ // two level
			foreach ($v as $kk => $vv)
			{
				if (in_array($kk, $field))
					$var[$k][$kk] = ShowPicture($vv);
			}
		}
		else
		{
			if (in_array($k, $field))
				$var[$k] = ShowPicture($v);
		}
	}
}

// 刪除table裡fields所指定欄位的檔案
function DeleteFiles($table, $id, $fields = array('picture'), $editor = false)
{
	global $rs3, $DOC_ROOT;

	if (!is_array($fields))
		$fields = array($fields);
	$rs3->select($table, $id);
	if ($rs3->count == 0)
		return;
	$r = $rs3->fetch();
	foreach ($fields as $f)
	{
		if (!empty($r[$f]) && file_exists($DOC_ROOT . $r[$f]))
			unlink($DOC_ROOT . $r[$f]);
		$v[$f] = '';
	}
	$rs3->update($table, $id, $v);
	// TODO: Delete files in editor
}

// 移動排序位置
function RankExchange($table, $dir, $rank, $id, $cond = '1')
{
	$rs = new ResultSet();

	if ($dir == 1)
		$sql = "SELECT id, rank FROM $table WHERE $cond AND rank < $rank ORDER BY rank DESC LIMIT 1";
	else
		$sql = "SELECT id, rank FROM $table WHERE $cond AND rank > $rank ORDER BY rank LIMIT 1";
	$rs->query($sql);
	list ($i, $r) = $rs->row();
	$rs->update($table, $i, array('rank' => $rank));
	$rs->update($table, $id, array('rank' => $r));
	unset($rs);
}

// 後兩者多使用在狀態部分, 如avail(tinyint), 1為on, 0為off, 會自動生成JS去勾選checkbox
function SetBit($fields)
{
	if (!is_array($fields))
		$fields = array($fields);
	foreach ($fields as $f)
		if (empty($_POST[$f]))
			$_POST[$f] = '0';
}

function GetBit($fields)
{
	global $SCRIPTS, $VARS;

	if (!is_array($fields))
		$fields = array($fields);
	foreach ($fields as $f)
		$SCRIPTS[] = "chooseBox(frm.$f, '{$VARS[$f]}');";
}

?>

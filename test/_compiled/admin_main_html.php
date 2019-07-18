<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><!-- InstanceBegin template="/Templates/admin.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<title><?php
echo $_obj['SITE_TITLE'];
?>
::網站管理系統</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="admin.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="func.js"></script>
<script language="javascript">
<!--
var tick = 1800;
var clockID = 0;

function UpdateClock()
{
	if(clockID) {
		clearTimeout(clockID);
		clockID  = 0;
	}
	tick -= 5;
	if (tick <= 0)
		go('index.php?fn=logout&x=x');
	minute = Math.floor((tick + 59) / 60);
	sp = document.getElementById('logout');
	sp.innerHTML = minute;
	//clockID = setTimeout("UpdateClock()", 5000);
}

function showPlate(show)
{
	var p = document.getElementById('WMSPlate');
	var h = document.getElementById('plateHide');
	var s = document.getElementById('plateShow');
	
	if (show)
	{
		p.style.display = '';
		h.style.display = '';
		s.style.display = 'none';
	}
	else
	{
		p.style.display = 'none';
		h.style.display = 'none';
		s.style.display = '';
	}
	iplate.location.replace('index.php?fn=wmsplate');
}

function nothing()
{
	iplate.location.replace('index.php?fn=blank');
}

function confirmDelete(id)
{
	if (confirm('確定要刪除嗎?'))
		go('<?php
echo $_obj['DELETE_URL'];
?>
&id=' + id);
}

function confirmDeletePicture(s)
{
	if (confirm('確定要刪除此圖片嗎?'))
		go('<?php
echo $_obj['EDIT_URL'];
?>
&sub=del&id=<?php
echo $_obj['id'];
?>
&s=' + s);
}

function confirmDeleteFile()
{
	if (confirm('確定要刪除此檔案嗎?'))
		go('<?php
echo $_obj['EDIT_URL'];
?>
&sub=del2&id=<?php
echo $_obj['id'];
?>
');
}

function edit(id)
{
	go('<?php
echo $_obj['EDIT_URL'];
?>
&id=' + id);
}

function view(id)
{
	go('<?php
echo $_obj['VIEW_URL'];
?>
&id=' + id);
}

function move(id, rank, up)
{
	go('<?php
echo $_obj['CUR_URL'];
?>
&mo=move&id=' + id + '&rank=' + rank + '&up=' + up);
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
</head>
<body background="/images/admin/bg.gif" text="#333333" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="MM_preloadImages('/images/admin/top2.gif')"><a name="top"></a>
<table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr> 
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="133" background="/images/admin/bg_t2.gif"><img src="/images/admin/spacer.gif" height="1"></td>
        <td><img src="/images/admin/bg_t.gif" width="76" height="45"></td>
        <td align="right" valign="bottom"><img src="/images/admin/logo_cus.gif"></td>
      </tr>
    </table></td>
  </tr>
	<tr id="WMSPlate" style="display: <?php
echo $_obj['WMSdisplay'];
?>
">
    <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="17%" valign="top" bgcolor="#000000"><table width="133" height="123" border="0" cellpadding="0" cellspacing="0">

          <tr>
            <td width="148" height="10"><img src="/images/admin/spacer.gif" width="10" height="11"></td>
          </tr>
          <tr>
            <td height="1" background="/images/admin/spacer02.gif"><img src="/images/admin/clear.gif" width="1" height="1" border="0"></td>
          </tr>
          <tr>
            <td height="27" align="center"><a href="../index.php" target="_blank"><img src="/images/admin/menu_s.gif" width="71" height="21" border="0"></a></td>
          </tr>
          <tr>
            <td height="1" background="/images/admin/spacer02.gif"><img src="/images/admin/clear.gif" width="1" height="1" border="0"></td>
          </tr>
          <tr>
            <td height="27" align="center"><a href="index.php?fn=logout"><img src="/images/admin/menu_s02.gif" width="71" height="21" border="0"></a></td>
          </tr>
          <tr>
            <td height="1" background="/images/admin/spacer02.gif"><img src="/images/admin/clear.gif" width="1" height="1" border="0"></td>
          </tr>
          <tr>
            <td height="27" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td height="1" background="/images/admin/spacer02.gif"><img src="/images/admin/clear.gif" width="1" height="1" border="0"></td>
          </tr>
          <tr>
            <td height="27" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td height="1" background="/images/admin/spacer02.gif"><img src="/images/admin/clear.gif" width="1" height="1" border="0"></td>
          </tr>

        </table></td>
        <td width="83%"><img src="/images/admin/main3.gif" height="157" border="0"></td>
      </tr>
    </table></td>
  </tr>
  <tr align="left" valign="top">
    <td height="22" align="right" background="/images/admin/spacer03.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="146" height="19" align="center" valign="top" background="/images/admin/leftpic.gif"><div align="center"><a id="plateHide" href="javascript:showPlate(false);" style="display: <?php
echo $_obj['WMSdisplay'];
?>
;" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ImageXX','','/images/admin/top2.gif',1)"><img src="/images/admin/top.gif" name="ImageXX" border="0"></a><a id="plateShow" href="javascript:showPlate(true);" style="display:<?php
echo $_obj['WMSdisplay2'];
?>
;" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ImageXY','','/images/admin/toplook2.gif',1)"><img src="/images/admin/toplook.gif" name="ImageXY" border="0"></a></div><iframe name="iplate" src="index.php?fn=blank" id="iplate" style="display:none;"></iframe></td>
              <td align="right" valign="baseline" class="f09">登入身份：<?php
echo $_obj['admin_name'];
?>
</td>
              <td width="10">&nbsp;</td>
            </tr>
          </table>
          </div></td>
        </tr>
    </table></td>
  </tr>
	<tr> 
    <td align="left" valign="top"> <table width="750" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="131" align="center" valign="top"> <table width="131" border="0" cellspacing="0" cellpadding="1">
				<?php
if (!empty($_obj['menu_list'])){
if (!is_array($_obj['menu_list']))
$_obj['menu_list']=array(array('menu_list'=>$_obj['menu_list']));
$_tmp_arr_keys=array_keys($_obj['menu_list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['menu_list']=array(0=>$_obj['menu_list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['menu_list'] as $rowcnt=>$menu_list) {
$menu_list['ROWCNT']=$rowcnt;
$menu_list['ALTROW']=$rowcnt%2;
$menu_list['ROWBIT']=$rowcnt%2;
$_obj=&$menu_list;
?>
              <tr> 
                <td colspan="2"><font color="#006699"><img src="/images/admin/menu_t<?php
echo $_obj['i'];
?>
.gif" width="26" height="16"><?php
echo $_obj['item_name'];
?>
</font></td>
              </tr>
              <tr> 
                <td width="10">&nbsp;</td>
                <td width="120">
								<table cellpadding="0" cellspacing="0">
					<?php
if (!empty($_obj['list'])){
if (!is_array($_obj['list']))
$_obj['list']=array(array('list'=>$_obj['list']));
$_tmp_arr_keys=array_keys($_obj['list']);
if ($_tmp_arr_keys[0]!='0')
$_obj['list']=array(0=>$_obj['list']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['list'] as $rowcnt=>$list) {
$list['ROWCNT']=$rowcnt;
$list['ALTROW']=$rowcnt%2;
$list['ROWBIT']=$rowcnt%2;
$_obj=&$list;
?>
								<tr><td><img src="/images/admin/dot.gif" width="10" height="9"> <a href="/admin/index.php?fn=<?php
echo $_obj['item_code'];
?>
"><?php
echo $_obj['item_name'];
?>
</a></td></tr>
					<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
								</table>
								</td>
              </tr>
						<tr><td colspan="2" height="10"></td></tr>
				<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
            </table>
            <p><a href="/admin/index.php?fn=logout"><img src="/images/admin/logout.gif" width="51" height="50" border="0"></a><br>
              <br>
							<span id="logout" style="color:#FF0000"></span><br><br>
            </p></td>
          <td align="left" valign="top" background="/images/admin/tr2.gif"> <br> <br> 
            <img src="/images/admin/spacer.gif" width="1" height="1"> </td>
          <td width="618" align="left" valign="top" bgcolor="#F5FAFA"><br> <table width="605" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td nowrap><font color="#0099CC"><strong><?php
echo $_obj['first_name'];
?>
 > <?php
echo $_obj['menu_name'];
?>
 :</strong></font></td>
              	<td align="right" nowrap><!-- InstanceBeginEditable name="Func" --><!-- InstanceEndEditable --></td>
              </tr>
              <tr>
              	<td colspan="2">
<?php
if (!empty($_obj['menu_memo'])){
?>
								<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td width="12">&nbsp;</td><td><?php
echo $_obj['menu_memo'];
?>
</td></tr></table>
<?php
}
?>	
								</td>
             	</tr>
              <tr> 
                <td colspan="2" background="/images/admin/tr1.gif"><img src="/images/admin/spacer.gif" width="1" height="1"></td>
              </tr>
							<tr><td colspan="2"><br><!-- InstanceBeginEditable name="Other2" --><!-- InstanceEndEditable --></td>
							</tr>
              <tr> 
                <td colspan="2" align="center">
								<?php
if (!empty($_obj['page_control'])){
?>
									<table width="100%" border="0" cellspacing="0" cellpadding="2" height="0" align="center" class="f08">
                    <tr> 
                      <td width="10%" nowrap><?php
echo $_obj['total_item'];
?>
 item(s) found</td>
                      <td>&nbsp;&nbsp;&nbsp;&nbsp;<font color="#DD8800">Page </font><?php
echo $_obj['page_control'];
?>
</td><td align="right"><!-- InstanceBeginEditable name="Other" -->&nbsp;<!-- InstanceEndEditable --></td>
                    </tr>
                  </table>
									<?php
}
?><table cellpadding="0" cellspacing="0" height="300" width="100%"><tr><td valign="top" align="center">
                  <!-- InstanceBeginEditable name="Content" --><!-- InstanceEndEditable --></td></tr></table>
                  <p>&nbsp;</p>
<div align="right"><a href="#top"><img src="/images/admin/up.gif" width="21" height="20" border="0"></a>&nbsp;</div>									</td>
              </tr>
            </table>
					</td>
        </tr>
      </table>
		</td>
  </tr>
  <tr> 
    <td bgcolor="#999999"><img src="/images/admin/clear.gif" width="1" height="1" border="0"></td>
  </tr>
  <tr> 
    <td bgcolor="#333333" class="f08"><font color="#CCCCCC"><img src="/images/admin/clear.gif" width="1" height="23" border="0"></font></td>
  </tr>
</table>
</body>
<!-- InstanceBeginEditable name="tail" --><!-- InstanceEndEditable -->
<!-- InstanceEnd --></html>

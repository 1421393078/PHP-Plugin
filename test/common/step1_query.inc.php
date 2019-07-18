<?php
/*-------------------------------------------------------------------------------------
 Script Name: step1_query.inc.php
 Script Version: 1.0
 Author: Tony Wei (魏志國) tonywei123@gmail.com
 Description: Step1 資料查詢
 Revision History:
   1.0: original version 2008/1/7

-------------------------------------------------------------------------------------*/

// 取得執行的實體路徑
require_once( dirname( __FILE__ ) . "/../ccioo/Data/class.DB.php" );
require_once( dirname( __FILE__ ) . "/functions.php" );
require_once( dirname( __FILE__ ) . "/datapager.php" );


//-------------------------------------------------------------------------------------
// 資料查詢
//-------------------------------------------------------------------------------------
function PageQuery( $sFunction )
{
	global $_WEB, $myQuery; // global $myQuery for debug use only

	$myDB = new DB();	// 建立資料庫連結


	//-------------------------------------------------------------------------------------
	// 主程式
	//-------------------------------------------------------------------------------------
	switch ( $sFunction ) // 以下依據開頭英文字母排序
	{

		//-------------------------------------------------------------------------------------
		// 新進產品詳細列表
		// 產品搜尋
		//-------------------------------------------------------------------------------------
		case "newproduct":
		case "search":

			$condition = "";
			$k = "";
			if ( $sFunction == "newproduct" ) {
				$condition = " AND data1.newin = 1";
			} else {
				$k = Avoid_Injection( $_GET["keyword"] );
				if ( $k == "" ) {
					Redirect( "/" );
				}
				$condition = " AND ( ( item_no LIKE '%$k%' OR spec1 LIKE '%$k%' OR spec2 LIKE '%$k%' OR spec3 LIKE '%$k%' OR spec4 LIKE '%$k%' OR spec5 LIKE '%$k%' OR category1.name = '$k' )";

				//-------------------------------------------------------------------------------------
				// 詮康客戶使用search後有個想法,  當打"vx-160"時可以秀出"vx-160"的相關產品, 但是否打"vx160"時也可以出現vx-160的結果?因為使用者可能不打 "-" 符號?
				// Added by Tony Wei 2008/3/24
				$iIndex = 99;
				for ( $i=0 ; $i <=9  ; $i++ )
				{
					$ii = strpos( $k, ( "" . $i ) );
					if ( $ii > 0 ) {
						$iIndex = ( $iIndex > $ii ) ? $ii : $iIndex;
					}
				}
				if ( $iIndex != 99 ) {
					$k = substr( $k, 0, $iIndex ) . "-" . substr( $k, $iIndex );
					$condition .= " OR ( item_no LIKE '%$k%' OR spec1 LIKE '%$k%' OR spec2 LIKE '%$k%' OR spec3 LIKE '%$k%' OR spec4 LIKE '%$k%' OR spec5 LIKE '%$k%' OR category1.name = '$k' ) )";
				} else {
					$condition .= " )";
				}
				//-------------------------------------------------------------------------------------
			}

			// 取出第一層分類
			$myQuery = "SELECT id, name FROM category1 WHERE parent = 0 and hide = 0 ORDER BY rank";
			$result = $myDB->ExecuteReader( $myQuery );
			$_WEB["menu2"] = "";
			$i = 1;
			while ( $row = mysql_fetch_assoc( $result ) ) {
                $_WEB['jsData']['type2'][] = $row; // 2019年5月15日12:01:21 添加
				$sTemp = "<td width='20' class='brand'><img src='images/dot2.gif' height='6' border='0'></td><td width='50%' class='brand'><a href='index.php?fn=product&cno=" . $row["id"] . "'>" . $row["name"] . "</a></td>";
				if ( $i == 1 ) {
					$i = 2;
					$_WEB["menu2"] .= "<tr>";
					$_WEB["menu2"] .= $sTemp;
				} else {
					$i = 1;
					$_WEB["menu2"] .= $sTemp;
					$_WEB["menu2"] .= "</tr>";
				}
			}
			if ( $i == 2 ) {
					$_WEB["menu2"] .= "<td width='20'>&nbsp;</td><td width='50%'>&nbsp;</td></tr>";
			}
	
			// 取出產品明細
			$myQuery = "SELECT item_no, spec1, spec2, spec3, spec4, spec5, data1.thumb, file, category1.parent FROM data1 "
								   . "LEFT JOIN category1 ON cat_id = category1.id "
								   . "WHERE data1.hide = 0 AND category1.hide = 0 " . $condition . " ORDER BY category1.rank, data1.rank";
			$dp = new datapager( $myDB->myConnection, $myQuery );

			// 取得目前頁數
			$page = isset( $_GET["p"] ) ? $_GET["p"] : 1;
			$pagesize = 10;
			$result      = $dp->execute( $page, $pagesize );
            
			$_WEB["table"]  = "";
			$_WEB["pager"] = "";
			if ( mysql_affected_rows( $dp->connection ) > 0 ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
                    $_WEB['jsData']['product'][] = $row; // 2019年5月15日12:01:21 添加
					// 不同分類，不同的表格欄位
					switch ( $row["parent"] ) {

						case 1:
							$VARS[ "spec1_name" ] = "Scanner Model";
							$VARS[ "spec2_name" ] = "OEM Reference";
							$VARS[ "spec3_name" ] = "Chemistry";
							$VARS[ "spec4_name" ] = "Voltage";
							$VARS[ "spec5_name" ] = "Capacity";
							break;

						case 2:
							$VARS[ "spec1_name" ] = "Radio Model";
							$VARS[ "spec2_name" ] = "OEM Reference";
							$VARS[ "spec3_name" ] = "Chemistry";
							$VARS[ "spec4_name" ] = "Voltage";
							$VARS[ "spec5_name" ] = "Capacity";
							break;

						case 3:
							$VARS[ "spec1_name" ] = "Equipment";
							$VARS[ "spec2_name" ] = "OEM Reference";
							$VARS[ "spec3_name" ] = "Chemistry";
							$VARS[ "spec4_name" ] = "Voltage";
							$VARS[ "spec5_name" ] = "Capacity";
							break;

						case 4:
							$VARS[ "spec1_name" ] = "Equipment";
							$VARS[ "spec2_name" ] = "Battery Model";
							$VARS[ "spec3_name" ] = "Chemistry";
							$VARS[ "spec4_name" ] = "Input Voltage";
							$VARS[ "spec5_name" ] = "Charge Current";
							break;
					}
					
					$_WEB["table"]  .= "<tr><td align='center' class='arial_15'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td><table width='100%' border='0' cellspacing='2' cellpadding='0'><tr><td width='7'><img src='images/arrow2.gif' width='7' height='5' border='0'> </td><td class='pro_name'>". $row["item_no"] . "</td><td align='right' class='download'>" . ( $row["file"] == "" ? "" : "<a href='" . $row["file"] . "' target='_blank'>&gt; download</a>" ) . "</td></tr></table></td></tr><tr><td><table width='100%' height='100%' border='0' cellpadding='0' cellspacing='1'><tr class='spc_name'><td rowspan='2' bgcolor='#E6E7E8'><img src='". $row["thumb"] . "' width='110' height='110' hspace='2' vspace='2'></td><td valign='top' bgcolor='#58595B' class='spc_name' width='50%'>" . $VARS[ "spec1_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name' nowrap>" . $VARS[ "spec2_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name'>" . $VARS[ "spec3_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name'>" . $VARS[ "spec4_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name'>" . $VARS[ "spec5_name" ] . "</td></tr><tr><td bgcolor='#E6E7E8' class='spc_content'>". $row["spec1"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec2"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec3"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec4"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec5"] . "</td></tr></table></td></tr></table></td></tr><tr><td align='center' class='arial_15'>&nbsp;</td></tr>";
				}
                
				$_WEB["pager"] = "<tr>" . $dp->prevpage("<td class='page'><a href='javascript:jump_get(%page%);'>&lt;Back</a></td>") . $dp->nextpage("<td class='page'><a href='javascript:jump_get(%page%);'>Next&gt; </a></td>") . "<td class='page_noneline'>Total </td><td class='page_numbers'>" . $dp->pagecount . "</td><td class='page_noneline'>Page(s)</td><td>&nbsp;</td><td class='page_noneline'>Go To Page</td><td><form><select name='menu1' size='1' onChange='jump_get( this.options[ this.selectedIndex ].text );'>";
				for ( $i=1 ; $i<= $dp->pagecount ; $i++ ) {
					$_WEB["pager"] .= "<option" . ( ( $i == $page ) ? " selected" : "" ) . ">" . $i . "</option>";
				}
				$_WEB["pager"] .= "</select></form></td></tr>";
			} else {
				$_WEB["table"] = "<tr><td align='center' class='pro_name'><br><br>We did not find results for: " . $k . "</td></tr>";
			}
			break;


		//-------------------------------------------------------------------------------------
		// 產品分類列表
		//-------------------------------------------------------------------------------------
		case "product":

			$cno = isset( $_GET["cno"] ) ? intval( $_GET["cno"] ) : 1;

			// 取出第一層分類
			$myQuery = "SELECT id, name FROM category1 WHERE parent = 0 and hide = 0 ORDER BY rank";
			$result = $myDB->ExecuteReader( $myQuery );
			$_WEB["menu"]   = "";
			$_WEB["menu2"] = "";
			$_WEB["cat_name"] = "";
			$i = 1;
			while ( $row = mysql_fetch_assoc( $result ) ) {
                $_WEB['jsData']['type'][] = $row; // 2019年5月15日12:04:59 添加
                $_WEB['jsData']['type2'][] = $row; // 2019年5月15日12:04:59 添加

				if ( $row["id"] == $cno ) {
					$_WEB["cat_name"] = "<a href='index.php?fn=product&cno=" . $row["id"] . "' class='sub_title'>" . $row["name"] . "</a>";
				}
				$_WEB["menu"] .= "<tr><td valign='top' class='arrow'><img src='images/arrow_white.gif' width='3' height='5' /></td><td class='choice'><a href='index.php?fn=product&cno=" . $row["id"] . "'>" . $row["name"] . "</a></td></tr>";
				$sTemp = "<td width='20' class='brand'><img src='images/dot2.gif' height='6' border='0'></td><td width='50%' class='brand'><a href='index.php?fn=product&cno=" . $row["id"] . "'>" . $row["name"] . "</a></td>";
				if ( $i == 1 ) {
					$i = 2;
					$_WEB["menu2"] .= "<tr>";
					$_WEB["menu2"] .= $sTemp;
				} else {
					$i = 1;
					$_WEB["menu2"] .= $sTemp;
					$_WEB["menu2"] .= "</tr>";
				}
			}
			if ( $i == 2 ) {
					$_WEB["menu2"] .= "<td width='20'>&nbsp;</td><td width='50%'>&nbsp;</td></tr>";
			}
	
			// 取出第二層分類	
			$myQuery = "SELECT id, name, thumb FROM category1 WHERE parent = " . $cno . " and hide = 0 ORDER BY rank";
			$dp = new datapager( $myDB->myConnection, $myQuery );

			// 取得目前頁數
			$page = isset( $_GET["p"] ) ? $_GET["p"] : 1;
			$pagesize = 12;
			$result      = $dp->execute( $page, $pagesize );

			$_WEB["table"]  = "";
			$_WEB["pager"] = "";
			$tr1 = "";
			$tr2 = "";
			$i = 1;
			if ( mysql_affected_rows( $dp->connection ) > 0 ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
                    $_WEB['jsData']['product'][] = $row; // 2019年5月15日12:01:21 添加

					if ( $i == 1 ) {
						$_WEB["table"] .= $tr1 . $tr2 . "<tr><td colspan='5'><img src='images/10x10.gif' width='10' height='10'></td></tr>\n";
						$tr1 = "<tr>\n";
						$tr2 = "<tr>\n";
					}
					$tr1 .= "<td align='center'><a href='index.php?fn=product_detail&cno=" . $row["id"] . "'><img src='" . $row["thumb"] . "' width='146' height='120' border='0'></a></td>\n";
					$tr2 .= "<td align='center' class='brand'><img src='images/dot.gif' width='9'><a href='index.php?fn=product_detail&cno=" . $row["id"] . "'>" . $row["name"] . "</a></td>\n";
					if ( $i != 3 ) {
						$tr1 .= "<td align='center'>&nbsp;</td>\n";
						$tr2 .= "<td align='center'>&nbsp;</td>\n";
					}
					if ( $i == 3 ) {
						$tr1 .= "</tr>\n";
						$tr2 .= "</tr>\n";
						$i = 0;
					}
					$i++;
				}
				if ( $i != 1 ) {
					while ( $i < 4 ) {
						$tr1 .= "<td><img src='images/10x10.gif' width='146' height='120'></td>\n";
						$tr2 .= "<td>&nbsp;</td>\n";
						if ( $i != 3 ) {
							$tr1 .= "<td align='center'>&nbsp;</td>\n";
							$tr2 .= "<td align='center'>&nbsp;</td>\n";
						}
						$i++;
					}
					$tr1 .= "</tr>\n";
					$tr2 .= "</tr>\n";
				}
				$_WEB["table"] .= $tr1 . $tr2;
				$_WEB["pager"] = "<tr>" . $dp->prevpage("<td class='page'><a href='javascript:jump_get(%page%);'>&lt;Back</a></td>") . $dp->nextpage("<td class='page'><a href='javascript:jump_get(%page%);'>Next&gt; </a></td>") . "<td class='page_noneline'>Total </td><td class='page_numbers'>" . $dp->pagecount . "</td><td class='page_noneline'>Page(s)</td><td>&nbsp;</td><td class='page_noneline'>Go To Page</td><td><form><select name='menu1' size='1' onChange='jump_get( this.options[ this.selectedIndex ].text );'>";
				for ( $i=1 ; $i<= $dp->pagecount ; $i++ ) {
					$_WEB["pager"] .= "<option" . ( ( $i == $page ) ? " selected" : "" ) . ">" . $i . "</option>";
				}
				$_WEB["pager"] .= "</select></form></td></tr>";
			}
			break;


		//-------------------------------------------------------------------------------------
		// 產品詳細列表
		//-------------------------------------------------------------------------------------
		case "product_detail":

			if ( ! isset( $_GET["cno"] ) ) {
				exit;
			}
			$cno = isset( $_GET["cno"] ) ? intval( $_GET["cno"] ) : 5;

			// 取出上一層的代碼
			$myQuery = "SELECT parent FROM category1 WHERE id = " . $cno;
			$parent = $myDB->ExecuteScalar( $myQuery );

			// 取出第一層分類
			$myQuery = "SELECT id, name FROM category1 WHERE parent = 0 and hide = 0 ORDER BY rank";
			$result = $myDB->ExecuteReader( $myQuery );
			$_WEB["menu"] = "";
			$_WEB["cat_name1"] = "";
			while ( $row = mysql_fetch_assoc( $result ) ) {
                $_WEB['jsData']['type'][] = $row; // 2019年5月15日12:04:59 添加
				if ( $row["id"] == $parent ) {
					$_WEB["cat_name1"] = "<a href='index.php?fn=product&cno=" . $row["id"] . "' class='sub_title'>" . $row["name"] . "</a>";
				}
				$_WEB["menu"] .= "<tr><td valign='top' class='arrow'><img src='images/arrow_white.gif' width='3' height='5' /></td><td class='choice'><a href='index.php?fn=product&cno=" . $row["id"] . "'>" . $row["name"] . "</a></td></tr>";
			}

			// 不同分類，不同的表格欄位
			switch ( $parent ) {

				case 1:
					$VARS[ "spec1_name" ] = "Scanner Model";
					$VARS[ "spec2_name" ] = "OEM Reference";
					$VARS[ "spec3_name" ] = "Chemistry";
					$VARS[ "spec4_name" ] = "Voltage";
					$VARS[ "spec5_name" ] = "Capacity";
					break;

				case 2:
					$VARS[ "spec1_name" ] = "Radio Model";
					$VARS[ "spec2_name" ] = "OEM Reference";
					$VARS[ "spec3_name" ] = "Chemistry";
					$VARS[ "spec4_name" ] = "Voltage";
					$VARS[ "spec5_name" ] = "Capacity";
					break;

				case 3:
					$VARS[ "spec1_name" ] = "Equipment";
					$VARS[ "spec2_name" ] = "OEM Reference";
					$VARS[ "spec3_name" ] = "Chemistry";
					$VARS[ "spec4_name" ] = "Voltage";
					$VARS[ "spec5_name" ] = "Capacity";
					break;

				case 4:
					$VARS[ "spec1_name" ] = "Equipment";
					$VARS[ "spec2_name" ] = "Battery Model";
					$VARS[ "spec3_name" ] = "Chemistry";
					$VARS[ "spec4_name" ] = "Input Voltage";
					$VARS[ "spec5_name" ] = "Charge Current";
					break;
			}

			// 取出第二層分類
			$myQuery = "SELECT id, name FROM category1 WHERE parent = " . $parent . " and hide = 0 ORDER BY rank";
			$result = $myDB->ExecuteReader( $myQuery );
			$_WEB["menu2"] = "";
			$_WEB["cat_name2"] = "";
			$i = 1;
			while ( $row = mysql_fetch_assoc( $result ) ) {
                $_WEB['jsData']['type2'][] = $row; // 2019年5月15日12:01:21 添加

				if ( $row["id"] == $cno ) {
					$_WEB["cat_name2"] = "<a href='index.php?fn=product_detail&cno=" . $row["id"] . "' class='sub_title'>" . $row["name"] . "</a>";
				}
				$sTemp = "<td width='20' class='brand'><img src='images/dot2.gif' height='6' border='0'></td><td width='50%' class='brand'><a href='index.php?fn=product_detail&cno=" . $row["id"] . "'>" . $row["name"] . "</a></td>";
				if ( $i == 1 ) {
					$i = 2;
					$_WEB["menu2"] .= "<tr>";
					$_WEB["menu2"] .= $sTemp;
				} else {
					$i = 1;
					$_WEB["menu2"] .= $sTemp;
					$_WEB["menu2"] .= "</tr>";
				}
			}
			if ( $i == 2 ) {
					$_WEB["menu2"] .= "<td width='20'>&nbsp;</td><td width='50%'>&nbsp;</td></tr>";
			}
	

			// 取出產品明細
			$myQuery = "SELECT id, item_no, spec1, spec2, spec3, spec4, spec5, thumb, file FROM data1 WHERE cat_id = " . $cno . " and hide = 0 ORDER BY rank";
			$dp = new datapager( $myDB->myConnection, $myQuery );

			// 取得目前頁數
			$page = isset( $_GET["p"] ) ? $_GET["p"] : 1;
			$pagesize = 10;
			$result      = $dp->execute( $page, $pagesize );

			$_WEB["table"]  = "";
			$_WEB["pager"] = "";
			if ( mysql_affected_rows( $dp->connection ) > 0 ) {
				while ( $row = mysql_fetch_assoc( $result ) ) {
                    $_WEB['jsData']['product'][] = $row; // 2019年5月15日12:01:21 添加

					$_WEB["table"]  .= "<tr><td align='center' class='arial_15'><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr><td><table width='100%' border='0' cellspacing='2' cellpadding='0'><tr><td width='7'><img src='images/arrow2.gif' width='7' height='5' border='0'> </td><td class='pro_name'>". $row["item_no"] . "</td><td align='right' class='download'>" . ( $row["file"] == "" ? "" : "<a href='" . $row["file"] . "' target='_blank'>&gt; download</a>" ) . "</td></tr></table></td></tr><tr><td><table width='100%' height='100%' border='0' cellpadding='0' cellspacing='1'><tr class='spc_name'><td rowspan='2' bgcolor='#E6E7E8'><img src='". $row["thumb"] . "' width='110' height='110' hspace='2' vspace='2'></td><td valign='top' bgcolor='#58595B' class='spc_name' width='50%'>" . $VARS[ "spec1_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name' nowrap>" . $VARS[ "spec2_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name'>" . $VARS[ "spec3_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name'>" . $VARS[ "spec4_name" ] . "</td><td valign='top' bgcolor='#58595B' class='spc_name'>" . $VARS[ "spec5_name" ] . "</td></tr><tr><td bgcolor='#E6E7E8' class='spc_content'>". $row["spec1"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec2"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec3"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec4"] . "</td><td align='center' bgcolor='#E6E7E8' class='spc_content'>". $row["spec5"] . "</td></tr></table></td></tr></table></td></tr><tr><td align='center' class='arial_15'>&nbsp;</td></tr>";
				}
				$_WEB["pager"] = "<tr>" . $dp->prevpage("<td class='page'><a href='javascript:jump_get(%page%);'>&lt;Back</a></td>") . $dp->nextpage("<td class='page'><a href='javascript:jump_get(%page%);'>Next&gt; </a></td>") . "<td class='page_noneline'>Total </td><td class='page_numbers'>" . $dp->pagecount . "</td><td class='page_noneline'>Page(s)</td><td>&nbsp;</td><td class='page_noneline'>Go To Page</td><td><form><select name='menu1' size='1' onChange='jump_get( this.options[ this.selectedIndex ].text );'>";
				for ( $i=1 ; $i<= $dp->pagecount ; $i++ ) {
					$_WEB["pager"] .= "<option" . ( ( $i == $page ) ? " selected" : "" ) . ">" . $i . "</option>";
				}
				$_WEB["pager"] .= "</select></form></td></tr>";
			}
			break;


	} // End of switch ( $sFunction )
    
    // 添加时间 2019年5月15日14:39:03
    if( isset($dp) && gettype($dp) == 'object' ){
        $_WEB['jsData']['page']['page'] = $dp->page;
        $_WEB['jsData']['page']['pagesize'] = $dp->pagesize;
        $_WEB['jsData']['page']['recordcount'] = $dp->recordcount;
        $_WEB['jsData']['page']['pagecount'] = $dp->pagecount;
    }

    if(isset($VARS))$_WEB['jsData']['vars'] = $VARS;


} // End of function PageQuery( $sFunction )


?>
<?php
/*-------------------------------------------------------------------------------------
 Script Name: step3_parse.inc.php
 Script Version: 1.0
 Author: Tony Wei (魏志國) tonywei123@gmail.com
 Description: Step3 輸出網頁
 Revision History:
   1.0: original version 2008/1/7

-------------------------------------------------------------------------------------*/

// 取得執行的實體路徑
require_once( dirname( __FILE__ ) . "/functions.php" );


//-------------------------------------------------------------------------------------
// 資料查詢
//-------------------------------------------------------------------------------------
function ParseHtml( $sFunction )
{
	global $_WEB;

	$filename = ( $sFunction == "" ) ? dirname( __FILE__ ) . "/../template/index.html" : dirname( __FILE__ ) . "/../template/" . $sFunction . ".html";
	if ( ! is_file( $filename ) ) { // 沒有此樣板檔
		$filename = dirname( __FILE__ ) . "/../template/404.html";
	}

	// 取出樣板檔內容
	$handle = fopen( $filename, "r" );
	$contents = fread( $handle, filesize( $filename ) );
	fclose( $handle );

	// 置換網頁元素
	$patterns = NULL;
	$replacements = NULL;

	// 整體轉換 -- 前置處理
	$patterns[0] = "/<meta http-equiv=['\"]content-type['\"][^>]+>[\r\n]*/i";	// charset
	$replacements[0] = '';
                        
    // 手机版公共部分
    // 头部
    $patterns[1001] = "/<!-- MOBILE_HEADER -->.+<!-- \/MOBILE_HEADER -->/i";
    $replacements[1001] = '
            <div class="layui-row mobile" >
                <div style="position: relative;" class="header">
                    <div class="top">
                        <div class="logo">
                            <a href="/">
                                <img src="images/index_top_01.png" />
                            </a>
                        </div>
                        <div class="top-more">
                            <img src="images/top-more.png" onclick="$(\'#xs-mainnav\').toggleClass(\'show\');$(\'#mainnav-background-layer\').toggleClass(\'show\')" />
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="mainnav-background-layer"  onclick="$(\'#xs-mainnav\').toggleClass(\'show\');$(\'#mainnav-background-layer\').toggleClass(\'show\')"></div>
                    <div class="mainnav" id="xs-mainnav">
                        <div class="navbg" style="">
                            <img src="/images/navbg.png">
                        </div>
                        <div class="layui-row">
                            <div class="layui-col-xs12 sidebar">
                                <ul>
                                    <li>
                                        <p>
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="/">Home</a>
                                        </p>
                                    </li>
                                    
                                    <li>
                                        <p>
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="newproduct.html">New Product</a>
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="product.html">Product</a>
                                        </p>
                                        <p class="type"></p>
                                    </li>
                                    <li>
                                        <p>
                            
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="quality.html">Quality Info</a>
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="warranty.html">Warranty</a>
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="download.html">Download</a>
                                        </p>
                            
                                    </li>
                                    <li>
                                        <p>
                                            <img src="images/arrow_white.gif" width="3" height="5" />
                                            <a href="contactus.html">Contact Us</a>
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="layui-row">
                <div class="layui-col-xs12">
                    <img src="/images/mobile_banner.png" style="max-width : 100%">
                </div>
            </div>
    
            
            <div class="layui-row search">
                <div class="layui-col-xs12 form-box tc">
                    <img src="../images/search.gif" width="70" height="27">
                    <form name="form1" onsubmit="go_search_mobile();return false;">
                        <input type="text" id="mobile_keyword" size="16" maxlength="30">
                    </form>
                    <a href="javascript:go_search_mobile();"><img  src="../images/go.gif" border="0"></a>
                </div>
            </div>';
                        
    // 尾部
    $patterns[1002] = "/<!-- MOBILE_FOOTER -->.+<!-- \/MOBILE_FOOTER -->/i";
    $replacements[1002] = ' <div class="mobile layui-row">
            <div class="footer">
                Friendship Links : <br/>
                &nbsp<a href="http://www.onwellaudio.com">www.onwellaudio.com</a>
                &nbsp<a href="http://www.onwell.cn">www.onwell.cn</a>
            </div>
        </div>';

        // var_dump($_WEB['jsData']);
        // return ;
    // json 数据
    $patterns[1003] = "/<!-- JSON_DATA -->.+<!-- \/JSON_DATA -->/i";
    $replacements[1003] = ' <script> var jsonData ='.json_encode($_WEB['jsData']).'</script>';
    
    // Pc 友情链接
    $patterns[1004] = "/<!-- FRIENDSHIP LINKS -->.+<!-- \/FRIENDSHIP LINKS -->/i";
    $replacements[1004] = ' <tr>
                <td style="background-color: #7c0000;padding: 5px 0;text-align: center;color: #f0e9e9;font-size:12px">
                    Friendship Links :
                    <a href="http://www.onwellaudio.com" style="color:#f0e9e9">www.onwellaudio.com</a>
                    <a href="http://www.onwell.cn" style="color:#f0e9e9">www.onwell.cn</a>
                </td>
            </tr>';

	// 路徑轉換
	$patterns[21] = "/\"index\.html{0,1}\"/i";						// 首頁 index.html 改為 index.php
	$patterns[22] = "/([\"'])([^\"':]+)\.html{0,1}(\#{0,1}.*)([\"'])/i";		// xxx.htm 與 xxx.html 一律改為 index.php?fn=xxx
	$replacements[21] = "\"index.php\"";
	$replacements[22] = "\\1index.php?fn=\\2\\3\\4";

	// 整體轉換 -- 後置處理
	$patterns[900] = "/<\/head>/i";	// 插入正確的 charset
	$replacements[900] = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n"
                        . "<script type=\"text/javascript\" src=\"/js/jquery-1.8.3.min.js\"></script>\n"
                        . "<script type=\"text/javascript\" src=\"/common/js/functions.js\"></script>\n"
                        . "<script type=\"text/javascript\" src=\"/common/js/querystring.js\"></script>\n"
                        . '<meta name="renderer" content="webkit">
                            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
                            <meta name="apple-mobile-web-app-status-bar-style" content="black">
                            <meta name="apple-mobile-web-app-capable" content="yes">
                            <meta name="format-detection" content="telephone=no">

                            <link rel="stylesheet" type="text/css" href="/css/common.css" />
                            <link rel="stylesheet" type="text/css" href="/js/layui/css/layui.css" />

                            <!--[if lt IE 9]>
                                <link href="/Public/css/ie8.css" rel="stylesheet" type="text/css" />
                                <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
                                <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
                            <![endif]-->';
	$patterns[901] = "/(<body[^>]*>[\r\n]*)/i";
	$replacements[901] = "\\1<a name=\"top\"></a>\n";
	
    
    
    
	$contents = preg_replace( $patterns, $replacements, $contents );

	// 清除
	$patterns = NULL;
	$replacements = NULL;

	switch ( $sFunction ) // 以下依據開頭英文字母排序
	{

		//-------------------------------------------------------------------------------------
		// 新進產品詳細列表
		// 產品搜尋
		//-------------------------------------------------------------------------------------
		case "newproduct":
		case "search":
			$patterns[0] = "/<!-- MENU2 -->.+<!-- \/MENU2 -->/i";
			$patterns[1] = "/<!-- TABLE -->.+<!-- \/TABLE -->/i";
			$patterns[2] = "/<!-- PAGER -->.+<!-- \/PAGER -->/i";
			$replacements[0] = $_WEB["menu2"];
			$replacements[1] = $_WEB["table"];
			$replacements[2] = $_WEB["pager"];
			$contents = str_replace( "\n", "**nl**",   $contents ); // 暫時移除 new line
			$contents = preg_replace( $patterns, $replacements, $contents );
			$contents = str_replace( "**nl**", "\n",   $contents ); // 加回 new line
			break;

		//-------------------------------------------------------------------------------------
		// 產品分類列表
		//-------------------------------------------------------------------------------------
		case "product":
			$contents = str_replace( "{cat_name}", $_WEB["cat_name"], $contents );
			$patterns[0] = "/<!-- MENU -->.+<!-- \/MENU -->/i";
			$patterns[1] = "/<!-- MENU2 -->.+<!-- \/MENU2 -->/i";
			$patterns[2] = "/<!-- TABLE -->.+<!-- \/TABLE -->/i";
			$patterns[3] = "/<!-- PAGER -->.+<!-- \/PAGER -->/i";
			$replacements[0] = $_WEB["menu"];
			$replacements[1] = $_WEB["menu2"];
			$replacements[2] = $_WEB["table"];
			$replacements[3] = $_WEB["pager"];
			$contents = str_replace( "\n", "**nl**",   $contents ); // 暫時移除 new line
			$contents = preg_replace( $patterns, $replacements, $contents );
			$contents = str_replace( "**nl**", "\n",   $contents ); // 加回 new line
			break;

		//-------------------------------------------------------------------------------------
		// 產品詳細列表
		//-------------------------------------------------------------------------------------
		case "product_detail":
			$contents = str_replace( "{cat_name1}", $_WEB["cat_name1"], $contents );
			$contents = str_replace( "{cat_name2}", $_WEB["cat_name2"], $contents );
			$patterns[0] = "/<!-- MENU -->.+<!-- \/MENU -->/i";
			$patterns[1] = "/<!-- MENU2 -->.+<!-- \/MENU2 -->/i";
			$patterns[2] = "/<!-- TABLE -->.+<!-- \/TABLE -->/i";
			$patterns[3] = "/<!-- PAGER -->.+<!-- \/PAGER -->/i";
			$replacements[0] = $_WEB["menu"];
			$replacements[1] = $_WEB["menu2"];
			$replacements[2] = $_WEB["table"];
			$replacements[3] = $_WEB["pager"];
			$contents = str_replace( "\n", "**nl**",   $contents ); // 暫時移除 new line
			$contents = preg_replace( $patterns, $replacements, $contents );
			$contents = str_replace( "**nl**", "\n",   $contents ); // 加回 new line
			break;


	} // End of switch ( $sFunction )


	// 輸出網頁內容
	print ltrim( $contents );


} // End of function ParseHtml( $sFunction )
?>
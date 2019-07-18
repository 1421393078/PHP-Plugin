<?php

/************************************

	datapager - this class provides a simple method of querying databases and returning specific 'page' sizes of results

@ 2002 Sam Yapp www.samscripts.com

	You are free to use modify and do whatever you like with this script.


	Usage:

	the constructor:

	$datapager->datapager($dbconnection, $query, $pagesize, $querytousetocountrecords);

	where:
	 $dbconnection is a connection to a mysql database
		$query is the sql query (*without any limit x, y on the end)
		$pagesize is the number of records per page
		$querytousetocountrecords is optional 
			it needs to be used when simply replaceing the fields in your queries SELECT bit with a COUNT(*) returns
			more than 1 row. (this is how datapager counts the number of records and the number of pages


		the main function - executes the query and returns a mysql result id or 0 if it fails

		$datapager->execute($pagesize, $pagenumber);

	where:
		$pagesize is the number or records per page
		$page is the page of results


	set up another query to execute

		$datapager->loadquery($query, $pagesize, $querytousetocountrecords); // called internally by creator function

	get a string containing a link to display the next/previous page

	$str = $datapager->nextpage($html, $althtml = "");
	$str = $datapager->prevpage($html, $althtml = "");

	where $html is something like <a href='thispage.php?page=%page%'>Next</a>
	and $althtml is what to use when this is already the last page - defaults to ""

	get a string containing links to all possible pages

		$datapager->pagelinks($linkhtml, $currenthtml = "%page%", $separator = " | ");

	where $linkhtml is something like <a href='thispage.php?page=%page%'>%page%</a>
	and $currenthtml is used for the current page, ie "%page%"
	and $separator is what to separate each pagenumber with


	the following variables are available once a query has been executed:

	$datapager->page		// the current page
	$datapager->pagesize	// number of records per page
	$datapager->recordcount	// total number of records available
	$datapager->pagecount	// total number of pages of records using this page size

************************************/

class datapager{

	var $mainquery;
	var $countquery;
	var $results;
	var $connection;
	var $pagesize;
	var $pagecount;
	var $page;
	var $recordcount;
	var $querydone;

	function datapager($conn = 0, $query  = "", $pagesize = 10, $countquery = ""){
		$this->connection = $conn;
		$this->querydone = false;
		$this->pagesize = $pagesize;
		$this->loadquery($query, $pagesize, $countquery);
	}

	function loadquery($query, $pagesize=0, $countquery=""){
		$this->querydone = false;
		if( $pagesize > 0 )$this->pagesize = $pagesize;
		$this->results = $this->pagecount = $this->page = $this->recordcount = 0;
		if( $query == "" || strtoupper(substr($query, 0, 6)) != "SELECT") return false;
		$this->mainquery = $query;
		if( $countquery == "" ){
			$frompos = strpos( strtoupper($query), "FROM");
			$this->countquery = "SELECT COUNT(*) ".substr($query, $frompos);
		}else{
			$this->countquery = $countquery;
		}
		if( $this->connection ){
			$res = mysql_query($this->countquery, $this->connection)or die(mysql_error());
			if( $res && mysql_num_rows($res) != 1 ){
				$res = mysql_query($this->mainquery, $this->connection)or die(mysql_error());
				$this->recordcount = mysql_num_rows($res);
			}else{
				list($this->recordcount) = mysql_fetch_row($res);
			}
			$this->pagecount = ceil($this->recordcount / $this->pagesize);
			$this->page = 1;
			$this->querydone = true;
			mysql_free_result($res);
			return true;
		}
		return false;
	}

	function nextpage($html, $althtml = ""){
		if( $this->page < $this->pagecount ){
			return str_replace("%page%", $this->page+1, $html);
		}else{
			return $althtml;
		}
	}

	function prevpage($html, $althtml = ""){
		if( $this->page > 1 ){
			return str_replace("%page%", $this->page-1, $html);
		}else{
			return $althtml;
		}
	}

	function old_pagelinks($linkhtml, $currenthtml = "%page%", $separator = " | "){
		$str = "";
		for( $i = 1; $i <= $this->pagecount; $i++){
			if( $i != $this->page ){
				$str .= str_replace("%page%", $i, $linkhtml);
			}else{
				$str .=  str_replace("%page%", $i, $currenthtml);
			}
			if( $i < $this->pagecount ) $str .= $separator;
		}
		return $str; 
	}

	function pagelinks($linkhtml, $currenthtml = "%page%", $separator = " | "){
		$str = "";
		$ten_pages = floor( ( $this->page - 1 ) / 10 ) + 1;
		$ten_pages = ( $ten_pages < 1 ) ? 1 : $ten_pages;
		for( $i = ( $ten_pages - 1 ) * 10 + 1; $i <= $ten_pages * 10 && $i <= $this->pagecount ; $i++){
			if( $i != $this->page ){
				$str .= str_replace("%page%", $i, $linkhtml);
			}else{
				$str .=  str_replace("%page%", $i, $currenthtml);
			}
			if( $i < $this->pagecount ) $str .= $separator;
		}
		return $str; 
	}

	function execute( $page = 1, $pagesize = 10){
		if( $this->querydone == false ) return 0;
		if( $page < 1 ) $page = 1;
		if( $pagesize > $this->recordcount ) $pagesize = $this->recordcount;
		$this->pagesize = $pagesize;
		$this->page = $page;
		$this->pagecount = @ceil($this->recordcount / $this->pagesize);	
		if( $this->page > $this->pagecount ) $this->page = $this->pagecount;

		// do query

		$sql = $this->mainquery." LIMIT ".(($this->page-1) * $this->pagesize).",".$this->pagesize;
		$this->results = mysql_query($sql);

		return $this->results;
	}

}


?>
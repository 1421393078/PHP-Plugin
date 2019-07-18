<?php
include_once '../set/ds.php';
include_once '../set/debug.php';
include_once 'Dir.php';
$date = $_GET['date'];
$path = $_GET['path'];

if(empty($date)){
	echo('请输入日期');	
	die();
}

if(empty($path)){
	echo('请输入路径');	
	die();
}

$time = strtotime($date);
$orgPath = realpath($path).DS;


$dir = new Dir($orgPath);
$dir->setFilter('createTime' , $time ) ;
$dir->setFilter('modifyTime' , $time ) ;
$dir->setFilterRule('createTime',1);
$dir->setFilterRule('modifyTime',1);
$copyPath = __DIR__.DS.( $_GET['item-name'] ?: date('YmdHis') ).DS;

asd($dir->getList());


die();

?>
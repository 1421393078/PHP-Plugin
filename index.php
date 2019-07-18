<?php
include_once 'Dir/Dir.php';
include_once 'set/header.php';
include_once 'set/ds.php';
include_once 'set/debug.php';
include_once 'set/error.php';

$path = 'D:'.DS.'quan'.DS.'html'.DS.'Js'.DS.'dir'.DS.'20190716165523';
$dir = new Dir($path);
$dir->setFilter('ext' , ['gif','js'] ) ;
$dir->setFilterRule('ext',2);
$copyPath = __DIR__.DS.'test';
// $dir->filter['ext'] = ['gif','js'];
// $dir->filterRule['ext'] = 2;
// $dir->filterRule['ext'] = 2;
asd($dir->copy($copyPath));
// ini_set('display_errors',1);
// error_reporting(E_ALL);
// include_once './set/error.php';
// echo "string";
// class d{
// 	public function list2()
// 	{
// 		return 1;
// 	}
// }
// // $d = new d();
// // echo $d->list();
// $time = '1563344700';
// var_dump(strtotime($time));
?>
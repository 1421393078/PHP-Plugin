<?php

Class Dir{
	public $path = ''; // 文件路径
	public $filter = [
		'ext' => [],
		'name'=> []
	];
	public $filterRule = 0 ; // 0不过滤 1保留 2清除

	public function __construct($path)
	{

		$this->path = realpath($path).DS;
	}

	public function getList($dir = '')
	{
		$dir = $dir ?: $this->path;

		$temp = scandir($dir);
		$list = [];
	    //遍历文件夹
	    foreach($temp as $v){
	        $file = $dir.$v;

        	if(!$this->filter($file))continue; // 文件过滤

	       	if(is_dir($file)){//如果是文件夹则执行
	           	if($v=='.' || $v=='..'){//判断是否为系统隐藏的文件.和..  如果是则跳过否则就继续往下走，防止无限循环再这里。
	               	continue;
	           	}
	           	$list = array_merge($list,$this->getList($file.DS));//因为是文件夹所以再次调用自己这个函数，把这个文件夹下的文件遍历出来
	       	}else{
		       	$list[] = $file;
	       	}
	    }
    	return $list;
	}

	public function getTree($path = '')
	{
		$tree = [];
		$dir = $path ?: $this->path;

		$list = scandir($dir, 1);        
		$list = array_diff($list, array('.', '..'));

		for ($i=0; $i < count($list); $i++) { 
        	$fileName = $list[$i];
        	$fullFileName = $dir.$fileName;


        	if(!$this->filter($fullFileName))continue; // 文件过滤

           	if(is_dir($fullFileName)){//如果是文件夹则执行
           		$data = [
           			'type' => 'dir',
           			'fileName' => basename($fileName),
           			'fullPath' => $fullFileName,
           			'child'	   => $this->getTree($fullFileName.DS)
           		];
           	}elseif(is_file($fullFileName)){
           		$data = [
           			'type' => 'file',
           			'fileName' => basename($fileName),
           			'fullPath' => $fullFileName
           		];
           	}
           	$tree[] = $data;

        }

        return $tree;
	}

	public function filter($file)
	{
		$status = true; // 默认保留
		if($this->filterRule === 0) return $status;
		if(is_dir($file))return $status;
		$filesInfo = pathinfo($file);

		if(!empty($this->filter['name'])){
			foreach ($this->filter['name'] as $value) {
				if(strpos($filesInfo['filename'],$value) === false){
					// 找不到的时候
					if($this->filterRule === 1){ // 如果找不到要保留的信息
						$status = false;
					}
				}else{ // 找得到
					if($this->filterRule === 2){
						$status = false;
					}
				}
			}
		}

			// 如果文件扩展名不符合就剔除
		if(!empty($this->filter['ext'])){
			if(!in_array($filesInfo['extension'],$this->filter['ext'])){// 找不到
					// 找不到的时候
				if($this->filterRule === 1){
					$status = false;
				}
			}else{// 找得到
				if($this->filterRule === 2){
					$status = false;
				}
			}
		}

		// echo $status ? 1 : 0;
		// echo "<br/>";
		return $status;
	}


}

$path = 'D:'.DS.'quan'.DS.'html'.DS.'Js'.DS.'dir'.DS.'20190716163328';
echo "<pre>";
$o = new Dir($path);
$o->filterRule = 2;
$o->filter['ext'] = ['html'];
// $o->filter['name'] = ['JumpPcMobile'];
print_r($o->getList());
die();
?>
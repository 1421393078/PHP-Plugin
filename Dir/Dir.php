<?php
Class Dir{
	public $path = ''; // 文件路径
	public $list = '';
	private $filter = [
		'ext' => [],
		'name'=> [],
		'fullFileName' => ['Thumbs.db','.gitignore'],
		'createTime' => 0, // 找出文件的创建时间大于 createTime 的文件
		'modifyTime' => 0  // 找出文件的修改时间大于 modifyTime 的文件 
	];

	// 0不过滤 1保留 2清除
	private $filterRule = [
		'ext' => 0,
		'name'=> 0,
		'fullFileName' => 2,
		'createTime' => 0, // 在这里设置符合规则的文件,是保留,还是清除
		'modifyTime' => 0
	]; 


	public function __construct($path='')
	{
		if(empty($path)){
			throw new Exception("Path cannot be empty. Line ".__LINE__." in the ".__FILE__);
		}
		$this->path = realpath($path).DIRECTORY_SEPARATOR;
	}

	public function setFilter($key,$value)
	{
		if(in_array($key,['ext','name','fullFileName']) ){
			if(is_array($value)){
				$this->filter[$key] = $value;
			}else{
				throw new Exception("The value requirement is an array. Line ".__LINE__." in the ".__FILE__);
			}
		}elseif( in_array($key,['createTime','modifyTime']) ){
			$this->filter[$key] = $value;

		}else{
			throw new Exception("No such attribute. Line ".__LINE__." in the ".__FILE__);
		}
				
	}

	public function get($attr)
	{
		return $this->$attr;
	}
	public function setFilterRule($key,$value)
	{
		if(in_array($key,['ext','name','fullFileName','createTime','modifyTime']) ){
			if($value !== 0 || $value !== 1 || $value !== 2 ){
				$this->filterRule[$key] = $value;
			}else{
				throw new Exception("The value requirement is an array. Line ".__LINE__." in the ".__FILE__);
			}
		}else{
			throw new Exception("No such attribute. Line ".__LINE__." in the ".__FILE__);
		}

	}

	public function appendFilter($key,$value)
	{
		if(in_array($key,['ext','name','fullFileName']) ){
			if(is_array($value)){
				array_merge($this->filter[$key] , $value);
			}else{
				array_push($this->filter[$key] , $value);
			}
		}else{
			throw new Exception("No such attribute. Line ".__LINE__." in the ".__FILE__);
		}
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
	           	$list = array_merge($list,$this->getList($file.DIRECTORY_SEPARATOR));//因为是文件夹所以再次调用自己这个函数，把这个文件夹下的文件遍历出来
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
           			'child'	   => $this->getTree($fullFileName.DIRECTORY_SEPARATOR)
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

	public function copy($copyPath = '')
	{
		if(empty($copyPath)){
			throw new Exception("Please enter the path to copy to. Line ".__LINE__." in the ".__FILE__);
		}
		$list = $this->getList();
		
		foreach ($list as $key => $file) {
			$currFile = str_replace($this->path , $copyPath, $file);
			if(!is_dir(dirname($currFile))){
				mkdir(dirname($currFile),777,true);
			}

			$state[$currFile] = copy($file,$currFile);
		}
		return $state;
	}

	public function filter($file)
	{
		$status = true;
		if(is_dir($file))return true;
		$filesInfo = pathinfo($file);

		// 文件名过滤
		if(!empty($this->filter['name']) && $this->filterRule['name'] !== 0){
			foreach ($this->filter['name'] as $value) {
				if(strpos($filesInfo['filename'],$value) === false){
					// 找不到的时候
					if($this->filterRule['name'] === 1){ // 如果找不到要保留的信息
						return false;
					}
				}else{ // 找得到
					if($this->filterRule['name'] === 2){
						return false;
					}
				}
			}
		}

		// 如果文件扩展名过滤
		if(!empty($this->filter['ext']) && $this->filterRule['ext'] !== 0){
			if(!in_array($filesInfo['extension'],$this->filter['ext'])){// 找不到
					// 找不到的时候
				if($this->filterRule['ext'] === 1){
					return false;
				}
			}else{// 找得到
				if($this->filterRule['ext'] === 2){
					return false;
				}
			}
		}

		if($this->filter['modifyTime'] !== 0 && $this->filterRule['modifyTime'] !== 0){
			$modifyTime =   is_numeric($this->filter['modifyTime']) ?
							intval($this->filter['modifyTime']) :
							strtotime($this->filter['modifyTime']);
			$fileModifyTime = filemtime($file);

			if($fileModifyTime > $modifyTime){ // 如果 文件修改时间  大于 要求的修改时间 的话  (符合规定)
				if($this->filterRule['modifyTime'] === 2){ 
					return false;
				}elseif($this->filterRule['modifyTime'] === 1){
					return true;
				}
			}else{// 找得到
				if($this->filterRule['modifyTime'] === 1){ // 在过滤规则是保留时
					return false;
				}
			}
		}

		if($this->filter['createTime'] !== 0 && $this->filterRule['createTime'] !== 0){
			$createTime =   is_numeric($this->filter['createTime']) ?
							intval($this->filter['createTime']) :
							strtotime($this->filter['createTime']);
			$fileCreateTime = filectime($file);

			if($fileCreateTime > $createTime){ // 找出文件的创建时间大于 createTime 的文件 (符合规定)
				if($this->filterRule['createTime'] === 2){ 
					return false;
				}elseif($this->filterRule['createTime'] === 1){
					return true;
				}
			}else{// 找得到
				if($this->filterRule['createTime'] === 1){ // 在过滤规则是保留时
					return false;
				}
			}

		}

		if(!empty($this->filter['fullFileName']) && $this->filterRule['fullFileName'] !== 0){
			if(!in_array(basename($file),$this->filter['fullFileName'])){ // 找不到
				if($this->filterRule['fullFileName'] === 1){ // 找不到的时候
					return false;
				}
			}else{// 找得到
				if($this->filterRule['fullFileName'] === 2){
					return false;
				}
			}
		}

		return true;
	}

}


?>
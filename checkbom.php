<?php
	/**
	 * 递归检查设定目录下面的文件bon
	 * @param $basedir 基础路径，该函数会递归读取所有的目录
	 * @param $auto 1为自动清除，0为手动清除
	*/
	function repair($basedir,$auto){

		if ($dh = opendir($basedir)) {
			while (($file = readdir($dh)) !== false) {
				if( $file != '.' && $file != '..' ){
					if( is_dir($basedir."/".$file) )
						repair($basedir."/".$file,$auto);
					else 
						echo "filename: $file ".checkbom("$basedir/$file",$auto)." <br>";
				}
			}
			closedir($dh);
		}

	}


	/**
	 * 修复bom
	 * @param $filename 文件路径
	 * @param $auto 1为自动清除，0为手动清除
	*/
	function checkbom ($filename,$auto) {
		$contents=file_get_contents($filename);
		$charset[1]=substr($contents, 0, 1);
		$charset[2]=substr($contents, 1, 1);
		$charset[3]=substr($contents, 2, 1);
		if (ord($charset[1])==239 && ord($charset[2])==187 && ord($charset[3])==191) {
			if ($auto==1) {
				$rest=substr($contents, 3);
				rewrite ($filename, $rest);
				return ("<font color=red>--Bom has been cleared</font>");
			} else {
				return ("<font color=red>--Bom found.</font>");
			}
		}else{
			return ("bom not found.");
		}
	}

	/**
	 * 写数据
	*/
	function rewrite ($filename, $data) {
		$filenum=fopen($filename,"w");
		flock($filenum,LOCK_EX);
		fwrite($filenum,$data);
		fclose($filenum);
	}


/*------------------------
 * 使用方法
 * 1.网页中直接访该文件即可清除文件以及子目录下面的bom
 * 2.使用命令行 php -f checkbom.php
-------------------------*/
$basedir= dirname(__FILE__);//路径为该文件的当前路径
$auto = 0; //是否自动移除发现的BOM信息。1为是，0为否。
repair($basedir,$auto);
?>

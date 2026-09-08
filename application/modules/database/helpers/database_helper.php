<?php
	function extract_zip($zip_file_path, $destination_path){
		$zip = new ZipArchive();
		if ($zip->open($zip_file_path) === true) {
			for($i = 0; $i < $zip->numFiles; $i++) {
				$filename = $zip->getNameIndex($i);
				$fileinfo = pathinfo($filename);
				$destination_path = $destination_path.$fileinfo['basename'];
				copy("zip://".$zip_file_path."#".$filename, $destination_path);
			}                   
			$zip->close(); 
			return $destination_path;                  
		}
		return false;
	}
	
	function mkpath($path){
		if(is_dir($path) && file_exists($path)) 
			return true;
		else
			return mkdir($path, 0755, true);
		
	}

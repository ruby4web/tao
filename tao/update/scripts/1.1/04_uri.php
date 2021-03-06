<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
 
 /* 
  * TO BE DEFINEED:
  * 
  * ROOT_PATH
  * DATABASE_URL
  * DATABASE_LOGIN
  * DATABASE_PASS
  * DATABASE_NAME
  */
 
 if(!defined("DATABASE_NAME")){
 	echo "\nPlease configure me!\n";
	exit(1);
 }

 define('SHOW_OUTPUT', true); 
 
 /**
  * 
  * @param string $uri
  * @return string
  */
 function replaceUri($uri){
 	if(preg_match("/([#]{1}[1-9]+[1-9a-zA-Z]*)$/", $uri)){
 		return str_replace('#', '#i', $uri);
 	}
 }
 
 /**
  * 
  * @param string $dir
  * @param array $types
  * @return array
  */
 function getFiles($dir, $types){
 	
	if(!preg_match("/\/$/", $dir)){
		$dir .= '/';
	}
	
	$typeExp = "/\.[".implode('|', $types)."]+$/";
	
	$files = array();
	foreach(scandir($dir)  as $file){
		if($file != '.' && $file != '..' && $file != '.svn'){
			$path  = $dir . $file;		
			if(is_dir($path)){
	 			$files = array_merge($files, getFiles($path, $types));
	 		}
			else if(preg_match($typeExp, $file) && !in_array($path, $files)){
				$files[] = $path;
			}
		}
 	}
	return $files;
 }
 
 
if(defined("UPDATE_URI_SOURCE")){
	 /*
	  * Parse source code
	  */
	 echo "\nParse source code\n";
	 
	 $extensions = array('filemanager', 'generis', 'tao', 'taoDelivery', 'taoGroups', 'taoItems', 'taoResults', 'taoSubjects', 'taoTests', 'wfEngine');
	 $filesEXt = array('php', 'tpl', 'xml', 'black', 'sql');
	 $files = array();
	 foreach($extensions as $extension){
	 	$files = array_merge($files, getFiles(ROOT_PATH.'/'.$extension, $filesEXt));
	 }
	 $matching_files = array();
	 foreach($files as $file){
	 	if(preg_match("/http(.*)\#[1-9]+/m",file_get_contents($file))) {
	 		$matching_files[] = $file;
	 	}
	 	else{
	 		if(basename($file) == 'constants.php'){
	 			if(preg_match("/\#[1-9]+/m",file_get_contents($file))) {
			 		$matching_files[] = $file;
			 	}
	 		}
	 	}
	 }
	 echo "\nFound ".count($matching_files)." on ".count($files)." files tested\n";
	 unset($files);
	 
	 foreach($matching_files as $file){
		
	 	
	 	
		$uris = array();
		$fileContent = file_get_contents($file);
		preg_match_all("/(\#[0-9]{4,})/m", $fileContent, $uris);
		
		$replaced = 0;
		foreach($uris[0] as $uri){
			//echo $uri."\n";
			if(preg_match("/".preg_quote($uri, '/')."/", $fileContent)){
				$newUri = str_replace('#', '#i', $uri);
			 	if(SHOW_OUTPUT){
					echo "Replace $uri by $newUri \n";
				}
				$fileContent = str_replace($uri, $newUri, $fileContent);
				$replaced++;
			}
		}
		
		if($replaced > 0){
			file_put_contents($file, $fileContent);
			echo "$replaced $file\n";
		}
		
	 }
}

if(defined("UPDATE_URI_DB")){
	 /*
	  * Parse Database
	  */
	 
	 echo "\nParse Database ".DATABASE_NAME."\n";
	 mysql_connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS);
	 mysql_select_db(DATABASE_NAME);
		
		
		$replaced = 0;
		
		$query = "SELECT * FROM `statements` WHERE `subject` LIKE '%#%' OR `predicate` LIKE '%#%' OR `object` LIKE '%#%'";
//	 	echo "\n$query\n";
		$result = mysql_query($query);
	 	while($row = mysql_fetch_assoc($result)){
		
			$updateSet = array();
			if(preg_match("/\#[1-9]+/", $row['subject'])){
				if(SHOW_OUTPUT)
					echo "Replace  {$row['subject']} by ".str_replace('#', '#i', $row['subject'])." \n";
				$updateSet[] = " `subject` = '".str_replace('#', '#i', $row['subject'])."' ";
			}
			if(preg_match("/\#[1-9]+/", $row['predicate'])){
				 if(SHOW_OUTPUT)
                                        echo "Replace  {$row['predicate']} by ".str_replace('#', '#i', $row['predicate'])." \n";
				$updateSet[] = " `predicate` = '".str_replace('#', '#i', $row['predicate'])."' ";
			}	
			if(preg_match("/\#[1-9]+/", $row['object'])){
				if(SHOW_OUTPUT)
					echo "Replace  {$row['object']} by ".str_replace('#', '#i', $row['object'])." \n";
				$updateSet[] = " `object` = '".str_replace('#', '#i', $row['object'])."' ";
			}	
			if(count($updateSet) > 0){
				$replaced += count($updateSet);
				mysql_query("UPDATE `statements` SET ".implode(',', $updateSet)." WHERE id = {$row['id']}");
			}
	 	}
		echo "$replaced fields replaced\n\n";
	 
	 mysql_close();
}
?>

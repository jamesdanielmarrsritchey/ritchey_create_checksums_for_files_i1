<?php
#Name:Ritchey Create Checksums For Files i1 v1
#Description:Hash all files in a directory (recursively), and create a plain-text file in each directory listing the checksums. On success returns an array of files created. Returns "FALSE" on failure.
#Notes:Optional arguments can be "NULL" to skip them in which case they will use default values. Files named 'sha256.txt' are not hashed.
#Arguments:'source_and_destination' (required) is the folder containing the files to hash. This is also where checksum files will be written. 'hashing_algorithm' (optional) is the hashing algorithm to use. Valid values are 'sha256'. Default value is 'sha256'. 'display_errors' (optional) indicates if errors should be displayed.
#Arguments (Script Friendly):source_and_destination:path:required,hashing_algorithm:string:optional,overwrite:bool:optional,display_errors:bool:optional
#Content:
#<value>
if (function_exists('ritchey_create_checksums_for_files_i1_v1') === FALSE){
function ritchey_create_checksums_for_files_i1_v1($source_and_destination, $hashing_algorithm = NULL, $overwrite = NULL, $display_errors = NULL){
	$errors = array();
	$location = realpath(dirname(__FILE__));
	if (@is_dir($source_and_destination) === FALSE){
		$errors[] = 'destination';
	}
	if ($hashing_algorithm === NULL){
		$hashing_algorithm = 'sha256';
	} else if ($hashing_algorithm === 'sha256'){
		//Do nothing
	} else {
		$errors[] = "hashing_algorithm";
	}
	if ($overwrite === NULL){
		$overwrite = FALSE;
	} else if ($overwrite === TRUE){
		//Do nothing
	} else if ($overwrite === FALSE){
		//Do nothing
	} else {
		$errors[] = "overwrite";
	}
	if ($display_errors === NULL){
		$display_errors = FALSE;
	} else if ($display_errors === TRUE){
		#Do Nothing
	} else if ($display_errors === FALSE){
		#Do Nothing
	} else {
		$errors[] = "display_errors";
	}
	##Task
	if (@empty($errors) === TRUE){
		###Get a list of all files
		$location = realpath(dirname(__FILE__));
		require_once $location . '/dependencies/ritchey_list_files_i1_v1/ritchey_list_files_i1_v1.php';
		$files = ritchey_list_files_i1_v1($source_and_destination, FALSE);
		###Hash each file, and add it to a checksums file called 'sha256.txt' in the same directory. If overwrite is FALSE, don't write checksums to any existing sha256.txt files.
		$result = array();
		$new_checksum_files = array();
		foreach ($files as &$item1){
			//Exclude checksum files from hashing
			if (basename($item1) !== 'sha256.txt'){
				####Check if there's a checksums file
				$checksums_file = '';
				if ($hashing_algorithm === 'sha256'){
					$checksums_file = dirname($item1) . '/sha256.txt';
				}
				####If applicable, hash the current file
				$checksum = FALSE;
				if (is_file($checksums_file) === TRUE){
					if ($overwrite === TRUE){
						if (array_search($checksums_file, $new_checksum_files) === FALSE){
							unlink($checksums_file);
						}
						$checksum = hash_file('sha256', $item1);
					} else if (array_search($checksums_file, $new_checksum_files) !== FALSE){
						$checksum = hash_file('sha256', $item1);
					}
				} else {
					$checksum = hash_file('sha256', $item1);
				}
				####If applicable, write the checksum to the checksums file
				if ($checksum !== FALSE){
					if (array_search($checksums_file, $new_checksum_files) === FALSE){
						$new_checksum_files[] = $checksums_file;
					}
					$line = $checksum . '  ' . basename($item1) . PHP_EOL;
					file_put_contents($checksums_file, $line, FILE_APPEND | LOCK_EX);
					$result[] = $checksum . ',' . $item1 . ',' . $checksums_file;
				}
			}
		}
		unset($item1);
	}
	result:
	##Display Errors
	if ($display_errors === TRUE){
		if (@empty($errors) === FALSE){
			$message = @implode(", ", $errors);
			if (function_exists('ritchey_create_checksums_for_files_i1_v1_format_error') === FALSE){
				function ritchey_create_checksums_for_files_i1_v1_format_error($errno, $errstr){
					echo $errstr;
				}
			}
			set_error_handler("ritchey_create_checksums_for_files_i1_v1_format_error");
			trigger_error($message, E_USER_ERROR);
		}
	}
	##Return
	if (@empty($errors) === TRUE){
		if (@empty($result) === TRUE){
			return TRUE;
		} else {
			return $result;
		}
	} else {
		return FALSE;
	}
}
}
#</value>
?>
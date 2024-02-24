<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/ritchey_create_checksums_for_files_i1_v1.php';
$return = ritchey_create_checksums_for_files_i1_v1("{$location}/temporary", 'sha256', FALSE, TRUE);
if (is_array($return) === TRUE){
	print_r($return) . PHP_EOL;
} else if ($return === TRUE) {
	echo "TRUE" . PHP_EOL;
} else {
	echo "FALSE" . PHP_EOL;
}
?>
<?php
/*
Plugin Name: Berman & Company - Extended Timber Classes
Version: 0.1-alpha
Description: Expanded Timber classes with new features
Author: Berman & Company
Author URI: http://bermanco.com
Text Domain: bac-timber-classes
Domain Path: /languages
*/

function bac_timber_classes_load_files(){

	$files = array(
		'classes/BasePost.php'
	);

	if ($files) {
		foreach ($files as $file){
			require_once($file);
		}
	}

}

function bac_timber_classes_init(){

	if (file_exists('vendor/autoload.php')){
		require_once('vendor/autoload.php');
	}

	bac_timber_classes_load_files();

}

bac_timber_classes_init();
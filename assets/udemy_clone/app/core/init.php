<?php
spl_autoload_register(function($class_name)
{
	$parts = explode("\\", $class_name);
	$class_name = array_pop($parts);
        $file= "../app/models/" .$class_name . ".php";

    // Check if the file exists
    if(file_exists($file)){

        // require_once $full_path;	
        require_once $file;

    }
});

require "config.php";
require "permissions.php";
require "functions.php";
require "database.php";
require "model.php";
require "controller.php";
require "app.php";

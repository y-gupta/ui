<?php
/**
 * This is the index.php file of a app that uses a common/external UI installation
 * $_UI_PATH is used only for clarity, it is not required
 * @var string $_APP_DIR is the path to the current app. It ends with a slash, like all other paths in UI
 */ 
$_UI_PATH=realpath(dirname(__FILE__).'/../').'/';
$_APP_DIR=dirname(__FILE__).'/';
include($_UI_PATH.'index.php');
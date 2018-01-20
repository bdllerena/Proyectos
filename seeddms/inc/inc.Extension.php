<?php
/**
 * Initialize extensions
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2013 Uwe Steinmann
 * @version    Release: @package_version@
 */

require "inc.ClassExtensionMgr.php";
require_once "inc.ClassExtBase.php";
require_once "inc.Version.php";
require_once "inc.Utils.php";

$extMgr = new SeedDMS_Extension_Mgr($settings->_rootDir."/ext", $settings->_cacheDir);
$extconffile = $extMgr->getExtensionsConfFile();
if(!file_exists($extconffile)) {
	$extMgr->createExtensionConf();
}
$EXT_CONF = array();
include($extconffile);

$version = new SeedDMS_Version;

foreach($EXT_CONF as $extname=>$extconf) {
	if(!isset($extconf['disable']) || $extconf['disable'] == false) {
		/* check for requirements */
		if(!empty($extconf['constraints']['depends']['seeddms'])) {
			$t = explode('-', $extconf['constraints']['depends']['seeddms'], 2);
			if(cmpVersion($t[0], $version->version()) > 0 || ($t[1] && cmpVersion($t[1], $version->version()) < 0))
				$extconf['disable'] = true;
		}
	}
	if(!isset($extconf['disable']) || $extconf['disable'] == false) {
		$classfile = $settings->_rootDir."/ext/".$extname."/".$extconf['class']['file'];
		if(file_exists($classfile)) {
			include($classfile);
			$obj = new $extconf['class']['name'];
			if(method_exists($obj, 'init'))
				$obj->init();
		}
		if(isset($extconf['language']['file'])) {
			$langfile = $settings->_rootDir."/ext/".$extname."/".$extconf['language']['file'];
			if(file_exists($langfile)) {
				unset($__lang);
				include($langfile);
				if($__lang) {
					foreach($__lang as $lang=>&$data) {
						if(isset($GLOBALS['LANG'][$lang]))
							$GLOBALS['LANG'][$lang] = array_merge($GLOBALS['LANG'][$lang], $data);
						else
							$GLOBALS['LANG'][$lang] = $data;
					}
				}
			}
		}
	}
}

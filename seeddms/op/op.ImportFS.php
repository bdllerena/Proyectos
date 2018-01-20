<?php
//    SeedDMS. Document Management System
//    Copyright (C) 2010-2016 Uwe Steinmann
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.LogInit.php");
include("../inc/inc.Utils.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

if (!isset($_GET["targetid"]) || !is_numeric($_GET["targetid"]) || $_GET["targetid"]<1) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_target_folder"));
}
$targetid = $_GET["targetid"];
$folder = $dms->getFolder($targetid);
if (!is_object($folder)) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_target_folder"));
}

if ($folder->getAccessMode($user) < M_READWRITE) {
	UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (empty($_GET["dropfolderfileform1"])) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_target_folder"));
}

$dirname = realpath($settings->_dropFolderDir.'/'.$user->getLogin()."/".$_GET["dropfolderfileform1"]);
if(strpos($dirname, realpath($settings->_dropFolderDir.'/'.$user->getLogin().'/')) !== 0 || !is_dir($dirname)) {
	UI::exitError(getMLText("admin_tools"),getMLText("invalid_dropfolder_folder"));
}

function import_folder($dirname, $folder) { /* {{{ */
	global $user, $doccount, $foldercount;

	$d = dir($dirname);
	$sequence = 1;
	while(false !== ($entry = $d->read())) {
		$path = $dirname.'/'.$entry;
		if($entry != '.' && $entry != '..' && $entry != '.svn') {
			if(is_file($path)) {
				$name = basename($path);
				$filetmp = $path;

				$reviewers = array();
				$approvers = array();
				$comment = '';
				$version_comment = '';
				$reqversion = 1;
				$expires = false;
				$keywords = '';
				$categories = array();

				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mimetype = finfo_file($finfo, $path);
				$lastDotIndex = strrpos($path, ".");
				if (is_bool($lastDotIndex) && !$lastDotIndex) $filetype = ".";
				else $filetype = substr($path, $lastDotIndex);

//				echo $mimetype." - ".$filetype." - ".$path."\n";
				if($res = $folder->addDocument($name, $comment, $expires, $user, $keywords,
																		$categories, $filetmp, $name,
																		$filetype, $mimetype, $sequence, $reviewers,
																		$approvers, $reqversion, $version_comment)) {
					$doccount++;
				} else {
					return false;
				}
				set_time_limit(30);
			} elseif(is_dir($path)) {
				$name = basename($path);
				if($newfolder = $folder->addSubFolder($name, '', $user, $sequence)) {
					$foldercount++;
					if(!import_folder($path, $newfolder))
						return false;
				} else {
					return false;
				}
			}
			$sequence++;
		}
	}
	return true;
} /* }}} */

$foldercount = $doccount = 0;
if($newfolder = $folder->addSubFolder($_GET["dropfolderfileform1"], '', $user, 1)) {
	if(!import_folder($dirname, $newfolder))
		$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('error_importfs')));
	else {
		if(isset($_GET['remove']) && $_GET["remove"]) {
			$cmd = 'rm -rf '.$dirname;
			$ret = null;
			system($cmd, $ret);
		}
		$session->setSplashMsg(array('type'=>'success', 'msg'=>getMLText('splash_importfs', array('docs'=>$doccount, 'folders'=>$foldercount))));
	}
} else {
	$session->setSplashMsg(array('type'=>'error', 'msg'=>getMLText('error_importfs')));
}

header("Location:../out/out.ViewFolder.php?folderid=".$newfolder->getID());

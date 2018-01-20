<?php
if(isset($_SERVER['SEEDDMS_HOME'])) {
	ini_set('include_path', $_SERVER['SEEDDMS_HOME']. PATH_SEPARATOR .ini_get('include_path'));
}
require_once("inc/inc.ClassSettings.php");

function usage() { /* {{{ */
	echo "Usage:\n";
	echo "  seeddms-expireddocs [--config <file>] [-u <user>] [-h] [-v] [-t] [-q] [-o] [-f <email>] [-u <user>] [-w] [-b <base>] [-c] -d <days> -D <days>\n";
	echo "\n";
	echo "Description:\n";
	echo "  Check for files which will expire in the next days and inform the\n";
	echo "  the owner and all users watching the document.\n";
	echo "\n";
	echo "Options:\n";
	echo "  -h, --help: print usage information and exit.\n";
	echo "  -v, --version: print version and exit.\n";
	echo "  --config=<file>: set alternative config file.\n";
	echo "  -u <user>: login name of user\n";
	echo "  -w: send mail also to all users watching the document\n";
	echo "  -c: list also categories for each document\n";
	echo "  -f <email>: set From field in notification mail\n";
	echo "  -b <base>: set base for links in html email. The final link will be\n";
	echo "             <base><httpRoot>out/out.ViewDocument.php. The default is\n";
	echo "             http://localhost\n";
	echo "  -d <days>: check till n days in the future (default 14). Days always\n".
	     "             start at 00:00:00 and end at 23:59:59. A value of '1' means today.\n";
	     "             '-d 2' will search for documents expiring today or tomorrow.\n";
	echo "  -D <days>: start checking in n days in the future (default 0). This value\n".
	     "             must be less then -d. A value of 0 means to start checking today.\n".
	     "             Any positive number will start checking in n days.\n".
	     "             A negative number will look backwards in time.\n".
	     "             '-d 10 -D 5' will search for documents expiring in 5 to 10 days.\n".
	     "             '-d 10 -D -5' will search for documents which have expired in the last 5 days\n".
	     "             or will expire in the next 10 days.\n";
	echo "  -o: list obsolete documents (default: do not list)\n";
	echo "  -t: run in test mode (will not send any mails)\n";
	echo "  -q: be quite (just output error messages)\n";
} /* }}} */

$version = "0.0.2";
$tableformat = "%-60s %-14s";
$tableformathtml = "<tr><td>%s</td><td>%s</td></tr>";
$baseurl = "http://localhost/";
$mailfrom = "uwe@steinman.cx";

$shortoptions = "u:d:D:f:b:wtqhvo";
$longoptions = array('help', 'version', 'config:');
if(false === ($options = getopt($shortoptions, $longoptions))) {
	usage();
	exit(0);
}

/* Print help and exit */
if(isset($options['h']) || isset($options['help'])) {
	usage();
	exit(0);
}

/* Print version and exit */
if(isset($options['v']) || isset($options['verѕion'])) {
	echo $version."\n";
	exit(0);
}

/* Set alternative config file */
if(isset($options['config'])) {
	$settings = new Settings($options['config']);
} else {
	$settings = new Settings();
}

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

include("inc/inc.Language.php");
include("inc/inc.Extension.php");

$LANG['de_DE']['daylyDigestMail'] = 'Tägliche Benachrichtigungsmail';
$LANG['en_GB']['daylyDigestMail'] = 'Dayly digest mail';
$LANG['de_DE']['docsExpiringInNDays'] = 'Dokumente, die in den nächsten [days] Tagen ablaufen';
$LANG['en_GB']['docsExpiringInNDays'] = 'Documents expiring in the next [days] days';
$LANG['de_DE']['docsExpiringBetween'] = 'Dokumente, die zwischen dem [start] und [end] ablaufen';
$LANG['en_GB']['docsExpiringBetween'] = 'Documents which expire between [start] and [end]';

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

require_once('Mail.php');
require_once('Mail/mime.php');

require_once("SeedDMS/Core.php");

$usernames = array();
if(isset($options['u'])) {
	$usernames = explode(',', $options['u']);
}

$informwatcher = false;
if(isset($options['w'])) {
	$informwatcher = true;
}

$showcats = false;
if(isset($options['c'])) {
	$showcats = true;
	$tableformathtml = "<tr><td>%s</td><td>%s</td><td>%s</td></tr>";
}

$days = 14;
if(isset($options['d'])) {
	$days = (int) $options['d'];
}
$enddays = 0;
if(isset($options['D'])) {
	$enddays = (int) $options['D'];
}

if($enddays >= $days) {
	echo "Value of -D must be less then value of -d\n";
	exit(1);
}

if(isset($options['f'])) {
	$mailfrom = trim($options['f']);
}

if(isset($options['b'])) {
	$baseurl = trim($options['b']);
}

$showobsolete = false;
if(isset($options['o'])) {
	$showobsolete = true;
}

$dryrun = false;
if(isset($options['t'])) {
	$dryrun = true;
	echo "Running in test mode will not send any mail.\n";
}
$quite = false;
if(isset($options['q'])) {
	$quite = true;
}

$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");
$db->_debug = 1;

$dms = new SeedDMS_Core_DMS($db, $settings->_contentDir.$settings->_contentOffsetDir);
if(!$settings->_doNotCheckDBVersion && !$dms->checkVersion()) {
	echo "Database update needed.";
	exit;
}
$dms->setRootFolderID($settings->_rootFolderID);
$dms->setMaxDirID($settings->_maxDirID);
$dms->setEnableConverting($settings->_enableConverting);
$dms->setViewOnlineFileTypes($settings->_viewOnlineFileTypes);

$startts = strtotime("midnight", time());
if(!$quite)
	echo "Checking for documents expiring between ".getLongReadableDate($startts+$enddays*86400)." and ".getLongReadableDate($startts+$days*86400-1)."\n";

$users = array();
if(!$usernames) {
	$users = $dms->getAllUsers();
} else {
	/* Create a global user object */
	foreach($usernames as $username) {
		if(!$user = $dms->getUserByLogin($username)) {
			echo "No such user with name '".$username."'\n";
			exit(1);
		}
		$users[] = $user;
	}
}

if (!$db->createTemporaryTable("ttstatid") || !$db->createTemporaryTable("ttcontentid")) {
	echo getMLText("internal_error_exit")."\n";
	exit;
}

foreach($users as $user) {
	$groups = $user->getGroups();
	$groupids = array();
	foreach($groups as $group)
		$groupids[] = $group->getID();
	$sendmail = false; /* Set to true if there is something to report */
	$body = "";
	$bodyhtml = "<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n<title>SeedDMS: ".getMLText('daylyDigestMail', array(), "", $user->getLanguage())."</title>\n<base href=\"".$baseurl.$settings->_httpRoot."\" />\n</head>\n<body>\n";

	/*
	$queryStr = "SELECT `tblDocuments`.* FROM `tblDocuments`".
		"WHERE `tblDocuments`.`owner` = '".$user->getID()."' ".
		"AND `tblDocuments`.`expires` < '".($startts + $days*86400)."' ".
		"AND `tblDocuments`.`expires` > '".($startts)."'";
	 */

	$queryStr = "SELECT DISTINCT a.*, tblDocumentStatusLog.* FROM `tblDocuments` a ".
		"LEFT JOIN `ttcontentid` ON `ttcontentid`.`document` = `a`.`id` ".
		"LEFT JOIN `tblDocumentContent` ON `a`.`id` = `tblDocumentContent`.`document` AND `tblDocumentContent`.`version` = `ttcontentid`.`maxVersion` ".
		"LEFT JOIN `tblNotify` b ON a.id=b.target ".
		"LEFT JOIN `tblDocumentStatus` ON `tblDocumentStatus`.`documentID` = `tblDocumentContent`.`document` AND `tblDocumentContent`.`version` = `tblDocumentStatus`.`version` ".
		"LEFT JOIN `ttstatid` ON `ttstatid`.`statusID` = `tblDocumentStatus`.`statusID` ".
		"LEFT JOIN `tblDocumentStatusLog` ON `tblDocumentStatusLog`.`statusLogID` = `ttstatid`.`maxLogID` ".
		"WHERE (a.`owner` = '".$user->getID()."' ".
		($informwatcher ? " OR ((b.userID = '".$user->getID()."' ".
		($groupids ? "or b.groupID in (".implode(',', $groupids).")" : "").") ".
		"AND b.targetType = 2) " : "").
		") AND a.`expires` < '".($startts + $days*86400)."' ".
		"AND a.`expires` > '".($startts + $enddays*86400)."' ";
	if(!$showobsolete)
		$queryStr .= "AND tblDocumentStatusLog.status != -2";

	$resArr = $db->getResultArray($queryStr);
	if (is_bool($resArr) && !$resArr) {
		echo getMLText("internal_error_exit")."\n";
		exit;
	}

	$body .= "==== ";
	$body .= getMLText('docsExpiringBetween', array('start'=>getReadableDate($startts + ($enddays)*86400), 'end'=>getReadableDate($startts + ($days)*86400)), "", $user->getLanguage())."\n";
	$body .= "==== ";
	$body .= $user->getFullname();
	$body .= "\n\n";
	$bodyhtml .= "<h2>";
	$bodyhtml .= getMLText('docsExpiringBetween', array('start'=>getReadableDate($startts + ($enddays)*86400), 'end'=>getReadableDate($startts + ($days)*86400)), "", $user->getLanguage())."\n";
	$bodyhtml .= "</h2>\n";
	$bodyhtml .= "<h3>";
	$bodyhtml .= $user->getFullname();
	$bodyhtml .= "</h3>\n";
	if (count($resArr)>0) {
		$sendmail = true;

		$body .= sprintf($tableformat."\n", getMLText("name", array(), "", $user->getLanguage()), getMLText("expires", array(), "", $user->getLanguage()));	
		$body .= "---------------------------------------------------------------------------------\n";
		$bodyhtml .= "<table>\n";
		if($showcats)
			$bodyhtml .= sprintf($tableformathtml."\n", getMLText("name", array(), "", $user->getLanguage()), getMLText("categories", array(), "", $user->getLanguage()), getMLText("expires", array(), "", $user->getLanguage()));	
		else
			$bodyhtml .= sprintf($tableformathtml."\n", getMLText("name", array(), "", $user->getLanguage()), getMLText("expires", array(), "", $user->getLanguage()));	

		foreach ($resArr as $res) {
			if($doc = $dms->getDocument((int) $res['id'])) {
				$catnames = array();
				if($cats = $doc->getCategories()) {
					foreach($cats as $cat)
						$catnames[] = $cat->getName();
				}
			}
		
			$body .= sprintf($tableformat."\n", $res["name"], (!$res["expires"] ? "-":getReadableDate($res["expires"])));
			if($showcats)
				$body .= getMLText("categories", array(), "", $user->getLanguage()).": ".implode(', ', $catnames)."\n";
			if($showcats)
				$bodyhtml .= sprintf($tableformathtml."\n", '<a href="out/out.ViewDocument.php?documentid='.$res["id"].'">'.htmlspecialchars($res["name"]).'</a>', implode(', ', $catnames), (!$res["expires"] ? "-":getReadableDate($res["expires"])));	
			else
				$bodyhtml .= sprintf($tableformathtml."\n", '<a href="out/out.ViewDocument.php?documentid='.$res["id"].'">'.htmlspecialchars($res["name"]).'</a>', (!$res["expires"] ? "-":getReadableDate($res["expires"])));	
		}		
		$bodyhtml .= "</table>\n";
		
	} else {
		$body .= getMLText("no_docs_to_look_at", array(), "", $user->getLanguage())."\n\n";
		$bodyhtml .= "<p>".getMLText("no_docs_to_look_at", array(), "", $user->getLanguage())."</p>\n\n";
	}

	if($sendmail) {
		if($user->getEmail()) {
			if(!$quite) {
				echo "Send mail to ".$user->getLogin()." <".$user->getEmail().">\n";
			echo $body;
			echo "----------------------------\n\n\n";
			echo $bodyhtml;
			}

			if(!$dryrun) {
				$mime = new Mail_mime(array('eol' => "\n"));

				$mime->setTXTBody($body);
				$mime->setHTMLBody($bodyhtml);

				$body = $mime->get(array(
					'text_encoding'=>'8bit',
					'html_encoding'=>'8bit',
					'head_charset'=>'utf-8',
					'text_charset'=>'utf-8',
					'html_charset'=>'utf-8'
				));
				$hdrs = $mime->headers(array('From' => $mailfrom, 'Subject' => 'SeedDMS: '.getMLText('daylyDigestMail', array(), "", $user->getLanguage()), 'Content-Type' => 'text/plain; charset=UTF-8'));

				$mail_params = array();
				if($settings->smtpServer) {
					$mail_params['host'] = $settings->smtpServer;
					if($settings->smtpPort) {
						$mail_params['port'] = $settings->smtpPort;
					}
					if($settings->smtpUser) {
						$mail_params['auth'] = true;
						$mail_params['username'] = $settings->smtpUser;
						$mail_params['password'] = $settings->smtpPassword;
					}
					$mail = Mail::factory('smtp', $mail_params);
				} else {
					$mail = Mail::factory('mail');
				}
				$mail->send($user->getEmail(), $hdrs, $body);
			}
		} else {
			if(!$quite) {
				echo "User ".$user->getLogin()." has no email\n";
			}
		}
	} else {
		if(!$quite) {
			echo "No notification for user ".$user->getLogin()." needed\n";
		}
	}
}
?>

<?php
if(isset($_SERVER['SEEDDMS_HOME'])) {
	require_once($_SERVER['SEEDDMS_HOME']."/inc/inc.ClassSettings.php");
} else {
	require_once("../inc/inc.ClassSettings.php");
}

function usage() { /* {{{ */
	echo "Usage:\n";
	echo "  seeddms-dbcheck [-h] [-v] [--config <file>]\n";
	echo "\n";
	echo "Description:\n";
	echo "  This program creates an xml dump of the whole or parts of the dms.\n";
	echo "\n";
	echo "Options:\n";
	echo "  -h, --help: print usage information and exit.\n";
	echo "  -v, --version: print version and exit.\n";
	echo "  --config: set alternative config file.\n";
} /* }}} */

function check_table($db, $tablename, $fieldname, $joins=array()) { /* {{{ */
	if($fieldname) {
		if(is_string($fieldname))
			$fieldnames = array($fieldname);
		else
			$fieldnames = $fieldname;
		$sql = "SELECT ".implode(",", $fieldnames)." FROM `".$tablename."` a";
		if($joins) {
			$i = 0;
			foreach($joins as $join) {
				$alias = chr(98+$i);
				$sql .= " LEFT JOIN `".$join['tablename']."` ".$alias." ON ".(strpos($join['key'], '.') ? $join['key'] : "`a`.`".$join['key']."`")." = `".$alias."`.`".$join['foreign_key']."`";
				$i++;
			}
		}
		$sql .= " ORDER BY ".$fieldnames[0]."";
//		echo $sql."\n";
	} else
		$sql = "SELECT * FROM `".$tablename."`";
	$resArr = $db->getResultArray($sql);
	if (is_bool($resArr) && $resArr == false)
		return false;

	$c = count($resArr);
	if($fieldname) {
		$str = '';
		$fieldvals = array();
		foreach($resArr as $rec) {
			$fieldvals[] = implode("|", $rec);
		}
		sort($fieldvals);
	} else {
		$fieldvals = array();
	}

	return array('count'=>$c, 'values'=>$fieldvals, 'md5'=>md5(implode('', $fieldvals)));
} /* }}} */

$version = "0.0.1";
$shortoptions = "hv";
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

require_once("SeedDMS/Core.php");
$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");

$tables = array(
	array('tblDocuments', array('`a`.`name`', '`a`.`comment`', '`a`.`keywords`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'owner', 'foreign_key'=>'id'))),
	array('tblDocumentContent', array('orgFileName', 'version', 'comment')),
	array('tblFolders', array('`a`.`name`', '`a`.`comment`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'owner', 'foreign_key'=>'id'))),
	array('tblAttributeDefinitions', array('name', 'objtype', 'type', 'valueset', 'regex')),
	array('tblCategory', 'name'),
	array('tblKeywordCategories', array('`a`.`name`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'owner', 'foreign_key'=>'id'))),
	array('tblKeywords', 'keywords'),
	array('tblDocumentApproveLog', array('`d`.`name`', '`c`.`version`', '`a`.`comment`', '`a`.`status`', '`a`.`date`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'), array('tablename'=>'tblDocumentApprovers', 'key'=>'approveID', 'foreign_key'=>'approveID'), array('tablename'=>'tblDocuments', 'key'=>'`c`.`documentID`', 'foreign_key'=>'id'))),
	array('tblDocumentReviewLog', array('`d`.`name`', '`c`.`version`', '`a`.`comment`', '`a`.`status`', '`a`.`date`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'), array('tablename'=>'tblDocumentReviewers', 'key'=>'reviewID', 'foreign_key'=>'reviewID'), array('tablename'=>'tblDocuments', 'key'=>'`c`.`documentID`', 'foreign_key'=>'id'))),
	array('tblDocumentReceiptLog', array('`d`.`name`', '`c`.`version`', '`a`.`comment`', '`a`.`status`', '`a`.`date`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'), array('tablename'=>'tblDocumentRecipients', 'key'=>'receiptID', 'foreign_key'=>'receiptID'), array('tablename'=>'tblDocuments', 'key'=>'`c`.`documentID`', 'foreign_key'=>'id'))),
	array('tblDocumentRevisionLog', array('`d`.`name`', '`c`.`version`', '`a`.`comment`', '`a`.`status`', '`a`.`date`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'), array('tablename'=>'tblDocumentRevisors', 'key'=>'revisionID', 'foreign_key'=>'revisionID'), array('tablename'=>'tblDocuments', 'key'=>'`c`.`documentID`', 'foreign_key'=>'id'))),
	array('tblDocumentStatusLog', array('`d`.`name`', '`c`.`version`', '`a`.`comment`', '`a`.`status`', '`a`.`date`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'), array('tablename'=>'tblDocumentStatus', 'key'=>'statusID', 'foreign_key'=>'statusID'), array('tablename'=>'tblDocuments', 'key'=>'`c`.`documentID`', 'foreign_key'=>'id'))),
	array('tblWorkflowLog', array('`a`.`comment`', '`a`.`date`')),
	array('tblDocumentAttributes', array('`a`.`value`', '`c`.`name` as attrname', '`b`.`name`'), array(array('tablename'=>'tblDocuments', 'key'=>'document', 'foreign_key'=>'id'), array('tablename'=>'tblAttributeDefinitions', 'key'=>'attrdef', 'foreign_key'=>'id'))),
	array('tblDocumentContentAttributes', array('value', '`c`.`name` as attrname', '`b`.`orgFileName`'), array(array('tablename'=>'tblDocumentContent', 'key'=>'content', 'foreign_key'=>'id'), array('tablename'=>'tblAttributeDefinitions', 'key'=>'attrdef', 'foreign_key'=>'id'))),
	array('tblDocumentCategory', array('`b`.`name`', '`c`.`name` as docname'), array(array('tablename'=>'tblCategory', 'key'=>'categoryID', 'foreign_key'=>'id'), array('tablename'=>'tblDocuments', 'key'=>'documentID', 'foreign_key'=>'id'))),
	array('tblFolderAttributes', array('value', '`c`.`name` as attrname', '`b`.`name`'), array(array('tablename'=>'tblFolders', 'key'=>'folder', 'foreign_key'=>'id'), array('tablename'=>'tblAttributeDefinitions', 'key'=>'attrdef', 'foreign_key'=>'id'))),
	array('tblRoles', array('name', 'noaccess')),
	array('tblUsers', array('login', 'pwd', 'secret', 'fullName', 'email', 'language', 'comment', 'hidden', 'disabled', 'quota')),
	array('tblGroups', array('name', 'comment')),
	array('tblGroupMembers', 'manager'),
	array('tblUserImages', array('image', 'mimeType')),
	array('tblDocumentApprovers', array('`b`.`name`', '`a`.`version`', '`a`.`type`'), array(array('tablename'=>'tblDocuments', 'key'=>'documentID', 'foreign_key'=>'id'))),
	array('tblDocumentReviewers', array('`b`.`name`', '`a`.`version`', '`a`.`type`'),array(array('tablename'=>'tblDocuments', 'key'=>'documentID', 'foreign_key'=>'id'))),
	array('tblDocumentRevisors', array('`b`.`name`', '`a`.`version`', '`a`.`type`'),array(array('tablename'=>'tblDocuments', 'key'=>'documentID', 'foreign_key'=>'id'))),
	array('tblDocumentRecipients', array('`b`.`name`', '`a`.`version`', '`a`.`type`'), array(array('tablename'=>'tblDocuments', 'key'=>'documentID', 'foreign_key'=>'id'))),
	array('tblDocumentStatus', array('`b`.`name`', '`a`.`version`'),array(array('tablename'=>'tblDocuments', 'key'=>'documentID', 'foreign_key'=>'id'))),
	array('tblDocumentLinks', array('`b`.`name`', '`a`.`public`', '`c`.`login`'), array(array('tablename'=>'tblDocuments', 'key'=>'document', 'foreign_key'=>'id'), array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblDocumentFiles', array('orgFileName', 'name', 'mimeType', 'comment', 'version', 'public')),
	array('tblDocumentLocks', array('`b`.`name`', '`c`.`login`'), array(array('tablename'=>'tblDocuments', 'key'=>'document', 'foreign_key'=>'id'), array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblDocumentCheckOuts', 'filename'),
	array('tblMandatoryReviewers', array('`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblMandatoryApprovers', array('`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblWorkflowMandatoryWorkflow', array('`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblEvents', array('name', '`a`.`comment`', 'start', 'stop', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblWorkflowStates', array('name', 'documentstatus')),
	array('tblWorkflows', array('`a`.`name`', '`b`.`name` initstate'), array(array('tablename'=>'tblWorkflowStates', 'key'=>'initstate', 'foreign_key'=>'id'))),
	array('tblWorkflowTransitions', array('`b`.`name`', '`c`.`name` sname', '`d`.`name` aname'), array(array('tablename'=>'tblWorkflows', 'key'=>'workflow', 'foreign_key'=>'id'), array('tablename'=>'tblWorkflowStates', 'key'=>'state', 'foreign_key'=>'id'), array('tablename'=>'tblWorkflowActions', 'key'=>'action', 'foreign_key'=>'id'))),
	array('tblWorkflowTransitionUsers', array('`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblWorkflowTransitionGroups', array('`b`.`name`'), array(array('tablename'=>'tblGroups', 'key'=>'groupid', 'foreign_key'=>'id'))),
	array('tblWorkflowDocumentContent', array('`b`.`name`', 'version', '`c`.`name` wname', '`d`.`name` sname'), array(array('tablename'=>'tblDocuments', 'key'=>'document', 'foreign_key'=>'id'), array('tablename'=>'tblWorkflows', 'key'=>'workflow', 'foreign_key'=>'id'), array('tablename'=>'tblWorkflowStates', 'key'=>'state', 'foreign_key'=>'id'))),
	array('tblTransmittals', array('name', '`a`.`comment`', '`b`.`login`'), array(array('tablename'=>'tblUsers', 'key'=>'userID', 'foreign_key'=>'id'))),
	array('tblTransmittalItems', array('`c`.`name`', '`b`.`name` transname'), array(array('tablename'=>'tblTransmittals', 'key'=>'transmittal', 'foreign_key'=>'id'), array('tablename'=>'tblDocuments', 'key'=>'document', 'foreign_key'=>'id'))),
);

foreach($tables as $table) {
	$data = check_table($db, $table[0], $table[1], isset($table[2]) ? $table[2] : null);
	printf("%-30s\t%6d\t%s\n", $table[0], $data['count'], $data['md5']);
	printf("---------------------------------------------------------------------------------\n");
	if($data && isset($data['values']))
		echo implode("\n", $data['values'])."\n\n";
	else
		echo "No data\n";
}

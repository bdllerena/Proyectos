<?php
/**
 * Implementation of DocumentNotify view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for DocumentNotify view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_DocumentNotify extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
?>
function checkForm()
{
	msg = new Array();
	if ((document.form1.userid.options[document.form1.userid.selectedIndex].value == -1) && 
		(document.form1.groupid.options[document.form1.groupid.selectedIndex].value == -1))
			msg.push("<?php printMLText("js_select_user_or_group");?>");
	if (msg != "") {
  	noty({
  		text: msg.join('<br />'),
  		type: 'error',
      dismissQueue: true,
  		layout: 'topRight',
  		theme: 'defaultTheme',
			_timeout: 1500,
  	});
		return false;
	}
	else
		return true;
}

$(document).ready( function() {
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$sortusersinlist = $this->params['sortusersinlist'];

		$notifyList = $document->getNotifyList();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

		$this->contentHeading(getMLText("edit_existing_notify"));
		$this->contentContainerStart();

		$userNotifyIDs = array();
		$groupNotifyIDs = array();

		print "<table class=\"table-condensed\">\n";
		if ((count($notifyList["users"]) == 0) && (count($notifyList["groups"]) == 0)) {
			print "<tr><td>".getMLText("empty_notify_list")."</td></tr>";
		}
		else {
			foreach ($notifyList["users"] as $userNotify) {
				print "<tr>";
				print "<td><i class=\"icon-user\"></i></td>";
				print "<td>" . htmlspecialchars($userNotify->getLogin() . " - " . $userNotify->getFullName()) . "</td>";
				if ($user->isAdmin() || $user->getID() == $userNotify->getID()) {
					print "<td><a href=\"../op/op.DocumentNotify.php?documentid=". $document->getID() . "&action=delnotify&userid=".$userNotify->getID()."\" class=\"btn btn-mini\"><i class=\"icon-remove\"></i> ".getMLText("delete")."</a></td>";
				}else print "<td></td>";
				print "</tr>";
				$userNotifyIDs[] = $userNotify->getID();
			}
			foreach ($notifyList["groups"] as $groupNotify) {
				print "<tr>";
				print "<td><i class=\"icon-group\"></i></td>";
				print "<td>" . htmlspecialchars($groupNotify->getName()) . "</td>";
				if ($user->isAdmin() || $groupNotify->isMember($user,true)) {
					print "<td><a href=\"../op/op.DocumentNotify.php?documentid=". $document->getID() . "&action=delnotify&groupid=".$groupNotify->getID()."\" class=\"btn btn-mini\"><i class=\"icon-remove\"></i> ".getMLText("delete")."</a></td>";
				}else print "<td></td>";
				print "</tr>";
				$groupNotifyIDs[] = $groupNotify->getID();
			}
		}
		print "</table>\n";

?>
<br>

<form class=form-horizontal" action="../op/op.DocumentNotify.php" name="form1" id="form1">
<input type="hidden" name="documentid" value="<?php print $document->getID()?>">
<input type="hidden" name="action" value="addnotify">

	<div class="control-group">
		<label class="control-label"><?php printMLText("user");?>:</label>
		<div class="controls">
			<select name="userid">
				<option value="-1"><?php printMLText("select_one");?>
				<?php
					if ($user->isAdmin()) {
						$allUsers = $dms->getAllUsers($sortusersinlist);
						foreach ($allUsers as $userObj) {
							if (!$userObj->isGuest() && ($document->getAccessMode($userObj) >= M_READ) && !in_array($userObj->getID(), $userNotifyIDs))
								print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "\n";
						}
					}
					elseif (!$user->isGuest() && !in_array($user->getID(), $userNotifyIDs)) {
						print "<option value=\"".$user->getID()."\">" . htmlspecialchars($user->getLogin() . " - " . $user->getFullName()) . "\n";
					}
				?>
			</select>
		</div>
	</div>


	<div class="control-group">
		<label class="control-label"><?php printMLText("group");?>:</label>

		<div class="controls">
			<select name="groupid">
				<option value="-1"><?php printMLText("select_one");?>
				<?php
					$allGroups = $dms->getAllGroups();
					foreach ($allGroups as $groupObj) {
						if (($user->isAdmin() || $groupObj->isMember($user,true)) && $document->getGroupAccessMode($groupObj) >= M_READ && !in_array($groupObj->getID(), $groupNotifyIDs)) {
							print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
						}
					}
				?>
			</select>
		</div>
	</div>

	<div class="controls">
		<input type="submit" class="btn" value="<?php printMLText("add") ?>">
	</div>

</form>

<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

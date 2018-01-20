<?php
/**
 * Implementation of UsrMgr view
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
 * Class which outputs the html page for UsrMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UsrMgr extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$seluser = $this->params['seluser'];
		$strictformcheck = $this->params['strictformcheck'];

		header('Content-Type: application/javascript');
?>
function checkForm()
{
	msg = new Array();

	if($("#login").val() == "") msg.push("<?php printMLText("js_no_login");?>");
	if(($("#userid").val() == "0") && ($("#pwd").val() == "")) msg.push("<?php printMLText("js_no_pwd");?>");
	if(($("#pwd").val() != $("#pwdconf").val())&&($("#pwd").val() != "")&&($("#pwd").val() != "")) msg.push("<?php printMLText("js_pwd_not_conf");?>");
	if($("#name").val() == "") msg.push("<?php printMLText("js_no_name");?>");
	if($("#email").val() == "") msg.push("<?php printMLText("js_no_email");?>");
<?php
	if ($strictformcheck) {
?>
	if($("#comment").val() == "") msg.push("<?php printMLText("js_no_comment");?>");
<?php
	}
?>
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
	$('body').on('submit', '#form', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
	$( "#selector" ).change(function() {
		$('div.ajax').trigger('update', {userid: $(this).val()});
	});
});
<?php
	} /* }}} */

	function info() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$seluser = $this->params['seluser'];
		$quota = $this->params['quota'];
		$workflowmode = $this->params['workflowmode'];

		if($seluser) {
			$sessionmgr = new SeedDMS_SessionMgr($dms->getDB());

			$this->contentHeading(getMLText("user_info"));
			echo "<table class=\"table table-condensed\">\n";
			echo "<tr><td>".getMLText('discspace')."</td><td>";
			if($quota) {
				$qt = $seluser->getQuota() ? $seluser->getQuota() : $quota;
				echo SeedDMS_Core_File::format_filesize($seluser->getUsedDiskSpace())." / ".SeedDMS_Core_File::format_filesize($qt)."<br />";
				echo $this->getProgressBar($seluser->getUsedDiskSpace(), $qt);
			} else {
				echo SeedDMS_Core_File::format_filesize($seluser->getUsedDiskSpace())."<br />";
			}
			echo "</td></tr>\n";
			$documents = $seluser->getDocuments();
			echo "<tr><td>".getMLText('documents')."</td><td>".count($documents)."</td></tr>\n";
			$documents = $seluser->getDocumentsLocked();
			echo "<tr><td>".getMLText('documents_locked')."</td><td>".count($documents)."</td></tr>\n";
			$categories = $seluser->getKeywordCategories();
			echo "<tr><td>".getMLText('personal_default_keywords')."</td><td>".count($categories)."</td></tr>\n";
			$dnot = $seluser->getNotifications(T_DOCUMENT);
			echo "<tr><td>".getMLText('documents_with_notification')."</td><td>".count($dnot)."</td></tr>\n";
			$fnot = $seluser->getNotifications(T_FOLDER);
			echo "<tr><td>".getMLText('folders_with_notification')."</td><td>".count($fnot)."</td></tr>\n";

			if($workflowmode == "traditional") {
				$resArr = $dms->getDocumentList('ReviewByMe', $seluser);
				if($resArr) {
					foreach ($resArr as $res) {
						$document = $dms->getDocument($res["id"]);
						if($document->getAccessMode($user) >= M_READ && $document->getLatestContent()) {
							$tasks['review'][] = array('id'=>$res['id'], 'name'=>$res['name']);
						}
					}
					echo "<tr><td>".getMLText('pending_reviews')."</td><td>".count($tasks['review'])."</td></tr>\n";
				}
			}
			if($workflowmode == "traditional" || $workflowmode == 'traditional_only_approval') {
				$resArr = $dms->getDocumentList('ApproveByMe', $seluser);
				if($resArr) {
					foreach ($resArr as $res) {
						$document = $dms->getDocument($res["id"]);
						if($document->getAccessMode($user) >= M_READ && $document->getLatestContent()) {
							$tasks['approval'][] = array('id'=>$res['id'], 'name'=>$res['name']);
						}
					}
					echo "<tr><td>".getMLText('pending_approvals')."</td><td>".count($tasks['approval'])."</td></tr>\n";
				}
				$resArr = $seluser->isMandatoryReviewerOf();
				if($resArr) {
					echo "<tr><td>".getMLText('mandatory_reviewers')."</td><td>".count($resArr)."</td></tr>\n";
				}
				$resArr = $seluser->isMandatoryApproverOf();
				if($resArr) {
					echo "<tr><td>".getMLText('mandatory_approvers')."</td><td>".count($resArr)."</td></tr>\n";
				}
			}
			if($workflowmode == 'advanced') {
				$workflows = $seluser->getWorkflowsInvolved();
				echo "<tr><td>".getMLText('workflows_involded')."</td><td>".count($workflows)."</td></tr>\n";
				$workflowStatus = $seluser->getWorkflowStatus();
				if($workflowStatus['u'])
					echo "<tr><td>".getMLText('pending_workflows')."</td><td>".count($workflowStatus['u'])."</td></tr>\n";
			}
			$sessions = $sessionmgr->getUserSessions($seluser);
			if($sessions) {
				$session = array_shift($sessions);
				echo "<tr><td>".getMLText('lastaccess')."</td><td>".getLongReadableDate($session->getLastAccess())."</td></tr>\n";
			}
			echo "</table>";

		}
	} /* }}} */

	function actionmenu() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$seluser = $this->params['seluser'];
		$quota = $this->params['quota'];
		$workflowmode = $this->params['workflowmode'];
		$undeluserids = $this->params['undeluserids'];
		$enableemail = $this->params['enableemail'];

		if($seluser) {
?>
<div class="btn-group">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
		<?php echo getMLText('action'); ?>
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
<?php
			if(!in_array($seluser->getID(), $undeluserids)) {
				echo '<li><a href="../out/out.RemoveUser.php?userid='.$seluser->getID().'"><i class="icon-remove"></i> '.getMLText("rm_user").'</a><li>';
			}
			echo '<li><a href="../out/out.RemoveUserFromProcesses.php?userid='.$seluser->getID().'"><i class="icon-remove"></i> '.getMLText("rm_user_from_processes").'</a></li>';
			echo '<li><a href="../out/out.TransferObjects.php?userid='.$seluser->getID().'"><i class="icon-share-alt"></i> '.getMLText("transfer_objects").'</a></li>';
			if($user->isAdmin() && $seluser->getID() != $user->getID())
				echo "<li><a href=\"../op/op.SubstituteUser.php?userid=".$seluser->getID()."&formtoken=".createFormKey('substituteuser')."\"><i class=\"icon-exchange\"></i> ".getMLText("substitute_user")."</a></li>\n";
			if($enableemail)
				echo '<li><a href="../out/out.SendLoginData.php?userid='.$seluser->getID().'"><i class="icon-envelope-alt"></i> '.getMLText("send_login_data").'</a></li>';
?>
	</ul>
</div>
<?php
		}
	} /* }}} */

	function form() { /* {{{ */
		$seluser = $this->params['seluser'];

		$this->showUserForm($seluser);
	} /* }}} */

	function showUserForm($currUser) { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$users = $this->params['allusers'];
		$groups = $this->params['allgroups'];
		$passwordstrength = $this->params['passwordstrength'];
		$passwordexpiration = $this->params['passwordexpiration'];
		$httproot = $this->params['httproot'];
		$enableuserimage = $this->params['enableuserimage'];
		$undeluserids = $this->params['undeluserids'];
		$workflowmode = $this->params['workflowmode'];
		$quota = $this->params['quota'];
?>
	<form action="../op/op.UsrMgr.php" method="post" enctype="multipart/form-data" name="form" id="form">
<?php
		if($currUser) {
			echo createHiddenFieldWithKey('edituser');
?>
	<input type="hidden" name="userid" id="userid" value="<?php print $currUser->getID();?>">
	<input type="hidden" name="action" value="edituser">
<?php
		} else {
			echo createHiddenFieldWithKey('adduser');
?>
	<input type="hidden" id="userid" value="0">
	<input type="hidden" name="action" value="adduser">
<?php
		}
?>
	<table class="table-condensed">
		<tr>
			<td><?php printMLText("user_login");?>:</td>
			<td><input type="text" name="login" id="login" value="<?php print $currUser ? htmlspecialchars($currUser->getLogin()) : "";?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("password");?>:</td>
			<td><input type="password" class="pwd" rel="strengthbar<?php echo $currUser ? $currUser->getID() : "0"; ?>" name="pwd" id="pwd"><?php if($currUser && $currUser->isGuest()) echo ' <input type="checkbox" name="clearpwd" value="1" /> '.getMLText('clear_password'); ?></td>
		</tr>
<?php
		if($passwordstrength > 0) {
?>
		<tr>
			<td><?php printMLText("password_strength");?>:</td>
			<td>
				<div id="strengthbar<?php echo $currUser ? $currUser->getID() : "0"; ?>" class="progress" style="width: 220px; height: 30px; margin-bottom: 8px;"><div class="bar bar-danger" style="width: 0%;"></div></div>
			</td>
		</tr>
<?php
		}
?>
		<tr>
			<td><?php printMLText("confirm_pwd");?>:</td>
			<td><input type="Password" name="pwdconf" id="pwdconf"></td>
		</tr>
<?php
	if($passwordexpiration > 0) {
?>
		<tr>
			<td><?php printMLText("password_expiration");?>:</td>
			<td><select name="pwdexpiration"><?php if($currUser) { ?><option value=""><?php printMLText("keep");?></option><?php } ?><option value="now"><?php printMLText("now");?></option><option value="<?php echo date('Y-m-d H:i:s', time()+$passwordexpiration*86400); ?>"><?php printMLText("according_settings");?></option><option value="never"><?php printMLText("never");?></option></select> <?php echo $currUser ? $currUser->getPwdExpiration() : ""; ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td><?php printMLText("user_name");?>:</td>
			<td><input type="text" name="name" id="name" value="<?php print $currUser ? htmlspecialchars($currUser->getFullName()) : "";?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("email");?>:</td>
			<td><input type="text" name="email" id="email" value="<?php print $currUser ? htmlspecialchars($currUser->getEmail()) : "";?>"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" id="comment" rows="4" cols="50"><?php print $currUser ? htmlspecialchars($currUser->getComment()) : "";?></textarea></td>
		</tr>
		<tr>
			<td><?php printMLText("role");?>:</td>
			<td><select name="role"><option value="<?php echo SeedDMS_Core_User::role_user ?>"><?php printMLText("role_user"); ?></option><option value="<?php echo SeedDMS_Core_User::role_admin ?>" <?php if($currUser && $currUser->getRole() == SeedDMS_Core_User::role_admin) echo "selected"; ?>><?php printMLText("role_admin"); ?></option><option value="<?php echo SeedDMS_Core_User::role_guest ?>" <?php if($currUser && $currUser->getRole() == SeedDMS_Core_User::role_guest) echo "selected"; ?>><?php printMLText("role_guest"); ?></option></select></td>
		</tr>
		<tr>
			<td><?php printMLText("groups");?>:</td>
			<td><select class="chzn-select" multiple="multiple" name="groups[]" data-placeholder="<?php printMLText('select_groups'); ?>">
<?php
		foreach($groups as $group) {
			echo '<option value="'.$group->getID().'"'.($currUser && $group->isMember($currUser) ? ' selected' : '').'>'.$group->getName().'</option>';
		}
?>
			</select></td>
		</tr>
		<tr>
			<td><?php printMLText("home_folder")?>:</td>
			<td><?php $this->printFolderChooser("form".($currUser ? $currUser->getId() : '0'), M_READ, -1, $currUser ? $dms->getFolder($currUser->getHomeFolder()) : 0, 'homefolder');?></td>
		</tr>
		<tr>
			<td><?php printMLText("quota");?>:</td>
			<td><input type="text" name="quota" value="<?php echo $currUser ? $currUser->getQuota() : ""; ?>">
<?php
	if($quota > 0)
		echo $this->warningMsg(getMLText('current_quota', array('quota'=>SeedDMS_Core_File::format_filesize($quota))));
	else
		echo $this->warningMsg(getMLText('quota_is_disabled'));
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("is_hidden");?>:</td>
			<td><input type="checkbox" name="ishidden" value="1"<?php print ($currUser && $currUser->isHidden() ? " checked='checked'" : "");?>></td>
		</tr>
		<tr>
			<td><?php printMLText("is_disabled");?>:</td>
			<td><input type="checkbox" name="isdisabled" value="1"<?php print ($currUser && $currUser->isDisabled() ? " checked='checked'" : "");?>></td>
		</tr>

<?php if ($enableuserimage){ ?>

<?php if ($currUser) { ?>
		<tr>
			<td><?php printMLText("user_image");?>:</td>
			<td>
<?php
					if ($currUser->hasImage())
						print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$currUser->getId()."\">";
					else
						printMLText("no_user_image");
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("new_user_image");?>:</td>
			<td>
<?php
	$this->printFileChooser('userfile', false, "image/jpeg");
?>
			</td>
		</tr>
<?php } else { ?>
		<tr>
			<td><?php printMLText("user_image");?>:</td>
			<td>
<?php
	$this->printFileChooser('userfile', false, 'image/jpeg');
?>
			</td>
		</tr>
<?php } ?>

<?php
		}
		if($workflowmode == "traditional" || $workflowmode == 'traditional_only_approval') {
		if($workflowmode == "traditional") {
?>

		<tr>
			<td colspan="2"><?php printMLText("reviewers");?>:</td>
		</tr>
		<tr>
			<td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
			</td>
			<td>
        <select class="chzn-select" name="usrReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_users'); ?>">
<?php

				if($currUser)
					$res=$currUser->getMandatoryReviewers();
				else
					$res = array();

				foreach ($users as $usr) {

					if ($usr->isGuest() || ($currUser && $usr->getID() == $currUser->getID()))
						continue;

					$checked=false;
					foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $checked=true;

					print "<option value=\"".$usr->getID()."\" ".($checked?"selected='selected' ":"").">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
			</td>
			<td>
        <select class="chzn-select" name="grpReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_groups'); ?>">
<?php
				foreach ($groups as $grp) {

					$checked=false;
					foreach ($res as $r) if ($r['reviewerGroupID']==$grp->getID()) $checked=true;

					print "<option value=\"".$grp->getID()."\" ".($checked?"selected='selected' ":"").">". htmlspecialchars($grp->getName())."</option>";
				}
?>
				</select>
			</td>
		</tr>
<?php
				}
?>
		<tr>
			<td colspan="2"><?php printMLText("approvers");?>:</td>
		</tr>
		<tr>
			<td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
			</td>
			<td>
        <select class="chzn-select" name="usrApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_users'); ?>">
<?php
				if($currUser)
					$res=$currUser->getMandatoryApprovers();
				else
					$res = array();
				foreach ($users as $usr) {
					if ($usr->isGuest() || ($currUser && $usr->getID() == $currUser->getID()))
						continue;

					$checked=false;
					foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $checked=true;

					print "<option value=\"".$usr->getID()."\" ".($checked?"selected='selected' ":"").">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
			</td>
			<td>
        <select class="chzn-select" name="grpApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_groups'); ?>">
<?php
				foreach ($groups as $grp) {

					$checked=false;
					foreach ($res as $r) if ($r['approverGroupID']==$grp->getID()) $checked=true;

					print "<option value=\"".$grp->getID()."\" ".($checked?"selected='selected' ":"").">". htmlspecialchars($grp->getName())."</option>";
				}
?>
				</select>
			</td>
		</tr>
<?php
		} else {
			$workflows = $dms->getAllWorkflows();
			if($workflows) {
?>
		<tr>
			<td>
				<div class="cbSelectTitle"><?php printMLText("workflow");?>:</div>
			</td>
			<td>
        <select class="chzn-select" name="workflows[]" multiple="multiple" data-placeholder="<?php printMLText('select_workflow'); ?>">
<?php
				print "<option value=\"\">"."</option>";
				$mandatoryworkflows = $currUser ? $currUser->getMandatoryWorkflows() : array();
				foreach ($workflows as $workflow) {
					print "<option value=\"".$workflow->getID()."\"";
					$checked = false;
					if($mandatoryworkflows) foreach($mandatoryworkflows as $mw) if($mw->getID() == $workflow->getID()) $checked = true;
					if($checked)
						echo " selected=\"selected\"";
					print ">". htmlspecialchars($workflow->getName())."</option>";
				}
?>
				</select>
			</td>
		</tr>
<?php
			}
		}
?>
		<tr>
			<td></td>
			<td><button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText($currUser ? "save" : "add_user")?></button></td>
		</tr>
	</table>
	</form>
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$seluser = $this->params['seluser'];
		$users = $this->params['allusers'];
		$groups = $this->params['allgroups'];
		$passwordstrength = $this->params['passwordstrength'];
		$passwordexpiration = $this->params['passwordexpiration'];
		$httproot = $this->params['httproot'];
		$enableuserimage = $this->params['enableuserimage'];
		$undeluserids = $this->params['undeluserids'];
		$workflowmode = $this->params['workflowmode'];
		$quota = $this->params['quota'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->contentHeading(getMLText("user_management"));
?>
<div class="row-fluid">
<div class="span4">
<form class="form-horizontal">
<select class="chzn-select" id="selector">
<option value="-1"><?php echo getMLText("choose_user")?></option>
<option value="0"><?php echo getMLText("add_user")?></option>
<?php
		foreach ($users as $currUser) {
			print "<option value=\"".$currUser->getID()."\" ".($seluser && $currUser->getID()==$seluser->getID() ? 'selected' : '')." data-subtitle=\"".htmlspecialchars($currUser->getFullName())."\">" . htmlspecialchars($currUser->getLogin()) . "</option>";
		}
?>
</select>
</form>
	<div class="ajax" style="margin-bottom: 15px;" data-view="UsrMgr" data-action="actionmenu" <?php echo ($seluser ? "data-query=\"userid=".$seluser->getID()."\"" : "") ?>></div>
	<div class="ajax" data-view="UsrMgr" data-action="info" <?php echo ($seluser ? "data-query=\"userid=".$seluser->getID()."\"" : "") ?>></div>
</div>

<div class="span8">
	<div class="well">
		<div class="ajax" data-view="UsrMgr" data-action="form" <?php echo ($seluser ? "data-query=\"userid=".$seluser->getID()."\"" : "") ?>></div>
	</div>
</div>
</div>

<?php
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

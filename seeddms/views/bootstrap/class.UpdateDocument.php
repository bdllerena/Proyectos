<?php
/**
 * Implementation of UpdateDocument view
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
 * Class which outputs the html page for UpdateDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UpdateDocument extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
		$dropfolderdir = $this->params['dropfolderdir'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$partitionsize = $this->params['partitionsize'];
		$maxuploadsize = $this->params['maxuploadsize'];
		header('Content-Type: application/javascript');
		$this->printDropFolderChooserJs("form1");
		$this->printSelectPresetButtonJs();
		$this->printInputPresetButtonJs();
		$this->printCheckboxPresetButtonJs();
		if($enablelargefileupload)
			$this->printFineUploaderJs('../op/op.UploadChunks.php', $partitionsize, $maxuploadsize, false);
?>
$(document).ready( function() {
	jQuery.validator.addMethod("alternatives", function(value, element, params) {
		if(value == '' && params.val() == '')
			return false;
		return true;
	}, "<?php printMLText("js_no_file");?>");
	/* The fineuploader validation is actually checking all fields that can contain
	 * a file to be uploaded. First checks if an alternative input field is set,
	 * second loops through the list of scheduled uploads, checking if at least one
	 * file will be submitted.
	 */
	jQuery.validator.addMethod("fineuploader", function(value, element, params) {
		if(params[1].val() != '')
			return true;
		uploader = params[0];
		arr = uploader.getUploads();
		for(var i in arr) {
			if(arr[i].status == 'submitted')
				return true;
		}
		return false;
	}, "<?php printMLText("js_no_file");?>");
	$("#form1").validate({
		debug: false,
		ignore: ":hidden:not(.do_validate)",
		invalidHandler: function(e, validator) {
			noty({
				text:  (validator.numberOfInvalids() == 1) ? "<?php printMLText("js_form_error");?>".replace('#', validator.numberOfInvalids()) : "<?php printMLText("js_form_errors");?>".replace('#', validator.numberOfInvalids()),
				type: 'error',
				dismissQueue: true,
				layout: 'topRight',
				theme: 'defaultTheme',
				timeout: 1500,
			});
		},
<?php
		if($enablelargefileupload) {
?>
		submitHandler: function(form) {
			/* fileuploader may not have any files if drop folder is used */
			if(userfileuploader.getUploads().length)
				userfileuploader.uploadStoredFiles();
			else
				form.submit();
		},
<?php
		}
?>
		rules: {
<?php
		if($enablelargefileupload) {
?>
			'userfile-fine-uploader-uuids': {
				fineuploader: [ userfileuploader, $('#dropfolderfileform1') ]
			}
<?php
		} else {
?>
			userfile: {
				alternatives: $('#dropfolderfileform1')
			},
			dropfolderfileform1: {
				 alternatives: $('#userfile')
			}
<?php
		}
?>
		},
		messages: {
			comment: "<?php printMLText("js_no_comment");?>",
		},
		errorPlacement: function( error, element ) {
			if ( element.is( ":file" ) ) {
				error.appendTo( element.parent().parent().parent());
console.log(element);
			} else {
				error.appendTo( element.parent());
			}
		}
	});
	$('#presetexpdate').on('change', function(ev){
		if($(this).val() == 'date')
			$('#control_expdate').show();
		else
			$('#control_expdate').hide();
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$strictformcheck = $this->params['strictformcheck'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$maxuploadsize = $this->params['maxuploadsize'];
		$enableadminrevapp = $this->params['enableadminrevapp'];
		$enableownerrevapp = $this->params['enableownerrevapp'];
		$enableselfrevapp = $this->params['enableselfrevapp'];
		$dropfolderdir = $this->params['dropfolderdir'];
		$workflowmode = $this->params['workflowmode'];
		$presetexpiration = $this->params['presetexpiration'];
		$documentid = $document->getId();

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');
		if($enablelargefileupload) {
			$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/fine-uploader/jquery.fine-uploader.min.js"></script>'."\n", 'js');
			$this->htmlAddHeader($this->getFineUploaderTemplate(), 'js');
		}

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);
		$this->contentHeading(getMLText("update_document"));

		if ($document->isLocked()) {

			$lockingUser = $document->getLockingUser();

			print "<div class=\"alert alert-warning\">";
			
			printMLText("update_locked_msg", array("username" => htmlspecialchars($lockingUser->getFullName()), "email" => $lockingUser->getEmail()));
			
			if ($lockingUser->getID() == $user->getID())
				printMLText("unlock_cause_locking_user");
			else if ($document->getAccessMode($user) == M_ALL)
				printMLText("unlock_cause_access_mode_all");
			else
			{
				printMLText("no_update_cause_locked");
				print "</div>";
				$this->contentEnd();
				$this->htmlEndPage();
				exit;
			}

			print "</div>";
		}

		$latestContent = $document->getLatestContent();
		$reviewStatus = $latestContent->getReviewStatus();
		$approvalStatus = $latestContent->getApprovalStatus();
		if($workflowmode == 'advanced') {
			if($status = $latestContent->getStatus()) {
				if($status["status"] == S_IN_WORKFLOW) {
					$this->warningMsg("The current version of this document is in a workflow. This will be interrupted and cannot be completed if you upload a new version.");
				}
			}
		}

		if($enablelargefileupload) {
			if($maxuploadsize) {
				$msg = getMLText("max_upload_size").": ".SeedDMS_Core_File::format_filesize($maxuploadsize);
			} else {
				$msg = '';
			}
		} else {
			$msg = getMLText("max_upload_size").": ".ini_get( "upload_max_filesize");
		}
		if(0 && $enablelargefileupload) {
			$msg .= "<p>".sprintf(getMLText('link_alt_updatedocument'), "out.AddMultiDocument.php?folderid=".$folder->getID()."&showtree=".showtree())."</p>";
		}
		if($msg)
			$this->warningMsg($msg);
		$this->contentContainerStart();
?>

<form action="../op/op.UpdateDocument.php" enctype="multipart/form-data" method="post" name="form1" id="form1">
	<?php echo createHiddenFieldWithKey('updatedocument'); ?>
	<input type="hidden" name="documentid" value="<?php print $document->getID(); ?>">
	<table class="table-condensed">
	
		<tr>
			<td><?php printMLText("local_file");?>:</td>
			<td>
<?php
		if($enablelargefileupload)
			$this->printFineUploaderHtml();
		else
			$this->printFileChooser('userfile', false);
?>
			</td>
		</tr>
<?php if($dropfolderdir) { ?>
		<tr>
			<td><?php printMLText("dropfolder_file");?>:</td>
			<td><?php $this->printDropFolderChooserHtml("form1");?></td>
		</tr>
<?php } ?>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td class="standardText">
				<textarea name="comment" rows="4" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>></textarea>
			</td>
		</tr>
<?php
			if($presetexpiration) {
				if(!($expts = strtotime($presetexpiration)))
					$expts = false;
			} else {
				$expts = false;
			}
?>
		<tr>
			<td><?php printMLText("preset_expires");?>:</td>
			<td>
				<select class="span6" name="presetexpdate" id="presetexpdate">
					<option value="never"><?php printMLText('does_not_expire');?></option>
					<option value="date"<?php echo ($expts != '' ? " selected" : ""); ?>><?php printMLText('expire_by_date');?></option>
					<option value="1w"><?php printMLText('expire_in_1w');?></option>
					<option value="1m"><?php printMLText('expire_in_1m');?></option>
					<option value="1y"><?php printMLText('expire_in_1y');?></option>
					<option value="2y"><?php printMLText('expire_in_2y');?></option>
				</select>
			</td>
		</tr>
		<tr id="control_expdate" <?php echo ($expts == false ? 'style="display: none;"' : ''); ?>>
			<td><?php printMLText("expires");?>:</td>
			<td class="standardText">
        <span class="input-append date span12" id="expirationdate" data-date="<?php echo ($expts ? date('Y-m-d', $expts) : ''); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
          <input class="span6" size="16" name="expdate" type="text" value="<?php echo ($expts ? date('Y-m-d', $expts) : ''); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
			</td>
		</tr>
<?php
	$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_documentcontent, SeedDMS_Core_AttributeDefinition::objtype_all));
	if($attrdefs) {
		foreach($attrdefs as $attrdef) {
			$arr = $this->callHook('editDocumentContentAttribute', $document, $attrdef);
			if(is_array($arr)) {
				if($arr) {
					echo "<tr>";
					echo "<td>".$arr[0].":</td>";
					echo "<td>".$arr[1]."</td>";
					echo "</tr>";
				}
			} else {
?>
    <tr>
	    <td><?php echo htmlspecialchars($attrdef->getName()); ?>:</td>
			<td><?php $this->printAttributeEditField($attrdef, '', 'attributes_version') ?>
<?php
			if($latestContent->getAttributeValue($attrdef)) {
				switch($attrdef->getType()) {
				case SeedDMS_Core_AttributeDefinition::type_string:
				case SeedDMS_Core_AttributeDefinition::type_date:
				case SeedDMS_Core_AttributeDefinition::type_int:
				case SeedDMS_Core_AttributeDefinition::type_float:
					$this->printInputPresetButtonHtml('attributes_version_'.$attrdef->getID(), $latestContent->getAttributeValue($attrdef), $attrdef->getValueSetSeparator());
					break;
				case SeedDMS_Core_AttributeDefinition::type_boolean:
					$this->printCheckboxPresetButtonHtml('attributes_version_'.$attrdef->getID(), $latestContent->getAttributeValue($attrdef));
					break;
				}
//				print_r($latestContent->getAttributeValue($attrdef));
			}
?></td>
    </tr>
<?php
			}
		}
	}

	$arrs = $this->callHook('addDocumentContentAttributes', $folder);
	if(is_array($arrs)) {
		foreach($arrs as $arr) {
			echo "<tr>";
			echo "<td>".$arr[0].":</td>";
			echo "<td>".$arr[1]."</td>";
			echo "</tr>";
		}
	}

	if($workflowmode == 'traditional' || $workflowmode == 'traditional_only_approval') {
		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $folder->getReadAccessList($enableadminrevapp, $enableownerrevapp);
		if($workflowmode != 'traditional_only_approval') {
?>
		<tr>
			<td colspan="2">
				<?php $this->contentSubHeading(getMLText("assign_reviewers")); ?>
      </td>
    </tr>
    <tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
      </td>
			<td>
        <select id="IndReviewer" class="chzn-select span9" name="indReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_reviewers'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<?php
				$res=$user->getMandatoryReviewers();
				foreach ($docAccess["users"] as $usr) {
					if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerUserID']==$usr->getID()) $mandatory=true;

					if ($mandatory) print "<option disabled=\"disabled\" value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
					else print "<option value=\"".$usr->getID()."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
				</select>
<?php
				$tmp = array();
				foreach($reviewStatus as $r) {
					if($r['type'] == 0) {
					 	if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['reviewerUserID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("IndReviewer", $tmp);
				}
				/* List all mandatory reviewers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['reviewerUserID'] > 0) {
							$u = $dms->getUser($r['reviewerUserID']);
							$tmp[] =  htmlspecialchars($u->getFullName().' ('.$u->getLogin().')');
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_reviewers').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}
				/* Check for mandatory reviewer without access */
				foreach($res as $r) {
					if($r['reviewerUserID']) {
						$hasAccess = false;
						foreach ($docAccess["users"] as $usr) {
							if ($r['reviewerUserID']==$usr->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessUser = $dms->getUser($r['reviewerUserID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_reviewer_no_access", array('user'=>htmlspecialchars($noAccessUser->getFullName()." (".$noAccessUser->getLogin().")")))."</div>";
						}
					}
				}
?>
      </td>
    </tr>
    <tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
      </td>
			<td>
        <select id="GrpReviewer" class="chzn-select span9" name="grpReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_reviewers'); ?>" data-no_results_text="<?php printMLText('unknown_group'); ?>">
<?php
				foreach ($docAccess["groups"] as $grp) {
				
					$mandatory=false;
					foreach ($res as $r) if ($r['reviewerGroupID']==$grp->getID()) $mandatory=true;	

					if ($mandatory) print "<option value=\"".$grp->getID()."\" disabled=\"disabled\">".htmlspecialchars($grp->getName())."</option>";
					else print "<option value=\"".$grp->getID()."\">".htmlspecialchars($grp->getName())."</option>";
				}
?>
			</select>
<?php
				$tmp = array();
				foreach($reviewStatus as $r) {
					if($r['type'] == 1) {
						if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['reviewerGroupID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("GrpReviewer", $tmp);
				}
				/* List all mandatory groups of reviewers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['reviewerGroupID'] > 0) {
							$u = $dms->getGroup($r['reviewerGroupID']);
							$tmp[] =  htmlspecialchars($u->getName());
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_reviewergroups').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}

				/* Check for mandatory reviewer group without access */
				foreach($res as $r) {
					if ($r['reviewerGroupID']) {
						$hasAccess = false;
						foreach ($docAccess["groups"] as $grp) {
							if ($r['reviewerGroupID']==$grp->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessGroup = $dms->getGroup($r['reviewerGroupID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_reviewergroup_no_access", array('group'=>htmlspecialchars($noAccessGroup->getName())))."</div>";
						}
					}
				}
?>
      </td>
		</tr>
<?php } ?>
    <tr>
			<td colspan=2>
				<?php $this->contentSubHeading(getMLText("assign_approvers")); ?>	
      </td>
    </tr>
    <tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
      </td>
      <td>
        <select id="IndApprover" class="chzn-select span9" name="indApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_approvers'); ?>" data-no_results_text="<?php printMLText('unknown_owner'); ?>">
<?php
				$res=$user->getMandatoryApprovers();
				foreach ($docAccess["users"] as $usr) {
					if (!$enableselfrevapp && $usr->getID()==$user->getID()) continue; 

					$mandatory=false;
					foreach ($res as $r) if ($r['approverUserID']==$usr->getID()) $mandatory=true;
					
					if ($mandatory) print "<option value=\"". $usr->getID() ."\" disabled='disabled'>". htmlspecialchars($usr->getFullName())."</option>";
					else print "<option value=\"". $usr->getID() ."\">". htmlspecialchars($usr->getLogin()." - ".$usr->getFullName())."</option>";
				}
?>
        </select>
<?php
				$tmp = array();
				foreach($approvalStatus as $r) {
					if($r['type'] == 0) {
						if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['approverUserID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("IndApprover", $tmp);
				}
				/* List all mandatory approvers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['approverUserID'] > 0) {
							$u = $dms->getUser($r['approverUserID']);
							$tmp[] =  htmlspecialchars($u->getFullName().' ('.$u->getLogin().')');
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_approvers').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}

				/* Check for mandatory approvers without access */
				foreach($res as $r) {
					if($r['approverUserID']) {
						$hasAccess = false;
						foreach ($docAccess["users"] as $usr) {
							if ($r['approverUserID']==$usr->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessUser = $dms->getUser($r['approverUserID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_approver_no_access", array('user'=>htmlspecialchars($noAccessUser->getFullName()." (".$noAccessUser->getLogin().")")))."</div>";
						}
					}
				}
?>
      </td>
    </tr>
      <td>
				<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
      </td>
      <td>
        <select id="GrpApprover" class="chzn-select span9" name="grpApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_approvers'); ?>" data-no_results_text="<?php printMLText('unknown_group'); ?>">
<?php
				foreach ($docAccess["groups"] as $grp) {
				
					$mandatory=false;
					foreach ($res as $r) if ($r['approverGroupID']==$grp->getID()) $mandatory=true;	

					if ($mandatory) print "<option value=\"". $grp->getID() ."\" disabled=\"disabled\">".htmlspecialchars($grp->getName())."</option>";
					else print "<option value=\"". $grp->getID() ."\">".htmlspecialchars($grp->getName())."</option>";

				}
?>
        </select>
<?php
				$tmp = array();
				foreach($approvalStatus as $r) {
					if($r['type'] == 1) {
						if($res) {
							$mandatory=false;
							foreach ($res as $rr)
								if ($rr['approverGroupID']==$r['required']) {
									$mandatory=true;
								}
							if(!$mandatory)
								$tmp[] = $r['required'];
						} else {
							$tmp[] = $r['required'];
						}
					}
				}
				if($tmp) {
					$this->printSelectPresetButtonHtml("GrpApprover", $tmp);
				}
				/* List all mandatory groups of approvers */
				if($res) {
					$tmp = array();
					foreach ($res as $r) {
						if($r['approverGroupID'] > 0) {
							$u = $dms->getGroup($r['approverGroupID']);
							$tmp[] =  htmlspecialchars($u->getName());
						}
					}
					if($tmp) {
						echo '<div class="mandatories"><span>'.getMLText('mandatory_approvergroups').':</span> ';
						echo implode(', ', $tmp);
						echo "</div>\n";
					}
				}

				/* Check for mandatory approver groups without access */
				foreach($res as $r) {
					if ($r['approverGroupID']) {
						$hasAccess = false;
						foreach ($docAccess["groups"] as $grp) {
							if ($r['approverGroupID']==$grp->getID())
								$hasAccess = true;
						}
						if(!$hasAccess) {
							$noAccessGroup = $dms->getGroup($r['approverGroupID']);
							echo "<div class=\"alert alert-warning\">".getMLText("mandatory_approvergroup_no_access", array('group'=>htmlspecialchars($noAccessGroup->getName())))."</div>";
						}
					}
				}
?>
			</td>
			</tr>
		<tr>
			<td colspan="2"><div class="alert"><?php printMLText("add_doc_reviewer_approver_warning")?></div></td>
		</tr>
<?php
	} else {
?>
		<tr>	
      <td>
			<div class="cbSelectTitle"><?php printMLText("workflow");?>:</div>
      </td>
      <td>
<?php
				$mandatoryworkflows = $user->getMandatoryWorkflows();
				if($mandatoryworkflows) {
					if(count($mandatoryworkflows) == 1) {
?>
				<?php echo htmlspecialchars($mandatoryworkflows[0]->getName()); ?>
				<input type="hidden" name="workflow" value="<?php echo $mandatoryworkflows[0]->getID(); ?>">
<?php
					} else {
?>
        <select class="_chzn-select-deselect span9" name="workflow" data-placeholder="<?php printMLText('select_workflow'); ?>">
<?php
					$curworkflow = $latestContent->getWorkflow();
					foreach ($mandatoryworkflows as $workflow) {
						print "<option value=\"".$workflow->getID()."\"";
						if($curworkflow && $curworkflow->getID() == $workflow->getID())
							echo " selected=\"selected\"";
						print ">". htmlspecialchars($workflow->getName())."</option>";
					}
?>
        </select>
<?php
					}
				} else {
?>
        <select class="_chzn-select-deselect span9" name="workflow" data-placeholder="<?php printMLText('select_workflow'); ?>">
<?php
					$workflows=$dms->getAllWorkflows();
					print "<option value=\"\">"."</option>";
					foreach ($workflows as $workflow) {
						print "<option value=\"".$workflow->getID()."\"";
						print ">". htmlspecialchars($workflow->getName())."</option>";
					}
?>
        </select>
<?php
				}
?>
      </td>
    </tr>
		<tr>	
      <td colspan="2">
			<?php $this->warningMsg(getMLText("add_doc_workflow_warning")); ?>
      </td>
		</tr>	
<?php
	}
?>
		<tr>
			<td></td>
			<td><input type="submit" class="btn" value="<?php printMLText("update_document")?>"></td>
		</tr>
	</table>
</form>

<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

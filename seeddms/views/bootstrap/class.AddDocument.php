<?php
/**
 * Implementation of AddDocument view
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
 * Class which outputs the html page for AddDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddDocument extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$dropfolderdir = $this->params['dropfolderdir'];
		$partitionsize = $this->params['partitionsize'];
		$maxuploadsize = $this->params['maxuploadsize'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$enablemultiupload = $this->params['enablemultiupload'];
		header('Content-Type: application/javascript; charset=UTF-8');

		if($enablelargefileupload) {
			$this->printFineUploaderJs('../op/op.UploadChunks.php', $partitionsize, $maxuploadsize, $enablemultiupload);
		}
?>
$(document).ready(function() {
	$('#new-file').click(function(event) {
		tttttt = $("#userfile-upload-file").clone().appendTo("#userfile-upload-files").removeAttr("id");
		tttttt.children('div').children('input').val('');
		tttttt.children('div').children('span').children('input').val('');
	});
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
			'userfile[]': {
				alternatives: $('#dropfolderfileform1')
			},
			dropfolderfileform1: {
				 alternatives: $("#userfile") //$(".btn-file input")
			}
<?php
		}
?>
		},
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>",
			keywords: "<?php printMLText("js_no_keywords");?>"
		},
		errorPlacement: function( error, element ) {
			if ( element.is( ":file" ) ) {
				error.appendTo( element.parent().parent().parent());
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
			$this->printKeywordChooserJs("form1");
			if($dropfolderdir) {
				$this->printDropFolderChooserJs("form1");
			}
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$enablelargefileupload = $this->params['enablelargefileupload'];
		$enablemultiupload = $this->params['enablemultiupload'];
		$enableadminrevapp = $this->params['enableadminrevapp'];
		$enableownerrevapp = $this->params['enableownerrevapp'];
		$enableselfrevapp = $this->params['enableselfrevapp'];
		$strictformcheck = $this->params['strictformcheck'];
		$dropfolderdir = $this->params['dropfolderdir'];
		$dropfolderfile = $this->params['dropfolderfile'];
		$workflowmode = $this->params['workflowmode'];
		$presetexpiration = $this->params['presetexpiration'];
		$sortusersinlist = $this->params['sortusersinlist'];
		$orderby = $this->params['orderby'];
		$folderid = $folder->getId();

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');
		if($enablelargefileupload) {
			$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/fine-uploader/jquery.fine-uploader.min.js"></script>'."\n", 'js');
			$this->htmlAddHeader($this->getFineUploaderTemplate(), 'js');
		}

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true), "view_folder", $folder);
		
		$msg = getMLText("max_upload_size").": ".ini_get( "upload_max_filesize");
		$this->warningMsg($msg);
		$this->contentHeading(getMLText("add_document"));
		$this->contentContainerStart();
		
		// Retrieve a list of all users and groups that have review / approve
		// privileges.
		$docAccess = $folder->getReadAccessList($enableadminrevapp, $enableownerrevapp);
?>
		<form action="../op/op.AddDocument.php" enctype="multipart/form-data" method="post" id="form1" name="form1">
		<?php echo createHiddenFieldWithKey('adddocument'); ?>
		<input type="hidden" name="folderid" value="<?php print $folderid; ?>">
		<input type="hidden" name="showtree" value="<?php echo showtree();?>">
		<table class="table-condensed">
		<tr>
			<td>
		<?php $this->contentSubHeading(getMLText("document_infos")); ?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" size="60"></td>
		</tr>
		<tr>
			<td><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" rows="3" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>></textarea></td>
		</tr>
		<tr>
			<td><?php printMLText("keywords");?>:</td>
			<td><?php $this->printKeywordChooserHtml("form1");?></td>
		</tr>
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td>
        <select class="chzn-select" name="categories[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<?php
			$categories = $dms->getDocumentCategories();
			foreach($categories as $category) {
				echo "<option value=\"".$category->getID()."\"";
				echo ">".$category->getName()."</option>";	
			}
?>
				</select>
      </td>
		</tr>
		<tr>
			<td><?php printMLText("sequence");?>:</td>
			<td><?php $this->printSequenceChooser($folder->getDocuments('s')); if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off'); ?></td>
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
				<select class="span3" name="presetexpdate" id="presetexpdate">
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
			<td>
        <span class="input-append date span6" id="expirationdate" data-date="<?php echo ($expts ? date('Y-m-d', $expts) : ''); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>" data-checkbox="#expires">
          <input class="span3" size="16" name="expdate" type="text" value="<?php echo ($expts ? date('Y-m-d', $expts) : ''); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
			</td>
		</tr>

<?php if($user->isAdmin()) { ?>
		<tr>
			<td><?php printMLText("owner");?>:</td>
			<td>
				<select class="chzn-select" name="ownerid">
<?php
	$allUsers = $dms->getAllUsers($sortusersinlist);
	foreach ($allUsers as $currUser) {
		if ($currUser->isGuest())
			continue;
		print "<option value=\"".$currUser->getID()."\" ".($currUser->getID()==$user->getID() ? 'selected' : '')." data-subtitle=\"".htmlspecialchars($currUser->getFullName())."\"";
		print ">" . htmlspecialchars($currUser->getLogin()) . "</option>\n";
	}
?>
				</select>
			</td>
		</tr>
<?php } ?>
<?php
			$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_document, SeedDMS_Core_AttributeDefinition::objtype_all));
			if($attrdefs) {
				foreach($attrdefs as $attrdef) {
					$arr = $this->callHook('addDocumentAttribute', null, $attrdef);
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
			<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
			<td><?php $this->printAttributeEditField($attrdef, '') ?></td>
		</tr>
<?php
					}
				}
			}
			$arrs = $this->callHook('addDocumentAttributes', $folder);
			if(is_array($arrs)) {
				foreach($arrs as $arr) {
					echo "<tr>";
					echo "<td>".$arr[0].":</td>";
					echo "<td>".$arr[1]."</td>";
					echo "</tr>";
				}
			}
?>
		<tr>
			<td>
		<?php $this->contentSubHeading(getMLText("version_info")); ?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("version");?>:</td>
			<td><input type="text" name="reqversion" value="1"></td>
		</tr>
		<tr>
			<td><?php printMLText("local_file");?>:</td>
			<td>
<?php
		if($enablelargefileupload)
			$this->printFineUploaderHtml();
		else {
			$this->printFileChooser('userfile[]', false);
			if($enablemultiupload) {
?>
			<a class="" id="new-file"><?php printMLtext("add_multiple_files") ?></a>
<?php
			}
		}
?>
			</td>
		</tr>
<?php if($dropfolderdir) { ?>
		<tr>
			<td><?php printMLText("dropfolder_file");?>:</td>
			<td><?php $this->printDropFolderChooserHtml("form1", $dropfolderfile);?></td>
		</tr>
<?php } ?>
		<tr>
			<td><?php printMLText("comment_for_current_version");?>:</td>
			<td><textarea name="version_comment" rows="3" cols="80"></textarea><br />
			<label class="checkbox inline"><input type="checkbox" name="use_comment" value="1" /> <?php printMLText("use_comment_of_document"); ?></label></td>
		</tr>
<?php
			$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_documentcontent, SeedDMS_Core_AttributeDefinition::objtype_all));
			if($attrdefs) {
				foreach($attrdefs as $attrdef) {
					$arr = $this->callHook('addDocumentContentAttribute', null, $attrdef);
					if(is_array($arr)) {
						echo "<tr>";
						echo "<td>".$arr[0].":</td>";
						echo "<td>".$arr[1]."</td>";
						echo "</tr>";
					} else {
?>
		<tr>
			<td><?php echo htmlspecialchars($attrdef->getName()); ?></td>
			<td><?php $this->printAttributeEditField($attrdef, '', 'attributes_version') ?></td>
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

		if($workflowmode == 'advanced') {
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
					foreach ($mandatoryworkflows as $workflow) {
						print "<option value=\"".$workflow->getID()."\"";
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
		} else {
			if($workflowmode == 'traditional') {
?>
		<tr>
      <td>
		<?php $this->contentSubHeading(getMLText("assign_reviewers")); ?>
      </td>
		</tr>	
		<tr>	
      <td>
			<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
      </td>
      <td>
<?php
				$res=$user->getMandatoryReviewers();
?>
        <select class="chzn-select span9" name="indReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_reviewers'); ?>">
<?php
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
        <select class="chzn-select span9" name="grpReviewers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_reviewers'); ?>">
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
        <td>
		<?php $this->contentSubHeading(getMLText("assign_approvers")); ?>
        </td>
		  </tr>	
		
		  <tr>	
        <td>
			<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
        </td>
				<td>
      <select class="chzn-select span9" name="indApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_approvers'); ?>">
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
		  <tr>	
        <td>
			<div class="cbSelectTitle"><?php printMLText("groups");?>:</div>
        </td>
        <td>
      <select class="chzn-select span9" name="grpApprovers[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_approvers'); ?>">
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
        <td colspan="2">
			<div class="alert"><?php printMLText("add_doc_reviewer_approver_warning")?></div>
        </td>
			</tr>	
<?php
		}
?>
		  <tr>	
        <td>
		<?php $this->contentSubHeading(getMLText("add_document_notify")); ?>
        </td>
			</tr>	

		  <tr>	
        <td>
			<div class="cbSelectTitle"><?php printMLText("individuals");?>:</div>
        </td>
        <td>
				<select class="chzn-select span9" name="notification_users[]" multiple="multiple" data-placeholder="<?php printMLText('select_ind_notification'); ?>">
<?php
						$allUsers = $dms->getAllUsers($sortusersinlist);
						foreach ($allUsers as $userObj) {
							if (!$userObj->isGuest() && $folder->getAccessMode($userObj) >= M_READ)
								print "<option value=\"".$userObj->getID()."\">" . htmlspecialchars($userObj->getLogin() . " - " . $userObj->getFullName()) . "\n";
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
				<select class="chzn-select span9" name="notification_groups[]" multiple="multiple" data-placeholder="<?php printMLText('select_grp_notification'); ?>">
<?php
						$allGroups = $dms->getAllGroups();
						foreach ($allGroups as $groupObj) {
							if ($folder->getGroupAccessMode($groupObj) >= M_READ)
								print "<option value=\"".$groupObj->getID()."\">" . htmlspecialchars($groupObj->getName()) . "\n";
						}
?>
				</select>
				</td>
			</tr>
		</table>

			<p><input type="submit" class="btn" value="<?php printMLText("add_document");?>"></p>
		</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();

	} /* }}} */
}
?>

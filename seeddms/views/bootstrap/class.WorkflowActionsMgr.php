<?php
/**
 * Implementation of WorkspaceActionsMgr view
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
 * Class which outputs the html page for WorkspaceActionsMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_WorkflowActionsMgr extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript; charset=UTF-8');
?>
function checkForm(num)
{
	msg = new Array()

	if($("#name").val() == "") msg.push("<?php printMLText("js_no_name");?>");
	if (msg != "")
	{
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
		$('div.ajax').trigger('update', {workflowactionid: $(this).val()});
	});
});
<?php
	} /* }}} */

	function info() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$selworkflowaction = $this->params['selworkflowaction'];

		if($selworkflowaction) {
			if($selworkflowaction->isUsed()) {
				$transitions = $selworkflowaction->getTransitions();
				if($transitions) {
					echo "<table class=\"table table-condensed\">";
					echo "<thead><tr><th>".getMLText('workflow')."</th><th>".getMLText('previous_state')."</th><th>".getMLText('next_state')."</th></tr></thead>\n";
					echo "<tbody>";
					foreach($transitions as $transition) {
						$state = $transition->getState();
						$nextstate = $transition->getNextState();
						$docstatus = $nextstate->getDocumentStatus();
						$workflow = $transition->getWorkflow();
						echo "<tr>";
						echo "<td>";
						echo $workflow->getName();
						echo "</td><td>";
						echo '<i class="icon-circle'.($workflow->getInitState()->getId() == $state->getId() ? ' initstate' : ' in-workflow').'"></i> '.$state->getName();
						echo "</td><td>";
						echo '<i class="icon-circle'.($docstatus == S_RELEASED ? ' released' : ($docstatus == S_REJECTED ? ' rejected' : ' in-workflow')).'"></i> '.$nextstate->getName();
						echo "</td></tr>";
					}
					echo "</tbody>";
					echo "</table>";
				}
			}
		}
	} /* }}} */

	function showWorkflowActionForm($action) { /* {{{ */
		if($action) {
			if($action->isUsed()) {
?>
				<p><?php echo getMLText('workflow_action_in_use') ?></p>
<?php
			} else {
?>
<form class="form-inline" action="../op/op.RemoveWorkflowAction.php" method="post">
  <?php echo createHiddenFieldWithKey('removeworkflowaction'); ?>
	<input type="hidden" name="workflowactionid" value="<?php print $action->getID();?>">
	<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("rm_workflow_action");?></button>
</form>
<?php
			}
		}
?>
<form action="../op/op.WorkflowActionsMgr.php" method="post" class="form-horizontal">
<?php
		if($action) {
			echo createHiddenFieldWithKey('editworkflowaction');
?>
	<input type="hidden" name="workflowactionid" value="<?php print $action->getID();?>">
	<input type="hidden" name="action" value="editworkflowaction">
<?php
		} else {
			echo createHiddenFieldWithKey('addworkflowaction');
?>
			<input type="hidden" name="action" value="addworkflowaction">
<?php
		}
?>
	<div class="control-group">
		<label class="control-label" for="login"><?php printMLText("workflow_action_name");?>:</label>
		<div class="controls">
			<input type="text" id="name" name="name" value="<?php print $action ? htmlspecialchars($action->getName()) : '';?>">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="login"></label>
		<div class="controls">
			<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button>
		</div>
	</div>
	</form>
<?php
	} /* }}} */

	function form() { /* {{{ */
		$selworkflowaction = $this->params['selworkflowaction'];

		$this->showWorkflowActionForm($selworkflowaction);
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$selworkflowaction = $this->params['selworkflowaction'];

		$workflowactions = $dms->getAllWorkflowActions();

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("workflow_actions_management"));
?>

<div class="row-fluid">
<div class="span4">
<div class="well">
<form class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="login"><?php printMLText("selection");?>:</label>
		<div class="controls">
<select id="selector" class="span9">
<option value="-1"><?php echo getMLText("choose_workflow_action")?>
<option value="0"><?php echo getMLText("add_workflow_action")?>
<?php
		foreach ($workflowactions as $currWorkflowAction) {
			print "<option value=\"".$currWorkflowAction->getID()."\" ".($selworkflowaction && $currWorkflowAction->getID()==$selworkflowaction->getID() ? 'selected' : '').">" . htmlspecialchars($currWorkflowAction->getName());
		}
?>
</select>
		</div>
	</div>
</form>
</div>
<div class="ajax" data-view="WorkflowActionsMgr" data-action="info" <?php echo ($selworkflowaction ? "data-query=\"workflowactionid=".$selworkflowaction->getID()."\"" : "") ?>></div>
</div>

<div class="span8">
	<div class="well">
		<div class="ajax" data-view="WorkflowActionsMgr" data-action="form" <?php echo ($selworkflowaction ? "data-query=\"workflowactionid=".$selworkflowaction->getID()."\"" : "") ?>></div>
	</div>
</div>

</div>
<?php
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

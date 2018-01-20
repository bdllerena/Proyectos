<?php
/**
 * Implementation of OverrideContentStatus view
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
 * Class which outputs the html page for OverrideContentStatus view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_OverrideContentStatus extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript; charset=UTF-8');
?>
function checkForm()
{
	msg = new Array();
	if (document.form1.overrideStatus.value == "") msg.push("<?php printMLText("js_no_override_status");?>");
	if (document.form1.comment.value == "") msg.push("<?php printMLText("js_no_comment");?>");
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
$(document).ready(function() {
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
		$content = $this->params['version'];

		$overallStatus = $content->getStatus();
		$reviewStatus = $content->getReviewStatus();
		$approvalStatus = $content->getApprovalStatus();

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

		$this->contentHeading(getMLText("change_status"));

		$this->contentContainerStart();

// Display the Review form.
?>
<form class="form-horizontal" method="post" action="../op/op.OverrideContentStatus.php" id="form1" name="form1">
	<div class="control-group">
		<label class="control-label"><?php echo(printMLText("comment"));?>:</label>
		<div class="controls">
			<textarea name="comment" cols="40" rows="4"></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php echo(printMLText("status")); ?>:</label>
		<div class="controls">
			<select name="overrideStatus">
				<option value=''></option>
	<?php

			if ($overallStatus["status"] == S_OBSOLETE) echo "<option value='".S_RELEASED."'>".getOverallStatusText(S_RELEASED)."</option>";
			if ($overallStatus["status"] == S_RELEASED) echo "<option value='".S_OBSOLETE."'>".getOverallStatusText(S_OBSOLETE)."</option>";

	?>
	</select>
	</div></div>
	<div class="controls">
		<input type='hidden' name='documentid' value='<?php echo $document->getID() ?>'/>
		<input type='hidden' name='version' value='<?php echo $content->getVersion() ?>'/>
		<input type='submit' class="btn" name='overrideContentStatus' value='<?php echo(printMLText("update")); ?>'/>
	</div>
</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

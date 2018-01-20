<?php
/**
 * Implementation of SetExpires view
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
 * Class which outputs the html page for SetExpires view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SetExpires extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
?>
$(document).ready( function() {
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

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);
		$this->contentHeading(getMLText("set_expiry"));
		$this->contentContainerStart();

		if($document->expires())
			$expdate = date('Y-m-d', $document->getExpires());
		else
			$expdate = '';
?>

<form class="form-horizontal" action="../op/op.SetExpires.php" method="post">
<input type="hidden" name="documentid" value="<?php print $document->getID();?>">
	<div class="control-group">
		<label class="control-label" for="login"><?php printMLText("preset_expires");?>:</label>
		<div class="controls">
			<select name="presetexpdate" id="presetexpdate">
				<option value="never"><?php printMLText('does_not_expire');?></option>
				<option value="date"<?php echo ($expdate != '' ? " selected" : ""); ?>><?php printMLText('expire_by_date');?></option>
				<option value="1w"><?php printMLText('expire_in_1w');?></option>
				<option value="1m"><?php printMLText('expire_in_1m');?></option>
				<option value="1y"><?php printMLText('expire_in_1y');?></option>
				<option value="2y"><?php printMLText('expire_in_2y');?></option>
			</select>
		</div>
	</div>
	<div class="control-group" id="control_expdate">
		<label class="control-label"><?php printMLText("expires");?>:</label>
		<div class="controls">
    <span class="input-append date span12" id="expirationdate" data-date="<?php echo $expdate; ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
      <input class="span3" name="expdate" type="text" value="<?php echo $expdate; ?>">
      <span class="add-on"><i class="icon-calendar"></i></span>
    </span>
		</div>
	</div>
	<div class="controls">
		<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save") ?></button>
	</div>
</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

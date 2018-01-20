<?php
/**
 * Implementation of AddSubFolder view
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
 * Class which outputs the html page for AddSubFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_AddSubFolder extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
		header('Content-Type: application/javascript');
?>
$(document).ready( function() {
	$("#form1").validate({
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
		highlight: function(e, errorClass, validClass) {
			$(e).parent().parent().removeClass(validClass).addClass(errorClass);
		},
		unhighlight: function(e, errorClass, validClass) {
			$(e).parent().parent().removeClass(errorClass).addClass(validClass);
		},
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>"
		},
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$strictformcheck = $this->params['strictformcheck'];
		$orderby = $this->params['orderby'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("folder_title", array("foldername" => htmlspecialchars($folder->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true), "view_folder", $folder);
		$this->contentHeading(getMLText("add_subfolder"));
		$this->contentContainerStart();
?>

<form class="form-horizontal" action="../op/op.AddSubFolder.php" id="form1" name="form1" method="post">
	<?php echo createHiddenFieldWithKey('addsubfolder'); ?>
	<input type="hidden" name="folderid" value="<?php print $folder->getId();?>">
	<input type="hidden" name="showtree" value="<?php echo showtree();?>">
	
		<div class="control-group">
			<label class="control-label"><?php printMLText("name");?>:</label>
			<div class="controls"><input type="text" name="name" size="60" required></div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php printMLText("comment");?>:</label>
			<div class="controls"><textarea name="comment" rows="4" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>></textarea></div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php printMLText("sequence");?>:</label>
			<div class="controls"><?php $this->printSequenceChooser($folder->getSubFolders('s')); if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off');?></div>
		</div>
<?php
	$attrdefs = $dms->getAllAttributeDefinitions(array(SeedDMS_Core_AttributeDefinition::objtype_folder, SeedDMS_Core_AttributeDefinition::objtype_all));
	if($attrdefs) {
		foreach($attrdefs as $attrdef) {
			$arr = $this->callHook('addFolderAttribute', null, $attrdef);
			if(is_array($arr)) {
				if($arr) {
					echo "<div class=\"control-group\">";
					echo "	<label class=\"control-label\">".$arr[0].":</label>";
					echo "	<div class=\"controls\">".$arr[1]."</div>";
					echo "</div>";
				}
			} else {
?>
<div class="control-group">
	<label class="control-label"><?php echo htmlspecialchars($attrdef->getName()); ?>:</label>
	<div class="controls"><?php $this->printAttributeEditField($attrdef, '') ?></div>
</div>
<?php
			}
		}
	}
?>

<div class="controls">
	<input type="submit" class="btn" value="<?php printMLText("add_subfolder");?>">
</div>

</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

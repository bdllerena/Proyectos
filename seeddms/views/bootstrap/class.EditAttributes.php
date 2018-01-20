<?php
/**
 * Implementation of EditAttributes view
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
 * Class which outputs the html page for EditAttributes view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditAttributes extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$folder = $this->params['folder'];
		$document = $this->params['document'];
		$version = $this->params['version'];
		$attrdefs = $this->params['attrdefs'];

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

		$this->contentHeading(getMLText("edit_attributes"));
		$this->contentContainerStart();
?>
<form class="form-horizontal" action="../op/op.EditAttributes.php" name="form1" method="POST">
	<?php echo createHiddenFieldWithKey('editattributes'); ?>
	<input type="hidden" name="documentid" value="<?php print $document->getID();?>">
	<input type="hidden" name="version" value="<?php print $version->getVersion();?>">

<?php
			if($attrdefs) {
				foreach($attrdefs as $attrdef) {
					$arr = $this->callHook('editDocumentContentAttribute', $version, $attrdef);
					if(is_array($arr)) {
						if($arr) {
							echo "<div class=\"control-group\">";
							echo "<label class=\"control-label\">".$arr[0].":</label>";
							echo "<div class=\"controls\">".$arr[1]."</div>";
							echo "</div>";
						}
					} else {
?>
    <div class="control-group">
	<label class="control-label"><?php echo htmlspecialchars($attrdef->getName()); ?></label>
        <div class="controls">
	        <?php $this->printAttributeEditField($attrdef, $version->getAttribute($attrdef)) ?>
        </div>
	</div>
<?php
					}
				}
			}
?>
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

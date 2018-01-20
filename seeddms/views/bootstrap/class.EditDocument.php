<?php
/**
 * Implementation of EditDocument view
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
 * Class which outputs the html page for EditDocument view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditDocument extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
		header('Content-Type: application/javascript');
		$this->printKeywordChooserJs('form1');
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
		messages: {
			name: "<?php printMLText("js_no_name");?>",
			comment: "<?php printMLText("js_no_comment");?>",
			keywords: "<?php printMLText("js_no_keywords");?>"
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
		$attrdefs = $this->params['attrdefs'];
		$strictformcheck = $this->params['strictformcheck'];
		$orderby = $this->params['orderby'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))));
		$this->globalNavigation($folder);
		$this->contentStart();
		$this->pageNavigation($this->getFolderPathHTML($folder, true, $document), "view_document", $document);

		$this->contentHeading(getMLText("edit_document_props"));
		$this->contentContainerStart();

		if($document->expires())
			$expdate = date('Y-m-d', $document->getExpires());
		else
			$expdate = '';
?>
<form action="../op/op.EditDocument.php" name="form1" id="form1" method="post">
	<input type="hidden" name="documentid" value="<?php echo $document->getID() ?>">
	<table cellpadding="3">
		<tr>
			<td class="inputDescription"><?php printMLText("name");?>:</td>
			<td><input type="text" name="name" id="name" value="<?php print htmlspecialchars($document->getName());?>" size="60" required></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("comment");?>:</td>
			<td><textarea name="comment" id="comment" rows="4" cols="80"<?php echo $strictformcheck ? ' required' : ''; ?>><?php print htmlspecialchars($document->getComment());?></textarea></td>
		</tr>
		<tr>
			<td valign="top" class="inputDescription"><?php printMLText("keywords");?>:</td>
			<td class="standardText">
<?php
	$this->printKeywordChooserHtml('form1', $document->getKeywords());
?>
			</td>
		</tr>
		<tr>
			<td><?php printMLText("categories")?>:</td>
			<td>
        <select class="chzn-select" name="categories[]" multiple="multiple" data-placeholder="<?php printMLText('select_category'); ?>" data-no_results_text="<?php printMLText('unknown_document_category'); ?>">
<?php
			$categories = $dms->getDocumentCategories();
			foreach($categories as $category) {
				echo "<option value=\"".$category->getID()."\"";
				if(in_array($category, $document->getCategories()))
					echo " selected";
				echo ">".$category->getName()."</option>";	
			}
?>
				</select>
      </td>
		</tr>
		<tr>
			<td><?php printMLText("expires");?>:</td>
			<td>
				<select class="span3" name="presetexpdate" id="presetexpdate">
					<option value="never"><?php printMLText('does_not_expire');?></option>
					<option value="date"<?php echo ($expdate != '' ? " selected" : ""); ?>><?php printMLText('expire_by_date');?></option>
					<option value="1w"><?php printMLText('expire_in_1w');?></option>
					<option value="1m"><?php printMLText('expire_in_1m');?></option>
					<option value="1y"><?php printMLText('expire_in_1y');?></option>
					<option value="2y"><?php printMLText('expire_in_2y');?></option>
				</select>
			</td>
		</tr>
		<tr id="control_expdate" <?php echo (!$expdate ? 'style="display: none;"' : ''); ?>>
			<td><?php printMLText("expires");?>:</td>
			<td>
        <span class="input-append date span6" id="expirationdate" data-date="<?php echo ($expdate ? $expdate : ''); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>" data-checkbox="#expires">
          <input class="span3" size="16" name="expdate" type="text" value="<?php echo ($expdate ? $expdate : ''); ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
        </span>
			</td>
		</tr>
<?php
		if ($folder->getAccessMode($user) > M_READ) {
			print "<tr>";
			print "<td class=\"inputDescription\">" . getMLText("sequence") . ":</td>";
			print "<td>";
			$this->printSequenceChooser($folder->getDocuments('s'), $document->getID());
			if($orderby != 's') echo "<br />".getMLText('order_by_sequence_off'); 
			print "</td></tr>";
		}
		if($attrdefs) {
			foreach($attrdefs as $attrdef) {
				$arr = $this->callHook('editDocumentAttribute', $document, $attrdef);
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
			<td><?php $this->printAttributeEditField($attrdef, $document->getAttribute($attrdef)) ?></td>
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
			<td></td>
			<td><button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button></td>
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

<?php
/**
 * Implementation of UserDefaultKeywords view
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
 * Class which outputs the html page for UserDefaultKeywords view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_UserDefaultKeywords extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript; charset=UTF-8');
?>
obj = -1;
function showKeywords(selectObj) {
	if (obj != -1)
		obj.style.display = "none";

	id = selectObj.options[selectObj.selectedIndex].value;
	if (id == -1)
		return;

	obj = document.getElementById("keywords" + id);
	obj.style.display = "";
}

sel = document.getElementById("selector");
sel.selectedIndex=0;
showKeywords(sel);

$(document).ready(function() {
	$('body').on('submit', '#form1', function(ev){
		if(checkForm()) return;
		ev.preventDefault();
	});
	$( "#selector" ).change(function() {
		showKeywords(this);
//		$('div.ajax').trigger('update', {userid: $(this).val()});
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$categories = $this->params['categories'];

		$this->htmlStartPage(getMLText("edit_default_keywords"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("my_account"), "my_account");
		$this->contentHeading(getMLText("edit_default_keywords"));
?>
<div class="row-fluid">
	<div class="span4">
		<div class="well">
			<form class="form-horizontal">
				<div class="control-group">
					<label class="control-label" for="login"><?php printMLText("selection");?>:</label>
					<div class="controls">
						<select id="selector">
							<option value="-1"><?php echo getMLText("choose_category")?>
							<option value="0"><?php echo getMLText("new_default_keyword_category")?>
<?php

		$selected=0;
		$count=2;
		foreach ($categories as $category) {

			$owner = $category->getOwner();
			if ($owner->getID() != $user->getID()) continue;

			if (isset($_GET["categoryid"]) && $category->getID()==$_GET["categoryid"]) $selected=$count;
			print "<option value=\"".$category->getID()."\">" . htmlspecialchars($category->getName());
			$count++;
		}
?>
						</select>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="span8">
		<div class="well">

			<div id="keywords0" style="display : none;">
				<form class="form-horizontal" action="../op/op.UserDefaultKeywords.php" method="post" name="addcategory">
					<input type="hidden" name="action" value="addcategory">

					<div class="control-group">
						<label class="control-label"><?php printMLText("name");?>:</label>
						<div class="controls">
							<input type="text" name="name">
						</div>
					</div>
					<div class="controls">
						<input type="submit" class="btn" value="<?php printMLText("new_default_keyword_category"); ?>">
					</div>
				</form>
			</div>
<?php
		foreach ($categories as $category) {
			$owner = $category->getOwner();
			if ($owner->getID() != $user->getID()) continue;

			print "<div id=\"keywords".$category->getID()."\" style=\"display : none;\">";
?>
				<div class="controls">
					<form class="form-horizontal" action="../op/op.UserDefaultKeywords.php" method="post">
					<?php echo createHiddenFieldWithKey('removecategory'); ?>
						<input type="hidden" name="action" value="removecategory">
						<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
						<button type="submit" class="btn" title="<?php echo getMLText("delete")?>"><i class="icon-remove"></i> <?php printMLText("rm_default_keyword_category");?></button>
					</form>
				</div>

				<form class="form-inline" action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo "category".$category->getID()?>">
				<div class="control-group">
					<label class="control-label"><?php echo getMLText("name")?>:</label>
					<div class="controls">
  						<?php echo createHiddenFieldWithKey('editcategory'); ?>
							<input type="hidden" name="action" value="editcategory">
							<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
							<input name="name" type="text" value="<?php echo htmlspecialchars($category->getName())?>">
  						<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button>
					</div>
				</div>
				</form>
				<div class="control-group">
					<label class="control-label"><?php echo getMLText("default_keywords")?>:</label>
					<div class="controls">
						<?php
							$lists = $category->getKeywordLists();
							if (count($lists) == 0)
								print getMLText("no_default_keywords");
							else
								foreach ($lists as $list) {
?>
									<form class="form-inline" style="display: inline-block;margin-bottom: 0px;" action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo "cat".$category->getID().".".$list["id"]?>">
									<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="hidden" name="action" value="editkeywords">
									<input type="text" name="keywords" value="<?php echo htmlspecialchars($list["keywords"]) ?>">
									<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save")?></button>
									</form>
									<form style="display: inline-block;" method="post" action="../op/op.UserDefaultKeywords.php" >
  								<?php echo createHiddenFieldWithKey('removekeywords'); ?>
									<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
									<input type="hidden" name="keywordsid" value="<?php echo $list["id"]?>">
									<input type="hidden" name="action" value="removekeywords">
									<button type="submit" class="btn"><i class="icon-remove"></i> <?php printMLText("delete")?></button>
									</form>
									<br>
						<?php }  ?>
					</div>
				</div>
				<div class="controls">
						<form class="form-inline" action="../op/op.UserDefaultKeywords.php" method="post" name="<?php echo $category->getID().".add"?>">
  				  <?php echo createHiddenFieldWithKey('newkeywords'); ?>
						<input type="hidden" name="action" value="newkeywords">
						<input type="hidden" name="categoryid" value="<?php echo $category->getID()?>">
						<input type="text" name="keywords">
						<input type="submit" class="btn" value="<?php printMLText("new_default_keywords");?>">
						</form>
				</div>

		</div>
<?php } ?>
</div>
</div>
</div>

<?php
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

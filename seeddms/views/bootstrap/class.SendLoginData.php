<?php
/**
 * Implementation of SendLoginData view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2017 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for SendLoginData view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2017 Uwe Steinmann
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_SendLoginData extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$newuser = $this->params['newuser'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentHeading(getMLText("send_login_data"));

		$this->contentContainerStart();
?>
<form class="form-horizontal" action="../op/op.UsrMgr.php" name="form1" method="post">
<input type="hidden" name="userid" value="<?php print $newuser->getID();?>">
<input type="hidden" name="action" value="sendlogindata">
<?php echo createHiddenFieldWithKey('sendlogindata'); ?>

<div class="control-group">
	<label class="control-label" for="assignTo">
<?php printMLText("comment"); ?>:
	</label>
	<div class="controls">
		<textarea name="comment"></textarea>
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<button type="submit" class="btn"><i class="icon-envelope-alt"></i> <?php printMLText("send_email");?></button>
	</div>
</div>

</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

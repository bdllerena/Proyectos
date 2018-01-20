<?php
/**
 * Implementation of ChangePassword view
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
 * Class which outputs the html page for ChangePassword view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ChangePassword extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript; charset=UTF-8');
?>
document.form1.newpassword.focus();
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$referuri = $this->params['referuri'];
		$hash = $this->params['hash'];
		$passwordstrength = $this->params['passwordstrength'];

		$this->htmlStartPage(getMLText("change_password"), "login");
		$this->globalBanner();
		$this->contentStart();
		$this->pageNavigation(getMLText("change_password"));
		$this->contentContainerStart();
?>
<form class="form-horizontal" action="../op/op.ChangePassword.php" method="post" name="form1">
<?php
		if ($referuri) {
			echo "<input type='hidden' name='referuri' value='".$referuri."'/>";
		}
		if ($hash) {
			echo "<input type='hidden' name='hash' value='".$hash."'/>";
		}
?>

		 <div class="control-group">
			<label class="control-label"><?php printMLText("password");?>:</label>
			<div class="controls"><input class="pwd" type="password" rel="strengthbar" name="newpassword" id="password"></div>
		 </div>
<?php
		if($passwordstrength > 0) {
?>
		<div class="control-group">
			<label class="control-label"><?php printMLText("password_strength");?>:</label>
			<div class="controls">
				<div id="strengthbar" class="progress" style="width: 220px; height: 30px; margin-bottom: 8px;"><div class="bar bar-danger" style="width: 0%;"></div></div>
			</div>
		</div>
<?php
		}
?>
		<div class="control-group">
			<label class="control-label"><?php printMLText("confirm_pwd");?>:</label>
			<div class="controls"><input type="password" name="newpasswordrepeat" id="passwordrepeat"></div>
		</div>
		<div class="control-group">
			<label class="control-label"></label>
			<div class="controls"><input class="btn" type="submit" value="<?php printMLText("submit_password") ?>"></div>
		</div>

</form>
<?php $this->contentContainerEnd(); ?>
<p><a href="../out/out.Login.php"><?php echo getMLText("login"); ?></a></p>
<?php
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

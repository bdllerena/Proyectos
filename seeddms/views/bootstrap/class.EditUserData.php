<?php
/**
 * Implementation of EditUserData view
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
 * Class which outputs the html page for EditUserData view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_EditUserData extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');
?>
$(document).ready( function() {
	$("#form").validate({
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
		rules: {
			currentpwd: {
				required: true
			},
			fullname: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			pwdconf: {
				equalTo: "#pwd"
			}
		},
		messages: {
			currentpwd: "<?php printMLText("js_no_currentpwd");?>",
			fullname: "<?php printMLText("js_no_name");?>",
			email: {
				required: "<?php printMLText("js_no_email");?>",
				email: "<?php printMLText("js_invalid_email");?>"
			},
			pwdconf: "<?php printMLText("js_unequal_passwords");?>",
		},
	});
});
<?php
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$enableuserimage = $this->params['enableuserimage'];
		$enablelanguageselector = $this->params['enablelanguageselector'];
		$enablethemeselector = $this->params['enablethemeselector'];
		$passwordstrength = $this->params['passwordstrength'];
		$httproot = $this->params['httproot'];

		$this->htmlAddHeader('<script type="text/javascript" src="../styles/'.$this->theme.'/validate/jquery.validate.js"></script>'."\n", 'js');

		$this->htmlStartPage(getMLText("edit_user_details"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("my_account"), "my_account");

		$this->contentHeading(getMLText("edit_user_details"));
		$this->contentContainerStart();
?>
<form class="form-horizontal" action="../op/op.EditUserData.php" enctype="multipart/form-data" method="post" id="form">
	<div class="control-group">
		<label class="control-label"><?php printMLText("current_password");?>:</label>
		<div class="controls">
			<input id="currentpwd" type="password" name="currentpwd" size="30">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php printMLText("new_password");?>:</label>
		<div class="controls">
			<input class="pwd" type="password" rel="strengthbar" id="pwd" name="pwd" size="30">
		</div>
	</div>
<?php
	if($passwordstrength) {
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
		<div class="controls">
			<input id="pwdconf" type="Password" id="pwdconf" name="pwdconf" size="30">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php printMLText("name");?>:</label>
		<div class="controls">
			<input type="text" id="fullname" name="fullname" value="<?php print htmlspecialchars($user->getFullName());?>" size="30">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php printMLText("email");?>:</label>
		<div class="controls">
			<input type="text" id="email" name="email" value="<?php print htmlspecialchars($user->getEmail());?>" size="30">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php printMLText("comment");?>:</label>
		<div class="controls">
			<textarea name="comment" rows="4" cols="80"><?php print htmlspecialchars($user->getComment());?></textarea>
		</div>
	</div>

<?php	
		if ($enableuserimage){	
?>	
	<div class="control-group">
		<label class="control-label"><?php printMLText("user_image");?>:</label>
		<div class="controls">
<?php
			if ($user->hasImage())
				print "<img src=\"".$httproot . "out/out.UserImage.php?userid=".$user->getId()."\">";
			else printMLText("no_user_image");
?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php printMLText("new_user_image");?>:</label>
		<div class="controls">
<?php
	$this->printFileChooser('userfile', false, "image/jpeg");
?>
		</div>
	</div>
<?php
		}
		if ($enablelanguageselector){	
?>
	<div class="control-group">
		<label class="control-label"><?php printMLText("language");?>:</label>
		<div class="controls">
			<select name="language">
<?php
			$languages = getLanguages();
			foreach ($languages as $currLang) {
				print "<option value=\"".$currLang."\" ".(($user->getLanguage()==$currLang) ? "selected" : "").">".getMLText($currLang)."</option>";
			}
?>
			</select>
		</div>
	</div>
<?php
		}
		if ($enablethemeselector){	
?>
	<div class="control-group">
		<label class="control-label"><?php printMLText("theme");?>:</label>
		<div class="controls">
			<select name="theme">
<?php
			$themes = UI::getStyles();
			foreach ($themes as $currTheme) {
				print "<option value=\"".$currTheme."\" ".(($user->getTheme()==$currTheme) ? "selected" : "").">".$currTheme."</option>";
			}
?>
			</select>
		</div>
	</div>
<?php
		}
?>
	<div class="controls">
		<button type="submit" class="btn"><i class="icon-save"></i> <?php printMLText("save"); ?></button>
	</div>
</form>

<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

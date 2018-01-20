<?php
/**
 * Implementation of ImportFS view
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
 * Class which outputs the html page for ImportFS view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ImportFS extends SeedDMS_Bootstrap_Style {

	function js() { /* {{{ */
		header('Content-Type: application/javascript');

		$this->printFolderChooserJs("form1");
		$this->printDropFolderChooserJs("form1", 1);
	} /* }}} */

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$dropfolderdir = $this->params['dropfolderdir'];

		$this->htmlStartPage(getMLText("import_fs"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");

		$this->contentHeading(getMLText("import_fs"));

		if($dropfolderdir && file_exists($dropfolderdir.'/'.$user->getLogin())) {
			echo "<div class=\"alert alert-warning\">";
			printMLText("import_fs_warning");
			echo "</div>\n";
			$this->contentContainerStart();
			print "<form class=\"form-horizontal\" action=\"../op/op.ImportFS.php\" name=\"form1\">";
			print "<div class=\"control-group\"><label class=\"control-label\">".getMLText('choose_target_folder')."</label><div class=\"controls\">";
			$this->printFolderChooserHtml("form1",M_READWRITE);
			print "</div></div>";
			print "<div class=\"control-group\"><label class=\"control-label\">";
			printMLText("dropfolder_folder");
			echo ": ";
			print "</label><div class=\"controls\">";
			/* Setting drop folder dir to "" will force to take the default from settings.xml */
			$this->printDropFolderChooserHtml("form1", "", 1);
			print "</div></div>";

			print "<div class=\"control-group\"><label class=\"control-label\">".getMLText('removeFolderFromDropFolder')."</label><div class=\"controls\">";
			print "<input type='checkbox' name='remove' value='1'/>";
			print "</div></div>";

			print "<div class=\"control-group\"><label class=\"control-label\">";
			print "</label><div class=\"controls\">";
			print "<input type='submit' class='btn' name='' value='".getMLText("import")."'/>";
			print "</div></div>";
			print "</form>\n";
			$this->contentContainerEnd();
		} else {
			echo "<div class=\"alert alert-warning\">";
			printMLText("dropfolderdir_missing");
			echo "</div>\n";
		}

		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}


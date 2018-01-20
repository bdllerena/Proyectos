<?php
//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//    Copyright (C) 2010 Matteo Lucarelli
//    Copyright (C) 2009-2012 Uwe Steinmann
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.


class SeedDMS_Bootstrap_Style extends SeedDMS_View_Common {
	var $imgpath;

	/**
	 * @var string $extraheader extra html code inserted in the html header
	 * of the page
	 *
	 * @access protected
	 */
	protected $extraheader;

	function __construct($params, $theme='bootstrap') {
		$this->theme = $theme;
		$this->params = $params;
		$this->imgpath = '../views/'.$theme.'/images/';
		$this->extraheader = array('js'=>'', 'css'=>'');
		$this->footerjs = array();
	}

	/**
	 * Add javascript to an internal array which is output at the
	 * end of the page within a document.ready() function.
	 *
	 * @param string $script javascript to be added
	 */
	function addFooterJS($script) { /* {{{ */
		$this->footerjs[] = $script;
	} /* }}} */

	function htmlStartPage($title="", $bodyClass="", $base="", $httpheader=array()) { /* {{{ */
		if(1 || method_exists($this, 'js')) {
			/* We still need unsafe-eval, because printDocumentChooserHtml and
			 * printFolderChooserHtml will include a javascript file with ajax
			 * which is evaled by jquery
			 * X-WebKit-CSP is deprecated, Chrome understands Content-Security-Policy
			 * since version 25+
			 * X-Content-Security-Policy is deprecated, Firefox understands
			 * Content-Security-Policy since version 23+
			 */
			$csp_rules = "script-src 'self' 'unsafe-eval';"; // style-src 'self';";
			foreach (array("X-WebKit-CSP", "X-Content-Security-Policy", "Content-Security-Policy") as $csp) {
				header($csp . ": " . $csp_rules);
			}
		}
		if($httpheader) {
			foreach($httpheader as $name=>$value) {
				header($name . ": " . $value);
			}
		}
		$hookObjs = $this->getHookObjects('SeedDMS_View_Bootstrap');
		foreach($hookObjs as $hookObj) {
			if (method_exists($hookObj, 'startPage')) {
				$hookObj->startPage($this);
			}
		}
		echo "<!DOCTYPE html>\n";
		echo "<html lang=\"en\">\n<head>\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">'."\n";
		if($base)
			echo '<base href="../../">'."\n";
		echo '<link href="../styles/'.$this->theme.'/bootstrap/css/bootstrap.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/font-awesome/css/font-awesome.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/datepicker/css/datepicker.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/chosen/css/chosen.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/select2/css/select2.min.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/select2/css/select2-bootstrap.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/jqtree/jqtree.css" rel="stylesheet">'."\n";
		echo '<link href="../styles/'.$this->theme.'/application.css" rel="stylesheet">'."\n";
		if($this->extraheader['css'])
			echo $this->extraheader['css'];
		if(method_exists($this, 'css'))
			echo '<link href="../out/out.'.$this->params['class'].'.php?action=css'.(!empty($_SERVER['QUERY_STRING']) ? '&'.$_SERVER['QUERY_STRING'] : '').'" rel="stylesheet">'."\n";

		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/jquery/jquery.min.js"></script>'."\n";
		if($this->extraheader['js'])
			echo $this->extraheader['js'];
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/passwordstrength/jquery.passwordstrength.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/jquery.noty.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/layouts/topRight.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/layouts/topCenter.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/noty/themes/default.js"></script>'."\n";
		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/jqtree/tree.jquery.js"></script>'."\n";
//		echo '<script type="text/javascript" src="../styles/'.$this->theme.'/jquery-cookie/jquery.cookie.js"></script>'."\n";
		echo '<link rel="shortcut icon" href="../styles/'.$this->theme.'/favicon.ico" type="image/x-icon"/>'."\n";
		if($this->params['session'] && $this->params['session']->getSu()) {
?>
<style type="text/css">
.navbar-inverse .navbar-inner {
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#882222), to(#111111));
background-image: webkit-linear-gradient(top, #882222, #111111);
background-image: linear-gradient(to bottom, #882222, #111111);;
}
</style>
<?php
		}
		$sitename = trim(strip_tags($this->params['sitename']));
		echo "<title>".(strlen($sitename)>0 ? $sitename : "SeedDMS").(strlen($title)>0 ? ": " : "").htmlspecialchars($title)."</title>\n";
		echo "</head>\n";
		echo "<body".(strlen($bodyClass)>0 ? " class=\"".$bodyClass."\"" : "").">\n";
		if($this->params['session'] && $flashmsg = $this->params['session']->getSplashMsg()) {
			$this->params['session']->clearSplashMsg();
			echo "<div class=\"splash\" data-type=\"".$flashmsg['type']."\">".$flashmsg['msg']."</div>\n";
		}
		foreach($hookObjs as $hookObj) {
			if (method_exists($hookObj, 'startBody')) {
				$hookObj->startBody($this);
			}
		}
	} /* }}} */

	function htmlAddHeader($head, $type='js') { /* {{{ */
		$this->extraheader[$type] .= $head;
	} /* }}} */

	function htmlEndPage($nofooter=false) { /* {{{ */
		if(!$nofooter) {
			$this->footNote();
			if($this->params['showmissingtranslations']) {
				$this->missingLanguageKeys();
			}
		}
		echo '<script src="../styles/'.$this->theme.'/bootstrap/js/bootstrap.min.js"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/datepicker/js/bootstrap-datepicker.js"></script>'."\n";
		foreach(array('de', 'es', 'ca', 'nl', 'fi', 'cs', 'it', 'fr', 'sv', 'sl', 'pt-BR', 'zh-CN', 'zh-TW') as $lang)
			echo '<script src="../styles/'.$this->theme.'/datepicker/js/locales/bootstrap-datepicker.'.$lang.'.js"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/chosen/js/chosen.jquery.min.js"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/select2/js/select2.min.js"></script>'."\n";
		parse_str($_SERVER['QUERY_STRING'], $tmp);
		$tmp['action'] = 'webrootjs';
		echo '<script src="'.$this->params['absbaseprefix'].'out/out.'.$this->params['class'].'.php?'.http_build_query($tmp).'"></script>'."\n";
		echo '<script src="../styles/'.$this->theme.'/application.js"></script>'."\n";
		if(isset($this->params['user']) && $this->params['user']) {
			$this->addFooterJS('checkTasks();');
		}
		if($this->footerjs) {
			$jscode = "$(document).ready(function () {\n";
			foreach($this->footerjs as $script) {
				$jscode .= $script."\n";
			}
			$jscode .= "});\n";
			$hashjs = md5($jscode);
			if(!is_dir($this->params['cachedir'].'/js')) {
				SeedDMS_Core_File::makeDir($this->params['cachedir'].'/js');
			}
			if(is_dir($this->params['cachedir'].'/js')) {
				file_put_contents($this->params['cachedir'].'/js/'.$hashjs.'.js', $jscode);
			}
			$tmp['action'] = 'footerjs';
			$tmp['hash'] = $hashjs;
			echo '<script src="'.$this->params['absbaseprefix'].'out/out.'.$this->params['class'].'.php?'.http_build_query($tmp).'"></script>'."\n";
		}
		if(method_exists($this, 'js')) {
			parse_str($_SERVER['QUERY_STRING'], $tmp);
			$tmp['action'] = 'js';
			echo '<script src="'.$this->params['absbaseprefix'].'out/out.'.$this->params['class'].'.php?'.http_build_query($tmp).'"></script>'."\n";
		}
		echo "</body>\n</html>\n";
	} /* }}} */

	function webrootjs() { /* {{{ */
		header('Content-Type: application/javascript');
		echo "var seeddms_absbaseprefix=\"".$this->params['absbaseprefix']."\";\n";
		echo "var seeddms_webroot=\"".$this->params['settings']->_httpRoot."\";\n";
	} /* }}} */

	function footerjs() { /* {{{ */
		header('Content-Type: application/javascript');
		if(file_exists($this->params['cachedir'].'/js/'.$_GET['hash'].'.js')) {
			readfile($this->params['cachedir'].'/js/'.$_GET['hash'].'.js');
		}
	} /* }}} */

	function missingLanguageKeys() { /* {{{ */
		global $MISSING_LANG, $LANG;
		if($MISSING_LANG) {
			echo '<div class="container-fluid">'."\n";
			echo '<div class="row-fluid">'."\n";
			echo '<div class="alert alert-error">'."\n";
			echo "<p><strong>This page contains missing translations in the selected language. Please help to improve SeedDMS and provide the translation.</strong></p>";
			echo "</div>";
			echo "<table class=\"table table-condensed\">";
			echo "<tr><th>Key</th><th>engl. Text</th><th>Your translation</th></tr>\n";
			foreach($MISSING_LANG as $key=>$lang) {
				echo "<tr><td>".$key."</td><td>".(isset($LANG['en_GB'][$key]) ? $LANG['en_GB'][$key] : '')."</td><td><div class=\"input-append send-missing-translation\"><input name=\"missing-lang-key\" type=\"hidden\" value=\"".$key."\" /><input name=\"missing-lang-lang\" type=\"hidden\" value=\"".$lang."\" /><input type=\"text\" class=\"input-xxlarge\" name=\"missing-lang-translation\" placeholder=\"Your translation in '".$lang."'\"/><a class=\"btn\">Submit</a></div></td></tr>";
			}
			echo "</table>";
			echo "<div class=\"splash\" data-type=\"error\" data-timeout=\"5500\"><b>There are missing translations on this page!</b><br />Please check the bottom of the page.</div>\n";
			echo "</div>\n";
			echo "</div>\n";
		}
	} /* }}} */

	function footNote() { /* {{{ */
		echo "<div class=\"container-fluid\">\n";
		echo '<div class="row-fluid">'."\n";
		echo '<div class="span12">'."\n";
		echo '<div class="alert alert-info">'."\n";
		if ($this->params['printdisclaimer']){
			echo "<div class=\"disclaimer\">".getMLText("disclaimer")."</div>";
		}

		if (isset($this->params['footnote']) && strlen((string)$this->params['footnote'])>0) {
			echo "<div class=\"footNote\">".(string)$this->params['footnote']."</div>";
		}
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
	
		return;
	} /* }}} */

	function contentStart() { /* {{{ */
		echo "<div class=\"container-fluid\">\n";
		echo " <div class=\"row-fluid\">\n";
	} /* }}} */

	function contentEnd() { /* {{{ */
		echo " </div>\n";
		echo "</div>\n";
	} /* }}} */

	function globalBanner() { /* {{{ */
		echo "<div class=\"navbar navbar-inverse navbar-fixed-top\">\n";
		echo " <div class=\"navbar-inner\">\n";
		echo "  <div class=\"container-fluid\">\n";
		echo "   <a class=\"brand\" href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS")."</a>\n";
		echo "  </div>\n";
		echo " </div>\n";
		echo "</div>\n";
	} /* }}} */

	function globalNavigation($folder=null) { /* {{{ */
		$dms = $this->params['dms'];
		echo "<div class=\"navbar navbar-inverse navbar-fixed-top\">\n";
		echo " <div class=\"navbar-inner\">\n";
		echo "  <div class=\"container-fluid\">\n";
		echo "   <a class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".nav-col1\">\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "     <span class=\"icon-bar\"></span>\n";
		echo "   </a>\n";
		echo "   <a class=\"brand\" href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".(strlen($this->params['sitename'])>0 ? $this->params['sitename'] : "SeedDMS")."</a>\n";
		if(isset($this->params['user']) && $this->params['user']) {
			echo "   <div class=\"nav-collapse nav-col1\">\n";
			echo "   <ul id=\"main-menu-admin\" class=\"nav pull-right\">\n";
			echo "    <li class=\"dropdown\">\n";
			echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".($this->params['session']->getSu() ? getMLText("switched_to") : getMLText("signed_in_as"))." '".htmlspecialchars($this->params['user']->getFullName())."' <i class=\"icon-caret-down\"></i></a>\n";
			echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
			if (!$this->params['user']->isGuest()) {
				$menuitems = array();
				$menuitems['my_documents'] = array('link'=>"../out/out.MyDocuments.php?inProcess=1", 'label'=>'my_documents');
				$menuitems['my_account'] = array('link'=>"../out/out.MyAccount.php", 'label'=>'my_account');
				$hookObjs = $this->getHookObjects('SeedDMS_View_Bootstrap');
				foreach($hookObjs as $hookObj) {
					if (method_exists($hookObj, 'userMenuItems')) {
						$menuitems = $hookObj->userMenuItems($this, $menuitems);
					}
				}
				if($menuitems) {
					foreach($menuitems as $menuitem) {
						echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
					}
					echo "    <li class=\"divider\"></li>\n";
				}
			}
			$showdivider = false;
			if($this->params['enablelanguageselector']) {
				$showdivider = true;
				echo "    <li class=\"dropdown-submenu\">\n";
				echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("language")."</a>\n";
				echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
				$languages = getLanguages();
				foreach ($languages as $currLang) {
					if($this->params['session']->getLanguage() == $currLang)
						echo "<li class=\"active\">";
					else
						echo "<li>";
					echo "<a href=\"../op/op.SetLanguage.php?lang=".$currLang."&referer=".$_SERVER["REQUEST_URI"]."\">";
					echo getMLText($currLang)."</a></li>\n";
				}
				echo "     </ul>\n";
				echo "    </li>\n";
			}
			if($this->params['user']->isAdmin()) {
				$showdivider = true;
				echo "    <li><a href=\"../out/out.SubstituteUser.php\">".getMLText("substitute_user")."</a></li>\n";
			}
			if($showdivider)
				echo "    <li class=\"divider\"></li>\n";
			if($this->params['session']->getSu()) {
				echo "    <li><a href=\"../op/op.ResetSu.php\">".getMLText("sign_out_user")."</a></li>\n";
			} else {
				echo "    <li><a href=\"../op/op.Logout.php\">".getMLText("sign_out")."</a></li>\n";
			}
			echo "     </ul>\n";
			echo "    </li>\n";
			echo "   </ul>\n";

			if($this->params['enablemenutasks']) {
				echo "   <div id=\"menu-tasks\">";
				echo "   <ul id=\"main-menu-tasks\" class=\"nav pull-right\">\n";
				echo "    <li class=\"dropdown\">\n";
//				echo $this->menuTasks(array('review'=>array(), 'approval'=>array(), 'receipt'=>array(), 'revision'=>array()));
				echo "    </li>\n";
				echo "   </ul>\n";
				echo "   </div>";
				//$this->addFooterJS('checkTasks();');
			}

			if($this->params['dropfolderdir'] && $this->params['enabledropfolderlist']) {
				echo "   <div id=\"menu-dropfolder\">";
				echo "     <div class=\"ajax\" data-no-spinner=\"true\" data-view=\"DropFolderChooser\" data-action=\"menuList\"";
				if ($folder!=null && is_object($folder) && !strcasecmp(get_class($folder), $dms->getClassname('folder')))
					echo " data-query=\"folderid=".$folder->getID()."\"";
				echo "></div>";
				echo "   </div>";
			}
			if($this->params['enablesessionlist']) {
				echo "   <div id=\"menu-session\">";
				echo "     <div class=\"ajax\" data-no-spinner=\"true\" data-view=\"Session\" data-action=\"menuSessions\"></div>";
				echo "   </div>";
			}
			if($this->params['enableclipboard']) {
				echo "   <div id=\"menu-clipboard\">";
				echo "     <div class=\"ajax\" data-no-spinner=\"true\" data-view=\"Clipboard\" data-action=\"menuClipboard\"></div>";
				echo "   </div>";
			}

			echo "   <ul class=\"nav\">\n";
	//		echo "    <li id=\"first\"><a href=\"../out/out.ViewFolder.php?folderid=".$this->params['rootfolderid']."\">".getMLText("content")."</a></li>\n";
	//		echo "    <li><a href=\"../out/out.SearchForm.php?folderid=".$this->params['rootfolderid']."\">".getMLText("search")."</a></li>\n";
			if ($this->params['enablecalendar']) echo "    <li><a href=\"../out/out.Calendar.php?mode=".$this->params['calendardefaultview']."\">".getMLText("calendar")."</a></li>\n";
			if ($this->params['user']->isAdmin()) echo "    <li><a href=\"../out/out.AdminTools.php\">".getMLText("admin_tools")."</a></li>\n";
			if($this->params['enablehelp']) {
			$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
			echo "    <li><a href=\"../out/out.Help.php?context=".$tmp[1]."\">".getMLText("help")."</a></li>\n";
			}
			echo "   </ul>\n";
			echo "     <form action=\"../out/out.Search.php\" class=\"form-inline navbar-search pull-left\" autocomplete=\"off\">";
			if ($folder!=null && is_object($folder) && !strcasecmp(get_class($folder), $dms->getClassname('folder'))) {
				echo "      <input type=\"hidden\" name=\"folderid\" value=\"".$folder->getID()."\" />";
			}
			echo "      <input type=\"hidden\" name=\"navBar\" value=\"1\" />";
			echo "      <input name=\"query\" class=\"search-query\" ".($this->params['defaultsearchmethod'] == 'fulltext' ? "" : "id=\"searchfield\"")." data-provide=\"typeahead\" type=\"text\" style=\"width: 150px;\" placeholder=\"".getMLText("search")."\"/>";
			if($this->params['defaultsearchmethod'] == 'fulltext')
				echo "      <input type=\"hidden\" name=\"fullsearch\" value=\"1\" />";
//			if($this->params['enablefullsearch']) {
//				echo "      <label class=\"checkbox\" style=\"color: #999999;\"><input type=\"checkbox\" name=\"fullsearch\" value=\"1\" title=\"".getMLText('fullsearch_hint')."\"/> ".getMLText('fullsearch')."</label>";
//			}
	//		echo "      <input type=\"submit\" value=\"".getMLText("search")."\" id=\"searchButton\" class=\"btn\"/>";
			echo "</form>\n";
			echo "    </div>\n";
		}
		echo "  </div>\n";
		echo " </div>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	function getFolderPathHTML($folder, $tagAll=false, $document=null) { /* {{{ */
		$path = $folder->getPath();
		$txtpath = "";
		for ($i = 0; $i < count($path); $i++) {
			$txtpath .= "<li>";
			if ($i +1 < count($path)) {
				$txtpath .= "<a href=\"../out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\" rel=\"folder_".$path[$i]->getID()."\" class=\"table-row-folder\" formtoken=\"".createFormKey('movefolder')."\">".
					htmlspecialchars($path[$i]->getName())."</a>";
			}
			else {
				$txtpath .= ($tagAll ? "<a href=\"../out/out.ViewFolder.php?folderid=".$path[$i]->getID()."&showtree=".showtree()."\">".
										 htmlspecialchars($path[$i]->getName())."</a>" : htmlspecialchars($path[$i]->getName()));
			}
			$txtpath .= " <span class=\"divider\">/</span></li>";
		}
		if($document)
			$txtpath .= "<li><a href=\"../out/out.ViewDocument.php?documentid=".$document->getId()."\">".htmlspecialchars($document->getName())."</a></li>";

		return '<ul class="breadcrumb">'.$txtpath.'</ul>';
	} /* }}} */
	
	function pageNavigation($pageTitle, $pageType=null, $extra=null) { /* {{{ */

		if ($pageType!=null && strcasecmp($pageType, "noNav")) {
			echo "<div class=\"navbar\">\n";
			echo " <div class=\"navbar-inner\">\n";
			echo "  <div class=\"container\">\n";
			echo "   <a class=\"btn btn-navbar\" data-toggle=\"collapse\" data-target=\".col2\">\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo " 		<span class=\"icon-bar\"></span>\n";
			echo "   </a>\n";
			switch ($pageType) {
				case "view_folder":
					$this->folderNavigationBar($extra);
					break;
				case "view_document":
					$this->documentNavigationBar($extra);
					break;
				case "my_documents":
					$this->myDocumentsNavigationBar();
					break;
				case "my_account":
					$this->accountNavigationBar();
					break;
				case "admin_tools":
					$this->adminToolsNavigationBar();
					break;
				case "calendarold";
					$this->calendarOldNavigationBar($extra);
					break;
				case "calendar";
					$this->calendarNavigationBar($extra);
					break;
			}
			echo " 	</div>\n";
			echo " </div>\n";
			echo "</div>\n";
			if($pageType == "view_folder" || $pageType == "view_document")
				echo $pageTitle."\n";
		} else {
			echo "<legend>".$pageTitle."</legend>\n";
		}

		return;
	} /* }}} */

	private function folderNavigationBar($folder) { /* {{{ */
		$dms = $this->params['dms'];
		if (!is_object($folder) || strcasecmp(get_class($folder), $dms->getClassname('folder'))) {
			echo "<ul class=\"nav\">\n";
			echo "</ul>\n";
			return;
		}
		$accessMode = $folder->getAccessMode($this->params['user']);
		$folderID = $folder->getID();
		echo "<id=\"first\"><a href=\"../out/out.ViewFolder.php?folderid=". $folderID ."&showtree=".showtree()."\" class=\"brand\">".getMLText("folder")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";
		$menuitems = array();

		if ($accessMode == M_READ && !$this->params['user']->isGuest()) {
			$menuitems['edit_folder_notify'] = array('link'=>"../out/out.FolderNotify.php?folderid=".$folderID."&showtree=".showtree(), 'label'=>'edit_folder_notify');
		}
		else if ($accessMode >= M_READWRITE) {
			$menuitems['add_subfolder'] = array('link'=>"../out/out.AddSubFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'add_subfolder');
			$menuitems['add_document'] = array('link'=>"../out/out.AddDocument.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'add_document');
			if(0 && $this->params['enablelargefileupload'])
				$menuitems['add_multiple_documents'] = array('link'=>"../out/out.AddMultiDocument.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'add_multiple_documents');
			$menuitems['edit_folder_props'] = array('link'=>"../out/out.EditFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'edit_folder_props');
			if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
				$menuitems['move_folder'] = array('link'=>"../out/out.MoveFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'move_folder');

			if ($accessMode == M_ALL) {
				if ($folderID != $this->params['rootfolderid'] && $folder->getParent())
					$menuitems['rm_folder'] = array('link'=>"../out/out.RemoveFolder.php?folderid=". $folderID ."&showtree=".showtree(), 'label'=>'rm_folder');
			}
			if ($accessMode == M_ALL) {
				$menuitems['edit_folder_access'] = array('link'=>"../out/out.FolderAccess.php?folderid=".$folderID."&showtree=".showtree(), 'label'=>'edit_folder_access');
			}
			$menuitems['edit_existing_notify'] = array('link'=>"../out/out.FolderNotify.php?folderid=". $folderID ."&showtree=". showtree(), 'label'=>'edit_existing_notify');
		}
		if ($this->params['user']->isAdmin() && $this->params['enablefullsearch']) {
			$menuitems['index_folder'] = array('link'=>"../out/out.Indexer.php?folderid=". $folderID."&showtree=".showtree(), 'label'=>'index_folder');
		}

		/* Check if hook exists because otherwise callHook() will override $menuitems */
		if($this->hasHook('folderNavigationBar'))
			$menuitems = $this->callHook('folderNavigationBar', $folder, $menuitems);

		foreach($menuitems as $menuitem) {
			echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function documentNavigationBar($document)	{ /* {{{ */
		$accessMode = $document->getAccessMode($this->params['user']);
		$docid=".php?documentid=" . $document->getID();
		echo "<id=\"first\"><a href=\"../out/out.ViewDocument". $docid ."\" class=\"brand\">".getMLText("document")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";
		$menuitems = array();

		if ($accessMode >= M_READWRITE) {
			if (!$document->isLocked()) {
				$menuitems['update_document'] = array('link'=>"../out/out.UpdateDocument".$docid, 'label'=>'update_document');
				$menuitems['lock_document'] = array('link'=>"../op/op.LockDocument".$docid, 'label'=>'lock_document');
				$menuitems['edit_document_props'] = array('link'=>"../out/out.EditDocument".$docid , 'label'=>'edit_document_props');
				$menuitems['move_document'] = array('link'=>"../out/out.MoveDocument".$docid, 'label'=>'move_document');
			}
			else {
				$lockingUser = $document->getLockingUser();
				if (($lockingUser->getID() == $this->params['user']->getID()) || ($document->getAccessMode($this->params['user']) == M_ALL)) {
					$menuitems['update_document'] = array('link'=>"../out/out.UpdateDocument".$docid, 'label'=>'update_document');
					$menuitems['unlock_document'] = array('link'=>"../op/op.UnlockDocument".$docid, 'label'=>'unlock_document');
					$menuitems['edit_document_props'] = array('link'=>"../out/out.EditDocument".$docid, 'label'=>'edit_document_props');
					$menuitems['move_document'] = array('link'=>"../out/out.MoveDocument".$docid, 'label'=>'move_document');
				}
			}
			if($this->params['accessobject']->maySetExpires()) {
				$menuitems['expires'] = array('link'=>"../out/out.SetExpires".$docid, 'label'=>'expires');
			}
		}
		if ($accessMode == M_ALL) {
			$menuitems['rm_document'] = array('link'=>"../out/out.RemoveDocument".$docid, 'label'=>'rm_document');
			$menuitems['edit_document_access'] = array('link'=>"../out/out.DocumentAccess". $docid, 'label'=>'edit_document_access');
		}
		if ($accessMode >= M_READ && !$this->params['user']->isGuest()) {
			$menuitems['edit_existing_notify'] = array('link'=>"../out/out.DocumentNotify". $docid, 'label'=>'edit_existing_notify');
		}

		/* Check if hook exists because otherwise callHook() will override $menuitems */
		if($this->hasHook('documentNavigationBar'))
			$menuitems = $this->callHook('documentNavigationBar', $document, $menuitems);

		/* Do not use $this->callHook() because $menuitems must be returned by the hook
		 * or left unchanged
		 */
		/*
		$hookObjs = $this->getHookObjects();
		foreach($hookObjs as $hookObj) {
			if (method_exists($hookObj, 'documentNavigationBar')) {
	      $menuitems = $hookObj->documentNavigationBar($this, $document, $menuitems);
			}
		}
		*/

		foreach($menuitems as $menuitem) {
			echo "<li><a href=\"".$menuitem['link']."\">".getMLText($menuitem['label'])."</a></li>";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function accountNavigationBar() { /* {{{ */
		echo "<id=\"first\"><a href=\"../out/out.MyAccount.php\" class=\"brand\">".getMLText("my_account")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		if ($this->params['user']->isAdmin() || !$this->params['disableselfedit'])
			echo "<li id=\"first\"><a href=\"../out/out.EditUserData.php\">".getMLText("edit_user_details")."</a></li>\n";
		
		if (!$this->params['user']->isAdmin()) 
			echo "<li><a href=\"../out/out.UserDefaultKeywords.php\">".getMLText("edit_default_keywords")."</a></li>\n";

		echo "<li><a href=\"../out/out.ManageNotify.php\">".getMLText("edit_existing_notify")."</a></li>\n";

		if ($this->params['enableusersview']){
			echo "<li><a href=\"../out/out.UsrView.php\">".getMLText("users")."</a></li>\n";
			echo "<li><a href=\"../out/out.GroupView.php\">".getMLText("groups")."</a></li>\n";
		}		
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function myDocumentsNavigationBar() { /* {{{ */

		echo "<id=\"first\"><a href=\"../out/out.MyDocuments.php?inProcess=1\" class=\"brand\">".getMLText("my_documents")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		echo "<li><a href=\"../out/out.MyDocuments.php?inProcess=1\">".getMLText("documents_in_process")."</a></li>\n";
		echo "<li><a href=\"../out/out.MyDocuments.php\">".getMLText("all_documents")."</a></li>\n";
		if($this->params['workflowmode'] == 'traditional' || $this->params['workflowmode'] == 'traditional_only_approval') {
			if($this->params['workflowmode'] == 'traditional')
				echo "<li><a href=\"../out/out.ReviewSummary.php\">".getMLText("review_summary")."</a></li>\n";
			echo "<li><a href=\"../out/out.ApprovalSummary.php\">".getMLText("approval_summary")."</a></li>\n";
		} else {
			echo "<li><a href=\"../out/out.WorkflowSummary.php\">".getMLText("workflow_summary")."</a></li>\n";
		}
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */

	private function adminToolsNavigationBar() { /* {{{ */
		echo "    <id=\"first\"><a href=\"../out/out.AdminTools.php\" class=\"brand\">".getMLText("admin_tools")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "   <ul class=\"nav\">\n";

		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("user_group_management")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.UsrMgr.php\">".getMLText("user_management")."</a></li>\n";
		echo "      <li><a href=\"../out/out.GroupMgr.php\">".getMLText("group_management")."</a></li>\n";
		echo "      <li><a href=\"../out/out.UserList.php\">".getMLText("user_list")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("definitions")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.DefaultKeywords.php\">".getMLText("global_default_keywords")."</a></li>\n";
		echo "     <li><a href=\"../out/out.Categories.php\">".getMLText("global_document_categories")."</a></li>\n";
		echo "     <li><a href=\"../out/out.AttributeMgr.php\">".getMLText("global_attributedefinitions")."</a></li>\n";
		if($this->params['workflowmode'] == 'advanced') {
			echo "     <li><a href=\"../out/out.WorkflowMgr.php\">".getMLText("global_workflows")."</a></li>\n";
			echo "     <li><a href=\"../out/out.WorkflowStatesMgr.php\">".getMLText("global_workflow_states")."</a></li>\n";
			echo "     <li><a href=\"../out/out.WorkflowActionsMgr.php\">".getMLText("global_workflow_actions")."</a></li>\n";
		}
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		if($this->params['enablefullsearch']) {
			echo "   <ul class=\"nav\">\n";
			echo "    <li class=\"dropdown\">\n";
			echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("fullsearch")." <i class=\"icon-caret-down\"></i></a>\n";
			echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
			echo "      <li><a href=\"../out/out.Indexer.php\">".getMLText("update_fulltext_index")."</a></li>\n";
			echo "      <li><a href=\"../out/out.CreateIndex.php\">".getMLText("create_fulltext_index")."</a></li>\n";
			echo "      <li><a href=\"../out/out.IndexInfo.php\">".getMLText("fulltext_info")."</a></li>\n";
			echo "     </ul>\n";
			echo "    </li>\n";
			echo "   </ul>\n";
		}

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("backup_log_management")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.BackupTools.php\">".getMLText("backup_tools")."</a></li>\n";
		if ($this->params['logfileenable'])
			echo "      <li><a href=\"../out/out.LogManagement.php\">".getMLText("log_management")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "   <ul class=\"nav\">\n";
		echo "    <li class=\"dropdown\">\n";
		echo "     <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">".getMLText("misc")." <i class=\"icon-caret-down\"></i></a>\n";
		echo "     <ul class=\"dropdown-menu\" role=\"menu\">\n";
		echo "      <li><a href=\"../out/out.ImportFS.php\">".getMLText("import_fs")."</a></li>\n";
		echo "      <li><a href=\"../out/out.Statistic.php\">".getMLText("folders_and_documents_statistic")."</a></li>\n";
		echo "      <li><a href=\"../out/out.Charts.php\">".getMLText("charts")."</a></li>\n";
		echo "      <li><a href=\"../out/out.Timeline.php\">".getMLText("timeline")."</a></li>\n";
		echo "      <li><a href=\"../out/out.ObjectCheck.php\">".getMLText("objectcheck")."</a></li>\n";
		echo "      <li><a href=\"../out/out.ExpiredDocuments.php\">".getMLText("documents_expired")."</a></li>\n";
		echo "      <li><a href=\"../out/out.ExtensionMgr.php\">".getMLText("extension_manager")."</a></li>\n";
		echo "      <li><a href=\"../out/out.ClearCache.php\">".getMLText("clear_cache")."</a></li>\n";
		echo "      <li><a href=\"../out/out.Info.php\">".getMLText("version_info")."</a></li>\n";
		echo "     </ul>\n";
		echo "    </li>\n";
		echo "   </ul>\n";

		echo "<ul class=\"nav\">\n";
		echo "</ul>\n";
		echo "</div>\n";
		return;
	} /* }}} */
	
	private function calendarOldNavigationBar($d){ /* {{{ */
		$ds="&day=".$d[0]."&month=".$d[1]."&year=".$d[2];
		echo "<id=\"first\"><a href=\"../out/out.CalendarOld.php?mode=y\" class=\"brand\">".getMLText("calendar")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		echo "<li><a href=\"../out/out.CalendarOld.php?mode=w".$ds."\">".getMLText("week_view")."</a></li>\n";
		echo "<li><a href=\"../out/out.CalendarOld.php?mode=m".$ds."\">".getMLText("month_view")."</a></li>\n";
		echo "<li><a href=\"../out/out.CalendarOld.php?mode=y".$ds."\">".getMLText("year_view")."</a></li>\n";
		if (!$this->params['user']->isGuest()) echo "<li><a href=\"../out/out.AddEvent.php\">".getMLText("add_event")."</a></li>\n";
		echo "</ul>\n";
		echo "</div>\n";
		return;
	
	} /* }}} */

	private function calendarNavigationBar($d){ /* {{{ */
		echo "<id=\"first\"><a href=\"../out/out.Calendar.php\" class=\"brand\">".getMLText("calendar")."</a>\n";
		echo "<div class=\"nav-collapse col2\">\n";
		echo "<ul class=\"nav\">\n";

		if (!$this->params['user']->isGuest()) echo "<li><a href=\"../out/out.AddEvent.php\">".getMLText("add_event")."</a></li>\n";
		echo "</ul>\n";
		echo "</div>\n";
		return;
	
	} /* }}} */

	function pageList($pageNumber, $totalPages, $baseURI, $params) { /* {{{ */

		$maxpages = 25; // skip pages when more than this is shown
		$range = 5; // pages left and right of current page
		if (!is_numeric($pageNumber) || !is_numeric($totalPages) || $totalPages<2) {
			return;
		}

		// Construct the basic URI based on the $_GET array. One could use a
		// regular expression to strip out the pg (page number) variable to
		// achieve the same effect. This seems to be less haphazard though...
		$resultsURI = $baseURI;
		$first=true;
		foreach ($params as $key=>$value) {
			// Don't include the page number in the basic URI. This is added in
			// during the list display loop.
			if (!strcasecmp($key, "pg")) {
				continue;
			}
			if (is_array($value)) {
				foreach ($value as $subkey=>$subvalue) {
					$resultsURI .= ($first ? "?" : "&").$key."%5B".$subkey."%5D=".$subvalue;
					$first = false;
				}
			}
			else {
					$resultsURI .= ($first ? "?" : "&").$key."=".$value;
			}
			$first = false;
		}

		echo "<div class=\"pagination pagination-small\">";
		echo "<ul>";
		if($totalPages <= $maxpages) {
			for ($i = 1; $i <= $totalPages; $i++) {
				echo "<li ".($i == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$i."\">".$i."</a></li>";
			}
		} else {
			if($pageNumber-$range > 1)
				$start = $pageNumber-$range;
			else
				$start = 2;
			if($pageNumber+$range < $totalPages)
				$end = $pageNumber+$range;
			else
				$end = $totalPages-1;
			/* Move start or end to always show 2*$range items */
			$diff = $end-$start-2*$range;
			if($diff < 0) {
				if($start > 2)
					$start += $diff;
				if($end < $totalPages-1)
					$end -= $diff;
			}
			if($pageNumber > 1)
				echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".($pageNumber-1)."\">&laquo;</a></li>";
			echo "<li ".(1 == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=1\">1</a></li>";
			if($start > 2)
				echo "<li><span>...</span></li>";
			for($j=$start; $j<=$end; $j++)
				echo "<li ".($j == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$j."\">".$j."</a></li>";
			if($end < $totalPages-1)
				echo "<li><span>...</span></li>";
			if($end < $totalPages)
				echo "<li ".($totalPages == $pageNumber ? 'class="active"' : "" )."><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".$totalPages."\">".$totalPages."</a></li>";
			if($pageNumber < $totalPages)
				echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=".($pageNumber+1)."\">&raquo;</a></li>";
		}
		if ($totalPages>1) {
			echo "<li><a href=\"".$resultsURI.($first ? "?" : "&")."pg=all\">".getMLText("all_pages")."</a></li>";
		}
		echo "</ul>";
		echo "</div>";

		return;
	} /* }}} */

	function contentContainer($content) { /* {{{ */
		echo "<div class=\"well\">\n";
		echo $content;
		echo "</div>\n";
		return;
	} /* }}} */

	function contentContainerStart($class='') { /* {{{ */
		echo "<div class=\"well".($class ? " ".$class : "")."\">\n";
		return;
	} /* }}} */

	function contentContainerEnd() { /* {{{ */

		echo "</div>\n";
		return;
	} /* }}} */

	function contentHeading($heading, $noescape=false) { /* {{{ */

		if($noescape)
			echo "<legend>".$heading."</legend>\n";
		else
			echo "<legend>".htmlspecialchars($heading)."</legend>\n";
		return;
	} /* }}} */

	function contentSubHeading($heading, $first=false) { /* {{{ */

//		echo "<div class=\"contentSubHeading\"".($first ? " id=\"first\"" : "").">".htmlspecialchars($heading)."</div>\n";
		echo "<h5>".$heading."</h5>";
		return;
	} /* }}} */

	function getMimeIcon($fileType) { /* {{{ */
		// for extension use LOWER CASE only
		$icons = array();
		$icons["txt"]  = "text-x-preview.svg";
		$icons["text"] = "text-x-preview.svg";
		$icons["tex"]  = "text-x-preview.svg";
		$icons["doc"]  = "office-document.svg";
		$icons["dot"]  = "office-document.svg";
		$icons["docx"] = "office-document.svg";
		$icons["dotx"] = "office-document.svg";
		$icons["rtf"]  = "office-document.svg";
		$icons["xls"]  = "office-spreadsheet.svg";
		$icons["xlt"]  = "office-spreadsheet.svg";
		$icons["xlsx"] = "office-spreadsheet.svg";
		$icons["xltx"] = "office-spreadsheet.svg";
		$icons["ppt"]  = "office-presentation.svg";
		$icons["pot"]  = "office-presentation.svg";
		$icons["pptx"] = "office-presentation.svg";
		$icons["potx"] = "office-presentation.svg";
		$icons["exe"]  = "executable.svg";
		$icons["html"] = "web.svg";
		$icons["htm"]  = "web.svg";
		$icons["gif"]  = "image.svg";
		$icons["jpg"]  = "image.svg";
		$icons["jpeg"] = "image.svg";
		$icons["bmp"]  = "image.svg";
		$icons["png"]  = "image.svg";
		$icons["tif"]  = "image.svg";
		$icons["tiff"] = "image.svg";
		$icons["log"]  = "text-x-preview.svg";
		$icons["midi"] = "audio.svg";
		$icons["pdf"]  = "gnome-mime-application-pdf.svg";
		$icons["wav"]  = "audio.svg";
		$icons["mp3"]  = "audio.svg";
		$icons["opus"]  = "audio.svg";
		$icons["c"]    = "text-x-preview.svg";
		$icons["cpp"]  = "text-x-preview.svg";
		$icons["h"]    = "text-x-preview.svg";
		$icons["java"] = "text-x-preview.svg";
		$icons["py"]   = "text-x-preview.svg";
		$icons["tar"]  = "package.svg";
		$icons["gz"]   = "package.svg";
		$icons["7z"]   = "package.svg";
		$icons["bz"]   = "package.svg";
		$icons["bz2"]  = "package.svg";
		$icons["tgz"]  = "package.svg";
		$icons["zip"]  = "package.svg";
		$icons["rar"]  = "package.svg";
		$icons["mpg"]  = "video.svg";
		$icons["avi"]  = "video.svg";
		$icons["ods"]  = "office-spreadsheet.svg";
		$icons["ots"]  = "office-spreadsheet.svg";
		$icons["sxc"]  = "office-spreadsheet.svg";
		$icons["stc"]  = "office-spreadsheet.svg";
		$icons["odt"]  = "office-document.svg";
		$icons["ott"]  = "office-document.svg";
		$icons["sxw"]  = "office-document.svg";
		$icons["stw"]  = "office-document.svg";
		$icons["odp"]  = "office-presentation.svg";
		$icons["otp"]  = "office-presentation.svg";
		$icons["sxi"]  = "office-presentation.svg";
		$icons["sti"]  = "office-presentation.svg";
		$icons["odg"]  = "office-drawing.svg";
		$icons["otg"]  = "office-drawing.svg";
		$icons["sxd"]  = "office-drawing.svg";
		$icons["std"]  = "office-drawing.svg";
		$icons["odf"]  = "ooo_formula.png";
		$icons["sxm"]  = "ooo_formula.png";
		$icons["smf"]  = "ooo_formula.png";
		$icons["mml"]  = "ooo_formula.png";

		$icons["default"] = "text-x-preview.svg"; //"default.png";

		$ext = strtolower(substr($fileType, 1));
		if (isset($icons[$ext])) {
			return $this->imgpath.$icons[$ext];
		}
		else {
			return $this->imgpath.$icons["default"];
		}
	} /* }}} */

	function getFileChooser($varname='userfile', $multiple=false, $accept='') { /* {{{ */
		$id = preg_replace('/[^A-Za-z]/', '', $varname);
		$html = '
	<div id="'.$id.'-upload-files">
		<div id="'.$id.'-upload-file" class="upload-file">
			<div class="input-append">
				<input type="text" class="form-control" readonly>
				<span class="btn btn-default btn-file">
					'.getMLText("browse").'&hellip; <input id="'.$id.'" type="file" name="'.$varname.'"'.($multiple ? " multiple" : "").($accept ? ' accept="'.$accept.'"' : "").'">
				</span>
			</div>
		</div>
	</div>
';
		return $html;
	} /* }}} */

	function printFileChooser($varname='userfile', $multiple=false, $accept='') { /* {{{ */
		echo $this->getFileChooser($varname, $multiple, $accept);
		return;
		$id = preg_replace('/[^A-Za-z]/', '', $varname);
?>
	<div id="<?php echo $id; ?>-upload-files">
		<div id="<?php echo $id; ?>-upload-file" class="upload-file">
			<div class="input-append">
				<input type="text" class="form-control" readonly>
				<span class="btn btn-default btn-file">
					<?php printMLText("browse");?>&hellip; <input id="<?php echo $id; ?>" type="file" name="<?php echo $varname; ?>"<?php if($multiple) echo " multiple"; ?><?php if($accept) echo " accept=\"".$accept."\""; ?>>
				</span>
			</div>
		</div>
	</div>
<?php
	} /* }}} */

	function printDateChooser($defDate = -1, $varName) { /* {{{ */
	
		if ($defDate == -1)
			$defDate = mktime();
		$day   = date("d", $defDate);
		$month = date("m", $defDate);
		$year  = date("Y", $defDate);

		print "<select name=\"" . $varName . "day\">\n";
		for ($i = 1; $i <= 31; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($day) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select> \n";
		print "<select name=\"" . $varName . "month\">\n";
		for ($i = 1; $i <= 12; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($month) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select> \n";
		print "<select name=\"" . $varName . "year\">\n";	
		for ($i = $year-5 ; $i <= $year+5 ; $i++)
		{
			print "<option value=\"" . $i . "\"";
			if (intval($year) == $i)
				print " selected";
			print ">" . $i . "</option>\n";
		}
		print "</select>";
	} /* }}} */

	function printSequenceChooser($objArr, $keepID = -1) { /* {{{ */
		if (count($objArr) > 0) {
			$max = $objArr[count($objArr)-1]->getSequence() + 1;
			$min = $objArr[0]->getSequence() - 1;
		}
		else {
			$max = 1.0;
		}
		print "<select name=\"sequence\">\n";
		if ($keepID != -1) {
			print "  <option value=\"keep\">" . getMLText("seq_keep");
		}
		print "  <option value=\"".$max."\">" . getMLText("seq_end");
		if (count($objArr) > 0) {
			print "  <option value=\"".$min."\">" . getMLText("seq_start");
		}
		for ($i = 0; $i < count($objArr) - 1; $i++) {
			if (($objArr[$i]->getID() == $keepID) || (($i + 1 < count($objArr)) && ($objArr[$i+1]->getID() == $keepID))) {
				continue;
			}
			$index = ($objArr[$i]->getSequence() + $objArr[$i+1]->getSequence()) / 2;
			print "  <option value=\"".$index."\">" . getMLText("seq_after", array("prevname" => htmlspecialchars($objArr[$i]->getName())));
		}
		print "</select>";
	} /* }}} */

	function printDocumentChooserHtml($formName) { /* {{{ */
		print "<input type=\"hidden\" id=\"docid".$formName."\" name=\"docid\" value=\"\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" id=\"choosedocsearch".$formName."\" data-target=\"docid".$formName."\" data-provide=\"typeahead\" name=\"docname".$formName."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" />";
		print "<a data-target=\"#docChooser".$formName."\" href=\"../out/out.DocumentChooser.php?form=".$formName."&folderid=".$this->params['rootfolderid']."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("document")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="docChooser<?php echo $formName ?>" tabindex="-1" role="dialog" aria-labelledby="docChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="docChooserLabel"><?php printMLText("choose_target_document") ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('tree_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
  </div>
</div>
<?php 
	} /* }}} */

	function printDocumentChooserJs($formName) { /* {{{ */
?>
function documentSelected<?php echo $formName ?>(id, name) {
	$('#docid<?php echo $formName ?>').val(id);
	$('#choosedocsearch<?php echo $formName ?>').val(name);
	$('#docChooser<?php echo $formName ?>').modal('hide');
}
function folderSelected<?php echo $formName ?>(id, name) {
}
<?php
	} /* }}} */

	function printDocumentChooser($formName) { /* {{{ */
		$this->printDocumentChooserHtml($formName);
?>
		<script language="JavaScript">
<?php
		$this->printDocumentChooserJs($formName);
?>
		</script>
<?php
	} /* }}} */

	function printFolderChooserHtml($form, $accessMode, $exclude = -1, $default = false, $formname = '') { /* {{{ */
		$formid = "targetid".$form;
		if(!$formname)
			$formname = "targetid";
		print "<input type=\"hidden\" id=\"".$formid."\" name=\"".$formname."\" value=\"". (($default) ? $default->getID() : "") ."\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" id=\"choosefoldersearch".$form."\" data-target=\"".$formid."\" data-provide=\"typeahead\"  name=\"targetname".$form."\" value=\"". (($default) ? htmlspecialchars($default->getName()) : "") ."\" placeholder=\"".getMLText('type_to_search')."\" autocomplete=\"off\" target=\"".$formid."\"/>";
		print "<button type=\"button\" class=\"btn\" id=\"clearfolder".$form."\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#folderChooser".$form."\" href=\"../out/out.FolderChooser.php?form=".$form."&mode=".$accessMode."&exclude=".$exclude."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("folder")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="folderChooser<?php echo $form ?>" tabindex="-1" role="dialog" aria-labelledby="folderChooser<?php echo $form ?>Label" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="folderChooser<?php echo $form ?>Label"><?php printMLText("choose_target_folder") ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('tree_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printFolderChooserJs($form) { /* {{{ */
?>
function folderSelected<?php echo $form ?>(id, name) {
	$('#targetid<?php echo $form ?>').val(id);
	$('#choosefoldersearch<?php echo $form ?>').val(name);
	$('#folderChooser<?php echo $form ?>').modal('hide');
}
$(document).ready(function() {
	$('#clearfolder<?php print $form ?>').click(function(ev) {
		$('#choosefoldersearch<?php echo $form ?>').val('');
		$('#targetid<?php echo $form ?>').val('');
	});
});
<?php
	} /* }}} */

	function printFolderChooser($form, $accessMode, $exclude = -1, $default = false, $formname='') { /* {{{ */
		$this->printFolderChooserHtml($form, $accessMode, $exclude, $default, $formname);
?>
		<script language="JavaScript">
<?php
		$this->printFolderChooserJs($form);
?>
		</script>
<?php
	} /* }}} */

	/**
	 * Do not use anymore. Was previously used to show the category
	 * chooser. It has been replaced by a select box
	 */
	function printCategoryChooser($formName, $categories=array()) { /* {{{ */
?>
<script language="JavaScript">
	function clearCategory<?php print $formName ?>() {
		document.<?php echo $formName ?>.categoryid<?php echo $formName ?>.value = '';
		document.<?php echo $formName ?>.categoryname<?php echo $formName ?>.value = '';
	}

	function acceptCategories() {
		var targetName = document.<?php echo $formName?>.categoryname<?php print $formName ?>;
		var targetID = document.<?php echo $formName?>.categoryid<?php print $formName ?>;
		var value = '';
		$('#keywordta option:selected').each(function(){
			value += ' ' + $(this).text();
		});
		targetName.value = value;
		targetID.value = $('#keywordta').val();
		return true;
	}
</script>
<?php
		$ids = $names = array();
		if($categories) {
			foreach($categories as $cat) {
				$ids[] = $cat->getId();
				$names[] = htmlspecialchars($cat->getName());
			}
		}
		print "<input type=\"hidden\" name=\"categoryid".$formName."\" value=\"".implode(',', $ids)."\">";
		print "<div class=\"input-append\">\n";
		print "<input type=\"text\" disabled name=\"categoryname".$formName."\" value=\"".implode(' ', $names)."\">";
		print "<button type=\"button\" class=\"btn\" onclick=\"javascript:clearCategory".$formName."();\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#categoryChooser\" href=\"../out/out.CategoryChooser.php?form=form1&cats=".implode(',', $ids)."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".getMLText("category")."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="categoryChooser" tabindex="-1" role="dialog" aria-labelledby="categoryChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="categoryChooserLabel"><?php printMLText("choose_target_category") ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('categories_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
    <button class="btn" data-dismiss="modal" aria-hidden="true" onClick="acceptCategories();"><i class="icon-save"></i> <?php printMLText("save") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printKeywordChooserHtml($formName, $keywords='', $fieldname='keywords') { /* {{{ */
		$strictformcheck = $this->params['strictformcheck'];
?>
		    <div class="input-append">
				<input type="text" name="<?php echo $fieldname; ?>" id="<?php echo $fieldname; ?>" value="<?php print htmlspecialchars($keywords);?>"<?php echo $strictformcheck ? ' required' : ''; ?> />
				<a data-target="#keywordChooser" role="button" class="btn" data-toggle="modal" href="../out/out.KeywordChooser.php?target=<?php echo $formName; ?>"><?php printMLText("keywords");?>…</a>
		    </div>
<div class="modal hide" id="keywordChooser" tabindex="-1" role="dialog" aria-labelledby="keywordChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="keywordChooserLabel"><?php printMLText("use_default_keywords") ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('keywords_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
    <button class="btn" data-dismiss="modal" aria-hidden="true" id="acceptkeywords"><i class="icon-save"></i> <?php printMLText("save") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printKeywordChooserJs($formName) { /* {{{ */
?>
$(document).ready(function() {
	$('#acceptkeywords').click(function(ev) {
		acceptKeywords();
	});
});
<?php
	} /* }}} */

	function printKeywordChooser($formName, $keywords='', $fieldname='keywords') { /* {{{ */
		$this->printKeywordChooserHtml($formName, $keywords, $fieldname);
?>
		<script language="JavaScript">
<?php
		$this->printKeywordChooserJs($formName);
?>
		</script>
<?php
	} /* }}} */

	function printAttributeEditField($attrdef, $attribute, $fieldname='attributes', $norequire=false) { /* {{{ */
		switch($attrdef->getType()) {
		case SeedDMS_Core_AttributeDefinition::type_boolean:
			echo "<input type=\"hidden\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"0\" />";
			echo "<input type=\"checkbox\" id=\"".$fieldname."_".$attrdef->getId()."\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"1\" ".(($attribute && $attribute->getValue()) ? 'checked' : '')." />";
			break;
		case SeedDMS_Core_AttributeDefinition::type_date:
				$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValue() : $attribute) : '';
?>
        <span class="input-append date datepicker" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd" data-date-language="<?php echo str_replace('_', '-', $this->params['session']->getLanguage()); ?>">
					<input id="<?php echo $fieldname."_".$attrdef->getId();?>" class="span9" size="16" name="<?php echo $fieldname ?>[<?php echo $attrdef->getId() ?>]" type="text" value="<?php if($objvalue) echo $objvalue; else echo "" /*date('Y-m-d')*/; ?>">
          <span class="add-on"><i class="icon-calendar"></i></span>
				</span>
<?php
			break;
		case SeedDMS_Core_AttributeDefinition::type_email:
			$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValue() : $attribute) : '';
			echo "<input type=\"text\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"".htmlspecialchars($objvalue)."\"".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').' data-rule-email="true"'." />";
			break;
		default:
			if($valueset = $attrdef->getValueSetAsArray()) {
				echo "<input type=\"hidden\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"\" />";
				echo "<select id=\"".$fieldname."_".$attrdef->getId()."\" name=\"".$fieldname."[".$attrdef->getId()."]";
				if($attrdef->getMultipleValues()) {
					echo "[]\" multiple";
				} else {
					echo "\"";
				}
				echo "".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').">";
				if(!$attrdef->getMultipleValues()) {
					echo "<option value=\"\"></option>";
				}
				$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValueAsArray() : $attribute) : array();
				foreach($valueset as $value) {
					if($value) {
						echo "<option value=\"".htmlspecialchars($value)."\"";
						if(is_array($objvalue) && in_array($value, $objvalue))
							echo " selected";
						elseif($value == $objvalue)
							echo " selected";
						echo ">".htmlspecialchars($value)."</option>";
					}
				}
				echo "</select>";
			} else {
				$objvalue = $attribute ? (is_object($attribute) ? $attribute->getValue() : $attribute) : '';
				if(strlen($objvalue) > 80) {
					echo "<textarea id=\"".$fieldname."_".$attrdef->getId()."\" class=\"input-xxlarge\" name=\"".$fieldname."[".$attrdef->getId()."]\"".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').">".htmlspecialchars($objvalue)."</textarea>";
				} else {
					echo "<input type=\"text\" id=\"".$fieldname."_".$attrdef->getId()."\" name=\"".$fieldname."[".$attrdef->getId()."]\" value=\"".htmlspecialchars($objvalue)."\"".((!$norequire && $attrdef->getMinValues() > 0) ? ' required' : '').($attrdef->getType() == SeedDMS_Core_AttributeDefinition::type_int ? ' data-rule-digits="true"' : '')." />";
				}
			}
			break;
		}
	} /* }}} */

	function printDropFolderChooserHtml($formName, $dropfolderfile="", $showfolders=0) { /* {{{ */
		print "<div class=\"input-append\">\n";
		print "<input readonly type=\"text\" id=\"dropfolderfile".$formName."\" name=\"dropfolderfile".$formName."\" value=\"".$dropfolderfile."\">";
		print "<button type=\"button\" class=\"btn\" id=\"clearfilename".$formName."\"><i class=\"icon-remove\"></i></button>";
		print "<a data-target=\"#dropfolderChooser\" href=\"../out/out.DropFolderChooser.php?form=form1&dropfolderfile=".urlencode($dropfolderfile)."&showfolders=".$showfolders."\" role=\"button\" class=\"btn\" data-toggle=\"modal\">".($showfolders ? getMLText("choose_target_folder"): getMLText("choose_target_file"))."…</a>\n";
		print "</div>\n";
?>
<div class="modal hide" id="dropfolderChooser" tabindex="-1" role="dialog" aria-labelledby="dropfolderChooserLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="dropfolderChooserLabel"><?php echo ($showfolders ? getMLText("choose_target_folder"): getMLText("choose_target_file")) ?></h3>
  </div>
  <div class="modal-body">
		<p><?php printMLText('files_loading') ?></p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php printMLText("close") ?></button>
  </div>
</div>
<?php
	} /* }}} */

	function printDropFolderChooserJs($formName, $showfolders=0) { /* {{{ */
?>
/* Set up a callback which is called when a folder in the tree is selected */
modalDropfolderChooser = $('#dropfolderChooser');
function fileSelected(name) {
	$('#dropfolderfile<?php echo $formName ?>').val(name);
	modalDropfolderChooser.modal('hide');
}
<?php if($showfolders) { ?>
function folderSelected(name) {
	$('#dropfolderfile<?php echo $formName ?>').val(name);
	modalDropfolderChooser.modal('hide');
}
<?php } ?>
$(document).ready(function() {
	$('#clearfilename<?php print $formName ?>').click(function(ev) {
		$('#dropfolderfile<?php echo $formName ?>').val('');
	});
});
<?php
	} /* }}} */

	function printDropFolderChooser($formName, $dropfolderfile="", $showfolders=0) { /* {{{ */
		$this->printDropFolderChooserHtml($formName, $dropfolderfile, $showfolders);
?>
		<script language="JavaScript">
<?php
		$this->printDropFolderChooserJs($formName, $showfolders);
?>
		</script>
<?php
	} /* }}} */

	function getImgPath($img) { /* {{{ */

		if ( is_file($this->imgpath.$img) ) {
			return $this->imgpath.$img;
		}
		return "../out/images/$img";
	} /* }}} */

	function getCountryFlag($lang) { /* {{{ */
		switch($lang) {
		case "en_GB":
			return 'flags/gb.png';
			break;
		default:
			return 'flags/'.substr($lang, 0, 2).'.png';
		}
	} /* }}} */

	function printImgPath($img) { /* {{{ */
		print $this->getImgPath($img);
	} /* }}} */

	function infoMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-info\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function warningMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-warning\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function errorMsg($msg) { /* {{{ */
		echo "<div class=\"alert alert-error\">\n";
		echo $msg;
		echo "</div>\n";
	} /* }}} */

	function exitError($pagetitle, $error, $noexit=false) { /* {{{ */
	
		$this->htmlStartPage($pagetitle);
		$this->globalNavigation();
		$this->contentStart();

		print "<div class=\"alert alert-error\">";
		print "<h4>".getMLText('error')."!</h4>";
		print htmlspecialchars($error);
		print "</div>";
		print "<div><button class=\"btn history-back\">".getMLText('back')."</button></div>";
		
		$this->contentEnd();
		$this->htmlEndPage();
		
		add_log_line(" UI::exitError error=".$error." pagetitle=".$pagetitle, PEAR_LOG_ERR);

		if($noexit)
			return;

		exit;	
	} /* }}} */

	function printNewTreeNavigation($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		$this->printNewTreeNavigationHtml($folderid, $accessmode, $showdocs, $formid, $expandtree, $orderby);
?>
		<script language="JavaScript">
<?php
		$this->printNewTreeNavigationJs($folderid, $accessmode, $showdocs, $formid, $expandtree, $orderby);
?>
	</script>
<?php
	} /* }}} */

	function printNewTreeNavigationHtml($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		echo "<div id=\"jqtree".$formid."\" style=\"margin-left: 10px;\" data-url=\"../op/op.Ajax.php?command=subtree&showdocs=".$showdocs."&orderby=".$orderby."\"></div>\n";
	} /* }}} */

	/**
	 * Create a tree of folders using jqtree.
	 *
	 * The tree can contain folders only or include documents.
	 *
	 * @param integer $folderid current folderid. If set the tree will be
	 *   folded out and the all folders in the path will be visible
	 * @param integer $accessmode use this access mode when retrieving folders
	 *   and documents shown in the tree
	 * @param boolean $showdocs set to true if tree shall contain documents
	 *   as well.
	 */
	function printNewTreeNavigationJs($folderid=0, $accessmode=M_READ, $showdocs=0, $formid='form1', $expandtree=0, $orderby='') { /* {{{ */
		function jqtree($path, $folder, $user, $accessmode, $showdocs=1, $expandtree=0, $orderby='') {
			if($path || $expandtree) {
				if($path)
					$pathfolder = array_shift($path);
				$subfolders = $folder->getSubFolders($orderby);
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>($subfolder->hasSubFolders() || ($subfolder->hasDocuments() && $showdocs)) ? true : false, 'is_folder'=>true);
					if($expandtree || $pathfolder->getID() == $subfolder->getID()) {
						if($showdocs) {
							$documents = $folder->getDocuments($orderby);
							$documents = SeedDMS_Core_DMS::filterAccess($documents, $user, $accessmode);
							foreach($documents as $document) {
								$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'load_on_demand'=>false, 'is_folder'=>false);
								$children[] = $node2;
							}
						}
						$node['children'] = jqtree($path, $subfolder, $user, $accessmode, $showdocs, $expandtree, $orderby);
					}
					$children[] = $node;
				}
				return $children;
			} else {
				$subfolders = $folder->getSubFolders($orderby);
				$subfolders = SeedDMS_Core_DMS::filterAccess($subfolders, $user, $accessmode);
				$children = array();
				foreach($subfolders as $subfolder) {
					$node = array('label'=>$subfolder->getName(), 'id'=>$subfolder->getID(), 'load_on_demand'=>($subfolder->hasSubFolders() || ($subfolder->hasDocuments() && $showdocs)) ? true : false, 'is_folder'=>true);
					$children[] = $node;
				}
				return $children;
			}
			return array();
		}

		if($folderid) {
			$folder = $this->params['dms']->getFolder($folderid);
			$path = $folder->getPath();
			$folder = array_shift($path);
			$node = array('label'=>$folder->getName(), 'id'=>$folder->getID(), 'load_on_demand'=>true, 'is_folder'=>true);
			if(!$folder->hasSubFolders()) {
				$node['load_on_demand'] = false;
				$node['children'] = array();
			} else {
				$node['children'] = jqtree($path, $folder, $this->params['user'], $accessmode, $showdocs, $expandtree, $orderby);
				if($showdocs) {
					$documents = $folder->getDocuments($orderby);
					$documents = SeedDMS_Core_DMS::filterAccess($documents, $this->params['user'], $accessmode);
					foreach($documents as $document) {
						$node2 = array('label'=>$document->getName(), 'id'=>$document->getID(), 'load_on_demand'=>false, 'is_folder'=>false);
						$node['children'][] = $node2;
					}
				}
			}
			/* Nasty hack to remove the highest folder */
			if(isset($this->params['remove_root_from_tree']) && $this->params['remove_root_from_tree']) {
				foreach($node['children'] as $n)
					$tree[] = $n;
			} else {
				$tree[] = $node;
			}
			
		} else {
			$root = $this->params['dms']->getFolder($this->params['rootfolderid']);
			$tree = array(array('label'=>$root->getName(), 'id'=>$root->getID(), 'load_on_demand'=>true, 'is_folder'=>true));
		}

?>
var data = <?php echo json_encode($tree); ?>;
$(function() {
	$('#jqtree<?php echo $formid ?>').tree({
		saveState: true,
		data: data,
		saveState: 'jqtree<?php echo $formid; ?>',
		openedIcon: '<i class="icon-minus-sign"></i>',
		closedIcon: '<i class="icon-plus-sign"></i>',
		_onCanSelectNode: function(node) {
			if(node.is_folder) {
				folderSelected<?php echo $formid ?>(node.id, node.name);
			} else
				documentSelected<?php echo $formid ?>(node.id, node.name);
		},
		autoOpen: true,
		drapAndDrop: true,
    onCreateLi: function(node, $li) {
        // Add 'icon' span before title
				if(node.is_folder)
					$li.find('.jqtree-title').before('<i class="icon-folder-close-alt table-row-folder" rel="folder_' + node.id + '"></i> ').attr('rel', 'folder_' + node.id).attr('formtoken', '<?php echo createFormKey('movefolder'); ?>');
				else
					$li.find('.jqtree-title').before('<i class="icon-file"></i> ');
    }
	});
	// Unfold tree if folder is opened
	$('#jqtree<?php echo $formid ?>').tree('openNode', $('#jqtree<?php echo $formid ?>').tree('getNodeById', <?php echo $folderid ?>), false);
  $('#jqtree<?php echo $formid ?>').bind(
		'tree.click',
		function(event) {
			var node = event.node;
			$('#jqtree<?php echo $formid ?>').tree('openNode', node);
//			event.preventDefault();
			if(node.is_folder) {
				folderSelected<?php echo $formid ?>(node.id, node.name);
			} else
				documentSelected<?php echo $formid ?>(node.id, node.name);
		}
	);
});
<?php
	} /* }}} */

	function printTreeNavigation($folderid, $showtree){ /* {{{ */
		if ($showtree==1){
			$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=0\"><i class=\"icon-minus-sign\"></i></a>", true);
			$this->contentContainerStart();
?>
	<script language="JavaScript">
	function folderSelected(id, name) {
		window.location = '../out/out.ViewFolder.php?folderid=' + id;
	}
	</script>
<?php
			$this->printNewTreeNavigation($folderid, M_READ, 0, '');
			$this->contentContainerEnd();
		} else {
			$this->contentHeading("<a href=\"../out/out.ViewFolder.php?folderid=". $folderid."&showtree=1\"><i class=\"icon-plus-sign\"></i></a>", true);
		}
	} /* }}} */

	/**
	 * Print clipboard in div container
	 *
	 * @param array clipboard
	 */
	function printClipboard($clipboard, $previewer){ /* {{{ */
		$this->contentHeading(getMLText("clipboard"), true);
		echo "<div id=\"main-clipboard\">\n";
?>
		<div class="ajax" data-view="Clipboard" data-action="mainClipboard"></div>
<?php
		echo "</div>\n";
	} /* }}} */

	/**
	 * Print button with link for deleting a document
	 *
	 * This button is used in document listings (e.g. on the ViewFolder page)
	 * for deleting a document. In seeddms version < 4.3.9 this was just a
	 * link to the out/out.RemoveDocument.php page which asks for confirmation
	 * an than calls op/op.RemoveDocument.php. Starting with version 4.3.9
	 * the button just opens a small popup asking for confirmation and than
	 * calls the ajax command 'deletedocument'. The ajax call is called
	 * in the click function of 'button.removedocument'. That button needs
	 * to have two attributes: 'rel' for the id of the document, and 'msg'
	 * for the message shown by notify if the document could be deleted.
	 *
	 * @param object $document document to be deleted
	 * @param string $msg message shown in case of successful deletion
	 * @param boolean $return return html instead of printing it
	 * @return string html content if $return is true, otherwise an empty string
	 */
	function printDeleteDocumentButton($document, $msg, $return=false){ /* {{{ */
		$docid = $document->getID();
		$content = '';
    $content .= '<a class="delete-document-btn" rel="'.$docid.'" msg="'.getMLText($msg).'" confirmmsg="'.htmlspecialchars(getMLText("confirm_rm_document", array ("documentname" => $document->getName())), ENT_QUOTES).'"><i class="icon-remove"></i></a>';
		if($return)
			return $content;
		else
			echo $content;
		return '';
	} /* }}} */

	function printDeleteDocumentButtonJs(){ /* {{{ */
		echo "
		$(document).ready(function () {
//			$('.delete-document-btn').click(function(ev) {
			$('body').on('click', 'a.delete-document-btn', function(ev){
				id = $(ev.currentTarget).attr('rel');
				confirmmsg = $(ev.currentTarget).attr('confirmmsg');
				msg = $(ev.currentTarget).attr('msg');
				formtoken = '".createFormKey('removedocument')."';
				bootbox.dialog(confirmmsg, [{
					\"label\" : \"<i class='icon-remove'></i> ".getMLText("rm_document")."\",
					\"class\" : \"btn-danger\",
					\"callback\": function() {
						$.get('../op/op.Ajax.php',
							{ command: 'deletedocument', id: id, formtoken: formtoken },
							function(data) {
								if(data.success) {
									$('#table-row-document-'+id).hide('slow');
									noty({
										text: msg,
										type: 'success',
										dismissQueue: true,
										layout: 'topRight',
										theme: 'defaultTheme',
										timeout: 1500,
									});
								} else {
									noty({
										text: data.message,
										type: 'error',
										dismissQueue: true,
										layout: 'topRight',
										theme: 'defaultTheme',
										timeout: 3500,
									});
								}
							},
							'json'
						);
					}
				}, {
					\"label\" : \"".getMLText("cancel")."\",
					\"class\" : \"btn-cancel\",
					\"callback\": function() {
					}
				}]);
			});
		});
		";
	} /* }}} */

	/**
	 * Print button with link for deleting a folder
	 *
	 * This button works like document delete button
	 * {@link SeedDMS_Bootstrap_Style::printDeleteDocumentButton()}
	 *
	 * @param object $folder folder to be deleted
	 * @param string $msg message shown in case of successful deletion
	 * @param boolean $return return html instead of printing it
	 * @return string html content if $return is true, otherwise an empty string
	 */
	function printDeleteFolderButton($folder, $msg, $return=false){ /* {{{ */
		$folderid = $folder->getID();
		$content = '';
		$content .= '<a class="delete-folder-btn" rel="'.$folderid.'" msg="'.getMLText($msg).'" confirmmsg="'.htmlspecialchars(getMLText("confirm_rm_folder", array ("foldername" => $folder->getName())), ENT_QUOTES).'"><i class="icon-remove"></i></a>';
		if($return)
			return $content;
		else
			echo $content;
		return '';
	} /* }}} */

	function printDeleteFolderButtonJs(){ /* {{{ */
		echo "
		$(document).ready(function () {
//			$('.delete-folder-btn').click(function(ev) {
			$('body').on('click', 'a.delete-folder-btn', function(ev){
				id = $(ev.currentTarget).attr('rel');
				confirmmsg = $(ev.currentTarget).attr('confirmmsg');
				msg = $(ev.currentTarget).attr('msg');
				formtoken = '".createFormKey('removefolder')."';
				bootbox.dialog(confirmmsg, [{
					\"label\" : \"<i class='icon-remove'></i> ".getMLText("rm_folder")."\",
					\"class\" : \"btn-danger\",
					\"callback\": function() {
						$.get('../op/op.Ajax.php',
							{ command: 'deletefolder', id: id, formtoken: formtoken },
							function(data) {
								if(data.success) {
									$('#table-row-folder-'+id).hide('slow');
									noty({
										text: msg,
										type: 'success',
										dismissQueue: true,
										layout: 'topRight',
										theme: 'defaultTheme',
										timeout: 1500,
									});
								} else {
									noty({
										text: data.message,
										type: 'error',
										dismissQueue: true,
										layout: 'topRight',
										theme: 'defaultTheme',
										timeout: 3500,
									});
								}
							},
							'json'
						);
					}
				}, {
					\"label\" : \"".getMLText("cancel")."\",
					\"class\" : \"btn-cancel\",
					\"callback\": function() {
					}
				}]);
			});
		});
		";
	} /* }}} */

	function printLockButton($document, $msglock, $msgunlock, $return=false) { /* {{{ */
		$docid = $document->getID();
		if($document->isLocked()) {
			$icon = 'unlock';
			$msg = $msgunlock;
			$title = 'unlock_document';
		} else {
			$icon = 'lock';
			$msg = $msglock;
			$title = 'lock_document';
		}
		$content = '';
    $content .= '<a class="lock-document-btn" rel="'.$docid.'" msg="'.getMLText($msg).'" title="'.getMLText($title).'"><i class="icon-'.$icon.'"></i></a>';
		if($return)
			return $content;
		else
			echo $content;
		return '';
	} /* }}} */

	/**
	 * Output left-arrow with link which takes over a number of ids into
	 * a select box.
	 *
	 * Clicking in the button will preset the comma seperated list of ids
	 * in data-ref as options in the select box with name $name
	 *
	 * @param string $name id of select box
	 * @param array $ids list of option values
	 */
	function printSelectPresetButtonHtml($name, $ids) { /* {{{ */
?>
	<span id="<?php echo $name; ?>_btn" class="selectpreset_btn" style="cursor: pointer;" title="<?php printMLText("takeOver".$name); ?>" data-ref="<?php echo $name; ?>" data-ids="<?php echo implode(",", $ids);?>"><i class="icon-arrow-left"></i></span>
<?php
	} /* }}} */

	/**
	 * Javascript code for select preset button
	 */
	function printSelectPresetButtonJs() { /* {{{ */
?>
$(document).ready( function() {
	$('.selectpreset_btn').click(function(ev){
		ev.preventDefault();
		if (typeof $(ev.currentTarget).data('ids') != 'undefined') {
			target = $(ev.currentTarget).data('ref');
			// Use attr() instead of data() because data() converts to int which cannot be split
			items = $(ev.currentTarget).attr('data-ids');
			arr = items.split(",");
			for(var i in arr) {
				$("#"+target+" option[value='"+arr[i]+"']").attr("selected", "selected");
			}
//			$("#"+target).trigger("chosen:updated");
			$("#"+target).trigger("change");
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Output left-arrow with link which takes over a string into
	 * a input field.
	 *
	 * Clicking on the button will preset the string
	 * in data-ref the value of the input field with name $name
	 *
	 * @param string $name id of select box
	 * @param string $text text
	 */
	function printInputPresetButtonHtml($name, $text, $sep='') { /* {{{ */
?>
	<span id="<?php echo $name; ?>_btn" class="inputpreset_btn" style="cursor: pointer;" title="<?php printMLText("takeOverAttributeValue"); ?>" data-ref="<?php echo $name; ?>" data-text="<?php echo is_array($text) ? implode($sep, $text) : htmlspecialchars($text);?>"<?php if($sep) echo "data-sep=\"".$sep."\""; ?>><i class="icon-arrow-left"></i></span>
<?php
	} /* }}} */

	/**
	 * Javascript code for input preset button
	 * This code workѕ for input fields and single select fields
	 */
	function printInputPresetButtonJs() { /* {{{ */
?>
$(document).ready( function() {
	$('.inputpreset_btn').click(function(ev){
		ev.preventDefault();
		if (typeof $(ev.currentTarget).data('text') != 'undefined') {
			target = $(ev.currentTarget).data('ref');
			value = $(ev.currentTarget).data('text');
			sep = $(ev.currentTarget).data('sep');
			if(sep) {
				// Use attr() instead of data() because data() converts to int which cannot be split
				arr = value.split(sep);
				for(var i in arr) {
					$("#"+target+" option[value='"+arr[i]+"']").attr("selected", "selected");
				}
			} else {
				$("#"+target).val(value);
			}
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Output left-arrow with link which takes over a boolean value
	 * into a checkbox field.
	 *
	 * Clicking on the button will preset the checkbox
	 * in data-ref the value of the input field with name $name
	 *
	 * @param string $name id of select box
	 * @param string $text text
	 */
	function printCheckboxPresetButtonHtml($name, $text) { /* {{{ */
?>
	<span id="<?php echo $name; ?>_btn" class="checkboxpreset_btn" style="cursor: pointer;" title="<?php printMLText("takeOverAttributeValue"); ?>" data-ref="<?php echo $name; ?>" data-text="<?php echo is_array($text) ? implode($sep, $text) : htmlspecialchars($text);?>"<?php if($sep) echo "data-sep=\"".$sep."\""; ?>><i class="icon-arrow-left"></i></span>
<?php
	} /* }}} */

	/**
	 * Javascript code for checkboxt preset button
	 * This code workѕ for checkboxes
	 */
	function printCheckboxPresetButtonJs() { /* {{{ */
?>
$(document).ready( function() {
	$('.checkboxpreset_btn').click(function(ev){
		ev.preventDefault();
		if (typeof $(ev.currentTarget).data('text') != 'undefined') {
			target = $(ev.currentTarget).data('ref');
			value = $(ev.currentTarget).data('text');
			if(value) {
				$("#"+target).attr('checked', '');
			} else {
				$("#"+target).removeAttribute('checked');
			}
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Return HTML of a single row in the document list table
	 *
	 * @param object $document
	 * @param object $previewer
	 * @param boolean $skipcont set to true if embrasing tr shall be skipped
	 */
	function documentListRow($document, $previewer, $skipcont=false, $version=0) { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$showtree = $this->params['showtree'];
		$workflowmode = $this->params['workflowmode'];
		$previewwidth = $this->params['previewWidthList'];
		$enableClipboard = $this->params['enableclipboard'];

		$content = '';

		$owner = $document->getOwner();
		$comment = $document->getComment();
		if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";
		$docID = $document->getID();

		if(!$skipcont)
			$content .= "<tr id=\"table-row-document-".$docID."\" class=\"table-row-document\" rel=\"document_".$docID."\" formtoken=\"".createFormKey('movedocument')."\" draggable=\"true\">";

		if($version)
			$latestContent = $document->getContentByVersion($version);
		else
			$latestContent = $document->getLatestContent();

		if($latestContent) {
			$previewer->createPreview($latestContent);
			$version = $latestContent->getVersion();
			$status = $latestContent->getStatus();
			$needwkflaction = false;
			if($workflowmode == 'advanced') {
				$workflow = $latestContent->getWorkflow();
				if($workflow) {
					$needwkflaction = $latestContent->needsWorkflowAction($user);
				}
			}
			
			/* Retrieve attacheѕ files */
			$files = $document->getDocumentFiles($latestContent->getVersion());
			$files = SeedDMS_Core_DMS::filterDocumentFiles($user, $files);

			/* Retrieve linked documents */
			$links = $document->getDocumentLinks();
			$links = SeedDMS_Core_DMS::filterDocumentLinks($user, $links);

			$content .= "<td>";
			if (file_exists($dms->contentDir . $latestContent->getPath())) {
				$content .= "<a draggable=\"false\" href=\"../op/op.Download.php?documentid=".$docID."&version=".$version."\">";
				if($previewer->hasPreview($latestContent)) {
					$content .= "<img draggable=\"false\" class=\"mimeicon\" width=\"".$previewwidth."\" src=\"../op/op.Preview.php?documentid=".$document->getID()."&version=".$latestContent->getVersion()."&width=".$previewwidth."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
				} else {
					$content .= "<img draggable=\"false\" class=\"mimeicon\" width=\"".$previewwidth."\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" ".($previewwidth ? "width=\"".$previewwidth."\"" : "")."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
				}
				$content .= "</a>";
			} else
				$content .= "<img draggable=\"false\" class=\"mimeicon\" width=\"".$previewwidth."\" src=\"".$this->getMimeIcon($latestContent->getFileType())."\" title=\"".htmlspecialchars($latestContent->getMimeType())."\">";
			$content .= "</td>";

			$content .= "<td>";	
			$content .= "<a draggable=\"false\" href=\"../out/out.ViewDocument.php?documentid=".$docID."&showtree=".$showtree."\">" . htmlspecialchars($document->getName()) . "</a>";
			$content .= "<br /><span style=\"font-size: 85%; font-style: italic; color: #666; \">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $document->getDate())."</b>, ".getMLText('version')." <b>".$version."</b> - <b>".date('Y-m-d', $latestContent->getDate())."</b>".($document->expires() ? ", ".getMLText('expires').": <b>".getReadableDate($document->getExpires())."</b>" : "")."</span>";
			if($comment) {
				$content .= "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
			}
			$content .= "</td>\n";

			$content .= "<td nowrap>";
			$attentionstr = '';
			if ( $document->isLocked() ) {
				$attentionstr .= "<img src=\"".$this->getImgPath("lock.png")."\" title=\"". getMLText("locked_by").": ".htmlspecialchars($document->getLockingUser()->getFullName())."\"> ";
			}
			if ( $needwkflaction ) {
				$attentionstr .= "<img src=\"".$this->getImgPath("attention.gif")."\" title=\"". getMLText("workflow").": ".htmlspecialchars($workflow->getName())."\"> ";
			}
			if($attentionstr)
				$content .= $attentionstr."<br />";
			$content .= "<small>";
			if(count($files))
				$content .= count($files)." ".getMLText("linked_files")."<br />";
			if(count($links))
				$content .= count($links)." ".getMLText("linked_documents")."<br />";
			if($status["status"] == S_IN_WORKFLOW && $workflowmode == 'advanced') {
				$workflowstate = $latestContent->getWorkflowState();
				$content .= '<span title="'.getOverallStatusText($status["status"]).': '.$workflow->getName().'">'.$workflowstate->getName().'</span>';
			} else {
				$content .= getOverallStatusText($status["status"]);
			}
			$content .= "</small></td>";
//				$content .= "<td>".$version."</td>";
			$content .= "<td>";
			$content .= "<div class=\"list-action\">";
			if($document->getAccessMode($user) >= M_ALL) {
				$content .= $this->printDeleteDocumentButton($document, 'splash_rm_document', true);
			} else {
				$content .= '<span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>';
			}
			if($document->getAccessMode($user) >= M_READWRITE) {
				$content .= '<a href="../out/out.EditDocument.php?documentid='.$docID.'" title="'.getMLText("edit_document_props").'"><i class="icon-edit"></i></a>';
			} else {
				$content .= '<span style="padding: 2px; color: #CCC;"><i class="icon-edit"></i></span>';
			}
			if($document->getAccessMode($user) >= M_READWRITE) {
				$content .= $this->printLockButton($document, 'splash_document_locked', 'splash_document_unlocked', true);
			}
			if($enableClipboard) {
				$content .= '<a class="addtoclipboard" rel="D'.$docID.'" msg="'.getMLText('splash_added_to_clipboard').'" title="'.getMLText("add_to_clipboard").'"><i class="icon-copy"></i></a>';
			}
			$content .= "</div>";
			$content .= "</td>";
		}
		if(!$skipcont)
			$content .= "</tr>\n";
		return $content;
	} /* }}} */

	function folderListRow($subFolder) { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
//		$folder = $this->params['folder'];
		$showtree = $this->params['showtree'];
		$enableRecursiveCount = $this->params['enableRecursiveCount'];
		$maxRecursiveCount = $this->params['maxRecursiveCount'];
		$enableClipboard = $this->params['enableclipboard'];

		$owner = $subFolder->getOwner();
		$comment = $subFolder->getComment();
		if (strlen($comment) > 150) $comment = substr($comment, 0, 147) . "...";

		$content = '';
		$content .= "<tr id=\"table-row-folder-".$subFolder->getID()."\" draggable=\"true\" rel=\"folder_".$subFolder->getID()."\" class=\"folder table-row-folder\" formtoken=\"".createFormKey('movefolder')."\">";
	//	$content .= "<td><img src=\"images/folder_closed.gif\" width=18 height=18 border=0></td>";
		$content .= "<td><a _rel=\"folder_".$subFolder->getID()."\" draggable=\"false\" href=\"../out/out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\"><img draggable=\"false\" src=\"".$this->imgpath."folder.svg\" width=\"24\" height=\"24\" border=0></a></td>\n";
		$content .= "<td><a draggable=\"false\" _rel=\"folder_".$subFolder->getID()."\" href=\"../out/out.ViewFolder.php?folderid=".$subFolder->getID()."&showtree=".$showtree."\">" . htmlspecialchars($subFolder->getName()) . "</a>";
		$content .= "<br /><span style=\"font-size: 85%; font-style: italic; color: #666;\">".getMLText('owner').": <b>".htmlspecialchars($owner->getFullName())."</b>, ".getMLText('creation_date').": <b>".date('Y-m-d', $subFolder->getDate())."</b></span>";
		if($comment) {
			$content .= "<br /><span style=\"font-size: 85%;\">".htmlspecialchars($comment)."</span>";
		}
		$content .= "</td>\n";
//		$content .= "<td>".htmlspecialchars($owner->getFullName())."</td>";
		$content .= "<td colspan=\"1\" nowrap><small>";
		if($enableRecursiveCount) {
			if($user->isAdmin()) {
				/* No need to check for access rights in countChildren() for
				 * admin. So pass 0 as the limit.
				 */
				$cc = $subFolder->countChildren($user, 0);
				$content .= $cc['folder_count']." ".getMLText("folders")."<br />".$cc['document_count']." ".getMLText("documents");
			} else {
				$cc = $subFolder->countChildren($user, $maxRecursiveCount);
				if($maxRecursiveCount > 5000)
					$rr = 100.0;
				else
					$rr = 10.0;
				$content .= (!$cc['folder_precise'] ? '~'.(round($cc['folder_count']/$rr)*$rr) : $cc['folder_count'])." ".getMLText("folders")."<br />".(!$cc['document_precise'] ? '~'.(round($cc['document_count']/$rr)*$rr) : $cc['document_count'])." ".getMLText("documents");
			}
		} else {
			/* FIXME: the following is very inefficient for just getting the number of
			 * subfolders and documents. Making it more efficient is difficult, because
			 * the access rights need to be checked.
			 */
			$subsub = $subFolder->getSubFolders();
			$subsub = SeedDMS_Core_DMS::filterAccess($subsub, $user, M_READ);
			$subdoc = $subFolder->getDocuments();
			$subdoc = SeedDMS_Core_DMS::filterAccess($subdoc, $user, M_READ);
			$content .= count($subsub)." ".getMLText("folders")."<br />".count($subdoc)." ".getMLText("documents");
		}
		$content .= "</small></td>";
//		$content .= "<td></td>";
		$content .= "<td>";
		$content .= "<div class=\"list-action\">";
		if($subFolder->getAccessMode($user) >= M_ALL) {
			$content .= $this->printDeleteFolderButton($subFolder, 'splash_rm_folder', true);
		} else {
			$content .= '<span style="padding: 2px; color: #CCC;"><i class="icon-remove"></i></span>';
		}
		if($subFolder->getAccessMode($user) >= M_READWRITE) {
			$content .= '<a class_="btn btn-mini" href="../out/out.EditFolder.php?folderid='.$subFolder->getID().'" title="'.getMLText("edit_folder_props").'"><i class="icon-edit"></i></a>';
		} else {
			$content .= '<span style="padding: 2px; color: #CCC;"><i class="icon-edit"></i></span>';
		}
		if($enableClipboard) {
			$content .= '<a class="addtoclipboard" rel="F'.$subFolder->getID().'" msg="'.getMLText('splash_added_to_clipboard').'" title="'.getMLText("add_to_clipboard").'"><i class="icon-copy"></i></a>';
		}
		$content .= "</div>";
		$content .= "</td>";
		$content .= "</tr>\n";
		return $content;
	} /* }}} */

	/**
	 * Output HTML Code for jumploader
	 *
	 * @param string $uploadurl URL where post data is send
	 * @param integer $folderid id of folder where document is saved
	 * @param integer $maxfiles maximum number of files allowed to upload
	 * @param array $fields list of post fields
	 */
	function printUploadApplet($uploadurl, $attributes, $maxfiles=0, $fields=array()){ /* {{{ */
?>
<applet id="jumpLoaderApplet" name="jumpLoaderApplet"
code="jmaster.jumploader.app.JumpLoaderApplet.class"
archive="jl_core_z.jar"
width="715"
height="400"
mayscript>
  <param name="uc_uploadUrl" value="<?php echo $uploadurl; ?>"/>
  <param name="ac_fireAppletInitialized" value="true"/>
  <param name="ac_fireUploaderSelectionChanged" value="true"/>
  <param name="ac_fireUploaderFileStatusChanged" value="true"/>
  <param name="ac_fireUploaderFileAdded" value="true"/>
  <param name="uc_partitionLength" value="<?php echo $this->params['partitionsize'] ?>"/>
<?php
	if($maxfiles) {
?>
  <param name="uc_maxFiles" value="<?php echo $maxfiles ?>"/>
<?php
	}
?>
</applet>
<div id="fileLinks">
</div>

<!-- callback methods -->
<script language="javascript">
    /**
     * applet initialized notification
     */
    function appletInitialized(applet) {
        var uploader = applet.getUploader();
        var attrSet = uploader.getAttributeSet();
        var attr;
<?php
	foreach($attributes as $name=>$value) {
?>
        attr = attrSet.createStringAttribute( '<?php echo $name ?>', '<?php echo $value ?>' );
        attr.setSendToServer(true);
<?php
	}
?>
    }
    /**
     * uploader selection changed notification
     */
    function uploaderSelectionChanged( uploader ) {
        dumpAllFileAttributes();
    }
    /**
     * uploader file added notification
     */
    function uploaderFileAdded( uploader ) {
        dumpAllFileAttributes();
    }
    /**
     * file status changed notification
     */
    function uploaderFileStatusChanged( uploader, file ) {
        traceEvent( "uploaderFileStatusChanged, index=" + file.getIndex() + ", status=" + file.getStatus() + ", content=" + file.getResponseContent() );
        if( file.isFinished() ) { 
            var serverFileName = file.getId() + "." + file.getName(); 
            var linkHtml = "<a href='/uploaded/" + serverFileName + "'>" + serverFileName + "</a> " + file.getLength() + " bytes"; 
            var container = document.getElementById( "fileLinks"); 
            container.innerHTML += linkHtml + "<br />"; 
        } 
    }
    /**
     * trace event to events textarea
     */
    function traceEvent( message ) {
        document.debugForm.txtEvents.value += message + "\r\n";
    }
</script>

<!-- debug auxiliary methods -->
<script language="javascript">
    /**
     * list attributes of file into html
     */
    function listFileAttributes( file, edit, index ) {
        var attrSet = file.getAttributeSet();
        var content = "";
        var attr;
				var value;
				if(edit)
					content += "<form name='form" + index + "' id='form" + index + "' action='#' >";
        content += "<table>";
				content += "<tr class='dataRow' colspan='2'><td class='dataText'><b>" + file.getName() + "</b></td></tr>";

<?php
	if(!$fields || (isset($fields['name']) && $fields['name'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('name') ?></td>";
				if(attr = attrSet.getAttributeByName('name'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<input id='name" + index + "' name='name' type='input' value='" + value + "' />";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['comment']) && $fields['comment'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('comment') ?></td>";
				if(attr = attrSet.getAttributeByName('comment'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<textarea id='comment" + index + "' name='comment' cols='40' rows='2'>" + value + "</textarea>";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['reqversion']) && $fields['reqversion'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('version') ?></td>";
				if(attr = attrSet.getAttributeByName('reqversion'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<input id='reqversion" + index + "' name='reqversion' type='input' value='" + value + "' />";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['version_comment']) && $fields['version_comment'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('comment_for_current_version') ?></td>";
				if(attr = attrSet.getAttributeByName('version_comment'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit)
					value = "<textarea id='version_comment" + index + "' name='version_comment' cols='40' rows='2'>" + value + "</textarea>";
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['keywords']) && $fields['keywords'])) {
?>
        content += "<tr class='dataRow'>";
        content += "<td class='dataField'><?php echo getMLText('keywords') ?></td>";
				if(attr = attrSet.getAttributeByName('keywords'))
					value = attr.getStringValue();
				else
					value = '';
				if(edit) {
					value = "<textarea id='keywords" + index + "' name='keywords' cols='40' rows='2'>" + value + "</textarea>";
					value += "<br /><a href='javascript:chooseKeywords(\"form" + index + ".keywords" + index +"\");'><?php echo getMLText("use_default_keywords");?></a>";
				}
        content += "<td class='dataText'>" + value + "</td>";
        content += "</tr>";
<?php
	}
?>

<?php
	if(!$fields || (isset($fields['categories']) && $fields['categories'])) {
?>
				content += "<tr class='dataRow'>";
				content += "<td class='dataField'><?php echo getMLText('categories') ?></td>";
				if(attr = attrSet.getAttributeByName('categoryids'))
					value = attr.getStringValue();
				else
					value = '';
				if(attr = attrSet.getAttributeByName('categorynames'))
					value2 = attr.getStringValue();
				else
					value2 = '';
				if(edit) {
					value = "<input type='hidden' id='categoryidform" + index + "' name='categoryids' value='" + value + "' />";
					value += "<input disabled id='categorynameform" + index + "' name='categorynames' value='" + value2 + "' />";
					value += "<br /><a href='javascript:chooseCategory(\"form" + index + "\", \"\");'><?php echo getMLText("use_default_categories");?></a>";
				} else {
					value = value2;
				}
        content += "<td class='dataText'>" + value + "</td>";
				content += "</tr>";
<?php
	}
?>

				if(edit) {
					content += "<tr class='dataRow'>";
					content += "<td class='dataField'></td>";
					content += "<td class='dataText'><input type='button' value='Set' onClick='updateFileAttributes("+index+")'/></td>";
					content += "</tr>";
        	content += "</table>";
        	content += "</form>";
				} else {
        	content += "</table>";
				}
        return content;
    }
    /**
     * return selected file if and only if single file selected
     */
    function getSelectedFile() {
        var file = null;
        var uploader = document.jumpLoaderApplet.getUploader();
        var selection = uploader.getSelection();
        var numSelected = selection.getSelectedItemCount();
        if( numSelected == 1 ) {
            var selectedIndex = selection.getSelectedItemIndexAt( 0 );
            file = uploader.getFile( selectedIndex );
        }
        return file;
    }
    /**
     * dump attributes of all files into html
     */
     function dumpAllFileAttributes() {
         var content = "";
         var uploader = document.jumpLoaderApplet.getUploader();
         var files = uploader.getAllFiles();
         var file = getSelectedFile();
				 if(file) {
					 for (var i = 0; i < uploader.getFileCount() ; i++) { 
						 if(uploader.getFile(i).getIndex() == file.getIndex())
							 content += listFileAttributes( uploader.getFile(i), 1, i );
						 else
							 content += listFileAttributes( uploader.getFile(i), 0, i );
					 }
					 document.getElementById( "fileList" ).innerHTML = content;
				 }
    }
     /**
      * update attributes for the selected file
      */
      function updateFileAttributes(index) {
        var uploader = document.jumpLoaderApplet.getUploader();
        var file = uploader.getFile( index );
        if( file != null ) {
				  var attr;
					var value;
          var attrSet = file.getAttributeSet();
					value = document.getElementById("name"+index);
          attr = attrSet.createStringAttribute( 'name', (value.value) ? value.value : "" );
          attr.setSendToServer( true );
					value = document.getElementById("comment"+index);
          attr = attrSet.createStringAttribute( 'comment', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("reqversion"+index);
          attr = attrSet.createStringAttribute( 'reqversion', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("version_comment"+index);
          attr = attrSet.createStringAttribute( 'version_comment', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );
					value = document.getElementById("keywords"+index);
          attr = attrSet.createStringAttribute( 'keywords', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					value = document.getElementById("categoryidform"+index);
          attr = attrSet.createStringAttribute( 'categoryids', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					value = document.getElementById("categorynameform"+index);
          attr = attrSet.createStringAttribute( 'categorynames', (value.value) ? value.value : ""  );
          attr.setSendToServer( true );

					dumpAllFileAttributes();
        } else {
            alert( "Single file should be selected" );
        }
     }
</script>
<form name="debugForm">
<textarea name="txtEvents" style="visibility: hidden;width:715px; font:10px monospace" rows="1" wrap="off" id="txtEvents"></textarea></p>
</form>
<p></p>
<p id="fileList"></p>
<?php
	} /* }}} */

	function show(){ /* {{{ */
		parent::show();
	} /* }}} */

	/**
	 * Return HTML Template for jumploader
	 *
	 * @param string $uploadurl URL where post data is send
	 * @param integer $folderid id of folder where document is saved
	 * @param integer $maxfiles maximum number of files allowed to upload
	 * @param array $fields list of post fields
	 */
	function getFineUploaderTemplate() { /* {{{ */
		return '
<script type="text/template" id="qq-template">
<div class="qq-uploader-selector qq-uploader" qq-drop-area-text="'.getMLText('drop_files_here').'">
	<div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
		<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
		</div>
	<div class="input-append">
	<div class="qq-upload-drop-area-selector qq-upload-drop-area" _qq-hide-dropzone>
		<span class="qq-upload-drop-area-text-selector"></span>
	</div>
	<span class="btn qq-upload-button-selector qq-upload-button">'.getMLText('browse').'&hellip;</span>
	</div>
	<span class="qq-drop-processing-selector qq-drop-processing">
		<span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
	</span>
	<ul class="qq-upload-list-selector qq-upload-list unstyled" aria-live="polite" aria-relevant="additions removals">
		<li>
			<div class="progress qq-progress-bar-container-selector">
				<div class="bar qq-progress-bar-selector qq-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
			<span class="qq-upload-spinner-selector qq-upload-spinner"></span>
			<img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
			<span class="qq-upload-file-selector qq-upload-file"></span>
			<span class="qq-upload-size-selector qq-upload-size"></span>
			<button class="btn btn-mini qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
			<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
		</li>
	</ul>
	<dialog class="qq-alert-dialog-selector">
		<div class="qq-dialog-message-selector"></div>
		<div class="qq-dialog-buttons">
			<button class="btn qq-cancel-button-selector">Cancel</button>
		</div>
	</dialog>

	<dialog class="qq-confirm-dialog-selector">
		<div class="qq-dialog-message-selector"></div>
		<div class="qq-dialog-buttons">
			<button class="btn qq-cancel-button-selector">Cancel</button>
			<button class="btn qq-ok-button-selector">Ok</button>
		</div>
	</dialog>

	<dialog class="qq-prompt-dialog-selector">
		<div class="qq-dialog-message-selector"></div>
		<input type="text">
		<div class="qq-dialog-buttons">
			<button class="btn qq-cancel-button-selector">Cancel</button>
			<button class="btn qq-ok-button-selector">Ok</button>
		</div>
	</dialog>
</div>
</script>
';
	} /* }}} */

	/**
	 * Output HTML Code for jumploader
	 *
	 * @param string $uploadurl URL where post data is send
	 * @param integer $folderid id of folder where document is saved
	 * @param integer $maxfiles maximum number of files allowed to upload
	 * @param array $fields list of post fields
	 */
	function printFineUploaderHtml($prefix='userfile') { /* {{{ */
?>
		<div id="<?php echo $prefix; ?>-fine-uploader"></div>
		<input type="hidden" <?php echo ($prefix=='userfile' ? 'class="do_validate"' : ''); ?> id="<?php echo $prefix; ?>-fine-uploader-uuids" name="<?php echo $prefix; ?>-fine-uploader-uuids" value="" />
		<input type="hidden" id="<?php echo $prefix; ?>-fine-uploader-names" name="<?php echo $prefix; ?>-fine-uploader-names" value="" />
<?php
	} /* }}} */

	/**
	 * Output Javascript Code for fine uploader
	 *
	 * @param string $uploadurl URL where post data is send
	 * @param integer $folderid id of folder where document is saved
	 * @param integer $maxfiles maximum number of files allowed to upload
	 * @param array $fields list of post fields
	 */
	function printFineUploaderJs($uploadurl, $partsize=0, $maxuploadsize=0, $multiple=true, $prefix='userfile') { /* {{{ */
?>
$(document).ready(function() {
	<?php echo $prefix; ?>uploader = new qq.FineUploader({
		debug: false,
		autoUpload: false,
		multiple: <?php echo ($multiple ? 'true' : 'false'); ?>,
		element: $('#<?php echo $prefix; ?>-fine-uploader')[0],
		template: 'qq-template',
		request: {
			endpoint: '<?php echo $uploadurl; ?>'
		},
<?php echo ($maxuploadsize > 0 ? '
		validation: {
			sizeLimit: '.$maxuploadsize.'
		},
' : ''); ?>
		chunking: {
			enabled: true,
			<?php echo $partsize ? 'partSize: '.(int)$partsize.",\n" : ''; ?>
			mandatory: true
		},
		messages: {
			sizeError: '{file} is too large, maximum file size is {sizeLimit}.'
		},
		callbacks: {
			onComplete: function(id, name, json, xhr) {
			},
			onAllComplete: function(succeeded, failed) {
				var uuids = Array();
				var names = Array();
				for (var i = 0; i < succeeded.length; i++) {
					uuids.push(this.getUuid(succeeded[i]))
					names.push(this.getName(succeeded[i]))
				}
				$('#<?php echo $prefix; ?>-fine-uploader-uuids').val(uuids.join(';'));
				$('#<?php echo $prefix; ?>-fine-uploader-names').val(names.join(';'));
				/* Run upload only if all files could be uploaded */
				if(succeeded.length > 0 && failed.length == 0)
					document.getElementById('form1').submit();
			},
			onError: function(id, name, reason, xhr) {
				noty({
					text: reason,
					type: 'error',
					dismissQueue: true,
					layout: 'topRight',
					theme: 'defaultTheme',
					timeout: 3500,
				});
			}
		}
	});
});
<?php
	} /* }}} */

	/**
	 * Output a protocol
	 *
	 * @param object $attribute attribute
	 */
	protected function printProtocol($latestContent, $type="") { /* {{{ */
		$dms = $this->params['dms'];
		$document = $latestContent->getDocument();
?>
		<legend><?php printMLText($type.'_log'); ?></legend>
		<table class="table condensed">
			<tr><th><?php printMLText('name'); ?></th><th><?php printMLText('last_update'); ?>, <?php printMLText('comment'); ?></th><th><?php printMLText('status'); ?></th></tr>
<?php
		switch($type) {
		case "review":
			$statusList = $latestContent->getReviewStatus(10);
			break;
		case "approval":
			$statusList = $latestContent->getApprovalStatus(10);
			break;
		default:
			$statusList = array();
		}
		foreach($statusList as $rec) {
			echo "<tr>";
			echo "<td>";
			switch ($rec["type"]) {
				case 0: // individual.
					$required = $dms->getUser($rec["required"]);
					if (!is_object($required)) {
						$reqName = getMLText("unknown_user")." '".$rec["required"]."'";
					} else {
						$reqName = htmlspecialchars($required->getFullName()." (".$required->getLogin().")");
					}
					break;
				case 1: // Approver is a group.
					$required = $dms->getGroup($rec["required"]);
					if (!is_object($required)) {
						$reqName = getMLText("unknown_group")." '".$rec["required"]."'";
					}
					else {
						$reqName = "<i>".htmlspecialchars($required->getName())."</i>";
					}
					break;
			}
			echo $reqName;
			echo "</td>";
			echo "<td>";
			echo "<i style=\"font-size: 80%;\">".$rec['date']." - ";
			$updateuser = $dms->getUser($rec["userID"]);
			if(!is_object($required))
				echo getMLText("unknown_user");
			else
				echo htmlspecialchars($updateuser->getFullName()." (".$updateuser->getLogin().")");
			echo "</i>";
			if($rec['comment'])
				echo "<br />".htmlspecialchars($rec['comment']);
			switch($type) {
			case "review":
				if($rec['file']) {
					echo "<br />";
					echo "<a href=\"../op/op.Download.php?documentid=".$document->getID()."&reviewlogid=".$rec['reviewLogID']."\" class=\"btn btn-mini\"><i class=\"icon-download\"></i> ".getMLText('download')."</a>";
				}
				break;
			case "approval":
				if($rec['file']) {
					echo "<br />";
					echo "<a href=\"../op/op.Download.php?documentid=".$document->getID()."&approvelogid=".$rec['approveLogID']."\" class=\"btn btn-mini\"><i class=\"icon-download\"></i> ".getMLText('download')."</a>";
				}
				break;
			}
			echo "</td>";
			echo "<td>";
			switch($type) {
			case "review":
				echo getReviewStatusText($rec["status"]);
				break;
			case "approval":
				echo getApprovalStatusText($rec["status"]);
				break;
			default:
			}
			echo "</td>";
			echo "</tr>";
		}
?>
				</table>
<?php
	} /* }}} */

	/**
	 * Show progressbar
	 *
	 * @param double $value value
	 * @param double $max 100% value
	 */
	protected function getProgressBar($value, $max=100.0) { /* {{{ */
		if($max > $value) {
			$used = (int) ($value/$max*100.0+0.5);
			$free = 100-$used;
		} else {
			$free = 0;
			$used = 100;
		}
		$html = '
		<div class="progress">
			<div class="bar bar-danger" style="width: '.$used.'%;"></div>
		  <div class="bar bar-success" style="width: '.$free.'%;"></div>
		</div>';
		return $html;
	} /* }}} */

	/**
	 * Output a timeline for a document
	 *
	 * @param object $document document
	 */
	protected function printTimelineJs($timelineurl, $height=300, $start='', $end='', $skip=array()) { /* {{{ */
		if(!$timelineurl)
			return;
?>
		var timeline;
		var data;

		// specify options
		var options = {
			'width':  '100%',
			'height': '100%',
<?php
		if($start) {
			$tmp = explode('-', $start);
			echo "\t\t\t'min': new Date(".$tmp[0].", ".($tmp[1]-1).", ".$tmp[2]."),\n";
		}
		if($end) {
			$tmp = explode('-', $end);
			echo "'\t\t\tmax': new Date(".$tmp[0].", ".($tmp[1]-1).", ".$tmp[2]."),\n";
		}
?>
			'editable': false,
			'selectable': true,
			'style': 'box',
			'locale': '<?php echo $this->params['session']->getLanguage() ?>'
		};

		function onselect() {
			var sel = timeline.getSelection();
			if (sel.length) {
				if (sel[0].row != undefined) {
					var row = sel[0].row;
					console.log(timeline.getItem(sel[0].row));
					item = timeline.getItem(sel[0].row);
					$('div.ajax').trigger('update', {documentid: item.docid, version: item.version, statusid: item.statusid, statuslogid: item.statuslogid, fileid: item.fileid});
				}
			}
		}
		$(document).ready(function () {
		// Instantiate our timeline object.
		timeline = new links.Timeline(document.getElementById('timeline'), options);
		links.events.addListener(timeline, 'select', onselect);
		$.getJSON(
			'<?php echo $timelineurl ?>', 
			function(data) {
				$.each( data, function( key, val ) {
					val.start = new Date(val.start);
				});
				timeline.draw(data);
			}
		);
		});
<?php
	} /* }}} */

	protected function printTimelineHtml($height) { /* {{{ */
?>
	<div id="timeline" style="height: <?php echo $height ?>px;"></div>
<?php
	} /* }}} */

	protected function printTimeline($timelineurl, $height=300, $start='', $end='', $skip=array()) { /* {{{ */
		echo "<script type=\"text/javascript\">\n";
		$this->printTimelineJs($timelineurl, $height, $start, $end, $skip);
		echo "</script>";
		$this->printTimelineHtml($height);
	} /* }}} */

	protected function printPopupBox($title, $content, $ret=false) { /* {{{ */
		$id = md5(uniqid());
		/*
		$this->addFooterJS('
$("body").on("click", "span.openpopupbox", function(e) {
	$(""+$(e.target).data("href")).toggle();
//	$("div.popupbox").toggle();
});
');
		 */
		$html = '
		<span class="openpopupbox" data-href="#'.$id.'">'.$title.'</span>
		<div id="'.$id.'" class="popupbox" style="display: none;">
		'.$content.'
			<span class="closepopupbox"><i class="icon-remove"></i></span>
		</div>';
		if($ret)
			return $html;
		else
			echo $html;
	} /* }}} */

	protected function printAccordion($title, $content) { /* {{{ */
		$id = substr(md5(uniqid()), 0, 4);
?>
		<div class="accordion" id="accordion<?php echo $id; ?>">
      <div class="accordion-group">
        <div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion<?php echo $id; ?>" href="#collapse<?php echo $id; ?>">
						<?php echo $title; ?>
          </a>
        </div>
				<div id="collapse<?php echo $id; ?>" class="accordion-body collapse" style="height: 0px;">
          <div class="accordion-inner">
<?php
		echo $content;
?>
          </div>
        </div>
      </div>
    </div>
<?php
	} /* }}} */
}
?>

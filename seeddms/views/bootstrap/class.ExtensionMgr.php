<?php
/**
 * Implementation of ExtensionMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2013 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Class which outputs the html page for ExtensionMgr view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2013 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_ExtensionMgr extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		$httproot = $this->params['httproot'];
		$version = $this->params['version'];

		$this->htmlStartPage(getMLText("admin_tools"));
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation(getMLText("admin_tools"), "admin_tools");
		$this->contentContainerStart();
		echo "<table class=\"table table-condensed\">\n";
		print "<thead>\n<tr>\n";
		print "<th></th>\n";	
		print "<th>".getMLText('name')."</th>\n";	
		print "<th>".getMLText('version')."</th>\n";	
		print "<th>".getMLText('author')."</th>\n";	
		print "</tr></thead>\n";
		$errmsgs = array();
		foreach($GLOBALS['EXT_CONF'] as $extname=>$extconf) {
			$errmsgs = array();
			if(!isset($extconf['disable']) || $extconf['disable'] == false) {
				/* check dependency on specific seeddms version */
				if(!isset($extconf['constraints']['depends']['seeddms']))
					$errmsgs[] = "Missing dependency on SeedDMS";
				if(!isset($extconf['constraints']['depends']['php']))
					$errmsgs[] = "Missing dependency on PHP";

				if(isset($extconf['constraints']['depends'])) {
					foreach($extconf['constraints']['depends'] as $dkey=>$dval) {
						switch($dkey) {
						case 'seeddms':
							$tmp = explode('-', $dval, 2);
							if(cmpVersion($tmp[0], $version->version()) > 0 || ($tmp[1] && cmpVersion($tmp[1], $version->version()) < 0))
								$errmsgs[] = sprintf("Incorrect SeedDMS version (needs version %s)", $extconf['constraints']['depends']['seeddms']);
							break;
						case 'php':
							$tmp = explode('-', $dval, 2);
							if(cmpVersion($tmp[0], phpversion()) > 0 || ($tmp[1] && cmpVersion($tmp[1], phpversion()) < 0))
								$errmsgs[] = sprintf("Incorrect PHP version (needs version %s)", $extconf['constraints']['depends']['php']);
							break;
						default:
							$tmp = explode('-', $dval, 2);
							if(isset($GLOBALS['EXT_CONF'][$dkey]['version'])) {
								if(cmpVersion($tmp[0], $GLOBALS['EXT_CONF'][$dkey]['version']) > 0 || ($tmp[1] && cmpVersion($tmp[1], $GLOBALS['EXT_CONF'][$dkey]['version']) < 0))
									$errmsgs[] = sprintf("Incorrect version of extension '%s' (needs version '%s' but provides '%s')", $dkey, $dval, $GLOBALS['EXT_CONF'][$dkey]['version']);
							} else {
								$errmsgs[] = sprintf("Missing extension or version for '%s'", $dkey);
							}
							break;
						}
					}
				}

				if($errmsgs)
					echo "<tr class=\"error\">";
				else
					echo "<tr class=\"success\">";
			} else
				echo "<tr class=\"warning\">";
			echo "<td>";
			if($extconf['icon'])
				echo "<img src=\"".$httproot."ext/".$extname."/".$extconf['icon']."\">";
			echo "</td>";
			echo "<td>".$extconf['title']."<br /><small>".$extconf['description']."</small>";
			if($errmsgs)
				echo "<div><img src=\"".$this->getImgPath("attention.gif")."\"> ".implode('<br /><img src="'.$this->getImgPath("attention.gif").'"> ', $errmsgs)."</div>";
			echo "</td>";
			echo "<td>".$extconf['version']."<br /><small>".$extconf['releasedate']."</small>";
			if($extconf['config'])
				echo "<div class=\"list-action\"><a href=\"../out/out.Settings.php?currenttab=extensions#".$extname."\"><i class=\"icon-cogs\"></i></a></div>";
			echo "</td>";
			echo "<td><a href=\"mailto:".$extconf['author']['email']."\">".$extconf['author']['name']."</a><br /><small>".$extconf['author']['company']."</small></td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
?>
<form action="../op/op.ExtensionMgr.php" name="form1" method="post">
  <?php echo createHiddenFieldWithKey('extensionmgr'); ?>
	<p><button type="submit" class="btn"><i class="icon-refresh"></i> <?php printMLText("refresh");?></button></p>
</form>
<?php
		$this->contentContainerEnd();
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}
?>

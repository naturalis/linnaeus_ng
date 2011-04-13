<?php /* Smarty version 2.6.26, created on 2011-04-12 11:53:13
         compiled from C:/Users/maarten/htdocs/tanbif/inc/inc.external_head.php */ ?>
<?php echo '<?php'; ?>


	require_once 'class.config.php';
	require_once 'class.html.php';
	require_once 'class.db.php';
	
	$cfg = new config();

	$db = new db(
		$cfg->db_host,
		$cfg->db_user,
		$cfg->db_pass
	);
	$db->select($cfg->db_database);

	$cfg->setConnection($db->conn);
	$cfg->setAvailableLanguages();
	$cfg->setLanguage();

	$html = new html(
		$cfg->languageID,
		$cfg->availableLanguages
		);
	$html->setRootPath($cfg->pathRoot);
	$html->setGetTextFunction(array($cfg,'getText'));
	$html->setImageRoot($newImageRoot ? $newImageRoot : '../images/');
	$html->setImageNames();
	$html->setActiveMenuItem($activeMenuItem ? $activeMenuItem : 5);

	echo $html->getBodyOpen();
	echo $html->getTopFrame();

<?php echo '?>'; ?>
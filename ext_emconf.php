<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "nr_cache".
 *
 * Auto generated 11-07-2014 15:17
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Netresearch locking module.',
	'description' => 'Provides an alternative locking using Redis.',
	'category' => 'fe',
	'author' => 'Sebastian Mendel',
	'author_company' => 'Netresearch GmbH & Co.KG',
	'author_email' => 'sebastian.mendel@netresearch.de',
	'shy' => '',
	'dependencies' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-5.99.99',
			'typo3' => '4.6.0-4.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.0.1',
	'_md5_values_when_last_written' => 'a:13:{s:9:"build.xml";s:4:"64f8";s:9:"ChangeLog";s:4:"297f";s:16:"ext_autoload.php";s:4:"933b";s:17:"ext_localconf.php";s:4:"5711";s:10:"README.rst";s:4:"9467";s:18:"doc/ide_helper.php";s:4:"02db";s:33:"src/Netresearch/Cache/Session.php";s:4:"9e3a";s:39:"src/Netresearch/Cache/StreamWrapper.php";s:4:"5c42";s:43:"src/Netresearch/Cache/Backend/Couchbase.php";s:4:"05e9";s:39:"src/Netresearch/Cache/Backend/Redis.php";s:4:"88c4";s:39:"src/Netresearch/Cache/Frontend/Code.php";s:4:"df38";s:49:"src/Netresearch/Cache/Frontend/FunctionResult.php";s:4:"f35b";s:41:"src/Netresearch/Cache/Frontend/String.php";s:4:"9643";}',
	'suggests' => array(
	),
);

?>

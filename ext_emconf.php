<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "nr_lock".
 *
 * Auto generated 25-08-2014 14:17
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Netresearch distributed lock manager (DLM).',
	'description' => 'Provides a distributed lock manager (DLM) with support for Redis and Couchbase.',
	'category' => 'fe',
	'author' => 'Sebastian Mendel',
	'author_company' => 'Netresearch GmbH & Co.KG',
	'author_email' => 'sebastian.mendel@netresearch.de',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-5.5.99',
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'state' => 'stable',
	'version' => '2.0.0',
	'_md5_values_when_last_written' => 'a:16:{s:9:"build.xml";s:4:"3f95";s:9:"ChangeLog";s:4:"bea1";s:16:"ext_autoload.php";s:4:"8322";s:12:"ext_icon.gif";s:4:"a459";s:17:"ext_localconf.php";s:4:"aa1d";s:10:"README.rst";s:4:"bf9c";s:18:"doc/ide_helper.php";s:4:"02db";s:12:"src/Lock.php";s:4:"0c7f";s:24:"src/Backend/Abstract.php";s:4:"a243";s:25:"src/Backend/Couchbase.php";s:4:"f6a0";s:21:"src/Backend/Redis.php";s:4:"1b76";s:19:"tests/bootstrap.php";s:4:"755b";s:18:"tests/LockTest.php";s:4:"5501";s:17:"tests/phpunit.xml";s:4:"c77f";s:30:"tests/Backend/AbstractTest.php";s:4:"26eb";s:27:"tests/Backend/RedisTest.php";s:4:"486d";}',
	'suggests' => array(
	),
);

?>
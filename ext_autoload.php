<?php
declare(encoding = 'UTF-8');

/**
 * NR Lock autoloader configuration.
 *
 * PHP version 5
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Configuration
 * @author     Andre Hähnel <andre.haehnel@netresearch.de>
 * @license    http://www.aida.de AIDA Copyright
 * @link       http://www.aida.de
 */

defined('TYPO3_MODE') or die('Access denied.');

$strPath = t3lib_extMgm::extPath('nr_lock');

return array(
    'netresearch\lock\backend_abstract'
        => $strPath . 'src/Backend/Abstract.php',
    'netresearch\lock\backend_couchbase'
        => $strPath . 'src/Backend/Couchbase.php',
    'netresearch\lock\backend_redis'
        => $strPath . 'src/Backend/Redis.php',
    'netresearch\lock\lock'
        => $strPath . 'src/Lock.php',
);
?>

<?php
/**
 * Extension config script
 *
 * PHP version 5
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Configuration
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 */

defined('TYPO3_MODE') or die('Access denied.');

$arLockCfg = $TYPO3_CONF_VARS['SYS']['locking'];

if (! empty($arLockCfg) && ! class_exists('ux_t3lib_lock', false)) {
    // we need to manually include these classes because autoloading does not work at this stage
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Backend/Abstract.php';
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Backend/Redis.php';
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Backend/Couchbase.php';
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Lock.php';
    // extend t3lib_lock
    class ux_t3lib_lock extends \Netresearch\Lock\Lock {}
}

?>

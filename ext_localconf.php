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

/** @var array[] $TYPO3_CONF_VARS */
global $TYPO3_CONF_VARS;

$arLockCfg = $TYPO3_CONF_VARS['SYS']['locking'];

if (! empty($arLockCfg) && ! class_exists('ux_t3lib_lock', false)) {
    // we need to manually include these classes because autoloading does not work at this stage
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Backend/Abstract.php';
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Backend/Redis.php';
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Backend/Couchbase.php';
    require_once t3lib_extMgm::extPath('nr_lock') . 'src/Lock.php';

    // for TYPO3 4.5 - 4.7
    // extend t3lib_lock (XCLASS)
    class ux_t3lib_lock extends \Netresearch\Lock\Lock {}


    // for TYPO3 6.2
    // extensions using old class names
    $TYPO3_CONF_VARS['SYS']['Objects']['t3lib_lock']
        ['className'] = 'Netresearch\Lock\Lock';

    // extensions using new class names
    $TYPO3_CONF_VARS['SYS']['Objects']['TYPO3\CMS\Core\Locking\Locker']
        ['className'] = 'Netresearch\Lock\Lock';
}

?>

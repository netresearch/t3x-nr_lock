<?php
declare(encoding = 'UTF-8');
/**
 * A caching backend which stores cache entries.
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Controller
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 */

namespace Netresearch\Lock;

/**
 * Class Netresearch_Lock_Backend_Abstract
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Backend
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 */
abstract class Backend_Abstract
{
    /**
     * Set the lock with the unique $strIdentifier and $strToken.
     *
     * @param string  $strIdentifier Lock unique identifier
     * @param string  $strToken      Token
     * @param integer $nTtl          Time to live
     *
     * @return boolean Success
     * @throws \Exception
     */
    public abstract function lock($strIdentifier, $strToken = null, $nTtl = null);



    /**
     * Drop/delete the lock identified by $strIdentifier und verified with $strToken.
     *
     * @param string $strIdentifier Lock unique identifier
     * @param string $strToken      Token
     *
     * @return boolean Success
     * @throws \Exception
     */
    public abstract function unlock($strIdentifier, $strToken = null);



    /**
     * Acquire a lock and return when successful. If the lock is already open,
     * the client will retry $nRetries times with a delay of $nSleep.
     *
     * It is important to know that the lock will be acquired in any case,
     * even if the request was blocked first. Therefore, the lock needs to be
     * released in every situation.
     *
     * Returns array with acquired lock data
     *
     * array(
     *     'strToken'  => [string]
     *     'nTtl'      => [integer]
     *     'nWaitTime' => [integer]
     * )
     *
     * Token:    $strToken or generated token
     * TTL:      Lock life time in seconds
     * WaitTime: Time till lock could be acquired - 0 means lock could be acquired immediately
     *
     * @param string  $strIdentifier Lock unique identifier
     * @param integer $nTtl          Time to live
     * @param string  $strToken      Token
     * @param integer $nRetries      Retry count for acquiring the lock
     * @param integer $nSleep        Sleep/wait time between retries
     *
     * @throws \Exception
     * @return array Acquired lock data
     */
    public function acquire(
        $strIdentifier, $nTtl = null, $strToken = null, $nRetries = 150, $nSleep = 100
    ) {
        $arResult = array();

        $strIdentifier = 'lock::' . $strIdentifier;

        if (null === $strToken) {
            $arResult['strToken'] = uniqid();
        } else {
            $arResult['strToken'] = $strToken;
        }
        $nRetryCountDown = $nRetries;
        if (null === $nTtl) {
            $arResult['nTtl'] = Lock::getLockTime();
        } else {
            $arResult['nTtl'] = $nTtl;
        }

        do {
            if ($this->lock($strIdentifier, $arResult['strToken'], $arResult['nTtl'])) {
                // returns false if we had to wait for existing lock to release
                // otherwise true if locking was immediately
                $arResult['nWaitTime'] = ($nRetries - $nRetryCountDown) * $nSleep;

                \t3lib_div::devLog(
                    'Acquired lock "' . $strIdentifier . '" after '
                    . $arResult['nWaitTime'] . ' ms',
                    'nr_lock',
                    \t3lib_div::SYSLOG_SEVERITY_INFO
                );

                return $arResult;
            }

            // Wait a random delay before to retry
            $nLockDelayTime = mt_rand(floor($nSleep / 2), $nSleep);
            usleep($nLockDelayTime * 1000);

            $nRetryCountDown--;

        } while ($nRetryCountDown > 0);

        \t3lib_div::devLog(
            'Could not acquire lock "' . $strIdentifier . '" after '
            . $nRetries . ' retries within ' . ($nSleep * $nRetries) . ' ms',
            'nr_lock',
            \t3lib_div::SYSLOG_SEVERITY_ERROR
        );

        throw new \Exception('Could not acquire lock');
    }



    /**
     * Release the lock identified by $strIdentifier und verified with $strToken. .
     *
     * @param string $strIdentifier Lock unique identifier
     * @param string $strToken      Lock token
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function release($strIdentifier, $strToken = null)
    {
        $strIdentifier = 'lock::' . $strIdentifier;

        if ($this->unlock($strIdentifier, $strToken)) {
            \t3lib_div::devLog(
                'Released lock "' . $strIdentifier . '"',
                'nr_lock',
                \t3lib_div::SYSLOG_SEVERITY_INFO
            );
            return true;
        } else {
            \t3lib_div::devLog(
                'Could not release lock "' . $strIdentifier . '"',
                'nr_lock',
                \t3lib_div::SYSLOG_SEVERITY_ERROR
            );
            return false;
        }
    }
}
?>

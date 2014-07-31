<?php
declare(encoding = 'UTF-8');
/**
 *
 * @category   Controller
 * @package    Netresearch
 * @subpackage Lock
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 * @api
 * @scope       prototype
 */

namespace Netresearch\Lock;

/**
 * Class Session.
 *
 * UX class for t3lib_lock to handle locking in memory based backend.
 *
 * @category   Controller
 * @package    Netresearch
 * @subpackage Lock
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 */
class Lock extends \t3lib_lock
{
    /**
     * @var Backend_Abstract Locking backend object
     */
    var $backend = null;

    /**
     * @var bool Flag indicating whether use TYPO3 original/classic locking
     */
    protected $bClassicLocking = true;

    /**
     * @var string Lock token
     */
    protected $strToken = null;



    /**
     * Constructor:
     * initializes locking, check input parameters and set variables accordingly.
     *
     * @param string  $id     ID to identify this lock in the system
     * @param string  $method Define which locking method to use. Defaults to "simple".
     * @param integer $loops  Number of times a locked resource is tried to be acquired. Only used in manual locks method "simple".
     * @param integer step    Milliseconds after lock acquire is retried. $loops * $step results in the maximum delay of a lock. Only used in manual lock method "simple".
     */
    public function __construct($id, $method = 'simple', $loops = 0, $step = 0)
    {
        $this->id = $id;

        if (intval($loops)) {
            $this->loops = intval($loops);
        }
        if (intval($step)) {
            $this->step = intval($step);
        }

        if ($this->initBackend()) {
            $this->bClassicLocking = false;
        } else {
            parent::__construct($id, $method, $loops, $step);
        }
    }



    /**
     * Initialize locking backend.
     *
     * @return bool success
     */
    protected function initBackend()
    {
        $arLockCfg = $GLOBALS['TYPO3_CONF_VARS']['SYS']['locking'];

        if (empty($arLockCfg['backend']) || ! class_exists($arLockCfg['backend'])) {
            \t3lib_div::devLog(
                'Locking backend class not found: "' . $arLockCfg['backend'] . '"',
                'nr_lock',
                \t3lib_div::SYSLOG_SEVERITY_ERROR
            );
            return false;
        }

        $this->backend = new $arLockCfg['backend']($arLockCfg['options']);

        return true;
    }



    /**
     * Acquire a lock and return when successful. If the lock is already open,
     * the client will retry {$this->loops} times with a {$this->step} delay.
     *
     * Returns true if the lock could be acquired immediately.
     * Returns false if the client had to wait for a former lock to be released.
     * Throws RuntimeException if no lock could be acquired.
     *
     * @return boolean TRUE if lock could be acquired without waiting, FALSE otherwise.
     * @throws \RuntimeException
     */
    public function acquire()
    {
        if ($this->bClassicLocking) {
            return parent::acquire();
        }

        try {
            $this->strToken = uniqid();
            $nTtl           = Lock::getLockTime();

            $arResult = $this->backend->acquire(
                $this->id, $nTtl, $this->strToken, $this->loops, $this->step
            );

            $this->isAcquired = true;

            if ($arResult['nWaitTime'] === 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Lock could not be created', 0, $e);
        }
    }



    /**
     * Release the lock.
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function release()
    {
        if ($this->bClassicLocking) {
            return parent::release();
        }

        if (!$this->isAcquired) {
            return true;
        }

        $this->isAcquired = false;
        return $this->backend->release($this->id, $this->strToken);
    }



    /**
     * Returns default locking time.
     *
     * @return int
     */
    public static function getLockTime()
    {
        $nTtl = intval(ini_get('max_execution_time'));
        if ($nTtl <= 0) {
            $nTtl = 30;
        }

        return $nTtl;
    }
}

?>

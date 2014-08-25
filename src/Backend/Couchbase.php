<?php
declare(encoding = 'UTF-8');
/**
 * A locking backend which stores locks by using Couchbase.
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
 * Class Netresearch_Lock_Backend_Couchbase
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Backend
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 */
class Backend_Couchbase extends Backend_Abstract
{
    const PREFIX = 'lock::';

    /**
     * @var \Couchbase
     */
    protected $couchbase = null;

    /**
     * @var string
     */
    protected $strCas = '';

    /**
     * Constructs this backend
     *
     * Options
     * <code>
     * array(
     *     // name       => default
     *     'hostname'    => 'localhost',
     *     'username'    => '',
     *     'password'    => '',
     *     'bucket'      => 'default',
     *     'persistent'  => true,
     *     'compression' => false,
     * )
     * </code>
     *
     * @param array $arOptions Couchbase connection options
     */
    public function __construct(array $arOptions)
    {
        $arOptionsDefault = array(
            'hostname'    => 'localhost',
            'username'    => '',
            'password'    => '',
            'bucket'      => 'default',
            'persistent'  => true,
            'compression' => false,
        );

        $arOptions = array_merge($arOptionsDefault, $arOptions);

        try {
            $this->couchbase = new \Couchbase(
                array($arOptions['hostname']),
                $arOptions['username'],
                $arOptions['password'],
                $arOptions['bucket'],
                $arOptions['persistent']
            );
        } catch (\Exception $e) {
            \t3lib_div::devLog(
                'Could not connect to couchbase: ' . $e->getMessage(),
                'nr_lock',
                \t3lib_div::SYSLOG_SEVERITY_ERROR
            );
            throw new \RuntimeException(
                'NR Lock could not connect to couchbase', 0, $e
            );
        }

        if ($arOptions['compression']) {
            $this->couchbase->setOption(
                COUCHBASE_OPT_COMPRESSION, COUCHBASE_COMPRESSION_FASTLZ
            );
        } else {
            $this->couchbase->setOption(
                COUCHBASE_OPT_COMPRESSION, COUCHBASE_COMPRESSION_NONE
            );
        }
    }



    /**
     * Get Lock.
     * Stores an exclusive key-value pair on couchbase instance.
     *
     * @param string  $strIdentifier Lock name/id - name/key of stored value
     * @param string  $strToken      Lock token - stored as value
     * @param integer $nTtl          Lock time to live in seconds
     *
     * @return boolean Success
     */
    public function lock($strIdentifier, $strToken = '', $nTtl = null)
    {
        if (empty($nTtl)) {
            $nTtl = Lock::getLockTime();
        }

        // set
        $strCas = $this->couchbase->set(
            self::PREFIX . $strIdentifier, $strToken, $nTtl
        );
        if (! $strCas) {
            return false;
        };

        // (get and) lock
        $result = $this->couchbase->getAndLock(
            self::PREFIX . $strIdentifier, $this->strCas, $nTtl
        );

        if ($result !== $strToken) {
            return false;
        }

        return true;
    }



    /**
     * Deletes lock from server.
     *
     * @param string $strIdentifier Lock name - key to access storage entry
     * @param string $strToken      Lock token - compared against stored value
     *
     * @return boolean Success
     */
    public function unlock($strIdentifier, $strToken = null)
    {
        $strCas = $this->couchbase->delete($strIdentifier, $this->strCas);

        return is_string($strCas);
    }
}
?>

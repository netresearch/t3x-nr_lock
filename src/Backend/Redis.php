<?php
declare(encoding = 'UTF-8');
/**
 * A locking backend which stores locks by using Redis.
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Controller
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 * @api
 */

namespace Netresearch\Lock;

/**
 * Class Netresearch_Lock_Backend_Redis
 *
 * @category   Netresearch
 * @package    Lock
 * @subpackage Backend
 * @author     Sebastian Mendel <sebastian.mendel@netresearch.de>
 * @license    AGPL http://www.netresearch.de/
 * @link       http://www.netresearch.de/
 */
class Backend_Redis extends Backend_Abstract
{
    /**
     * @var \Redis
     */
    protected $redis = null;



    /**
     * Constructor.
     * Init redis connection.
     *
     * Options
     * <code>
     * array(
     *     // name            => default
     *     'hostname'         => 'localhost',
     *     'port'             => 6379,
     *     'database'         => 0,
     *     'password'         => '',
     *     'compression'      => false,
     *     'compressionLevel' => 1,
     * )
     * </code>
     *
     * @param array $arOptions Redis connection options
     */
    public function __construct(array $arOptions)
    {
        $this->redis = new \Redis();

        $arOptionsDefault = array(
            'hostname'         => 'localhost',
            'port'             => 6379,
            'database'         => 0,
            'password'         => '',
            'compression'      => false,
            'compressionLevel' => 1,
        );

        $arOptions = array_merge($arOptionsDefault, $arOptions);

        try {
            $bConnect = $this->redis->connect($arOptions['hostname'], $arOptions['port']);
        } catch (\Exception $e) {
            $bConnect = false;
        }

        if (false === $bConnect) {
            throw new \RuntimeException(
                'Could not connect to redis server.',
                0,
                empty($e) ? null : $e
            );
        }

        if (strlen($arOptions['password'])) {
            $success = $this->redis->auth($arOptions['password']);
            if (!$success) {
                throw new \RuntimeException(
                    'The given password was not accepted by the redis server.'
                );
            }
        }

        if ($arOptions['database'] > 0) {
            $success = $this->redis->select($arOptions['database']);
            if (!$success) {
                throw new \RuntimeException(
                    'The given database "' . $arOptions['database'] . '" could not be selected.'
                );
            }
        }
    }



    /**
     * Get Lock.
     * Stores an exclusive key-value pair on redis instance.
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

        $strLockScript = <<<LUA
--
-- Set a lock
--
-- KEYS[1]  - key
-- KEYS[2]  - ttl in ms
-- KEYS[3]  - token
local key   = KEYS[1]
local ttl   = KEYS[2]
local token = KEYS[3]

local lockSet = redis.call('setnx', key, token)

if lockSet == 1 then
  redis.call('pexpire', key, ttl)
end

return lockSet
LUA;

        // FIXME: better use SCRIPT LOAD und EVALSHA ?
        //$sha = $this->redis->script('load', $strLockScript);
        //$this->redis->evalSha($sha);

        // FIXME: Redis => 1.6
        //$this->redis->set($strIdentifier, $strToken, ['NX', 'PX' => $nTtl]);
        // or ->setnx() and ->setex()

        return (bool) $this->redis->eval(
            $strLockScript,
            array(
                $strIdentifier,
                $nTtl * 1000,
                $strToken,
            ),
            3
        );
    }



    /**
     * Release a lock.
     * Removes key-value from redis server - if token fits
     *
     * @param string $strIdentifier Lock name - key to access storage entry
     * @param string $strToken      Lock token - compared against stored value
     *
     * @return boolean Success
     */
    public function unlock($strIdentifier, $strToken = null)
    {
        if (null === $strToken) {
            $strUnlockScript
                = <<<LUA
--
-- Release a lock
--
-- KEYS[1]   - key
local key   = KEYS[1]

return redis.call("DEL", key)
LUA;
        } else {
            $strUnlockScript
                = <<<LUA
--
-- Release a lock
--
-- KEYS[1]   - key
-- ARGV[1]   - token (lock content)
local key   = KEYS[1]
local token = ARGV[1]

if redis.call("GET", key) == token then
    return redis.call("DEL", key)
else
    return 0
end
LUA;
        }

        return (bool) $this->redis->eval(
            $strUnlockScript, array($strIdentifier, $strToken), 1
        );
    }
}
?>

Netresearch distributed lock manager
====================================

.. contents:: Contents


Overview
========

NR_Lock is a distributed lock manager (DLM).
This is designed for multiple TYPO3 frontend servers using a single typo3temp
share and Database.
NR_Lock currently supports single Redis or Couchbase (experimental) caching
server as locking instance.
It replaces/extends the TYPO3 lock facility t3lib_lock.


Requirements
============

- Redis: => 2.4 - 2.8
- Couchbase: => ???
- PHP => 5.3 - 5.5
- TYPO3 => 4.5 - 6.2
- PHP/redis => 1.6


Installation
============

Installation is done by TYPO3 extension manager.


Configuration
=============

To set up NR_Lock as a replacement for t3lib_lock you need to alter your
localconf.php in /typo3conf/ and add the following lines::

    $arLockCfg = &$TYPO3_CONF_VARS['SYS']['locking'];

    if (extension_loaded('redis')) {
        $arLockCfg = array(
            'backend' => '\Netresearch\Lock\Backend_Redis',
            'options' => array(
                //'hostname'         => 'localhost',
                //'port'             => 6379,
                //'database'         => 0,
                //'password'         => '',
                //'compression'      => false,
                //'compressionLevel' => 1,
            ),
        );
    } elseif (extension_loaded('couchbase')) {
        $arLockCfg = array(
            'backend' => '\Netresearch\Lock\Backend_Couchbase',
            'options' => array(
                'servers' => array(
                    'localhost',
                ),
            ),
        );
    }


ToDo
====

- Support multiple caching server.
- Test using LIST for locks with redis (performance).
- Use nr_lock in TYPO3 6.2 ClassLoader - this is currently hard coded in the TYPO3 core

References
==========

- http://www.couchbase.com/
- http://www.redis.io/
- http://redis.io/topics/distlock
- https://engineering.gosquared.com/distributed-locks-using-redis
- https://github.com/ronnylt/redlock-php

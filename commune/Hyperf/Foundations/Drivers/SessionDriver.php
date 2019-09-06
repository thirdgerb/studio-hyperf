<?php

/**
 * Class SessionDriver
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;

use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Memory\Memory;
use Commune\Chatbot\OOHost\History\Breakpoint;
use Commune\Chatbot\OOHost\History\Yielding;
use Commune\Chatbot\OOHost\Session\Driver as Contract;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\Snapshot;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Commune\Hyperf\Foundations\Database\TableSchema;
use Psr\Log\LoggerInterface;

class SessionDriver implements Contract
{
    use RunningSpyTrait;

    const SESSION_KEY = "commune:chatbot:session:%s";
    const SNAPSHOT_HASH_KEY = 'snapshot';


    /**
     * @var ClientDriver
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SessionDriver constructor.
     * @param ClientDriver $driver
     */
    public function __construct(ClientDriver $driver)
    {
        $this->driver = $driver;
        $this->logger = $driver->getLogger();
    }


    public function saveSnapshot(Snapshot $snapshot, int $expireSeconds = 0): void
    {
        $belongsTo = $snapshot->belongsTo;
        $key = $this->sessionKey($belongsTo);
        $caching = serialize($snapshot);
//        $bool = $this->driver->getRedis()->set($key, $caching, $expireSeconds);

        $redis = $this->driver->getRedis();
        $bool = $redis->hSet($key, self::SNAPSHOT_HASH_KEY, $caching);

        if ( $bool) {

            $redis->expire($key, $expireSeconds);

        } else {
            $this->logger->error(__METHOD__ . ' failed', [
                'belongsTo' => $belongsTo,
                'sessionId' => $snapshot->sessionId,
            ]);
        }
    }
    
    protected function sessionKey(string $belongsTo) : string
    {
        return sprintf(self::SESSION_KEY, $belongsTo);
    }

    public function findSnapshot(string $belongsTo): ? Snapshot
    {
        $key = $this->sessionKey($belongsTo);
        $serialized = $this
            ->driver
            ->getRedis()
            ->hGet($key, self::SNAPSHOT_HASH_KEY);

        if (empty($serialized)) {
            return null;
        }

        try {
            $snapshot = unserialize($serialized);
            if ($snapshot instanceof Snapshot) {
                return $snapshot;
            }
        } catch (\Throwable $e) {
        }
        $this->logger->error(__METHOD__ . ' failed', [
            'belongsTo' => $belongsTo
        ]);

        return null;
    }

    public function clearSnapshot(string $belongsTo): void
    {
        $key = $this->sessionKey($belongsTo);

        // 同时删除了所有相关缓存
        $this->driver->getRedis()->del($key);
    }

    public function saveYielding(Session $session, Yielding $yielding): void
    {
        $this->saveSessionData($session, $yielding);
    }

    public function findYielding(string $contextId): ? Yielding
    {
        $data = $this->getSessionData($contextId);

        return $data instanceof Yielding ? $data : null;
    }

    public function saveBreakpoint(Session $session, Breakpoint $breakpoint): void
    {
        // only cache
        $this->setSessionDataCache(
            $session->belongsTo,
            'bp-'.$breakpoint->getSessionDataId(),
            $breakpoint
        );
    }

    public function findBreakpoint(Session $session, string $id): ? Breakpoint
    {
        $data = $this->getSessionDataCache(
            $session->belongsTo,
            'bp-'. $id
        );
        return $data instanceof Breakpoint ? $data : null;
    }


    public function saveContext(Session $session, Context $context): void
    {
        $this->setSessionDataCache(
            $session->belongsTo,
            'ct-'. $context->getSessionDataId(),
            $context
        );

        // only memory should be save to persist database
        if ($context instanceof Memory) {
            $this->saveSessionData($session, $context);
        }
    }

    public function findContext(Session $session, string $contextId): ? Context
    {
        $data = $this->getSessionDataCache(
            $belongsTo = $session->belongsTo,
            $cacheId = 'ct-' . $contextId
        );

        if ($data instanceof Context) {
            return $data;
        }

        $data = $this->getSessionData($contextId);

        if ($data instanceof Context) {
            $this->setSessionDataCache(
                $belongsTo,
                $cacheId,
                $data
            );

            return $data;
        }

        return null;
    }

    protected function setSessionDataCache(
        string $belongsTo,
        string $id,
        SessionData $sessionData
    ) : void
    {
        $redis = $this->driver->getRedis();
        $key = $this->sessionKey($belongsTo);
        $caching = serialize($sessionData);
        $redis->hSet($key, $id, $caching);
    }

    protected function getSessionDataCache(
        string $belongsTo,
        string $id
    ) : ? SessionData
    {
        $redis = $this->driver->getRedis();
        $key = $this->sessionKey($belongsTo);

        $serialized = $redis->hGet($key, $id);

        if (empty($serialized)) {
            return null;
        }

        $data = unserialize($serialized);

        return $data instanceof SessionData ? $data : null;
    }

    protected function saveSessionData(Session $session, SessionData $data)
    {
        $update = TableSchema::getScopeFromSession($session);
        $update[TableSchema::SESSION_DATA_ID] = $id = $data->getSessionDataId();
        $update[TableSchema::SESSION_DATA_TYPE] = $data->getSessionDataType();
        $update[TableSchema::SESSION_DATA_SERIALIZED] = serialize($data);

        $db = $this->driver->getDB();
        $count = $db->table(TableSchema::SESSION_DATA_TABLE)
            ->where(TableSchema::SESSION_DATA_ID, $id)
            ->count();

        if ($count) {
            unset($update[TableSchema::SESSION_DATA_ID]);
            $db->table(TableSchema::SESSION_DATA_TABLE)
                ->where(TableSchema::SESSION_DATA_ID, $id)
                ->update($update);

        } else {
            $db->table(TableSchema::SESSION_DATA_TABLE)
                ->insert($update);
        }
    }

    protected function getSessionData(string $dataId) : ? SessionData
    {
        $data = $this->driver
            ->getDB()
            ->table(TableSchema::SESSION_DATA_TABLE)
            ->where(TableSchema::SESSION_DATA_ID, $dataId)
            ->first();

        if (empty($data)) {
            return null;
        }

        $serialized = $data->{TableSchema::SESSION_DATA_SERIALIZED};

        if (empty($serialized)) {
            return null;
        }

        $unserialized = unserialize($data->serialized);

        return $unserialized instanceof SessionData ? $unserialized : null;
    }
}
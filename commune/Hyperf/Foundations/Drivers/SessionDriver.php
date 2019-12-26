<?php

namespace Commune\Hyperf\Foundations\Drivers;

use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Memory\Memory;
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

    const SNAPSHOT_KEY = 'snapshot:%s';
    const SESSION_KEY = "session:%s";
    const GC_COUNTS_KEY = "gc_counts";

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

    protected function snapshotKey(string $belongsTo) : string
    {
        return sprintf(self::SNAPSHOT_KEY, $belongsTo);
    }

    public function saveSnapshot(Snapshot $snapshot, int $expireSeconds = 0): void
    {
        $sessionId = $snapshot->sessionId;
        $belongsToId = $snapshot->belongsTo;
        $key =  $this->sessionKey($sessionId);
        $snapshotKey = $this->snapshotKey($belongsToId);
        $caching = gzcompress(serialize($snapshot));

        $redis = $this->driver->getRedis();
        $result = $redis->hSet($key, $snapshotKey, $caching);

        if ( $result === false) {
            $this->logger->error(__METHOD__ . ' failed', [
                'belongsTo' => $belongsToId,
                'sessionId' => $sessionId,
            ]);
        } else {
            $redis->expire($key, $expireSeconds);
        }
    }
    
    protected function sessionKey(string $belongsTo) : string
    {
        return sprintf(self::SESSION_KEY, $belongsTo);
    }

    public function findSnapshot(string $sessionId, string $belongsTo): ? Snapshot
    {
        $key = $this->sessionKey($sessionId);
        $snapshotKey = $this->snapshotKey($belongsTo);
        $serialized = $this
            ->driver
            ->getRedis()
            ->hGet($key, $snapshotKey);

        if (empty($serialized)) {
            return null;
        }

        try {
            $snapshot = unserialize(gzuncompress($serialized));
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

    public function clearSnapshot(string $sessionId, string $belongsTo): void
    {
        $key = $this->sessionKey($sessionId);
        $snapshotKey = $this->snapshotKey($belongsTo);
        // 同时删除了所有相关缓存
        $this->driver->getRedis()->hDel($key, $snapshotKey);
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

    public function saveContext(Session $session, Context $context): void
    {
        $this->setSessionDataCache(
            $session->sessionId,
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
            $belongsTo = $session->sessionId,
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

    public function gcContexts(Session $session, string ...$ids): void
    {
        $ids = array_map(function(string $id) : string {
            return 'ct-' . $id;
        }, $ids);

        $this->removeSessionDataCache($session->sessionId, ...$ids);
    }


    protected function setSessionDataCache(
        string $sessionId,
        string $id,
        SessionData $sessionData
    ) : void
    {
        $redis = $this->driver->getRedis();
        $key = $this->sessionKey($sessionId);
        $caching = serialize($sessionData);
        $redis->hSet($key, $id, $caching);
    }

    protected function getSessionDataCache(
        string $sessionId,
        string $id
    ) : ? SessionData
    {
        $redis = $this->driver->getRedis();
        $key = $this->sessionKey($sessionId);

        $serialized = $redis->hGet($key, $id);

        if (empty($serialized)) {
            return null;
        }

        $data = unserialize($serialized);

        return $data instanceof SessionData ? $data : null;
    }

    protected function removeSessionDataCache(
        string $sessionId,
        string ...$ids
    ) : void
    {
        $redis = $this->driver->getRedis();
        $key = $this->sessionKey($sessionId);
        $redis->hDel($key, ...$ids);
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

    public function getGcCounts(string $sessionId): array
    {
        $key = $this->sessionKey($sessionId);
        $redis = $this->driver->getRedis();
        $gcc = $redis->hGet($key, self::GC_COUNTS_KEY);
        if (isset($gcc)) {
            $gcc = unserialize($gcc);
        }
        return is_array($gcc) ? $gcc : [];
    }

    public function saveGcCounts(string $sessionId, array $counts): void
    {
        $key = $this->sessionKey($sessionId);
        $redis = $this->driver->getRedis();
        $redis->hSet($key, self::GC_COUNTS_KEY, serialize($counts));
    }


}
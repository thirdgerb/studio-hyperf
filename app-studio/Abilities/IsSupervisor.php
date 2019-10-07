<?php

/**
 * Class IsSupervisor
 * @package Commune\Studio\Abilities
 */

namespace Commune\Studio\Abilities;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Studio\Commands\WhosYourDaddyCmd;

/**
 * 查看用户是否有 supervisor 权限.
 * @see WhosYourDaddyCmd
 */
class IsSupervisor implements Supervise
{
    /**
     * @var CacheAdapter
     */
    protected $cache;

    /**
     * IsSupervisor constructor.
     * @param CacheAdapter $cache
     */
    public function __construct(CacheAdapter $cache)
    {
        $this->cache = $cache;
    }

    public function isAllowing(Conversation $conversation): bool
    {
        $id = $conversation->getChat()->getChatId();
        $key = static::supervisorKey($id);
        return $this->cache->has($key);
    }

    public static function supervisorKey(string $chatId) : string
    {
        return 'chat:supervisor:'.md5($chatId);
    }

}
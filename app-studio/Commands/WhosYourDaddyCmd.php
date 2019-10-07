<?php


namespace Commune\Studio\Commands;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Studio\Abilities\IsSupervisor;

/**
 * 用咒语的力量赋予用户 supervisor 的权力!
 * @see IsSupervisor
 */
class WhosYourDaddyCmd extends SessionCommand
{
    const SIGNATURE = 'whosyourdaddy 
    {spell : magic spell}
    {token : magic item}
    ';

    const DESCRIPTION = 'speak spell then transform to the supervisor!';

    protected $sneak = true;

    /**
     * @var CacheAdapter
     */
    protected $cache;

    public function __construct(CacheAdapter $cacheAdapter)
    {
        $this->cache = $cacheAdapter;
    }

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $token = env('SUPERVISOR_TOKEN', '');
        $spell = $message['spell'] ?? '';
        if (empty($token)) {
            $this->say()->error('sorry mortal, there is no magic token for any delusional supervisor!! hahahahaha');
            return;
        }

        $matchedToken = $message['token'] ?? '';
        $user = $session->conversation->getUser();
        if ($matchedToken !== $token) {
            $this->say()->error('YOU shall not pass!!');
            $session->logger->error(
               "someone try to pretend to be supervisor",
               [
                   'user' => $user->toArray(),
               ]
            );
            return;
        }

        if ($spell !== $session->sessionId) {
            $this->say()->error("YOU shall not pass!!");
            $session->logger->error(
                "someone try to pretend to be supervisor",
                [
                    'user' => $user->toArray(),
                ]
            );
            return;
        }

        $key = IsSupervisor::supervisorKey($session->conversation->getChat()->getChatId());

        // 重输命令取消 supervisor 身份.
        if ($this->cache->has($key)) {
            $this->say()->info('falwell my lord');
            $this->cache->forget($key);

        } else {

            $this->say()->info("cheat!!");
            $this->cache->set(
                $key,
                'true',
                1800
            );
        }

    }


}
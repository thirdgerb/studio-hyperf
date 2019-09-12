<?php

/**
 * Class Server
 * @package Commune\Hyperf\Servers\Tinker
 */

namespace Commune\Hyperf\Servers\Tinker;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Framework\ChatApp;
use Commune\Chatbot\App\Messages\Events\ConnectionEvt;
use Symfony\Component\Console\Style\SymfonyStyle;

class TinkerChatServer implements ChatServer
{

    /**
     * @var ChatApp
     */
    protected $chatApp;

    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * Server constructor.
     * @param ChatApp $chatApp
     * @param SymfonyStyle $output
     */
    public function __construct(ChatApp $chatApp, SymfonyStyle $output)
    {
        $this->chatApp = $chatApp;
        $this->output = $output;
    }


    public function run(): void
    {
        $this->chatApp->bootApp();

        $kernel = $this->chatApp->getKernel();
        $option = $this->chatApp
            ->getProcessContainer()
            ->get(TinkerOption::class);

        $request = new TinkerMessageRequest(
            $option,
            $this->output,
            new ConnectionEvt()
        );

        while(true) {
            $kernel->onUserMessage($request);
            $answer = $this->output->ask('>>>');

            $request = new TinkerMessageRequest(
                $option,
                $this->output,
                $answer
            );
        }
    }

    public function sleep(int $millisecond): void
    {
        usleep($millisecond * 1000);
    }

    public function fail(): void
    {
        exit(255);
    }

    public function closeClient(Conversation $conversation): void
    {
        exit(0);
    }


}
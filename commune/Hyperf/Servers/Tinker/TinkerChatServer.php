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
     * @var string|null
     */
    protected $scene;

    /**
     * TinkerChatServer constructor.
     * @param ChatApp $chatApp
     * @param SymfonyStyle $output
     * @param null|string $scene
     */
    public function __construct(ChatApp $chatApp, SymfonyStyle $output, ?string $scene)
    {
        $this->chatApp = $chatApp;
        $this->output = $output;
        $this->scene = $scene;
    }


    public function run(): void
    {
        $this->chatApp->bootApp();

        $kernel = $this->chatApp->getKernel();
        $option = $this->chatApp
            ->getProcessContainer()
            ->get(TinkerOption::class);

        $request = new TinkerMessageRequest(
            $this->scene,
            $option,
            $this->output,
            new ConnectionEvt()
        );

        while(true) {
            $kernel->onUserMessage($request);
            $answer = $this->output->ask('>>>');

            $request = new TinkerMessageRequest(
                $this->scene,
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


    protected $available = true;

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $boolean): void
    {
        $this->available = $boolean;
    }


}
<?php


namespace Commune\Wechat\Messages;


use Carbon\Carbon;
use Commune\Chatbot\App\Messages\Media\Audio;
use EasyWeChat\Kernel\Contracts\MessageInterface;

class WechatAudio extends Audio implements WechatMedia
{
    /**
     * @var string
     */
    protected $mediaId;

    public function __construct(string $mediaId, Carbon $createdAt = null)
    {
        $this->mediaId = $mediaId;
        parent::__construct('', $createdAt);
    }

    /**
     * @return string
     */
    public function getMediaId(): string
    {
        return $this->mediaId;
    }


    public function toEasyWechatMessage(): MessageInterface
    {
        return new \EasyWeChat\Kernel\Messages\Voice($this->getMediaId());
    }

}
<?php


namespace Commune\Platform\Wechat\Messages;


use Carbon\Carbon;
use Commune\Chatbot\App\Messages\Media\Image;
use EasyWeChat\Kernel\Contracts\MessageInterface;

class WechatImage extends Image implements WechatMedia
{
    /**
     * @var string
     */
    protected $mediaId;

    /**
     * WechatImage constructor.
     * @param string $mediaId
     * @param string $url
     * @param Carbon|null $createdAt
     */
    public function __construct(string $mediaId, string $url, Carbon $createdAt = null)
    {
        $this->mediaId = $mediaId;
        parent::__construct($url, $createdAt);
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
        return new \EasyWeChat\Kernel\Messages\Image($this->getMediaId());
    }


}
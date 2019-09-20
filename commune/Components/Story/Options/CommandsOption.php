<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * 脚本中可用的命令形式.
 *
 * @property-read string $menu
 * @property-read string $fallback
 * @property-read string $repeat
 * @property-read string $selectEpisode
 * @property-read string $hearDescription
 * @property-read string $operations
 * @property-read string $unlockEndings
 *
 */
class CommandsOption extends Option
{
    public static function stub(): array
    {
        return [
            'menu' => '菜单',
            'fallback' => '返回',
            'selectEpisode' => '章节',
            'hearDescription' => '简介',
            'unlockEndings' => '结局',
            'operations' => '操作',
            'repeat' => '重播',
        ];
    }


}
<?php


namespace Commune\Components\Story\Options;


use Commune\Support\Option;

/**
 * 脚本中可用的命令形式.
 *
 * @property-read string $menu
 * @property-read string $skip
 * @property-read string $returnGame
 * @property-read string $chooseEpisode
 * @property-read string $help
 * @property-read string $unlockEndings
 * @property-read string $quit
 *
 */
class CommandsOption extends Option
{
    public static function stub(): array
    {
        return [
            'menu' => '打开菜单',
            'skip' => '跳过',
            'returnGame' => '继续游戏',
            'chooseEpisode' => '选择章节',
            'help' => '操作简介',
            'unlockEndings' => '解锁结局',
            'quit' => '退出游戏',
        ];
    }


}
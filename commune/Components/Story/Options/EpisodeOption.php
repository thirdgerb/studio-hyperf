<?php


namespace Commune\Components\Story\Options;


use Commune\Components\Story\Basic\EpisodeTask;
use Commune\Support\Option;

/**
 * 每一章的定义.
 *
 * @property-read string $id 章节的唯一ID
 * @property-read string $option 章节的选项.
 * @property-read string $title 章节的标题
 * @property-read array $defaultSlots 默认的参数.
 * @property-read string[] $middleware 每一 stage 都要执行的 middleware
 * @property-read StageOption[] $stages 本章可用的小节.
 * @property-read string $class 实例的类名.
 *
 */
class EpisodeOption extends Option
{
    const IDENTITY = 'id';

    protected static $associations = [
        'stages[]' => StageOption::class,
    ];

    public static function stub(): array
    {
        return [
            'id' => '',
            'title' => '',
            'middleware' => [],
            'defaultSlots' => [],
            'stages' => [],
            'class' => EpisodeTask::class,
        ];
    }

    public function getStageOption(string $stageName) : ? StageOption
    {
        foreach ($this->stages as $stage) {
            if ($stage->id === $stageName) {
                return $stage;
            }
        }
        return null;
    }

}
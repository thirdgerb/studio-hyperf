<?php


namespace Commune\Components;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\Story\Providers\StoryServiceProvider;

/**
 *
 * StoryComponent 是一个情景互动游戏的示范.
 * 可参考本模块开发类似的互动游戏.
 * 也可以封装出基于配置的引擎.
 *
 * @property-read string[] $resources 可以加载的资源
 * @property-read string $translationPath 脚本内容文件所在目录.
 */
class StoryComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'resources' => [
                __DIR__ . '/Story/examples/changbanpo.yaml',
            ],
            'translationPath' => __DIR__ . '/Story/langs'
        ];
    }


    protected function doBootstrap(): void
    {
        $this->app->registerProcessService(
            new StoryServiceProvider(
                $this->app->getProcessContainer(),
                $this
            )
        );

        $this->loadTranslationResource($this->translationPath);

    }

}
<?php

namespace common\modules\article;

use yii\base\BootstrapInterface;
use yii\helpers\VarDumper;

/**
 * Module bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
//        $app->getUrlManager()->addRules([
//            'article' => 'article/article/index',
//            'article/<alias>-<id:\d+>' => 'article/article/view',
//        ]);

        // Add module I18N category.
//        if (!isset($app->i18n->translations['article']) && !isset($app->i18n->translations['article/*'])) {
//            $app->i18n->translations['article'] = [
//                'class' => 'yii\i18n\PhpMessageSource',
//                'basePath' => '@common/modules/article/messages',
//            ];
//        }
    }
}

<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

/**
 * @var yii\web\View $this
 * @var common\modules\image\models\Image $model
 */
?>

<?php echo newerton\fancybox\FancyBox::widget([
    'target' => 'a[rel=fancybox]',
    'helpers' => true,
    'mouse' => true,
    'config' => [
        'maxWidth' => '90%',
        'maxHeight' => '90%',
        'playSpeed' => 7000,
        'padding' => 0,
        'fitToView' => false,
        'width' => '70%',
        'height' => '70%',
        'autoSize' => false,
        'closeClick' => false,
        'openEffect' => 'elastic',
        'closeEffect' => 'elastic',
        'prevEffect' => 'elastic',
        'nextEffect' => 'elastic',
        'closeBtn' => false,
        'openOpacity' => true,
        'helpers' => [
            'title' => ['type' => 'float'],
            'buttons' => [],
            'thumbs' => ['width' => 68, 'height' => 50],
            'overlay' => [
                'css' => [
                    'background' => 'rgba(0, 0, 0, 0.8)'
                ]
            ]
        ],
    ]
]); ?>

<div class="images" xmlns="http://www.w3.org/1999/html">

    <div class="gallery row">
        <div class="col-md-6">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image_id => $image): ?>
                    <?php $thumb_image = !empty($image[$model->getThumbImageSize()]) ? $image[$model->getThumbImageSize()] : null ?>
                    <div class="col-md-3">
                        <div class="thumbnail">
                            <?= Html::a(
                                Html::img(!empty($thumb_image) ?
                                    '/upload/' . $image[$model->getThumbImageSize()] :
                                    \Yii::$app->params['frontendUrl'] . 'images/no_image.png'
                                ),
                                '/upload/' . $image['original'], [
                                    'rel' => 'fancybox',
                                ]
                            ) ?>
                            <div class="row caption">
                                <div class="col-md-12">
                                    <p class="pull-left">
                                        <?php echo Html::a((!empty($main_image) && $main_image->id == $image_id ? Icon::show('check', ['class' => 'fa'], Icon::FA) : '') . \Yii::t('app', 'Main image'), [!empty($main_image_url) ? $main_image_url : 'main-image', 'id' => $image_id], [
                                            'class' => 'btn btn-xs btn-info',
                                        ]); ?>
                                    </p>

                                    <p class="pull-right">
                                        <?php echo Html::a(\Yii::t('app', 'Delete'), [!empty($delete_url) ? $delete_url : 'delete-image', 'id' => $image_id], [
                                            'class' => 'btn btn-xs btn-danger',
                                            'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
//                                            'data-method' => 'post',
                                        ]); ?>
                                    </p>
                                </div>

                                <div class="col-md-12">
                                    <p>
                                        <?php foreach ($image as $size => $path): ?>
                                            <?= Html::textInput('image_url', '/upload/' . $path) ?>
                                            <?= Html::a($size == 'original' ? \Yii::t('app', 'Original') : $size, '/upload/' . $path, [
                                                'target' => '_blank',
                                            ]) ?>
                                        <?php endforeach; ?>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
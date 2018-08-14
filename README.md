Image Module for Yii2
==============================

[![License](https://poser.pugx.org/xtracode/yii2-image/license.svg)](https://packagist.org/packages/xtracode/yii2-image)
[![Latest Stable Version](https://poser.pugx.org/xtracode/yii2-image/v/stable.svg)](https://packagist.org/packages/xtracode/yii2-image)
[![Latest Unstable Version](https://poser.pugx.org/xtracode/yii2-image/v/unstable.svg)](https://packagist.org/packages/xtracode/yii2-image)
[![Total Downloads](https://poser.pugx.org/xtracode/yii2-image/downloads.svg)](https://packagist.org/packages/xtracode/yii2-image)

Yii2 module for image manipulating.

Features
--------
- [x] image upload
- [x] display image widget
- [ ] watermark (text and image)
- [x] image resize on demand

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "xtracode/yii2-image" "*"
```
or add

```
"xtracode/yii2-image" : "dev-master"
```

to the require section of your application's `composer.json` file.

Configuration
=============

- Add module to config section:

```php
'modules' => [
    'image' => [
        'class' => 'xtracode\yii2-image\ImageModule'
    ]
]
```

- Run migrations:

```php
php yii migrate --migrationPath=@xtracode/yii2-image/migrations
```

Usage
-----

- Add actions to your controller:

```php
public function actions()
{
    return [
        'delete-image' => [
            'class' => '\xtracode\image\actions\DeleteImageAction',
        ],
        'main-image' => [
            'class' => '\xtracode\image\actions\MainImageAction',
        ],
    ];
}
```

Upload multiple image
---------------------

Use method **$model->uploadSingleImage()** in controller:

```php
public function actionCreate()
{
    $model = new Article;

    if ($model->load($_POST) && $model->save()) {
        $model->uploadImage();

        \Yii::$app->session->setFlash('success', \Yii::t('article', 'Article successfully saved'));
        return $this->redirect(['view', 'id' => $model->id]);
    } else {
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
```

In view:

```php
<div class="col-sm-offset-1">
    <?= \xtracode\image\widgets\ImageList::widget([
        'model' => $model,
        'model_id' => $model->id,
    ]) ?>
</div>

<?= $form->field($model, 'images[]')->widget(\kartik\widgets\FileInput::classname(), [
    'options' => ['accept' => 'image/*', 'multiple' => true],
    'pluginOptions' => [
        'showCaption' => true,
        'showUpload' => false
    ]
]); ?>
```

Upload single image
-------------------

use method **$model->uploadSingleImage()** in controller:

```php
public function actionCreate()
{
    $model = new Article;

    if ($model->load($_POST) && $model->save()) {
        $model->uploadSingleImage();

        \Yii::$app->session->setFlash('success', \Yii::t('article', 'Article successfully saved'));
        return $this->redirect(['view', 'id' => $model->id]);
    } else {
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
```

In view:

```php
<div class="col-sm-offset-1">
    <?= \xtracode\image\widgets\ImageList::widget([
        'model' => $model,
        'model_id' => $model->id,
    ]) ?>
</div>

<?= $form->field($model, 'image')->widget(\kartik\widgets\FileInput::classname(), [
    'options' => ['accept' => 'image/*', 'multiple' => true],
    'pluginOptions' => [
        'showCaption' => true,
        'showUpload' => false
    ]
]); ?>
```

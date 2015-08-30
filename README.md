Image Module for Yii2
==============================

Yii2 module for image manipulating.

Features
--------
- [x] image upload
- [x] display image widget
- [ ] watermark (text and image)
- [ ] image resize on demand

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "xtracode/yii2-image" "*"
```
or add

```json
"xtracode/yii2-image" : "*"
```

to the require section of your application's `composer.json` file.

Configuration
=============

- Add module to config section:

```
'modules' => [
    'image' => [
        'class' => 'xtracode\yii2-image\ImageModule'
    ]
]
```

- Run migrations:

```
php yii migrate --migrationPath=@xtracode/yii2-image/migrations
```

Usage
-----
Using a model:

```
use xtracode\image\widgets\ImageList;

<?= ImageList::widget([
    'model' => $model,
    'model_id' => $model->id,
]) ?>
```

Image Module for Yii2
==============================

Image module.

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

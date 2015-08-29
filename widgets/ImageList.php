<?php

namespace xtracode\image\widgets;

use xtracode\image\models\Image;
use yii\helpers\Html;
use yii\helpers\VarDumper;

class ImageList extends \yii\bootstrap\Widget
{
    /**
     * Records limit
     * @var int
     */
    public $limit = 5;

    /**
     * Image model name
     * @var
     */
    public $model;

    /**
     * Image model ID
     * @var
     */
    public $model_id;

    /**
     * Custom main image url
     * @var
     */
    public $main_image_url;

    /**
     * Custom delete url
     * @var
     */
    public $delete_url;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $images = $this->model->getModelImages($this->model_id);

        $main_image = $this->model->getMainModelImage($this->model_id);

        return $this->render('image_list', [
            'model' => $this->model,
            'images' => $images,
            'main_image' => $main_image,
            'main_image_url' => $this->main_image_url,
            'delete_url' => $this->delete_url,
        ]);
    }
}
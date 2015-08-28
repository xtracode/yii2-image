<?php

namespace common\modules\image\actions;

use common\modules\image\models\Image;
use Yii;
use yii\base;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\widgets\LinkPager;

class MainImageAction extends base\Action
{
    /**
     * Set image as main for model and model_id.
     * If successful, the browser will be redirected to the 'index' page.
     *
     * @param $id
     * @return mixed
     */
    public function run($id)
    {
        if (Image::setMainImage($id)) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Image successfully set as main'));
        } else {
            \Yii::$app->session->setFlash('error', \Yii::t('app', 'An error occur during set image as main'));
        }

        return $this->controller->redirect(\Yii::$app->request->referrer);
    }
}
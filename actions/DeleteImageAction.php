<?php

namespace common\modules\image\actions;

use common\modules\image\models\Image;
use Yii;
use yii\base;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\widgets\LinkPager;

class DeleteImageAction extends base\Action
{
    /**
     * Model User ID check if user has rights to delete image
     * @var
     */
    public $check_user_id = false;

    /**
     * Deletes an existing Image model and image file.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param $id
     * @param null $model_user_id
     * @return Response
     */
    public function run($id, $model_user_id = null)
    {
        if ($this->check_user_id) {
            if ($model_user_id != \Yii::$app->user->id) {
                \Yii::$app->session->setFlash('error', \Yii::t('app', 'You don\'t allow to delete this image'));
                return $this->controller->redirect(\Yii::$app->request->referrer);
            }
        }

        if (Image::deleteImage($id)) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Image successfully deleted'));
        } else {
            \Yii::$app->session->setFlash('error', \Yii::t('app', 'An error occur during image deleting'));
        }

        return $this->controller->redirect(\Yii::$app->request->referrer);
    }
}
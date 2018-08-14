<?php

namespace xtracode\image\models;

use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "image".
 */
class Image extends \xtracode\image\models\base\Image
{
    /**
     * Image
     * @var
     */
    public $image;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            // add additional translations
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // add additional rules
        ]);
    }

//    /**
//     * Все изображения конкретной модели
//     *
//     * @param $model_id
//     * @return array|bool
//     */
//    public static function getModelImages($model_id, $main = false)
//    {
//        $models = self::find()->where(['model_id' => $model_id])->all();
//
//        if (!empty($models)) {
//            $images = [];
//            foreach ($models as $model) {
//                if (!empty($model->width) && !empty($model->height)) {
//                    $images[$model->pid][$model->width . 'x' . $model->height] = $model->file_name;
//                } else {
//                    $images[$model->id]['original'] = $model->file_name;
//                }
//            }
//
//            return $images;
//        } else {
//            return false;
//        }
//    }

    /**
     * Image upload
     *
     * @param $file
     * @param $model_id
     * @param string $width
     * @param string $height
     * @param string $dir - папка внутри загрузочной директории
     * @param string $src_image - изображений, подгружаемое из файла (например в директории upload/tmp/)
     * @return bool
     */
    public function uploadImage($file, $model_id, $width = '250', $height = '250', $dir = null, $src_image = null)
    {
        if (!empty($file->tempName) || !empty($src_image)) {
            $sub_dirs = substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2);

            if (!empty($dir)) {
                $this->dir = $dir . '/' . $sub_dirs;
            } else {
                $this->dir = 'i/' . $sub_dirs;
            }

            $dir_path = Yii::getAlias('@frontend/web/upload/') . $this->dir;
            if (!is_dir($dir_path)) {
                @mkdir($dir_path, 0777, true);
            }

            // TODO find why extension is empty when uploading files from url
            if (empty($file->extension)) {
                $pathinfo = pathinfo($file->tempName);
                $ext = $pathinfo['extension'];
            } else {
                $ext = $file->extension;
            }

            if (empty($src_image)) {
                $image = \Yii::$app->image->load($file->tempName);
                $this->file_name = $model_id . '_' . md5(time() + rand()) . '.' . $ext;
            }

            // Если заданы точные размеры изображения
            if (!empty($width) && !empty($height)) {
                $this->width = $width;
                $this->height = $height;

                $image->resize($this->width, $this->height, \Yii\image\drivers\Image::PRECISE);
                $image->crop($this->width, $this->height);
            }

            $image_saved = $image->save($dir_path . '/' . $this->file_name);

            if ($image_saved) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

//    /**
//     * Обновляем изображение
//     *
//     * @param $id
//     * @return \yii\web\Response
//     */
//    public function updateImage($id)
//    {
//        $model = File::findOne($id);
//
//        $model->load($_POST);
//
//        if ($model->load($_POST) && $model->save()) {
//            return $this->redirect(\Yii::$app->request->referrer);
//        } else {
//            VarDumper::dump($model->getErrors());
//        }
//    }

    /**
     * Image delete
     *
     * @param $id
     * @return bool
     */
    public static function deleteImage($id)
    {
        $models = self::find()->where(['or', 'id=' . $id, 'pid=' . $id])->all();

        $path = \Yii::getAlias('@frontend/web/upload/');

        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($model->dir)) {
                    $dir = $model->dir;
                } else {
                    $dir = 'i';
                }

                @unlink($path . $dir . '/' . $model->file_name);
                self::findOne($model->id)->delete();
            }
        }

        return true;
    }

    /**
     * Deletes previous model images
     * TODO add transaction deleting with true and false return status
     *
     * @param $model
     * @param $model_id
     * @return bool
     * @throws \Exception
     */
    public static function deleteModelImages($model, $model_id)
    {
        $models = self::find()->where([
            'model' => $model,
            'model_id' => $model_id,
        ])->all();

        $path = \Yii::getAlias('@frontend/web/upload/');

        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($model->dir)) {
                    $dir = $model->dir;
                } else {
                    $dir = 'i';
                }

                @unlink($path . $dir . '/' . $model->file_name);
                self::findOne($model->id)->delete();
            }
        }

        return true;
    }

    /**
     * Set main image
     * TODO add transaction saving with true and false return status
     *
     * @param $id
     * @return bool
     */
    public static function setMainImage($id)
    {
        $image = Image::findOne($id);

        // Flush previous main image
        Image::updateAll(['main' => 0], ['model' => $image->model, 'model_id' => $image->model_id]);

        // Set new main image
        $models = Image::find()->where(['model' => $image->model, 'model_id' => $image->model_id])->andWhere(['id' => $image->id])->orWhere(['pid' => $image->id])->all();
        foreach ($models as $model) {
            $model->main = 1;
            $model->save();
        }

        return true;
    }
}

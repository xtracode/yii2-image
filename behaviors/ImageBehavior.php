<?php

namespace xtracode\image\behaviors;

use xtracode\image\models\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;
use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

class ImageBehavior extends Behavior
{
    /**
     * Single image file
     * @var
     */
    public $image;

    /**
     * Multiple image file
     * @var
     */
    public $images;

    /**
     * Crop image info
     * @var
     */
    public $crop_info;

    /**
     * All thumb image sizes
     * @var array
     */
    public $thumb_sizes = [];

    /**
     * Directory for uploaded files
     * @var
     */
    private $dir;

    /**
     * Default dir
     * @var string
     */
    public $default_dir = 'i';

    /**
     * Update previous image
     * @var bool
     */
    public $update_image = false;

    /**
     * Image URL
     * @var
     */
    public $image_url;

//    public function events()
//    {
//        return [
//            ActiveRecord::EVENT_BEFORE_VALIDATE => 'getSlug'
//        ];
//    }

    public function attach($owner)
    {
        parent::attach($owner);

//        $owner->attributeLabels(['image' => \Yii::t('app', 'Image')]);
//        array_merge($owner->attributeLabels(), [
//            // add additional translations
//            'image' => \Yii::t('app', 'Image'),
//        ]);
//        yii\helpers\VarDumper::dump($owner->attributeLabels(), 10, true);
//        exit();

//        if (in_array($owner->scenario, $this->scenarios))
//        {
//            $fileValidator = \yii\validators\Validator::createValidator('file', $this->owner, $this->attributeName, ['types' => $this->fileTypes]);
//            $owner->validators[] = $fileValidator;
//        }
    }

    /**
     * Multiple image upload
     *
     * @param null $dir
     */
    public function uploadImages($dir = null)
    {
        // Upload multiple images
        $this->owner->images = \yii\web\UploadedFile::getInstances($this->owner, 'images');

        foreach ($this->owner->images as $image) {
            $this->uploadImage($dir, $image);
        }
    }

    /**
     * Upload single image with previous image update
     *
     * @param null $dir
     */
    public function uploadSingleImage($dir = null)
    {
        $this->update_image = true;

        // Upload multiple images
        $image = \yii\web\UploadedFile::getInstance($this->owner, 'image');

        if (!empty($image)) {
            $this->uploadImage($dir, $image);
        }
    }

    /**
     * Single image upload
     *
     * @param null $dir
     * @param null $image
     * @param null $type
     */
    public function uploadImage($dir = null, $image = null, $type = null)
    {
        if (empty($dir)) {
            $this->dir = $this->default_dir;
        } else {
            $this->dir = $dir;
        }

        // Upload single images
        if (empty($image)) {
            $this->owner->image = \yii\web\UploadedFile::getInstance($this->owner, 'image');
        } else {
            $this->owner->image = $image;
        }

        // Get info about owner class
        $class = new \ReflectionClass($this->owner);

        if ($this->owner->validate()) {
            // Delete previous model image if necessary
            if ($this->update_image) {
                Image::deleteModelImages($class->getShortName(), $this->owner->id);
            }

            // Original image
            $file = new Image;
            $file->model = $class->getShortName();
            $file->model_id = $this->owner->id;
            $file->image = $this->owner->image;
            $file->dir = $this->dir;
            $image_saved = $file->uploadImage($this->owner->image, $file->model_id, null, null, $file->dir);
            if ($image_saved) {
                $file->create_time = date('Y-m-d H:i:s');
                $file->save();
            }

            $original_file = $file;

            // Save thumb images
            foreach ($this->thumb_sizes as $size) {
                if (!empty($original_file) && !empty($size['width']) && !empty($size['height'])) {
                    $file = new Image;
                    $file->model = $class->getShortName();
                    $file->pid = $original_file->id;
                    $file->model_id = $this->owner->id;
                    $file->image = $this->owner->image;
                    $file->dir = $this->dir;
                    $image_saved = $file->uploadImage($this->owner->image, $file->model_id, $size['width'], $size['height'], $file->dir);
                    if ($image_saved) {
                        $file->create_time = date('Y-m-d H:i:s');
                        $file->save();
                    }
                }
            }

            // Rendering information about crop of ONE option
            if (!empty($this->owner->crop_info)) {
                $cropInfo = yii\helpers\Json::decode($this->owner->crop_info)[0];
                $cropInfo['dw'] = (int)$cropInfo['dw']; //new width image
                $cropInfo['dh'] = (int)$cropInfo['dh']; //new height image
                $cropInfo['x'] = abs($cropInfo['x']); //begin position of frame crop by X
                $cropInfo['y'] = abs($cropInfo['y']); //begin position of frame crop by Y
                $cropInfo['w'] = (int)$cropInfo['w']; //width of cropped image
                $cropInfo['h'] = (int)$cropInfo['h']; //height of cropped image

//                yii\helpers\VarDumper::dump($cropInfo, 10, true);
//                $modelImage::$thumb_sizes[] = [
//                    'width' => $cropInfo['w'],
//                    'height' => $cropInfo['h'],
//                ];
//                yii\helpers\VarDumper::dump($modelImage::$thumb_sizes, 10, true);
//                exit();

                // Saving crop thumbnail
                $image = yii\imagine\Image::getImagine()->open($this->owner->image->tempName);
                $newSizeThumb = new Box($cropInfo['dw'], $cropInfo['dh']);
                $cropSizeThumb = new Box($cropInfo['w'], $cropInfo['h']); //frame size of crop
                $cropPointThumb = new Point($cropInfo['x'], $cropInfo['y']);

                $sub_dirs = substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2);
                $dir = $this->dir . '/' . $sub_dirs;
                $dir_path = Yii::getAlias('@frontend/web/upload/') . $dir;
                if (!is_dir($dir_path)) {
                    @mkdir($dir_path, 0777, true);
                }

                $file_name = $this->owner->id . '_' . md5(time() + rand()) . '.' . $this->owner->image->getExtension();
                $pathThumbImage = $dir_path . '/' . $file_name;

                $image_saved = $image->resize($newSizeThumb)
                    ->crop($cropPointThumb, $cropSizeThumb)
                    ->save($pathThumbImage, ['quality' => 100]);

                $file = new Image;
                $file->model = $class->getShortName();
                $file->pid = $original_file->id;
                $file->model_id = $this->owner->id;
                $file->dir = $dir;
                $file->file_name = $file_name;
                $file->width = $cropInfo['w'];
                $file->height = $cropInfo['h'];
                if ($image_saved) {
                    $file->create_time = date('Y-m-d H:i:s');
                    $file->save();
                }
            }

        } else {
            yii\helpers\VarDumper::dump($this->owner->getErrors(), 10, true);
            exit();
        }
    }

    /**
     * Upload single image without any manipulations (cropping, resizing and etc)
     *
     * @param null $dir
     * @return bool
     */
    public function uploadFile($dir = null)
    {
        $image = yii\imagine\Image::getImagine()->open($this->owner->image->tempName);

        if (empty($dir)) {
            $this->dir = $this->default_dir;
        }

        $sub_dirs = substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2);
        $dir = $this->dir . '/' . $sub_dirs;
        $dir_path = Yii::getAlias('@frontend/web/upload/') . $dir;
        if (!is_dir($dir_path)) {
            @mkdir($dir_path, 0777, true);
        }

        $image_saved = $image->save($dir_path . '/' . $this->file_name);

        if ($image_saved) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * All models images
     *
     * @return array|bool
     */
    public static function getAllModelImages()
    {
        $models = self::find()->where(['model' => 'Article'])->all();

        if (!empty($models)) {
            $images = [];
            foreach ($models as $model) {
                if (!empty($model->width) && !empty($model->height)) {
                    $images[$model->model_id][$model->pid][$model->width . 'x' . $model->height] = $model->file_name;
                } else {
                    $images[$model->model_id][$model->id]['original'] = $model->file_name;
                }
            }

            return $images;
        } else {
            return false;
        }
    }

    /**
     * All images of the model
     *
     * @param $model_id
     * @param bool $main
     * @param bool $single_image
     * @return array|bool
     */
    public function getModelImages($model_id, $main = false, $single_image = false)
    {
        // Get info about owner class
        $class = new \ReflectionClass($this->owner);

        // Get all images of the model
        $models = Image::find()->where(['model' => $class->getShortName(), 'model_id' => $model_id])->all();

        $images = [];
        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($model->width) && !empty($model->height)) {
                    if ($single_image) {
                        $images[$model->width . 'x' . $model->height] = $model->dir . '/' . $model->file_name;
                    } else {
                        $images[$model->pid][$model->width . 'x' . $model->height] = $model->dir . '/' . $model->file_name;
                    }
                } else {
                    if ($single_image) {
                        $images['original'] = $model->dir . '/' . $model->file_name;
                    } else {
                        $images[$model->id]['original'] = $model->dir . '/' . $model->file_name;
                    }
                }
            }

            return $images;
        } else {
            return false;
        }
    }

    /**
     * Thumb image of model
     *
     * @param null $width
     * @param null $height
     * @return array|null|ActiveRecord
     */
    public function getThumbImage($width = null, $height = null)
    {
        // Get info about owner class
        $class = new \ReflectionClass($this->owner);

        $model = Image::find()->where([
            'model_id' => $this->owner->id,
            'model' => $class->getShortName(),
            'width' => !empty($width) ? $width : $this->thumb_sizes[0]['width'],
            'height' => !empty($height) ? $height : $this->thumb_sizes[0]['height'],
        ])->one();

        if (empty($model) && !empty($width) && !empty($height)) {
            $model = $this->getResizedImage($width, $height);
        }

        return $model;
    }

    /**
     * Image thumb sizes
     *
     * @return string
     */
    public function getThumbImageSize()
    {
        return $this->thumb_sizes[0]['width'] . 'x' . $this->thumb_sizes[0]['height'];
    }

    /**
     * Get main model image
     *
     * @param $model_id
     * @return array|bool|null|ActiveRecord
     */
    public function getMainModelImage($model_id)
    {
        // Get info about owner class
        $class = new \ReflectionClass($this->owner);

        // Get main model image
        $model = Image::find()->where(['model' => $class->getShortName(), 'model_id' => $model_id, 'main' => 1, 'pid' => null])->one();

        if (!empty($model)) {
            return $model;
        } else {
            return false;
        }
    }

    /**
     * Grid images
     *
     * @param null $width
     * @param null $height
     * @return null
     */
    public function getGridImage($width = null, $height = null)
    {
        $models = $this->getModelImages($this->owner->id, false, true);

        if (empty($width) || empty($height)) {
            $thumb_size = $this->getThumbImageSize();
        }

        if (!empty($models)) {
            $images['original'] = $models['original'];
            $images['thumb'] = $models[$thumb_size];
        } else {
            $images = null;
        }

        return $images;
    }
    
    /**
     * Save images from URL
     *
     * @return string
     */
    public function getImagesFromUrl()
    {
        // Settings and info for tmp file
        $tmp_dir = 'tmp';
        $dir_path = Yii::getAlias('@frontend/web/upload/') . $tmp_dir;

        $pathinfo = pathinfo($this->owner->image_url);
        $ext = $pathinfo['extension'];

        $file_name = md5(time());
        $file = $dir_path . '/' . $file_name . '.' . $ext;

        // Save file from url via curl
        $ch = curl_init($this->owner->image_url);
        $fp = fopen($file, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        // Get info about owner class
        $class = new \ReflectionClass($this->owner);
        $class_name = $class->getShortName();

        // Fill FILES array with images data
        $_FILES[$class_name]['name']['images'][] = $file_name;
        $_FILES[$class_name]['type']['images'][] = 'image/jpeg';
        $_FILES[$class_name]['tmp_name']['images'][] = $file;
        $_FILES[$class_name]['error']['images'][] = UPLOAD_ERR_OK;
        $_FILES[$class_name]['size']['images'][] = filesize($file);

        return $file;
    }

    /**
     * Make resize on the fly
     *
     * @param $width
     * @param $height
     * @return array|bool|null|ActiveRecord
     */
    public function getResizedImage($width, $height)
    {
        // Get info about owner class
        $class = new \ReflectionClass($this->owner);

        // Check if the thumb image doesn't exist to create new
        $model = Image::find()->where([
            'model' => $class->getShortName(),
            'model_id' => $this->owner->id,
            'width' => $width,
            'height' => $height,
        ])->one();

        if (empty($model)) {
            // Get original model image
            $original_image_model = Image::find()->where([
                'model' => $class->getShortName(),
                'model_id' => $this->owner->id,
                'pid' => null,
            ])->one();

            $original_image = Yii::getAlias('@frontend/web/upload/') . $original_image_model->dir . '/' . $original_image_model->file_name;

            if (!file_exists($original_image)) {
                return false;
            }

            // Saving crop thumbnail
            $image = yii\imagine\Image::getImagine()->open($original_image);

            $newSizeThumb = new Box($width, $height);
//            $cropSizeThumb = new Box($width, $height); //frame size of crop
//            $cropPointThumb = new Point($cropInfo['x'], $cropInfo['y']);

            $sub_dirs = substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2) . '/' . substr(md5(time() + rand()), 0, 2);
            $dir = $this->default_dir . '/' . $sub_dirs;
            $dir_path = Yii::getAlias('@frontend/web/upload/') . $dir;

            if (!is_dir($dir_path)) {
                @mkdir($dir_path, 0777, true);
            }

            $pathinfo = pathinfo($original_image);
            $ext = $pathinfo['extension'];

            $file_name = $this->owner->id . '_' . md5(time() + rand()) . '.' . $ext;
            $pathThumbImage = $dir_path . '/' . $file_name;

            $image_saved = $image->resize($newSizeThumb)
                ->save($pathThumbImage, ['quality' => 100]);

//            yii\helpers\VarDumper::dump($pathThumbImage, 10, true);
//            yii\helpers\VarDumper::dump($image_saved, 10, true);
//            exit();

            // Save new thumb image to DB
            $new_thumb_image = new Image;
            $new_thumb_image->model = $class->getShortName();
            $new_thumb_image->pid = $original_image_model->id;
            $new_thumb_image->model_id = $this->owner->id;
            $new_thumb_image->dir = $dir;
            $new_thumb_image->file_name = $file_name;
            $new_thumb_image->width = $width;
            $new_thumb_image->height = $height;
            if ($image_saved) {
                $new_thumb_image->create_time = date('Y-m-d H:i:s');
                $new_thumb_image->save();

//                $new_thumb_image_file = $dir_path . '/' . $file_name;
            }

            return !empty($new_thumb_image) ? $new_thumb_image : null;
        } else {
            return false;
        }
    }
}
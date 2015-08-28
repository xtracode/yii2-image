<?php

namespace common\modules\image\models\base;

use Yii;

/**
 * This is the base-model class for table "image".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $model
 * @property integer $model_id
 * @property string $dir
 * @property string $file_name
 * @property integer $width
 * @property integer $height
 * @property string $title
 * @property string $description
 * @property integer $main
 * @property integer $type
 * @property string $create_time
 */
class Image extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'model_id', 'width', 'height', 'main', 'type'], 'integer'],
            [['model_id', 'create_time'], 'required'],
            [['create_time'], 'safe'],
            [['model'], 'string', 'max' => 64],
            [['dir'], 'string', 'max' => 150],
            [['file_name', 'description'], 'string', 'max' => 1025],
            [['title'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pid' => Yii::t('app', 'Parent ID'),
            'model' => Yii::t('app', 'Model'),
            'model_id' => Yii::t('app', 'Model ID'),
            'dir' => Yii::t('app', 'Директория файла'),
            'file_name' => Yii::t('app', 'Имя файла'),
            'width' => Yii::t('app', 'Ширина'),
            'height' => Yii::t('app', 'Высота'),
            'title' => Yii::t('app', 'Заголовок файла'),
            'description' => Yii::t('app', 'Описание файла'),
            'main' => Yii::t('app', 'Главное изображение'),
            'type' => Yii::t('app', 'Type'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }


    
}

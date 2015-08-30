<?php

use yii\db\Schema;
use yii\db\Migration;

class m150830_211010_create_image_table extends Migration
{
    public function safeUp()
    {
        $this->db->createCommand("
            CREATE TABLE `image` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `pid` int(11) unsigned DEFAULT NULL COMMENT 'Parent ID',
              `model` char(64) NOT NULL DEFAULT '',
              `model_id` int(10) unsigned NOT NULL,
              `dir` varchar(150) DEFAULT NULL COMMENT 'Image directory',
              `file_name` varchar(1025) NOT NULL DEFAULT '',
              `width` int(11) DEFAULT NULL,
              `height` int(11) DEFAULT NULL,
              `title` char(128) DEFAULT '' COMMENT 'Title of image',
              `description` varchar(1025) DEFAULT '' COMMENT 'Description of image',
              `main` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Main image flag',
              `type` int(11) NOT NULL DEFAULT '1' COMMENT 'Image type - main, preview',
              `create_time` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `model_id` (`model_id`),
              KEY `model` (`model`),
              KEY `pid` (`pid`),
              KEY `type` (`type`),
              KEY `main` (`main`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ")->execute();
    }

    public function safeDown()
    {
        $this->dropTable('image');
    }
}

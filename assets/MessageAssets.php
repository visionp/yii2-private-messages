<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 22.05.2015
 * Time: 14:05
 */

namespace vision\messages\assets;


class MessageAssets extends \yii\web\AssetBundle {
    public $sourcePath = '@vendor/vision/yii2-private-messages';
    public $js = [
        'js/vision_messages.js',
    ];
    public $css = [
        'css/vision_messages.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
} 
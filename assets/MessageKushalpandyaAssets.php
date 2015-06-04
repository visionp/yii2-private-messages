<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 04.06.2015
 * Time: 12:58
 */

namespace vision\messages\assets;


class MessageKushalpandyaAssets extends \yii\web\AssetBundle  {

    public $sourcePath = '@vendor/vision/yii2-private-messages/';
    public $js = [
        'js/kushalpandya.js',
    ];
    public $css = [
        'css/kushalpandya.css',
    ];
    public $depends = [
        'vision\messages\assets\MessageAssets'
    ];

} 
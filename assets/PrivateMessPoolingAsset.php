<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 06.06.2015
 * Time: 17:19
 */

namespace vision\messages\assets;


class PrivateMessPoolingAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@vendor/vision/yii2-private-messages';
    public $js = [
        'js/private_mess_pooling.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
} 
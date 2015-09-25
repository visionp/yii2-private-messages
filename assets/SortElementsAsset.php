<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 25.09.2015
 * Time: 14:46
 */

namespace vision\messages\assets;


class SortElementsAsset extends BaseMessageAssets {
    public $js = [
        'js/sortElement.js'
    ];


    public $depends = [
        'vision\messages\assets\PrivateMessPoolingAsset',
        'yii\web\JqueryAsset'
    ];
}
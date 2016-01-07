<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 26.08.2015
 * Time: 13:25
 */

namespace vision\messages\assets;

use yii\web\AssetBundle;


class TinyscrollbarAsset extends AssetBundle
{
    public $sourcePath = '@bower/tinyscrollbar';

    public $js = [
        'lib/jquery.tinyscrollbar.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

}
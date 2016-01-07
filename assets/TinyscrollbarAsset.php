<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 26.08.2015
 * Time: 13:25
 */

namespace vision\messages\assets;



class TinyscrollbarAsset extends BaseMessageAssets
{
    public $js = [
        'js/jquery.tinyscrollbar.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

}
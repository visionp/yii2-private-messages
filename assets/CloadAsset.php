<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 26.08.2015
 * Time: 12:53
 */

namespace vision\messages\assets;


class CloadAsset extends BaseMessageAssets {

    public $js = [
        'js/private_mess_cload.js'
    ];

    public $css = [
        'css/cload_message.css',
    ];

    public $depends = [
        'vision\messages\assets\PrivateMessPoolingAsset',
        'vision\messages\assets\TinyscrollbarAsset',
        'vision\messages\assets\SortElementsAsset'
    ];

}
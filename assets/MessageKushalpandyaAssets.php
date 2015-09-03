<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 04.06.2015
 * Time: 12:58
 */

namespace vision\messages\assets;


class MessageKushalpandyaAssets extends BaseMessageAssets  {

    public $css = [
        'css/kushalpandya.css',
    ];

    public $depends = [
        'vision\messages\assets\MessageAssets'
    ];

} 
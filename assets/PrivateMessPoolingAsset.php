<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 06.06.2015
 * Time: 17:19
 */

namespace vision\messages\assets;


use yii\web\View;

class PrivateMessPoolingAsset extends BaseMessageAssets {

    public $js = [
        'js/private_mess_pooling.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];


    /**
     * Registers this asset bundle with a view.
     * @param View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        $view->registerJs('alert(\'ok\');');
        return self::register($view);
    }


} 
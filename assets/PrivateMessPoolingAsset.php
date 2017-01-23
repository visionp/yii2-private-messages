<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 06.06.2015
 * Time: 17:19
 */

namespace vision\messages\assets;


use vision\messages\components\MyMessages;
use yii\web\View;

class PrivateMessPoolingAsset extends BaseMessageAssets {

    public $js = [
        'js/private_mess_pooling.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];


    /**
     * Registers the CSS and JS files with the given view.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $nameController = MyMessages::getMessageComponent()->nameController;
        $base_script = "var baseUrlPrivateMessage ='{$nameController}';";
        $view->registerJs($base_script, $view::POS_BEGIN);

        return parent::registerAssetFiles($view);
    }
} 
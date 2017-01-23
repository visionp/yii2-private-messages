<?php

namespace vision\messages\widgets;


use vision\messages\assets\MessageAssets;
use \yii\base\Widget;
use yii\helpers\Html;


class PrivateMessageWidget extends Widget {

    public $buttonName = "Отправить";
    protected $html;
    protected $uniq_id;


    public function init() {
        parent::init();
        $this->uniq_id = $this->createId();
    }


    public function run(){
        $this->assetJS();
        $this->addJs();
        $this->html = '<div id="' . $this->uniq_id . '" class="elastic">';
        $this->html .= $this->getListUsers();
        $this->html .= $this->getBoxMessages();
        $this->html .= $this->getFormInput();
        $this->html .= '</div>';
        return $this->html;
    }

    protected function assetJS() {
        MessageAssets::register($this->view);
    }


    protected function getListUsers() {
        $users = \Yii::$app->mymessages->getAllUsers();
        $html = '<ul class="list_users">';
        foreach($users as $usr) {
            $html .= '<li class="contact" data-user="' . $usr['id'] . '">';
            $html .= $usr[\Yii::$app->mymessages->attributeNameUser] . " ";
            if($usr['cnt_mess']){
                $html .= "<span>(" .$usr['cnt_mess'] . ")</span>";
            }
            $html .= "</li>";
        }
        $html .= '</ul>';
        return $html;
    }


    protected function getBoxMessages() {
        $html = '';
        $html .= '<div class="message-container"></div>';
        return $html;
    }


    protected function getFormInput() {
        $html = '<form action="#" class="message-form" method="POST">';
        $html .= '<input type="text" name="input_message">';
        $html .= '<input type="hidden" name="message_id_user" value="">';
        $html .= '<button type="submit">' . $this->buttonName . '</button>';
        $html .= '</form>';
        return $html;
    }


    protected function addJs() {
        $view = $this->getView();
        $var_name = 'mess_' . $this->uniq_id;
        $script = 'var ' . $var_name . ' = new visiPrivateMessages("#'. $this->uniq_id .'");';
        $script .= "$var_name.getAllMessages();";
        $view->registerJs($script, $view::POS_READY);
    }


    protected function createId() {
        $length = 5;
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return 'messag_'.$string;
    }
}

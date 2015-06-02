<?php

namespace vision\messages\widgets;


use vision\messages\assets\MessageAssets;
use \yii\base\Widget;


class PrivateMessage extends Widget {

    public $buttonName = "Отправить";
    protected $html;

    public function run(){
        MessageAssets::register($this->view);
        $this->html = '';
        $this->html .= $this->getListUsers();
        $this->html .= $this->getBoxMessages();
        $this->html .= $this->getFormInput();
        return $this->html;
    }

    protected function getListUsers() {
        $users = \Yii::$app->mymessages->getAllUsers();
        $html = '<ul class="list_users">';
        foreach($users as $usr) {
            $html .= '<li data-user="' . $usr['id'] . '">';
            $html .= $usr[\Yii::$app->mymessages->attributeNameUser] . " ";
            $html .= "<span>" .$usr['cnt_mess'] . "</span>";
            $html .= "</li>";
        }
        $html .= '</ul>';
        return $html;
    }


    protected function getBoxMessages() {
        $html = '';
        $html .= '<div class="message-container" id="message-container"></div>';
        return $html;
    }


    protected function getFormInput() {
        $html = '<form action="#" method="POST">';
        $html .= '<input type="text" name="input_message">';
        $html .= '<input type="hidden" name="message_id_user">';
        $html .= '<button type="submit">' . $this->buttonName . '</button>';
        $html .= '</form>';
        return $html;

    }
}

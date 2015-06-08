<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 04.06.2015
 * Time: 12:57
 */

namespace vision\messages\widgets;

use vision\messages\assets\MessageKushalpandyaAssets;


class PrivateMessageKushalpandyaWidget extends PrivateMessageWidget {

    public function run(){
        $this->assetJS();
        $this->addJs();
        $this->html = '<div id="' . $this->uniq_id . '" class="main-message-container">';
        $this->html .= '<div class="message-north">';
        $this->html .= $this->getListUsers();
        $this->html .= $this->getBoxMessages();
        $this->html .= '</div>';
        $this->html .= $this->getFormInput();
        $this->html .= '</div>';
        return $this->html;
    }


    protected function getListUsers() {
        $users = \Yii::$app->mymessages->getAllUsers();
        $html = '<ul class="list_users message-user-list">';
        foreach($users as $usr) {
            $html .= '<li class="contact" data-user="' . $usr['id'] . '"><a href="#">';
            //$html .= '<span class="user-img"></span>';
            $html .= '<span class="user-title">' . $usr[\Yii::$app->mymessages->attributeNameUser];
            $html .= ' <span id="cnt">';
            if($usr['cnt_mess']){
                $html .=  $usr['cnt_mess'];
            }
            $html .= "</span></span></a></li>";
        }
        $html .= '</ul>';
        return $html;
    }


    protected function getBoxMessages() {
        $html = '';
        $html .= '<div class="message-container message-thread">';
        $html .= '</div>';
        return $html;
    }


    protected function getFormInput() {
        $html = '<div class="message-south"><form action="#" class="message-form" method="POST">';
        $html .= '<textarea disabled="true" name="input_message"></textarea>';
        $html .= '<input type="hidden" name="message_id_user" value="">';
        $html .= '<button type="submit">' . $this->buttonName . '</button>';
        $html .= \Yii::$app->mymessages->enableEmail ? '<span class="send_mail"><input class="checkbox" id="send_mail" type="checkbox" name="send_mail" value="1"><label for="send_mail">Отправить также на email</label></span>' : '';
        $html .= '</form></div>';
        return $html;
    }


    protected function addJs() {
        $var_name = 'mess_' . $this->uniq_id;
        $script = 'var baseUrlPrivateMessage = ' . \Yii::$app->mymessages->nameController . ';';
        $script .= 'var ' . $var_name . ' = new visiPrivateMessages("#'. $this->uniq_id .'");';
        $script .= "$var_name.getAllMessages();";
        $view = $this->getView();
        $view->registerJs($script, $view::POS_READY);
    }


    protected function assetJS() {
        MessageKushalpandyaAssets::register($this->view);
    }

} 
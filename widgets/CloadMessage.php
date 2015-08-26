<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 26.08.2015
 * Time: 10:43
 */

namespace vision\messages\widgets;

use vision\messages\assets\CloadAsset;


class CloadMessage extends PrivateMessageWidget {

    public function run(){
        $this->assetJS();
        $this->html = '<div id="' . $this->uniq_id . '" class="main-message-container message-layout clearfix">';
        $this->html .= '<div class="message-north right-column message">';
        $this->html .= $this->getHead();
        $this->html .= $this->getBoxMessages();
        $this->html .=  $this->getListUsers();
        $this->html .= '</div></div>';
        return $this->html;
    }


    protected function getListUsers() {
        $users = \Yii::$app->mymessages->getAllUsers();
        $html = '<div class="list_users message-user-list friends-message-layout">';

        foreach($users as $usr) {
            $username = trim($usr[\Yii::$app->mymessages->attributeNameUser]);
            $names = explode(' ', $username);
            $html .= '<div class="contact friends" data-name="' . $username . '" data-user="' . $usr['id'] . '">';

            $html .= '<button class="delete-friend">&times;</button>';
            $html .= '<div class="friends-block"><img src="/img/thirg-place-ava.png"></div>';
            $html .= '<div class="friends-name">';


            $html .= array_reduce($names, function($pre, $val) {
                return $pre . "<div>$val</div>";
            });
            //if($usr['cnt_mess']){}
            $html .= "</div></div>";
        }

        $html .= '</div>';
        return $html;
    }


    protected function getBoxMessages() {
        $html = '';
        $html .= '<div class="message-thread message-wrapper clearfix">';
        $html .= '<div id="scrollbarY">';
        $html .= '<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>';
        $html .= '<div class="viewport"><div class="overview message-container">';
        $html .= '</div></div></div>';
        $html .= $this->getFormInput();
        $html .= '</div>';
        return $html;
    }


    protected function getFormInput() {
        $html = '<div class="message-south create-message"><form action="#" class="message-form" method="POST">';
        $html .= '<textarea class="textarea-layout" disabled="true" name="input_message"></textarea>';
        $html .= '<input type="hidden" name="message_id_user" value="">';
        $html .= '<button class="send-message" type="submit">' . $this->buttonName . '</button>';
        $html .= '</form></div>';
        return $html;
    }

    protected function getHead() {
        $html = '<div class="right-layout">';
        $html .= '<div class="separate"></div>';
        $html .= '<div class="layout-background clearfix">';
        $html .= '<div class="dialogue">ДИАЛОГИ</div>';
        $html .= '<div class="message-layout-search">';
        $html .= '<form><div class="search">';
        $html .= '<input type="search">';
        $html .= '<input type="submit" value="">';
        $html .= '</div></form></div></div></div>';
        return $html;
    }


    protected function assetJS() {
        CloadAsset::register($this->view);
        $this->addJs();
    }

}
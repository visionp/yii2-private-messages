<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 22.05.2015
 * Time: 12:50
 */

namespace vision\messages\actions;

use Yii;
use yii\base\Action;
use vision\messages\exceptions\EceptionMessages;



class MessageApiAction extends Action {

    public function run() {

    }


    protected function updateMessage() {
        $id_last_message = \Yii::$app->request->post('id_last_message');
        while(true){
            sleep(5);
        }

    }


    protected function sendJson($data) {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $data;
        $response->send();
        die();
    }

} 
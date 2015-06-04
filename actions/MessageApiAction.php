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

    protected $from_id;
    protected $last_id;
    protected $action;

    public function run() {
        $this->action = \Yii::$app->request->get('action', false);
        if(!$this->action) {
            $this->sendJson(['status' => false, 'message' => 'Action is null.']);
        }
        if(!method_exists($this, $this->action)) {
            $this->sendJson(['status' => false, 'message' => 'Action not exist.']);
        }
        try{
            $data['data'] = call_user_func([$this, $this->action]);
        }catch (EceptionMessages $e) {
            $this->sendJson(['status' => false, 'message' => $e->getMessage()]);
        }
        $data['status'] = true;
        $this->sendJson($data);
    }


    protected function getMessage() {
        $from_id = \Yii::$app->request->get('from_id', false);
        return \Yii::$app->mymessages->getAllMessages(\Yii::$app->user->identity->id, $from_id);
    }


    protected function getNewMessage() {
        $from_id = \Yii::$app->request->get('from_id', false);
        return \Yii::$app->mymessages->getNewMessages(\Yii::$app->user->identity->id, $from_id);
    }


    protected function updateMessage() {
        $id_last_message = \Yii::$app->request->post('id_last_message');
        while(true){
            sleep(5);
        }

    }


    protected function sendMessage() {
        $whom_id = \Yii::$app->request->get('whom_id', false);
        $message = \Yii::$app->request->get('text', false);
        if(!$whom_id && !$message) {
            $this->sendJson(['status' => false, 'message' => 'No data.']);
        }

        return \Yii::$app->mymessages->sendMessage($whom_id, $message);
    }


    protected function sendJson($data) {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $data;
        $response->send();
        die();
    }

}
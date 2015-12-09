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
use vision\messages\exceptions\ExceptionMessages;



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
        }catch (ExceptionMessages $e) {
            $this->sendJson(['status' => false, 'message' => $e->getMessage()]);
        }
        $data['status'] = true;
        $this->sendJson($data);
    }


    protected function getMessage() {
        $from_id = \Yii::$app->request->get('from_id', false);
        $data['messages'] = \Yii::$app->mymessages->getAllMessages(\Yii::$app->user->identity->id, $from_id);
        $data['from_id'] = $from_id;
        return $data;
    }


    protected function getNewMessage() {
        $from_id = \Yii::$app->request->get('from_id', false);
        $data['messages'] = \Yii::$app->mymessages->getNewMessages(\Yii::$app->user->identity->id, $from_id);
        $data['from_id'] = $from_id;
        return $data;
    }


    protected function sendMessage() {
        $whom_id = \Yii::$app->request->get('whom_id', false);
        $isEmail = \Yii::$app->request->get('isEmail', false);
        $message = \Yii::$app->request->get('text', false);

        if(!$whom_id && !$message) {
            $this->sendJson(['status' => false, 'message' => 'No data.']);
        }

        \Yii::$app->mymessages->sendMessage($whom_id, $message, $isEmail == 'true');
        $data['messages'] = \Yii::$app->mymessages->getNewMessages(\Yii::$app->user->identity->id, $whom_id);
        $data['from_id'] = $whom_id;
        return $data;
    }


    protected function deleteMessage() {
        $id_message = \Yii::$app->request->get('id_message', false);
        $result = \Yii::$app->mymessages->deleteMessage($id_message);
        return $result['id'];
    }


    protected function clearMessage() {
        $return = false;
        $user_id = \Yii::$app->request->get('user_id', false);
        if($user_id) {
            $return = \Yii::$app->mymessages->clearMessages($user_id);
        }
        return $return;
    }


    protected function pooling() {
        $last_id = \Yii::$app->request->get('last_id', false);
        $data    = \Yii::$app->mymessages->checkMessage($last_id);
        $this->sendJson($data);
        /*
        if(!$last_id) {
            sleep(2);
            $this->sendJson(['status' => false, 'message' => 'No last id, info:' . print_r($last_id, 1)]);
        }
        $time_cancel = (int) ini_get('max_execution_time') - 1;
        $duration = $time_cancel < 30 ? $time_cancel : 25;
        $endTime = time() + $duration; $i=0;
        while(time() < $endTime){
            $i++;
            if($i > 4) {
                $this->sendJson('dddd');
            }
            $data = \Yii::$app->mymessages->checkMessage($last_id);
            if (count($data) > 0) {
                $this->sendJson($data);
            }
            sleep(7);
        }
        */
    }


    protected function sendJson($data) {
        $response = \Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $data;
        $response->send();
        die();
    }

}
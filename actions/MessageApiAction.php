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
use vision\messages\components\MyMessages;
use vision\messages\exceptions\ExceptionMessages;


class MessageApiAction extends Action {


    protected $from_id;
    protected $last_id;
    protected $action;
    protected $whom_id;
    protected $isEmail;
    protected $message;


    /**
     *
     */
    public function init()
    {
        $request = \Yii::$app->request;
        $this->action  = $request->get('action', 'undefined');
        $this->whom_id = $request->get('whom_id', false);
        $this->isEmail = $request->get('isEmail', false);
        $this->message = $request->get('text', false);
        $this->from_id = $request->get('from_id', false);
    }


    /**
     * 
     */
    public function run() {
        $response = ['status' => false];
        try {
            if(method_exists($this, $this->action)) {
                $response['data'] = call_user_func([$this, $this->action]);
                $response['status'] = true;
            } else {
                $response = ['message' => 'Action is not exist.'];
            }

        } catch (ExceptionMessages $e) {
            $response = [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
        return $this->toJson($response);
    }


    /**
     * @return mixed
     */
    protected function getMessage() {
        $data['messages'] = $this
            ->getMessageComponent()
            ->getAllMessages(\Yii::$app->user->getId(), $this->from_id);
        $data['from_id'] = $this->from_id;
        return $data;
    }


    /**
     * @return mixed
     */
    protected function getNewMessage() {
        $data['messages'] = $this
            ->getMessageComponent()
            ->getNewMessages($this->getMyId(), $this->from_id);
        $data['from_id'] = $this->from_id;
        return $data;
    }


    /**
     *
     */
    protected function sendMessage() {
        if(!$this->whom_id && !$this->message) {
            return ['status' => false, 'message' => 'No data.'];
        }

        $this->getMessageComponent()->sendMessage($this->whom_id, $this->message, $this->isEmail == 'true');
        $data['messages'] = $this
            ->getMessageComponent()
            ->getNewMessages($this->getMyId(), $this->whom_id);

        $data['from_id'] = $this->whom_id;
        return $data;
    }


    /**
     * @return mixed
     */
    protected function deleteMessage() {
        $id_message = \Yii::$app->request->get('id_message', false);
        $result = [];
        if($id_message){
            $result = $this->getMessageComponent()->deleteMessage($id_message);
            return $result['id'];
        }
        return $result['id'];
    }


    /**
     * @return bool
     */
    protected function clearMessage() {
        $return = false;
        $user_id = \Yii::$app->request->get('user_id', false);
        if($user_id) {
            $return = \Yii::$app->mymessages->clearMessages($user_id);
        }
        return $return;
    }


    /**
     * Pooling new messages
     */
    protected function pooling() {
        $last_id = \Yii::$app->request->get('last_id', false);
        $data    = \Yii::$app->mymessages->checkMessage($last_id);
        return $data;
    }


    /**
     * @param $data
     * @return \yii\console\Response|\yii\web\Response
     */
    protected function toJson($data) {
        $response = \Yii::$app->response;
        $response->format = $response::FORMAT_JSON;
        $response->data = $data;
        return $response;
    }


    /**
     * @return int|string
     */
    protected function getMyId()
    {
        return \Yii::$app->user->getId();
    }


    /**
     * @return MyMessages
     */
    protected function getMessageComponent()
    {
        return \Yii::$app->mymessages;
    }

}
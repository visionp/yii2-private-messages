<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 21.05.2015
 * Time: 17:26
 */

namespace vision\messages\components;

use Yii;
use yii\base\Component;
use vision\messages\models\Messages;
use vision\messages\exceptions\EceptionMessages;
use vision\messages\events\MessageEvent;



class MyMessages extends Component {

    const EVENT_SEND   = 'sendMessage';
    const EVENT_STATUS = 'changeStatus';

    /** @var ActiveRecord */
    public $modelUser;

    /** @var string */
    public $attributeNameUser = 'username';

    /** @var string */
    public $nameController;

    /** @var boolean */
    public $enableEmail = false;

    /** @var function */
    public $getEmail = null;

    /** @var array */
    public $templateEmail = [];

    /** @var string */
    public $subject = 'Private message';

    /** @var string */
    protected $userTableName;


    public function init(){
        if(!$this->modelUser) {
            $this->modelUser = \Yii::$app->user->identityClass;
        }
        $this->userTableName = call_user_func(Array($this->modelUser, 'tableName'));
    }


    public function sendMessage($whom_id, $message, $sendEmail = false) {
        $result = null;
        if(is_array($whom_id)) {
            $result = $this->_sendMessages($whom_id, $message, $sendEmail);
        } else {
            $result = $this->_sendMessage($whom_id, $message, $sendEmail);
        }
        return $result;
    }

    /**
     * Method to getMyMessages.
     *
     * @throws EceptionMessages
     * @return array
     */
    public function getMyMessages() {
        return $this->getMessages(\Yii::$app->user->id);
    }


    public function checkMessage(){
        $result = $this->getAllUsers();
        return array_filter($result, function($arr) { return $arr['cnt_mess'] > 0 ;});
    }

    /**
     * Method to getAllMessages.
     *
     * @param $whom_id
     * @param $from_id
     *
     * @throws EceptionMessages
     * @return array
     */
    public function getAllMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id);
    }


    public function getNewMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id, 1);
    }


    /**
     * Method to sendMessage.
     *
     * @param $whom_id
     * @param $message
     *
     * @return array
     */
    protected function _sendMessage($whom_id, $message, $send_email = false) {
        $model = new Messages();
        $model->from_id = \Yii::$app->user->id;
        $model->whom_id = $whom_id;
        $model->message = $message;
        if($this->enableEmail && $send_email) {
            $this->_sendEmail($whom_id, $message);
        }

        return $this->saveData($model, self::EVENT_SEND);
    }

    /**
     * Method to _sendEmail.
     *
     * @param $whom_id
     * @param $message
     *
     * @throws EceptionMessages
     *
     * @return boolean, array
     */
    protected function _sendEmail($whom_id, $message) {
        if(!is_callable($this->getEmail)) {
            throw new EceptionMessages('Email not send. Set in config "getEmail" to callable func.');
        }
        if(!isset($this->templateEmail['html'], $this->templateEmail['text'])) {
            throw new EceptionMessages('Email not send. Set in config "templateEmail".');
        }
        $user = $this->getUser($whom_id);
        if($user) {
            $email = call_user_func($this->getEmail, $user);
        }
        if(!empty($email)) {
            return \Yii::$app->mailer
                ->compose(['html' => $this->templateEmail['html'], 'text' => $this->templateEmail['text']], ['message' => $message])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' private Message'])
                ->setTo($email)
                ->setSubject($this->subject)
                ->send();
        }
        return false;
    }

    /*
    public $getEmail = null;
    public $templateEmail = [];
     */


    /**
     * Method to many messages.
     *
     * @param $whom_ids
     * @param $message
     *
     * @return array
     */
    protected function _sendMessages(Array $whom_ids, $message, $sendEmail = false) {
        $result = Array();
        foreach($whom_ids as $id) {
            $result[] = $this->sendMessage($id, $message, $sendEmail);
        }
        return $result;
    }


    /**
     * Method to getNewMessages.
     *
     * @param $whom_id
     * @param $from_id
     *
     * @throws EceptionMessages
     * @return array
     */
    protected function _getNewMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id, Messages::STATUS_NEW);
    }


    /**
     * Method to changeStatusMessage.
     *
     * @param $id
     * @param $status
     *
     * @throws EceptionMessages
     * @return array
     */
    protected function changeStatusMessage($id, $status, $is_delete = false) {
        $model = Messages::findOne($id);
        $status_name = 'status';
        $current_user_id = \Yii::$app->user->identity->id;
        if(!$model) {
            throw new EceptionMessages('Message not found.');
        }
        if($model->from_id != $current_user_id && $model->whom_id != $current_user_id) {
            throw new EceptionMessages('Message not found for this user.');
        }
        if($is_delete) {
            switch ($current_user_id) {
                case $model->from_id:
                    $status_name = 'is_delete_from';
                    break;
                case $model->whom_id:
                    $status_name = 'is_delete_whom';
                    break;
            }
        }
        $model->$status_name = $status;
        return $this->saveData($model, self::EVENT_STATUS);
    }


    /**
     * Method to deleteMessage.
     *
     * @param $id
     *
     * @throws EceptionMessages
     * @return Messages
     */
    public function deleteMessage($id) {
        return $this->changeStatusMessage($id, 1, 1);
    }


    /**
     * Method to getUser.
     *
     * @throws EceptionMessages
     * @return array
     */
    public function getUser($id) {
        $model = new $this->modelUser();
        $user = $model::findOne($id);
        return $user;
    }

    /**
     * Method to getAllUsers.
     *
     * @throws EceptionMessages
     * @return array
     */
    public function getAllUsers() {
        $table_name = Messages::tableName();

        $sql = "select usr.id, usr.$this->attributeNameUser as username, msg.cnt as cnt_mess ";
        $sql .= "from $this->userTableName as usr ";
        $sql .= "left join ";
        $sql .= "(select from_id, count(id) as cnt from $table_name where status = 1 and whom_id = :user_id GROUP by from_id) as msg ON usr.id = msg.from_id ";
        $sql .= " where usr.id != :user_id ";

        $connection = \Yii::$app->db;
        $model = $connection->createCommand($sql);
        $model->bindValue(':user_id', \Yii::$app->user->identity->id);

        $users = $model->queryAll();

        return $users;
    }


    /**
     * Method to saveData.
     *
     * @param $model
     * @param $name_event
     *
     * @throws EceptionMessages
     * @return array
     */
    protected function saveData($model, $name_event = null) {
        if(!$model->save()) {
            $mess = $model->hasErrors() ? implode(', ', $model->getErrors()) : 'Not saved. ' . $name_event;
            throw new EceptionMessages($mess);
        } else {
            if($name_event) {
                $event = new MessageEvent;
                $event->message = $model;
                $this->trigger(self::EVENT_SEND, $event);
            }
        }
        return $model->toArray();
    }


    /**
     * Method to getMessages.
     *
     * @param $whom_id
     * @param $from_id
     * @param $type
     *
     * @throws EceptionMessages
     * @return array
     */
    protected function getMessages($whom_id, $from_id = null, $type = null, $last_id = null) {
        $table_name = Messages::tableName();
        $my_id = \Yii::$app->user->getId();

        $query = new \yii\db\Query();
        $query
            ->select(['FROM_UNIXTIME(msg.created_at, "%d-%m-%Y %H:%i:%S") as created_at', 'msg.id', 'msg.status', 'msg.message', "usr1.id as from_id", "usr1.$this->attributeNameUser as from_name", "usr2.id as whom_id", "usr2.$this->attributeNameUser as whom_name"])
            ->from("$table_name as msg")
            ->leftJoin("$this->userTableName as usr1", 'usr1.id = msg.from_id')
            ->leftJoin("$this->userTableName as usr2", 'usr2.id = msg.whom_id');


        if($from_id) {
            $query
                ->where(['msg.whom_id' => $whom_id, 'msg.from_id' => $from_id])
                ->orWhere(['msg.from_id' => $whom_id, 'msg.whom_id' => $from_id]);
        } else {
            $query->where(['msg.whom_id' => $whom_id]);
        }


        //if not set type
        //send all message where no delete
        if($type) {
            $query->andWhere(['=', 'msg.status', $type]);
        } else {
            $query->andWhere('((msg.is_delete_from != 1 AND from_id = :my_id) OR (msg.is_delete_whom != 1 AND whom_id = :my_id) ) ', [
                ':my_id' => $my_id,
            ]);
        }

        if($last_id){
            $query->andWhere(['>', 'msg.id', $last_id]);
        }

        $return = $query->orderBy('msg.id')->all();
        $ids = Array();
        foreach($return as $m) {
            if($m['whom_id'] == $my_id) {
                $ids[] = $m['id'];
            }
        }

        //change status to is_read
        if(count($ids) > 0) {
            Messages::updateAll(['status' => Messages::STATUS_READ], ['in', 'id', $ids]);
        }

        $user_id = \Yii::$app->user->getId();
        return array_map(function ($r) use ($user_id) { $r['i_am_sender'] = $r['from_id'] == $user_id; return $r;}, $return);
    }

}
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
    protected $userTableName;


    public function init(){
        if(!$this->modelUser) {
            $this->modelUser = \Yii::$app->user->identityClass;
        }
        $this->userTableName = call_user_func(Array($this->modelUser, 'tableName'));
    }


    /**
     * Method to sendMessage.
     *
     * @param $whom_id
     * @param $message
     *
     * @throws EceptionMessages
     * @return array
     */
    public function sendMessage($whom_id, $message) {
        $model = new Messages();
        $model->from_id = \Yii::$app->user->id;
        $model->whom_id = $whom_id;
        $model->message = $message;
        return $this->saveData($model, self::EVENT_SEND);
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


    /**
     * Method to getNewMessages.
     *
     * @param $whom_id
     * @param $from_id
     *
     * @throws EceptionMessages
     * @return array
     */
    public function getNewMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id, Messages::STATUS_NEW);
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


    /**
     * Method to changeStatusMessage.
     *
     * @param $id
     * @param $status
     *
     * @throws EceptionMessages
     * @return array
     */
    public function changeStatusMessage($id, $status) {
        $model = Messages::findOne($id);
        if(!$model) {
            throw new EceptionMessages('Message not found.');
        }
        $model->status = $status;
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
        return $this->changeStatusMessage($id, Messages::STATUS_DELETE);
    }


    /**
     * Method to deleteMessage.
     *
     * @throws EceptionMessages
     * @return Messages
     */
    public function getAllUsers() {
        $model = new $this->modelUser();
        $users = $model::find()->all();
        return $users;
    }


    /**
     * Method to deleteMessage.
     *
     * @param $id
     *
     * @throws EceptionMessages
     * @return ActiveRecord
     */
    public function getUser($id) {
        $model = new $this->modelUser();
        $user = $model::findOne($id);
        return $user;
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
    protected function getMessages($whom_id, $from_id = null, $type = null) {
        $table_name = Messages::tableName();

        $query = new \yii\db\Query();
        $query
            ->select(['msg.*', "usr1.$this->attributeNameUser as from_name", "usr2.$this->attributeNameUser as whom_name"])
            ->from("$table_name as msg")
            ->leftJoin("$this->userTableName as usr1", 'usr1.id = msg.from_id')
            ->leftJoin("$this->userTableName as usr2", 'usr2.id = msg.whom_id')
            ->where(['msg.whom_id' => $whom_id]);

        if($from_id) {
            $query->andWhere(['msg.from_id' => $from_id]);
        } else {
            $query->orWhere(['msg.from_id' => $whom_id]);
        }
        if($type) {
            $query->andWhere(['=', 'msg.status', $type]);
        }else {
            $query->andWhere(['!=', 'msg.status', Messages::STATUS_DELETE]);
        }

        $return = Array();
        $ids = Array();
        foreach($query->all() as $m) {
            $return[$m['from_name']][] = $m;
            $ids[] = $m['id'];
        }

        //change status to is_read
        if(count($ids) > 0 && $whom_id == \Yii::$app->user->id) {
            Messages::updateAll(['status' => Messages::STATUS_READ], ['in', 'id', $ids]);
        }

        return $return;
    }

} 
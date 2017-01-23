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
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use vision\messages\models\Messages;
use vision\messages\exceptions\ExceptionMessages;
use vision\messages\events\MessageEvent;
use yii\base\InvalidConfigException;



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

    /** @var boolean */
    public $admins = null;

    /** @var boolean */
    public $isSystem = false;

    /** @var callable */
    public $getEmail = null;

    /** @var callable */
    public $getLogo = null;

    /** @var array */
    public $templateEmail = [];

    /** @var string */
    public $subject = 'Private message';

    /** @var string */
    protected $userTableName;

    /** @var array */
    protected $adminIds = null;


    public static function getMessageComponent()
    {
        return \Yii::$app->mymessages;
    }


    public function init(){
        if(!$this->modelUser) {
            $this->modelUser = \Yii::$app->user->identityClass;
        }
        $this->userTableName = call_user_func([$this->modelUser, 'tableName']);
    }


    /**
     * @param $whom_id
     * @param $message
     * @param bool $sendEmail
     * @return array|null
     */
    public function sendMessage($whom_id, $message, $sendEmail = false) {
        $result = null;

        if($this->admins && !in_array(\Yii::$app->user->id, $this->getAdminIds())) {
            if(!in_array($whom_id, $this->getAdminIds())){
                $whom_id = $this->getAdminIds();
            }
        }

        if(!is_numeric($whom_id) && is_string($whom_id)) {
            $ids = $this->getUsersByRoles($whom_id);
            return $this->sendMessage($ids, $message, $sendEmail);
        } elseif (is_array($whom_id)) {
            $result = $this->_sendMessages($whom_id, $message, $sendEmail);
        } else {
            $result = $this->_sendMessage($whom_id, $message, $sendEmail);
        }
        return $result;
    }


    public function systemSend ($whom_id, $message, $sendEmail = false) {
        $this->isSystem = true;
        $this->sendMessage($whom_id, $message, $sendEmail);
        $this->isSystem = false;
    }


    public function getSystemListNew() {
        return $this->_getSystemMess(\Yii::$app->user->id, true);
    }


    public function getSystemMess() {
        $id = \Yii::$app->user->id;
        $message = $this->_getSystemMess($id);
        if($message){
            $this->_toReadSystemMessage($id);
        }
        return $message;
    }


    /**
     * @param $user_id
     * @param bool $only_new
     * @return array
     */
    protected function _getSystemMess($user_id, $only_new = false) {
        $messages = (new Query())
            ->select([
                'usr.id',
                'usr' => $this->attributeNameUser,
                'usr.email',
                'm.message',
                'm.created_at'
            ])
            ->from(['usr' => $this->userTableName])
            ->leftJoin(['m' => Messages::tableName()], 'usr.id = m.whom_id')
            ->where([
                'm.from_id' => null,
                'usr.id'=> $user_id
            ]);

        if($only_new) {
            $messages->andWhere(['m.status' => 1]);
        }


        return array_map(function ($item){
            $item['created_at'] = \DateTime::createFromFormat('U', $item['created_at'])->format('d-m-Y H-i-s');
            return $item;
        },
            $messages->all()
        );
    }


    /**
     * @param $user_id
     * @return int
     */
    protected function _toReadSystemMessage($user_id) {
        $count = Messages::updateAll(
            ['status' => Messages::STATUS_READ],
            [
                'whom_id' => $user_id,
                'from_id' => null
            ]
        );
        return $count;
    }


    /**
     * Method to getMyMessages.
     *
     * @throws ExceptionMessages
     * @return array
     */
    public function getMyMessages() {
        $id = $this->getIdCurrentUser();
        return $this->getMessages($id);
    }


    /**
     * @param bool $last_id
     * @return array
     */
    public function checkMessage($last_id = false){
        $result = $this->getAllUsers();
        return array_filter($result, function($arr) { return $arr['cnt_mess'] > 0 ;});
    }


    /**
     * Method to getAllMessages.
     *
     * @param $whom_id
     * @param $from_id
     *
     * @throws ExceptionMessages
     * @return array
     */
    public function getAllMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id);
    }


    /**
     * @param $whom_id
     * @param $from_id
     * @return array
     */
    public function getNewMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id, 1);
    }


    /**
     * @param $userId
     * @return bool|string
     */
    public function createLogo($userId) {
        $img = '<img src="">';
        if(is_callable($this->getLogo)){
            $pathImage = call_user_func($this->getLogo, $userId);
            if($pathImage) {
                $img = Html::img($pathImage, ['width' => 40]);
            }
        } else {
            return false;
        }
        return $img;
    }


    /**
     * @param $from_id
     * @return int
     */
    public function clearMessages($from_id) {
        $params = [':whom_user_id' => $from_id, ':from_user_id' => \Yii::$app->user->id];
        $result = $this->getDb()->createCommand()
            ->update(Messages::tableName(), ['is_delete_from' => 1],
                'from_id = :from_user_id AND whom_id = :whom_user_id',
                $params)
            ->execute();

        if($result) {
            $result += $this->getDb()->createCommand()
                ->update(Messages::tableName(), ['is_delete_whom' => 1],
                    'from_id = :whom_user_id AND whom_id = :from_user_id',
                    $params)
                ->execute();
        }

        return $result;
    }


    /**
     * Method to sendMessage.
     *
     * @param $whom_id
     * @param $message
     * @param bool $send_email
     * @return array
     */
    protected function _sendMessage($whom_id, $message, $send_email = false) {
        $model = new Messages();
        $model->from_id = $this->getIdCurrentUser();
        $model->whom_id = $whom_id;
        $model->message = Html::encode($message);
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
     * @throws ExceptionMessages
     *
     * @return boolean, array
     */
    protected function _sendEmail($whom_id, $message) {
        if(!is_callable($this->getEmail)) {
            throw new ExceptionMessages('Email not send. Set in config "getEmail" to callable func.');
        }
        if(!isset($this->templateEmail['html'], $this->templateEmail['text'])) {
            throw new ExceptionMessages('Email not send. Set in config "templateEmail".');
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


    /**
     * Method to many messages.
     *
     * @param array $whom_ids
     * @param $message
     * @param bool $sendEmail
     * @return array
     */
    protected function _sendMessages(array $whom_ids, $message, $sendEmail = false) {
        $result = [];
        foreach($whom_ids as $id) {
            $result[] = $this->_sendMessage($id, $message, $sendEmail);
        }
        return $result;
    }


    /**
     * Method to getNewMessages.
     *
     * @param $whom_id
     * @param $from_id
     *
     * @throws ExceptionMessages
     * @return array
     */
    protected function _getNewMessages($whom_id, $from_id) {
        return $this->getMessages($whom_id, $from_id, Messages::STATUS_NEW);
    }


    /**
     * @param $id
     * @param $status
     * @param bool $is_delete
     * @return array
     * @throws ExceptionMessages
     */
    protected function changeStatusMessage($id, $status, $is_delete = false) {
        $model = Messages::findOne($id);

        if(!$model) {
            throw new ExceptionMessages('Message not found.');
        }
        $status_name = 'status';
        $current_user_id = $this->getIdCurrentUser();

        if($model->from_id != $current_user_id && $model->whom_id != $current_user_id) {
            throw new ExceptionMessages('Message not found for this user.');
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
     * @throws ExceptionMessages
     * @return Messages
     */
    public function deleteMessage($id) {
        return $this->changeStatusMessage($id, 1, 1);
    }


    /**
     * Method to getUser.
     *
     * @param $id
     * @return ActiveRecord
     */
    public function getUser($id) {
        $className = $this->modelUser;
        return $className::findOne($id);
    }


    /**
     * Method to getAllUsers.
     *
     * @throws ExceptionMessages
     * @return array
     */
    public function getAllUsers() {
        $table_name = Messages::tableName();

        $subQuery = (new Query())
            ->select([
                'from_id',
                'cnt' => new Expression('count(id)')
            ])
            ->from($table_name)
            ->where([
                'status' => 1,
                'whom_id' => $this->getIdCurrentUser()
            ])
            ->groupBy([
                'from_id'
            ]);

        $query = (new Query())
            ->select([
                'usr.id',
                $this->attributeNameUser => 'usr.' . $this->attributeNameUser,
                'cnt_mess' => 'msg.cnt'
            ])
            ->from(['usr' => $this->userTableName])
            ->leftJoin(['msg' => $subQuery], 'usr.id = msg.from_id')
            ->where([
                '!=', 'usr.id', $this->getIdCurrentUser()
            ])
            ->orderBy([
                'msg.cnt' => SORT_DESC,
                'usr.' . $this->attributeNameUser => SORT_DESC
            ]);

        if($this->admins && !in_array(\Yii::$app->user->id, $this->getAdminIds())) {
            $query->andWhere([
                'in', 'usr.id', $this->adminIds
            ]);
        }

        return $query->all();
    }


    /**
     * Method to saveData.
     *
     * @param $model Messages
     * @param $name_event
     *
     * @throws ExceptionMessages
     * @return array
     */
    protected function saveData($model, $name_event = null) {
        if(!$model->save()) {
            $mess = $model->hasErrors() ? implode(', ', $model->getFirstErrors()) : 'Not saved. ' . $name_event;
            throw new ExceptionMessages($mess);
        } else {
            if($name_event) {
                $event = new MessageEvent();
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
     * @param $last_id
     * @param $type
     *
     * @throws ExceptionMessages
     * @return array
     */
    protected function getMessages($whom_id, $from_id = null, $type = null, $last_id = null) {
        $table_name = Messages::tableName();
        $my_id = $this->getIdCurrentUser();

        $query = (new Query())
            ->select([
                'msg.created_at',
                'msg.id',
                'msg.status',
                'msg.message',
                'from_id'   => 'usr1.id',
                'from_name' => "usr1.{$this->attributeNameUser}",
                'whom_id'   => 'usr2.id',
                'whom_name' => "usr2.{$this->attributeNameUser}"
            ])
            ->from(['msg' => $table_name])
            ->leftJoin(['usr1' => $this->userTableName], 'usr1.id = msg.from_id')
            ->leftJoin(['usr2' => $this->userTableName], 'usr2.id = msg.whom_id');


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
            /*
            $query->andWhere('((msg.is_delete_from != 1 AND from_id = :my_id) OR (msg.is_delete_whom != 1 AND whom_id = :my_id) ) ', [
                ':my_id' => $my_id,
            ]);
            */
        }

        $query->andWhere('((msg.is_delete_from != 1 AND from_id = :my_id) OR (msg.is_delete_whom != 1 AND whom_id = :my_id) ) ', [
            ':my_id' => $my_id,
        ]);

        if($last_id){
            $query->andWhere(['>', 'msg.id', $last_id]);
        }

        $return = $query->orderBy('msg.id')->all();
        $ids = [];
        foreach($return as $m) {
            if($m['whom_id'] == $my_id) {
                $ids[] = $m['id'];
            }
        }

        //change status to is_read
        if(count($ids) > 0) {
            Messages::updateAll(['status' => Messages::STATUS_READ], ['in', 'id', $ids]);
        }

        $user_id = $this->getIdCurrentUser();
        return array_map(function ($r) use ($user_id) {
            $r['i_am_sender'] = $r['from_id'] == $user_id;
            $r['created_at'] = \DateTime::createFromFormat('U', $r['created_at'])->format('d-m-Y H-i-s');
            return $r;
        },
            $return
        );
    }


    /**
     * @param $role
     * @return array
     */
    protected function getUsersByRoles($role) {
        $query = (new Query())
            ->select([
                'usr.id'
            ])
            ->from([ 'usr' => $this->userTableName])
            ->leftJoin(['ath' => 'auth_assignment'], 'usr.id = ath.user_id')
            ->where(['ath.item_name' => $role]);
        return ArrayHelper::getColumn($query->all(), 'id');
    }


    /**
     * @return int|null|string
     */
    protected function getIdCurrentUser() {
        return \Yii::$app->user->isGuest || $this->isSystem ? null : \Yii::$app->user->id;
    }


    /**
     * Method to getAdminIds.
     *
     * @return array|null
     * @throws ExceptionMessages
     */
    protected function getAdminIds() {
        if (!empty($this->adminIds)){
            return $this->adminIds;
        }
        if (empty($this->admins)) {
            return null;
        }
        $this->adminIds = [];
        if (is_array($this->admins)) {
            foreach($this->admins as $p){
                if(is_integer($p)){
                    $this->adminIds[] = $p;
                }
                if(is_string($p)){
                    $this->adminIds = array_merge($this->adminIds, $this->getUsersByRoles($p));
                }
            }
        } elseif(is_integer($this->admins)) {
            $this->adminIds[] = $this->admins;
        } elseif(is_string($this->admins)){
            $this->adminIds = array_merge($this->adminIds, $this->getUsersByRoles($this->admins));
        }
        $return = array_unique($this->adminIds);
        $return = array_filter($return);
        if (empty($return) || !count($return)){
            throw new ExceptionMessages('Not found admins.');
        }
        $this->adminIds = $return;

        return $this->adminIds;
    }


    /**
     * Find message by id
     *
     * @param $id
     * @return Messages
     * @throws ExceptionMessages
     */
    protected function findMessage($id)
    {
        $model = Messages::findOne($id);
        if(!$model){
            throw new ExceptionMessages('Not found message');
        }
        return $model;
    }


    /**
     * @return \yii\db\Connection
     */
    protected function getDb()
    {
        return Messages::getDb();
    }

}
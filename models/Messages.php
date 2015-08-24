<?php

namespace vision\messages\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property integer $from_id
 * @property integer $whom_id
 * @property string $message
 * @property integer $status
 * @property integer $is_delete_from
 * @property integer $is_delete_whom
 * @property integer $created_at
 * @property integer $updated_at
 *
 */
class Messages extends \yii\db\ActiveRecord
{

    const STATUS_NEW    = '1';
    const STATUS_READ   = '2';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%messages}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['whom_id', 'message'], 'required'],
            [['from_id', 'whom_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['message'], 'string', 'max' => 750],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_NEW, self::STATUS_READ], 'message' => 'Incorrect status'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'from_id' => Yii::t('app', 'From ID'),
            'whom_id' => Yii::t('app', 'Whom ID'),
            'message' => Yii::t('app', 'Message'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

}

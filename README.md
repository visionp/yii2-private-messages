Private messages
================
Private messages

Приватные сообщения (не чат).

Обновление сообщений при отправке или в фоне каждые 30 секунд (это можно изменить в "vendor\vision\yii2-private-messages\js\private_mess_pooling.js").
Есть возможность отправлять копию сообщения на email пользователя.
Также возможно в фоне проверять кол-во новых сообщений + их авторов.

скрин http://prntscr.com/7dt6tf



Установка
------------

Выполните

```
php composer.phar require --prefer-dist vision/yii2-private-messages "*"
```

или добавьте в ваш composer.json

```
"vision/yii2-private-messages": "*"
```

Использование
-----

После установки расширения необходимо выполнить миграцию:

yii migrate --migrationPath=@vendor/vision/yii2-private-messages/migrations/


В контроллере через который будут передаваться данные добавляем action
 
 ```
     public function actions()
     {
         return [
             'private-messages' => array(
                 'class' => \vision\messages\actions\MessageApiAction::className()
             )
         ];
     }
  ```

Далее прописываем в конфиге:

```
'components' => [...
        'mymessages' => [
                //Обязательно
            'class'    => 'vision\messages\components\MyMessages',
                //не обязательно
                //класс модели пользователей
                //по-умолчанию \Yii::$app->user->identityClass
            'modelUser' => 'common\models\User',
                //имя контроллера где разместили action
            'nameController' => 'site',
                //не обязательно
                //имя поля в таблице пользователей которое будет использоваться в качестве имени
                //по-умолчанию username
            'attributeNameUser' => 'username',
                //не обязательно
                //включение возможности дублировать сообщение на email
            'enableEmail' => true,
                //задаем функцию для возврата адреса почты
                //в качестве аргумента передается объект модели пользователя
            'getEmail' => function($user_model) {
                return $user_model->email;
            },            
                //указываем шаблоны сообщений, в них будет передаваться сообщение $message
            'templateEmail' => [
                'html' => 'private-message-text',
                'text' => 'private-message-html'
            ],
                //тема письма
            'subject' => 'Private message'
        ],
    ...]
```
     
Для работы достаточно во вьюхе вывести виджет

```
<?= vision\messages\widgets\PrivateMessageKushalpandyaWidget::widget() ?>
```


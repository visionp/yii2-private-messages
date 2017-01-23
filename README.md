Private messages
================
Private messages

Приватные сообщения (не чат).

Обновление сообщений при отправке или в фоне каждые 30 секунд (это можно изменить в "vendor\vision\yii2-private-messages\js\private_mess_pooling.js").
Есть возможность отправлять копию сообщения на email пользователя.
Возможен режим когда пользователи будут видеть и смогут писать сообщения только указанным администраторам.
Также возможно в фоне проверять кол-во новых сообщений + их авторов.



Установка
-----------

Выполните

```
php composer.phar require --prefer-dist vision/yii2-private-messages "^2"
```

или добавьте в ваш composer.json

```
"vision/yii2-private-messages": "^2"
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
             'private-messages' => [
                 'class' => \vision\messages\actions\MessageApiAction::className()
             ]
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
                //можно указать роли и/или id пользователей которые будут видны в списке контактов всем кто не подпадает 
                //в эту выборку, при этом указанные пользователи будут и смогут писать всем зарегестрированным пользователям
            'admins' => ['admin', 7],
                //не обязательно
                //включение возможности дублировать сообщение на email
                //для работы данной функции в проектк должна быть реализована отправка почты штатными средствами фреймворка
            'enableEmail' => true,
                //задаем функцию для возврата адреса почты
                //в качестве аргумента передается объект модели пользователя
            'getEmail' => function($user_model) {
                return $user_model->email;
            },
                //задаем функцию для возврата лого пользователей в списке контактов (для виджета cloud)
                //в качестве аргумента передается id пользователя
            'getLogo' => function($user_id) {
                return '\img\ghgsd.jpg';
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
<?= vision\messages\widgets\CloadMessage::widget() ?>
```
или
```
<?= vision\messages\widgets\PrivateMessageKushalpandyaWidget::widget() ?>
```



Если необходимо в фоне проверять новые сообщения мы можем зарегестрировать 
vision\messages\assets\PrivateMessPoolingAsset

и добавить нужный нам слушатель
```

<script>
var listener = new privateMessPooling();
listener.addListener('newData', function(result){
    console.log(result);
});
listener.start();
</script>
```

Private messages
================
Private messages.Llong polling.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vision/yii2-private-messages "*"
```

or add

```
"vision/yii2-private-messages": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

php yii migrate --migrationPath=@yii/rbac/migrations/

```php
<?= \vision\messages\AutoloadExample::widget(); ?>```
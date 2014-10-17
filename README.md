Yii2-image
==========
This extension for image manipulation.
Based on [intervention/image](http://image.intervention.io/)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ostashevdv/yii2-image "*"
```

or add

```
"ostashevdv/yii2-image": "*"
```

to the require section of your `composer.json` file.

Usage
-----

Add to your application config

```
components => [
    'image' => [
        'class' => 'ostashevdv\image\ImageManager',
        'cachePath' => '@webroot/icache/'
    ]
]
```

 Use it in your code by  :

```php
    if ($img = \Yii::$app->image->thumb('https://www.google.ru/images/srpr/logo11w.png', 120, 120)) {
        echo Html::img($img);
    }
```
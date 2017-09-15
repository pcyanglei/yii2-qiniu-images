yii2 七牛多图直传
===========
一款直传七牛的yii2图片widget

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist zh/yii2-qiniu-images "*"
```

or add

```
"zh/yii2-qiniu-images": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
    <?= $form->field($model, 'images')->widget(QiniuFileInput::className(),[
        'options' => [
            'class' => 'btn-danger'
        ],
        'qlConfig' => [
            'accessKey' => 'Q7wmo6VClEeYqnVnMSGdUBb7k0bl86KV5XLyh60N',
            'secretKey' => 'Y8JOtfZvHWsTXcy0CG_0zDNSrwls6p530k0LuT61',
            'scope'=>'test',
            'cdnUrl' => 'http://ouv520g7c.bkt.clouddn.com',//外链域名
        ],
        'clientOptions' => [
            'max' => 5,
            'size' => 204800,
            'btnName' => 'upload',
            'accept' => 'image/jpeg,image/gif,image/png'
        ],
        'pluginEvents' => [
            'delete' => 'function(item){console.log(item)}',
            'success' => 'function(res){console.log(res)}'
        ]
    ]) ?>

```
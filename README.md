yii2 七牛多图直传
===========
一款直传七牛的yii2图片widget 浏览器直传(没试过IE) 没有做分片上传  

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist zh/yii2-qiniu-images "dev-master"
```

or add

```
"zh/yii2-qiniu-images": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
    <?= $form->field($model, 'images')->widget(QiniuFileInput::className(),[
        //'options' => [
        //   'class' => 'btn-danger'//按钮class
        //],
        //'uploadUrl' => 'http://up-z2.qiniu.com',文件上传地址 不同地区的空间上传地址不一样 参见官方文档
        'qlConfig' => [
            'accessKey' => '你的七牛key',
            'secretKey' => '你的七牛secretKey',
            'scope'=>'你的空间名',
            'cdnUrl' => 'http://URL',//外链域名
        ],
        'clientOptions' => [
            'max' => 5,//最多允许上传图片个数  默认为3
            //'size' => 204800,//每张图片大小
            //'btnName' => 'upload',//上传按钮名字
            //'accept' => 'image/jpeg,image/gif,image/png'//上传允许类型
        ],
        //'pluginEvents' => [
        //    'delete' => 'function(item){console.log(item)}',
        //    'success' => 'function(res){console.log(res)}'
        //]
    ]) ?>

```


流程  :

	图片成功上传到七牛后,以数组的形式保存资源地址(外链域名+资源名)到当前模型的属性,例如:
	
	当前goods模型 添加一个成员属性 images 只支持required规则

-----
model

```php
class Goods extends \yii\db\ActiveRecord
{
    public $images;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['images'], 'required']
        ];
    }
}
```
-----

当提交form后端将收到如下类型的数据
```php
  [Goods] => Array
    (
        [images] => Array
            (
                [0] => http://ouv520g7c.bkt.clouddn.com/2017/9/er14pygpvq.jpg
                [1] => http://ouv520g7c.bkt.clouddn.com/2017/9/r5c0eidcx8.jpg
            )
    )
```
-----

更新如何显示已有数据

```php
$model->images = [
	'http://ouv520g7c.bkt.clouddn.com/2017/9/er14pygpvq.jpg',
	'http://ouv520g7c.bkt.clouddn.com/2017/9/r5c0eidcx8.jpg'
]
```
	
	
	
	

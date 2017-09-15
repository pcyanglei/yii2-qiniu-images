<?php
namespace zh\qiniu;
use yii\web\AssetBundle;
/**
 * @author yanghu <127802495@qq.com>
 */
class QiniuInputVueAsset extends AssetBundle
{
    public $sourcePath = '@bower/vue/dist';

    public $js = [
        'vue.js',
    ];
}
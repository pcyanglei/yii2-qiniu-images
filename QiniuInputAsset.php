<?php
namespace zh\qiniu;
use yii\web\AssetBundle;

class QiniuInputAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        $this->js = YII_DEBUG ? ['qiniu-input.js'] : ['qiniu-input.min.js'];
        $this->css = YII_DEBUG ? ['qiniu-input.css'] : ['qiniu-input.min.css'];
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
    
}
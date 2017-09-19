<?php
namespace zh\qiniu;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use Exception;

class QiniuFileInput extends InputWidget
{
    const JS_CLASS_NAME = 'QiniuFileInput';
    
    /**
     * @var array
     */
    public $clientOptions = [];
   
    /**
     * @var array
     */
    public $pluginEvents = [];
    
    /**
     * @var array
     */
    public $qlConfig;
    /**
     * @var array
     */
    public $policy = [];
    
    /**
     * @var string 
     */
    public $uploadUrl = 'http://up-z2.qiniu.com';
    
    /**
     * @var string
     */
    protected  $cdnUrl;
    
    /**
     * @var array
     */
    protected $defaultClientOptions = [
        'max' => 3,
        'size' => 204800,
        'accept' => 'image/jpeg,image/gif,image/png',
    ];
    
    /**
     * {@inheritDoc}
     * @see \yii\widgets\InputWidget::init()
     */
    public function init()
    {
        if ($this->qlConfig === null) {
            throw new InvalidConfigException('QiniuFileInput::qlConfig must be set');
        }
        parent::init();
        $this->registerAssetBundle();
        $this->registerPlugin();
    }
    
    /**
     * {@inheritDoc}
     * @see \yii\base\Widget::run()
     */
    public function run()
    {
        echo $this->getHtml();
    }
    
    /**
     * @throws InvalidConfigException
     * @return string
     */
    protected function getToken()
    {
        try {
            $auth = new Auth($this->qlConfig['accessKey'], $this->qlConfig['secretKey'], $this->qlConfig['scope']);
            $this->cdnUrl = $this->qlConfig['cdnUrl'];
        }catch (Exception $e) {
            throw new InvalidConfigException('qlConfig::'.$e->getMessage());
        }
        if (!empty($this->policy)) {
            $auth->policy = $this->policy;
        }
        return $auth->uploadToken();
    }

    /**
     * @return string
     */
    protected function getElName()
    {
        return $this->model->formName() .'-'. $this->attribute;
    }
    
    /**
     * @return string
     */
    protected function getHtml()
    {
        if ($this->hasModel()) {
            $el = $this->getElName();
            $id = html::getInputId($this->model, $this->attribute);
            $name = Html::getInputName($this->model, $this->attribute);
            $butName = isset($this->clientOptions['btnName']) ? $this->clientOptions['btnName'] : '请选择';
            $butClass  = isset($this->options['class']) ?$this->options['class'] : 'btn-success';
            $html = <<<HTML
                    <div id="$el">
                        <div class="zh-images" v-if="imageList.length > 0 || errMessage != ''">
                            <div class="err-box" v-show="errMessage != ''">
                                <span v-text="errMessage"></span><span class="close" @click="errMessage = ''">×</span>
                            </div>
                            <div v-for="(item,key) in imageList" class="zh-images-items">
                                <img :src="item.name"><div class="zh-cover" @click="deleteImg(key)"><i class="glyphicon glyphicon-trash"></i></div>
                            </div>
                        </div>
                        <div class="progress" v-if="progress > 0">
                            <div class="progress-bar" :style="{ width: progress+'%'}">{{progress}}%</div>
                        </div>
                        <div class="btn file-btn $butClass">
                            <span>$butName</span>
                            <template v-if="imageList.length > 0">
                            <input type="hidden" name="{$name}[]" v-model="img.name" v-for="img in imageList" v-if="imageList.length > 0" id="$id">
                            </template>
                            <template v-else>
                            <input type="hidden" id="$id">
                            </template>
                            <input type="file"  multiple="multiple" :accept="config.accept" @change="upload">
                        </div>
                    </div>
HTML;
        return $html;
        }
    }
    
    /**
     * Registers js and css
     */
    public function registerPlugin()
    {
        $config = $this->mergeConfig();
        if (!empty($this->pluginEvents)) {
            foreach ($this->pluginEvents as $event => $handler) {
                $function = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
                $config['on'.$event] = $function;
            }
        }
        $js = Json::encode($config);
        $this->getView()->registerJs('new ' . self::JS_CLASS_NAME ."($js)",View::POS_END);
    }

    
    /**
     * Registers the asset bundle and locale
     */
    public function registerAssetBundle()
    {
        QiniuInputVueAsset::register($this->getView());
        QiniuInputAsset::register($this->getView());
    }
    
    public function mergeConfig()
    {
        $config = ArrayHelper::merge($this->defaultClientOptions,$this->clientOptions);
        $config['url'] = $this->uploadUrl;
        $config['token'] = $this->getToken();
        $config['cdnUrl'] = $this->cdnUrl;
        $config['el'] = '#'.$this->getElName();
        if (($value = Html::getAttributeValue($this->model, $this->attribute)) != null) {
            $config['imageList'] = (array) $value;
        }
        return $config;
    }
    
}
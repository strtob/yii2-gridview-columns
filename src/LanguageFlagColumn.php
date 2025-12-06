<?php

/**
 * LanguageFlagColumn is a custom Yii2 GridView column for displaying language flags with optional value manipulation.
 * 
 * @link http://www.re-soft.de
 * @copyright Copyright (c) Tobias Streckel
 * @license http://www.re-soft.de
 */

namespace strtob\yii2GridviewColumns;

use strtob\yii2GridviewColumns\assets\LanguageFlagAsset;
use Yii;
use yii\helpers\Html;
use yii\helpers\Inflector;

class LanguageFlagColumn extends \yii\grid\Column
{

    const CONFIG_FLAG_TYPE_CSS_FLAGS = 1;
    const CONFIG_FLAG_TYPE_IMAGE_FLAGS = 2;


    private const VALUE_DEFAULT = 1;


    public $flagType = self::CONFIG_FLAG_TYPE_CSS_FLAGS;


    public $flagImagePath = '/images/flags';

    public $flagImageFileExtension = 'png';

    /**
     * @var string the attribute name associated with this column. 
     * If neither [[content]] nor [[value]] is specified, the value of the specified attribute 
     * will be retrieved from each data model and displayed.
     */
    public $attribute;


    /**
     * @var array the HTML attributes for the container options.
     */
    public $containerOptions = [];

    /**
     * @var string|null the related model's attribute, if any.
     */
    public $relation;

    /**
     * @var string the custom value to display in the column, or the attribute value by default.
     */
    public $value = '';

    /**
     * @var string the text to display along with the flag. 
     */
    public $flagText = '';

    /**
     * @var string|null the label for the column. If not set, the label associated with the attribute will be displayed.
     */
    public $label;

    /**
     * @var bool whether the header label should be HTML-encoded.
     */
    public $encodeLabel = true;

    /**
     * @var bool whether to encode the flag text for safety from XSS or HTML injection.
     * Default is true, meaning the text will be HTML-encoded.
     */
    public $encodeHtml = true;

    /**
     * @var int a specific value for the state, default is 1.
     */
    public $stateValue = self::VALUE_DEFAULT;

    /**
     * @var bool whether to allow sorting by this column.
     */
    public $enableSorting = true;

    /**
     * @var array the HTML attributes for the link tag in the header cell when sorting is enabled.
     */
    public $sortLinkOptions = [];

    /**
     * @var array the HTML attributes for the header options.
     */
    public $headerOptions = ['class' => 'location-column'];

    /**
     * @var \Closure|null an optional closure function to manipulate the value.
     * This closure will receive three parameters: `$model`, `$key`, and `$index`.
     * Example:
     * function ($model, $key, $index) {
     *     return strtoupper($model->language_code); // Custom transformation
     * }
     */
    public $valueCallback = null;

    /**
     * Initializes the column and registers the required assets.
     */
    public function init()
    {
        parent::init();

        if (empty($this->label)) {
            $this->label = Yii::t('app', 'Language');
        }

        // Register the CSS and assets for language flags
        if ($this->flagType == self::CONFIG_FLAG_TYPE_CSS_FLAGS) {
            LanguageFlagAsset::register($this->grid->view);
        }
    }

    /**
     * Renders the header cell content, supporting sorting and encoding.
     * 
     * @return string the rendered header cell content.
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || ($this->label === null && $this->attribute === null)) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();

        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if ($this->attribute !== null && $this->enableSorting) {
            $sort = $this->grid->dataProvider->getSort();
            if ($sort !== false && $sort->hasAttribute($this->attribute)) {
                return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
            }
        }

        return $label;
    }

    /**
     * Retrieves the label for the header cell.
     * 
     * @return string the header cell label.
     */
    protected function getHeaderCellLabel()
    {
        $provider = $this->grid->dataProvider;

        if ($this->label === null) {
            if ($provider instanceof \yii\data\ActiveDataProvider && $provider->query instanceof \yii\db\ActiveQueryInterface) {
                $modelClass = $provider->query->modelClass;
                $model = $modelClass::instance();
                return $model->getAttributeLabel($this->attribute);
            } elseif ($provider instanceof \yii\data\ArrayDataProvider && $provider->modelClass !== null) {
                $modelClass = $provider->modelClass;
                $model = $modelClass::instance();
                return $model->getAttributeLabel($this->attribute);
            } elseif ($this->grid->filterModel !== null && $this->grid->filterModel instanceof \yii\base\Model) {
                return $this->grid->filterModel->getAttributeLabel($this->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof \yii\base\Model) {
                    return $model->getAttributeLabel($this->attribute);
                } else {
                    return Inflector::camel2words($this->attribute);
                }
            }
        }

        return $this->label;
    }

    /**
     * Renders the content of a data cell, including a language flag.
     * 
     * @param mixed $model the data model.
     * @param mixed $key the key associated with the data model.
     * @param int $index the zero-based index of the data model.
     * @return string the rendered data cell content.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        // Use the closure to manipulate the value if available, otherwise use attribute or set value
        if ($this->valueCallback instanceof \Closure) {
            $value = call_user_func($this->valueCallback, $model, $key, $index);
        } else {
            $value = $this->value instanceof \Closure ? call_user_func($this->value, $model, $index) : $model->{$this->attribute};
        }

        $flagText = $this->encodeHtml ? Html::encode($this->flagText) : $this->flagText;


        if(strlen($value) >3){
            $value = substr($value, 0, 2);
        }

        // Create the opening div with container options
        $container = Html::beginTag('div', $this->containerOptions);

        if ($this->flagType == self::CONFIG_FLAG_TYPE_CSS_FLAGS) {
            // Create the content for the language flag
            $flagContent = '<span class="language-flag '
                . Html::encode($value) . '">'
                . $flagText . '</span>';
        } else {
            // Create the content for the language flag
            $flagContent = '<img class="language-flag" src="'
                . $this->flagImagePath . '/'
                . Html::encode($value)
                . '.' . $this->flagImageFileExtension
                . '" alt="' . $flagText . '">';
        }

        // Return the complete output: the opening div, content, and closing div
        return $container . $flagContent . Html::endTag('div');
    }


}

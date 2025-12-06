<?php
/**
 * @link http://www.re-soft.de
 * @copyright Copyright (c) Tobias Streckel
 * @license MIT
 */

namespace strtob\yii2GridviewColumns;

use kartik\ipinfo\IpInfo;
use kartik\popover\PopoverX;

class GeoIpColumn extends \yii\grid\Column{

    /**
     * {@inheritdoc}
     */
    public $headerOptions = ['class' => 'geoip-column'];

    /**
     * @var bool whether to show the popover or not
     */
    public $showPopover = false;

    
    /**
     * @var array the configuration options for the [[\kartik\popover\PopoverX]] widget. This property is used to configure the popover when [[showPopover]] is set to `true`.
     */
    public $popoverOptions = [];


    /**
     * @var string the attribute name associated with this column. When neither [[content]] nor [[value]]
     * is specified, the value of the specified attribute will be retrieved from each data model and displayed.
     *
     * Also, if [[label]] is not specified, the label associated with the attribute will be displayed.
     */
    public $attribute;


    protected function renderGeoIp($model, $key, $index)
    {

        $ip = \yii\helpers\ArrayHelper::getValue($model, $this->attribute);

        return IpInfo::widget([
            'ip' => $ip,
            'showPopover' => $this->showPopover,
            'popoverOptions' => $this->popoverOptions,
        ]);

    }


    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {

        if ($this->content instanceof Closure) {
            $cell = call_user_func($this->content, $model, $key, $index, $this);
        } else {
            $cell = $this->renderGeoIp($model,$key,$index);
        }

        return $cell;

    }

}
?>

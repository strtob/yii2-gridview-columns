<?php

/**
 * IconColumn class for Yii2 GridView
 * 
 * This class represents a custom column type in a Yii2 GridView that displays icons
 * (e.g., Font Awesome) alongside text based on the status of the associated model attributes.
 *
 * Usage Example:
 * 
 * ```php
 * use strtob\yii2GridviewColumns\IconColumn;
 * 
 * echo GridView::widget([
 *     'dataProvider' => $dataProvider,
 *     'columns' => [
 *         // Other columns...
 *         [
 *             'class' => IconColumn::class,
 *             'attribute' => 'status', // The attribute to check for the model's state
 *             'iconClass' => 'fa fa-check', // Font Awesome icon class for active state
 *             'iconClassInactive' => 'fa fa-times', // Icon class for inactive state
 *             'text' => 'Active', // Text for the active state
 *             'textInactive' => 'Inactive', // Text for the inactive state
 *             // Closure to determine icon and text dynamically
 *             'value' => function($model, $key, $index) {
 *                 return [
 *                     'iconClass' => $model->status === 'active' ? 'fa fa-check' : 'fa fa-times',
 *                     'text' => $model->status === 'active' ? 'Active' : 'Inactive',
 *                 ];
 *             },
 *             'headerOptions' => ['class' => 'text-center'], // Center align header
 *         ],
 *         // Other columns...
 *     ],
 * ]);
 * ```
 */

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\helpers\Html;

class IconColumn extends \yii\grid\Column
{

    public $attribute;

    /**
     * @var callable|null A closure that returns an array with 'iconClass' and 'text' based on the model.
     */
    public $value;

    /**
     * @var string The CSS class for the icon when the item is active.
     */
    public $iconClass;

    /**
     * @var string The CSS class for the icon when the item is inactive.
     */
    public $iconClassInactive;

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        // If a closure for value is provided, use it to get the icon and text
        if ($this->value instanceof \Closure) {
            $value = call_user_func($this->value, $model, $key, $index);
            $iconClass = $value['iconClass'] ?? $this->iconClass;
            $text = $value['text'] ?? '';
        } else {
            // Fallback to default icon class and text
            $iconClass = $model->{$this->attribute} === 'active' ? $this->iconClass : $this->iconClassInactive;
            $text = $model->{$this->attribute} === 'active' ? $this->text : $this->textInactive;
        }

        // Generate the icon HTML and return it with the text
        return Html::tag('i', '', ['class' => $iconClass]) . ' ' . Html::encode($text);
    }
}

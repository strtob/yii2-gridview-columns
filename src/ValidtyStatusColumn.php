<?php


namespace strtob\yii2GridviewColumns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

class ValidtyStatusColumn extends DataColumn
{
    public $attribute = 'status';
    public $label;

    /**
     * Template for rendering the column content.
     * Available placeholders:
     * - `{icon}`: The icon HTML.
     * - `{message}`: The status message.
     * - `{valid_from}`: Formatted valid_from date.
     * - `{valid_from_relative}`: Relative time for valid_from.
     * - `{valid_until}`: Formatted valid_until date.
     * - `{valid_until_relative}`: Relative time for valid_until.
     * 
     * example of use:
     *    [
     *      'class' => ValidtyStatusColumn::class,
     *       'attribute' => 'status', // optional, defaults already
     *       'label' => Yii::t('app', 'Validity'), // override label if you want
     *       'template' => '{icon} {message} {validity_period}', // you can customize placeholders
     *     ],
     */
    public $template = '{icon} {message} {validity_period}';

    public function init()
    {
        parent::init();

        // Set the default label if not provided
        $this->label = $this->label ?? Yii::t('app', 'Status');

        // Set format to raw to allow HTML output
        $this->format = 'raw';
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        // Retrieve the status data from model's `validityStage` attribute
        $v = $model->validityStage;

        // Set the placeholders for the template
        $placeholders = [
            '{icon}' => isset($v->icon) ? '<i class="' . Html::encode($v->icon) . ' me-2"></i>' : '',
            '{message}' => Html::encode($v->message),
            '{validity_period}' => $this->formatValidityPeriod($model),
        ];

        // Replace placeholders in the template with actual values
        return strtr($this->template, $placeholders);
    }

    /**
     * Helper method to format the validity period based on valid_from and valid_until.
     * @param $model
     * @return string Formatted validity period with relative times.
     */
    protected function formatValidityPeriod($model)
    {
        $formattedPeriod = '<div><small>';

        // Check if valid_from and valid_until dates exist
        if (isset($model->valid_from) && isset($model->valid_until)) {
            $formattedPeriod .= Yii::t('app', 'valid: ') .
                Yii::$app->formatter->asDate($model->valid_from, 'short') .
                ' (' . Yii::$app->formatter->asRelativeTime($model->valid_from) . ')' .
                ' - ' .
                Yii::$app->formatter->asDate($model->valid_until, 'short') .
                ' (' . Yii::$app->formatter->asRelativeTime($model->valid_until) . ')';
        } elseif (isset($model->valid_from)) {
            $formattedPeriod .= Yii::t('app', 'valid from ') .
                Yii::$app->formatter->asDate($model->valid_from, 'short') .
                ' (' . Yii::$app->formatter->asRelativeTime($model->valid_from) . ')';
        } elseif (isset($model->valid_until)) {
            $formattedPeriod .= Yii::t('app', 'valid until ') .
                Yii::$app->formatter->asDate($model->valid_until, 'short') .
                ' (' . Yii::$app->formatter->asRelativeTime($model->valid_until) . ')';
        }

        $formattedPeriod .= '</small></div>';
        return $formattedPeriod;
    }
}
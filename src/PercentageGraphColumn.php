<?php

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

/**
 * PercentageGraphColumn extends the DataColumn to display a percentage graph
 * using Bootstrap's progress bars alongside an optional inner label within a GridView.
 *
 * @package strtob\yii2GridviewColumns
 */
class PercentageGraphColumn extends DataColumn
{
    /**
     * @var callable A callable function that returns the percentage value.
     */
    public $value;

    /**
     * @var string A label for the column header.
     */
    public $label;

    /**
     * @var callable A callable function that returns the custom label to display inside the progress bar.
     */
    public $barLabel;

    /**
     * @var string Color of the percentage graph.
     */
    public $barColor = 'success'; // Default to Bootstrap success color


    public $progressBarClass = 'progress-bar'; // Default to Bootstrap progress-bar class

    /**
     * Initializes the column and checks for Bootstrap assets.
     */
    public function init()
    {
        parent::init();
        // Register Bootstrap CSS if not already registered
        if (!Yii::$app->assetManager->getPublishedUrl('@vendor/bower-asset/bootstrap') && !Yii::$app->assetManager->getPublishedUrl('@vendor/npm-asset/bootstrap')) {
            throw new \yii\base\InvalidConfigException('Bootstrap is not installed. Please install Bootstrap via Composer or Bower.');
        }
    }

    /**
     * Renders the header cell content.
     *
     * @return string The rendered header cell content.
     */
    protected function renderHeaderCellContent()
    {
        return $this->label ?: parent::renderHeaderCellContent();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        // Fetch the percentage value using the value callback
        $percentage = call_user_func($this->value, $model, $key, $index);

        // Ensure the percentage value is valid (0-100)
        $percentage = max(0, min(100, $percentage));

        // Generate the Bootstrap progress bar HTML
        $progressHtml = $this->renderProgressBar($percentage, $model);

        // Return the progress bar
        return Html::tag('div', $progressHtml, [
            'style' => 'display: inline-flex; align-items: center; width: 100%;',
        ]);
    }

    /**
     * Generates the HTML for the Bootstrap progress bar.
     *
     * @param int $percentage The percentage to display (0-100).
     * @param mixed $model The current model to fetch the bar label from.
     *
     * @return string The generated HTML for the progress bar.
     */
    protected function renderProgressBar($percentage, $model)
    {
        // Set the inner label; use the custom inner label function if provided
        $innerLabel = $this->barLabel !== null ? call_user_func($this->barLabel, $model) : $percentage . '%';

        // Generate the HTML for the progress bar
        return Html::tag('div', Html::tag('div', $innerLabel, [
            'class' => $this->progressBarClass . ' bg-' . $this->barColor,
            'role' => 'progressbar',
            'aria-valuenow' => $percentage,
            'aria-valuemin' => '0',
            'aria-valuemax' => '100',
            'style' => 'width: ' . $percentage . '%;',
        ]), [
            'class' => 'progress',
            'style' => 'height: 20px; width: 150px;', // Customize width as needed
        ]);
    }
}

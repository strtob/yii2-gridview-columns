<?php 

namespace strtob\yii2GridviewColumns;

use strtob\yii2GridviewColumns\assets\SparklineColumnAsset;
use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;

/**
 * SparklineColumn extends the DataColumn to provide sparkline charts within a GridView.
 * It generates a small graph (sparkline) based on a data series provided for each row.
 *
 * @package strtob\yii2GridviewColumns
 */
class SparklineColumn extends DataColumn
{
    /**
     * @var callable A callable function that returns the data series for the sparkline.
     */
    public $value;

    /**
     * @var array Options for configuring the sparkline display.
     */
    public $sparklineOptions = [];

    /**
     * @var bool Whether to display the trend indicator.
     */
    public $showTrend = true; // Default to true

    /**
     * @var string The icon to use for positive trend (default is up arrow).
     */
    public $positiveIcon = '↑';

    /**
     * @var string The icon to use for negative trend (default is down arrow).
     */
    public $negativeIcon = '↓';

    /**
     * @var string The color for positive trends (default is green).
     */
    public $positiveColor = 'green';

    /**
     * @var string The color for negative trends (default is red).
     */
    public $negativeColor = 'red';

    /**
     * @var array The additional styles for the trend indicator.
     */
    public $trendStyles = [
        'fontSize' => '20px',
        'marginLeft' => '10px',
        'lineHeight' => '25px',
    ];

    public function init()
    {
        parent::init();
        SparklineColumnAsset::register($this->grid->view);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->value === null) {
            return ''; // Return empty if no value function is provided
        }

        // Fetch the data series using the value callback
        $dataSeries = call_user_func($this->value, $model, $key, $index);

        if (empty($dataSeries) || !is_array($dataSeries)) {
            return ''; // Return empty if data series is not an array or is empty
        }

        // Generate the sparkline HTML
        $sparklineHtml = $this->renderSparkline($dataSeries);

        // If trend display is enabled, render the trend indicator
        $trendHtml = $this->showTrend ? $this->renderTrendIndicator($dataSeries) : '';

        // Wrap in a container for proper alignment (sparkline first, then trend)
        return Html::tag('div', $sparklineHtml . $trendHtml, [
            'style' => 'display: inline-flex; align-items: center;',
        ]);
    }

    /**
     * Generates the HTML for the sparkline using the provided data series.
     *
     * @param array $dataSeries The data series for the sparkline.
     *
     * @return string The generated HTML for the sparkline.
     */
    protected function renderSparkline(array $dataSeries)
    {
        // Convert data series to JavaScript array
        $dataJs = json_encode($dataSeries);

        // Prepare sparkline options as JSON
        $sparklineOptionsJs = json_encode($this->sparklineOptions);

        // Generate the HTML for the sparkline
        return Html::tag('div', '', [
            'class' => 'sparkline',
            'data' => [
                'values' => $dataJs,
                'options' => $sparklineOptionsJs,
            ],
        ]);
    }

    /**
     * Generates the HTML for the trend indicator based on the data series.
     *
     * @param array $dataSeries The data series for trend evaluation.
     *
     * @return string The HTML for the trend indicator.
     */
    protected function renderTrendIndicator(array $dataSeries)
    {
        // Determine trend: check if the last value is greater than the first
        $isIncreasing = end($dataSeries) > reset($dataSeries);

        // Define the trend icon and color based on the direction
        $icon = $isIncreasing ? $this->positiveIcon : $this->negativeIcon;
        $color = $isIncreasing ? $this->positiveColor : $this->negativeColor;

        // Return the trend indicator as a styled span
        return Html::tag('span', $icon, [
            'style' => 'color: ' . $color . '; font-size: ' . $this->trendStyles['fontSize'] . '; margin-left: ' . $this->trendStyles['marginLeft'] . '; line-height: ' . $this->trendStyles['lineHeight'] . ';',
        ]);
    }
}

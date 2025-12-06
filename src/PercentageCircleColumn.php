<?php

namespace strtob\yii2GridviewColumns;

use yii\grid\DataColumn;
use yii\helpers\Html;

/**
 * PercentageCircleColumn displays a percentage value in a circular format within a Yii2 GridView.
 */
class PercentageCircleColumn extends DataColumn
{
    /**
     * @var callable A closure to get the percentage value (0-100).
     */
    public $value;

    /**
     * @var callable A closure to get the text to display alongside the circle.
     */
    public $text;

    /**
     * @var string The position of the text ('left' or 'right').
     */
    public $textPosition = 'right';

    /**
     * @var int The diameter of the circle.
     */
    public $circleDiameter = 50;

    /**
     * @var int The starting position of the circle as a clock (0-12).
     */
    public $startPosition = 12;
    /**
     * @var string The background color of the circle.
     */
    public $backgroundColor = '#ffffff';
    /**
     * @var array The color ranges based on percentage. 
     *            The keys are the max percentage values, and the values are the corresponding colors.
     */
    public $colorRanges = [
        30 => '#dc3546', // red
        50 => '#fec107', // orange
        70 => '#17a2b7', // yellow
        100 => '#27a844'
    ];

    /**
     * @var string Template for rendering the cell content.
     */
    public $template = '<div class="d-flex">{circle}{text}</div>'; // Default template

    /**
     * Renders the content for a data cell.
     *
     * @param mixed $model The data model.
     * @param mixed $key The key associated with the data item.
     * @param int $index The zero-based index of the data item among the items array returned by data provider.
     * @return string The rendered data cell content.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        // Get the percentage value using the provided closure, or default to 0 if not provided.
        $percentage = is_callable($this->value) ? call_user_func($this->value, $model, $key, $index) : 0;

        // Get the text using the provided closure, or default to an empty string if not provided.
        $displayText = is_callable($this->text) ? call_user_func($this->text, $model, $key, $index) : '';

        // Generate the circle representation.
        $circle = $this->renderCircle($percentage);

        if ($displayText !== '') {
            // Prepare the text based on position and encode it for safety.
            $textHtml = Html::tag('span', Html::encode($displayText), ['class' => 'percentage-circle-text']);
            // Adjust text position based on the specified position
            $textHtml = $this->textPosition === 'left' ? $textHtml . ' ' : ' ' . $textHtml;
        } else
            $textHtml = '';

        // Prepare the final output using the template
        return str_replace(['{circle}', '{text}'], [$circle, $textHtml], $this->template);
    }

    /**
     * Renders the circle based on the given percentage.
     *
     * @param int $percentage The percentage value.
     * @return string The rendered HTML for the circle.
     */
    protected function renderCircle($percentage)
    {
        // Limit percentage to valid range (0-100).
        $percentage = max(0, min($percentage, 100));

        $percentage = round($percentage, 0);

        // Get the stroke color based on the percentage.
        $strokeColor = $this->getColorByPercentage($percentage);

        // Calculate the radius.
        $radius = $this->circleDiameter / 2;
        $innerRadius = $radius - 5; // Inner radius for the circle

        // Calculate the circumference of the circle.
        $circumference = 2 * pi() * $innerRadius;

        // Calculate the stroke-dasharray value.
        $dashArray = ($percentage / 100) * $circumference;

        // Calculate the circle's style and attributes.
        $circleStyle = "width: {$this->circleDiameter}px; height: {$this->circleDiameter}px; border-radius: 50%;";

        // Using heredoc syntax for better readability
        $svg = <<<SVG
        <svg width="{$this->circleDiameter}" height="{$this->circleDiameter}">
            <circle cx="{$radius}" cy="{$radius}" r="{$innerRadius}" stroke="#e6e6e6" stroke-width="5" fill="transparent"/>
            <circle cx="{$radius}" cy="{$radius}" r="{$innerRadius}" stroke="{$strokeColor}" stroke-width="5" fill="transparent" stroke-dasharray="{$dashArray}, {$circumference}" />
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="10" fill="#333">{$percentage}%</text>
        </svg>
        SVG;

          $classAppend = 'me-2';

        if($this->text == null) 
              $classAppend = 'mx-auto';
              

        return Html::tag('div', $svg, ['style' => $circleStyle, 'class' => 'percentage-circle ' . $classAppend]);
    }

    /**
     * Determines the color based on the percentage using the defined color ranges.
     *
     * @param int $percentage The percentage value.
     * @return string The color corresponding to the percentage.
     */
    protected function getColorByPercentage($percentage)
    {
        foreach ($this->colorRanges as $maxPercentage => $color) {
            if ($percentage <= $maxPercentage) {
                return $color;
            }
        }

        // Default color if no range matches (shouldn't normally reach here for 0-100)
        return '#000000'; // fallback to black
    }
}

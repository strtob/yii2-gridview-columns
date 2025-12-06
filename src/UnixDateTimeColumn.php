<?php

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\helpers\Html;
use yii\grid\DataColumn;

/**
 * UnixDateTimeColumn class for displaying Unix timestamps as formatted date-time.
 */
class UnixDateTimeColumn extends DataColumn
{
    /**
     * @var string The format to use for displaying date and time.
     * Possible values: 'datetime' (shows both date and time), 'date' (shows only date), 'time' (shows only time).
     */
    public $format = 'datetime';

    /**
     * @var string The timezone to use for displaying date and time.
     */
    public $timezone = 'UTC';

    /**
     * @var string|null An optional icon class to display before the relative time value.
     */
    public $iconRelativeTime = 'fa fa-clock'; // Default icon for the relative time

    /**
     * @var bool Whether to show relative time instead of absolute time.
     */
    public $showRelativeTime = true;

    /**
     * @var string The format to use for displaying the date (default is "Y-m-d").
     */
    public $dateFormat = 'php:d.m.Y';

    /**
     * @var string The format to use for displaying the time (default is "H:i").
     */
    public $timeFormat = 'php:H:i';

    /**
     * @var string The template for displaying the date-time information.
     * Use {icon}, {datetime}, and {relativeTime} placeholders for customization.
     */
    public $itemTemplate = '{datetime} {relativeTime}';

    /**
     * Renders the content for the data cell.
     *
     * @param mixed $model The model data.
     * @param mixed $key The key associated with the data.
     * @param int $index The zero-based index of the data item.
     * @return string The rendered content.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        // Get the Unix timestamp value
        $value = $this->getDataCellValue($model, $key, $index);

        // Return empty cell if value is null
        if ($value === null) {
            return $this->grid->emptyCell;
        }

        // Create DateTime object from Unix timestamp
        $dateTime = new \DateTime('@' . $value);
        $dateTime->setTimezone(new \DateTimeZone($this->timezone));

        // Build the output using the item template
        $formattedDateTime = $this->formatDateTime($dateTime); // Absolute date-time format

        // Show relative time if enabled
        $relativeTimeHtml = '';
        if ($this->showRelativeTime) {
            $relativeTime = Yii::$app->formatter->asRelativeTime($dateTime);
            $iconRelativeTimeHtml = Html::tag('i', '', ['class' => $this->iconRelativeTime]);
            $relativeTimeHtml = '<div style="font-size: small; color: gray;">' . $iconRelativeTimeHtml . ' ' . Html::encode($relativeTime) . '</div>';
        }

        // Replace placeholders in the item template
        $output = str_replace(
            ['{datetime}', '{relativeTime}'],
            [$formattedDateTime, $relativeTimeHtml],
            $this->itemTemplate
        );

        return '<div style="min-width: 200px;">' . $output . '</div>';
    }

    /**
     * Formats the DateTime object based on the specified options.
     *
     * @param \DateTime $dateTime The DateTime object to format.
     * @return string The formatted date-time string.
     */
    protected function formatDateTime($dateTime)
    {
        // Format the date and time based on user preferences
        $formatted = '';

        // Format the date part
        $formatted .= Yii::$app->formatter->asDate($dateTime, $this->dateFormat);

        // Format the time part without seconds
        $formatted .= ' ' . Yii::$app->formatter->asTime($dateTime, $this->timeFormat);

        return $formatted;
    }
}

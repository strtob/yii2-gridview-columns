<?php

/**
 * @link http://www.re-soft.de
 * @copyright Copyright (c) Tobias Streckel
 * @license http://www.re-soft.de
 */

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ActiveQueryInterface;
use yii\data\ArrayDataProvider;

/**
 * Class DateTimeRangeColumn
 * This class represents a column in a Yii2 GridView that displays a date range,
 * allowing for sorting and filtering of validity periods.
 *
 * @package strtob\yii2GridviewColumns
 */
class DateTimeRangeColumn extends \yii\grid\Column
{
    private const VALUE_DEFAULT = 1;

    /**
     * @var array $headerOptions HTML options for the header cell
     */
    public $headerOptions = ['class' => 'daterange-column'];

    /**
     * @var string $attribute The attribute used for sorting
     */
    public $attribute;

    /**
     * @var string $attributeFrom The attribute representing the start date
     */
    public $attributeFrom = 'valid_from';

    /**
     * @var string $attributeUntil The attribute representing the end date
     */
    public $attributeUntil = 'valid_until';

    /**
     * @var string $label The label for the column header
     */
    public $label;

    /**
     * @var array $filterOptions HTML options for the filter cell
     */
    public $durationHtmlOptions = [];

    /**
     * @var bool $encodeLabel Whether to HTML-encode the label
     */
    public $encodeLabel = true;

    /**
     * @var int $stateValue Default state value
     */
    public $stateValue = self::VALUE_DEFAULT;

    /**
     * @var bool $enableSorting Whether to enable sorting on this column
     */
    public $enableSorting = true;

    /**
     * @var array $sortLinkOptions HTML options for the sort link
     */
    public $sortLinkOptions = [];

    /**
     * @var string $filterAttribute The attribute used for filtering
     */
    public $filterAttribute = 'validityPeriod';

    /**
     * @var string $dateTimeFormat The format to use for displaying date and time.
     */
    public $dateTimeFormat = 'datetime'; // Default is to show both date and time

    /**
     * @var string $dateFormat The format to use for displaying the date
     */
    public $datetimeFormat = 'php:H:i';

   /**
     * @var bool $showRelativeTime Whether to show the relative time in the column
     */
    public $showRelativeTime = true;

    /**
     * @var bool $showYears Whether to show the years in the duration
     */
    public $showYears = true;

    /**
     * @var bool $showMonths Whether to show the months in the duration
     */
    public $showMonths = true;

    /**
     * @var bool $showDays Whether to show the days in the duration
     */
    public $showDays = true;

    /**
     * @var bool $showHours Whether to show the hours in the duration
     */
    public $showHours = true;

    /**
     * @var bool $showMinutes Whether to show the minutes in the duration
     */
    public $showMinutes = true;

    /**
     * @var bool $showDate Whether to show the date in the column
     */
    public $showDate = true; 

    /**
     * @var bool $showTime Whether to show the time in the column
     */
    public $showTime = true; 

    /**
     * @var string $entryIcon The icon to display for the start date
     */
    public $entryIcon = '<i class="fa-solid fa-arrow-right-to-bracket text-success"></i> ';

    /**
     * @var string $exitIcon The icon to display for the end date
     */
    public $exitIcon = '<i class="fa-solid fa-arrow-right-from-bracket text-danger"></i> ';

    /**
     * Initializes the column and checks for the existence of necessary attributes.
     * 
     * @throws InvalidConfigException if the attributes do not exist
     */
    public function init()
    {
        parent::init();

        if ($this->label === null) {
            $this->label = \yii::t('app', 'Validity');
        }

        $models = $this->grid->dataProvider->getModels();

        if (!empty($models)) {
            if (!$models[0]->hasAttribute($this->attributeFrom)) {
                throw new InvalidConfigException("Column '{$this->attributeFrom}' does not exist in your model.");
            }

            if (!$models[0]->hasAttribute($this->attributeUntil)) {
                throw new InvalidConfigException("Column '{$this->attributeUntil}' does not exist in your model.");
            }
         
        }
    }

    /**
     * Renders the header cell content, including sorting functionality.
     * 
     * @return string The rendered header cell content
     */
    protected function renderHeaderCellContent()
    {
        // Check if sorting is enabled and the attribute exists for sorting
        if ($this->enableSorting && ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $this->label]));
        }
        return Html::encode($this->label); // Return the encoded label if sorting is not enabled
    }

    /**
     * Renders the content of the filter cell.
     * 
     * @return string The rendered filter cell content
     */
    protected function renderFilterCellContent()
    {
        if ($this->filterAttribute !== null && $this->grid->filterModel !== null && ($filterModel = $this->grid->filterModel) instanceof Model && $filterModel->isAttributeActive($this->filterAttribute)) {
            $inputId = $this->getHeaderFilterInputId();

            return \kartik\daterange\DateRangePicker::widget([
                'model' => $filterModel,
                'attribute' => $this->filterAttribute,
                'startAttribute' => $this->attributeFrom,
                'endAttribute' => $this->attributeUntil,
                'id' => $inputId,
                'presetDropdown' => true,
                'convertFormat' => true,
                'includeMonthsFilter' => true,
                'pluginOptions' => [
                    'opens' => 'left',
                    'locale' => [
                        'separator' => ' ' . \yii::t('app_date_range', 'to') . ' ',
                    ],
                ],
            ]);
        }
        return '';
    }

    /**
     * Renders the data cell content for each row in the grid.
     * 
     * @param Model $model The model for the current row
     * @param mixed $key The key for the current row
     * @param int $index The index of the current row
     * @return string The rendered data cell content
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if (empty($model->{$this->attributeFrom}) && empty($model->{$this->attributeUntil})) {
            return ''; // Return an empty string if both values are empty
        }

        $r = '<div style="min-width: 200px">';
        $r .= '<div style="">';

        // Handle valid_from
        if (!empty($model->{$this->attributeFrom})) {
            $r .= $this->entryIcon;
            $r .= $this->formatDateTime($model->{$this->attributeFrom});

            if ($this->showRelativeTime) {
                $r .= ' <small>(' . Yii::$app->formatter->asRelativeTime($model->{$this->attributeFrom}) . ')</small>';
            }
        }

        $r .= '</div>';

        $r .= '<div style="">';

        // Handle valid_until
        if (!empty($model->{$this->attributeUntil})) {
            $r .= $this->exitIcon;
            $r .= $this->formatDateTime($model->{$this->attributeUntil});

            if ($this->showRelativeTime) {
                $r .= ' <small>(' . Yii::$app->formatter->asRelativeTime($model->{$this->attributeUntil}) . ')</small>';
            }
        }

        $r .= '</div>';

        // Calculate the duration and display it below the date fields
        if (!empty($model->{$this->attributeFrom}) && !empty($model->{$this->attributeUntil})) {
            $duration = $this->calculateTimeDifference($model->{$this->attributeFrom}, $model->{$this->attributeUntil});
            $r .= '<div>' . Html::tag('small', yii::t('app', 'Duration').': ' . $duration, [$this->durationHtmlOptions]) . '</div>';
        }

        $r .= '</div>';

        return $r;
    }

    /**
     * Formats a datetime value according to the specified format settings.
     * 
     * @param string $datetime The datetime to format
     * @return string The formatted datetime string
     */
    protected function formatDateTime($datetime)
    {
        $formatted = '';

        switch ($this->dateTimeFormat) {
            case 'date':
                $formatted .= Yii::$app->formatter->asDate($datetime);
                break;
            case 'time':
                $formatted .= Yii::$app->formatter->asTime($datetime);
                break;
            case 'datetime':
            default:
                // Default to showing both date and time
                $formatted .= Yii::$app->formatter->asDate($datetime);
                if ($this->showTime) {
                    $formatted .= ' ' . Yii::$app->formatter->asTime($datetime, $this->datetimeFormat);
                }
                break;
        }

        return $formatted;
    }

    /**
     * Calculates the time difference between two datetime values.
     * 
     * @param string $from The start datetime
     * @param string $until The end datetime
     * @return string The formatted duration string
     */
    protected function calculateTimeDifference($from, $until)
    {
        $fromDate = new \DateTime($from);
        $untilDate = new \DateTime($until);
        $interval = $fromDate->diff($untilDate);

        $parts = [];
        if ($this->showYears) {
            $parts[] = $interval->y > 0 ? $interval->y . ' year' . ($interval->y > 1 ? 's' : '') : '';
        }
        if ($this->showMonths) {
            $parts[] = $interval->m > 0 ? $interval->m . ' month' . ($interval->m > 1 ? 's' : '') : '';
        }
        if ($this->showDays) {
            $parts[] = $interval->d > 0 ? $interval->d . ' day' . ($interval->d > 1 ? 's' : '') : '';
        }
        if ($this->showHours) {
            $parts[] = $interval->h > 0 ? $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') : '';
        }
        if ($this->showMinutes) {
            $parts[] = $interval->i > 0 ? $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') : '';
        }

        return implode(', ', array_filter($parts)) ?: '0 minutes'; // Return formatted duration or 0 minutes if no duration
    }
}

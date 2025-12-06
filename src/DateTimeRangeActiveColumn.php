<?php

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\base\InvalidConfigException;
use strtob\yii2helpers\DateHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Class DateTimeRangeActiveColumn
 * 
 * This class extends the Yii2 GridView `Column` class to display a custom icon
 * based on a date range. It compares a model's start and end dates and renders
 * an icon that signifies whether the current date is within the specified range.
 * 
 * @property string|null $start The model attribute that holds the start date for the range
 * @property string|null $end The model attribute that holds the end date for the range
 * @property string $css_icon_class_active CSS class for the active status icon
 * @property string $css_icon_class_inactive CSS class for the inactive status icon
 * @property string|null $css_icon_class_active_text Text shown when hovering over the active status icon
 * @property string|null $css_icon_class_inactive_text Text shown when hovering over the inactive status icon
 * @property string|null $label The label for the column. If not set, defaults to the attribute label
 * @property bool $encodeLabel Whether to HTML-encode the column label
 * @property bool $enableSorting Whether sorting is enabled for this column
 * @property array $sortLinkOptions HTML attributes for the sort link in the header cell
 * 
 * @throws InvalidConfigException if the "start" or "end" properties are not set
 */
class DateTimeRangeActiveColumn extends \yii\grid\Column
{
    private const VALUE_DEFAULT = 1;

    /**
     * @var string|\Closure|null Value for the column.
     * If it's a Closure, it will be executed with ($model, $key, $index).
     * If null, no value is rendered after the icon.
     */
    public $value;

    /**
     * @var array HTML options for the header cell
     */
    public $headerOptions = ['class' => 'icon-column text-center'];

    /**
     * @var array HTML options for the content cell
     */
    public $contentOptions = ['class' => 'text-center', 'valign' => 'top'];

    /**
     * @var string|null The model attribute to be used for this column.
     */
    public $attribute;

    /**
     * @var string|null The model attribute representing the start date of the range
     */
    public $start = null;

    /**
     * @var string|null The model attribute representing the end date of the range
     */
    public $end = null;

    /**
     * @var string The CSS class for the active icon (shown when the current date is within the range)
     */
    public $css_icon_class_active = 'fas fa-check text-success';

    /**
     * @var string The CSS class for the inactive icon (shown when the current date is outside the range)
     */
    public $css_icon_class_inactive = 'fa-regular fa-circle-xmark text-danger';

    /**
     * @var string|null Text displayed when hovering over the active icon
     */
    public $css_icon_class_active_text = null;

    /**
     * @var string|null Text displayed when hovering over the inactive icon
     */
    public $css_icon_class_inactive_text = null;

    /**
     * @var string|null Custom label for the column header. If not set, defaults to the attribute's label.
     */
    public $label = '';

    /**
     * @var bool Whether to encode the label with HTML entities.
     */
    public $encodeLabel = true;

    /**
     * @var bool Whether to allow sorting for this column.
     */
    public $enableSorting = true;

    /**
     * @var array The HTML attributes for the sort link in the header cell.
     */
    public $sortLinkOptions = [];

    /**
     * @var string The template for rendering the cell content.
     */
    public $template = '{icon} <div><small>{text}</small></div>'; // Default template

    /**
     * Initializes the column.
     * 
     * @throws InvalidConfigException if the "start" or "end" properties are not set
     */
    public function init()
    {
        parent::init();

        if ($this->start === null) {
            throw new InvalidConfigException('The "start" property must be set.');
        }

        if ($this->end === null) {
            throw new InvalidConfigException('The "end" property must be set.');
        }

        if (!$this->css_icon_class_active_text) {
            $this->css_icon_class_active_text = Yii::t('app', 'in business');
        }

        if (!$this->css_icon_class_inactive_text) {
            $this->css_icon_class_inactive_text = Yii::t('app', 'out of business');
        }
    }

    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || ($this->label === null && $this->attribute === null)) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();

        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if (
            $this->attribute !== null && $this->enableSorting &&
            ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)
        ) {
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label]));
        }

        return $label;
    }

    protected function getHeaderCellLabel()
    {
        $provider = $this->grid->dataProvider;

        if ($this->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                $modelClass = $provider->query->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                $modelClass = $provider->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } elseif ($this->grid->filterModel !== null && $this->grid->filterModel instanceof Model) {
                $label = $this->grid->filterModel->getAttributeLabel($this->filterAttribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    $label = $model->getAttributeLabel($this->attribute);
                } else {
                    $label = Inflector::camel2words($this->attribute);
                }
            }
        } else {
            $label = $this->label;
        }

        return $label;
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        // Initialize icon class and tooltip
        $iconClass = $this->css_icon_class_inactive; // Default to inactive
        $tooltipText = $this->css_icon_class_inactive_text; // Default tooltip for inactive
        $displayText = '';

        // Process the value if a closure is provided
        if ($this->value instanceof \Closure) {
            // Call the closure with the model, key, and index
            $value = call_user_func($this->value, $model, $key, $index);
        } else {
            // If not a closure, use the value as-is
            $value = $this->value;
        }

        // Check if either the start or end attributes are set
        $start = $this->start !== null ? $model->{$this->start} : null;
        $end = $this->end !== null ? $model->{$this->end} : null;

        // Get the current date
        $now = new \DateTime();

        // Determine icon class and tooltip based on the date conditions
        if ($start === null && $end === null) {
            // Wenn beide NULL => automatisch aktiv
            $iconClass = $this->css_icon_class_active;
            $tooltipText = $this->css_icon_class_active_text;
            $displayText = Yii::t('app', 'no start & end set');
        } elseif ($start !== null || $end !== null) {
            // Case where both start and end are present
            if ($start !== null && $end !== null) {
                if (DateHelper::isDateOverToday($end)) {
                    // End date is in the past, item is inactive
                    $iconClass = $this->css_icon_class_inactive;
                    $tooltipText = $this->css_icon_class_inactive_text;
                    $displayText = Yii::t('app', 'over since {date}', [
                        'date' => Yii::$app->formatter->asDate($end)
                    ]);
                } elseif (DateHelper::isDateOverToday($start)) {
                    // Item is active
                    $iconClass = $this->css_icon_class_active;
                    $tooltipText = $this->css_icon_class_active_text;
                    $displayText = Yii::t('app', 'active since {date}', [
                        'date' => Yii::$app->formatter->asDate($start)
                    ]);
                } else {
                    // Start date is in the future, item is not yet active
                    $iconClass = $this->css_icon_class_inactive;
                    $tooltipText = $this->css_icon_class_inactive_text;
                    $displayText = Yii::t('app', 'will be active since {date}', [
                        'date' => Yii::$app->formatter->asDate($start)
                    ]);
                }
            } elseif ($start !== null) {
                // Case where only start date is present
                if (DateHelper::isDateOverToday($start)) {
                    $iconClass = $this->css_icon_class_active;
                    $tooltipText = $this->css_icon_class_active_text;
                    $displayText = Yii::t('app', 'active since {date}', [
                        'date' => Yii::$app->formatter->asDate($start)
                    ]);
                } else {
                    $iconClass = $this->css_icon_class_inactive;
                    $tooltipText = $this->css_icon_class_inactive_text;
                    $displayText = Yii::t('app', 'will be active since {date}', [
                        'date' => Yii::$app->formatter->asDate($start)
                    ]);
                }
            } elseif ($end !== null) {
                // Case where only end date is present
                if (!DateHelper::isDateOverToday($end)) {
                    $iconClass = $this->css_icon_class_active;
                    $tooltipText = $this->css_icon_class_active_text;
                    $displayText = Yii::t('app', 'active until {date}', [
                        'date' => Yii::$app->formatter->asDate($end)
                    ]);
                } else {
                    $iconClass = $this->css_icon_class_inactive;
                    $tooltipText = $this->css_icon_class_inactive_text;
                    $displayText = Yii::t('app', 'over since {date}', [
                        'date' => Yii::$app->formatter->asDate($end)
                    ]);
                }
            }
        }


        // Replace placeholders in the template with actual values
        $content = str_replace('{icon}', Html::tag('span', '', [
            'class' => $iconClass,
            'title' => $tooltipText,
            'aria-hidden' => 'true',
        ]), $this->template);

        // Add display text to the content
        if ($displayText) {
            $content = str_replace('{text}', $displayText, $content);
        } else {
            // Remove {text} if no display text
            $content = str_replace('{text}', '', $content);
        }

        return $content;
    }

}

<?php

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * DateTimeColumn extends the DataColumn to provide date and time formatting and sorting in a GridView.
 * It formats date values based on a customizable format and includes support for relative time display.
 * This column can also be used with a date range filter.
 * 
 * @package strtob\yii2GridviewColumns
 */
class DateTimeColumn extends DataColumn
{
    /**
     * @var string The date format used to display date and time values.
     *             It defaults to 'php:d.m.Y H:i' but can be customized per column instance.
     */
    public $dateFormat = 'php:d.m.Y H:i';

    /**
     * @var string|null An optional icon class to display before the relative time value.
     */
    public $iconRelativeTime = 'fa fa-clock'; // Default icon for the relative time

    /**
     * @var bool Whether to show absolute time next to relative time.
     */
    public $showAbsoluteTime = true;

    /**
     * @var array HTML options for the column header, setting a default CSS class.
     */
    public $headerOptions = ['class' => 'datetime-column'];

    /**
     * Renders the header cell content.
     * It checks for sorting options and encodes the label if specified.
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

        if ($this->attribute !== null && $this->enableSorting &&
            ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            return $sort->link($this->attribute, ['label' => $label]);
        }

        return $label;
    }

    /**
     * Gets the header label for the column based on the attribute, data provider, and model.
     * 
     * @return string The label for the header cell.
     */
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

    /**
     * Renders the content for a data cell.
     * Formats the attribute value as a date according to `dateFormat` and displays relative time.
     * 
     * @param mixed $model The data model.
     * @param mixed $key The key associated with the data item.
     * @param int $index The zero-based index of the data item among the items array returned by data provider.
     * 
     * @return string The rendered data cell content.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $attribute = $this->attribute;
        $content = $this->value !== null ? call_user_func($this->value, $model, $key, $index) : $this->resolveAttributeValue($model, $attribute);

        if ($content !== null) {
            // Format date using the custom `dateFormat` property
            $formattedDate = Yii::$app->formatter->asDate($content, $this->dateFormat);
            $relativeTime = Yii::$app->formatter->asRelativeTime($content);

            // Prepare icon HTML
            $iconHtml = Html::tag('i', '', ['class' => $this->iconRelativeTime]);

            // Prepare output
            $output = $formattedDate;

            if ($this->showAbsoluteTime) {
                $output .= '<div style="font-size: small; color: gray;">' 
                . $iconHtml . ' ' . Html::encode($relativeTime) 
                . '</div>';
            }

            return $output;
        }

        return '';
    }

    /**
     * Resolves the value of the specified attribute from the model.
     * Supports nested attributes using dot notation.
     * 
     * @param mixed $model The data model.
     * @param string $attribute The attribute name, which may be in dot notation for nested attributes.
     * 
     * @return mixed|null The resolved attribute value, or null if not found.
     */
    protected function resolveAttributeValue($model, $attribute)
    {
        $attributeParts = explode('.', $attribute);
        $value = $model;

        foreach ($attributeParts as $part) {
            if (is_object($value) && isset($value->{$part})) {
                $value = $value->{$part};
            } elseif (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return null;
            }
        }

        return $value;
    }
}

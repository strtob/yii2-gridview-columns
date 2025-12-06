<?php

namespace strtob\yii2GridviewColumns;

use Yii;
use yii\helpers\Html;
use yii\grid\Column;

/**
 * @link http://www.re-soft.de
 * @copyright Copyright (c) Tobias Streckel
 * @license MIT
 */
class BadgeColumn extends Column
{
    // Define constants for badge types
    const BADGE_TYPE_DEFAULT = 'secondary';
    const BADGE_TYPE_SUCCESS = 'success';
    const BADGE_TYPE_PRIMARY = 'primary';
    const BADGE_TYPE_WARNING = 'warning';
    const BADGE_TYPE_INFO = 'info';
    const BADGE_TYPE_DANGER = 'danger';

    // Default Badge Type
    const VALUE_DEFAULT = self::BADGE_TYPE_DEFAULT;

    public $attribute;

    public $value;

    // Label property for the column header
    public $label;

    /**
     * @var string The type of the badge (success, primary, etc.).
     */
    public $badgeType = self::VALUE_DEFAULT;

    /**
     * @var string The value to display inside the badge.
     */
    public $badgeValue;

    /**
     * @var \Closure|null an optional closure function to manipulate the value.
     * This closure will receive three parameters: `$model`, `$key`, and `$index`.
     */
    public $valueCallback = null;

    /**
     * Renders the header for the column, including the label.
     *
     * @return string The header HTML.
     */
    protected function renderHeaderCellContent()
    {
        return $this->label ? Html::encode($this->label) : '';
    }

    /**
     * Renders the data for the column.
     *
     * @param mixed $model The data model.
     * @param integer $key The key of the data model.
     * @param integer $index The index of the row.
     * @return string The rendering result.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        // Use the closure to manipulate the value if available, otherwise use attribute or set value
        if ($this->valueCallback instanceof \Closure) {
            $value = call_user_func($this->valueCallback, $model, $key, $index);
        } else {
            $value = $this->value instanceof \Closure ? call_user_func($this->value, $model, $index) : $model->{$this->attribute};
        }

        // Use the value passed to the column if specified, otherwise use the model's value
        $badgeContent = $this->badgeValue ? $this->badgeValue : $value;

        // Determine badge type based on value or configuration
        $badgeClass = $this->getBadgeClass($badgeContent);

        // Return the HTML for the Bootstrap badge
        return Html::tag('span', $badgeContent, ['class' => 'badge bg-' . $badgeClass]);
    }

    /**
     * Determines the badge class based on the badge value or type.
     *
     * @param string $badgeContent The value to display inside the badge.
     * @return string The CSS class for the badge.
     */
    protected function getBadgeClass($badgeContent)
    {
        // Example: You can add additional logic to choose the badge type based on the content
        switch ($badgeContent) {
            case 'success':
                return self::BADGE_TYPE_SUCCESS;
            case 'primary':
                return self::BADGE_TYPE_PRIMARY;
            case 'warning':
                return self::BADGE_TYPE_WARNING;
            case 'info':
                return self::BADGE_TYPE_INFO;
            case 'danger':
                return self::BADGE_TYPE_DANGER;
            default:
                return $this->badgeType; // Default badge type
        }
    }
}

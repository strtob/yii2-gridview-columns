# yii2-gridview-columns

Advanced GridView Columns (Custom Columns) for Yii2
===================================================

This package provides a collection of specialized GridView columns for Yii2, covering common use cases and significantly enhancing the display and interactivity of data tables.

## Installation

```bash
composer require strtob/yii2-gridview-columns
```

## Overview of Included Columns

| Class                      | Description |
|----------------------------|-------------|
| AmountColumn               | Displays formatted amounts with sum calculation, color coding, and badges. |
| BadgeColumn                | Renders values as colored (Bootstrap) badges. |
| BooleanColumn              | Shows boolean values as icons/text (e.g., On/Off, Active/Inactive, etc.). |
| CountryColumn              | Displays country codes/names, optionally with flag. |
| DateColumn                 | Renders formatted date values. |
| DateTimeColumn             | Shows date and time, including relative time. |
| DateTimeRangeActiveColumn  | Indicates if a time range is currently active (e.g., with icon). |
| DateTimeRangeColumn        | Displays a time range (from/to), including filter/search. |
| DebitCreditColumn          | Shows debit/credit amounts with color coding. |
| EventDurationColumn        | Visualizes event duration (donut/square). |
| GeoIpColumn                | Displays Geo-IP information, optionally with popover. |
| IconColumn                 | Renders icons (e.g., FontAwesome) depending on value. |
| ImageColumn                | Displays images/thumbnails in the table. |
| LanguageFlagColumn         | Shows language or country flags (CSS or image). |
| LocationColumn             | Renders formatted address data. |
| PercentageCircleColumn     | Visualizes percentage values as a circle chart. |
| PercentageGraphColumn      | Shows percentage values as a Bootstrap progress bar. |
| SparklineColumn            | Displays small line charts (sparklines) for data series. |
| TimestampColumn            | Shows Unix timestamps as date/time. |
| UnixDateTimeColumn         | Shows Unix timestamps as formatted date/time. |
| UserColumn                 | Displays user information (e.g., image, name). |
| ValidtyStatusColumn        | Shows the status of a time period (valid/expired) with icon and text. |


## Example Usage

```php
use strtob\yii2GridviewColumns\AmountColumn;
use strtob\yii2GridviewColumns\BadgeColumn;
// ... more columns ...

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => AmountColumn::class,
            'attribute' => 'amount',
            // more options ...
        ],
        [
            'class' => BadgeColumn::class,
            'attribute' => 'status',
            // more options ...
        ],
        // ... more columns ...
    ],
]);
```

## Details for Each Column

### AmountColumn
- Displays amounts with formatting, sum in footer, color coding, and optional badges.
- Options: `attribute`, `footer`, `badgeType`, `colorize`, ...

### BadgeColumn
- Renders values as colored badges (e.g., for status).
- Options: `attribute`, `badgeType`, `label`, ...

### BooleanColumn
- Shows boolean values as icon/text (e.g., On/Off, Active/Inactive, etc.).
- Options: `attribute`, `type` (various display types), ...

### CountryColumn
- Displays country codes/names, optionally with flag.
- Options: `attribute`, `relation`, `label`, ...

### DateColumn
- Renders formatted date values.
- Options: `attribute`, `label`, `format`, ...

### DateTimeColumn
- Shows date and time, including relative time.
- Options: `dateFormat`, `iconRelativeTime`, `showAbsoluteTime`, ...

### DateTimeRangeActiveColumn
- Indicates if a time range is currently active (e.g., with icon).
- Options: `start`, `end`, `css_icon_class_active`, ...

### DateTimeRangeColumn
- Displays a time range (from/to), including filter/search.
- Options: `attribute`, `label`, ...

### DebitCreditColumn
- Shows debit/credit amounts with color coding.
- Options: `attribute`, `label`, `prefix`, ...

### EventDurationColumn
- Visualizes event duration as donut or square.
- Options: `startAttribute`, `endAttribute`, `allDayAttribute`, ...

### GeoIpColumn
- Displays Geo-IP information, optionally with popover.
- Options: `attribute`, `showPopover`, `popoverOptions`, ...

### IconColumn
- Renders icons depending on value (e.g., status icons).
- Options: `attribute`, `iconClass`, `iconClassInactive`, ...

### ImageColumn
- Displays images/thumbnails in the table.
- Options: `attribute`, `imageOptions`, ...

### LanguageFlagColumn
- Shows language or country flags (CSS or image).
- Options: `attribute`, `flagType`, `flagImagePath`, ...

### LocationColumn
- Renders formatted address data.
- Options: `attribute`, `streetAttribute`, `addressReturnfunction`, ...

### PercentageCircleColumn
- Visualizes percentage values as a circle chart.
- Options: `value`, `text`, `circleDiameter`, ...

### PercentageGraphColumn
- Shows percentage values as a Bootstrap progress bar.
- Options: `value`, `barLabel`, `barColor`, ...

### SparklineColumn
- Displays small line charts (sparklines) for data series.
- Options: `value`, `sparklineOptions`, `showTrend`, ...

### TimestampColumn
- Shows Unix timestamps as date/time.
- Options: `attribute`, `format`, ...

### UnixDateTimeColumn
- Shows Unix timestamps as formatted date/time.
- Options: `format`, `timezone`, `showRelativeTime`, ...

### UserColumn
- Displays user information (e.g., image, name).
- Options: `attribute`, `label`, ...

### ValidtyStatusColumn
- Shows the status of a time period (valid/expired) with icon and text.
- Options: `attribute`, `template`, ...


## License
MIT

(c) Tobias Streckel

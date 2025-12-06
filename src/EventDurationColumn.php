<?php
namespace strtob\yii2GridviewColumns;

use Yii;
use yii\grid\Column;
use yii\helpers\Html;

class EventDurationColumn extends Column
{
    public $label;

    /**
     * Attribute für Start- und Endzeit (MySQL DATETIME)
     */
    public $startAttribute;
    public $endAttribute;

    /**
     * Optional: Attribut für Ganztages-Event (bool)
     */
    public $allDayAttribute = null;

    public $encodeLabel = true;

    // Donut Einstellungen
    public $circleSize = 40;
    public $donutThickness = 0.25;

    // Quadrat Einstellungen für All-Day / Multi-Day
    public $squareSize = 40;
    public $squareBorderColor = '#268CDD'; // default
    public $squareTextColor = '#007bff';   // default

    public $valign = 'middle';
    public $halign = 'center';

    protected function renderHeaderCellContent()
    {
        if ($this->header !== null) {
            return $this->header;
        }

        $label = $this->label ?? $this->startAttribute;
        return $this->encodeLabel ? Html::encode($label) : $label;
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        // Flexbox Maps
        $valignMap = ['top' => 'flex-start', 'middle' => 'center', 'bottom' => 'flex-end'];
        $halignMap = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'];

        // Wrapper für vertikale/horizontale Zentrierung
        $wrapperOptions = [
            'class' => 'd-flex my-2',
            'style' => "
                height:100%;
                align-items:{$valignMap[$this->valign]};
                justify-content:{$halignMap[$this->halign]};
            "
        ];

        // Ganztages-/Multi-Day Event
        if ($this->allDayAttribute && !empty($model->{$this->allDayAttribute})) {
            $start = new \DateTime($model->{$this->startAttribute});
            $end = new \DateTime($model->{$this->endAttribute});
            $interval = $start->diff($end);
            $days = max(1, $interval->days);

            $display = $days . ' ' . Yii::t('app', 'd');

            $square = Html::tag('div', $display, [
                'style' => "
                    width:{$this->squareSize}px;
                    height:{$this->squareSize}px;
                    border:3px solid {$this->squareBorderColor};
                    background-color:transparent;
                    color:{$this->squareTextColor};
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:0.7rem;
                    font-weight:bold;
                    border-radius:4px;
                "
            ]);

            return Html::tag('div', $square, $wrapperOptions);
        }

        // Normale Dauer in Minuten
        $start = new \DateTime($model->{$this->startAttribute});
        $end = new \DateTime($model->{$this->endAttribute});
        $interval = $start->diff($end);
        $minutes = ($interval->days * 24 + $interval->h) * 60 + $interval->i;
        if ($minutes <= 0) {
            return '';
        }

        $hours = round($minutes / 60, 1);
        $deg = $minutes < 60 ? $minutes * 6 : 360;

        // Dynamische Ringbreite
        $hoursTotal = $minutes / 60;
        $baseThickness = $this->donutThickness;
        if ($hoursTotal <= 1) {
            $dynamicThickness = $baseThickness;
        } elseif ($hoursTotal >= 4) {
            $dynamicThickness = 0.5; // max Ringbreite
        } else {
            $dynamicThickness = $baseThickness + (($hoursTotal - 1) / 3) * (0.5 - $baseThickness);
        }

        $innerSize = $this->circleSize * (1 - $dynamicThickness);

        // Donut
        $circle = Html::tag(
            'div',
            Html::tag('div', $hours . ' ' . Yii::t('app', 'h'), [
                'style' => "
                    width:{$innerSize}px;
                    height:{$innerSize}px;
                    border-radius:50%;
                    background-color:#fff;
                    position:absolute;
                    top:50%; left:50%;
                    transform:translate(-50%,-50%);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:0.7rem;
                    font-weight:bold;
                    color:#28a745;
                "
            ]),
            [
                'style' => "
                    width:{$this->circleSize}px;
                    height:{$this->circleSize}px;
                    border-radius:50%;
                    background:conic-gradient(#28a745 {$deg}deg, #e9ecef 0deg);
                    position:relative;
                "
            ]
        );

        return Html::tag('div', $circle, $wrapperOptions);
    }
}

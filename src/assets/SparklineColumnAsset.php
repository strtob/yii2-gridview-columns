<?php

namespace strtob\yii2GridviewColumns\assets;

use yii\web\AssetBundle;


class SparklineColumnAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../resources/SparklineColumn/js';

    // Register the languageFlags.css file
    public $js = [
        'SparklineColumn.js',
    ];


    public $depends = [
        \yii\web\YiiAsset::class,
        \machour\sparkline\SparklineAsset::class,    
    ];
}

<?php

namespace strtob\yii2GridviewColumns\assets;

use yii\web\AssetBundle;

class LanguageFlagAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/../resources/LanguageFlag';

    // Register the languageFlags.css file
    public $css = [
        'language-flags.css',
    ];


    public $depends = [
        'yii\web\YiiAsset',      
    ];
}

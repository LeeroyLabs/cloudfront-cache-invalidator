<?php
namespace leeroy\cloudfrontcacheinvalidator\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PluginAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        $this->sourcePath = "@leeroy/cloudfrontcacheinvalidator/assetbundles/assets";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/admin.js'
        ];

        $this->css = [
            'css/style.css'
        ];

        parent::init();
    }
}

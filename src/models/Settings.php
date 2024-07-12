<?php

namespace leeroy\cloudfrontcacheinvalidator\models;

use Craft;
use craft\base\Model;
use leeroy\cloudfrontcacheinvalidator\CloudfrontCacheInvalidator;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    const ZONE_ID    = '';
    const ACCOUNT_ID = '';
    const API_KEY    = '';

    public string $zoneId    = self::ZONE_ID;
    public string $accountId = self::ACCOUNT_ID;
    public string $apiKey    = self::API_KEY;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['zoneId', 'accountId'], 'required'],
        ];
    }
}
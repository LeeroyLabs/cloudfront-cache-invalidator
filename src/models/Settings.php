<?php

namespace leeroy\cloudfrontcacheinvalidator\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public string $secretKey = '';
    public string $apiToken = '';
    public string $distribId = '';

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
            [['secretKey', 'apiToken', 'distribId'], 'required'],
        ];
    }
}

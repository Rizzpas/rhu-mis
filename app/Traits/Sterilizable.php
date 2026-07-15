<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sterilizable
{
    /**
     * Boot the trait.
     */
    protected static function bootSterilizable()
    {
        static::saving(function ($model) {
            $fieldsToCapitalize = $model->sterilizable ?? [];

            foreach ($fieldsToCapitalize as $field) {
                if ($model->isDirty($field) && is_string($model->$field)) {
                    // Title Case: e.g., "jonathan smith" -> "Jonathan Smith"
                    $model->$field = Str::title(trim($model->$field));
                }
            }
        });
    }
}

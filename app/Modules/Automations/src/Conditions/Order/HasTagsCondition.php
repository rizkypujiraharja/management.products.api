<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class HasTagsCondition extends BaseOrderConditionAbstract
{
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        if (trim($expected_value) === '') {
            // empty value automatically invalidates query
            return $query->whereRaw('( "has_tags_condition"="" )');
        }

        $tagsArray = explode(',', $expected_value);

        return $query->withAllTags($tagsArray);
    }
}

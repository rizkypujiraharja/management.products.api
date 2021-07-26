<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Module.
 *
 * @property int         $id
 * @property string      $service_provider_class
 * @property bool        $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Module newModelQuery()
 * @method static Builder|Module newQuery()
 * @method static Builder|Module query()
 * @method static Builder|Module whereCreatedAt($value)
 * @method static Builder|Module whereEnabled($value)
 * @method static Builder|Module whereId($value)
 * @method static Builder|Module whereServiceProviderClass($value)
 * @method static Builder|Module whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Module extends Model
{
    protected $fillable = [
        'name',
        'description',
        'service_provider_class',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    protected $appends = [
        'enabled' => false,
    ];
}

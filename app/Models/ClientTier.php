<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ClientTier
 *
 * @property int $id
 * @property string $tier
 * @property float $discount
 * @property float $score
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Collection|Client[] $clients
 */
class ClientTier extends Model
{
    use SoftDeletes;

    protected $table = 'client_tiers';

    protected $casts = [
        'discount' => 'float',
        'score' => 'float',
    ];

    protected $fillable = [
        'tier',
        'discount',
        'score',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}

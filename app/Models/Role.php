<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;          // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany;    // Import BelongsToMany

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Defaults to: 'roles'
     */
    // protected $table = 'roles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Relationship: A Role has many Users.
     */
    public function users(): HasMany
    {
        // hasMany(RelatedModel, foreign_key_on_related_model, local_key_on_this_model)
        // We specify the foreign key ('role_id' on the users table)
        // and the local key ('role_id' on the roles table).
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    /**
     * Relationship: A Role belongs to many Permissions.
     */
    public function permissions(): BelongsToMany
    {
        // belongsToMany(RelatedModel, pivot_table_name, foreign_pivot_key, related_pivot_key)
        // Foreign Pivot Key ('role_id') is the foreign key of the current model in the pivot table.
        // Related Pivot Key ('permission_id') is the foreign key of the related model in the pivot table.
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
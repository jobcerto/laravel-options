<?php
namespace Jobcerto\Options;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'options';
    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public $casts = [
        'value' => 'array',
    ];

}

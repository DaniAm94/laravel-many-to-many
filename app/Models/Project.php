<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['title', 'description', 'slug', 'is_completed', 'type_id'];

    public function getFormattedDate($column, $format = 'd-m-Y')
    {
        return Carbon::create($this->$column)->format($format);
    }
    public function getAbstract($length)
    {
        $abstract = substr($this->description, 0, $length);
        if (strlen($this->description) > 100) {
            $abstract = $abstract . '...';
        }

        return  $abstract;
    }
    public function printImage()
    {
        return asset('storage/' . $this->image);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Query scope
    public function scopeStatusFilter(Builder $query, $status)
    {
        if (!$status) return $query;
        $value = $status === 'completed';
        return $query->whereIsCompleted($value);
    }
    public function scopeTypeFilter(Builder $query, $type_id)
    {
        if (!$type_id) return $query;
        return $query->whereTypeId($type_id);
    }
    public function scopeTechnologyFilter(Builder $query, $technology_id)
    {
        if (!$technology_id) return $query;
        return $query->whereHas('technologies', function ($query) use ($technology_id) {
            $query->where('technologies.id', $technology_id);
        });
    }
}

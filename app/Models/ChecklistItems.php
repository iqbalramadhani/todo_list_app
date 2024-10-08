<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItems extends Model
{
    use HasFactory;

    protected $table = 'checklist_items';
    protected  $fillable = ['item', 'status','checklist_id'];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    protected $hidden = [
        'user',
    ];


    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'checklist_id')->withDefault();
    }
}

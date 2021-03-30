<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Candidate extends MongoModel
{
    use HasFactory;
    protected $fillable = [
        'name',
        'source',
        'owner',
        'created_by',
    ];

    public function user() {
      //return $this->belongsTo(App\Models\User::class);
    	return $this->belongsTo(User::class,'created_by');
    }
}

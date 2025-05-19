<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Specify the table if needed
    protected $table = 'reports';

    // Fillable attributes for the report
    protected $fillable = [
        'item_name',
        'message',
    ];
}

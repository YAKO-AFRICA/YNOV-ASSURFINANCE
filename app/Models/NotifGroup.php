<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifGroup extends Model
{
    use HasFactory;

    protected $table = 'notif_groups';

    protected $fillable = ['code_group', 'name', 'branche', 'etat'];

}

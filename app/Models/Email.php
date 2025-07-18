<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Email extends Model
{
use SoftDeletes;

 protected $fillable = ['to', 'subject', 'body', 'status', 'sent_at'];

protected $dates = ['deleted_at'];
}

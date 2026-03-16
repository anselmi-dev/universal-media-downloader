<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadRequest extends Model
{
    public const STATUS_SUCCESS = 'success';

    public const STATUS_NO_MEDIA = 'no_media';

    public const STATUS_ERROR = 'error';

    protected $fillable = [
        'url',
        'platform',
        'status',
        'error_message',
        'items_count',
        'ip_address',
        'user_agent',
        'site_host',
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoices_attachments extends Model
{

    protected $fillable = [
        'id_invoice',
        'invoice_number',
        'file_name',
        'id_invoice',
        'created_by'
    ];
}

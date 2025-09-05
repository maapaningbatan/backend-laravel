<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Model;

class LibSupplier extends Model
{

    protected $table = 'lib_supplier';
    protected $primaryKey = 'Supplier_Id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Supplier_Name',
        'Supplier_Address',
        'Contract_Person',
        'Contract_No',
        'Tin_No',
        'Createdby',
        'Updatedby',
        'supplier_no',
        'supplier_email'
    ];
}

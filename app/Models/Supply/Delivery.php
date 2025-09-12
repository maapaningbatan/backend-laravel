<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Library\LibSupplier;
use App\Models\Library\LibEmployee;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_delivery';

    protected $primaryKey = 'delivery_id'; // ensure correct PK

    protected $fillable = [
        'iar_number','purchase_order_number','purchase_date','pr_number','pr_date',
        'supplier','warehouse','receiving_office','code_number','purpose','invoice_no','invoice_total_amount',
        'po_amount','po_date','dr_no','dr_date','ris_no','ris_date',
        'ors_no','ors_date','dv_no','dv_date','prepared_by', 'status', 'created_by', 'updated_by', 'deleted_by'
    ];

    protected $dates = ['deleted_at']; // ✅ tell Laravel to treat it as Carbon

    // Items relation
    public function items()
    {
        return $this->hasMany(ItemDelivery::class, 'delivery_id', 'delivery_id');
    }

    // Supplier relation
    public function supplierInfo()
    {
        return $this->belongsTo(LibSupplier::class, 'supplier', 'Supplier_Id')
                    ->withDefault([
                        'Supplier_Name' => 'N/A'
                    ]);
    }

    // Prepared by employee relation
    public function preparedByEmployee()
    {
        return $this->belongsTo(LibEmployee::class, 'prepared_by', 'Employee_PK')
                    ->withDefault([
                        'full_name' => 'N/A'
                    ]);
    }

    // Cascade delete items when delivery is deleted
    protected static function booted()
    {
        static::deleting(function ($delivery) {
            $delivery->items()->delete();
        });
    }
}

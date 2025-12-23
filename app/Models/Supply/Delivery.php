<?php

namespace App\Models\Supply;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Library\LibSupplier;
use App\Models\Library\LibWarehouse;
use App\Models\Tables\TblUser;

class Delivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_delivery';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'iar_number','purchase_order_number','purchase_date','pr_number','pr_date',
        'supplier_id','warehouse_id','receiving_office','code_number','purpose',
        'invoice_no','invoice_total_amount','invoice_date','unit','po_amount','po_date',
        'dr_no','dr_date','ris_no','ris_date','ors_no','ors_date','dv_no','dv_date',
        'prepared_by','status','updated_by','deleted_by'
    ];

    protected $dates = ['deleted_at'];

    // ✅ Items
    public function items()
    {
        return $this->hasMany(ItemDelivery::class, 'delivery_id', 'id');
    }

    // ✅ Supplier
 public function supplierInfo()
{
    return $this->belongsTo(LibSupplier::class, 'supplier_id', 'id')
        ->withDefault(['supplier_name' => 'N/A']);
}

    // ✅ Warehouse
    public function warehouse()
    {
        return $this->belongsTo(LibWarehouse::class, 'warehouse_id', 'id')
                    ->withDefault(['warehouse_desc' => 'N/A']);
    }

    // ✅ Prepared By
    public function preparedByUser()
    {
        return $this->belongsTo(TblUser::class, 'prepared_by', 'id')
                    ->select('id', 'first_name', 'middle_name', 'last_name', 'username');
    }

    // ✅ Cascade delete
    protected static function booted()
    {
        static::deleting(function ($delivery) {
            $delivery->items()->delete();
        });
    }
}

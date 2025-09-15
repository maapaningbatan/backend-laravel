<?php

namespace App\Http\Controllers\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property\PropertyCard;
use App\Models\Property\SemiExCard;

class PropertyIssuanceController extends Controller
{
    // List all property issuance records
    public function index(Request $request)
    {
        // PPE / Property Cards
        $propertyCards = PropertyCard::with('supply')->get()->map(function ($card) {
            return [
                'id'                      => $card->property_card_id,
                'reference_no'            => $card->reference_no,
                'receipt_qty'             => $card->receipt_qty,
                'Supplies_Desc'           => optional($card->supply)->Supplies_Desc ?? 'N/A',
                'type'                    => 'PPE',
                'property_card_id'        => $card->property_card_id,
                'semi_expandable_card_id' => null,
            ];
        });

        // ICS / Semi-Expandable Cards
        $semiCards = SemiExCard::with('supply')->get()->map(function ($card) {
            return [
                'id'                      => $card->semi_expandable_card_id,
                'reference_no'            => $card->reference_no,
                'receipt_qty'             => $card->receipt_qty,
                'Supplies_Desc'           => optional($card->supply)->Supplies_Desc ?? 'N/A',
                'type'                    => 'ICS',
                'property_card_id'        => null,
                'semi_expandable_card_id' => $card->semi_expandable_card_id,
            ];
        });

        $issuance = $propertyCards->merge($semiCards);

        return response()->json($issuance);
    }

    // Show single property issuance record
    public function show(Request $request, $id)
{
    $type = $request->query('type');

    if ($type === 'PPE') {
        $card = PropertyCard::with(['supply', 'itemdelivery.unit', 'itemdelivery.brand'])->find($id);

        if (!$card) {
            return response()->json(['message' => 'PPE record not found'], 404);
        }

        return response()->json([
            'id'                     => $card->property_card_id,
            'property_card_id'       => $card->property_card_id,
            'reference_no'           => $card->reference_no,
            'receipt_qty'            => $card->receipt_qty,
            'Supplies_Desc'          => optional($card->supply)->Supplies_Desc ?? 'N/A',
            'type'                   => 'PPE',
            'additional_description' => optional($card->itemdelivery)->additional_description ?? '',
            'brand'                  => optional(optional($card->itemdelivery)->brand)->Brand_Description ?? '',
            'model'                  => optional($card->itemdelivery)->model ?? '',
            'unit'                   => optional(optional($card->itemdelivery)->unit)->Unit_Description ?? '',
            'unit_value'             => optional($card->itemdelivery)->unit_value ?? '',
        ]);
    }

    if ($type === 'ICS') {
        $card = SemiExCard::with(['supply', 'itemdelivery.unit', 'itemdelivery.brand'])->find($id);

        if (!$card) {
            return response()->json(['message' => 'ICS record not found'], 404);
        }

        return response()->json([
            'id'                      => $card->semi_expandable_card_id,
            'semi_expandable_card_id' => $card->semi_expandable_card_id,
            'reference_no'            => $card->reference_no,
            'receipt_qty'             => $card->receipt_qty,
            'Supplies_Desc'           => optional($card->supply)->Supplies_Desc ?? 'N/A',
            'type'                    => 'ICS',
            'additional_description'  => optional($card->itemdelivery)->additional_description ?? '',
            'brand'                   => optional(optional($card->itemdelivery)->brand)->Brand_Description ?? '',
            'model'                   => optional($card->itemdelivery)->model ?? '',
            'unit'                    => optional(optional($card->itemdelivery)->unit)->Unit_Description ?? '',
            'unit_value'              => optional($card->itemdelivery)->unit_value ?? '',
        ]);
    }

    return response()->json(['message' => 'Invalid type'], 400);
}

}

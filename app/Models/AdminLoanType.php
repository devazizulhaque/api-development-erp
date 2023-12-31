<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLoanType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function createOrupdate($request, $id = null)
    {
        AdminLoanType::updateorcreate(
            [
                'id' => $id,
            ],
            [
                'code' => $request->code,
                'english_name' => $request->english_name,
                'short_english' => $request->short_english,
                'bangla_name' => $request->bangla_name,
                'short_bangla' => $request->short_bangla,
                'arabic_name' => $request->arabic_name,
                'short_arabic' => $request->short_arabic,
                'is_default' => $request->is_default,
                'rating' => $request->rating,
                'is_active' => $request->is_active,
                'is_draft' => $request->is_draft,
                'is_delete' => $request->is_delete,
                'action_user_id' => $request->action_user_id,
                'last_updated' => $request->last_updated,
            ]
        );
    }
}

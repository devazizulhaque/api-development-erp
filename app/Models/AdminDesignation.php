<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDesignation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function createOrUpdate($request, $id = null)
    {
        AdminDesignation::updateorcreate(
            [
                'id' => $id
            ],
            [
                'code' => $request->code,
                'english_name' => $request->english_name,
                'short_english' => $request->short_english,
                'is_default' => $request->is_default,
                'arabic_name' => $request->arabic_name,
                'short_arabic' => $request->short_arabic,
                'bangla_name' => $request->bangla_name,
                'short_bangla' => $request->short_bangla,
                'rating' => $request->rating,
                'is_active' => $request->is_active,
                'is_draft' => $request->is_draft,
                'last_updated' => $request->last_updated,
                'is_delete' => $request->is_delete,
                'action_user_id' => $request->action_user_id,
            ]
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'english_name',
        'short_english',
        'is_default',
        'arabic_name',
        'short_arabic',
        'bangla_name',
        'short_bangla',
        'rating',
        'is_active',
        'is_draft',
        'last_updated',
        'is_delete',
        'is_approved',
        'is_pending',
        'is_in_progress',
        'is_rejected',
        'approved_by',
        'rejected_by',
        'action_user_id',
    ];

    public static function createOrUpdate($request, $id = null)
    {
        AdminEmployee::updateorcreate(
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
                'is_approved' => $request->is_approved,
                'is_pending' => $request->is_pending,
                'is_in_progress' => $request->is_in_progress,
                'is_rejected' => $request->is_rejected,
                'approved_by' => $request->approved_by,
                'rejected_by' => $request->rejected_by,
                'action_user_id' => $request->action_user_id,
            ]
        );
    }
}

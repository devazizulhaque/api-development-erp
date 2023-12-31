<?php

namespace App\helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OverviewHelper
{
    public static function getModelOverview(Model $model, $dateColumn = 'created_at')
    {
        $data = [];
        $data['total'] = $model::count();
        $data['new'] = $model::whereDate($dateColumn, Carbon::today())->count();
        $data['draft'] = $model::where('is_draft', 1)->count();
        if (isset($data['approved'])) {
            $data['approved'] = $model::where('is_approved', 1)->count();
        }
        $data['active'] = $model::where('is_active', 1)->count();
        $data['in_active'] = $model::where('is_active', 0)->count();
        $data['update'] = $model::whereDate('last_updated', Carbon::today())
            ->where('last_updated', '<>', null)->count();
        $data['delete'] = $model::whereDate($dateColumn, Carbon::today())
            ->where('is_delete', 1)->count();
        $data['latest_records'] = $model::orderBy('id', 'DESC')->limit(5)->get();

        return $data;
    }
}
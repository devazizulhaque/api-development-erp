<?php 

namespace App\Helpers;

use App\Exports\ListExport;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class DataHelper
{
    public static function fetchPaginatedData($model, $request, $columnsToFilter, $fileName)
    {
        $query = $model::query();

        if ($request->filled('column_filter')) {
            $singleColumnFilter = json_decode($request->column_filter, true);

            if (is_array($singleColumnFilter)) {
                $query->where(function ($q) use ($singleColumnFilter) {
                    foreach ($singleColumnFilter as $column => $value) {
                        $column = trim($column);
                        $value = trim($value);
                        $q->where($column, 'like', "%$value%");
                    }
                });
            }
        }

        if ($request->filled('global_filter')) {
            $globalFilter = $request->global_filter;
            $query->where(function ($q) use ($globalFilter, $columnsToFilter) {
                foreach ($columnsToFilter as $column) {
                    $q->orWhere($column, 'like', "%$globalFilter%");
                }
            });
        }

        if ($request->filled('list')) {
            $list = $request->list;
            if ($list == 'new') {
                $query->whereDate('created_on', now()->toDateString());
            } elseif ($list == 'approved') {
                $query->where('is_approved', 1);
            } elseif ($list == 'draft') {
                $query->where('is_draft', 1);
            } elseif ($list == 'active') {
                $query->where('is_active', 1);
            } elseif ($list == 'in_active') {
                $query->where('is_active', 0);
            } elseif ($list == 'update') {
                $query->whereDate('last_updated', now()->toDateString());
            } elseif ($list == 'delete') {
                $query->whereDate('created_on', now()->toDateString())->where('is_delete', 1);
            }
        }

        // Apply Sorting
        $sortColumn = $request->filled('sort_column') ? $request->sort_column : 'id';
        $sortOrder = $request->filled('sort_order') ? $request->sort_order : 'DESC';
        $results = $query->orderBy($sortColumn, $sortOrder)->paginate(10);

        if ($request->filled('format')) {
            // Get columns for export
            $columns = $request->columns;
            $columns = explode(',', $columns);

            // Export data
            if ($request->format === 'excel') {
                $fileName .= '.xlsx';
                return Excel::download(new ListExport($results, $columns), $fileName);
            } elseif ($request->format === 'pdf') {
                $fileName .= '.pdf';
                $pdf = PDF::loadView('pdf', ['data' => $results, 'columns' => $columns]);
                return $pdf->download($fileName);
            }
        } else {
            return $results;
        }
    }
}
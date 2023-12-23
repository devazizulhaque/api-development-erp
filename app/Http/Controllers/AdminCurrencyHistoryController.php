<?php

namespace App\Http\Controllers;

use App\Exports\ListExport;
use App\Models\AdminCurrencyHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class AdminCurrencyHistoryController extends Controller
{
    public function overview(Request $request)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $data = array();
            $data['total'] = AdminCurrencyHistory::count();
            $data['new'] = AdminCurrencyHistory::whereDate('created_on', Carbon::today())->count();
            $data['draft'] = AdminCurrencyHistory::where('is_draft', 1)->count();
            $data['approved'] = AdminCurrencyHistory::where('is_approved', 1)->count();
            $data['active'] = AdminCurrencyHistory::where('is_active', 1)->count();
            $data['in_active'] = AdminCurrencyHistory::where('is_active', 0)->count();
            $data['update'] = AdminCurrencyHistory::whereDate('last_updated', Carbon::today())
                ->where('last_updated', '<>', null)->count();
            $data['delete'] = AdminCurrencyHistory::whereDate('created_on', Carbon::today())
                ->where('is_delete', 1)->count();
            $data['admin_currency_historys'] = AdminCurrencyHistory::orderBy('id', 'DESC')->limit(5)->get();

            $response = [
                'code' => '201',
                'status' => true,
                'message' => 'Admin Areas Overview retrived, successfully',
                'admin_currency_historys_overview' => $data,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'code' => '201',
                'status' => false,
                'token' => $request->header('token'),
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ];
            return response()->json($response, 200);
        }
    }

    public function admin_currency_histories(Request $request)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();

        if (!$token_check) {
            $response = [
                'code' => '201',
                'status' => false,
                'token' => $request->header('token'),
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ];

            return response()->json($response, 200);
        }

        $query = AdminCurrencyHistory::query();

        // Column Filter
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

        // Global Filter
        if ($request->filled('global_filter')) {
            $globalFilter = $request->global_filter;
            $query->where(function ($q) use ($globalFilter) {
                $columnsToFilter = [
                    'currency_id', 'status_type_id', 'approved_type_id', 'action_user_id', 'action_date',
                ];
                foreach ($columnsToFilter as $column) {
                    $q->orWhere($column, 'like', "%$globalFilter%");
                }
            });
        }

        // List-specific Filters
        if ($request->filled('list')) {
            $list = $request->list;
            if ($list == 'new') {
                $query->whereDate('created_on', now()->toDateString());
            } elseif ($list == 'approved') {
                $query->where('is_approved', 1);
            }
        }

        // Apply Sorting
        $sortColumn = $request->filled('sort_column') ? $request->sort_column : 'id';
        $sortOrder = $request->filled('sort_order') ? $request->sort_order : 'DESC';
        $admin_currency_histories = $query->orderBy($sortColumn, $sortOrder)->paginate(10);

        if ($request->filled('format')) {
            // Get columns for export
            $columns = $request->columns;
            $columns = explode(',', $columns);

            // Export data
            if ($request->format  === 'excel') {
                return Excel::download(new ListExport($admin_currency_histories, $columns), 'admin_currency_histories_list.xlsx');
            } elseif ($request->format  === 'pdf') {
                $pdf = PDF::loadView('pdf', ['data' => $admin_currency_histories, 'columns' => $columns]);
                return $pdf->download('admin_currency_histories_list.pdf');
            }
        }

        $response = [
            'code' => '201',
            'status' => true,
            'message' => 'Admin Areas List retrieved successfully',
            'admin_currency_histories' => $admin_currency_histories,
        ];

        return response()->json($response, 200);
    }

    public function admin_currency_history(Request $request, $id)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $admin_currency_history = AdminCurrencyHistory::where('id', $id)->first();
            if ($admin_currency_history) {
                $response = [
                    'code' => '201',
                    'status' => true,
                    'message' => 'Admin area retrived, successfully',
                    'admin_currency_history' => $admin_currency_history,
                ];
            } else {
                $response = [
                    'code' => '404',
                    'status' => false,
                    'message' => 'Admin area not found',
                    'admin_currency_history' => null,
                ];
            }
            return response()->json($response, 200);
        } else {
            $response = [
                'code' => '201',
                'status' => false,
                'token' => $request->header('token'),
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ];
            return response()->json($response, 200);
        }
    }

    public function admin_currency_history_store(Request $request)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $validator = Validator::make($request->all(), [
                'currency_id' => 'required|integer',
                'status_type_id' => 'required',
                'approved_type_id' => 'required',
                'action_user_id' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'message' => 'Missing Parameters',
                    'errors' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            AdminCurrencyHistory::insert([
                'currency_id' => $request['currency_id'],
                'status_type_id' => $request['status_type_id'],
                'approved_type_id' => $request['approved_type_id'],
                'action_user_id' => $request['action_user_id'],
                'action_date' => date('Y-m-d h:i:s'),
            ]);
            $admin_currency_history = AdminCurrencyHistory::orderBy('id', 'DESC')->first();
            $response = [
                'code' => '201',
                'status' => true,
                'message' => 'Admin area created, successfully',
                'admin_currency_history' => $admin_currency_history,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'code' => '201',
                'status' => false,
                'token' => $request->header('token'),
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ];
            return response()->json($response, 200);
        }
    }

    public function admin_currency_history_update(Request $request, $id)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $validator = Validator::make($request->all(), [
                'currency_id' => 'required|integer',
                'status_type_id' => 'required',
                'approved_type_id' => 'required',
                'action_user_id' => 'required',
            ]);

            if ($validator->fails()) {
                $response = [
                    'message' => 'Missing Parameters',
                    'errors' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            AdminCurrencyHistory::where('id', $id)->update([
                'currency_id' => $request['currency_id'],
                'status_type_id' => $request['status_type_id'],
                'approved_type_id' => $request['approved_type_id'],
                'action_user_id' => $request['action_user_id'],
                'action_date' => date('Y-m-d h:i:s'),

            ]);
            $admin_currency_history = AdminCurrencyHistory::where('id', $id)->first();
            if ($admin_currency_history) {
                
                $response = [
                    'code' => '201',
                    'status' => true,
                    'message' => 'Admin area updated, successfully',
                    'admin_currency_history' => $admin_currency_history,
                ];
            } else {
                $response = [
                    'code' => '404',
                    'status' => false,
                    'message' => 'Admin area not found',
                    'admin_currency_history' => null,
                ];
            }
            return response()->json($response, 200);
        } else {
            $response = [
                'code' => '201',
                'status' => false,
                'token' => $request->header('token'),
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ];
            return response()->json($response, 200);
        }
    }

}

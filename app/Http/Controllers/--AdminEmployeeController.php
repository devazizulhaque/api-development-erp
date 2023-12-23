<?php

namespace App\Http\Controllers;

use App\Exports\ListExport;
use App\Models\AdminEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminEmployeeController extends Controller
{
    public function overview(Request $request)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $data = array();
            $data['total'] = AdminEmployee::count();
            $data['new'] = AdminEmployee::whereDate('created_on', Carbon::today())->count();
            $data['draft'] = AdminEmployee::where('is_draft', 1)->count();
            $data['approved'] = AdminEmployee::where('is_approved', 1)->count();
            $data['active'] = AdminEmployee::where('is_active', 1)->count();
            $data['in_active'] = AdminEmployee::where('is_active', 0)->count();
            $data['update'] = AdminEmployee::whereDate('last_updated', Carbon::today())
                ->where('last_updated', '<>', null)->count();
            $data['delete'] = AdminEmployee::whereDate('created_on', Carbon::today())
                ->where('is_delete', 1)->count();
            $data['admin_employees'] = AdminEmployee::orderBy('id', 'DESC')->limit(5)->get();

            $response = [
                'code' => '201',
                'status' => true,
                'message' => 'Admin Areas Overview retrived, successfully',
                'admin_employees_overview' => $data,
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

    public function admin_employees(Request $request)
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

        $query = AdminEmployee::query();

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
                    'code', 'english_name', 'short_english', 'arabic_name', 'short_arabic', 'bangla_name', 'short_bangla',
                    'is_default', 'rating', 'created_on', 'is_active', 'last_updated',
                    'is_draft', 'is_delete', 'action_user_id', 'action_date',
                    'is_approved', 'is_pending', 'is_in_progress', 'is_rejected',
                    'approved_by', 'approved_date', 'rejected_by', 'rejected_date',
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
        $admin_employees = $query->orderBy($sortColumn, $sortOrder)->paginate(10);

        if ($request->filled('format')) {
            // Get columns for export
            $columns = $request->columns;
            $columns = explode(',', $columns);

            // Export data
            if ($request->format  === 'excel') {
                return Excel::download(new ListExport($admin_employees, $columns), 'admin_employeess_list.xlsx');
            } elseif ($request->format  === 'pdf') {
                $pdf = PDF::loadView('pdf', ['data' => $admin_employees, 'columns' => $columns]);
                return $pdf->download('admin_employeess_list.pdf');
            }
        }
        else{
            $admin_employees = AdminEmployee::all();
        }


        $response = [
            'code' => '201',
            'status' => true,
            'message' => 'Admin Areas List retrieved successfully',
            'admin_employees' => $admin_employees,
        ];

        return response()->json($response, 200);
    }

    public function admin_employee(Request $request, $id)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $admin_employee = AdminEmployee::where('id', $id)->first();
            if ($admin_employee) {
                $response = [
                    'code' => '201',
                    'status' => true,
                    'message' => 'Admin employee retrived, successfully',
                    'admin_employee' => $admin_employee,
                ];
            } else {
                $response = [
                    'code' => '404',
                    'status' => false,
                    'message' => 'Admin employee not found',
                    'admin_employee' => null,
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

    public function admin_employee_store(Request $request)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'english_name' => 'required|string|max:40',
                'short_english' => 'required|string|max:40',
                'short_bangla' => 'required|string|max:40',
                'short_arabic' => 'required|string|max:40',
                'is_default' => 'required',
                'rating' => 'required',
                'is_active' => 'required|integer',
                'is_draft' => 'required|integer',
                'is_delete' => 'required|integer',
                'action_user_id' => 'required|integer',
                'is_approved' => 'required',
                'is_pending' => 'required',
                'is_in_progress' => 'required',
                'is_rejected' => 'required',
                'approved_by' => 'required',
                'approved_date' => 'required',
                'rejected_by' => 'required',
                'rejected_date' => 'required',
                'approved_date' => 'required'
            ]);

            if ($validator->fails()) {
                $response = [
                    'message' => 'Missing Parameters',
                    'errors' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            AdminEmployee::insert([
                'code' => $request['code'],
                'english_name' => $request['english_name'],
                'short_english' => $request['short_english'],
                'arabic_name' => $request['arabic_name'],
                'short_arabic' => $request['short_arabic'],
                'bangla_name' => $request['bangla_name'],
                'short_bangla' => $request['short_bangla'],
                'is_default' => $request['is_default'],
                'rating' => $request['rating'],
                'created_on' => date('Y-m-d h:i:s'),
                'is_active' => $request['is_active'],
                'is_draft' => $request['is_draft'],
                'last_updated' => date('Y-m-d h:i:s'),
                'is_delete' => $request['is_delete'],
                'action_user_id' => $request['action_user_id'],
                'action_date' => date('Y-m-d h:i:s'),
                'is_approved' => $request['is_approved'],
                'is_pending' => $request['is_pending'],
                'is_in_progress' => $request['is_in_progress'],
                'is_rejected' => $request['is_rejected'],
                'approved_by' => $request['approved_by'],
                'approved_date' => $request['approved_date'],
                'rejected_by' => $request['rejected_by'],
                'rejected_date' => $request['rejected_date'],

            ]);
            $admin_employee = AdminEmployee::orderBy('id', 'DESC')->first();
            
            $response = [
                'code' => '201',
                'status' => true,
                'message' => 'Admin employee created, successfully',
                'admin_employee' => $admin_employee,
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

    public function admin_employee_update(Request $request, $id)
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token',  $request->header('token'))
            ->first();
        if ($token_check) {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'english_name' => 'required|string|max:40',
                'short_english' => 'required|string|max:40',
                'short_bangla' => 'required|string|max:40',
                'short_arabic' => 'required|string|max:40',
                'is_default' => 'required',
                'rating' => 'required',
                'is_active' => 'required|integer',
                'is_draft' => 'required|integer',
                'is_delete' => 'required|integer',
                'action_user_id' => 'required|integer',
                'is_approved' => 'required',
                'is_pending' => 'required',
                'is_in_progress' => 'required',
                'is_rejected' => 'required',
                'approved_by' => 'required',
                'approved_date' => 'required',
                'rejected_by' => 'required',
                'rejected_date' => 'required',
                'approved_date' => 'required'
            ]);

            if ($validator->fails()) {
                $response = [
                    'message' => 'Missing Parameters',
                    'errors' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }

            AdminEmployee::where('id', $id)->update([
                'code' => $request['code'],
                'english_name' => $request['english_name'],
                'short_english' => $request['short_english'],
                'arabic_name' => $request['arabic_name'],
                'short_arabic' => $request['short_arabic'],
                'bangla_name' => $request['bangla_name'],
                'short_bangla' => $request['short_bangla'],
                'is_default' => $request['is_default'],
                'rating' => $request['rating'],
                'created_on' => date('Y-m-d h:i:s'),
                'is_active' => $request['is_active'],
                'is_draft' => $request['is_draft'],
                'last_updated' => date('Y-m-d h:i:s'),
                'is_delete' => $request['is_delete'],
                'action_user_id' => $request['action_user_id'],
                'action_date' => date('Y-m-d h:i:s'),
                'is_approved' => $request['is_approved'],
                'is_pending' => $request['is_pending'],
                'is_in_progress' => $request['is_in_progress'],
                'is_rejected' => $request['is_rejected'],
                'approved_by' => $request['approved_by'],
                'approved_date' => $request['approved_date'],
                'rejected_by' => $request['rejected_by'],
                'rejected_date' => $request['rejected_date'],

            ]);
            $admin_employee = AdminEmployee::where('id', $id)->first();
            if ($admin_employee) {
                $response = [
                    'code' => '201',
                    'status' => true,
                    'message' => 'Admin employee updated, successfully',
                    'admin_employee' => $admin_employee,
                ];
            } else {
                $response = [
                    'code' => '404',
                    'status' => false,
                    'message' => 'Admin employee not found',
                    'admin_employee' => null,
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

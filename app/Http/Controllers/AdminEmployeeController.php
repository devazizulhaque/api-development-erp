<?php

namespace App\Http\Controllers;

use App\Helpers\DataHelper;
use App\helpers\TokenHelper;
use App\Models\AdminEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\OverviewHelper;

class AdminEmployeeController extends Controller
{
    private function validateRequest($request)
    {
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
            'rejected_by' => 'required',
        ]);

        return $validator;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tokenResponse = TokenHelper::checkToken($request);
        if ($tokenResponse !== null) {
            return $tokenResponse;
        }
        else{
            $model = AdminEmployee::class; // Or replace this with your model

            $columnsToFilter = [
                'code', 'english_name', 'short_english', 'arabic_name', 'short_arabic', 'bangla_name', 'short_bangla',
                'is_default', 'rating', 'created_on', 'is_active', 'last_updated',
                'is_draft', 'is_delete', 'action_user_id', 'action_date',
                'is_approved', 'is_pending', 'is_in_progress', 'is_rejected',
                'approved_by', 'approved_date', 'rejected_by', 'rejected_date',
            ];
            $filename = 'admin_employeess_list';

            $results = DataHelper::fetchPaginatedData($model, $request, $columnsToFilter, $filename);
            // $admin_employee = AdminEmployee::paginate(10);
            return response()->json(
                [
                    'code' => '200',
                    'status' => true,
                    'data' => $results,
                    'message' => 'Success',
                ],
                200
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tokenResponse = TokenHelper::checkToken($request);
        if ($tokenResponse !== null) {
            return $tokenResponse;
        }
        else{
            $validator = $this->validateRequest($request);

            if ($validator->fails()) {
                $response = [
                    'message' => 'Missing Parameters',
                    'errors' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }
            
            AdminEmployee::createOrUpdate($request);
            return response()->json([
                'code' => '200',
                'status' => true,
                'data' => null,
                'message' => 'Success',
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $tokenResponse = TokenHelper::checkToken($request);
        if ($tokenResponse !== null) {
            return $tokenResponse;
        }
        else{
            $admin_employee = AdminEmployee::find($id);
            return response()->json(
                [
                    'code' => '200',
                    'status' => true,
                    'data' => $admin_employee,
                    'message' => 'Success',
                ],
                200
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tokenResponse = TokenHelper::checkToken($request);
        if ($tokenResponse !== null) {
            return $tokenResponse;
        }
        else{
            $validator = $this->validateRequest($request);

            if ($validator->fails()) {
                $response = [
                    'message' => 'Missing Parameters',
                    'errors' => $validator->errors()->toArray(),
                ];
                return response()->json($response);
            }
            
            AdminEmployee::createOrUpdate($request, $id);
            return response()->json([
                'code' => '200',
                'status' => true,
                'data' => null,
                'message' => 'Success',
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function overview(Request $request)
    {
        $tokenResponse = TokenHelper::checkToken($request);
        if ($tokenResponse !== null) {
            return $tokenResponse;
        } else {
            $adminEmployee = new AdminEmployee(); // Create an instance of AdminEmployee
            $data = OverviewHelper::getModelOverview($adminEmployee); // Get overview data
            return response()->json(
                [
                    'code' => '200',
                    'status' => true,
                    'data' => $data,
                    'message' => 'Success',
                ],
                200
            );
        }
    }
}

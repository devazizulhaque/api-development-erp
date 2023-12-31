<?php

namespace App\Http\Controllers;

use App\Helpers\DataHelper;
use App\helpers\OverviewHelper;
use App\Models\AdminAllowanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminAllowanceTypeController extends Controller
{
    private function validateRequest($request, $id = null)
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
            'last_updated' => 'nullable|date',
        ]);

        return $validator;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $model = AdminAllowanceType::class; // Or replace this with your model

        $columnsToFilter = [
            'code', 'english_name', 'short_english', 'arabic_name', 'short_arabic', 'bangla_name', 'short_bangla',
            'is_default', 'rating', 'created_on', 'is_active', 'last_updated',
            'is_draft', 'is_delete', 'action_user_id', 'action_date',
        ];
        $filename = 'admin_allowancetype_list';

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
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            $response = [
                'message' => 'Missing Parameters',
                'errors' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }
        
        AdminAllowanceType::createOrUpdate($request);
        return response()->json([
            'code' => '200',
            'status' => true,
            'data' => null,
            'message' => 'Success',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin_allowancetype = AdminAllowanceType::find($id);
        return response()->json(
            [
                'code' => '200',
                'status' => true,
                'data' => $admin_allowancetype,
                'message' => 'Success',
            ],
            200
        );
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
        $validator = $this->validateRequest($request);

        if ($validator->fails()) {
            $response = [
                'message' => 'Missing Parameters',
                'errors' => $validator->errors()->toArray(),
            ];
            return response()->json($response);
        }
        
        AdminAllowanceType::createOrUpdate($request, $id);
        return response()->json([
            'code' => '200',
            'status' => true,
            'data' => null,
            'message' => 'Success',
        ], 200);
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
        $adminallowncetype = new AdminAllowanceType(); // Create an instance of AdminEmployee
        $data = OverviewHelper::getModelOverview($adminallowncetype); // Get overview data
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

use function PHPUnit\Framework\throwException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Product::all();
            if ($data) {
                return response([
                    'code_status' => 1,
                    'message' => 'Data Found',
                    'data' => $data
                ], 200);
            } else {
                return response([
                    'code_status' => 0,
                    'message' => 'Data With Id {$id} Found',
                    'data' => []
                ], 200);
            }
        } catch (\Exception $e) {
            return response([
                'code_status' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->wantsJson()) {
                $fields = $request->all();
                $validator = Validator::make(
                    $fields,
                    [
                        "name" => "required|unique:products,name",
                        "slug" => "required|unique:products,slug",
                        "description" => "required",
                        "price" => "required"
                    ]
                );
                if (!$validator->fails()) {
                    Product::create($fields);
                    return response([
                        "code_status" => 1,
                        "message" => "Data Created",
                        "data" => []
                    ], 201);
                } else {
                    return response([
                        "code_status" => 0,
                        "message" => "Invalid Request",
                        "data" => $validator->errors()
                    ], 422);
                }
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Request must be JSON format!",
                    "data" => []
                ], 403);
            }
            // $fields = $request->all();

        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Product::find($id);
            if ($data) {
                return response([
                    "code_status" => 1,
                    "message" => "Data Found",
                    "data" => $data
                ], 200);
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Data With Id {$id} Found",
                    "data" => []
                ], 200);
            }
        } catch (\Exception $e) {
            return response([
                "code_status" => $e->getCode(),
                "message" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if ($request->wantsJson()) {
                if (Product::find($id)) {
                    $fields = $request->all();
                    $validator = Validator::make(
                        $fields,
                        [
                            "name" => "required",
                            "slug" => "required",
                            "description" => "required",
                            "price" => "required"
                        ]
                    );

                    if (!$validator->fails()) {

                        Product::where('id', $id)
                            ->update($fields);

                        return response([
                            "code_status" => 1,
                            "message" => "Data Updated",
                            "data" => []
                        ], 201);
                    } else {
                        return response([
                            "code_status" => 0,
                            "message" => "Invalid Request",
                            "data" => $validator->errors()
                        ], 422);
                    }
                } else {
                    return response([
                        "code_status" => 1,
                        "message" => "Data with id {$id} not found",
                        "data" => []
                    ], 200);
                }
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Request must be JSON format!",
                    "data" => []
                ], 403);
            }
        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (Product::destroy($id)) {
                return response([
                    "code_status" => 1,
                    "message" => "Data deleted",
                    "data" => []
                ], 200);
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Data with id {$id} not found",
                    "data" => []
                ], 200);
            }
        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        try {
            $data = Product::where('name', 'like', '%' . $name . '%')->get();
            if ($data) {
                return response([
                    "code_status" => 1,
                    "message" => "Data Found",
                    "data" => $data
                ], 200);
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Data with name {$name} not found",
                    "data" => []
                ], 200);
            }
        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }
}

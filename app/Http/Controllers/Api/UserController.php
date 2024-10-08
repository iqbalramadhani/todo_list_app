<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     *    @OA\Get(
     *       path="/api/user",
     *       tags={"User"},
     *       summary="User Profile",
     *       description="Show detail user account",
     *       security={{"bearerAuth":{}}},
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *               "success": true,
     *               "user_profile": {
     *                  "id": 123,
     *                  "name": "tes",
     *                  "email": "iqbal2@email.com",
     *                   "created_at": "15-03-2024 06:06:30",
     *                   "updated_at": "15-03-2024 06:06:30",
     *               },
     *          }),
     *      ),
     *       @OA\Response(
     *           response="401",
     *           description="Error Access Token",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
     *          }),
     *      ),
     *       @OA\Response(
     *           response="500",
     *           description="Error Internal Server",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "error": "string"
     *          }),
     *      ),
     *  )
     */

    public function show(Request $request)
    {
        try {
            $id = JWTAuth::authenticate($request->token)->id;

            $user = User::find($id);

            if ($user) {

                return response()->json([
                    'status' => true,
                    'user_profile' => $user
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {
            Log::channel('error')->error("UserController::show : " . json_encode([
                "Message" => $th->getMessage(),
                "Line" => $th->getLine(),
                "File" => $th->getFile(),
            ]));

            return response()->json([
                'status' => false,
                'error' => 'Terjadi kesalahan pada sistem !'
            ], 500);
        }
    }
}

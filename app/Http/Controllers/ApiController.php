<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;


class ApiController extends Controller
{
    /**
     *    @OA\Post(
     *       path="/api/register",
     *       tags={"Auth"},
     *       summary="Account Register",
     *       description="Register in App",
     *       @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *       ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *               "success": true,
     *               "message": "string",
     *          }),
     *      ),
     *       @OA\Response(
     *           response="400",
     *           description="Error Validate",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "error": {
     *                   "name": {
     *                       "string"
     *                   },
     *                   "email": {
     *                       "string"
     *                   }
     *               }
     *          }),
     *      ),
     *       @OA\Response(
     *           response="500",
     *           description="Error Internal Server",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
     *          }),
     *      ),
     *  )
     */

    public function register(Request $request)
    {
        try {
            //Validate data
            $rule = [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:6|max:50',
            ];

            $validator = Validator::make($request->all(), $rule);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->getMessageBag()
                ], 400);
            }
            try {
                //Request is valid, create new user
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'username' => $request->username

                ]);

                //User created, return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil membuat akun',
                ], Response::HTTP_OK);
            } catch (\Throwable $th) {

                Log::channel('error')->error("ApiController::register : " . json_encode([
                    "Message" => $th,
                    "Line" => $th->getLine(),
                    "File" => $th->getFile(),
                ]));

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada sistem',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Throwable $th) {
            Log::channel('error')->error("ApiController::register : " . json_encode([
                "Message" => $th,
                "Line" => $th->getLine(),
                "File" => $th->getFile(),
            ]));

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *    @OA\Post(
     *       path="/api/login",
     *       tags={"Auth"},
     *       summary="Login Akun",
     *       description="Login ke aplikasi",
     *       @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *       ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *                  "success": true,
     *                  "user_data": {
     *                  "id": 3,
     *                  "name": "tes",
     *                  "email": "iqbal2@email.com",
     *                  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzEwODA1NTc4LCJleHAiOjE3MTA4MDkxNzgsIm5iZiI6MTcxMDgwNTU3OCwianRpIjoiUVRyVENYc0hLMU1DSFdlZSIsInN1YiI6IjMiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.TRR7zpFgY0xBNcWvCcw2JUJ1Zms6SbjnwsUqhAuxA2Q"
     *              }
     *          }),
     *      ),
     *       @OA\Response(
     *           response="400",
     *           description="Error validate data",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "error": {
     *                   "email": {
     *                       "The email field is required."
     *                   },
     *                   "password": {
     *                       "The password field is required."
     *                   }
     *               }
     *          }),
     *      ),
     *      @OA\Response(
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
    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'username' => 'required|string',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->getMessageBag()], 400);
        }

        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah.',
                ], 400);
            }

            $user = User::where('username', $credentials['username'])->first();

            //Token created, return with success response and jwt token
            return response()->json([
                'success' => true,
                'user_data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token,
                ]
            ]);
        } catch (\Throwable $e) {

            Log::channel('error')->error("ApiController::authenticate : " . json_encode([
                "Message" => $e,
                "Line" => $e->getLine(),
                "File" => $e->getFile(),
            ]));

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem',
            ], 500);
        }
    }

    /**
     *    @OA\Post(
     *       path="/api/logout",
     *       tags={"Auth"},
     *       summary="Logout Akun",
     *       description="Logout dari aplikasi",
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="success",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *           response="500",
     *           description="Error Internal Server",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
     *          }),
     *      ),

     *  )
     */

    public function logout(Request $request)
    {
        //Request is validated, do logout    
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (\Throwable $th) {

            Log::channel('error')->error("ApiController::logout : " . json_encode([
                "Message" => $th,
                "Line" => $th->getLine(),
                "File" => $th->getFile(),
            ]));

            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }
}

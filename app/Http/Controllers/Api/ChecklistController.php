<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChecklistController extends Controller
{


    /**
     *    @OA\Get(
     *       path="/api/show-all-checklist",
     *       tags={"Check List"},
     *       summary="Show All Checklist",
     *       description="Show All Checklist",
     *       security={{"bearerAuth":{}}},
     *       @OA\Response(
     *           response="401",
     *           description="Error validate access token or form data",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
     *          }),
     *      ),
     *      @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *               "success": true,
     *               
     *          }),
     *       ),
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

    public function showAllChecklist(Request $request)
    {
        try {

            $id = JWTAuth::authenticate($request->token)->id;
            $user = User::find($id);

            if ($user) {

                $checklistList = Checklist::where('user_id', $user->id)->get();

                if ($checklistList) {

                    return response()->json([
                        'status' => true,
                        'data' => $checklistList
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'data' => 'checklist tidak tersedia'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {

            Log::channel('error')->error("ChecklistController::index : " . json_encode([
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

    /**
     *    @OA\Post(
     *       path="/api/add-checklist",
     *       tags={"Check List"},
     *       summary="Add Checklist",
     *       description="",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(
     *                  description="Judul",
     *                  property="title",
     *                  type="string",
     *               ),
     *             )
     *          )
     *       ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *               "success": true,
     *          }),
     *      ),
     *      @OA\Response(
     *           response="400",
     *           description="Error Data Validation",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
     *          }),
     *      ),
     *      @OA\Response(
     *           response="401",
     *           description="Error Auth Validation",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
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

    public function addChecklist(Request $request)
    {
        try {

            //valid credential
            $validator = Validator::make($request->only('title'), [
                'title' => 'required|string',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->getMessageBag()->first()
                ], 400);
            }

            $id = JWTAuth::authenticate($request->token)->id;
            $user = User::find($id);

            if ($user) {

                $checklist = new Checklist();
                $checklist->title = $request->title;
                $checklist->user_id = $user->id;
                $checklist->save();

                return response()->json([
                    'status' => true,
                    'data' => $checklist
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {

            Log::channel('error')->error("ChecklistController : " . json_encode([
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
    /**
     *    @OA\Delete(
     *       path="/api/remove-checklist",
     *       tags={"Check List"},
     *       summary="Remove Checklist",
     *       description="",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(
     *                  description="ID Checklist",
     *                  property="id",
     *                  type="number",
     *               ),
     *             )
     *          )
     *       ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *               "success": true,
     *          }),
     *      ),
     *      @OA\Response(
     *           response="400",
     *           description="Error Data Validation",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
     *          }),
     *      ),
     *      @OA\Response(
     *           response="401",
     *           description="Error Auth Validation",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "message": "string"
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

    public function deleteChecklist(Request $request)
    {
        try {

            //valid credential
            $validator = Validator::make($request->only('id'), [
                'id' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->getMessageBag()->first()
                ], 400);
            }

            $id_user = JWTAuth::authenticate($request->token)->id;
            $user = User::find($id_user);

            if ($user) {

                $checklist = Checklist::find($request->id);

                if($checklist){
                    $checklist->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Checklist berhasil di hapus'
                    ]);
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Checklist tidak terdaftar !'
                    ], 400);
                }


            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {

            Log::channel('error')->error("ChecklistController : " . json_encode([
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

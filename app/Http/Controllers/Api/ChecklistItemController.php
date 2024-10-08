<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItems;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChecklistItemController extends Controller
{


    /**
     *    @OA\Get(
     *       path="/api/show-detail-checklist-item",
     *       tags={"Check List Item"},
     *       summary="Show All Checklist Item",
     *       description="Show All Checklist Item",
     *       security={{"bearerAuth":{}}},
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID checklist",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *       ),
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

    public function showItemChecklist(Request $request)
    {
        try {

            //valid credential
            $validator = Validator::make($request->only('id'), [
                'id' => 'required|string',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->getMessageBag()->first()
                ], 400);
            }


            $user_id = JWTAuth::authenticate($request->token)->id;
            $user = User::find($user_id);

            if ($user) {

                $checklistListItem = ChecklistItems::where('checklist_id', $request->id)->get();

                if ($checklistListItem) {

                    return response()->json([
                        'status' => true,
                        'data' => $checklistListItem
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'data' => 'checklist item tidak tersedia'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {

            Log::channel('error')->error("ChecklistItemController::index : " . json_encode([
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
     *       path="/api/add-checklist-item",
     *       tags={"Check List Item"},
     *       summary="Add Checklist Item",
     *       description="",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(
     *                  description="ID Check List",
     *                  property="id",
     *                  type="number",
     *               ),
     *                @OA\Property(
     *                  description="Nama Item",
     *                  property="item",
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

    public function addChecklistItem(Request $request)
    {
        try {

            //valid credential
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'item' => 'required|string',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->getMessageBag()->first()
                ], 400);
            }

            $user_id = JWTAuth::authenticate($request->token)->id;
            $user = User::find($user_id);

            if ($user) {

                $checklist = Checklist::find($request->id);

                if($checklist){
                    $checklistItems = new ChecklistItems();
                    $checklistItems->item = $request->item;
                    $checklistItems->status = '0';
                    $checklistItems->checklist_id = $request->id;
                    $checklistItems->save();
    
                    return response()->json([
                        'status' => true,
                        'data' => $checklistItems
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

            Log::channel('error')->error("ChecklistItemController : " . json_encode([
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
     *       path="/api/remove-checklist-item",
     *       tags={"Check List Item"},
     *       summary="Remove Checklist",
     *       description="",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(
     *                  description="ID Checklist Item",
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

    public function deleteChecklistItem(Request $request)
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

                $checklistItem = ChecklistItems::find($request->id);

                if ($checklistItem) {
                    $checklistItem->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Checklist item berhasil di hapus'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Checklist item tidak terdaftar !'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {

            Log::channel('error')->error("ChecklistItemController : " . json_encode([
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
     *    @OA\Put(
     *       path="/api/update-checklist-item",
     *       tags={"Check List Item"},
     *       summary="Upde Status Checklist Item",
     *       description="",
     *       security={{"bearerAuth":{}}},
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID checklist item",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
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

     public function updateChecklistItem(Request $request)
     {
         try {
 
             //valid credential
             $validator = Validator::make($request->all(), [
                 'id' => 'required',
             ]);
 
             //Send failed response if request is not valid
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false,
                     'error' => $validator->getMessageBag()->first()
                 ], 400);
             }
 
             $user_id = JWTAuth::authenticate($request->token)->id;
             $user = User::find($user_id);
 
             if ($user) {
 
                 $checklistItems = ChecklistItems::find($request->id);
 
                 if($checklistItems){
                     $checklistItems->status = $checklistItems->status == '0' ? '1' : '0';
                     $checklistItems->save();
     
                     return response()->json([
                         'status' => true,
                         'data' => $checklistItems
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
 
             Log::channel('error')->error("ChecklistItemController : " . json_encode([
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

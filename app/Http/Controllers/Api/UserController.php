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
     *                   "type": "organisasi",
     *                   "no_handphone": null,
     *                   "kode_refferal": null,
     *                   "no_rekening": null,
     *                   "bank": null,
     *                   "atas_nama_bank": null,
     *                   "url_photo": null,
     *                   "wakaf_maksimal": 0,
     *                   "bulan_maksimal_pembayaran": 0
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

                $fullSet = false;
                if ($user->type == "organisasi") {
                    $fullSet = !in_array("", [$user->email, $user->name, $user->no_handphone, $user->no_rekening, $user->bank, $user->atas_nama_bank, $user->url_photo]);
                    if ($fullSet) {
                        $fullSet = !in_array(0, [$user->wakaf_maksimal, $user->bulan_maksimal_pembayaran]);
                    }
                } else if ($user->type == "member") {
                    $fullSet = !in_array("", [$user->email, $user->name, $user->no_handphone, $user->no_rekening, $user->bank, $user->atas_nama_bank, $user->url_photo]);
                }

                $user->organisasi = $user->type == 'member' ? @User::select('name')->where('kode_refferal', $user->kode_refferal ?? '')->first()->name : null;
                $user->url_photo = !empty($user->url_photo) ? url($user->url_photo) : null;
                $user->profile_lengkap = $fullSet;

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     *    @OA\Post(
     *       path="/api/user/update-profile",
     *       tags={"User"},
     *       summary="Update Profile",
     *       description="Update user profile data",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(
     *                  description="No Handphone",
     *                  property="no_handphone",
     *                  type="string",
     *               ),
     *                @OA\Property(
     *                  description="No Rekening",
     *                  property="no_rekening",
     *                  type="string",
     *               ),
     *                @OA\Property(
     *                  description="Nama Bank",
     *                  property="nama_bank",
     *                  type="string",
     *               ),
     *                @OA\Property(
     *                  description="Pemilik Rekening",
     *                  property="pemilik_rekening",
     *                  type="string",
     *               ),
     *                @OA\Property(
     *                  description="Wakaf maksimal",
     *                  property="wakaf_maksimal",
     *                  type="number",
     *               ),
     *                @OA\Property(
     *                  description="Perbulan maksimal pembayaran",
     *                  property="bulan_maksimal_pembayaran",
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
     *                  "success": true,
     *                  "user_data": {
     *                      "id": "integer",
     *                      "name": "string",
     *                      "email": "string",
     *                      "role": "string",
     *                      "no_handphone": "string",
     *                      "kode_refferal": "string",
     *                      "no_rekening": "string",
     *                      "bank": "string",
     *                      "atas_nama_bank": "string",
     *                      "url_photo": "string",
     *                      "wakaf_maksimal": "integer",
     *                      "bulan_maksimal_pembayaran": "integer",
     *                      "organisasi": "string",
     *                      "profile_lengkap": "boolean",
     *              }
     *          }),
     *      ),
     *       @OA\Response(
     *           response="400",
     *           description="Validation if photo profile still empty after update data",
     *           @OA\JsonContent
     *           (example={
     *                  "success": true,
     *                  "user_data": {
     *                      "id": "integer",
     *                      "name": "string",
     *                      "email": "string",
     *                      "role": "string",
     *                      "no_handphone": "string",
     *                      "kode_refferal": "string",
     *                      "no_rekening": "string",
     *                      "bank": "string",
     *                      "atas_nama_bank": "string",
     *                      "url_photo": "string",
     *                      "wakaf_maksimal": "integer",
     *                      "bulan_maksimal_pembayaran": "integer",
     *                      "organisasi": "string",
     *                      "profile_lengkap": "boolean",
     *              },
     *              "message" : "string"
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

    public function update(Request $request)
    {
        $id = JWTAuth::authenticate($request->token)->id;

        try {
            $user = User::find($id);

            if ($user) {

                $rule = [
                    'no_handphone' => 'required|numeric|min:1',
                    'no_rekening' => 'required|numeric',
                    'nama_bank' => 'required',
                    'pemilik_rekening' => 'required',
                ];

                if($user->type == "organisasi"){
                    $rule['wakaf_maksimal'] = 'required|numeric|min:1';
                    $rule['bulan_maksimal_pembayaran'] = 'required|numeric|min:1';
                }


                $validator = Validator::make($request->all(), $rule);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->getMessageBag()->getMessages()
                    ], 400);
                }

                $old_file = $user->url_photo;
                $user->no_handphone = $request->no_handphone ?? $user->no_handphone;
                $user->no_rekening = $request->no_rekening ?? $user->no_rekening;
                $user->bank = $request->nama_bank ?? $user->bank;
                $user->atas_nama_bank = $request->pemilik_rekening ?? $user->atas_nama_bank;
                $user->wakaf_maksimal = $user->type == "organisasi" ? (float)$request->wakaf_maksimal ?? $user->wakaf_maksimal : 0;
                $user->bulan_maksimal_pembayaran = $user->type == "organisasi" ? (float)$request->bulan_maksimal_pembayaran ?? $user->bulan_maksimal_pembayaran : 0;
                $user->save();

                $fullSet = true;
                if ($user->type == "organisasi") {
                    $fullSet = !in_array("", [$user->email, $user->name, $user->no_handphone, $user->no_rekening, $user->bank, $user->atas_nama_bank, $user->url_photo]);
                    if ($fullSet) {
                        $fullSet = !in_array(0, [$user->wakaf_maksimal, $user->bulan_maksimal_pembayaran]);
                    }
                }
                if ($user->type == "member") {
                    $fullSet = !in_array("", [$user->email, $user->name, $user->no_handphone, $user->no_rekening, $user->bank, $user->atas_nama_bank, $user->url_photo]);
                }

                $user->organisasi = $user->type == 'member' ? @User::select('name')->where('kode_refferal', $user->kode_refferal ?? '')->first()->name : null;
                $user->profile_lengkap = $fullSet;

                
                $user->url_photo = $old_file ? url($old_file) : null;
                
                if(!isset($old_file)){
                    return response()->json([
                        'status' => false,
                        'user_profile' => $user,
                        'message' => 'menyimpan data berhasil, tapi profile belum lengkap karena photo profile masih kosong !'
                    ],400);
                }else{
                    return response()->json([
                        'status' => true,
                        'user_profile' => $user
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {
            Log::channel('error')->error("UserController::update : " . json_encode([
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
     *       path="/api/user/upload-photo-profile",
     *       tags={"User"},
     *       summary="Update Profile",
     *       description="Update user photo profile data",
     *       security={{"bearerAuth":{}}},
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(
     *                  description="Upload photo profile",
     *                  property="photo_profile",
     *                  type="string",
     *                  format="binary",
     *               ),
     *             )
     *          )
     *       ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *                  "success": true,
     *                  "user_data": {
     *                      "id": "integer",
     *                      "name": "string",
     *                      "email": "string",
     *                      "role": "string",
     *                      "no_handphone": "string",
     *                      "kode_refferal": "string",
     *                      "no_rekening": "string",
     *                      "bank": "string",
     *                      "atas_nama_bank": "string",
     *                      "url_photo": "string",
     *                      "wakaf_maksimal": "integer",
     *                      "bulan_maksimal_pembayaran": "integer",
     *                      "organisasi": "string",
     *                      "profile_lengkap": "boolean",
     *              }
     *          }),
     *      ),
     *      @OA\Response(
     *           response="401",
     *           description="Error Internal Server",
     *           @OA\JsonContent
     *           (example={
     *               "success": false,
     *               "error": "string"
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

    public function upload_photo_profile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'photo_profile' => 'max:2048|image',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->getMessageBag()
                ], 400);
            }


            $id = JWTAuth::authenticate($request->token)->id;
            $user = User::find($id);

            if ($user) {

                $old_file = $user->url_photo;

                if (!empty($request->file('photo_profile'))) {
                    $file                = $request->file('photo_profile');

                    $fileExtension       = $file->getClientOriginalExtension();
                    $fileDestination     = 'uploads/img/';
                    $fileRename          = Str::random(6) . date('dmYHis') . '.' . $fileExtension;
                    $filenameUploads     = $fileDestination . $fileRename;

                    if (!file_exists($fileDestination)) {
                        mkdir($fileDestination, 755, true);
                    }
                    Image::make($file->path())->save(public_path($fileDestination . $fileRename), 30);
                    if (!empty($old_file) && file_exists(public_path() . '/' . $old_file)) {
                        unlink($old_file);
                    }
                    $old_file = $filenameUploads;
                }

                $user->url_photo = $old_file;
                $user->save();

                $fullSet = true;
                if ($user->type == "organisasi") {
                    $fullSet = !in_array("", [$user->email, $user->name, $user->no_handphone, $user->no_rekening, $user->bank, $user->atas_nama_bank, $user->url_photo]);
                    if ($fullSet) {
                        $fullSet = !in_array(0, [$user->wakaf_maksimal, $user->bulan_maksimal_pembayaran]);
                    }
                }
                if ($user->type == "member") {
                    $fullSet = !in_array("", [$user->email, $user->name, $user->no_handphone, $user->no_rekening, $user->bank, $user->atas_nama_bank, $user->url_photo]);
                }

                $user->organisasi = $user->type == 'member' ? @User::select('name')->where('kode_refferal', $user->kode_refferal ?? '')->first()->name : null;
                $user->profile_lengkap = $fullSet;
                $user->url_photo = url($old_file);

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

            Log::channel('error')->error("UserController::update : " . json_encode([
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
     *    @OA\Get(
     *       path="/api/show-member-user-list",
     *       tags={"User"},
     *       summary="Show member user list",
     *       description="Show member user list",
     *       security={{"bearerAuth":{}}},
     *       @OA\Parameter(
     *         name="search_name",
     *         in="query",
     *         description="Seach member by name based on organization",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *       ),
     *       @OA\Parameter(
     *         name="pengguna_dana_wakaf",
     *         in="query",
     *         description="Filter limit by member use wakaf donation",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={0,1,2},
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
     *               "data": {
     *                   {
     *                       "id": 34,
     *                       "name": "Tes Member",
     *                       "email": "iqbal9@email.com",
     *                       "no_handphone": null,
     *                       "photo_url": "uploads/img/ihKytu30032024074639.png",
     *                       "created_at": "29-03-2024 03:25:39"
     *                   }
     *               }
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

    public function showMemberUserList(Request $request)
    {
        try {

            $id = JWTAuth::authenticate($request->token)->id;
            $user = User::find($id);

            if ($user) {

                if ($user->type == "organisasi") {
                    $listUser = User::select('id', 'name', 'email', DB::raw('CONCAT("' . url('/') . '/", url_photo) AS photo_url'), 'no_handphone', 'created_at')
                        ->where('type', 'member');

                    if (isset($request->search_name)) {
                        $listUser->where('name', 'like', '%' . $request->search_name . '%');
                    }
                    
                    if ($request->pengguna_dana_wakaf == "1") {
                        $listUser->whereHas('pengguna_dana_wakaf', function ($query) {
                            $query->where('status', PENGAJUAN_DITERIMA);
                            $query->where('is_process', 1);
                        });
                    }

                    if ($request->pengguna_dana_wakaf == "2") {
                        $listUser->whereHas('pengguna_dana_wakaf', function ($query) {
                            $query->where('status', MENGGUNAKAN_DANA);
                            $query->where('is_process', 1);
                        });
                    }

                    $listUsers = $listUser->where('kode_refferal', $user->kode_refferal)->get();

                    return response()->json([
                        'status' => true,
                        'data' => $listUsers
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Hanya organisasi yang dapat melihat list member !'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak terdaftar !'
                ], 400);
            }
        } catch (\Throwable $th) {

            Log::channel('error')->error("UserController::showMemberUserList : " . json_encode([
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

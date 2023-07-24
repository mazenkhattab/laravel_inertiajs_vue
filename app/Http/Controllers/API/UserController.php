<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileHandler;

class UserController extends Controller{
    use FileHandler;
   public function destroy (Request $request){
$user = User::find($request->id);



$this->deleteFile($user->image['path']);
$user->image->delete();
return  response()->json([
    'message' => 'Image Deleted Successfully '
]);
   }


   public function update (Request $request){
    $user = User::find($request->id);
    // dd($user->image);
    if(isset($request->image)){
        $this->UpdateModelImage($user,$request->image);
        // $user->image()->update([
        //     'path' => Storage::put('images',$request->image),
        // ]);
    };
   $user->load('image');
   
   
    return response()->json([
        "message" => 'user updated image successfully',
        'user'=> $user
    ]);
       }
}

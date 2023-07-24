<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterationRequest;
use App\Models\image;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileHandler;

class AuthController extends Controller
{
    use FileHandler;
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh(true, true));
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {   
        // $Create_token = User::where('id', auth()->user()->id)->update(['api_token' => $token]);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
   

    public function register(UserRegisterationRequest $request)
    {
        $data=$request->validated();
        $image=null;
        $user = User::create($data);
        if(isset($data['image'])){
            $image=$data['image'];
        unset($data['image']);
        $this->StoreImageToModel($user,$image);
        
      $user->load('image');
    }
    
        // event(new Registered($user));
      

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            
        ]);
    }

}

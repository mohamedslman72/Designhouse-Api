<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
//        dd($request->all());
        $this->validate( $request, [
            'tagline' => 'required',
            'name' => 'required',
            'about' => 'required',
            'formatted_address' => 'required',
            'location.latitude' => ['required','numeric','min:-90','max:90'],
            'location.longitude' => ['required','numeric','min:-180','max:180'],
            //'location.longitude' => 'required|number|min:-180|max:180',
        ] );
        $location = new Point($request->location['latitude'],$request->location['longitude']);
//        dd($location);
        $user->update([
            'name'=>$request->name,
            'about'=>$request->about,
            'formatted_address'=>$request->formatted_address,
            'location'=>$location,
            'tagline'=>$request->tagline,
            'available_to_hire'=>$request->available_to_hire,
        ]);
        return new UserResource($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request,[
          'current_password'  =>['required',new MatchOldPassword()],
          'password'  =>['required','confirmed','min:6',new CheckSamePassword()],
        ]);
        $user = auth()->user();
        $user->update(['password'=>bcrypt($request->password)]);
        return response()->json(['status'=>true,'message'=>'Password Updated'],200);
    }
}

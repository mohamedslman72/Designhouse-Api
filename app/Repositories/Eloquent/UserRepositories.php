<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 4/15/2020
 * Time: 7:08 PM
 */

namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\Contacts\UserInterface;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class UserRepositories extends BaseRepository implements UserInterface
{
    public function model()
    {
        return User::class;
    }

    public function findEmail($email)
    {
        return $this->model->where( 'email', $email )->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();

        // only designers who have designs
        if ($request->has_designs){
            $query->has('designs');
        }
        // Check for available_to_hire

        if ($request->available_to_hire){
            $query->where('available_to_hire',true);
        }

        // Geographic Search
        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
            $unit = $request->unit;
        if($lat && $lng){
            $point = new Point($lat, $lng);
            $unit == 'km' ? $dist *= 1000 : $dist *=1609.34;
            $query->distanceSphereExcludingSelf('location', $point, $dist);
        }
        // order the results
        if($request->orderBy=='closest'){
            $query->orderByDistanceSphere('location', $point, 'asc');
        } else if($request->orderBy=='latest'){
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }
}
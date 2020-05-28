<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contacts\DesignInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Str;

class DesignController extends Controller
{
    protected $designs;

    public function __construct(DesignInterface $designs)
    {
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->withCriteria( [
            new LatestFirst(),
            new IsLive(),
            new ForUser(2),
            new EagerLoad(['user','comments'])
        ] )->all();
        return DesignResource::collection( $designs );
    }

    public function findDesign($id)
    {
        $design = $this->designs->find( $id );
        return new DesignResource( $design );
    }

    public function update($id, Request $request)
    {
        $design = $this->designs->find( $id );
        $this->authorize( 'update', $design );
        $this->validate( $request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
            'team_id' =>['required_if:assign_to_team,true']
        ] );
        $design = $this->designs->update( $id, [
            'team_id' =>$request->team_id,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug( $request->title ),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ] );
        // apply the tags
        $this->designs->applyTags( $id, (array)$request->tags );
        return response()->json( ['status' => true, 'data' => new DesignResource( $design )], 200 );


    }

    public function destroy($id, Request $request)
    {
        $design = $this->designs->find( $id );
        $this->authorize( 'delete', $design );

        foreach (['thumbnail', 'large', 'original'] as $size) {
            // delete if the file exists in the database
            if (Storage::disk( $design->disk )->exists( "uploads/designs/{$size}/" . $design->image )) {
                Storage::disk( $design->disk )->delete( "uploads/designs/{$size}/" . $design->image );
            }
        }

        $this->designs->delete( $id );
        return response()->json( ['status' => true, 'message' => 'Record deleted'], 200 );
    }
    public function like($id)
    {
        $this->designs->like($id);
        return response()->json(['status'=>true,'message'=>'Successful'],200);
    }
    public function checkIfUserHasLiked($id)
    {
        $isLiked = $this->designs->isLikedByUser($id);
        return response()->json(['status'=>true,'liked'=>$isLiked],200);
    }
    public function search(Request $request)
    {
        $designs = $this->designs->search($request);
        return  DesignResource::collection($designs);
    }
    public function findBySlug($slug)
    {
        $design = $this->designs->withCriteria([new IsLive()])->findWhereFirst('slug',$slug);
        return new DesignResource($design);
    }
    public function getForTeam($team_id)
    {
        $designs = $this->designs->withCriteria([new IsLive()])->findWhere('team_id',$team_id);
        return  DesignResource::collection($designs);
    }
    public function getForUser($user_id)
    {
        $designs = $this->designs->withCriteria([new IsLive()])->findWhere('user_id',$user_id);
        return  DesignResource::collection($designs);
    }
}

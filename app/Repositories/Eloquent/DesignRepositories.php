<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 4/15/2020
 * Time: 7:06 PM
 */

namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contacts\DesignInterface;
use function foo\func;
use Illuminate\Http\Request;

class DesignRepositories extends BaseRepository implements DesignInterface
{
    public function model()
    {
        return Design::class;
    }

    public function applyTags($id, array $data)
    {
        $design = $this->find( $id );
        $design->retag( $data );

    }

    public function addComment($design_id, array $data)
    {
        $design = $this->find( $design_id );

        //create comment for the design
        $comment = $design->comments()->create( $data );
        return $comment;
    }

    public function like($id)
    {
        $design = $this->model->findOrFail( $id );
        if ($design->isLikedByUser( auth()->id() )) {
            $design->unlike();
        } else {
            $design->like();
        }
    }

    public function isLikedByUser($id)
    {
        $design = $this->model->findOrFail( $id );
        return $design->isLikedByUser( auth()->id() );
    }

    public function search(Request $request)
    {

        $query = $this->model->newQuery();
        $query->where( 'is_live', false );
        // return only designs with comments
        if ($request->has_comments) {
            $query->has( 'comments' );
        }

        // return only designs assigns to teams

        if ($request->has_team) {
            $query->has( 'team' );
        }
        // search title and description for provided string
        if ($request->q) {
            $query->where( function ($q) use ($request) {
                $q->where( 'title', 'like', '%' . $request->q . '%' )
                    ->orWhere( 'description', 'like', '%' . $request->q . '%' );
            } );
        }
//dd($query->toSql());
        // order the query by likes or latest first
        if ($request->orderBy == 'likes'){
            $query->withCount('likes') //likes_count
            ->orderByDesc('likes_count');
        }else{
            $query->latest();
        }

        return $query->get();

    }
//    public function getDesignsByTag()
//    {
//
//    }
}
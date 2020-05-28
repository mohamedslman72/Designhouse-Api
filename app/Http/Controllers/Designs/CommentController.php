<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contacts\CommentInterface;
use App\Repositories\Contacts\DesignInterface;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $comments;
    protected $designs;

    public function __construct(CommentInterface $comments, DesignInterface $designs)
    {
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $design_id)
    {
        $this->validate( $request, [
            'body' => ['required']
        ] );

        $comment = $this->designs->addComment( $design_id,[
            'body'=>  $request->body,
            'user_id'=>auth()->id()
        ]);
        return new  CommentResource( $comment );
    }
    public function update(Request $request,$id)
    {
//        dd($request->all());
        $comment = $this->comments->find($id);
        $this->authorize('update',$comment);
        $this->validate($request,[
            'body'=>'required'
        ]);
        $comment = $this->comments->update($id,[
            'body'=> $request->body
        ]);
        return new CommentResource($comment);
    }
    public function destroy($id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('delete',$comment);
        $this->comments->delete($id);
        return response()->json(['status'=>true,'message'=> __('Item deleted')]);
    }
}

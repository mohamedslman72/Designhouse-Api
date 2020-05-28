<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $this->validate( $request, [
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ] );

        // get the image
        $image = $request->file( 'image' );
        $image_path = $image->getPathName();

        // get the original file name and replace any space with _
        //ex : Business Cards.png = timestamp()_business_cards.png
        $file_name = time() . "_" . preg_replace( '/\$+/', '_', strtolower( $image->getClientOriginalName() ) );

        // move the image to the temporary location(tmp)
        $tmp = $image->storeAs( 'uploads/original', $file_name, 'tmp' );

        // create the database record for the design
        $design = auth()->user()->designs()->create( [
            'image' => $file_name,
            'disk' => config( 'site.upload_disk' )
        ] );

        // dispatch a job to handle the image manipulation
        $this->dispatch( new UploadImage( $design ) );
        return response()->json( ['status' => true, 'message' => 'Design Created Successfully', 'data' => $design], 200 );
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BookArea;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookAreaController extends Controller
{
    public function BookArea(){
        $book = BookArea::find(1);
        return view('backend.bookarea.book_area', compact('book'));
    }

    public function BookAreaUpdate(Request $request){
        $book_id = $request->id;
        $book = BookArea::find($book_id);
        if($request->file('image')){
            // Delete the existing image if it exists
            if($book->image){
                $image_path = public_path($book->image);
                if(file_exists($image_path)){
                    unlink($image_path);
                }
            }
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(1000,1000);
            
            $img->toJpeg(80)->save(base_path('public/upload/bookarea/'.$name_gen));
            $save_url = 'upload/bookarea/'.$name_gen;

            BookArea::findOrFail($book_id)->update([
                'short_title'=>$request->short_title,
                'main_title'=>$request->main_title,
                'short_desc'=>$request->short_desc,
                'link_url'=>$request->link_url,
                'image'=>$save_url,
                'created_at'=>Carbon::now()
            ]);
            $notification = array(
                'message' => 'Book Area Updated With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
        }else{
            BookArea::findOrFail($book_id)->update([
                'short_title'=>$request->short_title,
                'main_title'=>$request->main_title,
                'short_desc'=>$request->short_desc,
                'link_url'=>$request->link_url,
                'created_at'=>Carbon::now()
            ]);
            $notification = array(
                'message' => 'Book Area Updated Without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);

        }
        
    }
}

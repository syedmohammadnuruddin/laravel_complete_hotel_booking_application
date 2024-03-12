<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TestimonialController extends Controller
{
    public function AllTestimonial(){

        $testimonial = Testimonial::latest()->get();
        return view('backend.tesimonial.all_tesimonial',compact('testimonial'));

    }
    public function AddTestimonial(){
        return view('backend.tesimonial.add_testimonial');
    }
    public function StoreTestimonial(Request $request){
        if($request->file('image')){
            $manager = new ImageManager(new Driver());
            $img = $request->file('image');
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(50,50);
            $img->toJpeg(80)->save(base_path('public/upload/testimonial/'.$name_gen));
            $save_url = 'upload/testimonial/'.$name_gen;
    
    
            Testimonial::insert([
    
                'name' => $request->name,
                'city' => $request->city,
                'message' => $request->message,
                'image' => $save_url,
                'created_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'Testimonial Data Inserted Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.testimonial')->with($notification);
        }
        

    }
    public function EditTestimonial($id){

        $testimonial = Testimonial::find($id);
        return view('backend.tesimonial.edit_testimonial',compact('testimonial'));

    }
    public function UpdateTestimonial(Request $request){

        $test_id = $request->id;

        if($request->file('image')){

            $manager = new ImageManager(new Driver());
            $img = $request->file('image');
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(50,50);
            $img->toJpeg(80)->save(base_path('public/upload/testimonial/'.$name_gen));
            $save_url = 'upload/testimonial/'.$name_gen;

            Testimonial::findOrFail($test_id)->update([

            'name' => $request->name,
            'city' => $request->city,
            'message' => $request->message,
            'image' => $save_url,
            'created_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Testimonial Updated With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.testimonial')->with($notification);


        } else {

            Testimonial::findOrFail($test_id)->update([

                'name' => $request->name,
                'city' => $request->city,
                'message' => $request->message, 
                'created_at' => Carbon::now(),
                ]);

                $notification = array(
                    'message' => 'Testimonial Updated Without Image Successfully',
                    'alert-type' => 'success'
                );

                return redirect()->route('all.testimonial')->with($notification);

        }  

    }
    public function DeleteTestimonial($id){

        $item = Testimonial::findOrFail($id);
        $img = $item->image;
        unlink($img);

        Testimonial::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Testimonial Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


     }
}

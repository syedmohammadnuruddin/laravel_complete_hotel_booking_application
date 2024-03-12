<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\Contact;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class GalleryController extends Controller
{
    public function AllGallery(){

        $gallery = Gallery::latest()->get();
        return view('backend.gallery.all_gallery',compact('gallery'));

    }
    public function AddGallery(){
        return view('backend.gallery.add_gallery');
    }
    public function StoreGallery(Request $request){

        $images = $request->file('photo_name');

        foreach ($images as $img) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            $img = $manager->read($img);
            $img = $img->resize(550,550);
            
            $img->toJpeg(80)->save(base_path('public/upload/gallery/'.$name_gen));
            $save_url = 'upload/gallery/'.$name_gen;

        Gallery::insert([
            'photo_name' => $save_url,
            'created_at' => Carbon::now(), 
        ]);
        } 

        $notification = array(
            'message' => 'Gallery Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.gallery')->with($notification);

    }
    public function EditGallery($id){

        $gallery = Gallery::find($id);
        return view('backend.gallery.edit_gallery',compact('gallery'));

    }
    // public function UpdateGallery(Request $request){

    //     $gal_id = $request->id;

    //     $img = $request->file('photo_name');
    //     $manager = new ImageManager(new Driver());
    //     $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
    //     $img = $manager->read($img);
    //     $img = $img->resize(550,550);
        
    //     $img->toJpeg(80)->save(base_path('public/upload/gallery/'.$name_gen));
    //     $save_url = 'upload/gallery/'.$name_gen;

        

    //     Gallery::find($gal_id)->update([
    //         'photo_name' => $save_url, 
    //     ]); 

    //     $notification = array(
    //         'message' => 'Gallery Updated Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->route('all.gallery')->with($notification);  

    // }

    public function UpdateGallery(Request $request)
{
    $gal_id = $request->id;
    $prev_photo = Gallery::findOrFail($gal_id)->photo_name; // Get the path of the previous image

    if ($request->hasFile('photo_name')) {
        $img = $request->file('photo_name');
        $manager = new ImageManager(new Driver());
        $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
        $img = $manager->read($img);
        $img = $img->resize(550,550);
        
        $img->toJpeg(80)->save(base_path('public/upload/gallery/'.$name_gen));
        $save_url = 'upload/gallery/'.$name_gen;

        // Delete the previous image file
        if ($prev_photo && file_exists(public_path($prev_photo))) {
            unlink(public_path($prev_photo));
        }
    } else {
        $save_url = $prev_photo; // If no new image is uploaded, keep the previous image
    }

    Gallery::find($gal_id)->update([
        'photo_name' => $save_url,
    ]);

    $notification = array(
        'message' => 'Gallery Updated Successfully',
        'alert-type' => 'success'
    );

    return redirect()->route('all.gallery')->with($notification);
}

    public function DeleteGallery($id){

        $item = Gallery::findOrFail($id);
        $img = $item->photo_name;
        unlink($img);

        Gallery::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Gallery Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


     }
     public function DeleteGalleryMultiple(Request $request){

        $selectedItems = $request->input('selectedItem', []);

        foreach ($selectedItems as $itemId) {
           $item = Gallery::find($itemId);
           $img = $item->photo_name;
           unlink($img);
           $item->delete();
        }

        $notification = array(
            'message' => 'Selected Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

     }
     public function ShowGallery(){
        $gallery = Gallery::latest()->get();
        return view('frontend.gallery.show_gallery',compact('gallery'));
     }
     public function ContactUs(){

        return view('frontend.contact.contact_us');
     }
     public function StoreContactUs(Request $request){

        Contact::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Your Message Send Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

     }
     public function AdminContactMessage(){

        $contact = Contact::latest()->get();
        return view('backend.contact.contact_message',compact('contact'));

     }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Facility;
use App\Models\MultiImage;
use App\Models\RoomNumber;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RoomController extends Controller
{
    public function EditRoom($id){
        $basic_facility = Facility::where('rooms_id',$id)->get();
        $multiimgs = MultiImage::where('rooms_id',$id)->get();
        $editData = Room::findOrFail($id);
        $allroomNo = RoomNumber::where('rooms_id',$id)->get();
        return view('backend.allroom.rooms.edit_rooms',compact('editData','basic_facility','multiimgs','allroomNo'));
    }

    public function UpdateRoom(Request $request, $id){

        $room  = Room::find($id);
        $room->roomtype_id = $room->roomtype_id;
        $room->total_adult = $request->total_adult;
        $room->total_child = $request->total_child;
        $room->room_capacity = $request->room_capacity;
        $room->price = $request->price;

        $room->size = $request->size;
        $room->view = $request->view;
        $room->bed_style = $request->bed_style;
        $room->discount = $request->discount;
        $room->short_desc = $request->short_desc;
        $room->description = $request->description; 
        $room->status = 1;

        /// Update Single Image 
        if($request->file('image')){

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(550,670);
            
            $img->toJpeg(80)->save(base_path('public/upload/rooming/'.$name_gen));
            $save_url = 'upload/rooming/'.$name_gen;
            $room['image'] = $save_url; 
        }

        $room->save();

        if($request->facility_name == NULL){

            $notification = array(
                'message' => 'Sorry! Not Any Basic Facility Select',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);

        } else{
            Facility::where('rooms_id',$id)->delete();
            $facilities = Count($request->facility_name);
            for($i=0; $i < $facilities; $i++ ){
                $fcount = new Facility();
                $fcount->rooms_id = $room->id;
                $fcount->facility_name = $request->facility_name[$i];
                $fcount->save();
            } 
        } 
        // update multi image
        if($room->save()){
            $files = $request->multi_img;
            if(!empty($files)){
                $subimage = MultiImage::where('rooms_id',$id)->get()->toArray();
                MultiImage::where('rooms_id',$id)->delete();

            }
            if(!empty($files)){
                foreach($files as $file){
                    $imgName = date('YmdHi').$file->getClientOriginalName();
                    $file->move('upload/rooming/multi_img/',$imgName);
                    $save_url = 'upload/rooming/multi_img/'.$imgName;

                    $subimage = new MultiImage();
                    $subimage->rooms_id = $room->id;
                    $subimage->multi_img = $save_url;
                    $subimage->save();
                }

            }
        } //end multi image

        $notification = array(
            'message' => 'Room Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }

    public function MultiImageDelete($id){

        $deletedata = MultiImage::where('id',$id)->first();
        $imagePath = $deletedata->multi_img;
        unlink($imagePath);
        MultiImage::where('id',$id)->delete();

        // $deletedata = MultiImage::where('id',$id)->first();

        // if($deletedata){

        //     $imagePath = $deletedata->multi_img;

        //     // Check if the file exists before unlinking 
        //     if (file_exists($imagePath)) {
        //        unlink($imagePath);
        //        echo "Image Unlinked Successfully";
        //     }else{
        //         echo "Image does not exist";
        //     }

        //     //  Delete the record form database 

        //     MultiImage::where('id',$id)->delete();

        // }

        $notification = array(
            'message' => 'Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }

    public function StoreRoomNumber(Request $request,$id){

        $data = new RoomNumber();
        $data->rooms_id = $id;
        $data->room_type_id = $request->room_type_id;
        $data->room_no = $request->room_no;
        $data->status = $request->status;
        $data->save();

        $notification = array(
            'message' => 'Room Number Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }

    public function EditRoomNumber($id){

        $editroomno = RoomNumber::find($id);
        return view('backend.allroom.rooms.edit_room_no',compact('editroomno'));

    }

    public function UpdateRoomNumber(Request $request, $id){

        $data = RoomNumber::find($id);
        $data->room_no = $request->room_no;
        $data->status = $request->status;
        $data->save();

       $notification = array(
            'message' => 'Room Number Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('room.type.list')->with($notification); 

    }

    public function DeleteRoomNumber($id){
        RoomNumber::find($id)->delete();
        $notification = array(
            'message' => 'Room Number Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    // public function DeleteRoom($id){
    //     $room = Room::find($id);

    //     if(file_exists($room->image) AND !empty($room->image)){
    //         unlink($room->image);
    //     }

    //     $subimage = MultiImage::where('rooms_id', $id)->get()->toArray();

    //     if(!empty($subimage)){
    //         foreach($subimage as $value){
    //             if(!empty($value)){
    //                 unlink( $value->multi_img );
    //             }
    //         }
    //     }

    //     RoomType::where('id', $room->roomtype_id)->delete();
    //     MultiImage::where('rooms_id', $room->id)->delete();
    //     Facility::where('rooms_id', $room->id)->delete();
    //     RoomNumber::where('rooms_id', $room->id)->delete();
    //     $room->delete();

    //     $notification = array(
    //         'message' => 'Room Deleted Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->back()->with($notification);
    // }

    public function DeleteRoom($id) {
        $room = Room::find($id);
    
        // Delete room image if it exists
        if (!empty($room->image) && file_exists($room->image)) {
            unlink($room->image);
        }
    
        // Delete associated multi-images
        $multiImages = MultiImage::where('rooms_id', $id)->get();
        foreach ($multiImages as $multiImage) {
            if (!empty($multiImage->multi_img) && file_exists($multiImage->multi_img)) {
                unlink($multiImage->multi_img);
            }
        }
        // Delete multi-images records from the database
        MultiImage::where('rooms_id', $id)->delete();
    
        // Delete associated room type, facilities, and room numbers
        RoomType::where('id', $room->roomtype_id)->delete();
        Facility::where('rooms_id', $room->id)->delete();
        RoomNumber::where('rooms_id', $room->id)->delete();
    
        // Delete the room
        $room->delete();
    
        // Redirect back with success message
        return redirect()->back()->with([
            'message' => 'Room Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }
    
}

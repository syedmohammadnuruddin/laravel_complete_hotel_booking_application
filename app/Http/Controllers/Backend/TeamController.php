<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TeamController extends Controller
{
    public function AllTeam(){
        $team = Team::latest()->get();
        return view('backend.team.all_team',compact('team'));
    }

    public function AddTeam(){
        return view('backend.team.add_team');
    }

    public function storeTeam(Request $request){
        if($request->file('image')){
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(550,670);
            
            $img->toJpeg(80)->save(base_path('public/upload/team/'.$name_gen));
            $save_url = 'upload/team/'.$name_gen;

            Team::insert([
                'name'=>$request->name,
                'position'=>$request->position,
                'facebook'=>$request->facebook,
                'image'=>$save_url,
                'created_at'=>Carbon::now()
            ]);

        }

        $notification = array(
            'message'=> 'Team Data Inserted Successfully',
            'alert-type'=>'success'
        );

        return redirect()->route('all.team')->with($notification);
    }

    public function EditTeam($id){
        $team = Team::find($id);
        return view('backend.team.edit_team', compact('team'));
    }

    public function UpdateTeam(Request $request){
        $team_id = $request->id;
        $team = Team::find($team_id);
        if($request->file('image')){
            // Delete the existing image if it exists
            if($team->image){
                $image_path = public_path($team->image);
                if(file_exists($image_path)){
                    unlink($image_path);
                }
            }
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(550,670);
            
            $img->toJpeg(80)->save(base_path('public/upload/team/'.$name_gen));
            $save_url = 'upload/team/'.$name_gen;

            Team::findOrFail($team_id)->update([
                'name'=>$request->name,
                'position'=>$request->position,
                'facebook'=>$request->facebook,
                'image'=>$save_url,
                'created_at'=>Carbon::now()
            ]);
            $notification = array(
                'message' => 'Team Updated With Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);
        }else{
            Team::findOrFail($team_id)->update([
                'name'=>$request->name,
                'position'=>$request->position,
                'facebook'=>$request->facebook,
                'created_at'=>Carbon::now()
            ]);
            $notification = array(
                'message' => 'Team Updated Without Image Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.team')->with($notification);
        }
        
    }

    // public function UpdateTeam(Request $request){
    //     $team_id = $request->id;
    //     $team = Team::findOrFail($team_id);
        
    //     if($request->file('image')){
    //         // Delete the existing image if it exists
    //         if($team->image){
    //             $image_path = public_path($team->image);
    //             if(file_exists($image_path)){
    //                 unlink($image_path);
    //             }
    //         }
            
    //         // Save the new image
    //         $manager = new ImageManager(new Driver());
    //         $name_gen = hexdec(uniqid()).'.'.$request->file('image')->getClientOriginalExtension();
    //         $img = $manager->read($request->file('image'));
    //         $img = $img->resize(550,670);
    //         $img->toJpeg(80)->save(public_path('upload/team/'.$name_gen));
    //         $save_url = 'upload/team/'.$name_gen;
    //     }else{
    //         $save_url = $team->image;
    //     }
    
    //     // Update team details
    //     $team->update([
    //         'name' => $request->name,
    //         'position' => $request->position,
    //         'facebook' => $request->facebook,
    //         'image' => $save_url,
    //         'created_at' => Carbon::now()
    //     ]);
    
    //     // Prepare notification
    //     $notification = [
    //         'message' => $request->file('image') ? 'Team Updated With Image Successfully' : 'Team Updated Without Image Successfully',
    //         'alert-type' => 'success'
    //     ];
    
    //     // Redirect with notification
    //     return redirect()->route('all.team')->with($notification);
    // }

    public function DeleteTeam($id){
        $item = Team::findOrFail($id);
        $img = $item->image;
        unlink($img);

        Team::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Team Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
    
}

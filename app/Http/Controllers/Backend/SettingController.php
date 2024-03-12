<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmtpSetting;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SettingController extends Controller
{
    public function SmtpSetting(){

        $smtp = SmtpSetting::find(1);
        return view('backend.setting.smpt_update',compact('smtp'));

    }
    public function SmtpUpdate(Request $request){

        $smtp_id = $request->id;

        SmtpSetting::find($smtp_id)->update([
            'mailer' => $request->mailer,
            'host' => $request->host,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'encryption' => $request->encryption,
            'from_address' => $request->from_address,
        ]);

        $notification = array(
            'message' => 'Smtp Setting Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);  
    }
    public function SiteSetting(){

        $site = SiteSetting::find(1);
        return view('backend.site.site_update',compact('site'));

    }
    public function SiteUpdate(Request $request){
        $site_id = $request->id;
        $sitesetting = SiteSetting::find($site_id);
        if($request->file('logo')){
            // Delete the existing image if it exists
            if($sitesetting->logo){
                $image_path = public_path($sitesetting->logo);
                if(file_exists($image_path)){
                    unlink($image_path);
                }
            }
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$request->file('logo')->getClientOriginalExtension();
            $img = $manager->read($request->file('logo'));
            $img = $img->resize(110,44);
            
            $img->toJpeg(80)->save(base_path('public/upload/site/'.$name_gen));
            $save_url = 'upload/site/'.$name_gen;

            SiteSetting::findOrFail($site_id)->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'email' => $request->email,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'copyright' => $request->copyright,
                'logo' => $save_url, 
            ]);
            $notification = array(
                'message' => 'Site Setting Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
        }else{
            SiteSetting::findOrFail($site_id)->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'email' => $request->email,
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'copyright' => $request->copyright, 
            ]);
            $notification = array(
                'message' => 'Site Setting Updated Successfully',
                'alert-type' => 'success'
            );

            return redirect()->back()->with($notification);
        }
        
    }
}

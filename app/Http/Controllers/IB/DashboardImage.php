<?php


namespace App\Http\Controllers\IB;

use App\Http\Controllers\Controller;
use App\IbImage;
use App\IbLoginImage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class DashboardImage extends Controller
{
    //

    public function create(){
        $ibImages = IbImage::orderBy('id', 'DESC')->get();
        $ibLoginImages = IbLoginImage::orderBy('id', 'DESC')->get();
        return view('ib/images/create', compact('ibImages','ibLoginImages'));
    }


    public function store(Request $request){
		
        if(isset($request->title)) {
           $request->validate([
                'image_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'title' => 'required',
                'desc' => 'required'
            ]);
        }else{
            $request->validate([
                'image_name' => 'required',
            ]);
        }

		//$request->image_name->move(public_path('images'), $imageName);
		//$image_path = public_path('images').'/'.$imageName;
        $imageName = time().'.'.$request->image_name->getClientOriginalExtension();
       
		$request->image_name->move('/var/www/html/internet_banking/public/images/', $imageName);

        
		$image_path = '/var/www/html/internet_banking/public/images/'.$imageName;

        $status = 'active';

            if(isset($request->title))
            {
				$insert = IbImage::insert([
					'title'=>$request->title,
					'description'=>$request->desc,
					'photo_path'=>$image_path,
					'status'=>$status,
					'image_name'=>$imageName
				
				]);
				
            }else{
                $insert = IbLoginImage::insert([
                    'photo_path'=>$image_path,
                    'status'=>$status,
                    'image_name'=>$imageName
                ]);
            }


            if($insert==true)
            {
                $notification="Image Uploaded successfully!";
                $color="success";
            }else{
                $notification="Something went wrong!";
                $color="danger";
            }

            return redirect('ib/image_upload')->with('notification',$notification)->with('color',$color);

    }

    //delete an image
    public function delete(Request $r){
        $id = $r->id;
        $i = $r->i;
        if($i == 'dashboard'){
            $image = IbImage::where('id', $id)->get()[0];
        }elseif($i =='login'){
            $image = IbLoginImage::where('id', $id)->get()[0];
        }
        
        $path = $image->photo_path;
        //return $path;
        $delete = File::delete($path);

        if($delete){
            $notification = "Image file deleted successfully!";
            $color = "success";
        }else{
            $notification = "Image deleted unsuccessfully, failed to delete actual image!";
            $color = "danger";
        }


        if($image->delete()){
            $notification = "Image deleted successfully!";
            $color = "success";
        }else{
            $notification = "Image deleted unsuccessfully!";
            $color = "danger";
        }
        return redirect()->back()->with('notification',$notification)->with('color',$color);
    }
}


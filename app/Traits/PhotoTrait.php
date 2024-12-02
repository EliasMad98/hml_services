<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

trait PhotoTrait {
    protected function saveImage($photo,$alias_name,$folder): string
    {
        if($photo==null) return '';
        $random=  Str::ulid();
        $file_extension=$photo->getClientOriginalExtension();
        $file_name=$alias_name.$random.'.'.$file_extension;
        $path=$folder;
        $photo->move($path,$file_name);
        return $folder.'/'.$file_name;
    }
    protected function saveBase64File($base64,$alias_name,$folder): string
    {
        if($base64==null) return '';

        switch (mb_substr($base64, 0, 1)) {
            case '/':
              $extension = 'jpeg';
              break;
            case 'i':
              $extension = 'png';
              break;
            case 'R':
              $extension = 'gif';
              break;
            case 'U':
              $extension = 'webp';
              break;
            case 'J':
              $extension = 'pdf';
              break;
            case "A":
              $extension = "mp4";
              break;
            case 'S':
              $extension = 'mp3';
              break;
            default:
              $extension = 'unknown';
          }
        $file = $base64;  // your base64 encoded
        // $extension = explode('/', mime_content_type($file))[1];
        // $file = str_replace("data:image/$extension;base64,", '', $file);
        // $file = str_replace(' ', '+', $file);
        $fileName = $alias_name.Str::ulid().'.'.$extension;
        File::put($folder.'/'.$fileName, base64_decode($file));
        return $folder.'/'.$fileName;
    }


    // public function verifyAndUpload(Request $request, $fieldname = 'image', $directory = 'images' ) {

    //     if( $request->hasFile( $fieldname ) ) {

    //         if (!$request->file($fieldname)->isValid()) {

    //             flash('Invalid Image!')->error()->important();

    //             return redirect()->back()->withInput();

    //         }

    //         return $request->file($fieldname)->store($directory, 'public');

    //     }

    //     return null;

    // }

}

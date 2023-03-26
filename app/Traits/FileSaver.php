<?php


namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\DbDumper\Databases\MySql;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait FileSaver
{

    /*
     |--------------------------------------------------------------------------
     | UPLOAD FILE IN WEBP FORMAT WITH CUSTOM RESIZE OPTION
     |--------------------------------------------------------------------------
    */



    public function UploadWebp($file, $model, $database_field_name, $basePath, $width, $height)
    {


        if ($file) {

            try {
                $month = Carbon::now()->format('M');
                $basePath = 'assets/' . $basePath . '/' . date('Y') . '/' . $month . '/';

                $image_name = time() . '.' . 'webp';

                if (file_exists($model->$database_field_name)) {
                    unlink($model->$database_field_name);
                }


                if (!is_dir($basePath)) {
                    \File::makeDirectory($basePath, 493, true);
                }


                Image::make($file->getRealPath())->encode('webp', 90)->fit($width, $height)->save($basePath . '/' . $image_name);

                $model->update([$database_field_name => ($basePath . $image_name)]);
            } catch (\Exception $ex) {

                dd($ex);

            }
        }
    }




    /*
     |--------------------------------------------------------------------------
     | SLUG GENERATOR
     |--------------------------------------------------------------------------
    */


    public function GenerateSlug($slug_field_name, $model, $slug_name)
    {

        if ($slug_name) {

            $slug = Str::slug($slug_name);

            // Check existing slug in db**

            $check = $model->where($slug_field_name,$slug)->first();

            if ($check) {

                $update_slug = $slug.rand(100,200);
                $model->update([$slug_field_name => $update_slug]);

            }else {

                $model->update([$slug_field_name => $slug]);
            }
        }
    }




    /*
     |--------------------------------------------------------------------------
     | UPLOAD FILE IN LOCAL DISK, LOCAL DISK MEANS PUBLIC DIRECOTROY
     |--------------------------------------------------------------------------
    */

    public function upload_file($uploaded_file, $model, $database_field_name, $base_path)
    {
        // <!-- upload file -->
        if ($uploaded_file) {

            $month = Carbon::now()->format('M');

            // <!-- delete file if exist -->
            if (file_exists($model->$database_field_name)) {
                unlink($model->$database_field_name);
            }

            // <!-- create unique file name -->
            $new_file_name   = time() . uniqid() .'.'. $uploaded_file->getClientOriginalExtension();



            // <!-- create upload directory -->
            $directory   = 'public/' . $base_path . '/';




            // <!-- create store file to directory -->
            $uploaded_file->move($directory, $new_file_name);



            // <!-- update file name to database -->
            $model->update([$database_field_name => $directory . $new_file_name]);
        }
    }






}

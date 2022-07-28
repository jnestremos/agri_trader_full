<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::command('images:clear', function () {
//     $pattern = "/catalog_images\/*/";
//     for($i = 0; $i < count(Storage::disk('public')->allFiles()); $i++){      
//         if(Storage::disk('public')->allFiles()[$i] != '.gitignore' || preg_match($pattern, Storage::disk('public')->allFiles()[$i])){
//             Storage::disk('public')->delete(Storage::disk('public')->allFiles()[$i]);
//         }
//     }
//     $this->comment('Images Deleted!');
// })->purpose('To clear all images after clean migration');

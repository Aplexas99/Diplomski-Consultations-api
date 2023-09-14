<?php

use App\Http\Controllers\GoogleCalendarController;
use App\Models\User;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/signin', function () {
    require_once 'C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\vendor\autoload.php';
    if(isset($_GET['code'])){
       $code = $_GET['code'];
        
        return redirect()->away('http://localhost:8000/api/code?code='.$code.'');
    }
    return redirect()->away('http://localhost:4200');
});

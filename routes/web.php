<?php

use App\Http\Controllers\BuisnessController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome')->name('index')->middleware('guest');
Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('buisnesses/{buisness}/staff', [StaffController::class, 'show'])->name('buisnesses.staff');
Route::post('buisnesses/{buisness}/staff', [StaffController::class, 'store'])->name('buisnesses.staff.store');
Route::delete('buisnesses/{buisness}/staff', [StaffController::class, 'destroy'])->name('buisnesses.staff.destroy');
Route::resource('buisnesses', BuisnessController::class)->only('create', 'store', 'show');
Route::get('buisnesses/{buisness}/{ticket}', [BuisnessController::class, 'showTicket'])->name('buisnesses.ticket-show');
Route::put('buisnesses/{buisness}/{ticket}', [BuisnessController::class, 'sendTicketMessage'])->name('buisnesses.send-ticket-message');
Route::get('/b/{buisness}', [BuisnessController::class, 'redirect'])->name('buisness');
Route::get('/tickets/{buisness}', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/{buisness}/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets/{buisness}', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{buisness}/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
Route::put('/tickets/{buisness}/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
Route::delete('/tickets/{buisness}/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

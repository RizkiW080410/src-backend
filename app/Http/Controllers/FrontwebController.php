<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FrontwebController extends Controller
{
    public function index() {
        $mejas = Table::all();
        return view('tampilan_web.index', compact('mejas'));
    }

    public function submitBooking(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_book' => 'required|date|after:now',
            'finish_book' => 'required|date|after:start_book',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
        ]);
    
        $tableId = $request->input('table_id');
        $startBook = $request->input('start_book');
        $finishBook = $request->input('finish_book');
    
        $conflictingBookings = Booking::where('table_id', $tableId)
            ->where(function($query) use ($startBook, $finishBook) {
                $query->whereBetween('start_book', [$startBook, $finishBook])
                      ->orWhereBetween('finish_book', [$startBook, $finishBook])
                      ->orWhereRaw('? BETWEEN start_book AND finish_book', [$startBook])
                      ->orWhereRaw('? BETWEEN start_book AND finish_book', [$finishBook]);
            })
            ->exists();
    
        if ($conflictingBookings) {
            return response()->json(['success' => false, 'message' => 'The selected time is not available. Please choose a different time.']);
        }
    
        DB::beginTransaction();
    
        try {
            $booking = new Booking();
            $booking->table_id = $tableId;
            $booking->start_book = $startBook;
            $booking->finish_book = $finishBook;
            $booking->fullname = $request->input('fullname');
            $booking->phone = $request->input('phone');
            $booking->email = $request->input('email');
            $booking->status = 'Pending'; // Status awal sebelum dikonfirmasi
            $booking->save();
    
            // Ubah status menjadi 'booking'
            $booking->status = 'Booking';
            $booking->save();
    
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Booking submitted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error submitting booking. Please try again.']);
        }
    }    

    public function fetchUnavailableTimes(Request $request) {
        $table_id = $request->get('table_id');
        $date = $request->get('date');

        $bookings = Booking::where('table_id', $table_id)
            ->whereDate('start_book', $date)
            ->orWhereDate('finish_book', $date)
            ->get(['start_book', 'finish_book']);

        $unavailable_times = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->start_book)->format('H:i:s');
            $end = Carbon::parse($booking->finish_book)->format('H:i:s');
            $unavailable_times[] = [$start, $end];
        }

        return response()->json(['unavailable_times' => $unavailable_times]);
    }
}

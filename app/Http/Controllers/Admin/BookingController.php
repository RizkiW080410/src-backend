<?php

namespace App\Http\Controllers\Admin;

use App\Models\Table;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBookingRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $currentDateTime = Carbon::now();

        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            if ($currentDateTime->greaterThanOrEqualTo(Carbon::parse($booking->finish_book)) && $booking->status != 'Selesai') {
                $booking->update(['status' => 'Selesai']);
                $table = Table::find($booking->table_id);
                if ($table) {
                    $table->status = 'kosong';
                    $table->save();
                }
            }
        }

        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        abort_if(Gate::denies('booking_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $availableTables = Table::where('status', 'kosong')->get();

        return view('admin.bookings.create', compact('availableTables'));
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create($request->all());

        $table = Table::find($booking->table_id);
        if ($table && $booking->status == 'Main') {
            $table->status = 'penuh';
            $table->save();
        }

        return redirect()->route('admin.bookings.index');
    }

    public function edit(Booking $booking)
    {
        abort_if(Gate::denies('booking_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tables = Table::pluck('name', 'id')->all();

        return view('admin.bookings.edit', compact('booking', 'tables'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $oldStatus = $booking->status;
        $booking->update($request->all());

        $table = Table::find($booking->table_id);
        if ($table) {
            if ($oldStatus != 'Main' && $booking->status == 'Main') {
                $table->status = 'penuh';
            } elseif ($oldStatus == 'Main' && $booking->status == 'Selesai') {
                $table->status = 'kosong';
            }
            $table->save();
        }

        return redirect()->route('admin.bookings.index');
    }

    public function show(Booking $booking)
    {
        abort_if(Gate::denies('booking_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $booking->load('table');

        return view('admin.bookings.show', compact('booking'));
    }

    public function destroy(Booking $booking)
    {
        abort_if(Gate::denies('booking_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $booking->delete();

        return back();
    }

    public function massDestroy(MassDestroyBookingRequest $request)
    {
        $bookings = Booking::find(request('ids'));

        foreach ($bookings as $booking) {
            $booking->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

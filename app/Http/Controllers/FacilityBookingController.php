<?php

namespace App\Http\Controllers;

use App\Models\FacilityBooking;
use App\Models\IncomeCategory;
use App\Models\BankAccount;
use App\Models\HandCash;
use App\Models\MobileBankingAccount;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityBookingController extends Controller
{
    public function hub()
    {
        $cards = [
            ['icon' => 'fa-calendar-plus',  'title' => 'New Booking',    'subtitle' => 'Create a facility booking',  'route' => 'facilities.bookings.create', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-list',           'title' => 'All Bookings',   'subtitle' => 'View all bookings',          'route' => 'facilities.bookings.index',  'from' => '#4f46e5', 'to' => '#7c3aed'],
        ];
        return view('pages.facilities.hub', compact('cards'));
    }

    public function index()
    {
        $bookings = FacilityBooking::latest('booking_date')->paginate(20);
        $total    = FacilityBooking::where('status', 'confirmed')->sum('amount');
        return view('pages.facilities.index', compact('bookings', 'total'));
    }

    public function create()
    {
        $bankAccounts   = BankAccount::all();
        $mobileAccounts = MobileBankingAccount::all();
        $handCashes     = HandCash::all();
        return view('pages.facilities.create', compact('bankAccounts', 'mobileAccounts', 'handCashes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'facility_name'  => 'required|string|max:255',
            'booking_date'   => 'required|date_format:d/m/Y',
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'status'         => 'required|in:pending,confirmed,cancelled',
        ]);

        DB::transaction(function () use ($request) {
            $booking = FacilityBooking::create([
                'title'          => $request->title,
                'facility_name'  => $request->facility_name,
                'booking_date'   => \Carbon\Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d'),
                'start_time'     => $request->start_time,
                'end_time'       => $request->end_time,
                'booked_by'      => $request->booked_by,
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'account_type'   => $request->account_type,
                'account_id'     => $request->account_id,
                'status'         => $request->status,
                'notes'          => $request->notes,
                'recorded_by'    => auth()->id(),
            ]);

            if ($booking->status === 'confirmed' && $booking->amount > 0) {
                $category = IncomeCategory::firstOrCreate(
                    ['slug' => 'facility-booking'],
                    ['name' => 'Facility Booking', 'is_active' => true]
                );

                $booking->recordIncome($category->id, $booking->title, [
                    'amount'       => $booking->amount,
                    'account_type' => $booking->account_type,
                    'account_id'   => $booking->account_id,
                    'income_date'  => $booking->booking_date->toDateString(),
                    'payment_method' => $booking->payment_method,
                ]);

                if ($booking->account_type && $booking->account_id) {
                    AccountTransaction::record(
                        $booking->account_type,
                        $booking->account_id,
                        'credit',
                        $booking->amount,
                        'income',
                        null,
                        $booking->title,
                        $booking->booking_date,
                        FacilityBooking::class,
                        $booking->id,
                        auth()->id()
                    );
                }
            }
        });

        return redirect()->route('facilities.bookings.index')->with('success', 'Booking created successfully.');
    }

    public function show(FacilityBooking $facilityBooking)
    {
        return view('pages.facilities.show', ['booking' => $facilityBooking]);
    }

    public function edit(FacilityBooking $facilityBooking)
    {
        $bankAccounts   = BankAccount::all();
        $mobileAccounts = MobileBankingAccount::all();
        $handCashes     = HandCash::all();
        return view('pages.facilities.edit', [
            'booking'        => $facilityBooking,
            'bankAccounts'   => $bankAccounts,
            'mobileAccounts' => $mobileAccounts,
            'handCashes'     => $handCashes,
        ]);
    }

    public function update(Request $request, FacilityBooking $facilityBooking)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'facility_name'  => 'required|string|max:255',
            'booking_date'   => 'required|date_format:d/m/Y',
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'status'         => 'required|in:pending,confirmed,cancelled',
        ]);

        $facilityBooking->update([
            'title'          => $request->title,
            'facility_name'  => $request->facility_name,
            'booking_date'   => \Carbon\Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d'),
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
            'booked_by'      => $request->booked_by,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'account_type'   => $request->account_type,
            'account_id'     => $request->account_id,
            'status'         => $request->status,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('facilities.bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(FacilityBooking $facilityBooking)
    {
        AccountTransaction::removeSource(FacilityBooking::class, $facilityBooking->id);
        Transaction::where('transactionable_type', FacilityBooking::class)
            ->where('transactionable_id', $facilityBooking->id)
            ->delete();
        $facilityBooking->delete();

        return redirect()->route('facilities.bookings.index')->with('success', 'Booking deleted.');
    }
}

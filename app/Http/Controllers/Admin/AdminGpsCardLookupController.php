<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GpsCardLookupService;
use Illuminate\Http\Request;

class AdminGpsCardLookupController extends Controller
{
    public function __construct(private readonly GpsCardLookupService $lookupService)
    {
    }

    public function index()
    {
        return view('admin.gps_card_lookup.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'card_number' => 'required|digits:16',
        ]);

        $cardNumber = preg_replace('/\D+/', '', (string) $request->input('card_number'));
        $lookup = $this->lookupService->findByCardNumber($cardNumber);

        if (! $lookup) {
            return back()
                ->withErrors(['card_number' => 'No GPS card found with this 16-digit number.'])
                ->withInput();
        }

        return view('admin.gps_card_lookup.result', compact('lookup', 'cardNumber'));
    }
}

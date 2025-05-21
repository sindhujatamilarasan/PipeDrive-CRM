<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{

    public function getByEmail(Request $request)
    {
        $email = $request->query('email');

        // Fetch data from your internal app or Stripe
        $transactions =  Http::timeout(15)->withOptions([
            'verify' => false,
        ])->get('https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data', [
            'email' => $email,
        ]);


        return response()->json($transactions->json());
    }

}

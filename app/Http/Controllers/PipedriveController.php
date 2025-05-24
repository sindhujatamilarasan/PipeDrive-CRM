<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PipedriveController extends Controller
{
    public function redirectToPipedrive()
    {
        $query = http_build_query([
            'client_id' => config('services.pipedrive.client_id'),
            'redirect_uri' => config('services.pipedrive.redirect'),
            'response_type' => 'code',
        ]);

            $url = "https://oauth.pipedrive.com/oauth/authorize?$query";

           // dd(config('services.pipedrive.client_id'));
        return redirect($url);
    }

    public function handleCallback(Request $request)
    {
        $response = Http::asForm()->withOptions([
            'verify' => false, // Disable SSL cert check (only for local)
        ])->post('https://oauth.pipedrive.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'redirect_uri' => config('services.pipedrive.redirect'),
            'client_id' => config('services.pipedrive.client_id'),
            'client_secret' => config('services.pipedrive.client_secret'),
        ]);


        $data = $response->json();
       //dd($data);

        Cache::put('pipedrive_access_token', $data['access_token'], now()->addSeconds($data['expires_in']));
        Cache::put('pipedrive_api_domain', $data['api_domain'], now()->addSeconds($data['expires_in']));
        Cache::put('pipedrive_refresh_token', $data['refresh_token']);

        return redirect("https://sindhuja-sandbox.pipedrive.com/pipeline");
    }

    public function getPipedriveUser()
    {
        $token = Cache::get('pipedrive_access_token');

        if (!$token) {
            return 'Access token not found. Authenticate first.';
        }

        $response = Http::withToken($token)->withOptions([
            'verify' => false, // Disable SSL cert check (only for local)
        ])->get('https://api.pipedrive.com/v1/users/me');

        return $response->json();
    }



public function showPanel(Request $request)
{
 #To fetch json data
//  $curl = curl_init();

//     curl_setopt_array($curl, [
//         CURLOPT_URL => 'https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data?email=my_cool_customer%40example.com',
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => '',
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 30,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => 'GET',
//     ]);

//     $response = curl_exec($curl);

//     if (curl_errno($curl)) {
//         $error_msg = curl_error($curl);
//         curl_close($curl);
//         // Return error JSON response
//         return response()->json(['error' => $error_msg], 500);
//     }

//     curl_close($curl);

//     // Decode to check if valid JSON, else return error
//     $jsonData = json_decode($response, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         return response()->json(['error' => 'Invalid JSON received from API'], 500);
//     }

//     return response()->json($jsonData)
//         ->header('X-Frame-Options', 'ALLOW-FROM https://app.pipedrive.com')
//         ->header('Content-Security-Policy', "frame-ancestors https://app.pipedrive.com; connect-src 'self' https://api-segment.pipedrive.com https://esp-eu.aptrinsic.com https://sesheta.pipedrive.com;");


#To render HTML
 // Fetch your data from external API (via Http or cURL)
      $email = $request->query('email');
    $email = 'my_cool_customer@example.com';
        if (!$email) {
            return response()->json(['error' => 'Email parameter is required'], 400);
        }

        Log::info('Fetching invoices for email: ' . $email);

        // Call external API, passing the email
        $response = Http::get('https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data', [
            'email' => $email,
        ]);

        if ($response->failed()) {
            Log::error('Failed to fetch invoices', ['email' => $email, 'response' => $response->body()]);
            return response()->json(['error' => 'Failed to fetch invoices'], 500);
        }

        $data = $response->json();

        // Log the API response for debugging
        Log::info('Stripe API response:', ['response' => $data]);

        // Assuming invoices are in $data['invoices']
        $invoices = data_get($data, 'invoices', []);

        // Otherwise, just return the blade view for initial page load
     return response()
        ->view('pipedrive.panel', compact('invoices'))
        ->header('X-Frame-Options', 'ALLOW-FROM https://app.pipedrive.com')
        ->header('Content-Security-Policy', "frame-ancestors https://app.pipedrive.com;");
}


    // public function showPanel(Request $request)
    // {
    //   return response(
    //         '<!DOCTYPE html>
    //         <html>
    //         <head>
    //             <meta charset="UTF-8">
    //             <title>Custom Panel</title>
    //             <style>body { font-family: sans-serif; padding: 2em; }</style>
    //         </head>
    //         <body>
    //             <h1>Hello from custom panel</h1>
    //             <p>If you see this, iframe is working!</p>
    //         </body>
    //         </html>'
    //    )
    //     ->header('Content-Type', 'text/html')
    //     ->header('X-Frame-Options', 'ALLOWALL')
    //     ->header('Content-Security-Policy', 'frame-ancestors *');
    //     //->header('Content-Security-Policy', "frame-ancestors https://sindhuja-sandbox.pipedrive.com https://app.pipedrive.com")

    // // Remove X-Frame-Options header


    //     // $email = "my_cool_customer@example.com";

    //     // $response = Http::timeout(15)->get('https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data', [
    //     //     'email' => $email,
    //     // ]);

    //     // $data = $response->ok() ? $response->json() : [];

    //     // return response()
    //     //     ->view('pipedrive.panel')
    //     //     ->header('Content-Type', 'text/html')
    //     //     //->header('X-Frame-Options', '')
    //     //     ->header('Content-Security-Policy', "frame-ancestors 'self' https://pipedrive.com https://*.pipedrive.com;");
    // }

    public function panelPayload(Request $request)
    {
        return response()->json([
            'iframe' => [
                'url' => 'https://sells-ladder-framed-reconstruction.trycloudflare.com/iframe-panel-content',
                'height' => 400
            ]
        ]);
    }


}

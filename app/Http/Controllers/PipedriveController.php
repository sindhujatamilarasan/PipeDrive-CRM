<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
            'verify' => false,
        ])->get('https://api.pipedrive.com/v1/users/me');
        return $response->json();
    }

    public function showPanel(Request $request)
    {
        $personId = $request->selectedIds;
        $primaryEmail = null;
        $pipedriveResponse = Http::withOptions(['verify' => false])
            ->get("https://api.pipedrive.com/v1/persons/{$personId}", [
                'api_token' => config('services.pipedrive.api'),
            ]);
        if ($pipedriveResponse->successful() && isset($pipedriveResponse['data']['email'])) {
            $emails = $pipedriveResponse['data']['email'];
            $primaryEmail = is_array($emails) && count($emails) > 0 ? $emails[0]['value'] : null;
        }
        if (!$primaryEmail) {
            return response('<h3>No email found for the selected person.</h3>')
                ->header('Content-Type', 'text/html')
                ->header('X-Frame-Options', 'ALLOW-FROM https://app.pipedrive.com')
                ->header('Content-Security-Policy', "frame-ancestors https://app.pipedrive.com;");
        }
        $response = Http::withOptions(['verify' => false])
                ->get('https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data', [
                    'email' => $primaryEmail
        ]);

        if (!$response->successful()) {
            return '<p>Error loading data.</p>';
        }
        $data = $response->json();
        return response()->view('pipedrive.panel', [
            'email' => $primaryEmail,
            'invoices' => $data['invoices'] ?? [],
            'charges' => $data['charges'] ?? [],
        ])
        ->header('X-Frame-Options', 'ALLOW-FROM https://app.pipedrive.com')
        ->header('Content-Security-Policy', "frame-ancestors https://app.pipedrive.com;");
    }
}

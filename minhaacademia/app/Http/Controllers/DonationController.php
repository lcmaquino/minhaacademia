<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Redirect to donation url.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donationURLSetting = Setting::where(['key' => 'donation_url'])->first();
        $donationURL = empty($donationURLSetting) ? '' : $donationURLSetting->value;

        if(empty($donationURL)) {
            return redirect()->route('home');
        }else{
            return redirect()->away($donationURL);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Certify;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
use DomPDF;

class CertifyController extends Controller
{
    /**
     * Class CertifyController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Certify::class, 'certify');
    }

    /**
     * Display a form for verify a certify.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('site.certifies');
    }

    /**
     * Display a form for verify a certify.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $code = $request->query('code');
        if (empty($code)) {
            return view('site.certifiesVerify');
        }else{
            $certify = Certify::where(['code' => $code])->first();
            return view('site.certifiesVerify', ['code' => $code, 'certify' => $certify]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Certify  $certify
     * @return \Illuminate\Http\Response
     */
    public function show(Certify $certify)
    {   
        $settingKeys = [
            'certify_signature_name',
            'certify_state',
            'youtube_channel_title',
        ];
        $param = [];

        foreach($settingKeys as $key){
            $setting = Setting::where(['key' => $key])->first();
            $param[Str::camel($key)] = empty($setting) ? '' : $setting->value;
        }

        $param['certify'] = $certify;
        DomPDF::setOption([
              'dpi' => 150,
              'defaultFont' => 'sans-serif',
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true
              ]);
        $pdf = DomPDF::loadView('site.showCertifyPDF', $param)->setPaper('a4', 'landscape');

        return $pdf->download(Str::slug($certify->name) ."-". Str::slug($certify->title) . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Only admin users can run this method.
     * 
     * @param  \App\Certify  $certify
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certify  $certify)
    {
        $certify->delete();
           
        return redirect()->route('certifies');
    }
}

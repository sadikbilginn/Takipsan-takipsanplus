<?php

namespace App\Http\Controllers;

use App\Locale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{

    public function change($locale)
    {
        $locales = Locale::where('abbr', $locale)->first();
        if($locales)
        {
            app()->setLocale($locale);
            session()->put('locale', $locale);
        }

        return redirect()->back();
    }


}

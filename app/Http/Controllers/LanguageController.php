<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, config('app.available_locales'))) {
            abort(400);
        }

        // Store locale in session
        $request->session()->put('locale', $locale);

        // Redirect back
        return redirect()->back();
    }
}

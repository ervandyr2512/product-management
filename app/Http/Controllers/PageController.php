<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        // TODO: In production, send email to support team or save to database
        // For now, just return success message

        return back()->with('success', 'Terima kasih! Pesan Anda telah terkirim. Tim kami akan menghubungi Anda segera.');
    }
}

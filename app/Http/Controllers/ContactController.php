<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact');
    }

    public function send(ContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Mail::to(config('mail.contact_to', 'careentry.info@gmail.com'))
            ->send(new ContactReceived($data));

        return redirect()
            ->route('contact.show')
            ->with('success', 'お問い合わせを受け付けました。担当者より平日10:00〜18:00の間にご連絡いたします。');
    }
}

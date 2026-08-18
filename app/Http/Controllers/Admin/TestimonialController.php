<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $testimonials = Testimonial::latest()->paginate(15);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        return view('admin.testimonials.form');
    }

    public function store(Request $request): RedirectResponse
    {
        Testimonial::create([
            ...$this->validated($request),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect('/admin/testimonials')->with('success', 'Testimonial added.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            ...$this->validated($request),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect('/admin/testimonials')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect('/admin/testimonials')->with('success', 'Testimonial deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_name' => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'rating'      => 'nullable|integer|min:1|max:5',
            'content'     => 'required|string|max:1000',
        ]);
    }
}

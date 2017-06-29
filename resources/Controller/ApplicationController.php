<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionRequestDispatcher;
use App\ModelAdapters\UserAdapter as User;
use App\Exceptions\FieldException;

class ApplicationController extends Controller
{
    public function seccion(String $slug = null, Bool $hasError = false, String $message = null)
    {
        $UIApplication = request()->getUIApplication();

        $section = $UIApplication->getSectionBySlug($slug);

        $section->setFields();

        if ($hasError) {
            $section->setError([
                'message' => $message
                ]);
        }

        return view('front/application/seccion', compact('section'));
    }

    public function store(SectionRequestDispatcher $request, String $slug)
    {
        $UIApplication = request()->getUIApplication();

        $section = $UIApplication->getSectionBySlug($slug);

        $section->setFields();

        $section->save();

        if ($section->hasError()) {
            return redirect()->route('front.application.seccion', [
                'slug' => $slug,
                'hasError' => $section->hasError(),
                'message' => $section->getErrorMessage()
                ]);
        }

        $nextSectionSlug = $UIApplication->getNextSectionSlug($slug);

        if (!$nextSectionSlug) {
            return redirect()->route('front.application.completa');
        }

        return redirect()->route('front.application.seccion', ['slug' => $nextSectionSlug]);
    }

    public function completa()
    {
        return view('front/application/completa');
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display the About Us page.
     *
     * @return \Inertia\Response
     */
    public function about()
    {
        return Inertia::render('Web/AboutUs');
    }

    /**
     * Display the Contact page.
     *
     * @return \Inertia\Response
     */
    public function contact()
    {
        return Inertia::render('Web/Contact');
    }

    /**
     * Display the Terms & Conditions page.
     *
     * @return \Inertia\Response
     */
    public function terms()
    {
        return Inertia::render('Web/Terms');
    }
}

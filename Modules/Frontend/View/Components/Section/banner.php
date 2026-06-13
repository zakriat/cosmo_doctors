<?php

namespace Modules\Frontend\View\Components\section;

use Illuminate\View\Component;
use Illuminate\View\View;

class banner extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('frontend::components.section/banner');
    }
}

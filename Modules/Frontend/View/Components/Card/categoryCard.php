<?php

namespace Modules\Frontend\View\Components\card;

use Illuminate\View\Component;
use Illuminate\View\View;

class category_card extends Component
{
    /**
     * Create a new component instance.
     */
    public $category;
    public function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('frontend::components.card/category_card');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $modules = [
            [
                'name' => 'theme_mode',
                'val' => 'whiteTheme',
                'type' =>'string', 
            ],
            [
                'name' => 'Menubar_position',
                'val' => 'top',
                'type' =>'string', 
            ],
            [
                'name' => 'image_handling',
                'val' => 'new_image',
                'type' =>'string', 
            ],
            [
                'name' => 'menu_items',
                'val' => 'crop,rotate,flip,draw,shape,icon,text,mask',
                'type' =>'array', 
            ],
        ];
        foreach ($modules as $key => $value) {
        
            $service = [
                'name' => $value['name'],
                'val' => $value['val'],
                'type' => $value['type'],
                'datatype' => $value['datatype'] ?? null,
                'created_by' => 1,
                'updated_by' => 1,
            ];
            $service = Setting::create($service);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

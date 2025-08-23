<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use SampleDataSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DepartmentSeeder::class,
            DocumentTypeSeeder::class,
            UserSeeder::class,
            RoutingTemplateSeeder::class,
            SystemSettingSeeder::class,
            SampleDocumentSeeder::class,
        ]);
    }

    
}

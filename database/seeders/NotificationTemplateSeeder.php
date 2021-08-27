<?php

 namespace Database\Seeders;

 use Illuminate\Database\Seeder;
 use Illuminate\Support\Facades\DB;

 class NotificationTemplateSeeder extends Seeder
 {
    /**
     * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $templates = [
            [
                'name' => 'email-user-registration',
                'lang' => 'id',
                'type' => 'email',
                'required_data' => json_encode([
                    'fullName' => 'required|string|min:3',
                    'email' => 'required|string|filter:email'
                ]),
                'title' => 'Ini subject email',
                'template_path' => 'emails.user.registration'
            ],
            // add more templates here!
        ];

        DB::table('notification_templates')->insert($templates);
    }
}
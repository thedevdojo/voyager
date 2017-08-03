<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\FormBuilder;

class FormBuildersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        FormBuilder::firstOrCreate([
            'data_type_id' => 2,
            'details'      => json_encode([
                [
                    'class'  => 'col-md-8',
                    'panels' => [
                        [
                            'class'  => 'panel',
                            'title'  => 'Text Information',
                            'fields' => [
                                'title',
                                'excerpt',
                                'body',
                                'slug',
                            ],
                        ],
                        [
                            'class'  => 'panel panel-bordered panel-info',
                            'title'  => 'voyager.post.seo_content',
                            'fields' => [
                                'meta_keywords',
                                'meta_description',
                            ],
                        ],
                    ],
                ],
                [
                    'class'  => 'col-md-4',
                    'panels' => [
                        [
                            'class'  => 'panel panel-bordered panel-primary',
                            'title'  => 'voyager.post.details',
                            'fields' => [
                                'image',
                                'status',
                            ],
                        ],
                    ],
                ],
            ]),
        ]);
    }
}

<?php

use TCG\Voyager\Models\User;
use TCG\Voyager\Models\Post;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();

        // Lorem Ipsum Post
        $post = Post::firstOrNew([
            'slug' => 'lorem-ipsum-post',
        ]);
        if (!$post->exists) {
            $post->fill([
                'author_id'        => !is_null($user) ? $user->id : 0,
                'title'            => 'Lorem Ipsum Post',
                'excerpt'          => 'This is the excerpt for the Lorem Ipsum Post',
                'body'             => '<p>This is the body of the lorem ipsum post</p>',
                'image'            => 'posts/nlje9NZQ7bTMYOUG4lF1.jpg',
                'meta_description' => 'This is the meta description',
                'meta_keywords'    => 'keyword1, keyword2, keyword3',
                'status'           => 'PUBLISHED',
            ])->save();
        }

        // My Sample Post
        $post = Post::firstOrNew([
            'slug' => 'my-sample-post',
        ]);
        if (!$post->exists) {
            $post->fill([
                'author_id'        => !is_null($user) ? $user->id : 0,
                'title'            => 'My Sample Post',
                'excerpt'          => 'This is the excerpt for the sample Post',
                'body'             => '<p>This is the body for the sample post, which includes the body.</p>
<h2>We can use all kinds of format!</h2>
<p>And include a bunch of other stuff.</p>',
                'image'            => 'posts/7uelXHi85YOfZKsoS6Tq.jpg',
                'meta_description' => 'Meta Description for sample post',
                'meta_keywords'    => 'keyword1, keyword2, keyword3',
                'status'           => 'PUBLISHED',
            ])->save();
        }

        // Latest Post
        $post = Post::firstOrNew([
            'slug' => 'latest-post',
        ]);
        if (!$post->exists) {
            $post->fill([
                'author_id'        => !is_null($user) ? $user->id : 0,
                'title'            => 'Latest Post',
                'excerpt'          => 'This is the excerpt for the latest post',
                'body'             => '<p>This is the body for the latest post</p>',
                'image'            => 'posts/9txUSY6wb7LTBSbDPrD9.jpg',
                'meta_description' => 'This is the meta description',
                'meta_keywords'    => 'keyword1, keyword2, keyword3',
                'status'           => 'PUBLISHED',
            ])->save();
        }

        // Yarr Post
        $post = Post::firstOrNew([
            'slug' => 'yarr-post',
        ]);
        if (!$post->exists) {
            $post->fill([
                'author_id'        => !is_null($user) ? $user->id : 0,
                'title'            => 'Yarr Post',
                'excerpt'          => 'Reef sails nipperkin bring a spring upon her cable coffer jury mast spike marooned Pieces of Eight poop deck pillage. Clipper driver coxswain galleon hempen halter come about pressgang gangplank boatswain swing the lead. Nipperkin yard skysail swab lanyard Blimey bilge water ho quarter Buccaneer.',
                'body'             => '<p>Swab deadlights Buccaneer fire ship square-rigged dance the hempen jig weigh anchor cackle fruit grog furl. Crack Jennys tea cup chase guns pressgang hearties spirits hogshead Gold Road six pounders fathom measured fer yer chains. Main sheet provost come about trysail barkadeer crimp scuttle mizzenmast brig plunder.</p>
<p>Mizzen league keelhaul galleon tender cog chase Barbary Coast doubloon crack Jennys tea cup. Blow the man down lugsail fire ship pinnace cackle fruit line warp Admiral of the Black strike colors doubloon. Tackle Jack Ketch come about crimp rum draft scuppers run a shot across the bow haul wind maroon.</p>
<p>Interloper heave down list driver pressgang holystone scuppers tackle scallywag bilged on her anchor. Jack Tar interloper draught grapple mizzenmast hulk knave cable transom hogshead. Gaff pillage to go on account grog aft chase guns piracy yardarm knave clap of thunder.</p>',
                'image'            => 'posts/yuk1fBwmKKZdY2qR1ZKM.jpg',
                'meta_description' => 'this be a meta descript',
                'meta_keywords'    => 'keyword1, keyword2, keyword3',
                'status'           => 'PUBLISHED',
            ])->save();
        }
    }
}

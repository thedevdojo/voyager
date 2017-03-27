<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Page;

class PagesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $page = Page::firstOrNew([
            'slug' => 'hello-world',
        ]);
        if (!$page->exists) {
            $page->fill([
                'author_id' => 0,
                'title'     => 'Hello World',
                'excerpt'   => 'Hang the jib grog grog blossom grapple dance the hempen jig gangway pressgang bilge rat to go on account lugger. Nelsons folly gabion line draught scallywag fire ship gaff fluke fathom case shot. Sea Legs bilge rat sloop matey gabion long clothes run a shot across the bow Gold Road cog league.',
                'body'      => '<p>Hello World. Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>
<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>',
                'image'            => 'pages/AAgCCnqHfLlRub9syUdw.jpg',
                'meta_description' => 'Yar Meta Description',
                'meta_keywords'    => 'Keyword1, Keyword2',
                'status'           => 'ACTIVE',
            ])->save();
        }
    }
}

<?php

namespace TCG\Voyager\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Page;

class BreadMediaUploadTest extends TestCase
{
    protected $file = 'test.txt';
    protected $file_two = 'test2.txt';
    protected $file_three = 'test.pdf';
    protected $image_one = 'test1.png';
    protected $image_two = 'test2.png';
    protected $image_three = 'test3.png';
    protected $details = '{"thumbnails":[{"name":"small","scale":"25%"},{"name":"medium","scale":"50%"},{"name":"large","scale":"75%"}]}';
    protected $storage;

    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);

        $this->storage = Storage::disk(config('voyager.storage.disk'));
    }

    public function testMultipleImagesUpload()
    {
        $images = [$this->image_one, $this->image_two, $this->image_three];

        $page = $this->uploadMedia($images, 'multiple_images');

        $files = json_decode($page->image, true);

        $this->storage->assertExists($files[0]);
        $this->storage->assertExists($files[1]);
        $this->storage->assertExists($files[2]);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testMultipleImagesDelete()
    {
        $images = [$this->image_one, $this->image_two, $this->image_three];

        $page = $this->uploadMedia($images, 'multiple_images');

        $files = json_decode($page->image, true);

        $response = $this->post(route('voyager.pages.media.remove'), [
            'id'        => $page->id,
            'slug'      => 'pages',
            'field'     => 'image',
            'multi'     => 'true',
            'filename'  => $files[1],
        ]);

        $this->storage->assertExists($files[0]);
        $this->storage->assertMissing($files[1]);
        $this->storage->assertExists($files[2]);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testMultipleImagesRemoveOnDelete()
    {
        $images = [$this->image_one, $this->image_two, $this->image_three];

        $page = $this->uploadMedia($images, 'multiple_images');

        $files = json_decode($page->image, true);

        $this->delete(route('voyager.pages.destroy', [$page->id]));

        $this->storage->assertMissing($files[0]);
        $this->storage->assertMissing($files[1]);
        $this->storage->assertMissing($files[2]);
    }

    public function testImageWithThumbnailsUpload()
    {
        $page = $this->uploadMedia([$this->image_one], 'image', json_decode($this->details));

        $details = json_decode($this->details);

        foreach ($details->thumbnails as $thumbnail) {
            $path = preg_replace('/(.*)(\.[\w\d]{2,4})$/', '$1-'.$thumbnail->name.'$2', $page->image);

            $this->storage->assertExists($path);
        }

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testImageWithThumbnailsDelete()
    {
        $page = $this->uploadMedia([$this->image_one], 'image', json_decode($this->details));

        $response = $this->post(route('voyager.pages.media.remove'), [
            'id'        => $page->id,
            'slug'      => 'pages',
            'field'     => 'image',
            'multi'     => 'false',
            'filename'  => $page->image,
        ]);

        $details = json_decode($this->details);
        foreach ($details->thumbnails as $thumbnail) {
            $path = preg_replace('/(.*)(\.[\w\d]{2,4})$/', '$1-'.$thumbnail->name.'$2', $page->image);
            $this->storage->assertMissing($path);
        }

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testImageWithThumbnailsRemoveOnDelete()
    {
        $page = $this->uploadMedia([$this->image_one], 'image', json_decode($this->details));

        $details = json_decode($this->details);

        $this->delete(route('voyager.pages.destroy', [$page->id]));

        foreach ($details->thumbnails as $thumbnail) {
            $path = preg_replace('/(.*)(\.[\w\d]{2,4})$/', '$1-'.$thumbnail->name.'$2', $page->image);

            $this->storage->assertMissing($path);
        }
    }

    public function testMultipleImagesWithThumbnailsUpload()
    {
        $images = [$this->image_one, $this->image_two, $this->image_three];

        $page = $this->uploadMedia($images, 'multiple_images', json_decode($this->details));

        $files = json_decode($page->image, true);
        $details = json_decode($this->details);

        foreach ($files as $file) {
            foreach ($details->thumbnails as $thumbnail) {
                $path = preg_replace('/(.*)(\.[\w\d]{2,4})$/', '$1-'.$thumbnail->name.'$2', $file);

                $this->storage->assertExists($path);
            }
        }

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testMultipleImagesWithThumbnailsDelete()
    {
        $images = [$this->image_one, $this->image_two, $this->image_three];

        $page = $this->uploadMedia($images, 'multiple_images', json_decode($this->details));

        $files = json_decode($page->image, true);

        $response = $this->post(route('voyager.pages.media.remove'), [
            'id'        => $page->id,
            'slug'      => 'pages',
            'field'     => 'image',
            'multi'     => 'true',
            'filename'  => $files[1],
        ]);

        $details = json_decode($this->details);

        foreach ($files as $file) {
            foreach ($details->thumbnails as $thumbnail) {
                $path = preg_replace('/(.*)(\.[\w\d]{2,4})$/', '$1-'.$thumbnail->name.'$2', $file);

                if ($file == $files[1]) {
                    $this->storage->assertMissing($path);
                } else {
                    $this->storage->assertExists($path);
                }
            }
        }

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testMultipleImagesWithThumbnailsRemoveOnDelete()
    {
        $images = [$this->image_one, $this->image_two, $this->image_three];

        $page = $this->uploadMedia($images, 'multiple_images', json_decode($this->details));

        $files = json_decode($page->image, true);
        $details = json_decode($this->details);

        $this->delete(route('voyager.pages.destroy', [$page->id]));

        foreach ($files as $file) {
            foreach ($details->thumbnails as $thumbnail) {
                $path = preg_replace('/(.*)(\.[\w\d]{2,4})$/', '$1-'.$thumbnail->name.'$2', $file);

                $this->storage->assertMissing($path);
            }
        }
    }

    public function testFileUpload()
    {
        $page = $this->uploadMedia([$this->file], 'file');

        $file = json_decode($page->image, true);
        $this->storage->assertExists($file[0]['download_link']);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testFileDelete()
    {
        $page = $this->uploadMedia([$this->file], 'file');

        $file = json_decode($page->image, true);

        $this->call('POST', route('voyager.pages.media.remove'), [
            'id'        => $page->id,
            'slug'      => 'pages',
            'field'     => 'image',
            'multi'     => 'true',
            'filename'  => $file[0]['original_name'],
        ]);

        $this->storage->assertMissing($file[0]['download_link']);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testValidationForFile()
    {
        $validation = '{"validation":{"rule":"mimes:txt"}}';

        $page = $this->uploadMedia([$this->file, $this->file_three], 'file', json_decode($validation));

        $this->assertTrue($page === null);

        $page = $this->uploadMedia([$this->file, $this->file_two], 'file', json_decode($validation));

        $file = json_decode($page->image, true);

        $this->storage->assertExists($file[0]['download_link']);
        $this->storage->assertExists($file[1]['download_link']);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testFileRemoveOnDelete()
    {
        $page = $this->uploadMedia([$this->file], 'file');

        $this->delete(route('voyager.pages.destroy', [$page->id]));

        $file = json_decode($page->image, true);
        $this->storage->assertMissing($file[0]['download_link']);
    }

    public function testUploadSecondFile()
    {
        // First file
        $page = $this->uploadMedia([$this->file], 'file');

        // Second file
        $file = [];
        $file[] = UploadedFile::fake()->create($this->file_two, 1);
        $this->call('PUT', route('voyager.pages.update', $page->id), [
            'author_id' => $page->author_id,
            'title'     => $page->title,
            'slug'      => $page->slug,
            'status'    => $page->status,
        ], [], [
            'image' => $file,
        ]);

        $page = Page::where('slug', $page->slug)->firstOrFail();

        $file = json_decode($page->image, true);

        $this->storage->assertExists($file[0]['download_link']);
        $this->storage->assertExists($file[1]['download_link']);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testImageUpload()
    {
        $page = $this->uploadMedia([$this->image_one], 'image');

        $this->storage->assertExists($page->image);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testImageDelete()
    {
        $page = $this->uploadMedia([$this->image_one], 'image');

        $response = $this->post(route('voyager.pages.media.remove'), [
            'id'        => $page->id,
            'slug'      => 'pages',
            'field'     => 'image',
            'multi'     => 'false',
            'filename'  => $page->image,
        ]);

        $this->storage->assertMissing($page->image);

        $this->delete(route('voyager.pages.destroy', [$page->id]));
    }

    public function testImageRemoveOnDelete()
    {
        $page = $this->uploadMedia([$this->image_one], 'image');

        $this->delete(route('voyager.pages.destroy', [$page->id]));

        $this->storage->assertMissing($page->image);
    }

    private function uploadMedia($names, $type, $details = '')
    {
        // Change dataRow type and details
        $data_type_id = DataType::where('slug', 'pages')->first()->id;

        $data_row = DataRow::where(['data_type_id' => $data_type_id, 'field' => 'image'])->first();
        $data_row->type = $type;
        $data_row->details = $details;
        $data_row->save();

        switch ($type) {
            case 'image':
                $file = UploadedFile::fake()->image($names[0]);
                break;
            case 'file':
                $file = [];

                foreach ($names as $name) {
                    $file[] = UploadedFile::fake()->create($name, 1);
                }
                break;
            case 'multiple_images':
                $file = [];

                foreach ($names as $name) {
                    $file[] = UploadedFile::fake()->image($name);
                }
                break;
        }

        $this->call('POST', route('voyager.pages.store'), [
            'author_id' => 1,
            'title'     => 'Upload',
            'slug'      => 'upload-media',
            'status'    => 'ACTIVE',
        ], [], [
            'image' => $file,
        ]);

        $page = Page::where('slug', 'upload-media')->first();

        return $page;
    }
}

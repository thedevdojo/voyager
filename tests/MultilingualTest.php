<?php

namespace TCG\Voyager\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Translatable;
use TCG\Voyager\Translator;
use TCG\Voyager\Translator\Collection;

class MultilingualTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Add another language
        config()->set('voyager.multilingual.locales', ['en', 'da']);

        // Turn on multilingual
        config()->set('voyager.multilingual.enabled', true);
    }

    public function testCheckingModelIsTranslatable()
    {
        $this->assertTrue(Voyager::translatable(TranslatableModel::class));
        $this->assertTrue(Voyager::translatable(ActuallyTranslatableModel::class));
    }

    public function testCheckingModelIsNotTranslatable()
    {
        $this->assertFalse(Voyager::translatable(NotTranslatableModel::class));
        $this->assertFalse(Voyager::translatable(StillNotTranslatableModel::class));
    }

    public function testGettingModelTranslatableAttributes()
    {
        $this->assertEquals(['title'], (new TranslatableModel())->getTranslatableAttributes());
        $this->assertEquals([], (new ActuallyTranslatableModel())->getTranslatableAttributes());
    }

    public function testGettingTranslatorCollection()
    {
        $collection = TranslatableModel::all()->translate('da');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(Translator::class, $collection[0]);
    }

    public function testGettingTranslatorModelOfNonExistingTranslation()
    {
        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Lorem Ipsum Post', $model->title);
        $this->assertFalse($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));
    }

    public function testGettingTranslatorModelOfExistingTranslation()
    {
        DB::table('translations')->insert([
            'table_name'  => 'posts',
            'column_name' => 'title',
            'foreign_key' => 1,
            'locale'      => 'da',
            'value'       => 'Foo Bar Post',
        ]);

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Foo Bar Post', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));
    }

    public function testSavingNonExistingTranslatorModel()
    {
        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Lorem Ipsum Post', $model->title);
        $this->assertFalse($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));

        $model->title = 'Danish Title';

        $this->assertEquals('Danish Title', $model->title);
        $this->assertFalse($model->getRawAttributes()['title']['exists']);
        $this->assertTrue($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));

        $model->save();

        $this->assertEquals('Danish Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Danish Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));
    }

    public function testSavingExistingTranslatorModel()
    {
        DB::table('translations')->insert([
            'table_name'  => 'posts',
            'column_name' => 'title',
            'foreign_key' => 1,
            'locale'      => 'da',
            'value'       => 'Foo Bar Post',
        ]);

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Foo Bar Post', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));

        $model->title = 'Danish Title';

        $this->assertEquals('Danish Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertTrue($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));

        $model->save();

        $this->assertEquals('Danish Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Danish Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
        $this->assertEquals('Lorem Ipsum Post', $model->getOriginalAttribute('title'));
    }

    public function testGettingTranslationMetaDataFromTranslator()
    {
        $model = TranslatableModel::first()->translate('da');

        $this->assertFalse($model->translationAttributeExists('title'));
        $this->assertFalse($model->translationAttributeModified('title'));
    }

    public function testCreatingNewTranslation()
    {
        $model = TranslatableModel::first()->translate('da');

        $model->createTranslation('title', 'Danish Title Here');

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Danish Title Here', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
    }

    public function testUpdatingTranslation()
    {
        DB::table('translations')->insert([
            'table_name'  => 'posts',
            'column_name' => 'title',
            'foreign_key' => 1,
            'locale'      => 'da',
            'value'       => 'Title',
        ]);

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);

        $model->title = 'New Title';

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('New Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertTrue($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);

        $model->save();

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('New Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);

        $model = TranslatableModel::first()->translate('da');

        $this->assertInstanceOf(Translator::class, $model);
        $this->assertEquals('da', $model->getLocale());
        $this->assertEquals('New Title', $model->title);
        $this->assertTrue($model->getRawAttributes()['title']['exists']);
        $this->assertFalse($model->getRawAttributes()['title']['modified']);
        $this->assertEquals('da', $model->getRawAttributes()['title']['locale']);
    }
}

class TranslatableModel extends Model
{
    protected $table = 'posts';

    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body',
    ];

    protected $translatable = ['title'];
}

class NotTranslatableModel extends Model
{
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body',
    ];
}

class StillNotTranslatableModel extends Model
{
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body',
    ];

    protected $translatable = ['title'];
}

class ActuallyTranslatableModel extends Model
{
    protected $table = 'posts';

    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'body',
    ];
}

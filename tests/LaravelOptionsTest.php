<?php

namespace Jobcerto\Options\Tests;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Jobcerto\Options\Tests\TestCase;

class LaravelOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->tags = ['tag01', 'tag02', 'tag03', 'tag04'];

        options()->set('tags', $this->tags);

    }

    /** @test */
    public function it_can_get_all_options()
    {
        $this->assertCount(1, options()->all());
    }

    /** @test */
    public function it_can_set_optons()
    {

        $this->assertDatabaseHas('options', ['key' => 'tags', 'value' => json_encode($this->tags)]);
    }

    /** @test */
    public function it_can_set_optons_via_helper()
    {
        $value = ['foo', 'bar', 'baz'];

        options('some-key', $value);

        $this->assertDatabaseHas('options', ['key' => 'some-key', 'value' => json_encode($value)]);
    }

    /** @test */
    public function it_can_find_a_single_option()
    {

        $this->assertEquals($this->tags, options()->get('tags'));

        $this->assertEquals($this->tags, options('tags'));
    }

    /** @test */
    public function it_can_update_all_attributes()
    {

        $newTags = ['new foo', 'new bar', 'new baz'];

        $this->assertSame($newTags, options()->set('tags', $newTags)->value);

        $newTagsViaFunction = ['new foo', 'new bar', 'new baz'];

        $this->assertSame($newTagsViaFunction, options('new-tags', $newTagsViaFunction)->value);
    }

    /** @test */
    public function it_throw_exception_when_option_doesnt_exists()
    {
        $this->expectException(ModelNotFoundException::class);

        options()->get('unknow-meta');

    }

    /** @test */
    public function it_can_find_a_single_option_and_casts_to_object()
    {

        $options = ['foo' => 'value-foo', 'bar' => 'value-bar', 'baz' => 'value-baz'];

        options()->set('object', $options);

        $this->assertInstanceOf(\StdClass::class, options()->get('object', 'object'));

    }

    /** @test */
    public function it_can_find_a_single_option_and_casts_to_collection()
    {

        $options = ['foo' => 'value-foo', 'bar' => 'value-bar', 'baz' => 'value-baz'];

        options()->set('object', $options);

        $this->assertInstanceOf(Collection::class, options()->get('object', 'collection'));

    }

    /** @test */
    public function it_can_find_a_single_option_and_casts_to_boolean()
    {

        options()->set('isSubscribed', true);

        $this->assertIsBool(options()->get('isSubscribed', 'boolean'));

    }

    /** @test */
    public function it_can_find_a_value_using_search()
    {

        $this->assertEquals(options()->get('tags'), options()->search('tags'));
    }

    /** @test */
    public function it_can_check_if_has_one_option()
    {

        $this->assertTrue(options()->has('tags'));

        $this->assertFalse(options()->has('fake-tag'));

    }

    /** @test */
    public function it_can_delete_a_single_option()
    {

        options()->delete('tags');

        $this->assertCount(0, options()->all());

    }

    /** @test */
    public function it_convert_all_option_to_array()
    {

        options()->set('other-tags', ['01', '02', '03']);

        $this->assertCount(2, options()->toArray());
    }

    /** @test */
    public function it_can_find_value_in_option_using_dot_notation()
    {

        $attributes = [
            'br' => 'brasil',
            'eu' => 'Estados Únidos',
        ];

        options()->set('countries', $attributes);

        $this->assertEquals('brasil', options()->search('countries.br'));

        $this->assertEquals('Estados Únidos', options()->search('countries.eu'));

    }

    /** @test */
    public function it_return_the_default_value_or_null_when_search_inside_option()
    {

        $attributes = [
            'br' => 'brasil',
            'eu' => 'Estados Únidos',
        ];

        options()->set('countries', $attributes);

        $this->assertNull(options()->search('countries.something-that-is-fake'));

        $this->assertEquals('my-custom-value', options()->search('countries.something-that-is-fake', 'my-custom-value'));
    }

    /** @test */
    public function it_can_replace_any_value_inside_a_option()
    {
        $countries = [
            'br' => 'brazil',
            'eu' => 'estados unidos',
        ];

        options()->set('countries', $countries);

        options()->replace('countries.br', 'novo valor');

        $this->assertEquals('novo valor', options()->search('countries.br'));
    }

    /** @test */
    public function it_can_replace_an_unknown_value_inside_a_option()
    {

        $countries = [
            'br' => 'brazil',
            'eu' => 'estados unidos',
        ];

        options()->set('countries', $countries);

        options()->replace('countries.ru', 'Russia');

        $this->assertEquals('Russia', options()->search('countries.ru'));

        $countriesWithNewAddedValue = [
            'br' => 'brazil',
            'eu' => 'estados unidos',
            'ru' => 'Russia',
        ];

        $this->assertSame($countriesWithNewAddedValue, options()->get('countries'));
    }

    /** @test */
    public function it_throw_exception_when_tries_to_replace_whitout_one_dot()
    {
        $this->expectException(\Exception::class);

        $countries = [
            'br' => 'brazil',
            'eu' => 'estados unidos',
        ];

        options()->set('countries', $countries);

        options()->replace('countries', 'novo valor');
    }
}

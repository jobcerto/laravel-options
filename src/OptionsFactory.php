<?php

namespace Jobcerto\Options;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class OptionsFactory implements Arrayable
{
    public $model;

    public function __construct($model)
    {
        $this->model = resolve($model);
    }

    public function all()
    {
        return $this->model->all()->pluck('value', 'key');
    }

    public function set(string $key, $value)
    {
        return $this->model->updateOrCreate(
            ['key' => $key],
            ['key' => $key, 'value' => $value]
        );
    }

    public function get(string $key, $castable = null)
    {

        $value = $this->raw($key)->value;

        switch ($castable) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new Collection($this->fromJson($value));
            default:
                return $value;
        }
    }

    public function delete(string $key)
    {
        return !  ! $this->raw($key)->delete();
    }

    public function has(string $key)
    {
        return $this->model->where('key', $key)->exists();
    }

    public function search(string $dotNotation, $default = null)
    {

        if ( ! str_contains($dotNotation, '.')) {
            return $this->get($dotNotation);
        }

        $key = $this->getFirstKey($dotNotation);

        $option = $this->get($key);

        return data_get($option, str_after($dotNotation, $key . '.'), $default);
    }

    public function replace(string $dotNotation, $value)
    {

        throw_unless(str_contains($dotNotation, '.'), new \Exception('you have to give a value that can be replaceable'));

        $option = $this->raw($this->getFirstKey($dotNotation));

        tap($option)->forceFill([
            $this->qualifiedValueName($dotNotation) => $value,
        ])->save();

        return $option;
    }

    public function toArray()
    {
        return $this->all();
    }

    private function qualifiedValueName($keys)
    {
        return 'value->' . $this->getUpdatableAttributes($keys);
    }

    private function getUpdatableAttributes($keys)
    {
        return str_after($this->replaceDotsWithArrows($keys), '->');
    }

    /**
     * Get the first key of search
     *
     * @param  string $keys dot notation
     * @return string
     */
    private function getFirstKey($keys)
    {
        return str_before($this->replaceDotsWithArrows($keys), '->');
    }

    private function replaceDotsWithArrows($keys)
    {
        return preg_replace('/\./', '->', $keys);
    }

    private function raw(string $key)
    {
        return $this->model->where('key', $key)->firstOrFail();
    }

    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    private function fromJson($value, $asObject = false)
    {
        return json_decode(json_encode($value), ! $asObject);
    }
}

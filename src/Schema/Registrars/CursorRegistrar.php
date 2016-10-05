<?php

namespace Nuwave\Lighthouse\Schema\Registrars;

use Closure;

class CursorRegistrar
{
    /**
     * Collection of registered definitions.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * Create new instance of registrar.
     */
    public function __construct()
    {
        $this->collection = collect();
    }

    /**
     * Register new cursor.
     *
     * @param  string  $name
     * @param  callable $encode
     * @param  callable|null $decode
     * @return bool
     */
    public function register($name, callable $encode, callable $decode = null)
    {
        $this->collection->put($name, [
            'encode' => $encode,
            'decode' => $decode,
        ]);

        return true;
    }

    /**
     * Get encoder for type.
     *
     * @param  string $name
     * @return Closure|null
     */
    public function encoder($name)
    {
        $cursor = $this->collection->get($name, ['encode' => null]);

        return $cursor['encode'];
    }

    /**
     * Get decoder for type.
     *
     * @param  string $name
     * @return Closure|null
     */
    public function decoder($name)
    {
        $cursor = $this->collection->get($name, ['decode' => null]);

        return $cursor['decode'];
    }
}

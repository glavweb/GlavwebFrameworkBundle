<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;
use ArrayIterator;

/**
 * Ассоциативный контейнер без ext-ds (замена Ds\Map).
 *
 * get()/remove() принимают default; keys()/values()/toArray() возвращают array.
 *
 * @author Sergey Zvyagintsev <nitron.ru@gmail.com>
 */
class Map implements ArrayAccess, Countable, IteratorAggregate
{
    private array $data = [];

    public function __construct(iterable $values = [])
    {
        foreach ($values as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function put(mixed $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function putIfAbsent(mixed $key, mixed $value): void
    {
        if (!$this->hasKey($key)) {
            $this->put($key, $value);
        }
    }

    public function getOrPutDefault(mixed $key, mixed $value): mixed
    {
        $this->putIfAbsent($key, $value);

        return $this->get($key);
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    public function hasKey(mixed $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function remove(mixed $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $this->data)) {
            return $default;
        }

        $value = $this->data[$key];
        unset($this->data[$key]);

        return $value;
    }

    /**
     * @return list<mixed>
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * @return list<mixed>
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    /**
     * @return array<mixed, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasKey($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->put($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }
}

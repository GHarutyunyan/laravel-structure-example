<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Traits\ProxiesHandle;
use App\Repositories\Traits\ProxiesHandleArrayable;
use App\Repositories\Traits\ProxiesHandleArrayAccess;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class AbstractRepository implements ArrayAccess, Arrayable, RepositoryInterface
{
    use ProxiesHandle, ProxiesHandleArrayable, ProxiesHandleArrayAccess;

    /**
     * The handle property represents the proxied eloquent methods.
     *
     * @var mixed
     */
    private $handle;

    /**
     * @param  mixed  $handle
     * @throws \InvalidArgumentException
     */
    public function __construct($handle = null)
    {
        if (!is_null($handle) && !$this->isValidHandle($handle)) {
            throw new InvalidArgumentException(
                'Cannot use given handle of type '
                . (is_object($handle) ? get_class($handle) : gettype($handle))
                . ' to construct the repository'
            );
        }

        $this->handle = $handle ?? app()->make($this->getModelName());
    }

    /**
     * Create new repository instance with given handler.
     *
     * @param  mixed  $handle
     *
     * @return void
     */
    public static function of($handle)
    {
        if (is_a($handle, static::class)) {
            return $handle;
        }

        return new static($handle);
    }

    /**
     * Get current object handle.
     *
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get repository model classname.
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return Str::of(static::class)
            ->afterLast('\\')
            ->beforeLast('Repository')
            ->prepend('\\App\\Models\\')
            ->__toString();
    }

    private function isValidHandle($handle)
    {
        if (!is_object($handle)) {
            return false;
        }

        if (is_a($handle, $this->getModelName())) {
            return true;
        }

        if ($handle instanceof Relation) {
            return is_a($handle->getRelated(), $this->getModelName());
        }

        if ($handle instanceof Builder) {
            return is_a($handle->getModel(), $this->getModelName());
        }

        return $handle instanceof Collection || $handle instanceof AbstractPaginator;
    }
}

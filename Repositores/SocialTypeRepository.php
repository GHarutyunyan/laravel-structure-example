<?php

namespace App\Repositories;

class SocialTypeRepository extends AbstractRepository
{
    /**
     * Get social type by name
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug)
    {
        return $this->where(['slug' => $slug])->first();
    }
}

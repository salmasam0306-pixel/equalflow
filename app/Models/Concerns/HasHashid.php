<?php
// app/Models/Concerns/HasHashid.php

namespace App\Models\Concerns;

use Vinkla\Hashids\Facades\Hashids;

/**
 * Adds hashid-based route keys to a model.
 *
 * - getRouteKey()             → encodes the model's primary key when generating URLs
 * - resolveRouteBinding()     → decodes the hashid back to a model instance
 *                               (with integer fallback for legacy/stale URLs)
 * - resolveChildRouteBinding() → same, but for nested resources
 */
trait HasHashid
{
    /**
     * Encode the model's ID when Laravel generates URLs.
     */
    public function getRouteKey(): string
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Decode a hashid back into a model instance (route-model binding).
     *
     * Tolerant: if the value isn't a valid hashid but is numeric, treat it
     * as a raw primary key. This lets stale cached URLs (browser back button,
     * bookmarks from before the hashid migration) keep working.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = Hashids::decode($value);
        $id = !empty($decoded) ? $decoded[0] : $value;

        if (!is_numeric($id)) {
            abort(404);
        }

        return $this->where($this->getKeyName(), $id)->firstOrFail();
    }

    /**
     * Same, but for nested route bindings
     * (e.g. /projects/{project}/documents/{document}).
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        $decoded = Hashids::decode($value);
        $id = !empty($decoded) ? $decoded[0] : $value;

        if (!is_numeric($id)) {
            abort(404);
        }

        return parent::resolveChildRouteBinding($childType, $id, $field);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasHashid;

class Organization extends Model
{

    use HasHashid;

    protected $fillable = ['name', 'description', 'industry', 'invite_code', 'created_by', 'logo'];

    public function members()
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_members');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the full URL for the company logo
     */
    public function getLogoUrl()
    {
        if ($this->logo) {
            return asset('storage/company_logos/' . $this->logo);
        }
        
        return null;
    }

    /**
     * Get the logo with fallback (initials as avatar)
     */
    public function getLogoOrFallback()
    {
        if ($this->logo) {
            return asset('storage/company_logos/' . $this->logo);
        }
        
        // Return initials as fallback
        $initials = strtoupper(substr($this->name, 0, 2));
        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=1a2a4a&color=ffffff&size=100&font-size=0.5';
    }

    /**
     * Check if organization has a logo
     */
    public function hasLogo()
    {
        return !is_null($this->logo);
    }
}
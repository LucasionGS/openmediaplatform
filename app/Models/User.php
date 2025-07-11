<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'channel_name',
        'channel_description',
        'profile_picture',
        'channel_banner',
        'channel_links',
        'subscribers_count',
        'channel_created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'channel_created_at' => 'datetime',
            'channel_links' => 'array',
        ];
    }

    // Channel/User relationships
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function playlists(): HasMany
    {
        return $this->hasMany(Playlist::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscription::class, 'channel_id');
    }

    public function subscribedChannels(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscriber_id', 'channel_id');
    }

    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function videoEngagements(): HasMany
    {
        return $this->hasMany(VideoEngagement::class);
    }

    // Helper methods
    public function isSubscribedTo($channelId): bool
    {
        return $this->subscriptions()->where('channel_id', $channelId)->exists();
    }

    public function getChannelName(): string
    {
        return $this->channel_name ?? $this->name;
    }

    public function getAvatarUrl(): string
    {
        return $this->profile_picture ? asset('sf/' . $this->profile_picture) : asset('images/default-avatar.png');
    }

    public function getBannerUrl(): ?string
    {
        return $this->channel_banner ? asset('sf/' . $this->channel_banner) : null;
    }

    public function updateSubscribersCount(): void
    {
        $this->subscribers_count = $this->subscribers()->count();
        $this->save();
    }
}

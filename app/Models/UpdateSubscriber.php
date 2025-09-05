<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UpdateSubscriber extends Model
{
    protected $fillable = [
        'email',
        'unsubscribe_token',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Generate a unique unsubscribe token for this subscriber
     */
    public static function generateUnsubscribeToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('unsubscribe_token', $token)->exists());

        return $token;
    }

    /**
     * Subscribe an email address to the updates list
     */
    public static function subscribe(string $email): self
    {
        $subscriber = static::where('email', $email)->first();

        if ($subscriber) {
            // Reactivate if was unsubscribed
            if (!$subscriber->is_active) {
                $subscriber->update([
                    'is_active' => true,
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]);
            }
            return $subscriber;
        }

        // Create new subscriber
        return static::create([
            'email' => $email,
            'unsubscribe_token' => static::generateUnsubscribeToken(),
            'is_active' => true,
            'subscribed_at' => now(),
        ]);
    }

    /**
     * Unsubscribe using token
     */
    public function unsubscribe(): bool
    {
        return $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Scope for active subscribers only
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the unsubscribe URL for this subscriber
     */
    public function getUnsubscribeUrlAttribute(): string
    {
        return route('newsletter.unsubscribe', $this->unsubscribe_token);
    }
}

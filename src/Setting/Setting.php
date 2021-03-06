<?php

declare(strict_types=1);

namespace Orchid\Setting;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting.
 */
class Setting extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Cache result.
     *
     * @var bool
     */
    public $cache = true;

    /**
     * @var string
     */
    protected $table = 'settings';

    /**
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    /**
     * @param string $key
     * @param string|array $value
     *
     * Fast record
     *
     * @return bool
     */
    public function set(string $key, $value)
    {
        $result = $this->firstOrNew([
            'key' => $key,
        ])->fill([
            'value' => $value,
        ])->save();

        $this->cacheForget($key);

        return $result;
    }

    /**
     * @param $key
     *
     * @return null
     */
    private function cacheForget($key)
    {
        foreach (array_wrap($key) as $value) {
            Cache::forget($value);
        }
    }

    /**
     * @param string|array $key
     * @param string|null $default
     *                              Get values
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (! $this->cache) {
            return $this->getNoCache($key, $default);
        }

        return Cache::rememberForever('settings-'.implode(',', (array) $key), function () use ($key, $default) {
            return $this->getNoCache($key, $default);
        });
    }

    /**
     * @param             $key
     * @param string|null $default
     *
     * @return null
     */
    public function getNoCache($key, $default = null)
    {
        if (is_array($key)) {
            $result = $this->select('key', 'value')->whereIn('key', $key)->pluck('value', 'key')->toArray();

            return empty($result) ? $default : $result;
        }

        $result = $this->select('value')->where('key', $key)->first();

        return is_null($result) ? $default : $result->value;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function forget($key)
    {
        $key = array_wrap($key);
        $result = $this->whereIn('key', $key)->delete();
        $this->cacheForget($key);

        return $result;
    }
}

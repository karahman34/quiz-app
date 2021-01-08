<?php

namespace App\Helpers;

use App\Models\Session;
use Carbon\Carbon;

class TestSession
{
    private static $working = 'working';
    private static $code = 'code';
    private static $end_at = 'end_at';

    /**
     * Get status working
     *
     * @return  boolean
     */
    public static function getWorking()
    {
        return session()->get(self::$working, false);
    }

    /**
     * Get session code.
     *
     * @return  string
     */
    public static function getCode()
    {
        return session()->get(self::$code, null);
    }

    /**
     * Get session end time date.
     *
     * @return  Carbon
     */
    public static function getEndAt()
    {
        return session()->get(self::$end_at, null);
    }

    /**
     * Set session status.
     *
     * @param   bool  $value
     *
     * @return  void
     */
    public static function setWorking(bool $value)
    {
        session()->put(self::$working, $value);
    }

    /**
     * Set test code
     *
     * @param   string|null  $code
     *
     * @return  void
     */
    public static function setCode($code)
    {
        session()->put(self::$code, $code);
    }

    /**
     * Set session end time date.
     *
     * @param   Session|null  $session
     *
     * @return  void
     */
    public static function setEndAt($session)
    {
        if (!is_null($session)) {
            $available_for = explode(':', $session->available_for);
            $endAt = Carbon::parse($session->created_at);
            $endAt->addHours($available_for[0]);
            $endAt->addMinutes($available_for[1]);

            session()->put(self::$end_at, $endAt);
        } else {
            session()->put(self::$end_at, null);
        }
    }
}

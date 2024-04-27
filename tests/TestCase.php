<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from absens");
        DB::delete("delete from siswas");
        DB::delete("delete from mapels");
        DB::delete("delete from kelas");
        DB::delete("delete from gurus");
        DB::delete("delete from admins");
    }
}

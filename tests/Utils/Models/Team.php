<?php

namespace Tests\Utils\Models;

use Illuminate\Database\Eloquent\Model;
use Nuwave\Lighthouse\Support\Traits\IsRelayConnection;

class Team extends Model
{
    use IsRelayConnection;
}

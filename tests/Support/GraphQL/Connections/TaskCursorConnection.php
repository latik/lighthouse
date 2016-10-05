<?php

namespace Nuwave\Lighthouse\Tests\Support\GraphQL\Connections;

class TaskCursorConnection extends TaskConnection
{
    /**
     * Encodes the cursor for the connection.
     *
     * @param  mixed $item
     * @param  int $index
     * @param  int $page
     * @return mixed
     */
    public function encodeCursor($item, $index, $page)
    {
        return $index === 0 ? 'foo' : 'bar';
    }
}

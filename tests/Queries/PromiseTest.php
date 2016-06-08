<?php

namespace Nuwave\Tests\Queries;

use React\Promise\Promise;
use Nuwave\Lighthouse\Tests\DBTestCase;
use Nuwave\Lighthouse\Tests\Support\Models\User;
use Nuwave\Lighthouse\Tests\Support\Models\Task;

class PromiseTest extends DBTestCase
{
    /**
     * @test
     * @group failing
     */
    public function itCanUsePromisesToResolveConnection()
    {
        factory(User::class, 100)->create()
            ->each(function ($user) {
                factory(Task::class, 100)->create([
                    'user_id' => $user->id
                ]);
            });

        $startTime = microtime(true);

        $promises = User::all()->map(function ($user) {
            // dd($user->tasks()->paginate(5)->count());
            // $user->tasks = $user->tasks()->paginate(5);
            $promise = new Promise(function ($resolve) use ($user) {
                $resolve($user->tasks()->paginate(5));
            });

            $promise->then(function ($result) use (&$user) {
                dump("Processing tasks for user: " . $user->id);
                sleep(2);
                $user->tasks = $result;
            });

            return $promise;
        });

        $promise = \React\Promise\all($promises);
        $promise->then(function () use ($startTime) {
            $time = microtime(true) - $startTime;

            dump("Completed task in $time seconds");
        });
        // dd('done');
    }
}

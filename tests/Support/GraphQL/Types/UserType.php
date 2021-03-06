<?php

namespace Nuwave\Lighthouse\Tests\Support\GraphQL\Types;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Support\Definition\GraphQLType;
use Nuwave\Lighthouse\Support\Interfaces\RelayType;
use Nuwave\Lighthouse\Tests\Support\Models\User;

class UserType extends GraphQLType implements RelayType
{
    /**
     * Attributes of type.
     *
     * @var array
     */
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user.',
    ];

    /**
     * Get model by id.
     *
     * Note: When the root 'node' query is called, this method
     * will be used to resolve the type by providing the id.
     *
     * @param  mixed $id
     * @return mixed
     */
    public function resolveById($id)
    {
        return factory(User::class)->make([
            'id' => $id,
            'email' => 'foo@bar.com',
        ]);
    }

    /**
     * Type fields.
     *
     * @return array
     */
    public function fields()
    {
        return [
            'name' => [
                'type' => Type::string(),
                'description' => 'Name of the user.',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Email of the user.',
            ],
            'tasks' => GraphQL::connection('task')
                ->args([
                    'order' => [
                        'type' => Type::string(),
                        'description' => 'Sort order of tasks.',
                    ],
                ])
                ->resolve(function ($parent, array $args) {
                    return $parent->tasks->transform(function ($task) {
                        return array_merge($task->toArray(), ['title' => 'foo']);
                    });
                })
                ->field(),
        ];
    }

    /**
     * Resolve user email.
     *
     * @param  mixed $root
     * @param  array  $args
     * @return string
     */
    protected function resolveEmailField($root, array $args)
    {
        return 'foo@bar.com';
    }
}

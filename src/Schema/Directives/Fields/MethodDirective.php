<?php

namespace Nuwave\Lighthouse\Schema\Directives\Fields;

use GraphQL\Language\AST\FieldDefinitionNode;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;
use Nuwave\Lighthouse\Support\Traits\HandlesDirectives;

class MethodDirective implements FieldResolver
{
    use HandlesDirectives;

    /**
     * Resolve the field directive.
     *
     * @param FieldDefinitionNode $field
     *
     * @return \Closure
     */
    public function handle(FieldDefinitionNode $field)
    {
        // TODO: Look into creating a static method on Resolvers and Middleware
        // to return a name so we can use a $this->getDirective() or
        // $this->getArgument() methods w/o having to search for the directive by name.
        $method = $this->directiveArgValue(
            $this->fieldDirective($field, 'method'),
            'name'
        );

        return function ($root, array $args, $context = null, ResolveInfo $info = null) use ($method) {
            return call_user_func_array([$root, $method], [$args, $context, $info]);
        };
    }
}

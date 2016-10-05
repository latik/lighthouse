<?php

namespace Nuwave\Lighthouse\Schema\Registrars;

use GraphQL\Type\Definition\ObjectType;
use Nuwave\Lighthouse\Support\Definition\RelayConnectionType;
use Nuwave\Lighthouse\Support\Definition\Fields\ConnectionField;
use Nuwave\Lighthouse\Support\Interfaces\Connection;

class ConnectionRegistrar extends BaseRegistrar
{
    /**
     * Collection of registered type instances.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $instances;

    /**
     * Create new instance of connection registrar.
     */
    public function __construct()
    {
        parent::__construct();

        $this->instances = collect();
    }

    /**
     * Add type to registrar.
     *
     * @param  string $name
     * @param  array  $field
     * @return array
     */
    public function register($name, $field)
    {
        $this->collection->put($name, $field);

        return $field;
    }

    /**
     * Get instance of connection type.
     *
     * @param  string $name
     * @param  string|null $parent
     * @param  bool $fresh
     * @return \Nuwave\Lighthouse\Support\Definition\Fields\ConnectionField
     */
    public function instance($name, $parent = null, $fresh = false)
    {
        if (! $fresh && $this->instances->has($name)) {
            return $this->instances->get($name);
        }

        $connection = app($name);
        $nodeType = $this->getSchema()->typeInstance($connection->type());
        $instance = $this->getInstance($connection, $nodeType)->field();

        $this->instances->put($name, $instance);
        return $instance;
    }

    /**
     * Generate connection field.
     *
     * @param  string $name
     * @param  ObjectType $nodeType
     * @return array
     */
    public function getInstance($name, ObjectType $nodeType)
    {
        $isConnection = $name instanceof Connection;
        $connection = new RelayConnectionType();
        $instanceName = $this->instanceName($name);
        $connectionName = (!preg_match('/Connection$/', $instanceName)) ? $instanceName.'Connection' : $instanceName;
        $connection->setName(studly_case($connectionName));

        $pageInfoType = $this->getSchema()->typeInstance('pageInfo');
        $edgeType = $this->getSchema()->edgeInstance($instanceName, $nodeType);

        $connection->setEdgeType($edgeType);
        $connection->setPageInfoType($pageInfoType);
        $instance = $connection->toType();

        if ($isConnection && method_exists($name, 'encodeCursor')) {
            $encoder = [$name, 'encodeCursor'];

            app('graphql')->schema()->cursor($instance->name, [$name, 'encodeCursor']);
        }

        return new ConnectionField([
            'args'    => $isConnection ? array_merge($name->args(), RelayConnectionType::connectionArgs()) : RelayConnectionType::connectionArgs(),
            'type'    => $instance,
            'resolve' => $isConnection ? [$name, 'resolve'] : null,
        ]);
    }

    /**
     * Extract name.
     *
     * @param  mixed $name
     * @return string
     */
    protected function instanceName($name)
    {
        if ($name instanceof Connection) {
            return strtolower(snake_case((str_replace('\\', '_', $name->name()))));
        }

        return $name;
    }

    /**
     * Extract name.
     *
     * @param  mixed $name
     * @return string
     */
    protected function typeName($name)
    {
        if ($name instanceof Connection) {
            return $name->type();
        }

        return $name;
    }
}

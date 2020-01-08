<?php

declare(strict_types=1);

namespace App\Peak;

use Peak\Http\Request\HandlerResolver as BaseHandlerResolver;

class HandlerResolver extends BaseHandlerResolver
{
    /**
     * @param string $handler
     * @return mixed
     */
    protected function resolveString(string $handler)
    {
        // resolve using a container
        if (null !== $this->container) {
            return $this->container->get($handler);
        }

        // manual instantiation, work only with empty constructor classes
        return new $handler();
    }
}

<?php

namespace Visol\Cacheable\Aspect;

/*
 * This file is part of the Visol.Cacheable package.
 *
 * (c) visol digitale Dienstleistungen GmbH, www.visol.ch
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Cache\Frontend\VariableFrontend;
use Visol\Cacheable\Annotations\Method;

/**
 * @Flow\Aspect
 */
class MethodAspect
{

    /**
     * @var \Neos\Flow\Reflection\ReflectionService
     * @Flow\Inject
     */
    protected $reflectionService;

    /**
     * @var \Neos\Flow\Security\Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @var \Neos\Flow\Cache\CacheManager
     * @Flow\Inject
     */
    protected $cacheManager;

    /**
     *
     * @param \Neos\Flow\AOP\JoinPointInterface $joinPoint
     * @return mixed
     * @Flow\Around ("methodAnnotatedWith(Visol\Cacheable\Annotations\Method)")
     */
    public function checkAndLoadFromCacheOrSaveToCache(\Neos\Flow\AOP\JoinPointInterface $joinPoint)
    {
        $methodAnnotation = $this->reflectionService->getMethodAnnotation(
            $joinPoint->getClassName(),
            $joinPoint->getMethodName(),
            \Visol\Cacheable\Annotations\Method::class
        );

        if (!$methodAnnotation instanceof \Visol\Cacheable\Annotations\Method) {
            return $joinPoint->getAdviceChain()->proceed($joinPoint);
        }

        /** @var VariableFrontend $cache */
        $cache = $this->cacheManager->getCache($methodAnnotation->cacheIdentifier);

        $entryIdentifierProperties = [
            'namespace' => $methodAnnotation->namespace ?? ($joinPoint->getClassName(
                    ) . '->' . $joinPoint->getMethodName()),
            'arguments' => $joinPoint->getMethodArguments(),
        ];

        switch ($methodAnnotation->security) {
            case Method::SECURITY_ROLES:
                $entryIdentifierProperties['roles'] = array_keys($this->securityContext->getRoles());
                break;
            case Method::SECURITY_ACCOUNT:
                $entryIdentifierProperties['account'] = $this->securityContext->getAccount()->getAccountIdentifier();
                break;
        }

        $entryIdentifier = sha1(json_encode($entryIdentifierProperties));

        if ($cache->has($entryIdentifier)) {
            return $cache->get($entryIdentifier);
        }

        $result = $joinPoint->getAdviceChain()->proceed($joinPoint);

        $cache->set($entryIdentifier, $result, $methodAnnotation->tags, $methodAnnotation->lifetime);

        return $result;
    }
}

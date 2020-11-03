<?php

namespace Visol\Cacheable\Annotations;

/*
 * This file is part of the Visol.Cacheable package.
 *
 * (c) visol digitale Dienstleistungen GmbH, www.visol.ch
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class Method
{
    const SECURITY_NONE = 0;
    const SECURITY_ROLES = 1;
    const SECURITY_ACCOUNT = 2;

    const CACHE_TRANSIENT = 'CacheableMethodTransientCache';
    const CACHE_PERSISTENT = 'CacheableMethodPersistentCache';

    /**
     * @var string
     */
    public $cacheIdentifier = Method::CACHE_PERSISTENT;

    /**
     * @var string
     */
    public $namespace = null;

    /**
     * @var integer
     */
    public $lifetime = null;

    /**
     * @var array
     */
    public $tags = [];

    /**
     * @var integer
     */
    public $security = Method::SECURITY_NONE;

}

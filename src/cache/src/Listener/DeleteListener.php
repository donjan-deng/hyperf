<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Cache\Listener;

use Hyperf\Cache\AnnotationManager;
use Hyperf\Cache\CacheManager;
use Hyperf\Cache\Driver\DriverInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener
 */
class DeleteListener implements ListenerInterface
{
    /**
     * @var CacheManager
     */
    protected $manager;

    protected $annotationManager;

    public function __construct(CacheManager $manager, AnnotationManager $annotationManager)
    {
        $this->manager = $manager;
        $this->annotationManager = $annotationManager;
    }

    public function listen(): array
    {
        return [
            DeleteEvent::class,
        ];
    }

    /**
     * @param DeleteEvent $event
     */
    public function process(object $event)
    {
        $className = $event->getClassName();
        $method = $event->getMethod();
        $arguments = $event->getArguments();

        [$key, , $group] = $this->annotationManager->getCacheableValue($className, $method, $arguments);

        /** @var DriverInterface $driver */
        $driver = $this->manager->getDriver($group);
        $driver->delete($key);
    }
}

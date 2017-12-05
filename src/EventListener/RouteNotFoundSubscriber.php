<?php

namespace Jidoka1902\RedirectingFallbacksSymfony\EventSubscriber;


use Jidoka1902\RedirectingFallbacks\Resolver\RedirectResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This just redirects to the applications root, if there is some other stuff
 * to do - do in in another listener
 * if there are some other more specific redirects to do - put it in another listener
 * AND place it's configured order before this listener.
 *
 * Class RouteNotFoundListener
 * @package App\EventListener
 */
class RouteNotFoundSubscriber implements EventSubscriberInterface
{

    public const REDIRECT_PRIORITY = 100;

    /** @var RedirectResolver */
    private $resolver;

    /**
     * route generation is made within constructor to prevent runtime exceptions if route couldn't be found
     *
     * @param RedirectResolver $resolver
     */
    public function __construct(RedirectResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', self::REDIRECT_PRIORITY],
            ],
            KernelEvents::RESPONSE => 'onResponseCreated'
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        /** @var \Exception $exception */
        $exception = $event->getException();

        if (!$exception instanceof HttpExceptionInterface) {
            return;
        }

        if ($exception->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            return;
        }

        /** @var string $requestPath */
        $requestPath = $event->getRequest()->getPathInfo();

        if (!$this->resolver->canResolve($requestPath)) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->resolver->resolve($requestPath), Response::HTTP_MOVED_PERMANENTLY));
    }

    public function onResponseCreated(FilterResponseEvent $event)
    {
        /** @var int $statusCode */
        $statusCode = $event->getResponse()->getStatusCode();

        if ($statusCode !== Response::HTTP_NOT_FOUND) {
            return;
        }

        /** @var string $requestPath */
        $requestPath = $event->getRequest()->getPathInfo();

        if (!$this->resolver->canResolve($requestPath)) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->resolver->resolve($requestPath), Response::HTTP_MOVED_PERMANENTLY));
    }
}
<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // Gérer les requêtes OPTIONS (preflight CORS)
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $this->addCorsHeaders($response, $request);
            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $this->addCorsHeaders($response, $request);
    }

    private function addCorsHeaders(Response $response, $request): void
    {
        $origin = $request->headers->get('Origin');
        
        // Accepter toutes les origines pour le développement
        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-API-KEY');
        $response->headers->set('Access-Control-Expose-Headers', 'Link, X-Total-Count, X-Page-Count, X-Error-Message, Content-Type');
        $response->headers->set('Access-Control-Max-Age', '86400');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
    }
}

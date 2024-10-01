<?php

use Closure;
use BeyondCode\LaravelWebSockets\Facades\WebSockets;

class LogMessages
{
  public function handle($request, Closure $next)
  {
    $message = $request->getContent();

    // Log the message details (e.g., event name, payload)
    \Log::info('Received message:', json_decode($message, true));

    return $next($request);
  }
}

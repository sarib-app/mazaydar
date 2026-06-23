<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CamelCaseResponse {
    public function handle(Request $request, Closure $next) {
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            $response->setData($this->toCamel($response->getData(true)));
        }
        return $response;
    }

    private function toCamel(mixed $data): mixed {
        if (!is_array($data)) return $data;
        $result = [];
        foreach ($data as $key => $value) {
            $newKey = is_string($key)
                ? lcfirst(str_replace('_', '', ucwords($key, '_')))
                : $key;
            $result[$newKey] = $this->toCamel($value);
        }
        return $result;
    }
}

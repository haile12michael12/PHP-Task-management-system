<?php
/**
 * Simple Router Class
 * 
 * Handles routing of HTTP requests to the appropriate controller method
 */
class Router {
    private $routes = [];
    
    /**
     * Register a GET route
     * 
     * @param string $path The URL path to match
     * @param callable $callback The function to execute when this route is matched
     */
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    /**
     * Register a POST route
     * 
     * @param string $path The URL path to match
     * @param callable $callback The function to execute when this route is matched
     */
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    /**
     * Register a PUT route
     * 
     * @param string $path The URL path to match
     * @param callable $callback The function to execute when this route is matched
     */
    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }
    
    /**
     * Register a DELETE route
     * 
     * @param string $path The URL path to match
     * @param callable $callback The function to execute when this route is matched
     */
    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }
    
    /**
     * Add a route to the routes array
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path The URL path to match
     * @param callable $callback The function to execute when this route is matched
     */
    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    /**
     * Handle the current request and route it to the appropriate callback
     */
    public function route() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash (except for the root path)
        if ($requestPath !== '/' && substr($requestPath, -1) === '/') {
            $requestPath = rtrim($requestPath, '/');
        }
        
        foreach ($this->routes as $route) {
            // Check if the method and path match
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestPath)) {
                // Execute the callback function
                call_user_func($route['callback']);
                return;
            }
        }
        
        // No route matched, show 404 page
        $this->notFound();
    }
    
    /**
     * Check if the requested path matches a route path
     * 
     * @param string $routePath The route path to match against
     * @param string $requestPath The requested URL path
     * @return bool True if the paths match, false otherwise
     */
    private function matchPath($routePath, $requestPath) {
        // Exact match
        if ($routePath === $requestPath) {
            return true;
        }
        
        // TODO: Add support for path parameters if needed
        
        return false;
    }
    
    /**
     * Handle 404 Not Found errors
     */
    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        echo '<h1>404 Not Found</h1>';
        echo '<p>The page you requested could not be found.</p>';
        echo '<a href="/">Go to Homepage</a>';
        exit;
    }
}

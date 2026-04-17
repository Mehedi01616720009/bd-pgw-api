<?php

namespace Core;

use Core\Http\Request;
use Core\Http\Response;
use Core\Security\Validator;

class BaseController
{
    protected Request $request;
    protected Response $response;
    protected ?object $model = null;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->loadModel();
    }

    /**
     * Auto-load model based on controller name
     */
    private function loadModel(): void
    {
        // Get controller class name
        $controllerClass = get_class($this);
        $controllerName = basename(str_replace('\\', '/', $controllerClass));

        // Remove 'Controller' suffix to get model name
        $modelName = str_replace('Controller', '', $controllerName);

        // Build model class name
        $modelClass = "App\\Models\\{$modelName}";

        // Load model if exists
        if (class_exists($modelClass)) {
            $this->model = new $modelClass();
        }
    }

    /**
     * Render view
     */
    protected function view(string $name, array $data = []): Response
    {
        ob_start();
        view($name, $data);
        $content = ob_get_clean();

        return Response::html($content);
    }

    /**
     * JSON response
     */
    protected function json($data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * Redirect to route
     */
    protected function redirect(string $routeName, array $data = []): Response
    {
        return Response::redirect(route($routeName, $data));
    }

    /**
     * Redirect back
     */
    protected function back(): Response
    {
        return Response::back();
    }

    /**
     * Validate request
     */
    protected function validate(array $rules, array $customMessages = []): array
    {
        $validator = new Validator($this->request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            $_SESSION['old'] = $this->request->all();

            $response = Response::back();
            $response->send();
            exit();
        }

        return $validator->validated();
    }

    /**
     * Validate request (for API with JSON response)
     */
    protected function validateApi(array $rules, array $customMessages = []): array
    {
        $validator = new Validator($this->request->json(), $rules, $customMessages);

        if ($validator->fails()) {
            $response = Response::json([
                'success' => false,
                'message' => "Validation error!",
                'errors' => $validator->errors()
            ], 400);
            $response->send();
            exit();
        }

        return $validator->validated();
    }

    /**
     * Get authenticated user ID
     */
    protected function userId(): ?int
    {
        return user_id();
    }

    /**
     * Check if authenticated
     */
    protected function isAuth(): bool
    {
        return auth();
    }
}

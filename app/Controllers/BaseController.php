<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url', 'html', 'security'];

    /**
     * Session instance
     */
    protected $session;

    /**
     * Current user data
     */
    protected $currentUser;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        
        // Load current user data if authenticated
        if ($this->session->get('isLoggedIn')) {
            $this->currentUser = $this->session->get('userData');
        }
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * Check if user has required role level
     */
    protected function hasRole(int $requiredLevel): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $userLevel = $this->currentUser['level'] ?? null;
        return $userLevel !== null && $userLevel <= $requiredLevel;
    }

    /**
     * Get current user data
     */
    protected function getCurrentUser(): ?array
    {
        return $this->currentUser;
    }

    /**
     * Set flash message
     */
    protected function setFlashMessage(string $type, string $message): void
    {
        $this->session->setFlashdata('message_type', $type);
        $this->session->setFlashdata('message', $message);
    }

    /**
     * Redirect with success message
     */
    protected function redirectWithSuccess(string $url, string $message): ResponseInterface
    {
        $this->setFlashMessage('success', $message);
        return redirect()->to($url);
    }

    /**
     * Redirect with error message
     */
    protected function redirectWithError(string $url, string $message): ResponseInterface
    {
        $this->setFlashMessage('error', $message);
        return redirect()->to($url);
    }

    /**
     * Return JSON response
     */
    protected function jsonResponse(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }

    /**
     * Return success JSON response
     */
    protected function jsonSuccess(string $message = 'Success', array $data = []): ResponseInterface
    {
        return $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Return error JSON response
     */
    protected function jsonError(string $message = 'Error', array $errors = [], int $statusCode = 400): ResponseInterface
    {
        return $this->jsonResponse([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Validate CSRF token for AJAX requests
     */
    protected function validateCSRF(): bool
    {
        if ($this->request->isAJAX()) {
            $token = $this->request->getHeaderLine('X-CSRF-TOKEN') 
                  ?? $this->request->getPost('csrf_token_name');
            
            return csrf_verify($token);
        }
        
        return true;
    }

    /**
     * Log user activity
     */
    protected function logActivity(string $action, array $data = []): void
    {
        $logData = [
            'user_id' => $this->currentUser['id_user'] ?? null,
            'username' => $this->currentUser['username'] ?? 'guest',
            'action' => $action,
            'data' => $data,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        log_message('info', 'User Activity: ' . json_encode($logData));
    }
}
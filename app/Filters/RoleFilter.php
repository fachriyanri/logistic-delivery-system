<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Check if user is logged in first
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        // Get user data
        $userData = $session->get('userData');
        $userLevel = $userData['level'] ?? null;
        
        // Check if arguments are provided (required role level)
        if ($arguments && is_array($arguments) && count($arguments) > 0) {
            $requiredLevel = (int) $arguments[0];
            
            // Check if user has required role level
            // Lower level numbers have higher privileges (1 = Admin, 2 = Finance, 3 = Gudang)
            if ($userLevel === null || $userLevel > $requiredLevel) {
                // If it's an AJAX request, return JSON response
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Insufficient permissions',
                            'redirect' => '/dashboard'
                        ]);
                }
                
                // Redirect to dashboard with error message
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this page.');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
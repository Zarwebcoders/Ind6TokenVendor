<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\VendorModel;

class VendorApiFilter implements FilterInterface
{
    /**
     * Authenticate vendor by Bearer Token and IP Whitelist
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Unauthorized: Missing or invalid API token'
            ])->setStatusCode(401);
        }

        $token = $matches[1];
        $vendorModel = new VendorModel();

        $vendor = $vendorModel->where('api_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$vendor) {
            return service('response')->setJSON([
                'success' => false,
                'message' => 'Unauthorized: Invalid API token'
            ])->setStatusCode(401);
        }

        // IP Whitelist Check
        if (!empty($vendor['whitelisted_ips'])) {
            $clientIp = $request->getIPAddress();
            $allowedIps = array_map('trim', explode(',', $vendor['whitelisted_ips']));

            if (!in_array($clientIp, $allowedIps)) {
                log_message('error', "API Unauthorized: IP {$clientIp} not in whitelist for Vendor ID {$vendor['id']}");
                return service('response')->setJSON([
                    'success' => false,
                    'message' => 'Forbidden: IP not authorized'
                ])->setStatusCode(403);
            }
        }

        // Add vendor info to request object so controllers can access it
        $request->vendor = (object) $vendor;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}

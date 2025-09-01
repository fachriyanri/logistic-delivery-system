<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\PelangganService;
use CodeIgniter\HTTP\ResponseInterface;

class PelangganController extends BaseController
{
    protected PelangganService $pelangganService;

    public function __construct()
    {
        $this->pelangganService = new PelangganService();
    }

    /**
     * Display list of customers
     */
    public function index(): string
    {
        // Get filter parameters
        $filter = [
            'keyword' => trim($this->request->getGet('keyword') ?? '')
        ];

        $orderBy = $this->request->getGet('orderBy') ?? 'id_pelanggan';
        $orderType = $this->request->getGet('orderType') ?? 'ASC';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Get customers data
        [$customers, $total] = $this->pelangganService->getAllCustomers($filter, $limit, $offset, $orderBy, $orderType);

        // Setup pagination
        $pager = \Config\Services::pager();
        $pager->setPath('pelanggan');
        $pager->makeLinks($page, $limit, $total);

        $data = [
            'title' => 'Data Pelanggan',
            'customers' => $customers,
            'pager' => $pager,
            'total' => $total,
            'filter' => $filter,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'currentPage' => $page
        ];

        return view('pelanggan/index', $data);
    }

    /**
     * Show form for creating/editing customer
     */
    public function manage(?string $id = null): string
    {
        $data = [
            'title' => $id ? 'Edit Pelanggan' : 'Tambah Pelanggan',
            'isEdit' => !empty($id),
            'pelanggan' => null,
            'autocode' => $this->pelangganService->generateNextId()
        ];

        if ($id) {
            $pelanggan = $this->pelangganService->getCustomerById($id);
            if (!$pelanggan) {
                session()->setFlashdata('error', 'Pelanggan tidak ditemukan');
                return redirect()->to('/pelanggan');
            }
            $data['pelanggan'] = $pelanggan;
        }

        return view('pelanggan/manage', $data);
    }

    /**
     * Save customer (create or update)
     */
    public function save(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->to('/pelanggan');
        }

        $id = $post['id'] ?? '';

        // Prepare data
        $data = [
            'id_pelanggan' => $post['id_pelanggan'] ?? '',
            'nama' => $post['nama'] ?? '',
            'telepon' => $post['telepon'] ?? '',
            'alamat' => $post['alamat'] ?? ''
        ];

        // Validate data
        $errors = $this->pelangganService->validateCustomerData($data, $id);
        
        if (!empty($errors)) {
            session()->setFlashdata('error', implode('<br>', $errors));
            return redirect()->to('/pelanggan/manage/' . $id)->withInput();
        }

        // Save customer
        if (empty($id)) {
            // Create new customer
            $result = $this->pelangganService->createCustomer($data);
        } else {
            // Update existing customer
            $result = $this->pelangganService->updateCustomer($id, $data);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/pelanggan');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/pelanggan/manage/' . $id)->withInput();
        }
    }

    /**
     * Delete customer
     */
    public function delete(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID pelanggan tidak valid');
            return redirect()->to('/pelanggan');
        }

        $result = $this->pelangganService->deleteCustomer($id);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/pelanggan');
    }

    /**
     * AJAX endpoint to generate next customer ID
     */
    public function generateCode(): ResponseInterface
    {
        $nextId = $this->pelangganService->generateNextId();
        
        return $this->response->setJSON([
            'success' => true,
            'code' => $nextId
        ]);
    }

    /**
     * AJAX endpoint to check if customer name exists
     */
    public function checkName(): ResponseInterface
    {
        $name = $this->request->getPost('nama');
        $excludeId = $this->request->getPost('exclude_id') ?? '';
        
        $exists = $this->pelangganService->getCustomerById($name) !== null;
        
        return $this->response->setJSON([
            'exists' => $exists
        ]);
    }

    /**
     * AJAX endpoint to check if phone number exists
     */
    public function checkPhone(): ResponseInterface
    {
        $phone = $this->request->getPost('telepon');
        $excludeId = $this->request->getPost('exclude_id') ?? '';
        
        $exists = $this->pelangganService->getCustomerById($phone) !== null;
        
        return $this->response->setJSON([
            'exists' => $exists
        ]);
    }

    /**
     * Get customers for AJAX dropdown
     */
    public function getForSelect(): ResponseInterface
    {
        $customers = $this->pelangganService->getCustomersForSelect();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * AJAX search customers
     */
    public function search(): ResponseInterface
    {
        $keyword = $this->request->getGet('q') ?? '';
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        
        if (empty($keyword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Keyword tidak boleh kosong'
            ]);
        }

        $customers = $this->pelangganService->searchCustomers($keyword, $limit);
        
        $results = [];
        foreach ($customers as $customer) {
            $results[] = [
                'id' => $customer->id_pelanggan,
                'text' => $customer->nama . ' (' . $customer->telepon . ')',
                'nama' => $customer->nama,
                'telepon' => $customer->telepon,
                'alamat' => $customer->alamat
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get customer contact information (AJAX)
     */
    public function getContact(): ResponseInterface
    {
        $id = $this->request->getGet('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID pelanggan tidak valid'
            ]);
        }

        $contact = $this->pelangganService->getCustomerContact($id);
        
        if (!$contact) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $contact
        ]);
    }

    /**
     * Show customer statistics
     */
    public function statistics(): ResponseInterface
    {
        $stats = $this->pelangganService->getCustomerStatistics();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get customers by type
     */
    public function getByType(): ResponseInterface
    {
        $customersByType = $this->pelangganService->getCustomersByType();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customersByType
        ]);
    }

    /**
     * Validate phone number format (AJAX)
     */
    public function validatePhone(): ResponseInterface
    {
        $phone = $this->request->getPost('telepon');
        
        if (empty($phone)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor telepon tidak boleh kosong'
            ]);
        }

        $isValid = $this->pelangganService->validatePhoneNumber($phone);
        $formatted = $this->pelangganService->formatPhoneNumber($phone);
        
        return $this->response->setJSON([
            'success' => true,
            'valid' => $isValid,
            'formatted' => $formatted,
            'message' => $isValid ? 'Format nomor telepon valid' : 'Format nomor telepon tidak valid'
        ]);
    }
}
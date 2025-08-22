<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\KurirService;
use CodeIgniter\HTTP\ResponseInterface;

class KurirController extends BaseController
{
    protected KurirService $kurirService;

    public function __construct()
    {
        $this->kurirService = new KurirService();
    }

    /**
     * Display list of couriers
     */
    public function index(): string
    {
        // Get filter parameters
        $filter = [
            'keyword' => trim($this->request->getGet('keyword') ?? ''),
            'jenis_kelamin' => trim($this->request->getGet('gender') ?? '')
        ];

        $orderBy = $this->request->getGet('orderBy') ?? 'id_kurir';
        $orderType = $this->request->getGet('orderType') ?? 'ASC';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Get couriers data
        [$couriers, $total] = $this->kurirService->getAllCouriers($filter, $limit, $offset, $orderBy, $orderType);

        // Get gender options for filter dropdown
        $genderOptions = $this->kurirService->getGenderOptions();

        // Setup pagination
        $pager = \Config\Services::pager();
        $pager->setPath('kurir');
        $pager->makeLinks($page, $limit, $total);

        $data = [
            'title' => 'Data Kurir',
            'couriers' => $couriers,
            'genderOptions' => $genderOptions,
            'pager' => $pager,
            'total' => $total,
            'filter' => $filter,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'currentPage' => $page
        ];

        return view('kurir/index', $data);
    }

    /**
     * Show form for creating/editing courier
     */
    public function manage(?string $id = null): string
    {
        $data = [
            'title' => $id ? 'Edit Kurir' : 'Tambah Kurir',
            'isEdit' => !empty($id),
            'kurir' => null,
            'genderOptions' => $this->kurirService->getGenderOptions(),
            'autocode' => $this->kurirService->generateNextId()
        ];

        if ($id) {
            $kurir = $this->kurirService->getCourierById($id);
            if (!$kurir) {
                session()->setFlashdata('error', 'Kurir tidak ditemukan');
                return redirect()->to('/kurir');
            }
            $data['kurir'] = $kurir;
        }

        return view('kurir/manage', $data);
    }

    /**
     * Save courier (create or update)
     */
    public function save(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->to('/kurir');
        }

        $id = $post['id'] ?? '';
        $action = $post['action'] ?? 'save';

        // Prepare data
        $data = [
            'id_kurir' => $post['id_kurir'] ?? '',
            'nama' => $post['nama'] ?? '',
            'jenis_kelamin' => $post['jenis_kelamin'] ?? '',
            'telepon' => $post['telepon'] ?? '',
            'alamat' => $post['alamat'] ?? '',
            'password' => $post['password'] ?? ''
        ];

        // Validate data
        $errors = $this->kurirService->validateCourierData($data, $id);
        
        if (!empty($errors)) {
            session()->setFlashdata('error', implode('<br>', $errors));
            return redirect()->to('/kurir/manage/' . $id)->withInput();
        }

        // Save courier
        if (empty($id)) {
            // Create new courier
            $result = $this->kurirService->createCourier($data);
        } else {
            // Update existing courier
            $result = $this->kurirService->updateCourier($id, $data);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            
            if ($action === 'save') {
                $redirectId = $result['data']->id_kurir ?? $id;
                return redirect()->to('/kurir/manage/' . $redirectId);
            } else {
                return redirect()->to('/kurir');
            }
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/kurir/manage/' . $id)->withInput();
        }
    }

    /**
     * Delete courier
     */
    public function delete(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID kurir tidak valid');
            return redirect()->to('/kurir');
        }

        $result = $this->kurirService->deleteCourier($id);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/kurir');
    }

    /**
     * AJAX endpoint to generate next courier ID
     */
    public function generateCode(): ResponseInterface
    {
        $nextId = $this->kurirService->generateNextId();
        
        return $this->response->setJSON([
            'success' => true,
            'code' => $nextId
        ]);
    }

    /**
     * AJAX endpoint to check if phone number exists
     */
    public function checkPhone(): ResponseInterface
    {
        $phone = $this->request->getPost('telepon');
        $excludeId = $this->request->getPost('exclude_id') ?? '';
        
        $exists = $this->kurirService->getCourierById($phone) !== null;
        
        return $this->response->setJSON([
            'exists' => $exists
        ]);
    }

    /**
     * Get couriers for AJAX dropdown
     */
    public function getForSelect(): ResponseInterface
    {
        $couriers = $this->kurirService->getCouriersForSelect();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $couriers
        ]);
    }

    /**
     * AJAX search couriers
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

        $couriers = $this->kurirService->searchCouriers($keyword, $limit);
        
        $results = [];
        foreach ($couriers as $courier) {
            $results[] = [
                'id' => $courier->id_kurir,
                'text' => $courier->nama . ' (' . $courier->telepon . ')',
                'nama' => $courier->nama,
                'telepon' => $courier->telepon,
                'jenis_kelamin' => $courier->jenis_kelamin
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Update courier password
     */
    public function updatePassword(): ResponseInterface
    {
        $id = $this->request->getPost('id_kurir');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');
        
        if (empty($id) || empty($newPassword) || empty($confirmPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semua field harus diisi'
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Konfirmasi password tidak cocok'
            ]);
        }

        $result = $this->kurirService->updatePassword($id, $newPassword);
        
        return $this->response->setJSON($result);
    }

    /**
     * Show courier statistics
     */
    public function statistics(): ResponseInterface
    {
        $stats = $this->kurirService->getCourierStatistics();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $stats
        ]);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\BarangService;
use CodeIgniter\HTTP\ResponseInterface;

class BarangController extends BaseController
{
    protected BarangService $barangService;

    public function __construct()
    {
        $this->barangService = new BarangService();
    }

    /**
     * Display list of items
     */
    public function index(): string
    {
        // Get filter parameters
        $filter = [
            'keyword' => trim($this->request->getGet('keyword') ?? ''),
            'id_kategori' => trim($this->request->getGet('kategori') ?? '')
        ];

        $orderBy = $this->request->getGet('orderBy') ?? 'id_barang';
        $orderType = $this->request->getGet('orderType') ?? 'ASC';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Get items data
        [$items, $total] = $this->barangService->getAllItems($filter, $limit, $offset, $orderBy, $orderType);

        // Get categories for filter dropdown
        $categories = $this->barangService->getCategoriesForSelect();

        // Setup pagination
        $pager = \Config\Services::pager();
        $pager->setPath('barang');
        $pager->makeLinks($page, $limit, $total);

        $data = [
            'title' => 'Data Barang',
            'items' => $items,
            'categories' => $categories,
            'pager' => $pager,
            'total' => $total,
            'filter' => $filter,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'currentPage' => $page
        ];

        return view('barang/index', $data);
    }

    /**
     * Show form for creating/editing item
     */
    public function manage(?string $id = null): string
    {
        $data = [
            'title' => $id ? 'Edit Barang' : 'Tambah Barang',
            'isEdit' => !empty($id),
            'barang' => null,
            'categories' => $this->barangService->getCategoriesForSelect(),
            'autocode' => $this->barangService->generateNextId()
        ];

        if ($id) {
            $barang = $this->barangService->getItemById($id);
            if (!$barang) {
                session()->setFlashdata('error', 'Barang tidak ditemukan');
                return redirect()->to('/barang');
            }
            $data['barang'] = $barang;
        }

        return view('barang/manage', $data);
    }

    /**
     * Save item (create or update)
     */
    public function save(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->to('/barang');
        }

        $id = $post['id'] ?? '';
        $action = $post['action'] ?? 'save';

        // Prepare data
        $data = [
            'id_barang' => $post['id_barang'] ?? '',
            'nama' => $post['nama'] ?? '',
            'satuan' => $post['satuan'] ?? '',
            'harga' => $post['harga'] ?? 0.00,
            'id_kategori' => $post['id_kategori'] ?? ''
        ];

        // Validate data
        $errors = $this->barangService->validateItemData($data, $id);
        
        if (!empty($errors)) {
            session()->setFlashdata('error', implode('<br>', $errors));
            return redirect()->to('/barang/manage/' . $id)->withInput();
        }

        // Save item
        if (empty($id)) {
            // Create new item
            $result = $this->barangService->createItem($data);
        } else {
            // Update existing item
            $result = $this->barangService->updateItem($id, $data);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/barang');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/barang/manage/' . $id)->withInput();
        }
    }

    /**
     * Delete item
     */
    public function delete(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID barang tidak valid');
            return redirect()->to('/barang');
        }

        $result = $this->barangService->deleteItem($id);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/barang');
    }

    /**
     * AJAX endpoint to generate next item ID
     */
    public function generateCode(): ResponseInterface
    {
        $nextId = $this->barangService->generateNextId();
        
        return $this->response->setJSON([
            'success' => true,
            'code' => $nextId
        ]);
    }

    /**
     * AJAX endpoint to check if item name exists
     */
    public function checkName(): ResponseInterface
    {
        $name = $this->request->getPost('nama');
        $excludeId = $this->request->getPost('exclude_id') ?? '';
        
        $exists = $this->barangService->getItemById($name) !== null;
        
        return $this->response->setJSON([
            'exists' => $exists
        ]);
    }

    /**
     * Get items for AJAX dropdown
     */
    public function getForSelect(): ResponseInterface
    {
        $categoryId = $this->request->getGet('kategori') ?? '';
        $items = $this->barangService->getItemsForSelect($categoryId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * AJAX search items
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

        $items = $this->barangService->searchItems($keyword, $limit);
        
        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->id_barang,
                'text' => $item->nama . ' (' . $item->satuan . ')',
                'nama' => $item->nama,
                'satuan' => $item->satuan,
                'kategori' => $item->getCategoryName()
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get items by category (AJAX)
     */
    public function getByCategory(): ResponseInterface
    {
        $categoryId = $this->request->getGet('kategori_id') ?? '';
        
        if (empty($categoryId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID kategori tidak valid'
            ]);
        }

        $items = $this->barangService->getItemsByCategory($categoryId);
        
        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->id_barang,
                'nama' => $item->nama,
                'satuan' => $item->satuan
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $results
        ]);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\KategoriService;
use CodeIgniter\HTTP\ResponseInterface;

class KategoriController extends BaseController
{
    protected KategoriService $kategoriService;

    public function __construct()
    {
        $this->kategoriService = new KategoriService();
    }

    /**
     * Display list of categories
     */
    public function index(): string
    {
        // Get filter parameters
        $filter = [
            'keyword' => trim($this->request->getGet('keyword') ?? '')
        ];

        $orderBy = $this->request->getGet('orderBy') ?? 'id_kategori';
        $orderType = $this->request->getGet('orderType') ?? 'ASC';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Get categories data
        [$categories, $total] = $this->kategoriService->getAllCategories($filter, $limit, $offset, $orderBy, $orderType);

        // Setup pagination
        $pager = \Config\Services::pager();
        $pager->setPath('kategori');
        $pager->makeLinks($page, $limit, $total);

        $data = [
            'title' => 'Data Kategori',
            'categories' => $categories,
            'pager' => $pager,
            'total' => $total,
            'filter' => $filter,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'currentPage' => $page
        ];

        return view('kategori/index', $data);
    }

    /**
     * Show form for creating/editing category
     */
    public function manage(?string $id = null): string
    {
        $data = [
            'title' => $id ? 'Edit Kategori' : 'Tambah Kategori',
            'isEdit' => !empty($id),
            'kategori' => null,
            'autocode' => $this->kategoriService->generateNextId()
        ];

        if ($id) {
            $kategori = $this->kategoriService->getCategoryById($id);
            if (!$kategori) {
                session()->setFlashdata('error', 'Kategori tidak ditemukan');
                return redirect()->to('/kategori');
            }
            $data['kategori'] = $kategori;
        }

        return view('kategori/manage', $data);
    }

    /**
     * Save category (create or update)
     */
    public function save(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        if (!$post) {
            return redirect()->to('/kategori');
        }

        $id = $post['id'] ?? '';
        $action = $post['action'] ?? 'save';

        // Prepare data
        $data = [
            'id_kategori' => $post['id_kategori'] ?? '',
            'nama' => $post['nama'] ?? '',
            'keterangan' => $post['keterangan'] ?? ''
        ];

        // Validate data
        $errors = $this->kategoriService->validateCategoryData($data, $id);
        
        if (!empty($errors)) {
            session()->setFlashdata('error', implode('<br>', $errors));
            return redirect()->to('/kategori/manage/' . $id)->withInput();
        }

        // Save category
        if (empty($id)) {
            // Create new category
            $result = $this->kategoriService->createCategory($data);
        } else {
            // Update existing category
            $result = $this->kategoriService->updateCategory($id, $data);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            
            if ($action === 'save') {
                $redirectId = $result['data']->id_kategori ?? $id;
                return redirect()->to('/kategori/manage/' . $redirectId);
            } else {
                return redirect()->to('/kategori');
            }
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/kategori/manage/' . $id)->withInput();
        }
    }

    /**
     * Delete category
     */
    public function delete(?string $id = null): ResponseInterface
    {
        if (empty($id)) {
            session()->setFlashdata('error', 'ID kategori tidak valid');
            return redirect()->to('/kategori');
        }

        $result = $this->kategoriService->deleteCategory($id);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/kategori');
    }

    /**
     * AJAX endpoint to generate next category ID
     */
    public function generateCode(): ResponseInterface
    {
        $nextId = $this->kategoriService->generateNextId();
        
        return $this->response->setJSON([
            'success' => true,
            'code' => $nextId
        ]);
    }

    /**
     * AJAX endpoint to check if category name exists
     */
    public function checkName(): ResponseInterface
    {
        $name = $this->request->getPost('nama');
        $excludeId = $this->request->getPost('exclude_id') ?? '';
        
        $exists = $this->kategoriService->getCategoryById($name) !== null;
        
        return $this->response->setJSON([
            'exists' => $exists
        ]);
    }

    /**
     * Get categories for AJAX dropdown
     */
    public function getForSelect(): ResponseInterface
    {
        $categories = $this->kategoriService->getCategoriesForSelect();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }
}
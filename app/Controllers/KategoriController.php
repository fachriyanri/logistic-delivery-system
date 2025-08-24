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
            'autocode' => $this->kategoriService->generateNextId(),
            'validation' => session('validation')
        ];

        if ($id) {
            $kategori = $this->kategoriService->getCategoryById($id);
            if (!$kategori) {
                session()->setFlashdata('error', 'Kategori tidak ditemukan');
                return redirect()->to('/kategori')->send();
            }
            $data['kategori'] = $kategori;
        }

        return view('kategori/manage', $data);
    }

    /**
     * Show simplified form for creating/editing category (for debugging)
     */
    public function manageSimple(?string $id = null): string
    {
        $data = [
            'title' => $id ? 'Edit Kategori (Simple)' : 'Tambah Kategori (Simple)',
            'isEdit' => !empty($id),
            'kategori' => null,
            'autocode' => $this->kategoriService->generateNextId(),
            'validation' => session('validation')
        ];

        if ($id) {
            $kategori = $this->kategoriService->getCategoryById($id);
            if (!$kategori) {
                session()->setFlashdata('error', 'Kategori tidak ditemukan');
                return redirect()->to('/kategori')->send();
            }
            $data['kategori'] = $kategori;
        }

        return view('kategori/manage_simple', $data);
    }

    /**
     * Save category (create or update)
     */
    public function save(): ResponseInterface
    {
        $post = $this->request->getPost();
        
        if (!$post) {
            session()->setFlashdata('error', 'Data tidak valid');
            return redirect()->to('/kategori');
        }

        // Prepare data
        $data = [
            'id_kategori' => trim($post['id_kategori'] ?? ''),
            'nama' => trim($post['nama'] ?? ''),
            'keterangan' => trim($post['keterangan'] ?? '')
        ];

        // Determine if this is an edit or create operation
        $isEdit = !empty($post['original_id']) || $this->kategoriService->getCategoryById($data['id_kategori']);
        $originalId = $post['original_id'] ?? $data['id_kategori'];

        // Validate data
        $errors = $this->kategoriService->validateCategoryData($data, $isEdit ? $originalId : '');
        
        if (!empty($errors)) {
            session()->setFlashdata('error', implode('<br>', $errors));
            if ($isEdit) {
                return redirect()->to('/kategori/manage/' . $originalId)->withInput();
            } else {
                return redirect()->to('/kategori/manage')->withInput();
            }
        }

        // Save category
        if ($isEdit) {
            // Update existing category
            $result = $this->kategoriService->updateCategory($originalId, $data);
        } else {
            // Create new category
            $result = $this->kategoriService->createCategory($data);
        }

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/kategori');
        } else {
            session()->setFlashdata('error', $result['message']);
            if ($isEdit) {
                return redirect()->to('/kategori/manage/' . $originalId)->withInput();
            } else {
                return redirect()->to('/kategori/manage')->withInput();
            }
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

        // Check if category exists using direct database query
        $db = \Config\Database::connect();
        $existingCount = $db->table('kategori')->where('id_kategori', $id)->countAllResults();
        
        if ($existingCount === 0) {
            session()->setFlashdata('error', 'Kategori tidak ditemukan');
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
        
        $exists = $this->kategoriService->getCategoryByName($name, $excludeId);
        
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

    /**
     * Export categories to Excel
     */
    public function exportExcel(): ResponseInterface
    {
        // Get all categories without pagination
        [$categories, $total] = $this->kategoriService->getAllCategories([], 0, 0);
        
        // Create Excel file using PhpSpreadsheet
        $filename = 'kategori_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Set proper headers for Excel download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column headers
        $sheet->setCellValue('A1', 'ID Kategori')
              ->setCellValue('B1', 'Nama Kategori')
              ->setCellValue('C1', 'Keterangan');
        
        // Add data
        $row = 2;
        foreach ($categories as $kategori) {
            $sheet->setCellValue('A' . $row, $kategori->id_kategori)
                  ->setCellValue('B' . $row, $kategori->nama)
                  ->setCellValue('C' . $row, $kategori->keterangan ?? '');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Freeze the first row
        $sheet->freezePane('A2');
        
        // Redirect output to a client's web browser (Xlsx)
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
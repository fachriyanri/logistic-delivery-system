<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?php
    // Prepare form fields configuration
    $formFields = [];
    
    // Add hidden field for edit operations
    if ($isEdit ?? false) {
        $formFields['original_id'] = [
            'type' => 'hidden',
            'value' => $kategori->id_kategori ?? ''
        ];
    }
    
    $formFields = array_merge($formFields, [
        'group_basic' => [
            'type' => 'group_start',
            'label' => 'Basic Information',
            'icon' => 'fas fa-info-circle'
        ],
        'id_kategori' => [
            'label' => 'Category ID',
            'type' => 'text',
            'value' => old('id_kategori', $kategori->id_kategori ?? $autocode),
            'required' => true,
            'icon' => 'fas fa-tag',
            'placeholder' => 'e.g., KTG01',
            'help' => 'Format: KTGxx (example: KTG01)',
            'width' => 'col-md-6',
            'attributes' => [
                'maxlength' => '5',
                'pattern' => 'KTG[0-9]{2}',
                'readonly' => $isEdit ?? false
            ]
        ],
        'nama' => [
            'label' => 'Category Name',
            'type' => 'text',
            'value' => old('nama', $kategori->nama ?? ''),
            'required' => true,
            'icon' => 'fas fa-folder',
            'placeholder' => 'Enter category name',
            'help' => 'Maximum 30 characters',
            'width' => 'col-md-6',
            'attributes' => [
                'maxlength' => '30'
            ]
        ],
        'group_basic_end' => [
            'type' => 'group_end'
        ],
        'group_details' => [
            'type' => 'group_start',
            'label' => 'Additional Details',
            'icon' => 'fas fa-edit'
        ],
        'keterangan' => [
            'label' => 'Description',
            'type' => 'textarea',
            'value' => old('keterangan', $kategori->keterangan ?? ''),
            'required' => false,
            'icon' => 'fas fa-align-left',
            'placeholder' => 'Enter category description (optional)',
            'help' => 'Maximum 150 characters (optional)',
            'width' => 'col-12',
            'rows' => 4,
            'attributes' => [
                'maxlength' => '150'
            ]
        ],
        'group_details_end' => [
            'type' => 'group_end'
        ]
    ]);

    // Set page actions for breadcrumb
    $pageActions = [
        [
            'title' => 'Back to Categories',
            'url' => base_url('kategori'),
            'icon' => 'fas fa-arrow-left',
            'class' => 'btn-outline-secondary'
        ]
    ];
    ?>

    <!-- Page Content -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?= component('form', [
                'action' => base_url('kategori/save'),
                'method' => 'POST',
                'fields' => $formFields,
                'data' => [],
                'validation' => $validation ?? null,
                'submitText' => ($isEdit ?? false) ? 'Update Category' : 'Create Category',
                'cancelUrl' => base_url('kategori'),
                'ajax' => false,
                'class' => 'category-form',
                'attributes' => [
                    'id' => 'categoryForm'
                ]
            ]) ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    if (!form) return;
    
    const idKategoriField = form.querySelector('[name="id_kategori"]');
    const namaField = form.querySelector('[name="nama"]');
    const keteranganField = form.querySelector('[name="keterangan"]');
    
    // ID is auto-incremented by the system (KTG01, KTG02, etc.)
    // No need for auto-generation from category name
    
    // Add character counters
    function addCharacterCounter(field, maxLength) {
        if (!field) return;
        
        const counter = document.createElement('div');
        counter.className = 'character-counter text-end mt-1';
        counter.style.fontSize = '0.75rem';
        
        function updateCounter() {
            const percentage = (field.value.length / maxLength) * 100;
            counter.textContent = `${field.value.length}/${maxLength} characters`;
            
            if (percentage >= 90) {
                counter.className = 'character-counter text-end mt-1 text-danger';
            } else if (percentage >= 75) {
                counter.className = 'character-counter text-end mt-1 text-warning';
            } else {
                counter.className = 'character-counter text-end mt-1 text-muted';
            }
        }
        
        field.parentNode.appendChild(counter);
        field.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // Add character counters to fields
    if (namaField) addCharacterCounter(namaField, 30);
    if (keteranganField) addCharacterCounter(keteranganField, 150);
    
    // Basic form validation
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        
        // Validate required fields
        if (idKategoriField && !idKategoriField.value.trim()) {
            hasErrors = true;
            showFieldError(idKategoriField, 'ID Kategori harus diisi');
        } else if (idKategoriField && !/^KTG[0-9]{2}$/.test(idKategoriField.value)) {
            hasErrors = true;
            showFieldError(idKategoriField, 'Format ID harus KTGxx (contoh: KTG01)');
        }
        
        if (namaField && !namaField.value.trim()) {
            hasErrors = true;
            showFieldError(namaField, 'Nama kategori harus diisi');
        } else if (namaField && namaField.value.length < 3) {
            hasErrors = true;
            showFieldError(namaField, 'Nama kategori minimal 3 karakter');
        }
        
        if (hasErrors) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        }
    });
    
    function showFieldError(field, message) {
        // Remove existing error
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-danger mt-1';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
        
        // Add error styling to field
        field.classList.add('is-invalid');
        
        // Remove error on input
        field.addEventListener('input', function() {
            field.classList.remove('is-invalid');
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, { once: true });
    }
});
</script>
<?= $this->endSection() ?>
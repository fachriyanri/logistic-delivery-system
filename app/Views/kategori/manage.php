<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?php
    // Prepare form fields configuration
    $formFields = [
        'id' => [
            'type' => 'hidden',
            'value' => $kategori->id_kategori ?? ''
        ],
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
    ];

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
    const idKategoriField = form.querySelector('[name="id_kategori"]');
    const namaField = form.querySelector('[name="nama"]');
    const keteranganField = form.querySelector('[name="keterangan"]');
    
    // Auto-generate category ID from name (for new categories)
    <?php if (!($isEdit ?? false)): ?>
    if (namaField && idKategoriField) {
        namaField.addEventListener('input', function() {
            if (!idKategoriField.value || idKategoriField.value === '<?= $autocode ?>') {
                // Generate ID from name
                let generatedId = 'KTG';
                const nameWords = this.value.trim().split(' ');
                if (nameWords.length > 0 && nameWords[0]) {
                    const firstWord = nameWords[0].substring(0, 2).toUpperCase();
                    generatedId += firstWord.padEnd(2, '0');
                } else {
                    generatedId += '01';
                }
                
                // Check if this ID already exists (you might want to implement this check)
                idKategoriField.value = generatedId;
            }
        });
    }
    <?php endif; ?>
    
    // Add character counters
    function addCharacterCounter(field, maxLength) {
        if (!field) return;
        
        const counter = document.createElement('div');
        counter.className = 'character-counter text-end mt-1';
        counter.style.fontSize = '0.75rem';
        
        function updateCounter() {
            const remaining = maxLength - field.value.length;
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
    addCharacterCounter(namaField, 30);
    addCharacterCounter(keteranganField, 150);
    
    // Form validation enhancements
    const formValidator = new FormValidator(form, {
        realTimeValidation: true,
        showToasts: true
    });
    
    // Add custom validation rules
    formValidator.addRule('id_kategori', {
        type: 'custom',
        validator: function(value, field) {
            const pattern = /^KTG[0-9]{2}$/;
            return {
                valid: pattern.test(value),
                message: 'Category ID must follow format KTGxx (e.g., KTG01)'
            };
        }
    });
    
    formValidator.addRule('nama', {
        type: 'custom',
        validator: function(value, field) {
            if (value.length < 3) {
                return {
                    valid: false,
                    message: 'Category name must be at least 3 characters long'
                };
            }
            return { valid: true, message: '' };
        }
    });
    
    // Handle form success
    form.addEventListener('formSuccess', function(e) {
        showToast('Category saved successfully!', 'success');
        
        // Redirect after a short delay
        setTimeout(() => {
            window.location.href = '<?= base_url('kategori') ?>';
        }, 1500);
    });
    
    // Handle form errors
    form.addEventListener('formError', function(e) {
        showToast('Please correct the errors and try again.', 'error');
    });
});
</script>
<?= $this->endSection() ?>
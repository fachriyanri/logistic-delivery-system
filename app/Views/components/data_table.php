<?php
/**
 * Data Table Component
 * 
 * @param array $columns - Table columns configuration
 * @param array $data - Table data
 * @param array $options - Table options
 * @param string $id - Table ID
 * @param bool $searchable - Enable search functionality
 * @param bool $sortable - Enable sorting functionality
 * @param bool $paginated - Enable pagination
 * @param int $perPage - Items per page
 * @param string $emptyMessage - Message when no data
 * @param array $actions - Row actions
 * @param string $class - Additional CSS classes
 */

$columns = $columns ?? [];
$data = $data ?? [];
$options = $options ?? [];
$id = $id ?? 'dataTable-' . uniqid();
$searchable = $searchable ?? true;
$sortable = $sortable ?? true;
$paginated = $paginated ?? true;
$perPage = $perPage ?? 10;
$emptyMessage = $emptyMessage ?? 'No data available';
$actions = $actions ?? [];
$class = $class ?? '';

$tableClass = "table table-hover data-table";
if ($class) {
    $tableClass .= " {$class}";
}

// Generate unique IDs for components
$searchId = $id . '-search';
$paginationId = $id . '-pagination';
$infoId = $id . '-info';
?>

<div class="data-table-wrapper">
    <!-- Table Header Controls -->
    <div class="data-table-header mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <?php if ($searchable): ?>
                <div class="data-table-search">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="<?= $searchId ?>" 
                               placeholder="Search..."
                               data-table="<?= $id ?>">
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <!-- Per Page Selector -->
                    <?php if ($paginated): ?>
                    <div class="data-table-per-page">
                        <label class="form-label mb-0 me-2">Show:</label>
                        <select class="form-select form-select-sm" 
                                style="width: auto;" 
                                data-table="<?= $id ?>"
                                data-action="per-page">
                            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Export Options -->
                    <div class="data-table-export">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                    type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-download me-1"></i>
                                Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" 
                                       href="#" 
                                       data-table="<?= $id ?>" 
                                       data-action="export-csv">
                                        <i class="fas fa-file-csv me-2"></i>
                                        Export CSV
                                    </a>
                                </li>
                                <li>
                                    <?php if (isset($options['exportExcelUrl'])): ?>
                                    <a class="dropdown-item" 
                                       href="<?= $options['exportExcelUrl'] ?>">
                                        <i class="fas fa-file-excel me-2"></i>
                                        Export Excel
                                    </a>
                                    <?php else: ?>
                                    <a class="dropdown-item" 
                                       href="#" 
                                       data-table="<?= $id ?>" 
                                       data-action="export-excel">
                                        <i class="fas fa-file-excel me-2"></i>
                                        Export Excel
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <a class="dropdown-item" 
                                       href="#" 
                                       data-table="<?= $id ?>" 
                                       data-action="print">
                                        <i class="fas fa-print me-2"></i>
                                        Print
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-responsive">
        <table id="<?= $id ?>" class="<?= $tableClass ?>" data-sortable="<?= $sortable ? 'true' : 'false' ?>">
            <thead class="table-dark">
                <tr>
                    <?php foreach ($columns as $key => $column): ?>
                        <?php
                        $columnTitle = is_array($column) ? ($column['title'] ?? ucfirst($key)) : $column;
                        $sortable = is_array($column) ? ($column['sortable'] ?? true) : true;
                        $width = is_array($column) ? ($column['width'] ?? '') : '';
                        $class = is_array($column) ? ($column['class'] ?? '') : '';
                        ?>
                        <th <?= $width ? 'style="width: ' . $width . '"' : '' ?> 
                            <?= $class ? 'class="' . $class . '"' : '' ?>
                            <?= $sortable ? 'data-sortable="true" data-column="' . $key . '"' : '' ?>>
                            <?= esc($columnTitle) ?>
                            <?php if ($sortable): ?>
                                <i class="fas fa-sort ms-1 sort-icon"></i>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
                    
                    <?php if (!empty($actions)): ?>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $key => $column): ?>
                                <td>
                                    <?php
                                    if (is_array($column) && isset($column['render'])) {
                                        // Custom render function
                                        echo $column['render']($row[$key] ?? '', $row);
                                    } elseif (is_array($column) && isset($column['type'])) {
                                        // Handle different data types
                                        switch ($column['type']) {
                                            case 'date':
                                                echo format_date($row[$key] ?? '');
                                                break;
                                            case 'datetime':
                                                echo format_datetime($row[$key] ?? '');
                                                break;
                                            case 'currency':
                                                echo format_currency($row[$key] ?? 0);
                                                break;
                                            case 'badge':
                                                echo badge($row[$key] ?? '', $column['variant'] ?? 'secondary');
                                                break;
                                            case 'status':
                                                echo status_badge($row[$key] ?? 0);
                                                break;
                                            default:
                                                echo esc($row[$key] ?? '');
                                        }
                                    } else {
                                        echo esc($row[$key] ?? '');
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                            
                            <?php if (!empty($actions)): ?>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php foreach ($actions as $action): ?>
                                            <?php
                                            $url = $action['url'];
                                            // Replace placeholders in URL with row data
                                            foreach ($row as $field => $value) {
                                                $url = str_replace('{' . $field . '}', $value, $url);
                                            }
                                            
                                            // Debug: Check URL replacement in development
                                            if (ENVIRONMENT === 'development' && strpos($url, '{') !== false) {
                                                error_log("URL placeholder not replaced: " . $url . " | Row data: " . json_encode($row));
                                            }
                                            ?>
                                            <a href="<?= $url ?>" 
                                               class="btn <?= $action['class'] ?? 'btn-outline-primary' ?>"
                                               <?= isset($action['title']) ? 'title="' . esc($action['title']) . '"' : '' ?>
                                               <?= isset($action['confirm']) ? 'data-action="confirm-delete" data-message="' . esc($action['confirm']) . '"' : '' ?>>
                                                <?php if (isset($action['icon'])): ?>
                                                    <i class="<?= $action['icon'] ?>"></i>
                                                <?php endif; ?>
                                                <?php if (isset($action['text'])): ?>
                                                    <?= esc($action['text']) ?>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($columns) + (!empty($actions) ? 1 : 0) ?>" class="text-center py-5">
                            <?= empty_state('No Data Found', $emptyMessage, [
                                'icon' => 'fas fa-table',
                                'class' => 'py-3'
                            ]) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <?php if ($paginated && !empty($data)): ?>
    <div class="data-table-footer mt-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div id="<?= $infoId ?>" class="data-table-info text-muted">
                    Showing 1 to <?= min($perPage, count($data)) ?> of <?= count($data) ?> entries
                </div>
            </div>
            <div class="col-md-6">
                <nav aria-label="Table pagination">
                    <ul id="<?= $paginationId ?>" class="pagination pagination-sm justify-content-end mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Initialize DataTable -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof DataTableManager !== 'undefined') {
        new DataTableManager('<?= $id ?>', {
            searchable: <?= $searchable ? 'true' : 'false' ?>,
            sortable: <?= $sortable ? 'true' : 'false' ?>,
            paginated: <?= $paginated ? 'true' : 'false' ?>,
            perPage: <?= $perPage ?>
        });
    }
});
</script>
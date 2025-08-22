/**
 * DataTable Manager
 * Handles sorting, filtering, pagination for data tables
 */

class DataTableManager {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = document.getElementById(tableId);
        this.options = {
            searchable: true,
            sortable: true,
            paginated: true,
            perPage: 10,
            ...options
        };
        
        this.currentPage = 1;
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.searchTerm = '';
        this.originalData = [];
        this.filteredData = [];
        
        this.init();
    }

    init() {
        if (!this.table) {
            console.error(`Table with ID ${this.tableId} not found`);
            return;
        }

        this.extractData();
        this.setupEventListeners();
        this.render();
    }

    extractData() {
        const tbody = this.table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');
        
        this.originalData = Array.from(rows).map(row => {
            const cells = row.querySelectorAll('td');
            const rowData = {
                element: row,
                data: Array.from(cells).map(cell => cell.textContent.trim())
            };
            return rowData;
        });
        
        this.filteredData = [...this.originalData];
    }

    setupEventListeners() {
        // Search functionality
        if (this.options.searchable) {
            const searchInput = document.querySelector(`input[data-table="${this.tableId}"]`);
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.searchTerm = e.target.value.toLowerCase();
                    this.applyFilters();
                });
            }
        }

        // Sorting functionality
        if (this.options.sortable) {
            const headers = this.table.querySelectorAll('th[data-sortable="true"]');
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    const column = header.dataset.column;
                    this.sort(column);
                });
            });
        }

        // Per page selector
        if (this.options.paginated) {
            const perPageSelect = document.querySelector(`select[data-table="${this.tableId}"][data-action="per-page"]`);
            if (perPageSelect) {
                perPageSelect.addEventListener('change', (e) => {
                    this.options.perPage = parseInt(e.target.value);
                    this.currentPage = 1;
                    this.render();
                });
            }
        }

        // Export functionality
        this.setupExportListeners();
    }

    setupExportListeners() {
        const exportButtons = document.querySelectorAll(`[data-table="${this.tableId}"][data-action^="export"], [data-table="${this.tableId}"][data-action="print"]`);
        
        exportButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const action = button.dataset.action;
                
                switch (action) {
                    case 'export-csv':
                        this.exportCSV();
                        break;
                    case 'export-excel':
                        this.exportExcel();
                        break;
                    case 'print':
                        this.print();
                        break;
                }
            });
        });
    }

    applyFilters() {
        this.filteredData = this.originalData.filter(row => {
            if (!this.searchTerm) return true;
            
            return row.data.some(cell => 
                cell.toLowerCase().includes(this.searchTerm)
            );
        });
        
        this.currentPage = 1;
        this.render();
    }

    sort(column) {
        const columnIndex = this.getColumnIndex(column);
        if (columnIndex === -1) return;

        // Toggle sort direction
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }

        // Sort the filtered data
        this.filteredData.sort((a, b) => {
            const aValue = a.data[columnIndex];
            const bValue = b.data[columnIndex];
            
            // Try to parse as numbers
            const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
            const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
            
            let comparison = 0;
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                // Numeric comparison
                comparison = aNum - bNum;
            } else {
                // String comparison
                comparison = aValue.localeCompare(bValue);
            }
            
            return this.sortDirection === 'asc' ? comparison : -comparison;
        });

        this.updateSortIcons(column);
        this.render();
    }

    getColumnIndex(column) {
        const headers = this.table.querySelectorAll('th[data-column]');
        for (let i = 0; i < headers.length; i++) {
            if (headers[i].dataset.column === column) {
                return i;
            }
        }
        return -1;
    }

    updateSortIcons(activeColumn) {
        const headers = this.table.querySelectorAll('th[data-sortable="true"]');
        
        headers.forEach(header => {
            const icon = header.querySelector('.sort-icon');
            if (!icon) return;
            
            if (header.dataset.column === activeColumn) {
                icon.className = this.sortDirection === 'asc' ? 'fas fa-sort-up ms-1 sort-icon' : 'fas fa-sort-down ms-1 sort-icon';
            } else {
                icon.className = 'fas fa-sort ms-1 sort-icon';
            }
        });
    }

    render() {
        if (!this.options.paginated) {
            this.renderAllRows();
        } else {
            this.renderPaginatedRows();
            this.renderPagination();
            this.renderInfo();
        }
    }

    renderAllRows() {
        const tbody = this.table.querySelector('tbody');
        tbody.innerHTML = '';
        
        if (this.filteredData.length === 0) {
            this.renderEmptyState(tbody);
            return;
        }
        
        this.filteredData.forEach(row => {
            tbody.appendChild(row.element);
        });
    }

    renderPaginatedRows() {
        const tbody = this.table.querySelector('tbody');
        tbody.innerHTML = '';
        
        if (this.filteredData.length === 0) {
            this.renderEmptyState(tbody);
            return;
        }
        
        const startIndex = (this.currentPage - 1) * this.options.perPage;
        const endIndex = startIndex + this.options.perPage;
        const pageData = this.filteredData.slice(startIndex, endIndex);
        
        pageData.forEach(row => {
            tbody.appendChild(row.element);
        });
    }

    renderEmptyState(tbody) {
        const columnCount = this.table.querySelectorAll('thead th').length;
        const emptyRow = document.createElement('tr');
        const emptyCell = document.createElement('td');
        
        emptyCell.colSpan = columnCount;
        emptyCell.className = 'text-center py-5';
        emptyCell.innerHTML = `
            <div class="empty-state py-3">
                <div class="empty-state-icon">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <div class="empty-state-title">
                    No matching records found
                </div>
                <div class="empty-state-description">
                    Try adjusting your search criteria
                </div>
            </div>
        `;
        
        emptyRow.appendChild(emptyCell);
        tbody.appendChild(emptyRow);
    }

    renderPagination() {
        const paginationContainer = document.getElementById(`${this.tableId}-pagination`);
        if (!paginationContainer) return;
        
        const totalPages = Math.ceil(this.filteredData.length / this.options.perPage);
        
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.currentPage - 1}">Previous</a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, this.currentPage + 2);
        
        if (startPage > 1) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }
        
        // Next button
        paginationHTML += `
            <li class="page-item ${this.currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${this.currentPage + 1}">Next</a>
            </li>
        `;
        
        paginationContainer.innerHTML = paginationHTML;
        
        // Add click listeners to pagination links
        paginationContainer.querySelectorAll('a[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.dataset.page);
                if (page >= 1 && page <= totalPages && page !== this.currentPage) {
                    this.currentPage = page;
                    this.render();
                }
            });
        });
    }

    renderInfo() {
        const infoContainer = document.getElementById(`${this.tableId}-info`);
        if (!infoContainer) return;
        
        const startIndex = (this.currentPage - 1) * this.options.perPage + 1;
        const endIndex = Math.min(this.currentPage * this.options.perPage, this.filteredData.length);
        const total = this.filteredData.length;
        
        if (total === 0) {
            infoContainer.textContent = 'No entries to show';
        } else {
            infoContainer.textContent = `Showing ${startIndex} to ${endIndex} of ${total} entries`;
            
            if (this.searchTerm && total !== this.originalData.length) {
                infoContainer.textContent += ` (filtered from ${this.originalData.length} total entries)`;
            }
        }
    }

    exportCSV() {
        const headers = Array.from(this.table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        const rows = this.filteredData.map(row => row.data);
        
        let csvContent = headers.join(',') + '\n';
        csvContent += rows.map(row => row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(',')).join('\n');
        
        this.downloadFile(csvContent, 'table-export.csv', 'text/csv');
    }

    exportExcel() {
        // For now, export as CSV with .xlsx extension
        // In a real implementation, you'd use a library like SheetJS
        this.exportCSV();
        showToast('Excel export feature coming soon. CSV exported instead.', 'info');
    }

    print() {
        const printWindow = window.open('', '_blank');
        const tableHTML = this.table.outerHTML;
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Table Print</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    @media print {
                        .btn, .dropdown { display: none !important; }
                    }
                </style>
            </head>
            <body>
                ${tableHTML}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    }

    downloadFile(content, filename, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        URL.revokeObjectURL(url);
    }

    // Public methods for external control
    search(term) {
        this.searchTerm = term.toLowerCase();
        this.applyFilters();
    }

    goToPage(page) {
        const totalPages = Math.ceil(this.filteredData.length / this.options.perPage);
        if (page >= 1 && page <= totalPages) {
            this.currentPage = page;
            this.render();
        }
    }

    refresh() {
        this.extractData();
        this.applyFilters();
    }
}

// Auto-initialize data tables
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        if (!table.dataset.initialized) {
            new DataTableManager(table.id);
            table.dataset.initialized = 'true';
        }
    });
});

// Export for global use
window.DataTableManager = DataTableManager;
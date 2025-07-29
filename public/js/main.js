document.addEventListener('DOMContentLoaded', function() {
    const selectAllMain = document.getElementById('select-all');
    const selectAllHeader = document.getElementById('header-select-all');
    const weaponCheckboxes = document.querySelectorAll('.weapon-checkbox');
    const bulkExportBtn = document.getElementById('bulk-export-btn');
    const selectionCount = document.getElementById('selection-count');
    const bulkExportForm = document.getElementById('bulk-export-form');
    const selectedWeaponsContainer = document.getElementById('selected-weapons-container');

    function updateHiddenInputs() {
        // Clear existing hidden inputs
        selectedWeaponsContainer.innerHTML = '';
        
        // Add hidden inputs for selected weapons
        const checkedBoxes = document.querySelectorAll('.weapon-checkbox:checked');
        checkedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'weapon_ids[]';
            hiddenInput.value = checkbox.value;
            selectedWeaponsContainer.appendChild(hiddenInput);
        });
    }

    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('.weapon-checkbox:checked');
        const totalBoxes = weaponCheckboxes.length;
        const checkedCount = checkedBoxes.length;

        // Update selection count
        selectionCount.textContent = `${checkedCount} selected`;

        // Update bulk export button state
        bulkExportBtn.disabled = checkedCount === 0;

        // Update select all checkboxes
        const allChecked = checkedCount === totalBoxes && totalBoxes > 0;
        const someChecked = checkedCount > 0 && checkedCount < totalBoxes;

        selectAllMain.checked = allChecked;
        selectAllHeader.checked = allChecked;
        selectAllMain.indeterminate = someChecked;
        selectAllHeader.indeterminate = someChecked;

        // Update hidden inputs for form submission
        updateHiddenInputs();
    }

    function toggleAllCheckboxes(checked) {
        weaponCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateSelectionState();
    }

    // Event listeners for select all checkboxes
    if (selectAllMain) {
        selectAllMain.addEventListener('change', function() {
            toggleAllCheckboxes(this.checked);
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            toggleAllCheckboxes(this.checked);
        });
    }

    // Event listeners for individual checkboxes
    weaponCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionState);
    });

    // Form submission handler
    if (bulkExportForm) {
        bulkExportForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.weapon-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one weapon to export.');
                return;
            }

            // Show loading state
            bulkExportBtn.disabled = true;
            bulkExportBtn.innerHTML = '⏳ Generating PDFs...';
            
            // Create a confirmation message with selected weapon names
            const selectedNames = Array.from(checkedBoxes)
                .map(cb => cb.getAttribute('data-weapon-name'))
                .slice(0, 5); // Show first 5 names
            
            let confirmMessage = `Export ${checkedBoxes.length} weapon(s) to PDF?\n\n`;
            confirmMessage += selectedNames.join('\n');
            if (checkedBoxes.length > 5) {
                confirmMessage += `\n... and ${checkedBoxes.length - 5} more`;
            }
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                // Reset button state
                bulkExportBtn.disabled = checkedBoxes.length === 0;
                bulkExportBtn.innerHTML = '📄 Export Selected PDFs';
                return;
            }
            
            // Reset button after delay (in case of errors or slow response)
            setTimeout(() => {
                bulkExportBtn.disabled = checkedBoxes.length === 0;
                bulkExportBtn.innerHTML = '📄 Export Selected PDFs';
            }, 10000); // 10 seconds timeout
        });
    }

    // Initial state update
    updateSelectionState();

    // Handle row clicks to toggle checkbox (optional enhancement)
    const tableRows = document.querySelectorAll('.table-data tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger on clicks to links, buttons, or the checkbox itself
            if (e.target.tagName === 'A' || 
                e.target.tagName === 'BUTTON' || 
                e.target.type === 'checkbox' ||
                e.target.closest('a') || 
                e.target.closest('button')) {
                return;
            }
            
            const checkbox = row.querySelector('.weapon-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updateSelectionState();
            }
        });
    });
});
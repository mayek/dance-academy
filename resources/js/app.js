//
document.addEventListener('DOMContentLoaded', function () {
    initStudentSearch();
    initPassTypeAutoAmount();
});

function initStudentSearch() {
    const searchInput = document.getElementById('student_search');
    if (!searchInput) return;

    const students = window.studentSearchData || [];
    const hiddenInput = document.getElementById('student_id');
    const dropdown = document.getElementById('student_dropdown');
    const clearBtn = document.getElementById('student_clear');

    function renderDropdown(query) {
        const q = query.toLowerCase().trim();
        if (!q) {
            dropdown.classList.add('hidden');
            return;
        }

        const matches = students.filter(s =>
            (s.full_name && s.full_name.toLowerCase().includes(q)) ||
            (s.email && s.email.toLowerCase().includes(q)) ||
            (s.pesel && s.pesel.includes(q)) ||
            (s.phone && s.phone.replace(/\s/g, '').includes(q.replace(/\s/g, ''))) ||
            (s.parent_phone && s.parent_phone.replace(/\s/g, '').includes(q.replace(/\s/g, '')))
        );

        if (matches.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No students found</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = matches.map(s => {
            const parts = [];
            if (s.pesel) parts.push('PESEL: ' + s.pesel);
            if (s.phone) parts.push('Tel: ' + s.phone);
            if (s.email) parts.push(s.email);
            const meta = parts.join(' &middot; ');
            return `<div class="px-4 py-2 cursor-pointer hover:bg-emerald-50 border-b border-gray-100 last:border-0" data-id="${s.id}" data-name="${s.full_name}">
                <div class="text-sm font-medium text-gray-900">${s.full_name}</div>
                <div class="text-xs text-gray-500">${meta}</div>
            </div>`;
        }).join('');

        dropdown.classList.remove('hidden');

        dropdown.querySelectorAll('[data-id]').forEach(el => {
            el.addEventListener('mousedown', function (e) {
                e.preventDefault();
                hiddenInput.value = this.dataset.id;
                searchInput.value = this.dataset.name;
                dropdown.classList.add('hidden');
                clearBtn.classList.remove('hidden');
            });
        });
    }

    searchInput.addEventListener('input', function () {
        hiddenInput.value = '';
        clearBtn.classList.add('hidden');
        renderDropdown(this.value);
    });

    searchInput.addEventListener('focus', function () {
        if (this.value) renderDropdown(this.value);
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        hiddenInput.value = '';
        dropdown.classList.add('hidden');
        clearBtn.classList.add('hidden');
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

function initPassTypeAutoAmount() {
    const passType = document.getElementById('pass_type');
    const amount = document.getElementById('amount');
    if (!passType || !amount) return;

    passType.addEventListener('change', function () {
        amount.value = this.value === 'monthly' ? '150.00' : '25.00';
    });
}

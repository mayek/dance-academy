import { Chart, BarController, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initStudentSearch();
    initPassTypeAutoAmount();
    initRevenueChart();
});

function initMobileMenu() {
    const button = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');
    if (!button || !menu) return;

    button.addEventListener('click', function () {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        menu.classList.toggle('hidden', expanded);
        button.setAttribute('aria-expanded', String(!expanded));
    });
}

function initStudentSearch() {
    const searchInput = document.getElementById('student_search');
    const hiddenInput = document.getElementById('student_id');
    const dropdown = document.getElementById('student_dropdown');
    const clearBtn = document.getElementById('student_clear');
    const groupSelect = document.querySelector('[data-group-filter]');

    if (!searchInput || !hiddenInput || !dropdown || !clearBtn) return;

    const students = window.studentSearchData || [];

    function filterGroups(studentId) {
        if (!groupSelect) return;
        const student = students.find(s => String(s.id) === String(studentId));
        const allowed = student ? (student.group_ids || []).map(String) : null;

        Array.from(groupSelect.options).forEach(o => {
            if (!o.value) return;
            o.hidden = allowed !== null && !allowed.includes(o.value);
        });

        if (allowed && groupSelect.value && !allowed.includes(String(groupSelect.value))) {
            groupSelect.value = '';
        }
    }

    window.filterStudentGroups = filterGroups;

    function renderDropdown(query) {
        const q = query.toLowerCase().trim();
        if (!q) {
            dropdown.classList.add('hidden');
            return;
        }

        const matches = students.filter(s =>
            (s.full_name && s.full_name.toLowerCase().includes(q)) ||
            (s.email && s.email.toLowerCase().includes(q)) ||
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
                filterGroups(this.dataset.id);
            });
        });
    }

    searchInput.addEventListener('input', function () {
        hiddenInput.value = '';
        clearBtn.classList.add('hidden');
        filterGroups(null);
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
        filterGroups(null);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    if (hiddenInput.value) {
        filterGroups(hiddenInput.value);
    }
}

function initPassTypeAutoAmount() {
    const passType = document.getElementById('pass_type');
    const amount = document.getElementById('amount');
    if (!passType || !amount) return;

    passType.addEventListener('change', function () {
        amount.value = this.value === 'monthly' ? '150.00' : '25.00';
    });
}

function initRevenueChart() {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels);
    const data = JSON.parse(canvas.dataset.revenue);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: canvas.dataset.label || '',
                data: data,
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.parsed.y.toFixed(2) + ' z\u0142';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value.toFixed(2) + ' z\u0142';
                        }
                    }
                }
            }
        }
    });
}

import { createIcons, ArrowUpRight } from 'lucide';

export default function initializeWorkspace() {
    if (!document.body.classList.contains('workspace-page')) return;

    const select = selector => document.querySelector(selector);
    const sampleRequests = [
        { resident: 'Maria Santos', reference: 'CERT-2026-048', document: 'Barangay Clearance', status: 'Issued', date: 'Sep 18, 2026' },
        { resident: 'Juan Dela Cruz', reference: 'CERT-2026-049', document: 'Certificate of Residency', status: 'Pending', date: 'Sep 18, 2026' },
        { resident: 'Ana Garcia', reference: 'CERT-2026-050', document: 'Certificate of Indigency', status: 'Processing', date: 'Sep 18, 2026' },
        { resident: 'Pedro Mendoza', reference: 'CERT-2026-051', document: 'Barangay Clearance', status: 'Pending', date: 'Sep 17, 2026' },
        { resident: 'Rosa Villanueva', reference: 'CERT-2026-052', document: 'Certificate of Residency', status: 'Issued', date: 'Sep 17, 2026' },
        { resident: 'Carlo Reyes', reference: 'CERT-2026-053', document: 'Barangay Clearance', status: 'Pending', date: 'Sep 17, 2026' },
        { resident: 'Liza Ramos', reference: 'CERT-2026-054', document: 'Certificate of Indigency', status: 'Pending', date: 'Sep 16, 2026' },
        { resident: 'Miguel Flores', reference: 'CERT-2026-055', document: 'Certificate of Residency', status: 'Pending', date: 'Sep 16, 2026' },
        { resident: 'Elena Torres', reference: 'CERT-2026-056', document: 'Barangay Clearance', status: 'Pending', date: 'Sep 16, 2026' },
    ];
    const datasets = {
        residents: {
            title: 'Residents', heading: 'Resident directory', description: 'Resident information, organized for everyday service.',
            columns: [['name', 'Resident'], ['reference', 'Resident ID'], ['purok', 'Purok'], ['gender', 'Gender'], ['status', 'Status']],
            rows: [
                { name: 'Maria Santos', reference: 'RES-001', purok: 'Purok 1', gender: 'Female', status: 'Active' },
                { name: 'Juan Dela Cruz', reference: 'RES-002', purok: 'Purok 2', gender: 'Male', status: 'Active' },
                { name: 'Ana Garcia', reference: 'RES-003', purok: 'Purok 4', gender: 'Female', status: 'Active' },
                { name: 'Pedro Mendoza', reference: 'RES-004', purok: 'Purok 5', gender: 'Male', status: 'Active' },
                { name: 'Rosa Villanueva', reference: 'RES-005', purok: 'Purok 6', gender: 'Female', status: 'Active' },
                { name: 'Carlo Reyes', reference: 'RES-006', purok: 'Purok 3', gender: 'Male', status: 'Active' },
            ],
        },
        households: {
            title: 'Households', heading: 'Household directory', description: 'Connected households across the six puroks.',
            columns: [['head', 'Household head'], ['reference', 'Household ID'], ['purok', 'Purok'], ['members', 'Members'], ['status', 'Status']],
            rows: [
                { head: 'Maria Santos', reference: 'HH-0321', purok: 'Purok 1', members: '4', status: 'Active' },
                { head: 'Juan Dela Cruz', reference: 'HH-0322', purok: 'Purok 2', members: '5', status: 'Active' },
                { head: 'Ana Garcia', reference: 'HH-0323', purok: 'Purok 4', members: '3', status: 'Active' },
                { head: 'Pedro Mendoza', reference: 'HH-0324', purok: 'Purok 5', members: '4', status: 'Active' },
                { head: 'Rosa Villanueva', reference: 'HH-0325', purok: 'Purok 6', members: '2', status: 'Active' },
                { head: 'Carlo Reyes', reference: 'HH-0326', purok: 'Purok 3', members: '5', status: 'Active' },
            ],
        },
        certificates: {
            title: 'Certificates', heading: 'Certificate requests', description: 'Review sample document requests and their current status.',
            columns: [['resident', 'Resident'], ['reference', 'Reference'], ['document', 'Document'], ['status', 'Status'], ['date', 'Date']], rows: sampleRequests,
        },
        blotter: {
            title: 'Blotter records', heading: 'Case register', description: 'Track sample community concerns and scheduled hearings.',
            columns: [['reference', 'Case reference'], ['concern', 'Concern'], ['complainant', 'Complainant'], ['status', 'Status'], ['hearing', 'Hearing']],
            rows: [
                { reference: 'BL-2026-014', concern: 'Neighborhood dispute', complainant: 'Sample resident A', status: 'Scheduled', hearing: 'Sep 21, 2026, 9:00 AM' },
                { reference: 'BL-2026-015', concern: 'Property boundary concern', complainant: 'Sample resident B', status: 'Scheduled', hearing: 'Sep 22, 2026, 10:00 AM' },
                { reference: 'BL-2026-016', concern: 'Noise complaint', complainant: 'Sample resident C', status: 'Open', hearing: 'Not yet scheduled' },
                { reference: 'BL-2026-017', concern: 'Community concern', complainant: 'Sample resident D', status: 'Open', hearing: 'Not yet scheduled' },
            ],
        },
        officials: {
            title: 'Barangay officials', heading: 'Officials directory', description: 'Illustrative positions and committee assignments.',
            columns: [['name', 'Official'], ['position', 'Position'], ['committee', 'Committee'], ['status', 'Status']],
            rows: [
                { name: 'Sample official A', position: 'Barangay Captain', committee: 'General administration', status: 'Active' },
                { name: 'Sample official B', position: 'Barangay Secretary', committee: 'Records & documentation', status: 'Active' },
                { name: 'Sample official C', position: 'Barangay Treasurer', committee: 'Finance', status: 'Active' },
                { name: 'Sample official D', position: 'Barangay Kagawad', committee: 'Health & sanitation', status: 'Active' },
                { name: 'Sample official E', position: 'Barangay Kagawad', committee: 'Peace & order', status: 'Active' },
                { name: 'Sample official F', position: 'SK Chairperson', committee: 'Youth development', status: 'Active' },
            ],
        },
    };

    function element(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function badge(status) {
        return element('span', `status-badge status-${status.toLowerCase()}`, status);
    }

    function detailsButton(record, columns) {
        const button = element('button', 'table-details');
        button.type = 'button';
        button.title = `View ${record.reference || record.name}`;
        button.setAttribute('aria-label', button.title);
        const icon = element('i');
        icon.dataset.lucide = 'arrow-up-right';
        icon.setAttribute('aria-hidden', 'true');
        button.append(icon);
        button.addEventListener('click', () => showDetails(record, columns));
        return button;
    }

    const detailDialog = select('#workspace-dialog');
    const residentDialog = select('#resident-dialog');

    function openDialog(dialog) {
        dialog.showModal();
        document.body.classList.add('dialog-open');
    }

    function showDetails(record, columns) {
        select('#workspace-dialog-title').textContent = record.resident || record.name || record.head || record.reference;
        const content = select('#workspace-dialog-content');
        content.replaceChildren();
        const list = element('dl', 'detail-grid');
        for (const [key, label] of columns) {
            const row = element('div');
            row.append(element('dt', '', label), element('dd', '', record[key]));
            list.append(row);
        }
        content.append(list, element('p', 'form-note', 'Sample record in the demo workspace.'));
        openDialog(detailDialog);
    }

    for (const [dialog, selector] of [[detailDialog, '[data-close-workspace-dialog]'], [residentDialog, '[data-close-resident-dialog]']]) {
        document.querySelectorAll(selector).forEach(button => button.addEventListener('click', () => dialog.close()));
        dialog.addEventListener('close', () => document.body.classList.remove('dialog-open'));
        dialog.addEventListener('click', event => {
            const bounds = dialog.getBoundingClientRect();
            if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
        });
    }

    function renderRecent() {
        const query = select('#request-search').value.toLowerCase().trim();
        const status = select('#request-status').value;
        const rows = sampleRequests.slice(0, 5).filter(row => Object.values(row).join(' ').toLowerCase().includes(query) && (status === 'all' || row.status === status));
        const body = select('#recent-requests');
        body.replaceChildren();
        for (const row of rows) {
            const tr = element('tr');
            const personCell = element('td');
            const person = element('div', 'table-person');
            const name = element('span');
            name.append(element('strong', '', row.resident), element('small', '', row.reference));
            person.append(element('span', 'table-avatar', row.resident.split(' ').map(part => part[0]).slice(0, 2).join('')), name);
            personCell.append(person);
            const statusCell = element('td');
            statusCell.append(badge(row.status));
            const action = element('td');
            action.append(detailsButton(row, datasets.certificates.columns));
            tr.append(personCell, element('td', '', row.document), statusCell, element('td', '', row.date.replace(', 2026', '')), action);
            body.append(tr);
        }
        if (!rows.length) {
            const tr = element('tr');
            const cell = element('td', '', 'No matching requests. Try another name or status.');
            cell.colSpan = 5;
            tr.append(cell);
            body.append(tr);
        }
        select('#request-summary').textContent = `Showing ${rows.length} of 5 recent requests`;
        createIcons({ icons: { ArrowUpRight } });
    }
    select('#request-search').addEventListener('input', renderRecent);
    select('#request-status').addEventListener('change', renderRecent);

    let currentSection = 'overview';
    function filteredRecords() {
        const query = select('#records-search').value.trim().toLowerCase();
        return datasets[currentSection].rows.filter(row => Object.values(row).join(' ').toLowerCase().includes(query));
    }

    function renderRecords() {
        const dataset = datasets[currentSection];
        const rows = filteredRecords();
        const header = element('tr');
        for (const [, label] of dataset.columns) header.append(element('th', '', label));
        const actionHeader = element('th');
        actionHeader.append(element('span', 'sr-only', 'Details'));
        header.append(actionHeader);
        select('#records-head').replaceChildren(header);
        const body = select('#records-body');
        body.replaceChildren();
        for (const record of rows) {
            const row = element('tr');
            for (const [key] of dataset.columns) {
                const cell = element('td');
                if (key === 'status') cell.append(badge(record[key]));
                else cell.textContent = record[key];
                row.append(cell);
            }
            const action = element('td');
            action.append(detailsButton(record, dataset.columns));
            row.append(action);
            body.append(row);
        }
        select('#records-empty').hidden = rows.length > 0;
        select('#records-count').textContent = `${rows.length} of ${dataset.rows.length} records`;
        createIcons({ icons: { ArrowUpRight } });
    }
    select('#records-search').addEventListener('input', renderRecords);

    const sidebar = select('.workspace-sidebar');
    const sidebarToggle = select('.sidebar-toggle');
    function setSidebar(open) {
        sidebar.classList.toggle('open', open);
        select('.sidebar-backdrop').hidden = !open;
        document.body.classList.toggle('sidebar-open', open);
        sidebarToggle.setAttribute('aria-expanded', String(open));
        select('.workspace-shell').inert = open;
        if (open) sidebar.querySelector('a').focus();
    }
    sidebarToggle.addEventListener('click', () => setSidebar(true));
    select('.sidebar-backdrop').addEventListener('click', () => { setSidebar(false); sidebarToggle.focus(); });
    document.addEventListener('keydown', event => {
        if (!sidebar.classList.contains('open')) return;
        if (event.key === 'Escape') { setSidebar(false); sidebarToggle.focus(); }
        if (event.key === 'Tab') {
            const focusable = [...sidebar.querySelectorAll('a, button')];
            const first = focusable[0];
            const last = focusable.at(-1);
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    window.matchMedia('(min-width: 961px)').addEventListener('change', event => {
        if (event.matches) setSidebar(false);
    });
    sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setSidebar(false)));

    function showSection() {
        const requested = location.hash.slice(1);
        currentSection = Object.hasOwn(datasets, requested) || requested === 'reports' ? requested : 'overview';
        const overview = currentSection === 'overview';
        const reports = currentSection === 'reports';
        select('#overview-view').hidden = !overview;
        select('#records-view').hidden = overview || reports;
        select('#reports-view').hidden = !reports;
        const title = overview ? 'Dashboard' : reports ? 'Reports & analytics' : datasets[currentSection].title;
        select('#workspace-title').textContent = title;
        select('#breadcrumb-current').textContent = overview ? 'Overview' : title;
        select('#workspace-subtitle').textContent = overview ? "Welcome back. Here's what's happening in your barangay." : reports ? 'Community information that helps you see the bigger picture.' : datasets[currentSection].description;
        document.title = `${title} | Barangay Information System`;
        sidebar.querySelectorAll('[data-section]').forEach(link => {
            const selected = link.dataset.section === currentSection;
            link.classList.toggle('selected', selected);
            if (selected) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });
        if (!overview && !reports) {
            select('#records-heading').textContent = datasets[currentSection].heading;
            select('#records-description').textContent = datasets[currentSection].description;
            select('#records-search').value = '';
            renderRecords();
        }
        setSidebar(false);
    }
    window.addEventListener('hashchange', showSection);

    function renderChart() {
        const recent = select('#chart-period').value === 'recent';
        const months = recent ? ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'] : ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];
        const values = recent ? [30, 36, 41, 39, 48, 48] : [26, 31, 36, 38, 37, 44];
        const bars = select('#chart-bars');
        bars.replaceChildren();
        months.forEach((month, index) => {
            const column = element('div', 'chart-column');
            const bar = element('div', 'chart-bar');
            bar.style.height = `calc((100% - 23px) * ${values[index] / 60})`;
            bar.tabIndex = 0;
            bar.setAttribute('aria-label', `${month}: ${values[index]} certificates`);
            bar.title = `${month}: ${values[index]} certificates`;
            bar.append(element('span', 'chart-value', values[index]));
            column.append(bar, element('span', 'chart-month', month));
            bars.append(column);
        });
        select('#chart-total').textContent = values.reduce((sum, value) => sum + value, 0);
        select('#chart-trend').hidden = !recent;
        select('#certificate-chart').setAttribute('aria-label', `Monthly certificates issued: ${months.map((month, index) => `${month} ${values[index]}`).join(', ')}.`);
    }
    select('#chart-period').addEventListener('change', renderChart);

    const puroks = [236, 214, 208, 196, 202, 192];
    puroks.forEach((count, index) => {
        const row = element('div', 'purok-row');
        const track = element('div', 'purok-track');
        const fill = element('div', 'purok-fill');
        fill.style.width = `${count / 250 * 100}%`;
        track.append(fill);
        row.append(element('span', '', `Purok ${index + 1}`), track, element('strong', '', count));
        select('#purok-report').append(row);
    });

    function downloadCsv(name, columns, rows) {
        const quote = value => {
            let text = String(value ?? '');
            if (/^[=+@\-\t\r]/.test(text)) text = `'${text}`;
            return `"${text.replaceAll('"', '""')}"`;
        };
        const csv = [columns.map(([, label]) => quote(label)).join(','), ...rows.map(row => columns.map(([key]) => quote(row[key])).join(','))].join('\r\n');
        const url = URL.createObjectURL(new Blob(['\uFEFF', csv], { type: 'text/csv;charset=utf-8;' }));
        const link = element('a');
        link.href = url;
        link.download = `barangay-demo-${name}.csv`;
        document.body.append(link);
        link.click();
        link.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }
    select('#export-records').addEventListener('click', () => downloadCsv(currentSection, datasets[currentSection].columns, filteredRecords()));
    select('#export-report').addEventListener('click', () => downloadCsv('community-summary', [['purok', 'Purok'], ['residents', 'Residents']], puroks.map((residents, index) => ({ purok: `Purok ${index + 1}`, residents }))));

    let toastTimer;
    function notify(message) {
        clearTimeout(toastTimer);
        select('#workspace-toast').textContent = message;
        select('#workspace-toast').hidden = false;
        toastTimer = setTimeout(() => { select('#workspace-toast').hidden = true; }, 4500);
    }
    document.querySelectorAll('[data-new-resident]').forEach(button => button.addEventListener('click', () => openDialog(residentDialog)));
    select('#resident-form').addEventListener('submit', event => {
        event.preventDefault();
        const form = event.currentTarget;
        for (const field of [form.elements.firstName, form.elements.lastName]) {
            field.setCustomValidity(field.value.trim() ? '' : 'Enter a name.');
        }
        if (!form.reportValidity()) return;
        const data = new FormData(form);
        datasets.residents.rows.unshift({ name: `${data.get('firstName').trim()} ${data.get('lastName').trim()}`, reference: `DEMO-${String(datasets.residents.rows.length + 1).padStart(3, '0')}`, purok: data.get('purok'), gender: data.get('gender'), status: 'Active' });
        residentDialog.close();
        form.reset();
        location.hash = 'residents';
        showSection();
        notify('Demo resident added. This entry lasts until the page is reloaded.');
    });
    select('#resident-form').addEventListener('input', event => {
        if (event.target instanceof HTMLInputElement) event.target.setCustomValidity('');
    });
    select('[data-workspace-detail="notifications"]').addEventListener('click', () => {
        select('#workspace-dialog-title').textContent = 'Notifications';
        const content = select('#workspace-dialog-content');
        content.replaceChildren();
        const list = element('dl', 'detail-grid');
        for (const [title, description] of [['Certificate requests', '6 sample requests awaiting review'], ['Upcoming hearings', '2 sample hearings scheduled'], ['Workspace', 'You are viewing demo information']]) {
            const row = element('div');
            row.append(element('dt', '', title), element('dd', '', description));
            list.append(row);
        }
        content.append(list);
        openDialog(detailDialog);
    });

    renderRecent();
    renderChart();
    showSection();
}

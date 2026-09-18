import { createIcons, ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight, BadgeCheck, Bell, Building2, CalendarClock, CalendarDays, ChartNoAxesCombined, Check, ChevronDown, ChevronRight, ClipboardList, Download, ExternalLink, FileCheck2, Files, Flag, Globe, HandHeart, HeartHandshake, HeartPulse, House, Info, Landmark, LayoutDashboard, LogOut, MapPin, Megaphone, Menu, MessagesSquare, NotebookPen, PanelLeft, Plus, Search, SearchX, Sprout, TrendingUp, UserRound, UserRoundPlus, UsersRound, X } from 'lucide';
import initializeWorkspace from './workspace';

createIcons({ icons: { ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight, BadgeCheck, Bell, Building2, CalendarClock, CalendarDays, ChartNoAxesCombined, Check, ChevronDown, ChevronRight, ClipboardList, Download, ExternalLink, FileCheck2, Files, Flag, Globe, HandHeart, HeartHandshake, HeartPulse, House, Info, Landmark, LayoutDashboard, LogOut, MapPin, Megaphone, Menu, MessagesSquare, NotebookPen, PanelLeft, Plus, Search, SearchX, Sprout, TrendingUp, UserRound, UserRoundPlus, UsersRound, X } });

initializeWorkspace();

const menuToggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#primary-navigation');

function closeMenu() {
    if (!navigation || !menuToggle) return;
    navigation.classList.remove('open');
    menuToggle.setAttribute('aria-expanded', 'false');
    menuToggle.setAttribute('aria-label', 'Open navigation');
    menuToggle.title = 'Open navigation';
}

menuToggle?.addEventListener('click', () => {
    const open = navigation.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    menuToggle.title = open ? 'Close navigation' : 'Open navigation';
});

navigation?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && navigation?.classList.contains('open')) {
        closeMenu();
        menuToggle.focus();
    }
});
document.addEventListener('click', event => {
    if (!event.target.closest('.site-header')) closeMenu();
});
window.matchMedia('(min-width: 761px)').addEventListener('change', closeMenu);

const dialog = document.querySelector('#detail-dialog');
document.querySelectorAll('[data-detail-title]').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelector('#detail-title').textContent = button.dataset.detailTitle;
        document.querySelector('#detail-text').textContent = button.dataset.detailText;
        document.querySelector('#detail-label').textContent = button.dataset.detailLabel;
        dialog.showModal();
        document.body.classList.add('dialog-open');
    });
});
document.querySelectorAll('.dialog-close').forEach(button => button.addEventListener('click', () => dialog.close()));
dialog?.addEventListener('close', () => document.body.classList.remove('dialog-open'));
dialog?.addEventListener('click', event => {
    const bounds = dialog.getBoundingClientRect();
    if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
});

document.querySelectorAll('[data-filter]').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('[data-filter]').forEach(filter => {
            const selected = filter === button;
            filter.classList.toggle('active', selected);
            filter.setAttribute('aria-pressed', String(selected));
        });
        document.querySelectorAll('[data-notice-type]').forEach(notice => {
            notice.hidden = button.dataset.filter !== 'all' && notice.dataset.noticeType !== button.dataset.filter;
        });
    });
});

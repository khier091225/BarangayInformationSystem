import { createIcons, ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight, BadgeCheck, Bell, Building2, CalendarClock, CalendarDays, ChartNoAxesCombined, Check, ChevronDown, ChevronRight, ClipboardList, Download, ExternalLink, FileCheck2, Files, Flag, Globe, HandHeart, HeartHandshake, HeartPulse, House, Info, Landmark, LayoutDashboard, LogOut, MapPin, Megaphone, Menu, MessagesSquare, NotebookPen, PanelLeft, Plus, Search, SearchX, Sprout, TrendingUp, UserRound, UserRoundPlus, UsersRound, X } from 'lucide';
import initializeWorkspace from './workspace';

createIcons({ icons: { ArrowLeft, ArrowRight, ArrowUp, ArrowUpRight, BadgeCheck, Bell, Building2, CalendarClock, CalendarDays, ChartNoAxesCombined, Check, ChevronDown, ChevronRight, ClipboardList, Download, ExternalLink, FileCheck2, Files, Flag, Globe, HandHeart, HeartHandshake, HeartPulse, House, Info, Landmark, LayoutDashboard, LogOut, MapPin, Megaphone, Menu, MessagesSquare, NotebookPen, PanelLeft, Plus, Search, SearchX, Sprout, TrendingUp, UserRound, UserRoundPlus, UsersRound, X } });

initializeWorkspace();

const menuToggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#primary-navigation');
const desktopNavigation = window.matchMedia('(min-width: 960px)');

function closeMenu(focusTarget = null) {
    if (!navigation || !menuToggle) return;
    if (focusTarget) {
        focusTarget.focus({ preventScroll: true });
    } else if (!desktopNavigation.matches && navigation.contains(document.activeElement)) {
        menuToggle.focus({ preventScroll: true });
    }
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

navigation?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
    if (!navigation.classList.contains('open')) return;
    const target = link.hash ? document.getElementById(link.hash.slice(1)) : null;
    closeMenu(target);
}));
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && navigation?.classList.contains('open')) {
        closeMenu(menuToggle);
    }
});
document.addEventListener('click', event => {
    if (!event.target.closest('.site-header')) closeMenu();
});
let lastFocusedElement = document.activeElement;
document.addEventListener('focusin', event => { lastFocusedElement = event.target; });
desktopNavigation.addEventListener('change', () => {
    const focusWasInNavigation = navigation?.contains(lastFocusedElement);
    const focusWasOnToggle = lastFocusedElement === menuToggle;
    closeMenu();
    if (!desktopNavigation.matches && focusWasInNavigation) {
        menuToggle?.focus({ preventScroll: true });
    }
    if (desktopNavigation.matches && focusWasOnToggle) {
        navigation?.querySelector('a')?.focus({ preventScroll: true });
    }
});

const demoEntryForm = document.querySelector('[data-demo-entry]');
const demoEntrySubmit = demoEntryForm?.querySelector('[type="submit"]');
let enteringDemo = false;

demoEntryForm?.addEventListener('submit', event => {
    if (enteringDemo) {
        event.preventDefault();
        return;
    }
    if (event.defaultPrevented) return;
    enteringDemo = true;
    queueMicrotask(() => {
        if (event.defaultPrevented) {
            enteringDemo = false;
            return;
        }
        demoEntrySubmit.disabled = true;
    });
});
window.addEventListener('pageshow', () => {
    enteringDemo = false;
    if (demoEntrySubmit) demoEntrySubmit.disabled = false;
});

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
        let visibleNotices = 0;
        document.querySelectorAll('[data-notice-type]').forEach(notice => {
            notice.hidden = button.dataset.filter !== 'all' && notice.dataset.noticeType !== button.dataset.filter;
            if (!notice.hidden) visibleNotices += 1;
        });
        const filterStatus = document.querySelector('#notice-filter-status');
        if (filterStatus) {
            filterStatus.textContent = `${button.textContent.trim()}: ${visibleNotices} sample ${visibleNotices === 1 ? 'notice' : 'notices'} shown.`;
        }
    });
});

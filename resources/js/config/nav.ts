import {
    LayoutGrid, ClipboardList, ShoppingCart, UserCheck,
    DollarSign, Package, UsersRound, Truck,
    BookOpen, FolderGit2, Briefcase, ContactRound,
} from 'lucide-vue-next';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Operacional',
        icon: Briefcase,
        children: [
            { title: 'Serviços / OS', href: '/servicos',    icon: ClipboardList }, // 👈
            { title: 'Pedidos',       href: '/pedidos',     icon: ShoppingCart },
            { title: 'Estoque',       href: '/estoque',     icon: Package },
            { title: 'Fornecedores',  href: '/fornecedores',icon: Truck },
        ],
    },
    {
        title: 'Pessoas',
        icon: ContactRound,
        children: [
            { title: 'Clientes', href: '/clientes', icon: UsersRound },
            { title: 'Equipe',   href: '/equipe',   icon: UserCheck },
        ],
    },
    {
        title: 'Financeiro',
        href: '/financeiro',
        icon: DollarSign,
    },
];

export const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
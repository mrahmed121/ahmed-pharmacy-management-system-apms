import { NavLink } from 'react-router-dom';
import { useAuth } from '../../auth/AuthContext';
const GROUPS = [
  { label: 'Overview', items: [{ to: '/', label: 'Dashboard', icon: '◈', permission: 'dashboard.view' }] },
  { label: 'Sales', items: [{ to: '/pos', label: 'POS Terminal', icon: '💊', permission: 'pos.use' }] },
  { label: 'Stock', items: [
    { to: '/inventory', label: 'Inventory', icon: '📦', permission: 'inventory.view' },
    { to: '/suppliers', label: 'Suppliers', icon: '🏭', permission: 'purchases.view' },
  ]},
  { label: 'Reports', items: [{ to: '/reports', label: 'Sales Reports', icon: '📊', permission: 'reports.view' }] },
];
export default function Sidebar() {
  const { hasPermission, user } = useAuth();
  return (
    <aside className="flex h-full w-60 flex-col border-r border-slate-800 bg-slate-950">
      <div className="border-b border-slate-800 px-5 py-4"><div className="flex items-center gap-2">
        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-green-600 text-lg font-bold text-slate-950">A</div>
        <div><div className="font-bold text-slate-100">APMS</div><div className="text-[10px] uppercase tracking-wider text-emerald-500/80">Pharmacy</div></div>
      </div></div>
      <nav className="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        {GROUPS.map((g) => { const vis = g.items.filter((i) => hasPermission(i.permission)); if (!vis.length) return null;
          return (<div key={g.label}><div className="mb-2 px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">{g.label}</div>
          <div className="space-y-1">{vis.map((item) => (
            <NavLink key={item.to} to={item.to} className={({ isActive }) => `flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition ${isActive ? 'bg-emerald-500/10 font-medium text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'}`}>
              <span>{item.icon}</span>{item.label}</NavLink>))}</div></div>); })}
      </nav>
      <div className="border-t border-slate-800 px-5 py-3"><div className="text-xs text-slate-400">{user?.name}</div><div className="text-[10px] text-slate-500">Developed by Ahmed</div></div>
    </aside>
  );
}

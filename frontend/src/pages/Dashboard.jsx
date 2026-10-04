import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';
import api from '../api/client';
export default function Dashboard() {
  const { user, hasPermission } = useAuth();
  const [s, setS] = useState(null); const [loading, setLoading] = useState(true);
  useEffect(() => { api.get('/dashboard').then((r) => setS(r.data.data)).catch(() => {}).finally(() => setLoading(false)); }, []);
  const cards = [
    ['💊', 'POS Terminal', 'New Sale', '/pos', 'pos.use'],
    ['📦', "Today's Revenue", s ? `Rs ${s.today_revenue}` : '—', '/reports', 'reports.view'],
    ['📋', "Today's Invoices", s?.today_invoices ?? '—', '/reports', 'reports.view'],
    ['⚠️', 'Low Stock', s?.low_stock_count ?? '—', '/inventory', 'inventory.view'],
    ['⏰', 'Near Expiry', s?.near_expiry_count ?? '—', '/inventory', 'inventory.view'],
  ];
  return (
    <div className="space-y-6">
      <div><h1 className="text-2xl font-bold text-slate-100">Welcome, {user?.name?.split(' ')[0]}</h1>
      <p className="text-sm text-slate-400">Ahmed Pharmacy — Dispense With Precision.</p></div>
      {s && (s.low_stock_count > 0 || s.near_expiry_count > 0) && (
        <div className="rounded-lg border border-amber-500/30 bg-amber-950/20 p-4 text-sm text-amber-200">
          ⚠️ Alerts: {s.low_stock_count} medicines low on stock, {s.near_expiry_count} batches expiring within 90 days.
        </div>)}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {loading ? [1,2,3].map(i => <div key={i} className="h-28 animate-pulse rounded-lg bg-slate-800/50"/>) :
          cards.filter(([, , , , p]) => hasPermission(p)).map(([icon, label, value, link]) => (
            <Link key={label} to={link} className="rounded-lg border border-slate-800 bg-slate-900 p-5 transition hover:border-emerald-500/50">
              <div className="text-2xl">{icon}</div><div className="mt-2 text-sm text-slate-400">{label}</div>
              <div className="text-2xl font-bold text-emerald-400">{value}</div>
            </Link>))}
      </div>
    </div>
  );
}
